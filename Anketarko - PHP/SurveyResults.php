<?php
session_start();
require_once 'dbConnection.php';
require_once 'Survey.php';
require_once 'header.php';
$response = "";

// Tekstualni odgovori
$answerTexts = [
    1 => "Potpuno se ne slažem",
    2 => "Ne slažem se",
    3 => "Neutralan/na",
    4 => "Slažem se",
    5 => "Potpuno se slažem"
];

if (isset($_POST['SurveyStats'])) {
    $user_id = $_SESSION['userID'];
    $survey_id = $_POST['id'];
    $db = Database::getDb();
    $survey = new Survey();

    // Get average response for the survey
    $averageResponse = $survey->avgResponse($db, $survey_id);

    // Get number of users who have completed the survey
    $noOfUsers = $survey->numberofuserspersurvey($survey_id, $db);

    // Get all questions and their corresponding answers count for the survey
    $questionsWithResponses = $survey->getQuestionsWithResponses($db, $survey_id);
}
?>

<html lang="en">
<head>
    <title>Anketarko</title>
    <meta name="description" content="Survey Bobby">
    <meta name="keywords" content="Survey, Category, User, Admin">
    <meta charset="utf-8">
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.4.1/darkly/bootstrap.min.css" rel="stylesheet" integrity="sha384-rCA2D+D9QXuP2TomtQwd+uP50EHjpafN+wruul0sXZzX/Da7Txn4tB9aLMZV4DZm" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Fleksibilne kolone */
            gap: 20px; /* Razmak između Chart-ova */
            margin-top: 40px; /* Razmak između vrha i Pie Chart-ova */
        }
        .chart-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            border-radius: 10px;
        }
        .chart-title {
            margin-bottom: 10px;
            text-align: center;
            color: #fff; /* Naslovi će biti beli, prilagođeno tamnoj pozadini */
        }
        .btn-back {
            margin-top: 40px; /* Razmak između vrha stranice i dugmeta */
        }
    </style>
</head>

<body>

<main id="surveyResult" style="text-align: center" class="m-5">
    <h1 class="h1" style="color: #fff;">Status Anketi koje ste postavili</h1>
    
    <h3 class="h3" style="color: #fff;">Broj ljudi koji je odradilo anketu: <?= isset($noOfUsers['no_of_users']) ? $noOfUsers['no_of_users'] : "N/A"; ?></h3>

    <div class="charts-grid">
        <!-- Prikaz pitanja i njihovih odgovora kao mali Pie Chart u fleksibilnim kolonama -->
        <?php if (isset($questionsWithResponses)): ?>
            <?php foreach ($questionsWithResponses as $index => $questionData): ?>
                <div class="chart-container">
                    <h4 class="chart-title"><?= $questionData['question']; ?></h4>
                    <canvas id="questionChart<?= $index; ?>" width="150" height="150"></canvas> <!-- Mali Pie Chart -->
                </div>
                <script>
                var ctx = document.getElementById('questionChart<?= $index; ?>').getContext('2d');
                var questionChart<?= $index; ?> = new Chart(ctx, {
                    type: 'pie', // Koristimo Pie Chart
                    data: {
                        labels: [ // Tekstualni odgovori kao oznake
                            "<?= $answerTexts[1] ?>", 
                            "<?= $answerTexts[2] ?>", 
                            "<?= $answerTexts[3] ?>", 
                            "<?= $answerTexts[4] ?>", 
                            "<?= $answerTexts[5] ?>"
                        ],
                        datasets: [{
                            data: [<?= implode(', ', array_values($questionData['answers'])); ?>], // Podaci za pie chart, broj odgovora za svaki odgovor
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: false, // Postavljamo na false kako bi dijagrami bili fiksne veličine
                        maintainAspectRatio: true
                    }
                });
                </script>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <a href="list.php" id="btn_back" class="btn btn-secondary align-center btn-lg btn-back">Vrati se nazad</a>
</main>

</body>
</html>
