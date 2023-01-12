<?php
require_once('./services/secure.php');
if (isset($loggedIn)) :
    require_once('./services/mysql.php');
    $link = connect();
    $logId = -1;
    if($_SESSION['user']['account_type'] != 'admin') {
        $logId = $_SESSION['user']['id'];
    }
    $logs = getAllLogs($link, $logId);
?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Message</th>
                <th>Date</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($logs as $keyLog => $log) : ?>
                <tr>
                    <?php foreach ($log as $key => $cell) : ?>
                        <td>
                            <?php if ($key === 'id_user') : ?>
                                <?= getName($link, $cell) ?>
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