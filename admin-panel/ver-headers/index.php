<?php 
include '../classes/class.consultas.php';
include '../classes/class.admin.session.php';

if (!empty($_SESSION['admin_id'])) {
	$adminDetails=$adminClass->adminDetails($session_id);
	$_SESSION['id'] = '';
	$Admin_Name = $adminDetails->Nombre;
	$Admin_Apellido = $adminDetails->Apellido;
	$Admin_FullName = $Admin_Name.' '. $Admin_Apellido;
  $u_Token = $adminDetails->Token_ID;

  $busca_profile_image = $db->prepare("SELECT * FROM usuarios_profile_images WHERE Token_ID='$u_Token'");
  $busca_profile_image->execute();
  $result_profile_image = $busca_profile_image->fetchAll();
      foreach ($result_profile_image as $row) {
          $Profile_Imagen = $row['Profile_Image'];
      }
}


if (!empty($_SESSION['id'])) {
	$busca_user_admin = $db->prepare("SELECT COUNT(*) AS total FROM usuarios_administradores WHERE Admin_ID=".$_SESSION['id']." AND Rango_ID>0");
	$busca_user_admin->execute();
	$result_user_admin = $busca_user_admin->fetchColumn();
}


?>
<?php if (!empty($_SESSION['id']) && $result_user_admin < 1) { ?>
	<?php include '../templates/error404.html'; ?>
<?php } else { $db = getDB(); ?>
<!DOCTYPE html>
<html>
<head>
	<!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title><?php echo $Panel_Title; ?></title>
	<link rel="icon" type="image/png" href="../../templates/static/images/icon.png"/>
	 <!-- plugins:css -->
    <link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="../assets/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="../assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="../assets/vendors/owl-carousel-2/owl.carousel.min.css">
    <link rel="stylesheet" href="../assets/vendors/owl-carousel-2/owl.theme.default.min.css">
    <link rel="stylesheet" type="text/css" href="../static/css/bootstrap.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- End layout styles -->
</head>
<body>

	<div class="container-scroller">
      <!-- partial:partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
          <a class="sidebar-brand brand-logo" href="#"><img style="height: 100px !important;" src="../static/images/logo_white.png" alt="logo" /></a>
          <a class="sidebar-brand brand-logo-mini" href="#"><img src="../static/images/icon.png" alt="logo" /></a>
        </div>
        <ul class="nav">
          <li class="nav-item profile">
            <div class="profile-desc">
              <div class="profile-pic">
                <div class="count-indicator">
                  <img class="img-xs rounded-circle " src="../../templates/static/images/profile_images/<?php echo $Profile_Imagen; ?>" alt="">
                  <span class="count bg-success"></span>
                </div>
                <div class="profile-name">
                  <h5 class="mb-0 font-weight-normal"><?php echo $Admin_FullName; ?></h5>
                  <span><?php echo $Rango; ?></span>
                </div>
              </div>
              <a href="#" id="profile-dropdown" data-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>
              <div class="dropdown-menu dropdown-menu-right sidebar-dropdown preview-list" aria-labelledby="profile-dropdown">
                <a href="../configurar-cuenta" class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-dark rounded-circle">
                      <i class="mdi mdi-settings text-primary"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <p class="preview-subject ellipsis mb-1 text-small">Configuración de la cuenta</p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <a href="../job-info" class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-dark rounded-circle">
                      <i class="mdi mdi-onepassword  text-info"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <p class="preview-subject ellipsis mb-1 text-small">Información de mi trabajo</p>
                  </div>
                </a>
              </div>
            </div>
          </li>
          <li class="nav-item nav-category">
            <span class="nav-link">Navegación</span>
          </li>
          <li class="nav-item menu-items ">
            <a class="nav-link" href="../inicio">
              <span class="menu-icon">
                <i class="mdi mdi-speedometer"></i>
              </span>
              <span class="menu-title">Inicio</span>
            </a>
          </li>
          <?php if ($Rango_ID == 1) { ?>
          <li class="nav-item menu-items">
            <a class="nav-link" href="../usuarios">
              <span class="menu-icon">
                <i class="mdi mdi-account text-info"></i>
              </span>
              <span class="menu-title">Usuarios</span>
            </a>
          </li>

          <li class="nav-item menu-items active">
            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
              <span class="menu-icon">
                <i class="mdi mdi-bookmark-plus text-info"></i>
              </span>
              <span class="menu-title">Headers</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-basic">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="../publicar-header">Publicar header</a></li>
                <li class="nav-item"> <a class="nav-link" href="../ver-headers">Ver headers</a></li>
              </ul>
            </div>
          </li>
          <?php } ?>
          <li class="nav-item menu-items">
            <a class="nav-link" href="mensajes-responder">
              <span class="menu-icon">
                <i class="mdi mdi-message text-info"></i>
              </span>
              <span class="menu-title">Mensajes a responder</span>
            </a>
          </li>
        </ul>
      </nav>

      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar p-0 fixed-top d-flex flex-row">
          <div class="navbar-brand-wrapper d-flex d-lg-none align-items-center justify-content-center">
            <a class="navbar-brand brand-logo-mini" href="#"><img src="../static/images/icon.png" alt="logo" /></a>
          </div>
          <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
              <span class="mdi mdi-menu"></span>
            </button>
            <ul class="navbar-nav navbar-nav-right">
              <?php if ($result_mensajes != 0) { ?>
              <li class="nav-item dropdown border-left">
                <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                  <i class="mdi mdi-bell"></i>
                  <span class="count bg-info"></span>
                </a>
                
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="messageDropdown">
                  <h6 class="p-3 mb-0">Mensajes</h6>
                  <div class="dropdown-divider"></div>
                  <?php 
                  foreach ($result_notificacion as $row){
        						$Emisor_ID = $row['Emisor_ID'];
        				   
        				   $busca_imagen_emisor = $db->prepare("SELECT * FROM usuarios_administradores WHERE Admin_ID='$Emisor_ID'");
        				   $busca_imagen_emisor->execute();
        				   $result_imagen_emisor = $busca_imagen_emisor->fetchAll();
        				   foreach ($result_imagen_emisor as $rows) {
        				   	$Imagen_Emisor = $rows['Profile_Imagen'];
        				   }
				  	
                  ?>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <img src="../../templates/static/images/profile_images/<?php echo $rows['Profile_Imagen']; ?>" alt="image" class="rounded-circle profile-pic">
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject ellipsis mb-1"><?php echo $row['Mensaje']; ?></p>
                      <p class="text-muted mb-0"> <?php echo time_passed($row['Fecha']); ?> </p>

                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <?php } ?>
                  <div class="dropdown-divider"></div>
                  <p class="p-3 mb-0 text-center"><?php echo $result_mensajes.' '. $dato; ?></p>
                </div>
              </li>
              <?php } ?>
              <li class="nav-item dropdown">
                <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
                  <div class="navbar-profile">
                    <img class="img-xs rounded-circle" src="../../templates/static/images/profile_images/<?php echo $Profile_Imagen; ?>" alt="">
                    <p class="mb-0 d-none d-sm-block navbar-profile-name"><?php echo $Admin_FullName; ?></p>
                    <i class="mdi mdi-menu-down d-none d-sm-block"></i>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="profileDropdown">
                  <h6 class="p-3 mb-0">Perfil</h6>
                  <div class="dropdown-divider"></div>
                  <a href="../configurar-cuenta" class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-settings text-success"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1">Ajustes</p>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <a href="../logout" class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-logout text-danger"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1">Cerrar sesion</p>
                    </div>
                  </a>
              </li>
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
              <span class="mdi mdi-format-line-spacing"></span>
            </button>
          </div>
        </nav>
        <!-- partial -->

        <div class="main-panel">
          <div class="content-wrapper">
            <div class="row">
              <div class="col-sm-4 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <h5>Usuarios</h5>
                    <div class="row">
                      <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                          <h2 class="mb-0"><?php echo $result_usuarios_registrados; ?></h2>
                        </div>
                        <h6 class="text-muted font-weight-normal">Usuarios registrados</h6>
                      </div>
                      <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-account-multiple text-info ml-auto"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-4 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <h5>Adopciones</h5>
                    <div class="row">
                      <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                          <h2 class="mb-0"><?php echo $result_adopciones; ?></h2>
                        </div>
                        <h6 class="text-muted font-weight-normal">Adopciones publicadas</h6>
                      </div>
                      <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi mdi-gate text-success ml-auto"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-4 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <h5>Perdidos</h5>
                    <div class="row">
                      <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                          <h2 class="mb-0"><?php echo $result_perdidos; ?></h2>
                        </div>
                        <h6 class="text-muted font-weight-normal">Perdidos publicados</h6>
                      </div>
                      <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-google-nearby text-danger ml-auto"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row ">
              <div class="col-12 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Headers</h4>
                    <div class="table-responsive">
                      <div id="contenido">
                        <?php
                        $db = getDB();
                         require 'paginacion_headers.php';
                        ?>
                       </div>
                        <nav class="mt-3">
                          <ul class="pagination justify-content-center">
                            <?php 
                            if ($total > 0) {
                              for($n = 1; $n <= $paginas; $n++) {
                                echo '<li class="page-item "><a onclick="Pagina('.$n.')" class="btn btn-rounded btn-outline-info">'.$n.'</a></li>';
                              }
                            }
                            ?>
                          </ul>
                        </nav>
                  </div>
                </div>
              </div>
            </div>
            </div>
            <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © Altri0n</span>
              <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center"> <a href="https://www.instagram.com/altri0n/" target="_blank">Altrion web design</a></span>
            </div>
          </footer>
          </div>
  </div>

	<!-- plugins:js -->
    <script src="../assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="../assets/vendors/chart.js/Chart.min.js"></script>
    <script src="../assets/vendors/progressbar.js/progressbar.min.js"></script>
    <script src="../assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
    <script src="../assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="../assets/vendors/owl-carousel-2/owl.carousel.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="../assets/js/off-canvas.js"></script>
    <script src="../assets/js/hoverable-collapse.js"></script>
    <script src="../assets/js/misc.js"></script>
    <script src="../assets/js/settings.js"></script>
    <script src="../assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="../assets/js/dashboard.js"></script>
    <!-- End custom js for this page -->
    <script src="../static/js/alertify.js"></script>

   <script type="text/javascript">
  function objetoAjax(){
	 var xmlhttp=false;
	  try{
	   xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	  }catch(e){
	   try {
	    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	   }catch(E){
	    xmlhttp = false;
	   }
	  }
	  if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
	   xmlhttp = new XMLHttpRequest();
	  }
	  return xmlhttp;
	}

	function Pagina(nropagina){
	 //donde se mostrará los registros
	 divContenido = document.getElementById('contenido');
	 
	 ajax=objetoAjax();
	 //uso del medoto GET
	 //indicamos el archivo que realizará el proceso de paginar
	 //junto con un valor que representa el nro de pagina
	 ajax.open("GET", "paginacion.php?page="+nropagina);
	 divContenido.innerHTML= '<img src="../static/css/ajax-loader.gif">';
	 ajax.onreadystatechange=function() {
	  if (ajax.readyState==4) {
	   //mostrar resultados en esta capa
	   divContenido.innerHTML = ajax.responseText
	  }
	 }
	 //como hacemos uso del metodo GET
	 //colocamos null ya que enviamos 
	 //el valor por la url ?pag=nropagina
	 ajax.send(null)
	}
</script>

   <script type="text/javascript">
  function objetoAjax(){
	 var xmlhttp=false;
	  try{
	   xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	  }catch(e){
	   try {
	    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	   }catch(E){
	    xmlhttp = false;
	   }
	  }
	  if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
	   xmlhttp = new XMLHttpRequest();
	  }
	  return xmlhttp;
	}

	function Pagina_s(nropagina){
	 //donde se mostrará los registros
	 donationsContenido = document.getElementById('donations_content');
	 
	 ajax=objetoAjax();
	 //uso del medoto GET
	 //indicamos el archivo que realizará el proceso de paginar
	 //junto con un valor que representa el nro de pagina
	 ajax.open("GET", "paginacion_donaciones.php?donations_page="+nropagina);
	 donationsContenido.innerHTML= '<img src="../static/css/ajax-loader.gif">';
	 ajax.onreadystatechange=function() {
	  if (ajax.readyState==4) {
	   //mostrar resultados en esta capa
	   donationsContenido.innerHTML = ajax.responseText
	  }
	 }
	 //como hacemos uso del metodo GET
	 //colocamos null ya que enviamos 
	 //el valor por la url ?pag=nropagina
	 ajax.send(null)
	}
</script>
</body>
</html>
<?php } ?>