<?php 
include '../classes/class.consultas.php';
require_once("../classes/class.session.php");
if (empty($_SESSION['id'])) {
  header("Location: /login");
} else {
include '../templates/header.html';
include '../templates/modals.html';
include '../templates/publicar-perdido.html';
include '../templates/footer.html';
}
?>