<?php
///Mihajlo Eskic

session_start();


// Proveri da li je korisnik prijavljen - ako nije, preusmeri na stranicu za prijavu
if(!isset($_SESSION['userID']) && !isset($_SESSION['username']) && !isset($_SESSION['userType'])){
    header('Location: signin.php');
    exit();
}

// Proveri da li je POST zahtev sa id-em poslat
if(isset($_POST['id'])){
    // Preuzmi ID korisnika
    $id = $_POST['id'];

    require_once 'dbConnection.php';
    require_once 'User.php';

    // Povezivanje sa bazom
    $dbcon = Database::getDb();

    // Brisanje korisnika iz baze
    $u = new User();
    $count = $u->deleteUser($dbcon, $id);

    // Ako je brisanje uspešno, vrati 'success', inače vrati 'error'
    if($count){
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
