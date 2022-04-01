<?php 
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
			$stmt = $db->prepare("SELECT * FROM usuarios WHERE id=:id"); 
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
function generar_token_seguro($longitud) {
	if ($longitud < 4) {
		$longitud = 4;
	}
	return bin2hex(openssl_random_pseudo_bytes(($longitud - ($longitud % 2)) / 2));
}


?>