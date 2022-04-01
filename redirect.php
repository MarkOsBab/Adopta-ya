<?php

require_once 'classes/class.consultas.php';

$btnLogin = "";
require_once 'classes/google_auth/vendor/autoload.php';
// init configuration
$clientID = '796401265661-1ac9pb8gso3lgqefp486eda8qt6jc94i.apps.googleusercontent.com';
$clientSecret = '68CP9EQg9-gw57LLqiTQWzZO';
$redirectUri = 'https://adoptaya.com/redirect.php';

// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

// authenticate code from Google OAuth Flow
if (isset($_GET['code'])) {
  $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

  if (!isset($token['error'])) {
  	$client->setAccessToken($token['access_token']);
  	// get profile info
	$google_oauth = new Google_Service_Oauth2($client);
	$google_account_info = $google_oauth->userinfo->get();
	$email =  $google_account_info->email;
	$id = $google_account_info->id;

	$busca_gID = $db->prepare("SELECT COUNT(google_id) FROM usuarios WHERE google_id='$id'");
  	$busca_gID->execute();
  	$buscaGoogleID = $busca_gID->fetchColumn();

  	$contaCorreo = $db->prepare("SELECT COUNT(*) FROM usuarios WHERE Correo='$email'");
  	$contaCorreo->execute();
  	$resultCorreo = $contaCorreo->fetchColumn();

  	$buscaCorreo = $db->prepare("SELECT * FROM usuarios WHERE Correo='$email'");
  	$buscaCorreo->execute();
  	$getUserinfo = $buscaCorreo->fetchAll();

  	foreach ($getUserinfo as $row) {
  		$cor_id = $row['id'];
  	}

  	if ($resultCorreo>0) {
  		$setGoogle_id = $db->prepare("UPDATE usuarios SET google_id='$id' WHERE id='$cor_id'");
  		$setGoogle_id->execute();
  		$_SESSION['id'] = $cor_id;
  		header("Location: ../");
  	}

  	$buscaUser = $db->prepare("SELECT * FROM usuarios WHERE google_id='$id'");
  	$buscaUser->execute();
  	$resultUser = $buscaUser->fetchAll();

  	foreach ($resultUser as $row) {
  		$u_id = $row['id'];
  	}

  	if ($buscaGoogleID>0) {
  		$_SESSION['id'] = $u_id;
  		header("Location: ../");
	  } else {
	  	include 'templates/registra_google.html';
	  }
  } else {
  	echo "Error";
  }
// now you can use this profile info to create account in your website and make user logged in.
} else {
  $btnLogin =  "<a class='btn default-btn btn-floating mx-1' href='".$client->createAuthUrl()."'><i class='fab fa-google'></i></a>";
}
?>