<?php 
require_once 'classes/class.consultas.php';

if(!empty($_SESSION['id'])) {
	if (!empty($_GET['token']) && !empty($_GET['ammount'])) {
		$Token_ID = $_GET['token'];
		$Ammount = $_GET['ammount'];
		$diassemana = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
					 
		$date = $diassemana[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y');
		$inserta_donacion = $db->prepare("INSERT INTO donaciones (User_ID, Money, Transaction_ID, Transaction_From, Fecha) VALUES (".$_SESSION['id'].", '$Ammount', '$Token_ID', 'Mercado Pago', '$date')");
		$inserta_donacion->execute();

		if ($inserta_donacion) {
			header("Location: /success");
		}
	}
} else {
	header("Location: /success");
}
?>