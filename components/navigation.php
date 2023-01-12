<nav>
    <div class="nav-wrapper">
        <div class="container">
            <a href="/proiect" class="brand-logo">Inchiriere filme</a>
            <?php
            require_once('./services/secure.php');
            if (isset($loggedIn)):
            ?>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <?php if($_SESSION['user']['account_type'] === 'admin'): ?>
                        <li><a href="/proiect/addMovie.php">Adauga film</a></li>
                        <li><a href="/proiect/users.php">Lista Useri</a></li>
                        <?php endif; ?>
                    <li><a href="/proiect/logs.php">Logs</a></li>
                    <li><a href="/proiect/profile.php">Profile</a></li>
                    <li><a href="/proiect/logout.php"><i class="material-icons">logout</i></a></li>
                </ul>
            <?php else: ?>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li><a href="/proiect/register.php">Register</a></li>
                    <li><a href="/proiect/login.php"><i class="material-icons">login</i></a></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>