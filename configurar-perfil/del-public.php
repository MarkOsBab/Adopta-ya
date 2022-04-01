<?php 
require_once '../classes/class.config.php';
$db = getDB();
if (empty($_SESSION['id'])) {
  header("Location: /login");
} else {
$ID_Publicacion = $_GET['publicacion'];

	$busca_imagenes = $db->prepare("SELECT * FROM publicaciones_imagenes WHERE ID_Publicacion='$ID_Publicacion' AND User_ID=".$_SESSION['id']."");
	$busca_imagenes->execute();
	$result_imagenes = $busca_imagenes->fetchAll();

	$busca_publicacion = $db->prepare("SELECT * FROM publicaciones_animales WHERE id='$ID_Publicacion' AND User_ID=".$_SESSION['id']."");
	$busca_publicacion->execute();
	$result_publicaciones = $busca_publicacion->fetchAll();

	
	foreach ($result_imagenes as $row) {
		$get_userID = $row['User_ID'];
		$imagenes = $row['imagenes'];

		if ($get_userID == $_SESSION['id']) {
			unlink('../publicaciones/imagenes/'.$imagenes);
			$borra_imagenes = $db->prepare("DELETE FROM publicaciones_imagenes WHERE ID_Publicacion='$ID_Publicacion' AND User_ID=".$_SESSION['id']."");
			$borra_imagenes->execute();
		}
	}
	foreach ($result_publicaciones as $row) {
		$user_publicacion = $row['User_ID'];
		$titulo = $row['Titulo'];
	
		if ($user_publicacion == $_SESSION['id']) {
			$borra_publicacion = $db->prepare("DELETE FROM publicaciones_animales WHERE id='$ID_Publicacion' AND User_ID=".$_SESSION['id']."");
			$borra_publicacion->execute();
		}
	}
	if ($borra_publicacion) {
		if ($borra_imagenes) {
			echo '<h4 class="style-text-2 default-font w3-text-green"><i class="far fa-badge-check"></i> ¡La publicación '.$titulo.' se borró exitosamente!</h4>';
			include 'consult.php';
		}
	}
}
?>