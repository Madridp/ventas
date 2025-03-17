<?php
    require_once "./app/views/inc/session_start.php";
    
    // Obtener las cajas para el mapeo
    $datos_cajas = $insLogin->seleccionarDatos("Normal", "caja", "*", 0);
    $cajas_map = [];
    $cajas_options = '<option value="">Todas las Cajas</option>'; // Opción por defecto
    
    while($caja = $datos_cajas->fetch()) {
        $cajas_map[$caja['caja_id']] = $caja['caja_nombre'];
        $selected = (isset($_GET['caja_id']) && $_GET['caja_id'] == $caja['caja_id']) ? 'selected' : '';
        $cajas_options .= '<option value="' . $caja['caja_id'] . '" ' . $selected . '>' . $caja['caja_nombre'] . '</option>';
    }
    
    // Guardar el id de caja seleccionado desde la URL
    $caja_id_filtro = isset($_GET['caja_id']) ? $_GET['caja_id'] : null;
    
    // Obtener los gastos usando seleccionarDatos
    $datos_gastos = $insLogin->seleccionarDatos("Normal", "gastos", "*", 0);
    $gastos_array = [];
    
    // Preparar el array de gastos
    while($gasto = $datos_gastos->fetch()) {
        // Aplicar filtro por caja_id
        if($caja_id_filtro === null || $caja_id_filtro == $gasto['caja_id']) {
            $gastos_array[] = [
                'id' => $gasto['id'],
                'razon' => $gasto['razon'],
                'monto' => $gasto['monto'],
                'fondo' => $gasto['fondo'],
                'fecha' => $gasto['fecha'],
                'caja_id' => $gasto['caja_id'],
                'caja_nombre' => isset($cajas_map[$gasto['caja_id']]) ? $cajas_map[$gasto['caja_id']] : 'Sin caja'
            ];
        }
    }
?>

<!-- Inicialización de datos iniciales -->
<script type="text/javascript">
    window.initialGastos = <?php echo json_encode($gastos_array); ?>;
</script>

<script>
    // Variables globales
    const APP_URL = document.querySelector('meta[name="app-url"]').content;
    const gastosArray = <?php echo json_encode($gastos_array); ?>;

    // Función para formatear fecha en español
    function formatearFecha(fecha) {
        const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        const date = new Date(fecha);
        const dia = date.getDate();
        const mes = meses[date.getMonth()];
        const año = date.getFullYear();
        return `${dia} de ${mes} ${año}`;
    }

    // Función para filtrar gastos
    function filtrarGastos() {
        const cajaId = document.querySelector('select[name="caja_id"]').value;
        const gastosFiltrados = cajaId ? gastosArray.filter(gasto => gasto.caja_id == cajaId) : gastosArray;
        
        // Actualizar URL con el parámetro de filtro
        const url = new URL(window.location.href);
        if (cajaId) {
            url.searchParams.set('caja_id', cajaId);
        } else {
            url.searchParams.delete('caja_id');
        }
        history.pushState({}, '', url);

        renderizarGastos(gastosFiltrados);
    }

    // Función para renderizar gastos
    function renderizarGastos(gastos) {
        const gastosList = document.querySelector('#gastos-list');
        let html = '';
        
        if(gastos.length === 0) {
            gastosList.innerHTML = `
                <div class="column is-12">
                    <article class="message is-warning">
                        <div class="message-header">
                            <p>No hay gastos registrados</p>
                        </div>
                        <div class="message-body has-text-centered">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i><br>
                            No se han registrado gastos hasta el momento.
                        </div>
                    </article>
                </div>`;
            return;
        }

        // Generar HTML para cada gasto
        gastos.forEach(gasto => {
            html += `
                <div class="column is-4">
                    <div class="card dashboard-card">
                        <div class="card-content">
                            <p class="title is-4">${gasto.razon}</p>
                            <div class="badges-container">
                                <span class="badge is-money">
                                    <span class="icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </span>
                                    <span>Q${parseFloat(gasto.monto).toFixed(2)}</span>
                                </span>
                                <span class="badge is-date">
                                    <span class="icon">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                    <span>${formatearFecha(gasto.fecha)}</span>
                                </span>
                                <span class="badge is-caja">
                                    <span class="icon">
                                        <i class="fas fa-cash-register"></i>
                                    </span>
                                    <span>Caja ${gasto.caja_id}</span>
                                </span>
                            </div>
                            <div class="buttons mt-3">
                                <button class="button is-warning" onclick="abrirModalEditar(${gasto.id})">Editar</button>
                                <button class="button is-danger" onclick="eliminarGasto(${gasto.id})">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>`;
        });
        
        gastosList.innerHTML = html;
    }

    function abrirModalEditar(id) {
        const gasto = gastosArray.find(g => g.id == id);
        if (gasto) {
            document.getElementById('razonGasto').value = gasto.razon;
            document.getElementById('montoGasto').value = gasto.monto;
            document.getElementById('idGasto').value = gasto.id;
            document.getElementById('modalEditarGasto').classList.add('is-active');
        }
    }

    function actualizarGasto() {
        const id = document.getElementById('idGasto').value;
        const razon = document.getElementById('razonGasto').value;
        const monto = document.getElementById('montoGasto').value;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', APP_URL + '/ajax/gastoAjax.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (this.responseText === 'success') {
                alert('Gasto actualizado correctamente.');
                actualizarGastosDesdeServidor();
                cerrarModalEditar();
            } else {
                alert('Error al actualizar el gasto.');
            }
        };
        xhr.send('modulo_gasto=actualizar&id=' + id + '&razon=' + razon + '&monto=' + monto);
    }

    function eliminarGasto(id) {
        document.getElementById('confirmarEliminar').setAttribute('data-id', id);
        document.getElementById('modalEliminarGasto').classList.add('is-active');
    }

    document.getElementById('confirmarEliminar').addEventListener('click', function() {
        const gastoId = this.getAttribute('data-id');
        const xhr = new XMLHttpRequest();
        xhr.open('POST', APP_URL + '/ajax/gastoAjax.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (this.responseText === 'success') {
                alert('Gasto eliminado correctamente.');
                actualizarGastosDesdeServidor();
                cerrarModalEliminarGasto();
            } else {
                alert('Error al eliminar el gasto.');
            }
        };
        xhr.send('modulo_gasto=eliminar&id=' + gastoId);
    });
</script>

<div class="container is-fluid mb-6">
    <h1 class="title">Lista de Gastos</h1>
    <h2 class="subtitle"><i class="fas fa-money-bill-wave"></i> Control de gastos del sistema</h2>
</div>

<div class="container pb-6 pt-6">
    <div class="columns">
        <div class="column is-full">
            <!-- Filtro de cajas -->
            <div class="field mb-5">
                <label class="label">Filtrar por Caja</label>
                <div class="control has-icons-left">
                    <div class="select is-rounded">
                        <select name="caja_id" onchange="filtrarGastos()">
                            <?php 
                                echo $cajas_options;
                            ?>
                        </select>
                    </div>
                    <span class="icon is-small is-left">
                        <i class="fas fa-cash-register"></i>
                    </span>
                </div>
            </div>

            <!-- Lista de gastos -->
            <div class="columns is-multiline" id="gastos-list">
                <?php 
                    if(empty($gastos_array)): ?>
                        <div class="column is-12">
                            <article class="message is-warning">
                                <div class="message-header">
                                    <p>No hay gastos registrados</p>
                                </div>
                                <div class="message-body has-text-centered">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i><br>
                                    No se han registrado gastos hasta el momento.
                                </div>
                            </article>
                        </div>
                    <?php else: ?>
                        <?php foreach($gastos_array as $gasto): ?>
                            <div class="column is-4">
                                <div class="card dashboard-card">
                                    <div class="card-content">
                                        <p class="title is-4"><?php echo $gasto['razon']; ?></p>
                                        <div class="badges-container">
                                            <span class="badge is-money">
                                                <span class="icon">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </span>
                                                <span>Q<?php echo number_format($gasto['monto'], 2); ?></span>
                                            </span>
                                            <span class="badge is-date">
                                                <span class="icon">
                                                    <i class="fas fa-calendar"></i>
                                                </span>
                                                <span><?php echo date('d \d\e M Y', strtotime($gasto['fecha'])); ?></span>
                                            </span>
                                            <span class="badge is-caja">
                                                <span class="icon">
                                                    <i class="fas fa-cash-register"></i>
                                                </span>
                                                <span>Caja <?php echo $gasto['caja_id']; ?></span>
                                            </span>
                                        </div>
                                        <div class="buttons mt-3">
                                            <button class="button is-warning" onclick="abrirModalEditar(<?php echo $gasto['id']; ?>)">Editar</button>
                                            <button class="button is-danger" onclick="eliminarGasto(<?php echo $gasto['id']; ?>)">Eliminar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar gasto -->
<div class="modal" id="modalEditarGasto">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Editar Gasto</p>
            <button class="delete" aria-label="close" onclick="cerrarModalEditar()"></button>
        </header>
        <section class="modal-card-body">
            <div class="field">
                <label class="label">Razón</label>
                <div class="control">
                    <input class="input" type="text" id="razonGasto" placeholder="Razón del gasto">
                </div>
            </div>
            <div class="field">
                <label class="label">Monto</label>
                <div class="control">
                    <input class="input" type="number" id="montoGasto" placeholder="Monto del gasto">
                </div>
            </div>
            <input type="hidden" id="idGasto">
        </section>
        <footer class="modal-card-foot">
            <button class="button is-success" onclick="actualizarGasto()">Guardar cambios</button>
            <button class="button" onclick="cerrarModalEditar()">Cancelar</button>
        </footer>
    </div>
</div>

<!-- Modal para eliminar gasto -->
<div class="modal" id="modalEliminarGasto">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Eliminar Gasto</p>
            <button class="delete" aria-label="close" onclick="cerrarModalEliminarGasto()"></button>
        </header>
        <section class="modal-card-body">
            <p>¿Estás seguro de que deseas eliminar este gasto?</p>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-danger" id="confirmarEliminar">Eliminar</button>
            <button class="button" onclick="cerrarModalEliminarGasto()">Cancelar</button>
        </footer>
    </div>
</div>

<style>
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem;
        border-radius: 6px;
        font-size: 0.9rem;
        margin: 0.2rem;
        color: white;
    }
    .badge.is-money {
        background-color: #00d1b2;
    }
    .badge.is-date {
        background-color: #3273dc;
    }
    .badge.is-caja {
        background-color: #ff3860;
    }
    .badge .icon {
        margin-right: 0.3rem;
    }
    .dashboard-card {
        transition: all 0.3s ease;
        border-radius: 12px;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
    .badges-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
    }
</style>
