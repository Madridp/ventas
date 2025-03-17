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

<div class="container is-fluid mb-6">
    <h1 class="title">Registro de Gastos</h1>
    <div class="columns">
        <div class="column is-half">
            <h2 class="title is-4"><i class="fas fa-list-alt"></i> Listado de Gastos</h2>
            <div class="field">
                <label class="label">Filtrar por Caja</label>
                <div class="control">
                    <div class="select">
                        <select name="caja_id" onchange="filtrarGastos()">
                            <?php echo $cajas_options; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div id="gastos-list">
                <div class="cards columns is-multiline">
                <?php if(empty($gastos_array)): ?>
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
                            <div class="card mb-3 gasto-card">
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
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="column is-half">
            <div class="card" style="border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div class="card-header" style="border-top-left-radius: 8px; border-top-right-radius: 8px;">
                    <p class="card-header-title">
                        <i class="fas fa-money-bill-wave mr-3"></i> Registro de Gastos
                    </p>
                </div>
                <div class="card-content">
                    <form id="form-gasto" class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/gastoAjax.php" method="POST" autocomplete="off">
                        <input type="hidden" name="modulo_gasto" value="registrar">
                        <div class="field">
                            <label class="label">
                                <span class="icon-text">
                                    <span class="icon mr-2">
                                        <i class="fas fa-file-signature"></i>
                                    </span>
                                    <span>Razón</span>
                                </span>
                            </label>
                            <div class="control has-icons-left">
                                <input class="input" type="text" name="razon" placeholder="Descripción del gasto" required>
                                <span class="icon is-small is-left">
                                    <i class="fas fa-file-signature"></i>
                                </span>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">
                                <span class="icon-text">
                                    <span class="icon mr-2">
                                        <i class="fas fa-dollar-sign"></i>
                                    </span>
                                    <span>Monto</span>
                                </span>
                            </label>
                            <div class="control has-icons-left has-icons-right">
                                <input class="input" type="number" name="monto" placeholder="0.00" min="1" step="0.01" required>
                                <span class="icon is-small is-left">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                                <span class="icon is-small is-right">
                                    <i class="fas fa-check"></i>
                                </span>
                            </div>
                            <p class="help">Ingrese el monto en quetzales (Q)</p>
                        </div>
                        <div class="field">
                            <label class="label">
                                <span class="icon-text">
                                    <span class="icon mr-2">
                                        <i class="fas fa-cash-register"></i>
                                    </span>
                                    <span>Caja</span>
                                </span>
                            </label>
                            <div class="control has-icons-left">
                                <div class="select is-fullwidth">
                                    <select name="caja_id" required>
                                        <?php echo $cajas_options; ?>
                                    </select>
                                </div>
                                <span class="icon is-small is-left">
                                    <i class="fas fa-cash-register"></i>
                                </span>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">
                                <span class="icon-text">
                                    <span class="icon mr-2">
                                        <i class="fas fa-money-check-alt"></i>
                                    </span>
                                    <span>Fondo</span>
                                </span>
                            </label>
                            <div class="control has-icons-left">
                                <div class="select is-fullwidth">
                                    <select name="fondo" required>
                                        <option value="">Seleccionar Fondo</option>
                                        <option value="Efectivo">Efectivo</option>
                                        <option value="Tarjeta">Tarjeta</option>
                                        <option value="Transferencia">Transferencia</option>
                                    </select>
                                </div>
                                <span class="icon is-small is-left">
                                    <i class="fas fa-money-check-alt"></i>
                                </span>
                            </div>
                        </div>
                        <div class="field">
                            <div class="control">
                                <button class="button is-primary is-fullwidth is-rounded hover-effect" type="submit">
                                    <span class="icon mr-2">
                                        <i class="fas fa-save"></i>
                                    </span>
                                    <span>Registrar Gasto</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <style>
                .button.hover-effect {
                    transition: all 0.3s ease;
                }
                .button.hover-effect:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    background-color: #00c4a7;
                }
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
                .gasto-card {
                    transition: all 0.3s ease;
                }
                .gasto-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
                }
                .badges-container {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 0.5rem;
                    margin-top: 1rem;
                }
                .icon-text {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                }
                .message {
                    border-radius: 8px;
                }
                .message-header {
                    border-top-left-radius: 8px;
                    border-top-right-radius: 8px;
                }
                .mb-3 {
                    margin-bottom: 1rem !important;
                }
            </style>
        </div>
    </div>
</div>

<script>
    // Variables globales
    let todosLosGastos = <?php echo json_encode($gastos_array); ?>;
    const APP_URL = document.querySelector('meta[name="app-url"]').content;

    // Función para formatear fecha en español
    function formatearFecha(fecha) {
        const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        const date = new Date(fecha);
        const dia = date.getDate();
        const mes = meses[date.getMonth()];
        const año = date.getFullYear();
        return `${dia} de ${mes} ${año}`;
    }

    // Función para renderizar gastos
    function renderizarGastos(gastos) {
        const gastosList = document.querySelector('#gastos-list .cards');
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
                    <div class="card mb-3 gasto-card">
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
                        </div>
                    </div>
                </div>`;
        });
        
        gastosList.innerHTML = html;
    }

    // Función para filtrar gastos
    function filtrarGastos() {
        const cajaId = document.querySelector('select[name="caja_id"]').value;
        const gastosFiltrados = cajaId ? todosLosGastos.filter(gasto => gasto.caja_id === cajaId) : todosLosGastos;
        renderizarGastos(gastosFiltrados);
    }

    // Inicialización
    document.addEventListener('DOMContentLoaded', function() {
        // Renderizar gastos iniciales
        filtrarGastos();

        // Manejar cambios en el select de caja
        document.querySelector('select[name="caja_id"]').addEventListener('change', function() {
            const url = new URL(window.location.href);
            
            if(this.value) {
                url.searchParams.set('caja_id', this.value);
            } else {
                url.searchParams.delete('caja_id');
            }
            
            // Recargar la página con el nuevo filtro
            window.location.href = url.toString();
        });

        // Manejar envío del formulario
        document.getElementById('form-gasto').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Obtener los datos del formulario
            const formData = new FormData(this);
            formData.append('modulo_gasto', 'registrar');
            
            // Realizar la petición Ajax
            fetch(`${APP_URL}app/ajax/gastoAjax.php`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Mostrar la alerta
                Swal.fire({
                    icon: data.icono,
                    title: data.titulo,
                    text: data.texto,
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    if(data.tipo === "recargar") {
                        // Limpiar el formulario
                        this.reset();
                        // Recargar la página manteniendo el filtro
                        window.location.reload();
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al procesar la solicitud'
                });
            });
        });
    });
</script>
