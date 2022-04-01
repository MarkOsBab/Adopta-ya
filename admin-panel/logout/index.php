<?php
include('../../classes/class.config.php');
$session_uid='';
$_SESSION['admin_id']=''; 
if(empty($session_uid) && empty($_SESSION['admin_id'])){
	header("Location: ../");
}
?>