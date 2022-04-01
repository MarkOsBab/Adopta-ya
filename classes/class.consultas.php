<?php 
include 'class.config.php';
include 'class.functions.php';
/*/ Mail send /*/
require_once "mail/Exception.php";
require_once "mail/OAuth.php";
require_once "mail/PHPMailer.php";
require_once "mail/POP3.php";
require_once "mail/SMTP.php";
require_once 'reCaptcha.php';

/*/ End mail send /*/
$adminClass = new AdminClass();
$userClass = new userClass();
$errorMsgRegistro = "";
$errorMsgRegistro_ALR = "";
$errorMsgLogin = "";
$errorMsgRecover = "";
$errorMsgPublicar = "";
$msgUserInfoError = "";
$editMsgError = "";
$msgErrorEdición = "";
$errorAdminLogin = "";
$errorChangeProfilePhoto = "";
$errorMsgZoom = "";

$secret  = "6Lcqz1waAAAAAEne1bBGK0-viDYvUOWXRnedTM5f";
$response = null;
$reCaptcha = new ReCaptcha($secret);

$db = getDB();

	if (isset($_POST['g-recaptcha-response'])) {
		$secret  = "6Lf9zXgaAAAAABs3Cbu3OME62jZxwjUl600-AP3C";
		$url = 'https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response'];
		$curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    $data = curl_exec($curl);
	    curl_close($curl);
	    $responseCaptchaData = json_decode($data);
    	if($responseCaptchaData->success) {
			$Nombre = $_POST['reg_nombre'];
			$Apellido = $_POST['reg_apellido'];
			$Correo = $_POST['reg_email'];
			$Password = $_POST['reg_password'];
			$Departamento = $_POST['reg_departamento'];
			$Cel = $_POST['reg_telcel'];
			$google_id = $_POST['google_id'];

			$email_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+.([a-zA-Z]{2,4})$~i', $Correo);
			$password_check = preg_match('~^[A-Za-z0-9!@#$%^&*()_]{6,20}$~i', $Password);
			$db = getDB();
			$buscarCorreo = "SELECT COUNT(Correo) AS total FROM usuarios WHERE Correo='$Correo'";
			$resultadoCorreo = $db->query($buscarCorreo);
			$contadorEmail = $resultadoCorreo->fetchColumn();
				$Registro = $userClass->registro_usuarios($Nombre, $Apellido, $Correo, $Password, $Cel, $Departamento, $google_id);
			if ($contadorEmail > 0) {
					$errorMsgRegistro_ALR = '<label class="text-danger"><i class="fas fa-exclamation-triangle"></i>Este correo electrónico ya se encuentra registrado.</label>';
			} else {
				if (!empty($Nombre) || !empty($Apellido) || !empty($Correo) || !empty($Password) || !empty($Departamento) || !empty($Cel)) {			
					if ($Registro) {
						$busca_registro = $db->prepare("SELECT * FROM usuarios INNER JOIN keys_usuarios ON usuarios.id = keys_usuarios.User_ID WHERE usuarios.Correo='$Correo'");
						$busca_registro->execute();
						$registro = $busca_registro->fetchAll();
						foreach ($registro as $row) {
							$token_registro = $row['origin_key'];
						}
							/*/ Si registra envia el email /*/
							$mail = new PHPMailer\PHPMailer\PHPMailer();
					       	//Enable SMTP debugging. 
							$mail->SMTPDebug = 0;                               
					       	//Set PHPMailer to use SMTP.
							$mail->isSMTP();            
					       	//Set SMTP host name                          
							$mail->Host = 'localhost';
							$mail->SMTPAuth = false;
							$mail->SMTPAutoTLS = false; 
							$mail->Port = 25; 
							$mail->From = "info@adoptaya.com";       
							$mail->FromName = "Adopta ¡YA!";         
							$mail->Username = "info@adoptaya.com";                 
							$mail->Password = "M.g4089188";                                                         
							
							$mail->addAddress($Correo);
							$mail->isHTML(true);
							$mail->CharSet = 'UTF-8';

							$mail->Subject = "Activa tu cuenta " . $Nombre .' '. $Apellido;
							include 'mail-activar.php';
							$mail->Body = $activar;
							if(!$mail->send()) 
					        {
					            echo "Mailer error: " . $mail->ErrorInfo;
					        } 
					        else 
					        {
					            echo "Enviado";
					        }
							

						$url = "/configurar-perfil";
						header("Location: $url");
					}
				} 
			}
		} else {
			$errorMsgRegistro = '<label class="text-danger"><i class="fas fa-exclamation-triangle"></i> Verificación del captcha fallido, intenta nuevamente.</label>';
		}
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
				$errorAdminLogin = '<p><i class="fas fa-exclamation-triangle"></i> <small>Verificación del captcha fallido, por favor intenta nuevamente.</small></p>';
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

if (isset($_POST['ay_iniciar_sesion'])) {
	if ($_POST["g-recaptcha-response"]) {
	    $response = $reCaptcha->verifyResponse(
	    $_SERVER["REMOTE_ADDR"],
	    $_POST["g-recaptcha-response"]
	    );
	}
	if ($response != null && $response->success) {
			$Correo = $_POST['ay_email'];
			$password = $_POST['ay_password'];
			$db = getDB();
			$sql = $db->prepare("SELECT * FROM usuarios WHERE Correo='$Correo'");
			$sql->execute();
			$resultado = $sql->fetchAll();

			$busca_Correo = "SELECT COUNT(Correo) FROM usuarios WHERE Correo='$Correo'";
			$result_Correo = $db->query($busca_Correo);
			$conta_Correo = $result_Correo->fetchColumn();

			if ($conta_Correo == 0) {
				$errorMsgLogin = '<p class="w3-text-red"><i class="fas fa-exclamation-triangle"></i> <small>El correo electrónico que ingresaste no se encuentra registrado</small></p>';
			} else {
				foreach ($resultado as $row) {
					$check_email = $row['Correo'];
					$check_password = $row['Password'];
					if(strlen(trim($Correo))>1 && strlen(trim($password))>1 ) {
						$id=$userClass->userLogin($Correo, $password);
						if ($id) {
							$url = '../';
							header("Location: $url");
						} elseif ($check_password != $password) {
							$errorMsgLogin = '<p class="w3-text-red"><i class="fas fa-exclamation-triangle"></i> <small>La contraseña es incorrecta</small></p>';
						}
					}
				}
			}
	} else {
        $errorMsgLogin = '<label><i class="fas fa-exclamation-triangle"></i> Por favor debes verificar que no eres un robot para ingresar.</label>';
    }
}

if (!empty($_POST['recup_send'])) {
	$Correo = $_POST['recup_correo'];

	$db = getDB();
	$buscarCorreo = "SELECT COUNT(Correo) AS total FROM usuarios WHERE Correo='$Correo'";
	$resultadoCorreo = $db->query($buscarCorreo);
	$contadorEmail = $resultadoCorreo->fetchColumn();

	if ($contadorEmail == 0) {
		$errorMsgRecover = '<p class="w3-text-red"><i class="fas fa-exclamation-triangle"></i> <small>Este correo no se encuentra registrado</small></p>';
	} else {
		$inserta_recup = $userClass->recup_password($Correo);
		if ($inserta_recup) {
			$busca_key = $db->prepare("SELECT * FROM recover_usuarios WHERE Correo='$Correo'");
			$busca_key->execute();
			$result_key = $busca_key->fetchAll();

			foreach ($result_key as $row) {
				$token_recover = $row['origin_key'];
			}
				
				/*/ Si registra envia el email /*/
				$mail = new PHPMailer\PHPMailer\PHPMailer();
	        	//Enable SMTP debugging. 
				$mail->SMTPDebug = 0;                               
	        	//Set PHPMailer to use SMTP.
				$mail->isSMTP();            
	        	//Set SMTP host name                          
				$mail->Host = 'localhost';
				$mail->SMTPAuth = false;
				$mail->SMTPAutoTLS = false; 
				$mail->Port = 25; 
				$mail->From = "info@adoptaya.com";       
				$mail->FromName = "Adopta ¡YA!";         
				$mail->Username = "info@adoptaya.com";                 
				$mail->Password = "M.g4089188";

				$mail->addAddress($Correo);

				$mail->isHTML(true);
				$mail->CharSet = 'UTF-8';

				$mail->Subject = "Recuperar tu contraseña";
				include 'mail-recup.php';
				$mail->Body = $recup;
				if(!$mail->send()) 
		        {
		            
		        } 
		        else 
		        {
		            
		        }
			
			$errorMsgRecover = "<p class='w3-text-green default-font' style='font-weight: bold; font-size:1.1em;'><i class='fad fa-check-circle'></i> Te hemos enviado un mensaje a tu casilla de correo electrónico para recuperar tu contraseña.</p>";
		}
	}
}

if (!empty($_POST['change_pass'])) {
	$pass_1 = $_POST['pass_1'];
	$pass_2 = $_POST['pass_2'];
	$Correo = $_GET['mail'];
	$Key = $_GET['recup'];
	$busca_user = $db->prepare("SELECT * FROM recover_usuarios INNER JOIN usuarios ON recover_usuarios.Correo = usuarios.Correo WHERE recover_usuarios.Correo='$Correo' AND recover_usuarios.origin_key='$Key'");
	$busca_user->execute();
	$user_info = $busca_user->fetchAll();

	foreach ($user_info as $row) {
		$password = $row['Password'];
		/*/ HASH password /*/
		function gen_token($pass, $salt) {
			$salt = strtolower($salt);
			$str = hash("sha512", $pass.$salt);
			$len = strlen($salt);
			return strtoupper(substr($str, $len, 17));
		}
		if (strlen(trim($password))>1) {
			if ($pass_1 == $pass_2) {
				$hash_password = gen_token($pass_1, $Correo);
				$cambia_pass = $db->prepare("UPDATE usuarios SET Password='$hash_password' WHERE Correo='$Correo'");
				$cambia_pass->execute();
				$cambia_estado = $db->prepare("UPDATE recover_usuarios SET estado=1 WHERE origin_key='$Key'");
				$cambia_estado->execute();
				header("Location: /pass_changed");
			}
		}
	}
}
if (isset($_POST['addAnimal'])) {
	$titulo = $_POST['titulo'];
	$tipoAnimal = $_POST['tipoAnimal'];
	$cantidadAnimales = $_POST['cantidadAnimales'];
	$edadAnimales = $_POST['edadAnimales'];
	$mesesYears = $_POST['mesesYears'];
	$whyAdopta = $_POST['whyAdopta'];
	$sexoAnimal = $_POST['sexoAnimal'];
	$sizeAnimal = $_POST['sizeAnimal'];
	$castradoAnimal = $_POST['castradoAnimal'];
	$desparasitadoAnimal = $_POST['desparasitadoAnimal'];
	$vacunaAnimal = $_POST['vacunaAnimal'];
	$deptoUsuario = $_POST['deptoUsuario'];
	$barrioUsuario = $_POST['barrioUsuario'];
	$Dept_Barrio = $deptoUsuario .' - '. $barrioUsuario;
	$whatsUsuario = $_POST['whatsUsuario'];
	$idUser = $_POST['idUser'];
	$perdido = $_POST['perdido'];

	if (isset($_FILES["imagen"])) {
		$cantidad = count($_FILES["imagen"]["tmp_name"]);
		if ($cantidad > 6) {
			$errorMsgPublicar = "<p class='w3-text-red default-font' style='font-weight: bold; font-size:1.3em;'><i class='fas fa-exclamation-triangle'></i> No puedes publicar más de 6 imágenes por publicación</p>";
		} else {
			for ($i=0; $i<$cantidad; $i++){
				//Comprobamos si el fichero es una imagen
				if ($_FILES['imagen']['type'][$i]=='image/png' || $_FILES['imagen']['type'][$i]=='image/jpeg' || $_FILES['imagen']['type'][$i]=='image/jpg'){
					//Subimos el fichero al servidor
					//Where $file is an instance of Illuminate\Http\UploadFile
					$nombre = $_FILES["imagen"]["name"][$i];
					$ext = pathinfo($nombre, PATHINFO_EXTENSION);
					$l_nombre = generar_token_seguro(5,$nombre).'.'.$ext;

					$compressedImage = compressImage($_FILES["imagen"]["tmp_name"][$i],'../publicaciones/imagenes/'.$l_nombre, 40);

					$get_last_id = $db->prepare("SELECT MAX(id) AS id FROM publicaciones_animales");
					$get_last_id->execute();

					$result_id = $get_last_id->fetch(PDO::FETCH_NUM);
					$id = trim($result_id[0]);

					$last_id = $id++;

					$sube_img_db = $db->prepare("INSERT INTO publicaciones_imagenes (imagenes, creado, User_ID, ID_Publicacion) VALUES ('$l_nombre', current_timestamp, '$idUser', '$id')");
					$sube_img_db->execute();

					$validar=true;
				} else $validar=false;
			}
			$inserta_animal = $userClass->publicarAnimal($perdido, $titulo, $tipoAnimal, $cantidadAnimales, $edadAnimales, $mesesYears, $whyAdopta, $sexoAnimal, $sizeAnimal, $castradoAnimal, $desparasitadoAnimal, $vacunaAnimal, $Dept_Barrio, $whatsUsuario, $idUser);

			if ($inserta_animal) {
				header("Location: /publicaciones/$id");
			}
		}
	}
}

if (!empty($_GET['publicacion'])) {
	$Publicacion_ID = $_GET['publicacion'];
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	    $getData = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	    $getData = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
	    $getData = $_SERVER['REMOTE_ADDR'];
	}
	$info_publicacion = $db->prepare("SELECT * FROM publicaciones_animales WHERE id = '$Publicacion_ID'");
	$info_publicacion->execute();

	foreach ($info_publicacion as $row) {

	$User_ID = $row['User_ID'];
	$Titulo = $row['Titulo'];
	$Tipo_Animal = $row['Tipo_Animal'];
	$Edad_Animal = $row['Edad_Animal'];
	$Meses_Years = $row['Meses_Years'];
	$Descripcion = $row['Descripcion'];
	$Sexo = $row['Sexo'];
	$Size = $row['Size'];
	$Castrado = $row['Castrado'];
	$Desparasitado = $row['Desparasitado'];
	$Vacunado = $row['Vacunado'];
	$Cantidad_Animales = $row['Cantidad_Animales'];
	$Perdido = $row['Perdido'];
	$Show_Photo = $row['Show_Photo'];

	$Dept_Barrio = $row['Localidad'];
	$WhatsApp = $row['WhatsApp'];

	$Fecha = $row['Fecha'];
	
	$info_imagenes = $db->prepare("SELECT * FROM publicaciones_imagenes WHERE ID_Publicacion='$Publicacion_ID'");
	$info_imagenes->execute();

	$user_info = $db->prepare("SELECT * FROM usuarios WHERE id='$User_ID'");
	$user_info->execute();

	

	$contaData = $db->prepare("SELECT COUNT(*) FROM cms_dinamic_app WHERE Data_IP='$getData'");
	$contaData->execute();
	$resultCountData = $contaData->fetchColumn();
	
	$ContaVisitas = $db->prepare("SELECT * FROM cms_dinamic_app WHERE Data_IP='$getData'");
	$ContaVisitas->execute();
	$resultVisitas = $ContaVisitas->fetchAll();
	foreach ($resultVisitas as $row) {
		$Visitas_Gatos = $row['Visit_Gatos'];
		$Visitas_Perros = $row['Visit_Perros'];
		$Visitas_Adopcion = $row['Visit_Adopcion'];
		$Visitas_Perdidos = $row['Visit_Perdidos'];

		if ($resultCountData>=1) {
			switch ($Tipo_Animal) {
				case 'Gato':
				$Visitas_Gatos++;
				$addCountGato = $db->prepare("UPDATE cms_dinamic_app SET Visit_Gatos='$Visitas_Gatos' WHERE Data_IP='$getData'");
				$addCountGato->execute();
				break;
				case 'Perro':
				$Visitas_Perros++;
				$addCountPerro = $db->prepare("UPDATE cms_dinamic_app SET Visit_Perros='$Visitas_Perros' WHERE Data_IP='$getData'");
				$addCountPerro->execute();
				break;
			}
			switch ($Perdido) {
				case 0:
				$Visitas_Adopcion++;
				$addCountAdopcion = $db->prepare("UPDATE cms_dinamic_app SET Visit_Adopcion='$Visitas_Adopcion' WHERE Data_IP='$getData'");
				$addCountAdopcion->execute();
				break;
				case 1:
				$Visitas_Perdidos++;
				$addCountPerdido = $db->prepare("UPDATE cms_dinamic_app SET Visit_Perdidos='$Visitas_Perdidos' WHERE Data_IP='$getData'");
				$addCountPerdido->execute();
				break;
			}
		}
	}

	
	}
}


if (isset($_POST['edit_user'])) {
	$edit_correo = $_POST['edit_correo'];
	$edit_cel = $_POST['edit_cel'];
	$edit_departamento = $_POST['edit_departamento'];

	if (!empty($edit_correo) || !empty($edit_cel) || !empty($edit_departamento)) {
		$resultado = $userClass->editUser($edit_correo, $edit_cel, $edit_departamento);
		$msgUserInfoError = "<p class='w3-text-green default-font' style='font-weight: bold; font-size:1.3em;'><i class='fad fa-check-circle'></i> Datos actualizados con éxito.</p>";
	} else {
		$msgUserInfoError = "<p class='w3-text-red default-font' style='font-weight: bold; font-size:1.3em;'><i class='fas fa-exclamation-triangle'></i> Por favor completa todos los campos requeridos.</p>";
	}

}

if (!empty($_POST['edit_password'])) {
	$current_password = $_POST['current_password'];
	$change_pass_1 = $_POST['change_pass_1'];
	$confirm_new_pass = $_POST['confirm_new_pass'];
	/*/ HASH password /*/
	function gen_token($pass, $salt) {
		$salt = strtolower($salt);
		$str = hash("sha512", $pass.$salt);
		$len = strlen($salt);
		return strtoupper(substr($str, $len, 17));
	}

	$busca_user = $db->prepare("SELECT * FROM usuarios WHERE id=".$_SESSION['id']."");
	$busca_user->execute();

	foreach ($busca_user as $row) {
		$Password = $row['Password'];
		$Correo = $row['Correo'];
	}

	$hash_current_password = gen_token($current_password, $Correo);

	if ($hash_current_password != $Password) {
		$msgUserInfoError = "<p class='w3-text-red default-font' style='font-weight: bold; font-size:1.3em;'><i class='fas fa-exclamation-triangle'></i> La contraseña actual es incorrecta.</p>";
	} else {
		if ($change_pass_1 == $confirm_new_pass) {
			$resultado = $userClass->editPassword($current_password, $change_pass_1, $confirm_new_pass);	
			$msgUserInfoError = "<p class='w3-text-green default-font' style='font-weight: bold; font-size:1.1em;'><i class='fad fa-check-circle'></i> Contraseña cambiada con éxito.</p>";
		} else {
			$msgUserInfoError = "<p class='w3-text-red default-font' style='font-weight: bold; font-size:1.1em;'><i class='fas fa-exclamation-triangle'></i> Las contraseñas no coinciden.</p>";
		}
	}
}

if (!empty($_POST['edit_publiacion'])) {
	$id = $_GET['edit'];

	$busca_edit = $db->prepare("SELECT * FROM publicaciones_animales WHERE id=$id");
	$busca_edit->execute();

	$conta_edit = count($busca_edit->fetchAll( PDO::FETCH_BOTH ) );
	if ($conta_edit > 0) {
		$edit_titulo = addslashes($_POST['edit_titulo']);
		$edit_tipoAnimal = addslashes($_POST['edit_tipoAnimal']);
		$edit_cantidadAnimales = addslashes($_POST['edit_cantidadAnimales']);
		$edit_edadAnimales = addslashes($_POST['edit_edadAnimales']);
		$edit_mesesYears = addslashes($_POST['edit_mesesYears']);
		$edit_whyAdopta = addslashes($_POST['edit_whyAdopta']);
		$edit_sexoAnimal = addslashes($_POST['edit_sexoAnimal']);
		$edit_sizeAnimal = addslashes($_POST['edit_sizeAnimal']);
		$edit_castradoAnimal = addslashes($_POST['edit_castradoAnimal']);
		$edit_desparasitadoAnimal = addslashes($_POST['edit_desparasitadoAnimal']);
		$edit_vacunaAnimal = addslashes($_POST['edit_vacunaAnimal']);
		$edit_barrioUsuario = addslashes($_POST['edit_barrioUsuario']);
		$edit_whatsUsuario = addslashes($_POST['edit_whatsUsuario']);

		$finaliza_edicion = $db->prepare("UPDATE publicaciones_animales SET Titulo='$edit_titulo', Tipo_Animal='$edit_tipoAnimal', Cantidad_Animales='$edit_cantidadAnimales', Edad_Animal='$edit_edadAnimales', Meses_Years='$edit_mesesYears', Descripcion='$edit_whyAdopta', Sexo='$edit_sexoAnimal', Size='$edit_sizeAnimal', Castrado='$edit_castradoAnimal', Desparasitado='$edit_desparasitadoAnimal', Vacunado='$edit_vacunaAnimal', Barrio='$edit_barrioUsuario', WhatsApp='$edit_whatsUsuario', Fecha=current_timestamp WHERE id=$id");
		$finaliza_edicion->execute();
		if ($finaliza_edicion) {
			$ip = $_SERVER['REMOTE_ADDR']; 
			$inserta_edicion = $db->prepare("INSERT INTO publicaciones_editadas (UserID, PublicacionID, Fecha, IP) VALUES (".$_SESSION['id'].", '$id', current_timestamp, '$ip')");
			$inserta_edicion->execute();
			$msgErrorEdición = "<p class='w3-text-green default-font' style='font-weight: bold; font-size:1.1em;'><i class='fad fa-check-circle'></i> Publicación editada con éxito. Serás redirigido en 5 segundos..</p>
				<script type='text/javascript' language='JavaScript'>
					setTimeout(function () { location.href = '/mis-publicaciones';
					}, 5000);
				</script>";
		}
	}

}

/*/ Contacto /*/
if (isset($_GET['cAccion']) == 'creaContacto') {
	$Nombre_Contacto = $_POST['contact_nombre'];
	$Email_Contacto = $_POST['contact_correo'];
	$Mensaje_Contacto = $_POST['contact_mensaje'];
	$token_contact = generar_token_seguro(70);

		$inserta_contact = $db->prepare("INSERT INTO contacto (Nombre, Correo, Mensaje, token) VALUES ('$Nombre_Contacto', '$Email_Contacto', '$Mensaje_Contacto', '$token_contact')");
		$inserta_contact->execute();
		$reply = array(
				'reply' => 'Mensaje enviado con éxito.'
			);
	echo json_encode($reply);
}


$conta_publicaciones = $db->prepare("SELECT COUNT(*) FROM publicaciones_animales");
$conta_publicaciones->execute();
$result_count = $conta_publicaciones->fetchColumn();

if (isset($_POST['change'])) {
	$imagen = $_FILES['profile_image']['tmp_name'];
	$ext = explode(".", $_FILES['profile_image']['name']);

	if (strtolower($ext[1]) == "png" || strtolower($ext[1]) == "jpg" || strtolower($ext[1]) == "jpeg") {
		$nombre = $_FILES["profile_image"]["name"];
		$ext = pathinfo($nombre, PATHINFO_EXTENSION);
		$l_nombre = generar_token_seguro(5,$nombre).'.'.$ext;

		$busca_user=$db->prepare("SELECT Token_ID FROM usuarios WHERE id=".$_SESSION['id']."");
		$busca_user->execute();
		$result_user = $busca_user->fetchAll();
		foreach ($result_user as $row) {
			$u_Token = $row['Token_ID'];
			
		}
		$busca_profile_image = $db->prepare("SELECT * FROM usuarios_profile_images WHERE Token_ID='$u_Token'");
		$busca_profile_image->execute();
		$result_profile_image = $busca_profile_image->fetchAll();
		    foreach ($result_profile_image as $row) {
		        $Profile_Image = $row['Profile_Image'];
		    }
			if ($Profile_Image != 'default_profile.jpg') {
				unlink('../templates/static/images/profile_images/'.$Profile_Image);
				$compressedImage = compressImage($_FILES["profile_image"]["tmp_name"],'../templates/static/images/profile_images/'.$l_nombre, 40); 
				$sube_img_db = $db->prepare("UPDATE usuarios_profile_images SET Profile_Image='$l_nombre' WHERE Token_ID='$u_Token'");
				$sube_img_db->execute();
			} else {
				$compressedImage = compressImage($_FILES["profile_image"]["tmp_name"],'../templates/static/images/profile_images/'.$l_nombre, 40); 
				$sube_img_db = $db->prepare("UPDATE usuarios_profile_images SET Profile_Image='$l_nombre' WHERE Token_ID='$u_Token'");
				$sube_img_db->execute();
			}
		} else {
			$errorChangeProfilePhoto = '<p class="style-text-2 text-center text-bold" style="font-size: 1.2em;"><i class="fas fa-exclamation-square color-2"></i> El formato del archivo que intentas subir no es válido.</p>';
		}
	}

if (isset($_POST['dona_mp'])) {
	// SDK de Mercado Pago
	require_once 'mercadopago/vendor/autoload.php';
	// Agrega credenciales
	MercadoPago\SDK::setAccessToken('APP_USR-8185561060799818-021123-4a0a684886427f7d5319c522e0e482fc-714353892');
	$ammount = $_POST['ammount_mp'];
	$transaction_token = $_POST['token_transaction'];
	$preference = new MercadoPago\Preference();

	$item = new MercadoPago\Item();
	$item->id = "1";
	$item->title = "Donación $".$ammount;
	$item->description = "Agradecemos por la iniciativa que has tomado.";
	$item->category_id = "Donacion";
	$item->quantity = 1;
	$item->currency_id = "UYU";
	$item->unit_price = $ammount;
	$preference->back_urls = array(
    "success" => "https://adoptaya.com/success.php?token=".$transaction_token."&ammount=".$ammount."",
    "failure" => "https://adoptaya.com/",
    "pending" => "https://adoptaya.com/"
	);
	$preference->items = array($item);
	$preference->save();
	header("Location: $preference->init_point");
}

if (isset($_POST['g-recaptcha-response'])) {
	$secret  = "6LdcTYAaAAAAACgXJdl0yLdcRPMnbmIL3gyhX8wo";
		$url = 'https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response'];
		$curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    $data = curl_exec($curl);
	    curl_close($curl);
	    $responseCaptchaData = json_decode($data);
	if($responseCaptchaData->success) {
		$zoom_ref_prot = $_POST['zoom_ref_prot'];
		$zoom_nombre = $_POST['zoom_nombre'];
		$zoom_correo = $_POST['zoom_correo'];
		$zoom_telCel = $_POST['zoom_telCel'];

		$inserta_zoom = $db->prepare("INSERT INTO reunion_zoom (Tipo, Nombre, Correo, Tel_Cel) VALUES ('$zoom_ref_prot', '$zoom_nombre', '$zoom_correo', '$zoom_telCel')");
		$inserta_zoom->execute();
		if ($inserta_zoom) {
			$errorMsgZoom = '<label class="text-success"><i class="fad fa-badge-check"></i> Datos enviados correctamente.</label>';
		}
	} else {
			$errorMsgZoom = '<label class="text-danger"><i class="fas fa-exclamation-triangle"></i> Verificación del captcha fallido, intenta nuevamente.</label>';
		}
}

if (!empty($_GET['mail']) && !empty($_GET['reunion_zoom']) && $_GET['reunion_zoom'] == 1) {
	$correo = $_GET['mail'];
	$verifica = $db->prepare("UPDATE reunion_zoom SET Asiste=1 WHERE Correo='$correo'");
	$verifica->execute();
}

?>