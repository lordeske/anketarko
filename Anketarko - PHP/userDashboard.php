<?php
session_start();
require_once 'dbConnection.php';
require_once 'User.php';
require_once 'Survey.php';

//If user is not loggid in, will be re-directed to login page
if(!isset($_SESSION['userType']) && !isset($_SESSION['userID'])){
  header('login.php');
}
if(isset($_SESSION['username']) && isset($_SESSION['userID']) && isset($_SESSION['userType'] )){

//echo $_SESSION['username'];

//         User 
$id=(int)$_SESSION['userID'];
//echo $id;

$dbcon = Database::getDb();

$s = new User();
// $users = $s->listUsers($dbcon); 

// show user
$pageuser = $s->displayUser($dbcon, $id);
//var_dump ($pageuser);
$user_fname=$pageuser->fname;
$user_lname=$pageuser->lname;
$user_email=$pageuser->email;
$user_password=$pageuser->password;
$user_isAdmin=$pageuser->isAdmin;

// end show user

// update User 

        // Update User
        // Update User
// Update User
if(isset($_POST['updUser'])) {
  $user_fname = $_POST['userfname'];
  $user_lname = $_POST['userlname'];
  $new_password = $_POST['newpassword'];
  $confirm_password = $_POST['confirmpassword'];

  // Provera da li su nove šifre iste
  if ($new_password === $confirm_password) {
      $dbcon = Database::getDb();
      $s = new User();

      // Hashovanje nove šifre pre čuvanja u bazi (ako je šifra promenjena)
      $hashed_password = !empty($new_password) ? password_hash($new_password, PASSWORD_DEFAULT) : $user_password;

      // Ažuriranje korisnika u bazi (imenom, prezimenom i šifrom)
      $count = $s->updateUserPassword($dbcon, $id, $user_fname, $user_lname, $hashed_password);

      if($count){
          // Ažuriranje sesije sa novim imenom i prezimenom
          $_SESSION['username'] = $user_fname; // Ažuriraj sesiju sa novim imenom

          // Poruka o uspešnom ažuriranju
          $message = "Podaci su uspešno ažurirani!";
          header("Location: userDashboard.php");
          exit();
      } else {
          echo "Problem prilikom ažuriranja korisnika.";
      }
  } else {
      echo "Šifre se ne poklapaju!";
  }
}




        // Delete User

        if(isset($_POST['deleteUser'])){
            $user_id = $_POST['sid'];

            $dbcon = Database::getDb();

            $s = new User();
            $count = $s->deleteUser($dbcon,$id);

            if($count){
                header("Location: signup.php");
            }
            else {
                echo " Problem Deleting your profile";
            }
        }

        // End Delete User

// end update User

//Begin List the surveys

$dbcon = Database::getDb();
$s = new Survey();

// show Surveys that are created  by the user
$allsurveys = $s->displaySurveys($dbcon, $id);
//var_dump($allsurveys);
//Show all surveys that user have taken

$takenSurveys= $s->AllSurveysTakenByUser($dbcon, $id);
//var_dump($takenSurveys);
// END List the surveys
      }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.4.1/darkly/bootstrap.min.css">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/userDashboard.css">
    <title>Anketarko</title>
</head>
<body>
<?php include_once 'header.php'; ?>
  <div>
    <header class="header text-center">
    
    
    <a style="float: right" class="btn" href="list.php" target="">Sve ankete</a>
    <a style="float: right" class="btn" href="index.php" target="">Početna</a>
    </header>
  </div>

<main class="main">

<!-- Top buttons -->
<div .class="inline-block">

  <a class="btn btn-primary btn-lg" href="createSurvey.php" role="button">Kreiraj novu anketu</a>
 
  
</div> 

</div>

<!-- end Top Buttons -->
<div id="tables">
<h2>Vase ankete</h2>
<table class="table table-hover">
  <thead>
    <tr>
      <!-- <th scope="col">Survey ID</th> -->
      <th scope="col">Naziv ankete</th>
      
      <th scope="col">Opis</th>
      <th scope="col">Datum kreiranja</th>
      <th scope="col">Statistika</th>
    </tr>
  </thead>
  <tbody>
<?php 
    for ($i=0; $i<count($allsurveys); $i++ ){
      echo '<tr class="table-primary">';

      // echo '<td scope="row">'.$allsurveys[$i]->survey_id.'</td>';
      echo '<td scope="row">'.$allsurveys[$i]->title.'</td>';
      echo '<td scope="row">'.$allsurveys[$i]->description.'</td>';
      echo '<td scope="row">'.$allsurveys[$i]->created_date.'</td>';
      //input survey id to calculate average for specific survey   
        $avrg= $s->avgResponse($dbcon, $allsurveys[$i]->survey_id);
        $average= $avrg[0]->average;
       $avrg= $s->avgResponse($dbcon, $allsurveys[$i]->survey_id);
        $average= $avrg[0]->average;
      echo '<td scope="row"> 
      <form action="SurveyResults.php" method="post">
      <input type="hidden" name="id" value="'.$allsurveys[$i]->survey_id.'"/>
      <input type="submit" class="btn btn-link" name="SurveyStats" value="Status"/>
  </form>
  </td>';
      echo '</td>';
      echo '</tr>';
    }


    

?>
  </tbody>
</table> 




<!--Begin Table of Surveys User Taken-->
<h2>Odrađene ankete</h2>
<table class="table table-hover">
  <thead>
    <tr>
      <!-- <th scope="col">Survey ID</th> -->
      <th scope="col">Naziv ankete</th>
      <th scope="col">Opis</th>
      <th scope="col">Datum kreiranja</th>
      <th scope="col">Ocena</th>
    </tr>
  </thead>
  <tbody>
<?php 
    for ($i=0; $i<count($takenSurveys); $i++ ){
      echo '<tr class="table-primary">';

      // echo '<td scope="row">'.$takenSurveys[$i]->id.'</td>';
      echo '<td scope="row">'.$takenSurveys[$i]->title.'</td>';
      echo '<td scope="row">'.$takenSurveys[$i]->description.'</td>';
      echo '<td scope="row">'.$takenSurveys[$i]->created_date.'</td>';
      //input survey id to calculate average for specific survey   
        $avrg= $s->avgResponse($dbcon, $takenSurveys[$i]->id);
        $average= $avrg[0]->average;
      echo '<td scope="row">'.$average.'</td>';
      // echo '</td>';
      echo '</tr>';
    }

   

?>
  </tbody>
</table> 
</div>
<!--End Table of Surveys User Taken-->





<div class="jumbotron profile">
  <!-- <h1 class="display-3">Hello, <?= $user_fname ?></h1> -->
  <div class="profileImage">
<!-- https://www.iconfinder.com/icons/131511/account_boss_caucasian_chief_director_directory_head_human_lord_main_male_man_manager_profile_user_icon -->
  <!-- <img src="img/face.png" alt="generic face photo"></br> -->
 </div> 
 <form action="" method="POST">
  <fieldset> 
    <input type="hidden" id="sid" name="sid" value="<?=$id;?>" />     
    <label for="userfname">Ime: </label>
    <input type="text" id="userfname" name="userfname" value="<?=$user_fname?>" /> </br>
    <label for="userlname">Prezime: </label>
    <input type="text" id="userlname" name="userlname" value="<?=$user_lname?>" /></br>

    <!-- Polja za unos nove šifre -->
    <label for="newpassword">Nova šifra: </label>
    <input type="password" id="newpassword" name="newpassword" /> </br>

    <label for="confirmpassword">Potvrdi novu šifru: </label>
    <input type="password" id="confirmpassword" name="confirmpassword" /></br>

    <button type="submit" name="updUser" id="updUser" class="button">Azuriraj nalog</button>
  </fieldset>  
</form>

<div> 
        <?php if(isset($message)){
        echo $message;
        } 
        ?>
</div>


</main> 


</body>
</html>
