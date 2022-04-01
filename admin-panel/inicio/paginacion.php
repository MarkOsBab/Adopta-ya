<table class="table">
	<thead>
		<tr>
			<th> Nombre de la persona </th>
			<th> Teléfono </th>
			<th> Publicación </th>
			<th> Fecha de publicación </th>
			<th> Cambiar estado </th>
			<th> Estado </th>
		</tr>
	</thead>
	<tbody>
		<?php
		
		
		$_BS['bypage'] = 6;
		require_once '../classes/class.config.php';
		$db = getDB();
		$conta_total = $db->prepare("SELECT COUNT(*) AS total FROM publicaciones_animales");
		$conta_total->execute();
		$total = $conta_total->fetchColumn();

		$paginas =  (($total % $_BS['bypage']) > 0) ? (int)($total / $_BS['bypage']) + 1 : ($total / $_BS['bypage']);

		if (isset($_GET['page'])) {
			$pagina = (int)$_GET['page'];
		} else {
			$pagina = 1;
		}

		$pagina = max(min($paginas, $pagina), 1);
		$inicio = ($pagina - 1) * $_BS['bypage'];
		$sql = $db->prepare("SELECT * FROM `publicaciones_animales` ORDER BY `id` ASC LIMIT ".$inicio.", ".$_BS['bypage']);
		$sql->execute();
		$resultado = $sql->fetchAll();

		foreach ($resultado as $row) {
			$id = $row['id'];
			$visible = $row['Visible'];
			$titulo = $row['Titulo'];
			$fecha = $row['Fecha'];
			$publicador = $row['User_ID'];


			$busca_publicador = $db->prepare("SELECT * FROM usuarios WHERE id='$publicador'");
			$busca_publicador->execute();
			$result_publicador = $busca_publicador->fetchAll();
			foreach ($result_publicador as $row) {
				$Nombre_publicador = $row['Nombre'];
				$Apellido_Publicador = $row['Apellido'];
				$full_name_publicador = $Nombre_publicador.' '.$Apellido_Publicador;
				$cel_publicador = $row['Celular'];
			}

			switch ($visible) {
				case 0:
				$state_icon = '<div class="badge badge-outline-success">Visible</div>';
				break;
				case 1:
				$state_icon = '<div class="badge badge-outline-danger">Oculto</div>';
				break;
			}
			?>
			<tr>
				<td>
					<span class="pl-2"><?php echo $full_name_publicador; ?></span>
				</td>
				<td> <?php echo $cel_publicador; ?></td>
				<td> <?php echo $titulo; ?> </td>
				<td> <?php echo $fecha; ?> </td>
				<td>
					<form method="post">
						<input type="text" value="<?php echo $id; ?>" name="id_publicacion" hidden>
						<input type="text" value="<?php echo $visible; ?>" name="visible" hidden>
						<input type="submit" value="Cambiar Estado" class="btn btn-rounded btn-outline-info" name="cambia_estado">
					</form>
				</td>
				<td>
					<?php echo $state_icon; ?>
				</td>
			</tr>

		<?php } ?>
		
	</tbody>
</table>