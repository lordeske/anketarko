<?php
session_start();
date_default_timezone_set("America/Toronto");
require_once 'dbConnection.php';

// If user is not logged in, redirect to login page
if (!isset($_SESSION['userType']) && !isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit();
}

$dbcon = Database::connectDB();

if (isset($_POST['surveySubmit'])){
    foreach ($_POST as $q=>$a) {
        if($q !== "surveySubmit")
            Database::insertAnswer($a, $q , $_SESSION['userID']);
    }
    header('Location: list.php');
    exit();
} else {
    if (!isset($_POST['takeSurvey'])) {
        header('Location: list.php');
        exit();
    }

    $survey_id = $_POST['id'];

    // Proveravamo da li je korisnik već popunio ovu anketu
    $user_id = $_SESSION['userID'];
    $query = "SELECT COUNT(*) as count FROM answers WHERE user_id = :user_id AND question_id IN (SELECT id FROM questions WHERE survey_id = :survey_id)";
    $stmt = $dbcon->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':survey_id', $survey_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);

    // Ako je rezultat veći od nule, korisnik je već popunio anketu
    if ($result->count > 0) {
        echo "<body style='background-color: #222222; color: #ffffff; text-align: center; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;'>";
        echo "<div style='text-align: center;'>";
        echo "<h1 style='font-size: 2.5rem; margin-bottom: 20px;'>Anketa je već popunjena</h1>";
        echo "<h3 style='font-size: 1.5rem; margin-bottom: 30px;'>Već ste popunili ovu anketu i ne možete je ponovo popuniti.</h3>";
        echo "<a href='list.php' style='display: inline-block; padding: 10px 20px; font-size: 1.2rem; color: #ffffff; background-color: #375A7F; text-decoration: none; border-radius: 5px; transition: background-color 0.3s ease;'>Vrati se nazad</a>";
        echo "<style>a:hover { background-color: #ff4c4c; }</style>";
        echo "</div>";
        echo "</body>";
        exit();
    }
    
    
    

    $survey = Database::getSurvey($survey_id);
    $title = $survey->title;
    $category = Database::getCategoryName($survey->category_id);
    $creator = Database::getUserName($survey->user_id);
    $description = $survey->description;

    $questions = Database::getQuestions($survey->id);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.4.1/darkly/bootstrap.min.css">
    <link rel="stylesheet" href="css/takeSurvey.css">

    <title>Take Survey</title>
    <style>
        .alert-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 70vh;
        }

        .alert {
            max-width: 600px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        .alert h4 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .alert p {
            font-size: 1.1rem;
            margin-bottom: 20px;
        }

        .alert a {
            padding: 10px 20px;
            font-size: 1.1rem;
            border-radius: 5px;
        }
        
        /* Custom styles for survey result page */
        #surveyResult {
            text-align: center;
            margin: 5rem;
        }

        #surveyResult h1, #surveyResult h3 {
            color: #fff;
        }

        .btn-back {
            margin-top: 2rem;
            padding: 10px 20px;
            font-size: 1.2rem;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .chart-container {
            background-color: #fff;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .chart-title {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: #333;
        }
    </style>
</head>

<body>

    <main id="takeSurvey">
        <!-- Intro Card -->
        <div id="surveyIntro" class="card text-white bg-primary mb-3" style="max-width: 20rem;">
            <div class="card-body">
                <h4 class="card-title"><?= $title ?></h4>
                <p class="card-text"><?= $description ?></p>
            </div>
            <div class="card-header">Kreirao/la <?= $creator ?></div>
            <div class="card-header"><?= $category ?></div>
        </div>
        <div class="surveyCancelLink"><a href="list.php">Vrati se nazad bez da radiš anketu</a></div>

        <!-- Survey Form -->
        <?php if ($result->count == 0) : ?>
        <form method="post">
            <?php foreach ($questions as $index => $question) { ?>
                <fieldset class="form-group question">
                    <legend class="questionNo">Pitanje <?= $index + 1 ?></legend>
                    <legend><?= $question->question_text ?></legend>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="<?= $question->id ?>" value="1" required>
                            Potpuno se ne slažem
                        </label>
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="<?= $question->id ?>" value="2">
                            Ne slažem se
                        </label>
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="<?= $question->id ?>" value="3">
                            Neutrlan/na 
                        </label>
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="<?= $question->id ?>" value="4">
                            Slažem se
                        </label>
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="<?= $question->id ?>" value="5">
                            Potpuno se slažem
                        </label>
                    </div>
                </fieldset>
            <?php } ?>
            <button id="surveySubmit" name="surveySubmit" type="submit" class="btn btn-success btn-lg">Potvrdi</button>
            <div class="surveyCancelLink"><a href="list.php">Vrati se nazad</a></div>
        </form>
        <?php endif; ?>
    </main>

</body>

</html>
