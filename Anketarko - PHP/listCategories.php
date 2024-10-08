<?php
///Mihajlo Eskic

session_start();

require_once 'dbConnection.php';
require_once 'classes/category.php';
require_once 'User.php';
include_once 'header.php';


$dbcon = Database::getDb();
$c = new Category();
$categories =  $c->getAllCategories(Database::getDb());

//to check admin or not
$user_isAdmin=($_SESSION['userType'] );
?>

<html lang="en">
<head>
    <title>Lista kategorija</title>
    <meta name="description" content="Anketarko">
    <meta name="keywords" content="Survey, Category, User, Admin">
    <title>Anketarko</title>
    <meta charset="utf-8">
    <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.4.1/darkly/bootstrap.min.css" rel="stylesheet" integrity="sha384-rCA2D+D9QXuP2TomtQwd+uP50EHjpafN+wruul0sXZzX/Da7Txn4tB9aLMZV4DZm" crossorigin="anonymous">
</head>

<body>

<main class="m-5">
<h1 class="h3">Lista svih anketa</h1>
    <!--    Displaying Data in Table-->
    <table class="table table-bordered tbl">
        <thead class="text-center">
        <tr>
            <th scope="col">Naziv</th>
            <th scope="col">Opis</th>
            <?php if ($user_isAdmin == 1){?>
            <th scope="col">Azuriraj</th>
            <th scope="col">Obrisi</th>
            <?php }?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($categories as $category) { ?>
            <tr>
                <td><?= $category->name ?></td>
                <td><?= $category->description ?></td>
                <?php if ($user_isAdmin == 1){?>
                <td>
                    <form action="updateCategory.php" method="post">
                        <input type="hidden" name="id" value="<?= $category->id ?>"/>
                        <input type="submit" class="button btn btn-primary" name="updateCategory" value="Azuriraj"/>
                    </form>
                </td>
                <td>
                    <form action="deleteCategory.php" method="post">
                        <input type="hidden" name="id" value="<?= $category->id ?>"/>
                        <input type="submit" class="button btn btn-danger" name="deleteCategory" value="Obrisi"/>
                    </form>
                </td>
                <?php }?>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php if ($user_isAdmin == 1){?>
    <a href="addcategory.php" id="btn_addCategory" class="btn btn-success btn-lg float-right">Dodaj kategoriju</a>
    <?php } ?><?php if ($user_isAdmin == 0){?><div>
    <a href="createSurvey.php" class="btn btn-primary btn-lg float-right">Vrati se na kreiranje anketa</a></div>
    <?php } ?>
</main>
</body>
</html>
<?php include_once 'footer.php';?>















