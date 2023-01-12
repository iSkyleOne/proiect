<?php
require_once('./services/secure.php');

if (isset($loggedIn)) {
    require_once('./services/mysql.php');
    $id = $_GET['id'];
    $link = connect();
    $movie = getMovieById($link, $id);
    if ($movie['active'] == 0 && $_SESSION['user']['account_type'] != 'admin') {
        header('Location: index.php');
    }
    $message = '';
} else {
    header('Location: login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include('./components/head.php'); ?>

<body>
    <?php include('./components/navigation.php'); ?>
    <div class="container">
        <div class="card">
            <div class="photo-container">
                <div class="big_photo">
                    <?php print '<img src="data:image/jpeg;base64,' . base64_encode($movie['photo0']) . '"/>'; ?>
                </div>
                <div class="photo1">
                    <?php print '<img src="data:image/jpeg;base64,' . base64_encode($movie['photo2']) . '"/>'; ?>
                </div>
                <div class="photo2">
                    <?php print '<img src="data:image/jpeg;base64,' . base64_encode($movie['photo1']) . '"/>'; ?>
                </div>
            </div>
        </div>
        <div class="card">
            <table class="striped">
                <tr>
                    <th>Titlu</th>
                    <td>
                        <?php echo $movie['title'] ?>
                    </td>
                </tr>
                <tr>
                    <th>Data aparitie</th>
                    <td><?php echo $movie['released'] ?></td>
                </tr>
                <tr>
                    <th>Actori</th>
                    <td><?php echo $movie['actors'] ?></td>
                </tr>
                <tr>
                    <th>Rating</th>
                    <td><?php echo getAvarageReviews($link, $id) ?></td>
                </tr>
                <tr>
                    <th>Pret</th>
                    <td><?php echo $movie['price'] ?> RON</td>
                </tr>
                <tr>
                    <th>Inchiriaza film</th>
                    <td>
                        <?php
                        if (isset($_POST['rent'])) {
                            $message = rentMovie($link, $id, $_SESSION['id_user']);
                        }
                        ?>
                        <form method="post">
                            <button class="waves-effect waves-light btn red lighten-2 <?php echo $movie['rented_by_id'] ? 'disabled' : '' ?>" name="rent">
                                Inchiriaza
                                <i class="material-icons white-text right">lock_reset</i>
                            </button>
                        </form>
                        <?php if (isset($message)) : ?>
                            <p><?= $message; ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if ($_SESSION['user']['account_type'] == 'admin') : ?>
                    <tr>
                        <th>
                            <?php if ($movie['rented_by_id']) : ?>
                                Filmul este inchiriat de <a href="profile.php?id=<?= $movie['rented_by_id'] ?>"><?= getName($link, $movie['rented_by_id']) ?></a><br>
                            <?php endif; ?>
                            Forteaza user sa returneze filmul</th>
                        <td>
                            <?php
                            if (isset($_POST['forceUserUnrent'])) {
                                unRentMovie($link, $movie['rented_by_id'], $id);
                                $movie['rented_by_id'] = null;
                            }
                            ?>
                            <form method="post">
                                <button class="waves-effect waves-light btn red lighten-2 <?php echo $movie['rented_by_id'] ? '' : ' disabled' ?>" type="submit" name="forceUserUnrent">
                                    Returneaza Fortat
                                    <i class="material-icons white-text right">lock_reset</i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <th>Activare / Dezactivare film</th>
                        <td>
                            <?php
                            if (isset($_POST['deactivateMovie'])) {
                                if ($movie['active'] == 1) {
                                    deactivateMovie($link, $_SESSION['user']['id'], $id);
                                } else {
                                    activateMovie($link, $_SESSION['user']['id'], $id);
                                }
                                $movie['active'] = !$movie['active'];
                            }
                            ?>
                            <form method="post">
                                <button class="waves-effect waves-light btn red lighten-2" type="submit" name="deactivateMovie" onclick="return confirm('Esti sigur ca vrei sa activezi/dezactivezi filmul?');">
                                    <?php if ($movie['active'] == 1) : ?>
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
            </table>
        </div>
        <style>
            .review {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }
            .insertReview {
                padding: 0 1rem 0 1rem;
            }
        </style>
        <div class="card review">
            <div class="insertReview">
                <?php
                if (isset($_POST['addReview'])) {
                    addReview($link, $id, $_SESSION['user']['id'], $_POST['reviewStars'], $_POST['reviewDescription']);
                }
                ?>
                <form method="post">
                    <div class="review-title">
                        <label for="textarea1">Review </label>
                        <p class="range-field disabled text-red">
                            <input type="range" id="test5" min="1" max="5" value="5" name="reviewStars" />
                        </p>
                    </div>
                    <textarea id="textarea1" class="materialize-textarea" name="reviewDescription"></textarea>
                    <button class="waves-effect waves-light btn red lighten-2" type="submit" name="addReview">
                        Adauga review
                        <i class="material-icons white-text right">add</i>
                    </button>
                </form>
            </div>
            <?php
            $_GET['reviewMovieId'] = $id;
            include('./components/tableReviews.php') ?>
        </div>
    </div>
    <?php include('./components/footer.php') ?>
</body>

</html>