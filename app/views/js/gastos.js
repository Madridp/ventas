// Variables globales
let todosLosGastos = window.initialGastos || [];
const APP_URL = document.querySelector('meta[name="app-url"]').content;

// Función para cargar los gastos desde el servidor
async function cargarGastos() {
    try {
        const response = await fetch(`${APP_URL}app/ajax/gastoAjax.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'modulo_gasto=obtener_gastos&tipo=Normal&tabla=gastos&campo=*&id=0'
        });
        const data = await response.json();
        
        if (data.error) {
            throw new Error(data.mensaje);
        }
        
        todosLosGastos = data;
        actualizarListaGastos();
    } catch (error) {
        console.error('Error al cargar gastos:', error);
        mostrarError();
    }
}

function actualizarListaGastos() {
    const cajaId = document.querySelector('select[name="caja_id"]').value;
    renderizarGastos(filtrarGastos(cajaId));
}

function mostrarError() {
    const gastosList = document.getElementById('gastos-list');
    gastosList.innerHTML = `
        <article class="message is-danger">
            <div class="message-header">
                <p>Error</p>
            </div>
            <div class="message-body has-text-centered">
                <i class="fas fa-exclamation-circle fa-2x mb-3"></i><br>
                Ocurrió un error al cargar los gastos. Por favor, intente nuevamente.
            </div>
        </article>`;
}

function filtrarGastos(cajaId) {
    if (!cajaId) {
        return todosLosGastos;
    }
    return todosLosGastos.filter(gasto => gasto.caja_id === cajaId);
}

function formatearFecha(fecha) {
    return new Date(fecha).toLocaleDateString('es-GT', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function renderizarGastos(gastos) {
    const gastosList = document.querySelector('#gastos-list .cards');
    
    if(gastos.length === 0) {
        gastosList.innerHTML = `
            <article class="message is-warning">
                <div class="message-header">
                    <p>No hay gastos registrados</p>
                </div>
                <div class="message-body has-text-centered">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i><br>
                    No se han registrado gastos hasta el momento.
                </div>
            </article>`;
        return;
    }

    let html = '';
    gastos.forEach(gasto => {
        html += `
            <div class="card mb-3 gasto-card">
                <div class="card-content">
                    <div class="media">
                        <div class="media-left">
                            <span class="icon is-large">
                                <i class="fas fa-file-invoice-dollar fa-2x"></i>
                            </span>
                        </div>
                        <div class="media-content">
                            <p class="title is-4">${gasto.razon}</p>
                            <p class="subtitle is-6">
                                <span class="icon-text">
                                    <span class="icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </span>
                                    <span>Monto: Q${parseFloat(gasto.monto).toFixed(2)}</span>
                                </span>
                                <br>
                                <span class="icon-text">
                                    <span class="icon">
                                        <i class="fas fa-box"></i>
                                    </span>
                                    <span>Fondo: ${gasto.fondo}</span>
                                </span>
                                <br>
                                <span class="icon-text">
                                    <span class="icon">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                    <span>${formatearFecha(gasto.fecha)}</span>
                                </span>
                                <br>
                                <span class="icon-text">
                                    <span class="icon">
                                        <i class="fas fa-cash-register"></i>
                                    </span>
                                    <span>Caja: ${gasto.caja_nombre}</span>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>`;
    });
    gastosList.innerHTML = html;
}

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    // Manejar cambios en el select de caja
    document.querySelector('select[name="caja_id"]').addEventListener('change', function() {
        const cajaId = this.value;
        const url = new URL(window.location.href);
        
        if(cajaId) {
            url.searchParams.set('caja_id', cajaId);
        } else {
            url.searchParams.delete('caja_id');
        }
        
        history.pushState({}, '', url);
        actualizarListaGastos();
    });

    // Manejar envío del formulario
    document.getElementById('form-gasto').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Obtener los datos del formulario
        const formData = new FormData(this);
        
        // Realizar la petición Ajax
        fetch(this.action, {
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
                    // Recargar los gastos manteniendo el filtro
                    cargarGastos();
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

    // Renderizar los gastos iniciales
    actualizarListaGastos();

    // Actualizar la lista cada 30 segundos
    setInterval(cargarGastos, 30000);
});
