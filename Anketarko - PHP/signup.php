<?php
session_start();

// Ako je korisnik već ulogovan, preusmeri ga na odgovarajući dashboard
if (isset($_SESSION['userID']) && isset($_SESSION['username']) && isset($_SESSION['userType'])) {
    if ($_SESSION['userType'] == 1) {
        header('Location:adminDashboard.php');
    } else {
        header('Location:userDashboard.php');
    }
}

require_once 'dbConnection.php';
require_once 'Survey.php';
require_once 'User.php';
$warning = "none;";
$error = "";

if (isset($_POST['signup'])) {
    $fname = $_POST['userFname'];
    $lname = $_POST['userLname'];
    $email = $_POST['userEmail'];
    $password = $_POST['userPassword'];
    
    // reCAPTCHA validacija
    $captcha = $_POST['g-recaptcha-response'];
    $secretKey = '////';
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha");
    $responseKeys = json_decode($response, true);

    if (intval($responseKeys["success"]) !== 1) {
        $warning = "block;";
        $error = "Molimo potvrdite da niste robot";
    } else {
        // Povezivanje sa bazom i registracija korisnika
        $dbcon = Database::connectDB();
        $newUserId = Database::registerUser($fname, $lname, $email, $password);

        if ($newUserId) {
            // Preusmeravanje na stranicu za verifikaciju sa ID-om korisnika kao GET parametrom
            header('Location: verify.php?userID=' . $newUserId);
            exit(); // Osigurava da se dalje izvršavanje koda zaustavi nakon redirekcije
        } else {
            $warning = "block;";
            $error = "Desila se greska!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Anketarko</title>
    <link rel="stylesheet" href="css/signin.css">
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.4.1/darkly/bootstrap.min.css" rel="stylesheet" integrity="sha384-rCA2D+D9QXuP2TomtQwd+uP50EHjpafN+wruul0sXZzX/Da7Txn4tB9aLMZV4DZm" crossorigin="anonymous">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <main id="signupSection">
        <form id="signupForm" method="POST">
            <fieldset>
                <legend>Kreiraj novi nalog</legend>
                <div class="form-group row">
                    <label for="userfname" class="col-sm-2 col-form-label">Ime</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="userFname" name="userFname" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="userlname" class="col-sm-2 col-form-label">Prezime</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="userLname" name="userLname" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="userEmail" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="userEmail" name="userEmail" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-sm-2 col-form-label">Lozinka</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="userPassword" name="userPassword" required>
                    </div>
                </div>
                
                <!-- reCAPTCHA widget -->
                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <div class="g-recaptcha" data-sitekey="6LdxSzMqAAAAAJl4Rc6_E1QvnBlN4DhIZ1rwOGlM"></div>
                    </div>
                </div>

                <button name="signup" type="submit" class="btn btn-primary">Kreiraj nalog</button>
                
            </fieldset>
        </form>

        <a href="signin.php">Vec imas nalog?</a>

        <div id="wrongUserCred" class="alert alert-dismissible alert-danger" <?= 'style="display: ' . $warning . '"' ?>>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Desila se greska!</strong><?=$error?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer text-center">
        <p>&copy; <?php echo date('Y'); ?> &#128221; Anketarko &#128221;</p>
    </footer>
    <!-- END Footer -->

    <!-- scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>
