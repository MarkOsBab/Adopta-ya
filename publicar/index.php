<?php 
include '../classes/class.consultas.php';
require_once("../classes/class.session.php");
if (empty($_SESSION['id'])) {
  header("Location: /login");
} else {
include '../templates/header.html';
include '../templates/modals.html';
include '../templates/publicar.html';
include '../templates/footer.html';
?>
<script type="text/javascript">
 $('.slider-for').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  infinite: true,
  speed: 500,
  autoplay: true,
  autoplaySpeed: 3000,
  centerMode: true,
  adaptiveHeight: false,
  arrows: false,
  fade: true,
  cssEase: 'linear',
  dots: true,
  asNavFor: '.slider-nav'
});
$('.slider-nav').slick({
  slidesToShow: 3,
  slidesToScroll: 1,
  asNavFor: '.slider-for',
  focusOnSelect: true
});

</script>
<?php } ?>