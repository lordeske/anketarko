<?php
///Mihajlo Eskic
session_start();




// Proveri da li je korisnik prijavljen
if(!isset($_SESSION['userID']) && !isset($_SESSION['username']) && !isset($_SESSION['userType'])){
    header('Location: signin.php');
    exit();
}

// Proveri da li je korisnik admin
if($_SESSION['userType'] == 0){
    header('Location: index.php');
    exit();
}

// Proveri da li je POST zahtev sa id-em poslat
if(isset($_POST['id'])){
    // Preuzmi ID
    $id = $_POST['id'];

    // Povezivanje sa bazom
    require_once 'dbConnection.php';
    require_once 'classes/Explanation.php';

    $dbcon = Database::getDb();

    // Brisanje iz baze
    $e = new Explanation();
    $count = $e->deleteExp($dbcon, $id);

    // Ako je uspešno obrisano, vrati success, u suprotnom vrati error
    if($count){
        echo 'success';
    } else {
        echo 'error';
    }
}
