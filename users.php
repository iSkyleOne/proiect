<?php
    require_once('./services/secure.php');
    if(!isset($loggedIn)) {
        header('Location: login.php');
    }
    if($_SESSION['user']['account_type'] != 'admin') {
        header('Location: index.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
<?php include('./components/head.php') ?>
<body>
    <?php include('./components/navigation.php') ?>
    <div class="container">
    <?php include('./components/tableWithUsers.php')?>
    </div>
    <?php include('./components/footer.php') ?>
</body>
</html>