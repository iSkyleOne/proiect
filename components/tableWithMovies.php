<?php
require_once('./services/secure.php');
if (isset($loggedIn)) :
    require_once('./services/mysql.php');
    $link = connect();
    if ($_SESSION['user']['account_type'] == 'admin') {
        $movies = getAllMovies($link, true);
    } else {
        $movies = getAllMovies($link, false);
    }
?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Film</th>
                <th>Data aparitie</th>
                <th>Actori</th>
                <th>Disponibil</th>
                <th>Pret</th>
                <?php if ($_SESSION['user']['account_type'] == 'admin') : ?>
                    <th>Activ</th>
                <?php endif; ?>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($movies as $movie) : ?>
                <tr>
                    <?php foreach ($movie as $key => $cell) : ?>
                        <td>
                            <?php if ($key === 'title') : ?>
                                <a href="viewMovie.php?id=<?= $movie['id'] ?>"><?= $cell ?></a>
                            <?php elseif ($key === 'available') : ?>
                                <?php if ($cell === '1') : ?>
                                    <p class="green-text">Da</p>
                                <?php else : ?>
                                    <p class="red-text">Nu</p>
                                <?php endif; ?>
                            <?php elseif ($key == 'active') : ?>
                                <?php if ($_SESSION['user']['account_type'] == 'admin') : ?>
                                    <?php if ($cell === '1') : ?>
                                        <p class="green-text">Da</p>
                                    <?php else : ?>
                                        <p class="red-text">Nu</p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php else : ?>
                                <?= $cell ?>
                                <?php if ($key === 'price') : ?>
                                    RON
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>