<?php 
		require_once '../classes/class.config.php';
		$db = getDB();
		if (empty($_SESSION['id'])) {
		  header("Location: /login");
		} else {
		$busca_publicaciones = $db->prepare("SELECT * FROM publicaciones_animales  WHERE User_ID=".$_SESSION['id']."");
		$busca_publicaciones->execute();
		while ($row = $busca_publicaciones->fetch(PDO::FETCH_ASSOC)) {
			$id = $row['id'];
			$Perdido = $row['Perdido'];

			$info_imagenes = $db->prepare("SELECT MAX(id) FROM publicaciones_imagenes WHERE ID_Publicacion='$id'");
			$info_imagenes->execute();
			$info_imagenes_result=$info_imagenes->fetchColumn();

			$BuscaImg = $db->prepare("SELECT * FROM publicaciones_imagenes WHERE id='$info_imagenes_result'");
			$BuscaImg->execute();


			if ($Perdido == 1) {
					$Informacion =  "Publicado en perdidos";
			} else {
					$Informacion = "Publicado en adopción";
			}
			while ($img = $BuscaImg->fetch(PDO::FETCH_ASSOC)) {
				$imag = $img['imagenes'];
			}
			
			echo '
			<div class="table_content">
				<div class="row d-flex text-center border-top border-bottom">
					<div class="col-sm-2">
						<div class="slider slider-for">
							';
								echo '<img class="img-fluid" src="/publicaciones/imagenes/'.$imag.'" style="width: 140px; height: 100px;">';
							echo '
						</div>
					</div>
					<div class="col-sm-3 mt-4 border-bottom">
						<h3 class="style-text-1">'.$row['Titulo'].'</h3>
					</div>
					<div class="col-sm-3 mt-4 border-bottom">
						<h3 class="style-text-1">'.$row['Fecha'].'</h3>
					</div>
					<div class="col-sm-2 mt-4 border-bottom">
						<h3 class="style-text-1">'.$Informacion.'</h3>
					</div>
					<div class="col-sm-1 mt-4 border-bottom">
						<a style=\"text-decoration:underline;cursor:pointer;\" onclick="eliminarDato('.$row['id'].')"><i class="fal fa-trash-alt fa-2x color-2"></i></a>
						<a onclick=\"eliminarDato("'.$row['id'].'")\"></a>
					</div>
					<div class="col-sm-1 mt-4 border-bottom">
						<a href="/edit-publicacion?edit='.$id.'"><i class="fal fa-edit fa-2x w3-text-green"></i></a>
					</div>
				</div>
			</div>
			'; } ?>
<script type="text/javascript">
 $('.slider-for').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  infinite: true,
  speed: 300,
  centerMode: true,
  adaptiveHeight: false,
  arrows: false,
  fade: true,
  dots: false,
  asNavFor: '.slider-nav',
  autoplay: true,
  autoplaySpeed: 2000
});
$('.slider-nav').slick({
  slidesToShow: 3,
  slidesToScroll: 1,
  asNavFor: '.slider-for',
  focusOnSelect: true
});

</script>
<?php } ?>