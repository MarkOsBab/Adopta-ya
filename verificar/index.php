<?php 
include '../classes/class.consultas.php';
include '../classes/class.session.php';
if (empty($_SESSION['id'])) {
  header("Location: /login");
} else {
/*/ VARIABLES SESION /*/
if(!empty($_SESSION['id'])) {
	$userDetails=$userClass->userDetails($session_id);
	$U_Name = $userDetails->Nombre;
	$u_ID = $userDetails->id;
	$u_Token = $userDetails->origin_key;
}
include '../templates/header.html';
include '../templates/modals.html';
include '../templates/verificar.html';
include '../templates/footer.html';
?>
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