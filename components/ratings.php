<?php
require_once('../services/secure.php');
if (isset($loggedIn)) :
    require_once('../services/mysql.php');
    $link = connect();

    $receipts = getReceiptsByUserId($link, $_SESSION['id_user']);
?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Balanta Veche</th>
                <th>Balanta Noua</th>
                <th>Film</th>
                <th>Data</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($receipts as $receipt) : ?>
                <tr>
                    <?php foreach ($receipt as $key => $cell) : ?>
                        <?php if ($key != 'userId') : ?>
                            <td>
                                <?php if ($key == 'movieId') : ?>
                                    <?= getMovieName($link, $cell) ?>
                                <?php elseif($key == 'id') : ?>
                                    <a href="/proiect/printReceipt.php?id=<?= $cell ?>"><?= $cell ?></a>
                                    <?php else: ?>
                                    <?= $cell ?>
                                <?php endif; ?>

                            </td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>