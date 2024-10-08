
<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="index.php">&#128221; Anketarko &#128221;</a>
    <ul class="navbar-nav mr-auto">
        <li class="nav-item">
            <a class="nav-link" href="index.php">Početna</span></a>
        </li>
        
        <?php if(isset($_SESSION['userID']) && isset($_SESSION['username']) && isset($_SESSION['userType'])){ ?>
        <li class="nav-item">
            <a class="nav-link" href="<?php if($_SESSION['userType'] == 0){ echo 'userDashboard.php'; } else{ echo 'adminDashboard.php'; } ?>">Dashboard</a>
        </li>
        <?php } ?>
    </ul>
    <?php if(!isset($_SESSION['userID']) && !isset($_SESSION['username']) && !isset($_SESSION['userType'])){ ?>
        <a style="margin-left:1em" class="login btn btn-primary" href="signin.php">Login</a>
        <a style="margin-left:1em" class="register btn btn-primary button" href="signup.php">Kreiraj nalog</a>
    <?php } else { ?>
        <li class="nav-link active">Dobrodošli nazad, <?= $_SESSION['username']; ?></li>
        <a class="btn btn-danger float-right" href="logout.php">Logout</a>
    <?php } ?>
</nav>
