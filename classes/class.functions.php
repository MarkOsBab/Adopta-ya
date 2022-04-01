<?php 
use GeoIp2\Database\Reader;
class userClass {
	public function registro_usuarios($Nombre, $Apellido, $Correo, $password, $Celular, $Departamento, $google_id) {
		try{
			/*/ HASH password /*/
			function gen_token($pass, $salt) {
				$salt = strtolower($salt);
				$str = hash("sha512", $pass.$salt);
				$len = strlen($salt);
				return strtoupper(substr($str, $len, 17));
			}
			$db = getDB();
			$st = $db->prepare("SELECT id FROM usuarios WHERE Correo=:Correo"); 
			$st->bindParam("Correo", $Correo,PDO::PARAM_STR);
			$token_registro = generar_token_seguro(70);

			$st->execute();
			$count=$st->rowCount();

			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			    $ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
			    $ip = $_SERVER['REMOTE_ADDR'];
			}
			require dirname(__FILE__).'/geoip/vendor/autoload.php';
			

			// This creates the Reader object, which should be reused across
			// lookups.
			$reader = new Reader(dirname(__FILE__).'/geoip/vendor/geoip2/geoip2/GeoLite2-City.mmdb');
			$record = $reader->city($ip);

			$Localizacion = $record->city->name;
			$Codigo_Postal = $record->postal->code;		

			$cell_Extension = "+598";

			$Cel = $cell_Extension.''.$Celular;

			$token_id = generar_token_seguro(50);

			$diassemana = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
			$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
				 
			$date = $diassemana[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y');

			if ($count<1) {
				
				$stmt = $db->prepare("INSERT INTO usuarios (Nombre, Apellido, Correo, Password, Celular, Departamento, Localizacion, Postal_Code, Date_Registro, IP, Token_ID, google_id) VALUES (:Nombre, :Apellido, :Correo, :hash_password, '$Cel', :Departamento, '$Localizacion', '$Codigo_Postal', '$date', '$ip', '$token_id', :google_id)");

				$stmt->bindParam("Nombre", $Nombre,PDO::PARAM_STR);
				$stmt->bindParam("Apellido", $Apellido,PDO::PARAM_STR);
				$hash_password = gen_token($password, $Correo);
				$stmt->bindParam("hash_password", $hash_password,PDO::PARAM_STR);
				$stmt->bindParam("Correo", $Correo,PDO::PARAM_STR);
				$stmt->bindParam("Departamento", $Departamento,PDO::PARAM_STR);
				$stmt->bindParam("google_id", $google_id,PDO::PARAM_STR);
				$stmt->execute();
		    	$id=$db->lastInsertId(); // Last inserted row id

		    	$default_img = "default_profile.jpg";

		    	$insert_profile_img = $db->prepare("INSERT INTO usuarios_profile_images (Token_ID, Profile_Image) VALUES ('$token_id', '$default_img')");
		    	$insert_profile_img->execute();
		    	
		    	$origin = $db->prepare("INSERT INTO keys_usuarios (origin_key, Verificada, User_ID) VALUES ('$token_registro', 0, $id)");
		    	$origin->execute();

		    	$db = null;
		    	$_SESSION['id']=$id;
		    	$db = getDB();
		    	$st1 = $db->prepare("SELECT id FROM usuarios ORDER BY Date_Registro DESC LIMIT 1");
		    	$st1->execute();
		    	return true;
		    } else {
		    	$db = null;
		    	return false;
		    }
		    
		} catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}'; 
		}
	}

	/* User Login */
	public function userLogin($Correo,$password) {
		try {
			/*/ HASH password /*/
			function gen_token($pass, $salt) {
				$salt = strtolower($salt);
				$str = hash("sha512", $pass.$salt);
				$len = strlen($salt);
				return strtoupper(substr($str, $len, 17));
			}
			$db = getDB();
			$stmt = $db->prepare("SELECT id FROM usuarios WHERE Correo=:Correo AND Password=:hash_password"); 
			$stmt->bindParam("Correo", $Correo,PDO::PARAM_STR) ;
			$hash_password = gen_token($password, $Correo);
			$stmt->bindParam("hash_password", $hash_password,PDO::PARAM_STR) ;
			$stmt->execute();
			$count=$stmt->rowCount();
			$data=$stmt->fetch(PDO::FETCH_OBJ);
			$db = null;
			if($count) {
		      	$_SESSION['id']=$data->id; // Storing user session value
		      	return true;
		      } else {
		      	return false;
		      }
		  } catch(PDOException $e) {
		  	echo '{"error":{"text":'. $e->getMessage() .'}}';
		  }
		}

	public function editUser($Correo, $Celular, $Departamento){
		try {
			$db = getDB();
			$stmt = $db->prepare("SELECT * FROM usuarios WHERE id=".$_SESSION['id']."");
			$stmt->execute();
			$count=$stmt->rowCount();
			
			if ($count) {
				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				    $ip = $_SERVER['HTTP_CLIENT_IP'];
				} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
				    $ip = $_SERVER['REMOTE_ADDR'];
				}
				require dirname(__FILE__).'/geoip/vendor/autoload.php';
			

				// This creates the Reader object, which should be reused across
				// lookups.
				$reader = new Reader(dirname(__FILE__).'/geoip/vendor/geoip2/geoip2/GeoLite2-City.mmdb');
				$record = $reader->city($ip);

				$Localizacion = $record->city->name;
				$Codigo_Postal = $record->postal->code;	

		        geoip_close($GeoIP);
				$stmt = $db->prepare("UPDATE usuarios SET Correo=:Correo, Celular=:Celular, Departamento=:Departamento, IP='$ip', Localizacion='$Localizacion', Postal_Code='$Codigo_Postal' WHERE id=".$_SESSION['id']."");
				$stmt->bindParam("Correo", $Correo,PDO::PARAM_STR);
				$stmt->bindParam("Celular", $Celular,PDO::PARAM_STR);
				$stmt->bindParam("Departamento", $Departamento,PDO::PARAM_STR);
				$stmt->execute();
			}
			
		} catch (Exception $e) {
			echo '{error":{text:'. $e->getMessage() .'}}';
		}
	}

	public function editPassword($OldPassword, $NewPassword, $ConfirmNewPassword){
		try {
			
			$db = getDB();
			$stmt = $db->prepare("SELECT * FROM usuarios WHERE id=".$_SESSION['id']."");
			$stmt->execute();
			foreach ($stmt as $row) {
				$Correo = $row['Correo'];
			}

			$count=$stmt->rowCount();

			if ($count) {
				$stmt = $db->prepare("UPDATE usuarios SET Password=:hash_password WHERE id=".$_SESSION['id']."");
				$hash_password = gen_token($NewPassword, $Correo);
				$stmt->bindParam("hash_password", $hash_password,PDO::PARAM_STR);
				$stmt->execute();
			}
		} catch (Exception $e) {
			echo '{error":{text:'. $e->getMessage() .'}}';
		}
	}

		/* User Details */
		public function userDetails($id) {
			try {
				$db = getDB();
				$stmt = $db->prepare("SELECT * FROM usuarios INNER JOIN keys_usuarios ON usuarios.id = keys_usuarios.User_ID WHERE usuarios.id=:id"); 
				$stmt->bindParam("id", $id,PDO::PARAM_INT);
				$stmt->execute();
		    $data = $stmt->fetch(PDO::FETCH_OBJ); //User data
		    $user_id=$data->id; // Storing user session value

		    return $data;
		} catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
	}

	public function recup_password($Correo){
		try {
			$db = getDB();
			$st = $db->prepare("SELECT id FROM usuarios WHERE Correo=:Correo");
			$st->bindParam("Correo", $Correo,PDO::PARAM_STR);
			$st->execute();
			$token_recover = generar_token_seguro(70);

			$count=$st->rowCount();

			if ($count > 0) {
				$stmt = $db->prepare("INSERT INTO recover_usuarios (Correo, origin_key) VALUES (:Correo, '$token_recover')");
				$stmt->bindParam("Correo", $Correo,PDO::PARAM_STR);
				$stmt->execute();
				return true;
			} else {
				return false;
			}

			$db = null;
		} catch (Exception $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
	}

	public function publicarAnimal($perdido, $Titulo, $Tipo, $Cantidad, $Edad, $MesesYears, $Descripcion, $Sexo, $Size, $Castrado, $Desparasitado, $Vacunado, $Dept_Barrio, $WhatsApp, $idUser){
		try {
			$db = getDB();
			
			$stmt = $db->prepare("SELECT * FROM usuarios INNER JOIN keys_usuarios ON usuarios.id = keys_usuarios.User_ID WHERE usuarios.id = :idUser AND keys_usuarios.Verificada = 1");
			$stmt->bindParam("idUser", $idUser, PDO::PARAM_STR);
			$stmt->execute();
			$count=$stmt->rowCount();
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			    $ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
			    $ip = $_SERVER['REMOTE_ADDR'];
			}
			if ($count > 0) {

				$busca_null = $db->prepare("SELECT MAX(id) FROm publicaciones_animales");
				$busca_null->execute();

				$result_null = $busca_null->fetchColumn();

				$busca_ultimo_id = $db->prepare("SELECT * FROM publicaciones_animales ORDER by id DESC LIMIT 1");
				$busca_ultimo_id->execute();
				$result_utlimo_id = $busca_ultimo_id->fetchAll();

				if ($result_null==null) {
					$newID=1;
				} else {
					foreach ($result_utlimo_id as $row) {
						$newID = $row['id'];
						$newID++;
					}
				}		
				$diassemana = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
				$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
				 
				$date = $diassemana[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y');
				
				
				$stmt = $db->prepare("INSERT INTO publicaciones_animales (id, Perdido, Titulo, Tipo_Animal, Cantidad_Animales, Edad_Animal, Meses_Years, Descripcion, Sexo, Size, Castrado, Desparasitado, Vacunado, Localidad, WhatsApp, User_ID, User_IP, Fecha) VALUES ('$newID', :perdido, :Titulo, :Tipo, :Cantidad, :Edad, :MesesYears, :Descripcion, :Sexo, :Size, :Castrado, :Desparasitado, :Vacunado, :Dept_Barrio, :WhatsApp, :idUser, '$ip', '$date')");
				$stmt->bindParam("Titulo", $Titulo,PDO::PARAM_STR);
				$stmt->bindParam("Tipo", $Tipo,PDO::PARAM_STR);
				$stmt->bindParam("Cantidad", $Cantidad,PDO::PARAM_STR);
				$stmt->bindParam("Edad", $Edad,PDO::PARAM_STR);
				$stmt->bindParam("MesesYears", $MesesYears,PDO::PARAM_STR);
				$stmt->bindParam("Descripcion", $Descripcion,PDO::PARAM_STR);
				$stmt->bindParam("Sexo", $Sexo,PDO::PARAM_STR);
				$stmt->bindParam("Size", $Size,PDO::PARAM_STR);
				$stmt->bindParam("Castrado", $Castrado,PDO::PARAM_STR);
				$stmt->bindParam("Desparasitado", $Desparasitado,PDO::PARAM_STR);
				$stmt->bindParam("Vacunado", $Vacunado,PDO::PARAM_STR);
				$stmt->bindParam("Dept_Barrio", $Dept_Barrio, PDO::PARAM_STR);
				$stmt->bindParam("WhatsApp", $WhatsApp, PDO::PARAM_STR);
				$stmt->bindParam("idUser", $idUser,PDO::PARAM_STR);
				$stmt->bindParam("perdido", $perdido,PDO::PARAM_STR);
				$stmt->execute();
				
				$id=$db->lastInsertId(); // Last inserted row id
				return true;
			} else {
				return false;
			}
			$db = null;
		} catch (Exception $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
	}
}

function generar_token_seguro($longitud) {
	if ($longitud < 4) {
		$longitud = 4;
	}
	return bin2hex(openssl_random_pseudo_bytes(($longitud - ($longitud % 2)) / 2));
}

function activar_cuenta(){
	if (!empty($_GET['reg'])) {
		$Token = $_GET['reg'];
		$Correo = $_GET['mail'];
		$db = getDB();
		$getToken = $db->prepare("SELECT COUNT(*) FROM usuarios INNER JOIN keys_usuarios ON usuarios.id = keys_usuarios.User_ID WHERE keys_usuarios.origin_key = '$Token' AND usuarios.Correo = '$Correo' AND keys_usuarios.Verificada = 0");
		$getToken->execute();
		$token = $getToken->fetchColumn();

		if ($token > 0) {
			$getInfo = $db->prepare("SELECT * FROM usuarios INNER JOIN keys_usuarios ON usuarios.id = keys_usuarios.User_ID WHERE keys_usuarios.origin_key = '$Token' AND usuarios.Correo = '$Correo' AND keys_usuarios.Verificada = 0");
			$getInfo->execute();
			$tokenInfo = $getInfo->fetchAll();
			foreach ($tokenInfo as $row) {
				$origin_key = $row['origin_key'];
				$Correo = $row['Correo'];
				$Verificada = $row['Verificada'];

				if ($Verificada == 0) {
					$ActivaCuenta = $db->prepare("UPDATE keys_usuarios INNER JOIN usuarios ON keys_usuarios.User_ID = usuarios.id SET Verificada = 1 WHERE keys_usuarios.origin_key='$origin_key' AND usuarios.Correo='$Correo'");
					$ActivaCuenta->execute();
				}
			}
		}
	}
}


function reply_activacion() {
	if (!empty($_GET['replyMail'])) {
		$db = getDB();
		$U_ID = $_GET['id'];
		$busca_user = $db->prepare("SELECT * FROM Usuarios INNER JOIN keys_usuarios ON usuarios.id = keys_usuarios.User_ID WHERE usuarios.id=$U_ID");
		$busca_user->execute();
		$result_user = $busca_user->fetchAll();

		foreach ($result_user as $row) {
			$Nombre = $row['Nombre'];
			$Apellido = $row['Apellido'];
			$Correo = $row['Correo'];
			$token_registro = $row['origin_key'];
			$Departamento = $row['Departamento'];
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
		include 'classes/mails.php';
		$mail->Body = $activar;
		if(!$mail->send()) 
		{
			echo "No enviado";
		} 
		else 
		{
			echo "Enviado";
		}
		$url = "/reenviado";
		header("Location: $url");
	}
}

function send_contacto_usuario() {
	if (!empty($_GET['contactar']) && !empty($_GET['contactador']) && !empty($_GET['public_id'])) {
		$db = getDB();
		$id_publicacion = $_GET['public_id'];
		$contactado_ID = $_GET['contactar'];
		$contactador_ID = $_GET['contactador'];

		$contactado_info = $db->prepare("SELECT * FROM usuarios WHERE id='$contactado_ID'");
		$contactado_info->execute();

		$result_contactado = $contactado_info->fetchAll();

		foreach ($result_contactado as $contactado) {
			$Correo_Contactado = $contactado['Correo'];
			$Nombre_Contactado = $contactado['Nombre'];
			$Apellido_Contactado = $contactado['Apellido'];
		}

		$contactador_info = $db->prepare("SELECT * FROM usuarios WHERE id='$contactador_ID'");
		$contactador_info->execute();

		$result_contactador = $contactador_info->fetchAll();

		foreach ($result_contactador as $contactador) {
			$Correo_Contactador = $contactador['Correo'];
			$Nombre_Contactador = $contactador['Nombre'];
			$Apellido_Contactador = $contactador['Apellido'];
			$Celular_Contactador = $contactador['Celular'];
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

		$mail->addAddress($Correo_Contactado);

		$mail->isHTML(true);
		$mail->CharSet = 'UTF-8';

		$mail->Subject = "A ". $Nombre_Contactador.' '. $Apellido_Contactador." le interesó tu publicación";
		include 'classes/mails.php';
		$mail->Body = $contactar_publicador;
		if(!$mail->send()) 
		{
			echo "No enviado";
		} 
		else 
		{
		header("location: /publicaciones/$id_publicacion");
		}
	}
}

class AdminClass {
	/* Admin Login */
	public function AdminLogin($Correo,$password,$pin) {
		try {
			/*/ HASH password /*/
			function gen_token($pass, $salt) {
				$salt = strtolower($salt);
				$str = hash("sha512", $pass.$salt);
				$len = strlen($salt);
				return strtoupper(substr($str, $len, 17));
			}
			$db = getDB();
			$stmt = $db->prepare("SELECT * FROM usuarios INNER JOIN usuarios_administradores ON usuarios.id = usuarios_administradores.Admin_ID WHERE usuarios.Correo=:Correo AND usuarios.Password=:hash_password AND usuarios_administradores.Pin_Ingreso=:pin"); 
			$stmt->bindParam("Correo", $Correo,PDO::PARAM_STR) ;
			$hash_password = gen_token($password, $Correo);
			$stmt->bindParam("hash_password", $hash_password,PDO::PARAM_STR) ;
			$stmt->bindParam("pin", $pin,PDO::PARAM_STR);
			$stmt->execute();
			$count=$stmt->rowCount();
			$data=$stmt->fetch(PDO::FETCH_OBJ);
			$db = null;
			if($count) {
		      	$_SESSION['admin_id']=$data->id; // Storing user session value
		      	return true;
		      } else {
		      	return false;
		      }
		  } catch(PDOException $e) {
		  	echo '{"error":{"text":'. $e->getMessage() .'}}';
		  }
		}
	/* Admin Details */
	public function adminDetails($id) {
		try {
			$db = getDB();
			$stmt = $db->prepare("SELECT * FROM usuarios INNER JOIN usuarios_administradores ON usuarios.id = usuarios_administradores.Admin_ID WHERE usuarios.id=:id"); 
			$stmt->bindParam("id", $id,PDO::PARAM_INT);
			$stmt->execute();
		    $data = $stmt->fetch(PDO::FETCH_OBJ); //User data
		    $user_id=$data->id; // Storing user session value

		    return $data;
		} catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
	}
}

function compressImage($source, $destination, $quality) { 
    // Obtenemos la información de la imagen
    $imgInfo = getimagesize($source); 
    $mime = $imgInfo['mime']; 
     
    // Creamos una imagen
    switch($mime){ 
        case 'image/jpeg': 
            $image = imagecreatefromjpeg($source); 
            break; 
        case 'image/png': 
            $image = imagecreatefrompng($source); 
            break; 
        case 'image/gif': 
            $image = imagecreatefromgif($source); 
            break; 
        default: 
            $image = imagecreatefromjpeg($source); 
    } 
     
    // Guardamos la imagen
    imagejpeg($image, $destination, $quality); 
     
    // Devolvemos la imagen comprimida
    return $destination; 
} 

?>