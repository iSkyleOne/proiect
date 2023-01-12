<?php
require_once('./services/secure.php');
if(isset($loggedIn)) {
    header('Location: index.php');
}

$email = isset($_POST['email']) ? trim($_POST['email']) : null;
$pass = isset($_POST['pass']) ? trim($_POST['pass']) : null;

if ($email && $pass) {
    require_once('./services/mysql.php');

    $link = connect();
    $user = login($link, $email, $pass);

    if ($user !== false) {
        session_start();
        $_SESSION['user'] = $user;
        $_SESSION['id_user'] = $user['id'];
        header('Location: index.php');
    } else {
        $msg = 'Credentiale incorecte / cont dezactivat.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include('./components/head.php'); ?>

<body>
    <?php include('./components/navigation.php'); ?>
    <div class="container login">
        <div class="card">
            <form action="login.php" method="post">
                <div class="card-content">
                    <input type="text" placeholder="E-mail" name="email">
                    <input type="password" placeholder="Parola" name="pass">
                </div>
                <div class="card-action">
                    <button class="waves-effect waves-light btn red lighten-2">
                        Login
                        <i class="material-icons white-text right">login</i>
                    </button>
                    <?php if (isset($msg)) : ?>
                        <p class="red-text"><?= $msg; ?></p>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    <?php include('./components/footer.php') ?>
</body>

</html>