<?php
require_once('./services/secure.php');

if (!isset($loggedIn)) {
    header('Location: login.php');
}

if (isset($loggedIn)) {
    require_once('./services/mysql.php');

    if (isset($_GET['id'])) {
        if ($_SESSION['user']['account_type'] != 'admin' && $_GET['id'] != $_SESSION['user']['id']) {
            header('Location: index.php');
        }
        $id = $_GET['id'];
    }
    $link = connect();

    if (!isset($id)) {
        $id = $_SESSION['user']['id'];
    }

    $user = getUserById($link, $id);
    // print_r($user);

    $passwordReseted = '';
    $rentedMovie = $user['rented_movie'] == 0 ? false : true;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include('./components/head.php'); ?>

<body>
    <?php include('./components/navigation.php'); ?>
    <div class="container">
        <div class="card">
            <table class="striped">
                <?php if($_SESSION['user']['account_type'] == 'admin'): ?>
                <tr>
                    <th>Activare / Dezactivare Cont</th>
                    <td>
                        <?php
                        if (isset($_POST['deactivateAccount'])) {
                            if ($user['active'] == 1) {
                                deactivateAccount($link, $_SESSION['user']['id'], $id);
                            } else {
                                activateAccount($link, $_SESSION['user']['id'], $id);
                            }
                            $user['active'] = !$user['active'];
                        }
                        ?>
                        <form method="post">
                            <button class="waves-effect waves-light btn red lighten-2 <?php echo $id != $_SESSION['user']['id'] ? '' : 'disabled' ?>" type="submit" name="deactivateAccount" onclick="return confirm('Esti sigur ca vrei sa activezi/dezactivezi contul?');">
                                <?php if ($user['active'] == 1) : ?>
                                    Dezactiveaza
                                <?php else : ?>
                                    Activeaza
                                <?php endif; ?>
                                <i class="material-icons white-text right">lock_reset</i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th>E-mail</th>
                    <td>
                        <?php echo $user['email'] ?>
                    </td>
                </tr>
                <tr>
                    <th>Nume</th>
                    <td><?php echo $user['name'] ?></td>
                </tr>
                <tr>
                    <th>Data de nastere</th>
                    <td><?php echo $user['dob'] ?></td>
                </tr>
                <tr>
                    <th>Tip cont</th>
                    <td><?php echo $user['account_type'] ?></td>
                </tr>
                <tr>
                    <th>Balanta</th>
                    <td>
                        <?php echo $user['balance'] ?> RON<br>
                        <?php if ($_SESSION['user']['account_type'] == 'admin') : ?>
                            <?php
                            if (isset($_POST['updateBalance'])) {
                                addMoney($link, $_SESSION['user']['id'], $id, $_POST['money']);
                                header("Location: profile.php?id=" . $id);
                            }
                            ?>
                            <form method="post">
                                <input type="text" placeholder="Adauga bani" name="money">
                                <button class="waves-effect waves-light btn red lighten-2" type="submit" name="updateBalance">
                                    Adauga
                                    <i class="material-icons white-text right">add</i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Parola (resetarea parolei)</th>
                    <td>
                        <?php
                        if (isset($_POST['resetPassword'])) {
                            $passwordReseted = "Noua parola este: " . resetPasswordUser($link, $id);
                        }
                        ?>
                        <form method="post">
                            <button class="waves-effect waves-light btn red lighten-2" name="resetPassword" onclick="return confirm('Esti sigur ca vrei sa resetezi parola contului?');">
                                RESET
                                <i class="material-icons white-text right">lock_reset</i>
                            </button>
                            <?php if (isset($passwordReseted)) : ?>
                                <p class="red-text"><b><?= $passwordReseted; ?></b></p>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
                <tr>
                    <th>Returneaza filmul</th>
                    <td>
                        <?php
                        if (isset($_GET['returnMovie'])) {
                            unRentMovie($link, $_SESSION['user']['id'], $_SESSION['user']['rented_movie']);
                            header('Location: profile.php?id=' . $id);
                        }
                        ?>
                        <form method="get">
                            <button class="waves-effect waves-light btn red lighten-2 <?php echo $rentedMovie == true ? '' : 'disabled' ?>" name="returnMovie">
                                Returneaza
                                <i class="material-icons white-text right">lock_reset</i>
                            </button>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
        <div class="card">
        </div>
        <div class="card">
            <?php
            $_GET['idReceipts'] = $id;
            include('./components/receipts.php');
            ?>
        </div>
    </div>
    <?php include('components/footer.php') ?>
</body>

</html>