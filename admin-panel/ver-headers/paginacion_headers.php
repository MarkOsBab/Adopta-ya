<table class="table text-center">
	<thead>
		<tr>
			<th>Titulo</th>
            <th>Contenido</th>
            <th>Mostrar para</th>
            <th>Editar</th>
		</tr>
	</thead>
	<tbody>
		<?php
		
		
		$_BS['bypage'] = 6;
		require_once '../classes/class.config.php';
		$db = getDB();
		$conta_total = $db->prepare("SELECT COUNT(*) AS total FROM cms_home_headers");
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
		$sql = $db->prepare("SELECT * FROM `cms_home_headers` ORDER BY `id` ASC LIMIT ".$inicio.", ".$_BS['bypage']);
		$sql->execute();
		$resultado = $sql->fetchAll();

		foreach ($resultado as $row) {
			$id = $row['id'];
			$Titulo = $row['Titulo'];
			$Contenido = $row['Contenido'];
			$Show_For = $row['Show_For'];

			switch ($Show_For) {
				case 1:
					$resultShow =  "Todos";
					break;
				case 2:
					$resultShow = "Interes perros";
					break;
				case 3:
					$resultShow = "Interes gatos";
					break;
				case 0:
					$resultShow = "Nadie";
					break;
			}
			?>
			<tr>
				<td> <?php echo $Titulo; ?></td>
				<td> <?php echo $Contenido; ?> </td>
				<td> <?php echo $resultShow; ?></td>
				<td> <a href="../publicar-header?edit_header=<?php echo $id; ?>"><i class="mdi mdi-table-edit" style="font-size: 1.3rem;"></i></a></td>
			</tr>

		<?php } ?>
		
	</tbody>
</table>