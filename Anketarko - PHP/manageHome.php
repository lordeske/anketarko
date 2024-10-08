<?php
///Mihajlo Eskic
session_start();

// Provera da li je korisnik prijavljen
if(!isset($_SESSION['userID']) && !isset($_SESSION['username']) && !isset($_SESSION['userType'])){
    header('Location: signin.php');
    exit();
}

// Provera da li je korisnik admin
if($_SESSION['userType'] == 0){
    header('Location: index.php');
    exit();
}

require_once 'dbConnection.php';
require_once 'classes/FAQ.php';
require_once 'classes/Explanation.php';

// Povezivanje sa bazom
$dbcon = Database::connectDB();

// Lista objašnjenja
$f = new FAQ();
$faqs = $f->listFAQs($dbcon);

// Lista često postavljenih pitanja
$e = new Explanation();
$exps = $e->listExps($dbcon);
?>
<html lang="en">
<head>
    <title>Anketarko</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/manageHome.css">
    <link rel="stylesheet" href="css/footerStyle.css">
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.4.1/darkly/bootstrap.min.css" rel="stylesheet" integrity="sha384-rCA2D+D9QXuP2TomtQwd+uP50EHjpafN+wruul0sXZzX/Da7Txn4tB9aLMZV4DZm" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<?php include_once 'header.php' ?>
<body>
    <main id="main">
        <h1 class="h1 text-center pad-top">Uredi Početnu stranu</h1>
        <div class="m-5 content">
            <h2 class="title">Objašnjenja</h2>
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th scope="col">Pitanje</th>
                    <th scope="col">Odgovor</th>
                </tr>
                </thead>
                <tbody>
                <!-- Petlja kroz objašnjenja i prikazivanje -->
                <?php foreach($exps as $exp) { ?>
                    <tr id="exp-row-<?= $exp['id'] ?>">
                        <td><?= $exp['section_name'] ?></td>
                        <td><?= $exp['body'] ?></td>
                        <td>
                            <form action="updateExplanation.php" method="post">
                                <input type="hidden" name="id" value="<?= $exp['id'] ?>">
                                <input type="submit" class="button btn btn-primary" name="updateExp" value="Azuriraj" />
                            </form>
                        </td>
                        <td>
                            <button class="button btn btn-danger delete-exp" data-id="<?= $exp['id'] ?>">Obriši</button>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <div class="text-center">
                <a href="addExplanation.php" id="addExp" class="btn btn-success btn-lg">Dodaj Objašnjenje</a>
            </div>
        </div>
        <div class="m-5 content">
            <h2 class="title">Često postavljena pitanja</h2>
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th scope="col">Pitanje</th>
                    <th scope="col">Odgovor</th>
                </tr>
                </thead>
                <tbody>
                <!-- Petlja kroz često postavljena pitanja i prikazivanje -->
                <?php foreach($faqs as $faq) { ?>
                    <tr id="faq-row-<?= $faq['id'] ?>">
                        <td><?= $faq['question'] ?></td>
                        <td><?= $faq['answer'] ?></td>
                        <td>
                            <form action="updateFAQ.php" method="post">
                                <input type="hidden" name="id" value="<?= $faq['id'] ?>">
                                <input type="submit" class="button btn btn-primary" name="updateFAQ" value="Azuriraj" />
                            </form>
                        </td>
                        <td>
                            <button class="button btn btn-danger delete-faq" data-id="<?= $faq['id'] ?>">Obrisi</button>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <div class="text-center">
                <a href="addFAQ.php" id="addFAQ" class="btn btn-success btn-lg">Dodaj pitanje</a>
            </div>
        </div>
    </main>
<?php include_once 'footer.php' ?>
<script>
$(document).ready(function() {
    // Asinhrono brisanje objašnjenja
    $('.delete-exp').on('click', function(event) {
        event.preventDefault(); // Sprečava osvežavanje stranice
        var id = $(this).data('id');
        if (confirm('Da li ste sigurni da želite da obrišete ovaj zapis?')) {
            $.ajax({
                url: 'deleteExplanation.php',
                method: 'POST',
                data: { id: id },
                success: function(response) {
                    if(response === 'success') {
                        $('#exp-row-' + id).remove();
                    } else {
                        alert('Greška prilikom brisanja zapisa.');
                    }
                }
            });
        }
    });

    // Asinhrono brisanje FAQ
    $('.delete-faq').on('click', function(event) {
        event.preventDefault(); // Sprečava osvežavanje stranice
        var id = $(this).data('id');
        if (confirm('Da li ste sigurni da želite da obrišete ovaj zapis?')) {
            $.ajax({
                url: 'deleteFAQ.php',
                method: 'POST',
                data: { id: id },
                success: function(response) {
                    if(response === 'success') {
                        $('#faq-row-' + id).remove();
                    } else {
                        alert('Greška prilikom brisanja zapisa.');
                    }
                }
            });
        }
    });
});
</script>
</body>
</html>
