<?php
require_once('./services/secure.php');
if (isset($loggedIn)) :
    require_once('./services/mysql.php');
    $link = connect();
    $users = getAllUsers($link);
?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nume</th>
                <th>E-mail</th>
                <th>Cont</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <?php foreach ($user as $key => $cell) : ?>
                        <td>
                            <?php if ($key === 'name') : ?>
                                <a href="profile.php?id=<?= $user['id'] ?>"><?= $cell ?></a>
                            <?php elseif ($key === 'active') : ?>
                                <?php if ($cell === '1') : ?>
                                    <p class="green-text">Activ</p>
                                <?php else : ?>
                                    <p class="red-text">Inactiv</p>
                                <?php endif; ?>
                            <?php else : ?>
                                <?= $cell ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>