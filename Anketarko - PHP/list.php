<?php
session_start();
date_default_timezone_set("Europe/Belgrade");
require_once 'dbConnection.php';
require_once 'Survey.php';
include_once 'header.php';

// If user is not logged in, they will be redirected to the login page
if (!isset($_SESSION['userType']) && !isset($_SESSION['userID'])) {
    header('location: login.php');
}

$dbcon = Database::getDb();
$s = new Survey();

// Pretraga po nazivu ankete
$searchTerm = "";
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $surveys = $s->searchVisibleSurveysByTitle($dbcon, $searchTerm);
} else {
    $surveys = $s->listAllVisibleSurveys($dbcon);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.4.1/darkly/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Liste svih Anekta na stanju</title>
</head>
<body>

<main class="m-5">
    <!-- Forma za pretragu po nazivu ankete -->
    <form id="surveySearchForm" action="" method="get" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Pretraži po nazivu ankete" value="<?= htmlspecialchars($searchTerm) ?>">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">Pretraži</button>
            </div>
        </div>
    </form>

    <div>  
        <table class="table table-bordered table-hover tbl">
            <thead class="text-center">
                <tr>
                    <th scope="col">Naziv</th>
                    <th scope="col">Opis</th>
                    <th scope="col">Uradi anketu</th>
                </tr>
            </thead>
            <tbody id="surveyTableBody">
                <?php foreach ($surveys as $survey) { ?>
                    <tr>
                        <td><?= $survey->title ?></td>
                        <td><?= $survey->description ?></td>
                        <td>
                            <form action="takeSurvey.php" method="post">
                                <input type="hidden" name="id" value="<?= $survey->id ?>"/>
                                <input type="submit" class="button btn btn-primary" name="takeSurvey" value="Odradi anketu"/>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div><a class="btn btn-primary btn-lg" href="createSurvey.php" role="button">Kreiraj novu anketu</a></div>
</main>   

<script>
$(document).ready(function() {
    $('#surveySearchForm').on('submit', function(event) {
        event.preventDefault(); // Sprečava osvežavanje stranice
        var searchTerm = $('input[name="search"]').val();

        $.ajax({
            url: '', // koristi trenutnu stranicu za rukovanje AJAX zahtevom
            method: 'GET',
            data: { search: searchTerm },
            success: function(response) {
                // Ažuriraj telo tabele sa rezultatima anketa
                $('#surveyTableBody').html($(response).find('#surveyTableBody').html());
            }
        });
    });
});
</script>

</body>
</html>
