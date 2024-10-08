<?php
session_start();

///Mihajlo Eskic

// Proveri da li je korisnik prijavljen - ako nije, preusmeri na stranicu za prijavu
if(!isset($_SESSION['userID']) && !isset($_SESSION['username']) && !isset($_SESSION['userType'])){
    header('Location: signin.php');
    exit();
}

// Proveri da li je POST zahtev sa id-em poslat
if(isset($_POST['id'])){
    // Preuzmi ID
    $id = $_POST['id'];

    require_once 'dbConnection.php';
    require_once 'Survey.php';
    require_once 'classes/Question.php';

    // Povezivanje sa bazom
    $dbcon = Database::getDb();

    // Brisanje ankete iz baze
    $s = new Survey();
    $count = $s->deleteSurvey($dbcon, $id);

    // Brisanje svih povezanih pitanja iz baze
    $q = new Question();
    $count2 = $q->deleteAllQuestions($dbcon, $id);

    // Ako su oba brisanja uspešna, vrati 'success', inače vrati 'error'
    if($count && $count2){
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
