<?php


session_start();

//add if statement to check if user is logged in - if not redirect to login page
if(!isset($_SESSION['userID']) && !isset($_SESSION['username']) && !isset($_SESSION['userType'])){
    header('Location:signin.php');
}

require_once 'dbConnection.php';
require_once 'Survey.php';
require_once 'classes/Question.php';
require_once 'classes/category.php';

$id = $_POST['id'];

//connect to db
$dbcon = Database::connectDB();

//list faqs
$s = new Survey();
$survey = $s->showSurvey($dbcon, $id);

$title = $survey->title;
$description = $survey->description;
$category = $survey->category_id;

$c = new Category();
$cat = $c->getCategoryById($category, $dbcon);

//list questions
$q = new Question();
$questions = $q->getQuestionsByID($dbcon, $id);


?>
<html lang="en">
<head>
    <title>Anketarko</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/manageHome.css">
    <link rel="stylesheet" href="css/footerStyle.css">
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.4.1/darkly/bootstrap.min.css" rel="stylesheet" integrity="sha384-rCA2D+D9QXuP2TomtQwd+uP50EHjpafN+wruul0sXZzX/Da7Txn4tB9aLMZV4DZm" crossorigin="anonymous">
</head>
<body>
<?php include_once 'header.php'; ?>
<main id="main">
    <h1 class="h1 text-center pad-top">Detaljni pogled na anketu</h1>
    <div class="m-5">
        <p>Naziv: <span><?= $title; ?></span></p>
        <p>Opis: <span><?= $description; ?></span></p>
        <p>Kategorija: <span><?= $cat->name; ?></span></p>
        <form action="updateSurvey.php" method="post" class="form-inline">
            <input type="hidden" name="id" value="<?= $id; ?>" />
            <input type="submit" class="button btn btn-primary" name="updateSurvey" value="Azuriaj anektu" />
        </form>
        <form action="deleteSurvey.php" method="post" class="form-inline">
            <input type="hidden" name="id" value="<?= $id; ?>">
            <input type="submit" class="button btn btn-danger" name="deleteSurvey" value="Obrisi anketu" onclick="return confirm('Da li ste sigurni da zelite da obrisete anektu?')" />
        </form>
    </div>
    <div class="m-5 content">
        <h2 class="title">Pitanja</h2>
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <th scope="col">Pitanja</th>
                <th scope="col" colspan="2">Akcije</th>
            </tr>
            </thead>
            <tbody>
            <!-- loop through explanations and display -->
            <?php foreach($questions as $question) { ?>
                <tr>
                    <td><?= $question['question_text'] ?></td>
                    <td>
                        <form action="updateQuestion.php" method="post">
                            <input type="hidden" name="id" value="<?= $question['id'] ?>" />
                            <input type="submit" class="button btn btn-primary" name="updateQuestion" value="Azuriraj" />
                        </form>
                    </td>
                    <td>
                        <form action="deleteQuestion.php" method="post">
                            <input type="hidden" name="id" value="<?= $question['id'] ?>"/>
                            <input type="submit" class="button btn btn-danger" name="deleteQuestion" value="Obrisi" onclick="return confirm('Da li ste sigurni da zelite da obrisete?')" />
                        </form>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <div class="text-center">
            <form action="addQuestion.php"method="post">
                <input type="hidden" name="surveyid" value="<?= $id; ?>">
                <button type="submit" class="btn btn-success btn-lg" name="addQuestion">Dodaj pitanje</button>
            </form>
        </div>
    </div>
</main>
<?php include_once 'footer.php' ?>
</body>
</html>