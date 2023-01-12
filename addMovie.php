<?php
require_once('./services/secure.php');
if(!isset($loggedIn)) {
    header('Location: index.php');
}


$title = isset($_POST['title']) ? trim($_POST['title']) : null;
$released = isset($_POST['released']) ? trim($_POST['released']) : null;
$actors = isset($_POST['actors']) ? trim($_POST['actors']) : null;
$price = isset($_POST['price']) ? trim($_POST['price']) : null;
$image0 = isset($_FILES['photo0']) ? $_FILES['photo0'] : null;
$image1 = isset($_FILES['photo1']) ? $_FILES['photo1'] : null;
$image2 = isset($_FILES['photo2']) ? $_FILES['photo2'] : null;
// $blob0 = isset($image0) ? base64_decode(file_get_contents(str_replace(array('-', '_', ' ', '\n'), array('+', '/', ' '), $_FILES['photo0']['tmp_name']))) : null;
// $blob1 = isset($image1) ? base64_decode(file_get_contents(str_replace(array('-', '_', ' ', '\n'), array('+', '/', ' '), $_FILES['photo1']['tmp_name']))) : null;
// $blob2 = isset($image2) ? base64_decode(file_get_contents(str_replace(array('-', '_', ' ', '\n'), array('+', '/', ' '), $_FILES['photo2']['tmp_name']))) : null;

$blob0 = isset($image0) ? addslashes(file_get_contents($image0["tmp_name"])) : null;
$blob1 = isset($image1) ? addslashes(file_get_contents($image1["tmp_name"])) : null;
$blob2 = isset($image2) ? addslashes(file_get_contents($image2["tmp_name"])) : null;


// var_dump($_FILES);

if ($title && $released && $actors && $price) {
    require_once('./services/mysql.php');

    $link = connect();
    $movie = addMovie($link, $_SESSION['user']['id'], $title, $released, $actors, $price, $blob0, $blob1, $blob2);

    if ($movie !== false) {
        header('Location: /proiect/viewMovie.php?id='. $movie['id']);
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
            <form enctype="multipart/form-data" action="addMovie.php" method="post">
                <div class="card-content">
                    <input type="text" placeholder="Titlu" name="title">
                    <input type="date" placeholder="Data aparitie" name="released">
                    <input type="text" placeholder="Actori" name="actors">
                    <input type="text" placeholder="Pret" name="price">
                    <input type="file" placeholder="Poza 1" name="photo0">
                    <input type="file" placeholder="Poza 2" name="photo1">
                    <input type="file" placeholder="Poza 3" name="photo2">
                </div>
                <div class="card-action">
                    <button class="waves-effect waves-light btn red lighten-2" type="submit">
                        Adauga film
                        <i class="material-icons white-text right">add_user</i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php include('./components/footer.php') ?>
</body>

</html>