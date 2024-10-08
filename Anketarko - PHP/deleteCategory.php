<?php
require_once 'dbConnection.php';
require_once 'classes/category.php';
require_once 'Survey.php';

if (isset($_POST['id'])) {
    $categoryId = $_POST['id'];

    $dbcon = Database::getDb();

    // Provera da li postoji anketa sa ovom kategorijom
    $s = new Survey();
    $surveysWithCategory = $s->getSurveysByCategory($dbcon, $categoryId);

    if (count($surveysWithCategory) > 0) {
        // Ako postoje ankete sa ovom kategorijom, ne dozvoli brisanje
        echo "<script>alert('Ne možete obrisati kategoriju koja se koristi u anketi.'); window.location.href='listCategories.php';</script>";
    } else {
        // Ako ne postoje ankete, dozvoli brisanje
        $c = new Category();
        $deleted = $c->deleteCategory($categoryId, $dbcon); // Pozivanje metode u ispravnom redosledu
        
        if ($deleted) {
            echo "<script>alert('Kategorija je uspešno obrisana.'); window.location.href='listCategories.php';</script>";
        } else {
            echo "<script>alert('Došlo je do greške prilikom brisanja kategorije.'); window.location.href='listCategories.php';</script>";
        }
    }
}
?>
