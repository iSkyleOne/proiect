<?php
require_once('./services/secure.php');
if (isset($loggedIn)) :
    require_once('./services/mysql.php');
    $link = connect();
    $id = $_GET['id'];

    $receipt = getOneReceiptById($link, $id);
?>
    <style type="text/css">
        .tg {
            border-collapse: collapse;
            border-spacing: 0;
        }

        .tg td {
            border-color: black;
            border-style: solid;
            border-width: 1px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            overflow: hidden;
            padding: 10px 5px;
            word-break: normal;
        }

        .tg th {
            border-color: black;
            border-style: solid;
            border-width: 1px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: normal;
            overflow: hidden;
            padding: 10px 5px;
            word-break: normal;
        }

        .tg .tg-0pky {
            border-color: inherit;
            text-align: left;
            vertical-align: top
        }

        .tg .tg-0lax {
            text-align: left;
            vertical-align: top
        }
    </style>
    <table class="tg" style="table-layout: fixed; width: 900px">
        <colgroup>
            <col style="width: 95px">
            <col style="width: 173px">
            <col style="width: 156px">
            <col style="width: 158px">
            <col style="width: 109px">
            <col style="width: 117px">
            <col style="width: 92px">
        </colgroup>
        <thead>
            <tr>
                <th class="tg-0pky">ID Factura</th>
                <th class="tg-0lax">Balanta Veche</th>
                <th class="tg-0lax">Balanta Noua</th>
                <th class="tg-0lax">Produs (Film)</th>
                <th class="tg-0lax">Data Inchiriere</th>
                <th class="tg-0lax">Vandut de</th>
                <th class="tg-0lax">Cumparator</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tg-0lax"><?= $receipt['id'] ?></td>
                <td class="tg-0lax"><?= $receipt['oldBalance'] ?></td>
                <td class="tg-0lax"><?= $receipt['newBalance'] ?></td>
                <td class="tg-0lax"><?= getMovieName($link, $receipt['movieId']) ?></td>
                <td class="tg-0lax"><?= $receipt['timestamp'] ?></td>
                <td class="tg-0lax">Inchirieri Filme</td>
                <td class="tg-0lax"><?= getName($link, $receipt['userId']) ?></td>
            </tr>
        </tbody>
    </table>
    <br>
    <button onclick="window.print(); return false;">Print</button>
<?php endif; ?>