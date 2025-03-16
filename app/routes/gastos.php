<?php
use app\controllers\GastosController;

$gastosController = new GastosController();

// Route for creating a new gasto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create') {
    $gastosController->create();
}

// Route for displaying the gastos view
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['view']) && $_GET['view'] === 'gastos') {
    include 'app/views/content/gastos-view.php';
}
?>
