<div class="container is-fluid">
	<h1 class="title">Home</h1>
  	<div class="columns is-flex is-justify-content-center">
    	<figure class="image is-128x128">
    		<?php
    			if(is_file("./app/views/fotos/".$_SESSION['foto'])){
    				echo '<img class="is-rounded" src="'.APP_URL.'app/views/fotos/'.$_SESSION['foto'].'">';
    			}else{
    				echo '<img class="is-rounded" src="'.APP_URL.'app/views/fotos/default.png">';
    			}
    		?>
		</figure>
  	</div>
  	<div class="columns is-flex is-justify-content-center">
  		<h2 class="subtitle">¡Bienvenido <?php echo $_SESSION['nombre']." ".$_SESSION['apellido']; ?>!</h2>
  	</div>
</div>
<?php
	$total_cajas=$insLogin->seleccionarDatos("Normal","caja","caja_id",0);

	$total_usuarios=$insLogin->seleccionarDatos("Normal","usuario WHERE usuario_id!='1' AND usuario_id!='".$_SESSION['id']."'","usuario_id",0);

	$total_clientes=$insLogin->seleccionarDatos("Normal","cliente WHERE cliente_id!='1'","cliente_id",0);

	$total_categorias=$insLogin->seleccionarDatos("Normal","categoria","categoria_id",0);

	$total_productos=$insLogin->seleccionarDatos("Normal","producto","producto_id",0);

	$total_ventas=$insLogin->seleccionarDatos("Normal","venta","venta_id",0);
	
	// Calcular el total de ingresos
	$ingresos=$insLogin->seleccionarDatos("Normal","venta","SUM(venta_total) as total_ingresos",0);
	$total_ingresos = $ingresos->fetch()['total_ingresos'] ?? 0;

	// Calcular el total de gastos
	$gastos=$insLogin->seleccionarDatos("Normal","gastos","SUM(monto) as total_gastos",0);
	$total_gastos = $gastos->fetch()['total_gastos'] ?? 0;

	// Calcular el balance
	$balance = $total_ingresos - $total_gastos;
?>
<style>
	.dashboard-card {
		border-radius: 12px;
		transition: all 0.3s ease;
		height: 100%;
	}
	.dashboard-card:hover {
		transform: translateY(-5px);
		box-shadow: 0 8px 16px rgba(0,0,0,0.1);
	}
	.dashboard-card .icon-wrapper {
		width: 50px;
		height: 50px;
		border-radius: 12px;
		display: flex;
		align-items: center;
		justify-content: center;
		margin-bottom: 1rem;
	}
	.dashboard-card .icon-wrapper.income {
		background-color: rgba(0, 209, 178, 0.1);
		color: #00d1b2;
	}
	.dashboard-card .icon-wrapper.expense {
		background-color: rgba(255, 56, 96, 0.1);
		color: #ff3860;
	}
	.dashboard-card .icon-wrapper.balance {
		background-color: rgba(50, 115, 220, 0.1);
		color: #3273dc;
	}
	.dashboard-card .icon-wrapper.clients {
		background-color: rgba(255, 196, 9, 0.1);
		color: #ffc409;
	}
	.dashboard-card .icon-wrapper.sales {
		background-color: rgba(132, 94, 247, 0.1);
		color: #845ef7;
	}
	.dashboard-card .icon-wrapper.products {
		background-color: rgba(255, 107, 0, 0.1);
		color: #ff6b00;
	}
	.dashboard-card .icon-wrapper.categories {
		background-color: rgba(46, 204, 113, 0.1);
		color: #2ecc71;
	}
	.dashboard-card .icon-wrapper.users {
		background-color: rgba(52, 152, 219, 0.1);
		color: #3498db;
	}
	.dashboard-card .icon-wrapper.cashiers {
		background-color: rgba(155, 89, 182, 0.1);
		color: #9b59b6;
	}
	.dashboard-card .card-value {
		font-size: 1.8rem;
		font-weight: bold;
		margin: 0.5rem 0;
	}
	.dashboard-card .card-label {
		color: #7a7a7a;
		font-size: 1rem;
		margin-bottom: 0.5rem;
	}
	.stats-row {
		margin-bottom: 2rem;
	}
</style>

<div class="container pb-6 pt-6">
	<!-- Fila de estadísticas financieras -->
	<div class="columns is-multiline stats-row">
		<div class="column is-4">
			<div class="card dashboard-card">
				<div class="card-content">
					<div class="icon-wrapper income">
						<span class="icon is-large">
							<i class="fas fa-hand-holding-usd fa-2x"></i>
						</span>
					</div>
					<div class="content">
						<h3 class="card-label">Ingresos Totales</h3>
						<p class="card-value has-text-success">
							Q<?php echo number_format($total_ingresos, 2); ?>
						</p>
						<p class="subtitle is-6">
							<?php echo $total_ventas->rowCount(); ?> ventas registradas
						</p>
					</div>
				</div>
			</div>
		</div>

		<div class="column is-4">
			<a href="<?php echo APP_URL; ?>gastosList/" class="has-text-dark">
				<div class="card dashboard-card">
					<div class="card-content">
						<div class="icon-wrapper expense">
							<span class="icon is-large">
								<i class="fas fa-money-bill-wave fa-2x"></i>
							</span>
						</div>
						<div class="content">
							<h3 class="card-label">Gastos Totales</h3>
							<p class="card-value has-text-danger">
								Q<?php echo number_format($total_gastos, 2); ?>
							</p>
							<p class="subtitle is-6">
								Click para ver detalles
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>

		<div class="column is-4">
			<div class="card dashboard-card">
				<div class="card-content">
					<div class="icon-wrapper balance">
						<span class="icon is-large">
							<i class="fas fa-balance-scale fa-2x"></i>
						</span>
					</div>
					<div class="content">
						<h3 class="card-label">Balance Total</h3>
						<p class="card-value <?php echo $balance >= 0 ? 'has-text-success' : 'has-text-danger'; ?>">
							Q<?php echo number_format($balance, 2); ?>
						</p>
						<p class="subtitle is-6">
							<?php echo $balance >= 0 ? 'Balance positivo' : 'Balance negativo'; ?>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Resto de estadísticas -->
	<div class="columns is-multiline">
		<?php if($_SESSION['cargo']=="Administrador"){ ?>
		<div class="column is-4">
			<a href="<?php echo APP_URL; ?>cashierList/" class="has-text-dark">
				<div class="card dashboard-card">
					<div class="card-content">
						<div class="icon-wrapper cashiers">
							<span class="icon is-large">
								<i class="fas fa-cash-register fa-2x"></i>
							</span>
						</div>
						<div class="content">
							<h3 class="card-label">Cajas Registradas</h3>
							<p class="card-value">
								<?php echo $total_cajas->rowCount(); ?>
							</p>
							<p class="subtitle is-6">
								Click para administrar cajas
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>

		<div class="column is-4">
			<a href="<?php echo APP_URL; ?>userList/" class="has-text-dark">
				<div class="card dashboard-card">
					<div class="card-content">
						<div class="icon-wrapper users">
							<span class="icon is-large">
								<i class="fas fa-users fa-2x"></i>
							</span>
						</div>
						<div class="content">
							<h3 class="card-label">Usuarios del Sistema</h3>
							<p class="card-value">
								<?php echo $total_usuarios->rowCount(); ?>
							</p>
							<p class="subtitle is-6">
								Click para gestionar usuarios
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>

		<div class="column is-4">
			<a href="<?php echo APP_URL; ?>categoryList/" class="has-text-dark">
				<div class="card dashboard-card">
					<div class="card-content">
						<div class="icon-wrapper categories">
							<span class="icon is-large">
								<i class="fas fa-tags fa-2x"></i>
							</span>
						</div>
						<div class="content">
							<h3 class="card-label">Categorías</h3>
							<p class="card-value">
								<?php echo $total_categorias->rowCount(); ?>
							</p>
							<p class="subtitle is-6">
								Click para ver categorías
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>

		<div class="column is-4">
			<a href="<?php echo APP_URL; ?>productList/" class="has-text-dark">
				<div class="card dashboard-card">
					<div class="card-content">
						<div class="icon-wrapper products">
							<span class="icon is-large">
								<i class="fas fa-cubes fa-2x"></i>
							</span>
						</div>
						<div class="content">
							<h3 class="card-label">Productos</h3>
							<p class="card-value">
								<?php echo $total_productos->rowCount(); ?>
							</p>
							<p class="subtitle is-6">
								Click para gestionar productos
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
		<?php } ?>

		<div class="column is-4">
			<a href="<?php echo APP_URL; ?>clientList/" class="has-text-dark">
				<div class="card dashboard-card">
					<div class="card-content">
						<div class="icon-wrapper clients">
							<span class="icon is-large">
								<i class="fas fa-address-book fa-2x"></i>
							</span>
						</div>
						<div class="content">
							<h3 class="card-label">Clientes Registrados</h3>
							<p class="card-value">
								<?php echo $total_clientes->rowCount(); ?>
							</p>
							<p class="subtitle is-6">
								Click para ver clientes
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>

		<div class="column is-4">
			<a href="<?php echo APP_URL; ?>saleList/" class="has-text-dark">
				<div class="card dashboard-card">
					<div class="card-content">
						<div class="icon-wrapper sales">
							<span class="icon is-large">
								<i class="fas fa-shopping-cart fa-2x"></i>
							</span>
						</div>
						<div class="content">
							<h3 class="card-label">Total de Ventas</h3>
							<p class="card-value">
								<?php echo $total_ventas->rowCount(); ?>
							</p>
							<p class="subtitle is-6">
								Click para ver historial
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
	</div>

</div>