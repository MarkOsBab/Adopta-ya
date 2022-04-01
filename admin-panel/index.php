<?php 
include 'classes/class.consultas.php';
include 'classes/class.admin.session.php';
if (!empty($_SESSION['admin_id'])) {
	$adminDetails=$adminClass->adminDetails($session_id);
	$U_Name = $adminDetails->Nombre;
	$U_Apellido = $adminDetails->Apellido;
	$u_Correo = $adminDetails->Correo;
	$U_Password = $adminDetails->Password;
	$U_Departamento = $adminDetails->Departamento;
	$u_ID = $adminDetails->id;
	$u_Celular = $adminDetails->Celular;
}

if (!empty($_SESSION['id'])) {
	$busca_user_admin = $db->prepare("SELECT COUNT(*) AS total FROM usuarios_administradores WHERE Admin_ID=".$_SESSION['id']." AND Rango_ID>0");
	$busca_user_admin->execute();
	$result_user_admin = $busca_user_admin->fetchColumn();
}

?>
<?php if (!empty($_SESSION['id']) && $result_user_admin < 1) { ?>
	<?php include '../templates/error404.html'; ?>
<?php } else { ?>
<!DOCTYPE html>
<html>
<head>
	<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback" async defer></script>
    <script>
    var onloadCallback = function() {
        grecaptcha.execute();
    };

    function setResponse(response) { 
        document.getElementById('captcha-response').value = response; 
    }
    </script>
	<title><?php echo $Panel_Title; ?></title>
	<meta charset="utf-8">
    <link rel="icon" type="image/png" href="../templates/static/images/icon.png"/>
    <link rel="stylesheet" href="../templates/static/css/bootstrap.css">
    <link rel="stylesheet" href="../templates/static/css/fontawesome.css">
    <link rel="stylesheet" href="../templates/static/css/w3.css">
    <link rel="stylesheet" href="static/css/admin-main.css">
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap-pincode-input.css">
    <link rel="preconnect" href="https://fonts.g../templates/static.com">
	<link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:ital,wght@1,200&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Pathway+Gothic+One|Roboto+Slab&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../templates/static/css/slick.css">
    <link rel="stylesheet" type="text/css" href="../templates/static/css/slick-theme.css">
    <link rel="stylesheet" type="text/css" href="../templates/static/css/opentip.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <meta name="description" content="Somos una página web con el objetivo de que las personas que quieran tener en sus hogares a una nueva mascota puedan hacerlo.">
    <meta name="viewport" content="initial-scale=1.0, width=device-width" />
</head>
<body class="admin-bg">

<section class="row m-0 justify-content-center align-items-center vh-100">
	<div class="col-sm-6 login-bg rounded text-center">
		<img src="static/images/logo_test.png" class="ay_logo img-fluid text-center" width="150">
		<div class="block-bg rounded w3-padding-16">
			<h1 class="text-center w3-text-white style-text"><i class="fas fa-user-cog"></i> Ingresar como administrador</h1>
			<form method="post">
				<div class="container-fluid justify-content-center align-items-center">
					<div class="row form-group  m-5">
						<i class="fal fa-user-tie col-sm-1 fa-2x text-white pt-1"></i>
						<div class="col-sm-11">
							<input type="text" name="admin_email" class="form-control default-font" placeholder="Correo electrónico">
						</div>
					</div>
					<div class="row form-group m-5">
						<i class="far fa-key col-sm-1 fa-2x text-white pt-1"></i>
						<div class="col-sm-11">
							<input type="password" name="admin_password" class="form-control default-font" placeholder="Correo electrónico">
						</div>
					</div>
					<div class="row form-group m-5">
						<i class="fal fa-keyboard col-sm-1 fa-2x w3-text-white pt-1"></i>
						<div class="col-sm-11">

							<input name="admin_pin" type="text" id="pincode-input4" >
						</div>
					</div>
					<?php echo $errorAdminLogin; ?>
					<div class="row form-group">
						<div class="col-sm-12 text-right">
							<input type="submit" name="admin_login" class="btn rounded text-white rounded-pill style-text btn-login btn-lg" value="Ingresar">
						</div>
					</div>
					<input type="hidden" id="captcha-response" name="captcha-response" />
					<div class="g-recaptcha" data-sitekey="6Lcqz1waAAAAAJ72GsX_f5Bgc8czt6Yhpbblprmr"  data-size="invisible" data-callback="setResponse"></div>
				</div>
			</form>
		</div>
	</div>
</section>
	


    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="../templates/static/js/bootstrap.js"></script>
    <script src="../templates/static/js/00bf663aea.js"></script>
    <script src="../templates/static/js/slick.js"></script>
    <script src="../templates/static/js/opentip.jquery.js"></script>
    <script src="../templates/static/js/alertify.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="static/js/bootstrap-pincode-input.js"></script>
    <script>
  	AOS.init();
	</script>
	<script type="text/javascript">
		$('#pincode-input4').pincodeInput({hidedigits: false, inputs: 4});
	</script>
</body>

</html>
</body>
</html>

<?php } ?>