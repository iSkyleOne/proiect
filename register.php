<?php
require_once('./services/secure.php');
if(isset($loggedIn)) {
    header('Location: index.php');
}


$name = isset($_POST['name']) ? trim($_POST['name']) : null;
$email = isset($_POST['email']) ? trim($_POST['email']) : null;
$pass = isset($_POST['pass']) ? trim($_POST['pass']) : null;
$dob = isset($_POST['dob']) ? trim($_POST['dob']) : null;

if ($email && $pass && $name && $dob) {
    require_once('./services/mysql.php');

    $link = connect();
    $user = createUser($link, $name, $email, $pass, $dob);

    if ($user == true) {
        header('Location: login.php');
    } else {
        $msg = 'Ceva nu a mers cum trebuie.';
    }
} else {
    $msg = 'Verifica toate campurile!';
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include('./components/head.php'); ?>

<body>
    <?php include('./components/navigation.php'); ?>
    <div class="container login">
        <div class="card">
            <form action="register.php" method="post">
                <div class="card-content">
                    <input type="text" placeholder="Nume" name="name">
                    <input type="text" placeholder="E-mail" name="email">
                    <input type="password" placeholder="Parola" name="pass">
                    <input type="date" placeholder="Data de nastere" name="dob">
                </div>
                <div class="card-action">
                    <button class="waves-effect waves-light btn red lighten-2">
                        Register
                        <i class="material-icons white-text right">add_user</i>
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