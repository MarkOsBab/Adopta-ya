<?php 
include 'class.config.php';
include 'class.functions.php';
date_default_timezone_set('America/Montevideo');
$adminClass = new AdminClass();
$errorAdminLogin = "";
$secretKey  = "6Lcqz1waAAAAAEne1bBGK0-viDYvUOWXRnedTM5f";
$db = getDB();

/*/ Calcular tiempo /*/
function time_passed($get_timestamp) {
    $timestamp = strtotime($get_timestamp);
    $diff = time() - (int)$timestamp;

    if ($diff == 0) 
        return 'justo ahora';
    if ($diff > 604800)
        return date("Y m d",$timestamp);

    $intervals = array(
	    $diff < 31556926    => array('mes',   2628000),
	    $diff < 2629744     => array('semana',    604800),
	    $diff < 604800      => array('día',     86400),
	    $diff < 86400       => array('hora',    3600),
	    $diff < 3600        => array('minuto',  60),
	    $diff < 60          => array('segundo',  1)
	);

	$value = floor($diff/$intervals[1][1]);
	return 'hace '.$value.' '.$intervals[1][0].($value > 1 ? 's' : '');
}

if (!empty($_POST['admin_login'])) {
	if(isset($_POST['captcha-response']) && !empty($_POST['captcha-response'])){
		$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['captcha-response']);
		$responseData = json_decode($verifyResponse);
		if ($responseData->success) {
			$admin_correo = $_POST['admin_email'];
			$admin_password = $_POST['admin_password'];
			$admin_pin = $_POST['admin_pin'];

			$db = getDB();
			$busca_admin = $db->prepare("SELECT * FROM usuarios INNER JOIN usuarios_administradores ON usuarios.id = usuarios_administradores.Admin_ID WHERE Correo='$admin_correo'");
			$busca_admin->execute();
			$result_admin = $busca_admin->fetchAll();

			$count_admin = $db->prepare("SELECT COUNT(Correo) AS total FROM usuarios WHERE Correo='$admin_correo'");
			$count_admin->execute();
			$result_count_admin = $count_admin->fetchColumn();

			if ($result_count_admin == 0) {
				$errorAdminLogin = '<p class="w3-text-red"><i class="fas fa-exclamation-triangle"></i> <small>Verificación del captcha fallido, por favor intenta nuevamente.</small></p>';
			} else {
				foreach ($result_admin as $row) {
					$check_email = $row['Correo'];
					$check_password = $row['Password'];
					$check_pin = $row['Pin_Ingreso'];
				}
				if (strlen(trim($admin_correo))>1 && strlen(trim($admin_password))>1 && strlen(trim($admin_pin))>1) {
					$admin_login = $adminClass->adminLogin($admin_correo, $admin_password, $admin_pin);
					if ($admin_login) {
						$url = "inicio/";
						header("Location:$url");
					} elseif ($check_pin != $admin_pin){
						$errorAdminLogin = '<p class="w3-text-white text-left"><i class="fas fa-exclamation-triangle w3-text-red"></i> El pin es incorrecto</p>';
					} elseif ($check_password != $admin_password){
						$errorAdminLogin = '<p class="w3-text-white text-left"><i class="fas fa-exclamation-triangle w3-text-red"></i> La contraseña es incorrecta</p>';
					} elseif (empty($admin_pin)){
						$errorAdminLogin = '<p class="w3-text-white text-left"><i class="fas fa-exclamation-triangle w3-text-red"></i> Debes ingresar el pin para entrar.</p>';
					} elseif (empty($admin_correo)){
						$errorAdminLogin = '<p class="w3-text-white text-left"><i class="fas fa-exclamation-triangle w3-text-red"></i> Debes ingresar el correo para entrar.</p>';
					} elseif (empty($admin_password)){
						$errorAdminLogin = '<p class="w3-text-white text-left"><i class="fas fa-exclamation-triangle w3-text-red"></i> Debes ingresar la contraseña para entrar.</p>';
					}
				}
			}

		} else {
			$errorMsgLogin = '<p class="w3-text-white text-left"><i class="fas fa-exclamation-triangle w3-text-red"></i> Verificación del captcha fallido, por favor intenta nuevamente.</p>';
		}
	} else {
        $errorMsgLogin = '<p class="w3-text-white text-left"><i class="fas fa-exclamation-triangle w3-text-red"></i> Verificación del captcha fallido, por favor intenta nuevamente.</p>';
    }
}

if (!empty($_SESSION['admin_id'])) {
	$busca_admin = $db->prepare("SELECT * FROM usuarios_administradores WHERE Admin_ID=".$_SESSION['admin_id']."");
	$busca_admin->execute();
	$result_admin = $busca_admin->fetchAll();
	foreach ($result_admin as $row) {
		$Rango_ID = $row['Rango_ID'];
	}

	switch ($Rango_ID) {
		case 1:
			$Rango = "Dueño";
			break;
		case 2:
			$Rango = "Administrador";
			break;
		case 3:
			$Rango = "Atención al público";
			break;
		case 4:
			$Rango = "Administrador";
			break;
	}

	$busca_notificacion = $db->prepare("SELECT * FROM admin_notifications INNER JOIN usuarios_administradores ON admin_notifications.Receptor_ID = usuarios_administradores.Admin_ID WHERE admin_notifications.Receptor_ID = ".$_SESSION['admin_id']."");
	$busca_notificacion->execute();
	$result_notificacion = $busca_notificacion->fetchAll();
		
	$conta_mensajes = $db->prepare("SELECT COUNT(*) FROM admin_notifications WHERE Receptor_ID=".$_SESSION['admin_id']."");
	$conta_mensajes->execute();
	$result_mensajes = $conta_mensajes->fetchColumn();

	if ($result_mensajes > 1) {
		$dato = "nuevos mensajes";
	} else {
		$dato = "nuevo mensaje";
	}

	$busca_usuarios_registrados = $db->prepare("SELECT COUNT(*) FROM usuarios");
	$busca_usuarios_registrados->execute();
	$result_usuarios_registrados = $busca_usuarios_registrados->fetchColumn();

	$busca_adopciones = $db->prepare("SELECT COUNT(*) FROM publicaciones_animales WHERE Perdido=0");
	$busca_adopciones->execute();
	$result_adopciones = $busca_adopciones->fetchColumn();

	$busca_perdidos = $db->prepare("SELECT COUNT(*) FROM publicaciones_animales WHERE Perdido=1");
	$busca_perdidos->execute();
	$result_perdidos = $busca_perdidos->fetchColumn();
}

if (isset($_POST['cambia_estado'])) {
	$id_publicacion = $_POST['id_publicacion'];
	$visible = $_POST['visible'];
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	    $ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
	    $ip = $_SERVER['REMOTE_ADDR'];
	}

	if ($visible==0) {
		$inserta_edicion = $db->prepare("INSERT INTO admin_edits (Admin_ID, Texto, Fecha, IP) VALUES (".$_SESSION['admin_id'].", 'Editó el estado de la publicacion #$id_publicacion a no visible.', current_timestamp, '$ip')");
		$inserta_edicion->execute();
		$cambiar_no_visible = $db->prepare("UPDATE publicaciones_animales SET Visible=1 WHERE id='$id_publicacion'");
		$cambiar_no_visible->execute();
		header("Location: ?state_update=$id_publicacion");

	} elseif ($visible==1){
		$inserta_edicion = $db->prepare("INSERT INTO admin_edits (Admin_ID, Texto, Fecha, IP) VALUES (".$_SESSION['admin_id'].", 'Editó el estado de la publicacion #$id_publicacion a visible.', current_timestamp, '$ip')");
		$inserta_edicion->execute();
		$cambiar_no_visible = $db->prepare("UPDATE publicaciones_animales SET Visible=0 WHERE id='$id_publicacion'");
		$cambiar_no_visible->execute();
		header("Location: ?state_update=$id_publicacion");
	}
}

if (!empty($_POST['admin_actualizar_datos'])) {
	$Nombre = $_POST['edit_admin_nombre'];
	$Apellido = $_POST['edit_admin_apellido'];
	$Email = $_POST['edit_admin_email'];
	$Celular = $_POST['edit_admin_cel'];
	$Departamento = $_POST['edit_admin_dep'];

	$actualiza_datos = $db->prepare("UPDATE usuarios SET Nombre='$Nombre', Apellido='$Apellido', Correo='$Email', Celular='$Celular', Departamento='$Departamento' WHERE id=".$_SESSION['admin_id']."");
	$actualiza_datos->execute();

	if ($actualiza_datos) {
		header("Location: ../inicio");
	}
}

if (!empty($_POST['add_header'])) {
	$titulo_header = $_POST['titulo_header'];
	$contenido_header = $_POST['contenido_header'];
	$show_for = $_POST['show_for'];
	$imagen = $_FILES['image_header']['tmp_name'];
	$ext = explode(".", $_FILES['image_header']['name']);
	if (strtolower($ext[1]) == "png" || strtolower($ext[1]) == "jpg" || strtolower($ext[1]) == "jpeg") {
		$nombre = $_FILES["image_header"]["name"];
		$ext = pathinfo($nombre, PATHINFO_EXTENSION);
		$l_nombre = generar_token_seguro(5,$nombre).'.'.$ext;

		$diassemana = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
				 
		$date = $diassemana[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y');
		move_uploaded_file($_FILES["image_header"]["tmp_name"],'../../templates/static/images/headers/'.$l_nombre);

		$inserta_header = $db->prepare("INSERT INTO cms_home_headers (Titulo, Contenido, Imagen, Admin_ID, Show_For, Fecha) VALUES ('$titulo_header', '$contenido_header', '$l_nombre', ".$_SESSION['admin_id'].", '$show_for', '$date')");
		$inserta_header->execute();

		if ($inserta_header) {
			header("Location: ../ver-headers");
		}
	}
}

if (!empty($_GET['edit_header'])) {
	if (isset($_POST['edit_header'])) {
		$header_id = $_GET['edit_header'];
		$edit_titulo_header = $_POST['edit_titulo_header'];
		$edit_contenido_header = $_POST['edit_contenido_header'];
		$edit_show_for = $_POST['edit_show_for'];
		$edit_imagen = $_FILES['edit_image_header']['tmp_name'];
		$ext = explode(".", $_FILES['edit_image_header']['name']);
		if (strtolower($ext[1]) == "png" || strtolower($ext[1]) == "jpg" || strtolower($ext[1]) == "jpeg") {
			$nombre = $_FILES["edit_image_header"]["name"];
			$ext = pathinfo($nombre, PATHINFO_EXTENSION);
			$l_nombre = generar_token_seguro(5,$nombre).'.'.$ext;

			$busca_header_image = $db->prepare("SELECT * FROM cms_home_headers WHERE id='$header_id'");
			$busca_header_image->execute();
			$result_header_image = $busca_header_image->fetchAll();
			    foreach ($result_header_image as $row) {
			        $Header_Image = $row['Imagen'];
			    }

			unlink('../../templates/static/images/headers/'.$Header_Image);
			move_uploaded_file($_FILES["edit_image_header"]["tmp_name"],'../../templates/static/images/headers/'.$l_nombre);

			$inserta_header = $db->prepare("UPDATE cms_home_headers SET Titulo='$edit_titulo_header', Contenido='$edit_contenido_header', Imagen='$l_nombre', Show_For='$edit_show_for' WHERE id=$header_id");
			$inserta_header->execute();

			if ($inserta_header) {
				header("Location: ../ver-headers");
			}
		} else {}
	}
}

?>