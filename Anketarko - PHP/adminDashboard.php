<?php
session_start();
date_default_timezone_set("Europe/Belgrade");
require_once 'dbConnection.php';
require_once 'User.php';
require_once 'Survey.php';

// If user is not logged in, will be re-directed to login page
if(!isset($_SESSION['userType']) && !isset($_SESSION['userID'])){
  header('location: login.php');
  exit();
}

// Database connection
$dbcon = Database::getDb();

// Users
$u = new User();

// Search users
$userSearchTerm = "";
if (isset($_GET['userSearch'])) {
    $userSearchTerm = $_GET['userSearch'];
    $allusers = $u->searchUsersByName($dbcon, $userSearchTerm);
} else {
    $allusers = $u->listUsers($dbcon);
}

// Count users
$userscount = $u->countUsers($dbcon);
$countU = $userscount[0]->total;

$users30days = $u->countUsers30($dbcon);
$users30dayscount = $users30days[0]->total30days;

// Surveys
$s = new Survey();

// Search surveys
$surveySearchTerm = "";
if (isset($_GET['surveySearch'])) {
    $surveySearchTerm = $_GET['surveySearch'];
    $allsurveys = $s->searchSurveysByTitle($dbcon, $surveySearchTerm);
} else {
    $allsurveys = $s->listAllSurveys($dbcon);
}

// Count surveys
$surveyscount = $s->countSurveys($dbcon);
$countS = $surveyscount[0]->total;

$surveys30days = $s->countSurveys30($dbcon);
$surveys30dayscount = $surveys30days[0]->total30days;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.4.1/darkly/bootstrap.min.css">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/adminDashboard.css">
  <title>Admin Stranica</title>
  <style>
    /* Smanjuje širinu input polja i dugmeta i poravnava ih levo */
    .input-group {
        max-width: 300px;
        text-align: left;
    }

    /* Smanjuje veličinu dugmeta */
    .input-group-append .btn {
        padding: 5px 10px;
        font-size: 0.875rem;
    }

    /* Smanjuje veličinu input polja */
    .form-control {
        padding: 5px 10px;
        font-size: 0.875rem;
    }
  </style>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
<div>
    <header class="header text-center">
        <a style="float: right" href="listCategories.php" class="btn">Upravljaj Kategorijama</a>
        <a style="float: right" class="btn" href="manageHome.php" target="">Upravljaj glavnom stranom</a>
        <a style="float: right" class="btn" href="index.php" target="">Početna</a>
    </header>
</div>

<main class="main">
    <h1>Admin stranica</h1>

    <aside class="right">
      <h2>Status Anketa na stanju</h2>
      <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between align-items-center">
          Nove ankete (zadnjih 30 dana) 
          <span class="badge badge-primary badge-pill"><?= $surveys30dayscount ?></span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          Ukupno anketa 
          <span class="badge badge-primary badge-pill"><?= $countS ?></span>
        </li>
      </ul>
    </aside>

    <aside class="left">
      <h2>Korisnici Anketarka</h2>
      <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between align-items-center">
          Novi korisnici (Zadnjih 30 dana)
          <span class="badge badge-primary badge-pill"><?= $users30dayscount ?></span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          Ukupno korisnika Anketarka
          <span class="badge badge-primary badge-pill"><?= $countU ?></span>
        </li>
      </ul>
    </aside>

    <!-- Surveys Table Section -->
    <div class="survey">
      <h3>Anekte</h3>
      <form id="surveySearchForm" action="" method="get" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="surveySearch" placeholder="Pretraži po nazivu ankete" value="<?= htmlspecialchars($surveySearchTerm) ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Pretraži</button>
                </div>
            </div>
        </form>
  
      <hr />
  
      <table class="table table-hover">
        <thead>
          <tr>
            <th scope="col">Naziv Ankete</th>
            <th scope="col">Opis Ankete</th>
            <th scope="col">Datum</th>
            <th colspan="1" scope="col" style="text-align:center;">Akcije</th>
          </tr>
        </thead>
        <tbody id="surveyTableBody">
          <?php 
            for ($i = 0; $i < count($allsurveys); $i++) {
                echo '<tr class="table-primary" id="survey-row-' . $allsurveys[$i]->id . '">';
                echo '<td scope="row">' . $allsurveys[$i]->title . '</td>';
                echo '<td scope="row">' . $allsurveys[$i]->description . '</td>';
                echo '<td scope="row">' . $allsurveys[$i]->created_date . '</td>';
                
                // Proverava vidljivost ankete i prikazuje odgovarajuće dugme
                if ($allsurveys[$i]->visible) {
                    echo '<td scope="row"><button class="button btn btn-warning toggle-visibility" data-id="' . $allsurveys[$i]->id . '" data-visible="1">Onemogući</button></td>';
                } else {
                    echo '<td scope="row"><button class="button btn btn-success toggle-visibility" data-id="' . $allsurveys[$i]->id . '" data-visible="0">Omogući</button></td>';
                }
                
                echo '</tr>';
            }
          ?>
        </tbody>
      </table>
    </div >

    <!-- Users Table Section -->
    <div class="users">
      <h3>Korisnička sekcija</h3>
      <form id="userSearchForm" action="" method="get" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="userSearch" placeholder="Pretraži po imenu korisnika" value="<?= htmlspecialchars($userSearchTerm) ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Pretraži</button>
                </div>
            </div>
        </form>
      <hr />
      <table class="table table-hover">
          <thead>
            <tr>            
              <th scope="col">Ime Korisnika</th>
              <th scope="col">Prezime Korisnika</th>
              <th scope="col">Datum registracije</th>
                
            </tr>
        </thead>
        <tbody id="userTableBody">
        <?php 
          for ($i=0; $i<count($allusers); $i++ ){
          echo '<tr class="table-primary" id="user-row-'.$allusers[$i]->id.'">';
          echo '<td scope="row">'.$allusers[$i]->fname.'</td>';
          echo '<td scope="row">'.$allusers[$i]->lname.'</td>';
          echo '<td scope="row">'.$allusers[$i]->reg_date.'</td>';
          }
        ?>
        </tbody>
      </table>
  
     </div>

  </main>

  <script>
    $(document).ready(function() {
        // Asinhrona pretraga anketa
        $('#surveySearchForm').on('submit', function(event) {
            event.preventDefault(); // Sprečava osvežavanje stranice
            var searchTerm = $('input[name="surveySearch"]').val();

            $.ajax({
                url: 'adminDashboard.php',
                method: 'GET',
                data: { surveySearch: searchTerm },
                success: function(response) {
                    // Ažuriranje tabele sa rezultatima anketa
                    $('#surveyTableBody').html($(response).find('#surveyTableBody').html());
                }
            });
        });

        // Asinhrono omogućavanje/onemogućavanje vidljivosti anketa
        $('.toggle-visibility').on('click', function(event) {
            event.preventDefault();
            var id = $(this).data('id');
            var visible = $(this).data('visible');
            var newVisibility = visible == 1 ? 0 : 1; // Promeni vrednost

            $.ajax({
                url: 'toggleVisibility.php',
                method: 'POST',
                data: { id: id, visible: newVisibility },
                success: function(response) {
                    if(response === 'success') {
                        // Ažuriraj tekst dugmeta i klasu na osnovu nove vrednosti
                        var button = $('#survey-row-' + id + ' .toggle-visibility');
                        if (newVisibility == 1) {
                            button.removeClass('btn-success').addClass('btn-warning').text('Onemogući');
                        } else {
                            button.removeClass('btn-warning').addClass('btn-success').text('Omogući');
                        }
                        button.data('visible', newVisibility);
                    } else {
                        alert('Greška prilikom ažuriranja vidljivosti ankete.');
                    }
                }
            });
        });
    });
  </script>

</body>
</html>
