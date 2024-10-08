<?php
require 'vendor/autoload.php';
require_once 'dbConnection.php';
use thiagoalessio\TesseractOCR\TesseractOCR;

$verificationResult = '';
$extractedText = '';
$userNameFromDB = '';
$redirect = false;

$userID = isset($_GET['userID']) ? $_GET['userID'] : (isset($_POST['userID']) ? $_POST['userID'] : '');

if (isset($_POST['upload'])) {
    $imagePath = $_FILES['image']['tmp_name'];

    $ocr = new TesseractOCR($imagePath);
    $ocr->executable('C:\Program Files\Tesseract-OCR\tesseract.exe');
    $extractedText = $ocr->run();

    $dbcon = Database::connectDB();
    $stmt = $dbcon->prepare("SELECT fname, lname FROM users WHERE id = ?");
    $stmt->bindParam(1, $userID, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $userNameFromDB = trim($user['fname'] . ' ' . $user['lname']);

        if (strtolower(trim($extractedText)) === strtolower($userNameFromDB)) {
            $updateStmt = $dbcon->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
            $updateStmt->bindParam(1, $userID, PDO::PARAM_INT);
            $updateStmt->execute();
            $verificationResult = "<p style='color: green;'>Identitet potvrđen! Preusmeravanje na login stranicu za 3 sekunde...</p>";
            $redirect = true; // Oznaka da treba preusmeriti korisnika
        } else {
            $verificationResult = "<p style='color: red;'>Identitet nije potvrđen. Pokušajte ponovo.</p>";
        }
    } else {
        $verificationResult = "<p style='color: red;'>Korisnik nije pronađen.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikacija naloga</title>
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.4.1/darkly/bootstrap.min.css" rel="stylesheet" integrity="sha384-rCA2D+D9QXuP2TomtQwd+uP50EHjpafN+wruul0sXZzX/Da7Txn4tB9aLMZV4DZm" crossorigin="anonymous">
    <style>
        .container {
            background-color: #2b2b2b;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            margin-top: 50px;
        }
        h2, h4 {
            color: #fff;
        }
        .form-group label {
            color: #ddd;
        }
        .btn {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .result p {
            margin-top: 20px;
        }
    </style>
    <?php if ($redirect): ?>
        <script>
            setTimeout(function() {
                window.location.href = "signin.php"; // Preusmeravanje na login stranicu
            }, 3000); // 3000 ms = 3 sekunde
        </script>
    <?php endif; ?>
</head>
<body>
    <div class="container text-center">
        <h2>Verifikacija naloga</h2>
        <form action="verify.php?userID=<?= htmlspecialchars($userID) ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="userID" value="<?= htmlspecialchars($userID) ?>">
            <div class="form-group">
                <label for="image">Upload sliku sa vašim imenom i prezimenom:</label>
                <input type="file" name="image" id="image" class="form-control" required>
            </div>
            <button type="submit" name="upload" class="btn btn-primary btn-lg btn-block">Verifikuj</button>
        </form>

        <div class="result">
            <?= $verificationResult ?>
            <?php if (!empty($extractedText) && !empty($userNameFromDB)): ?>
                <h4>Ekstrahovani tekst:</h4> <p><?= htmlspecialchars($extractedText) ?></p>
                <h4>Očekivani tekst:</h4> <p><?= htmlspecialchars($userNameFromDB) ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
