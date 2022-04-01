<?php 
require 'class.consultas.php';
$db = getDB();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$Localidad=$_POST['Departamento'];

	$sql=$db->prepare("SELECT * FROM Barrios WHERE Depto='$Localidad'");
	$sql->execute();
	$rows = $sql->fetchAll();
	$cadena="<select id='Localidades' name='barrioUsuario' class='w3-input'>";
	foreach ($rows as $ver) {
		if (!empty($ver['Barrio'])) {
			$cadena=$cadena.'<option value="'.$ver['Barrio'].'">'.$ver['Barrio'].'</option>';
		}
	}
	echo  $cadena."</select>";

?>