<?php
namespace app\ajax;

$peticionAjax = true;
require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\controllers\GastosController;

if(isset($_POST['modulo_gasto'])){

    $insGasto = new GastosController();

    if($_POST['modulo_gasto'] == "registrar"){
        echo $insGasto->registrarGastoControlador();
    }

    if($_POST['modulo_gasto'] == "eliminar"){
        echo $insGasto->eliminarGastoControlador();
    }

    if($_POST['modulo_gasto'] == "actualizar"){
        echo $insGasto->actualizarGastoControlador();
    }

}else{
    session_destroy();
    header("Location: ".APP_URL."login/");
}
?>
