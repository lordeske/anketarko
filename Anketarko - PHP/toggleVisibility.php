<?php
require_once 'dbConnection.php';

if (isset($_POST['id']) && isset($_POST['visible'])) {
    $id = $_POST['id'];
    $visible = $_POST['visible'];
    
    $dbcon = Database::getDb();
    $query = "UPDATE surveys SET visible = :visible WHERE id = :id";
    $stmt = $dbcon->prepare($query);
    $stmt->bindParam(':visible', $visible, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
