<?php
require_once 'class.config.php';
$db = getDB();
 
// Attempt search query execution
try{
    if(isset($_REQUEST["term"])){
        // create prepared statement
        $sql = "SELECT * FROM barrios WHERE Barrio LIKE :term";
        $stmt = $db->prepare($sql);
        $term = $_REQUEST["term"] . '%';
        // bind parameters to statement
        $stmt->bindParam(":term", $term);
        // execute the prepared statement
        $stmt->execute();
        if($stmt->rowCount() > 0){
            while($row = $stmt->fetch()){
                echo "<p>" . "<span>".$row['Depto']."</span>"." - ".$row["Barrio"] . "</p>";
            }
        } else{
            echo "<p>No se encuentra el barrio</p>";
        }
    }  
} catch(PDOException $e){
    die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}
 
// Close statement
unset($stmt);
 
// Close connection
unset($db);
?>