<?php 
include '../classes/class.consultas.php';
require_once("../classes/class.session.php");
include '../templates/header.html';
include '../templates/modals.html';
include '../templates/pass_changed.html';
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