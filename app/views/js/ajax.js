/* Enviar formularios via AJAX */
const formularios_ajax=document.querySelectorAll(".FormularioAjax");

formularios_ajax.forEach(formularios => {

    formularios.addEventListener("submit",function(e){
        
        e.preventDefault();

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Quieres realizar la acción solicitada",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, realizar',
            cancelButtonText: 'No, cancelar'
        }).then((result) => {
            if (result.isConfirmed){

                let data = new FormData(this);
                let method=this.getAttribute("method");
                let action=this.getAttribute("action");

                let encabezados= new Headers();

                let config={
                    method: method,
                    headers: encabezados,
                    mode: 'cors',
                    cache: 'no-cache',
                    body: data
                };

                fetch(action,config)
                .then(respuesta => {
                    // Intentar parsear como JSON, si falla, mostrar un error genérico
                    return respuesta.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (error) {
                            console.error("Error al parsear JSON:", text);
                            return {
                                tipo: "simple",
                                titulo: "Error en el sistema",
                                texto: "Ha ocurrido un error al procesar la respuesta del servidor",
                                icono: "error"
                            };
                        }
                    });
                })
                .then(respuesta =>{ 
                    return alertas_ajax(respuesta);
                })
                .catch(error => {
                    console.error("Error en la petición:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en el sistema',
                        text: 'Ha ocurrido un error al procesar la solicitud',
                        confirmButtonText: 'Aceptar'
                    });
                });
            }
        });

    });

});

/* Boton cerrar sesion */
let btn_exit=document.querySelectorAll(".btn-exit");

btn_exit.forEach(exitSystem => {
    exitSystem.addEventListener("click", function(e){

        e.preventDefault();
        
        Swal.fire({
            title: '¿Quieres salir del sistema?',
            text: "La sesión actual se cerrará y saldrás del sistema",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, salir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                let url=this.getAttribute("href");
                window.location.href=url;
            }
        });

    });
});

function alertas_ajax(alerta){
    if(alerta.tipo=="simple"){
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        });
    }else if(alerta.tipo=="recargar"){
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if(result.isConfirmed){
                if(alerta.url){
                    window.location.href = alerta.url;
                } else {
                    location.reload();
                }
            }
        });
    }else if(alerta.tipo=="limpiar"){
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if(result.isConfirmed){
                document.querySelector(".FormularioAjax").reset();
            }
        });
    }else if(alerta.tipo=="redireccionar"){
        window.location.href=alerta.url;
    }
}