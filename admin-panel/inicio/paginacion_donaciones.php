<?php 
$_BS['bypage_donations'] = 3;
require_once '../classes/class.config.php';
$db = getDB();

$conta_total = $db->prepare("SELECT COUNT(*) AS total FROM donaciones");
$conta_total->execute();
$total = $conta_total->fetchColumn();

$paginas =  (($total % $_BS['bypage_donations']) > 0) ? (int)($total / $_BS['bypage_donations']) + 1 : ($total / $_BS['bypage_donations']);

if (isset($_GET['donations_page'])) {
  $pagina = (int)$_GET['donations_page'];
} else {
  $pagina = 1;
}

$pagina = max(min($paginas, $pagina), 1);
$inicio = ($pagina - 1) * $_BS['bypage_donations'];
$sql = $db->prepare("SELECT * FROM `donaciones` ORDER BY `id` ASC LIMIT ".$inicio.", ".$_BS['bypage_donations']);
$sql->execute();
$resultado = $sql->fetchAll();

foreach ($resultado as $row) {


 ?>
 <div class="bg-gray-dark d-flex d-md-block d-xl-flex flex-row py-3 px-4 px-md-3 px-xl-4 rounded mt-3">
 <div class="text-md-center text-xl-left">
  <h6 class="mb-1">Donación desde <?php echo $row['Transaction_From']; ?></h6>
  <p class="text-muted mb-0"><?php echo $row['Fecha']; ?></p>
</div>
<div class="align-self-center flex-grow text-right text-md-center text-xl-right py-md-2 py-xl-0">
  <h6 class="font-weight-bold mb-0">$<?php echo $row['Money']; ?></h6>
</div>
</div>
<?php } ?>