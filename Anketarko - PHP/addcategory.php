<?php
///Mihajlo Eskic

require_once 'dbConnection.php';
require_once 'classes/category.php';
    if(isset($_POST['addCategory'])){
       $name = $_POST['name'];
       $description = $_POST['description'];


       $db = Database::getDb();
       $s = new Category();
       $c = $s->addCategory($name, $description, $db);


       if($c){
           header('Location:  listCategories.php');
       } else {
           echo "problem adding a category";
       }

    }
?>

<html lang="en">

<head>
    <title>Anketarko</title>
    <meta name="description" content="Anketarko">
    <meta name="keywords" content="Survey, Category, User, Admin">
    <title>Anketarko</title>
    <meta charset="utf-8">
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.4.1/darkly/bootstrap.min.css" rel="stylesheet" integrity="sha384-rCA2D+D9QXuP2TomtQwd+uP50EHjpafN+wruul0sXZzX/Da7Txn4tB9aLMZV4DZm" crossorigin="anonymous">
    <link rel="stylesheet" href="CSS/main.css" type="text/css">
</head>

<body>

<main class="m-5">
    <!--    Form to Add  Category bu Admin -->
    <form action="" method="post">
    <h1 class="h3">Dodajte novu kategoriju:</h1>
        <div class="form-group">
            <label for="name">Naziv :</label>
            <input type="text" class="form-control" name="name" id="name" value=""
                   placeholder="Enter name">
            <span style="color: red">

            </span>
        </div>
        <div class="form-group">
            <label for="description">Opis :</label>
            <input type="text" class="form-control" id="description" name="description"
                   value="" placeholder="Enter description">
            <span style="color: red">

            </span>
        </div>
        <div lass="text-center">
        <a href="listCategories.php" id="btn_back" class="btn btn-success btn-lg"> Nazad</a>
        <button type="submit" name="addCategory"
        class="btn btn-success btn-lg" id="btn-submit">
            Dodaj kategoriju
        </button>
        </div>
    </form>
</main>


</body>
</html
