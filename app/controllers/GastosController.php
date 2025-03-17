<?php
namespace app\controllers;

use app\models\mainModel;

class GastosController extends mainModel {
    private $razon;
    private $monto;
    private $fondo;

    public function obtenerGastosControlador() {
        try {
            // Obtener los parámetros para seleccionarDatos
            $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : "Normal";
            $tabla = isset($_POST['tabla']) ? $_POST['tabla'] : "gastos";
            $campo = isset($_POST['campo']) ? $_POST['campo'] : "*";
            $id = isset($_POST['id']) ? $_POST['id'] : 0;

            // Usar seleccionarDatos para obtener los gastos
            $gastos = $this->seleccionarDatos($tipo, $tabla, $campo, $id);
            $cajas = $this->seleccionarDatos("Normal", "caja", "*", 0);
            
            // Crear un mapa de cajas para acceso rápido
            $cajas_map = [];
            while($caja = $cajas->fetch()) {
                $cajas_map[$caja['caja_id']] = $caja['caja_nombre'];
            }
            
            $gastos_array = [];
            while($gasto = $gastos->fetch()) {
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
            
            return json_encode($gastos_array);
        } catch (Exception $e) {
            return json_encode([
                'error' => true,
                'mensaje' => 'Error al obtener los gastos'
            ]);
        }
    }

    public function registrarGastoControlador(){
        try {
            # Almacenando datos#
            $this->razon = $this->limpiarCadena($_POST['razon']);
            $this->monto = $this->limpiarCadena($_POST['monto']);
            $this->fondo = $this->limpiarCadena($_POST['fondo']);
            $caja_id = $this->limpiarCadena($_POST['caja_id']);

            # Verificando campos obligatorios #
            if($this->razon == "" || $this->monto == "" || $caja_id == ""){
                $alerta=[
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No has llenado todos los campos que son obligatorios",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            # Verificando integridad de los datos #
            if(!preg_match("/^[0-9]+$/", $this->monto)){
                $alerta=[
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El MONTO no coincide con el formato solicitado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            /*== Comprobando caja en la DB ==*/
            $check_caja = $this->seleccionarDatos("Unico", "caja", "caja_id", $caja_id);
            if($check_caja->rowCount() <= 0){
                $alerta=[
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "La caja seleccionada no existe en el sistema",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            /*== Preparando datos para guardar el gasto ==*/
            $datos_gasto_reg = [
                [
                    "campo_nombre" => "razon",
                    "campo_marcador" => ":Razon",
                    "campo_valor" => $this->razon
                ],
                [
                    "campo_nombre" => "monto",
                    "campo_marcador" => ":Monto",
                    "campo_valor" => $this->monto
                ],
                [
                    "campo_nombre" => "fondo",
                    "campo_marcador" => ":Fondo",
                    "campo_valor" => $this->fondo
                ],
                [
                    "campo_nombre" => "caja_id",
                    "campo_marcador" => ":Caja",
                    "campo_valor" => $caja_id
                ]
            ];

            /*== Guardando el gasto usando guardarDatos ==*/
            $registrar_gasto = $this->guardarDatos("gastos", $datos_gasto_reg);

            if($registrar_gasto->rowCount() == 1){
                $alerta=[
                    "tipo" => "recargar",
                    "titulo" => "Gasto registrado",
                    "texto" => "El gasto ha sido registrado con éxito",
                    "icono" => "success"
                ];
            }else{
                $alerta=[
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No se pudo registrar el gasto, por favor intente nuevamente",
                    "icono" => "error"
                ];
            }

            return json_encode($alerta);
        } catch (Exception $e) {
            $alerta=[
                "tipo" => "simple",
                "titulo" => "Error en el sistema",
                "texto" => "Ha ocurrido un error al procesar la solicitud",
                "icono" => "error"
            ];
            return json_encode($alerta);
        }
    }

    /*----------  Controlador eliminar gasto  ----------*/
    public function eliminarGastoControlador(){
        if(isset($_POST['id'])){
            $id=$this->limpiarCadena($_POST['id']);
            
            // Verificando que el gasto exista
            $check_gasto=$this->ejecutarConsulta("SELECT id FROM gastos WHERE id='$id'");
            if($check_gasto->rowCount()<=0){
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"El gasto no existe en el sistema",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
            }
            
            // Eliminando el gasto
            $eliminar_gasto=$this->ejecutarConsulta("DELETE FROM gastos WHERE id='$id'");
            if($eliminar_gasto->rowCount()>=1){
                $alerta=[
                    "tipo"=>"limpiar",
                    "titulo"=>"Gasto eliminado",
                    "texto"=>"El gasto ha sido eliminado con éxito",
                    "icono"=>"success"
                ];
            }else{
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"No se pudo eliminar el gasto, por favor intente nuevamente",
                    "icono"=>"error"
                ];
            }
            
            return json_encode($alerta);
        }
        
        $alerta=[
            "tipo"=>"simple",
            "titulo"=>"Ocurrió un error inesperado",
            "texto"=>"No se ha recibido el ID del gasto",
            "icono"=>"error"
        ];
        return json_encode($alerta);
    }
}
?>
