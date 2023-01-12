<?php
  function connect () {
    $link = mysqli_connect('localhost', 'root' , '' ,'rental');

    return $link;
  }

  // -------------------------------------
  //User
  function createUser($link, $name, $email, $pass, $dob) {
    $sql = "INSERT INTO `users` (`id`, `name`, `email`, `password`, `dob`) VALUES (NULL, '$name', '$email', '$pass', '$dob');";
    registerUserLog($link, $name, $email);
    return mysqli_query($link, $sql);
  }

  function login ($link, $user, $pass) {
    $sql = "SELECT * FROM users WHERE email = '$user' AND active = 1 and password = '$pass';";
    $result = mysqli_query($link, $sql);
    $numRows = mysqli_num_rows($result);
    if ($numRows > 0) {
      $row = mysqli_fetch_assoc($result);

      return $row;
    } else {
      return false;
    }
  }

  function getName($link, $id_user) {
    if($id_user == -1) {
      return 'System';
    }
    $sql = "SELECT name FROM users WHERE `id` = '$id_user';";
    return mysqli_fetch_assoc(mysqli_query($link, $sql))['name'];
  }

  function getAllUsers($link) {
    $sql = "SELECT id, name, email, active FROM users";
    $result = mysqli_query($link, $sql);
    $rows = [];

    while($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  function addMoney($link, $userId, $targetedUserId, $sum){ 
    $sql = "UPDATE users SET balance = balance + '$sum' WHERE `id` = '$targetedUserId';";
    mysqli_query($link, $sql);
    addMoneyLog($link, $userId, $targetedUserId, $sum);
  }

  function deactivateAccount($link, $userId, $targetedUserId){ 
    if($targetedUserId != $userId) {
      $sql = "UPDATE users SET active = 0 WHERE `id` = '$targetedUserId';";
      mysqli_query($link, $sql);
      deactivateAccountLog($link, $userId, $targetedUserId);
    }
  }

  function activateAccount($link, $userId, $targetedUserId){ 
    if($targetedUserId != $userId) {
      $sql = "UPDATE users SET active = 1 WHERE `id` = '$targetedUserId';";
      mysqli_query($link, $sql);
      activateAccountLog($link, $userId, $targetedUserId);
    }
  }

  function getUserById($link, $id_user) {
    $sql = "SELECT * FROM users WHERE `id` = '$id_user';";
    return mysqli_fetch_assoc(mysqli_query($link, $sql));
  }

  function resetPasswordUser($link, $userId) {
    $randPass = substr(str_shuffle(MD5(microtime())), 0, 10);
    $sql = "UPDATE users SET password = '$randPass' WHERE `id` = '$userId';";
    mysqli_query($link, $sql);
    return $randPass;
  }

  // -------------------------------------
  // Movies
  function getMovieById($link, $movieId) {
    $sql = "SELECT * FROM movies WHERE `id` = '$movieId'";
    return mysqli_fetch_assoc(mysqli_query($link, $sql));
  }

  function getMovieName($link, $movieId) {
    $sql = "SELECT title FROM movies WHERE `id` = '$movieId'";
    return mysqli_fetch_assoc(mysqli_query($link, $sql))['title'];
  }

  function getAllMovies($link, $all) {
    if($all == true) {
      $sql = "SELECT id, title, released, actors, available, price, active FROM movies";
    } else {
      $sql = "SELECT id, title, released, actors, available, price, active FROM movies WHERE active = 1";
    }
    $result = mysqli_query($link, $sql);
    $rows = [];

    while($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  function addMovie($link, $userId, $title, $released, $actors, $price, $photo0, $photo1, $photo2) {
    $sql = "INSERT INTO `movies` (`id`, `title`, `released`, `actors`, `price`, `photo0`, `photo1`, `photo2`, `available`, `rented_by_id`) VALUES (NULL, '$title', '$released', '$actors', '$price', '$photo0', '$photo1', '$photo2', 1, NULL);";
    $query = mysqli_query($link, $sql);
    $sql = "SELECT * FROM `movies` WHERE id = " . mysqli_insert_id($link);
    $movie = mysqli_fetch_assoc(mysqli_query($link, $sql));
    addMovieLog($link, $userId, $movie['id']);
    return $movie;
  }

  function deactivateMovie($link, $userId, $movieId){
    $movieName = getMovieName($link, $movieId);
    $sql = "UPDATE `movies` SET active = 0 WHERE id = '$movieId'";
    $result = mysqli_query($link, $sql);

    if($result) {
      deactivateMovieLog($link, $userId, $movieName);
    }
  }

  function activateMovie($link, $userId, $movieId){
    $movieName = getMovieName($link, $movieId);
    $sql = "UPDATE `movies` SET active = 1 WHERE id = '$movieId'";
    $result = mysqli_query($link, $sql);

    if($result) {
      activateMovieLog($link, $userId, $movieName);
    }
  }

  function rentMovie($link, $movieId, $userId) {
    $movie = getMovieById($link, $movieId);

    if($movie['active'] == 0) {
      return 'Acest film nu este pus spre inchiriere';
    }
    
    if($movie['available'] == 0) {
      return 'Acest film este deja inchiriat';
    }

    if($_SESSION['user']['balance'] - $movie['price'] < 0) {
      return 'Nu ai destui bani';
    }

    $sql = "SELECT (EXISTS (SELECT * FROM movies WHERE rented_by_id = '$userId')) AS result;";
    $result = mysqli_fetch_assoc(mysqli_query($link, $sql));

    if ($result['result'] == 0) {
      $sql = "UPDATE movies SET rented_by_id = '$userId', available = 0 WHERE id = $movieId";
      mysqli_query($link, $sql);
      rentMovieLog($link, $userId, $movieId);
      $balance = $_SESSION['user']['balance'];
      $newBalance = $_SESSION['user']['balance'] - $movie['price'];
      $sql = "INSERT INTO `receipts` (`id`, `userId`, `oldBalance`, `newBalance`, `movieId`) VALUES (NULL, '$userId', '$balance', '$newBalance', '$movieId');";
      mysqli_query($link, $sql);
      $_SESSION['user']['balance'] = $newBalance;
      $sql = "UPDATE users SET balance = '" . $_SESSION['user']['balance'] . "', rented_movie = '" . $movieId . "' WHERE id = '" . $_SESSION['user']['id'] . "';";
      mysqli_query($link, $sql);
      $_SESSION['user']['rented_movie'] = $movieId;
      
      return 'Ai inchiriat filmul';
    } else {
      return "Ai deja un film inchiriat";
    }   
  }

  function unRentMovie($link, $userId, $movieId) {
    $sql = "UPDATE movies SET rented_by_id = NULL, available = 1 WHERE id = '$movieId'";
    mysqli_query($link, $sql);
    $sql = "UPDATE users SET rented_movie = 0 WHERE id = '$userId';";
    mysqli_query($link, $sql);
    returnMovieLog($link, $userId, $movieId);
  }

  // -------------------------------------
  // Reviews

  function addReview($link, $movieId, $userId, $stars, $description) {
    if($movieId && $userId && $description && ($stars >= 1 && $stars <= 5)) {
      $sql = "INSERT INTO `reviews` (`id`, `movieId`, `userId`, `stars`, `description`) VALUES (NULL, '$movieId', '$userId', '$stars', '$description');";
      mysqli_query($link, $sql);
    }
  }

  function getAvarageReviews($link, $movieId) {
    $sql = "SELECT AVG(stars) as avarage FROM `reviews` WHERE movieId = '$movieId';";
    $result = mysqli_fetch_row(mysqli_query($link, $sql));
    if ($result[0] == null) {
      return 0;
    } else {
      return $result[0];
    }
  }

  function getReviews($link, $movieId) {
    $sql = "SELECT * FROM `reviews` WHERE movieId = $movieId ORDER BY date DESC;";
    $result = mysqli_query($link, $sql);
    $rows = [];

    while($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  // -------------------------------------
  // Receipts

  function getAllReceipts($link) {
    $sql = "SELECT * FROM `receipts`";
    $result = mysqli_query($link, $sql);
    $rows = [];

    while($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  function getReceiptsByUserId($link, $userId) {
    $sql = "SELECT * FROM `receipts` WHERE userId = '$userId' ORDER BY id DESC";
    $result = mysqli_query($link, $sql);
    $rows = [];

    while($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    return $rows;
  }

  function getOneReceiptById($link, $id) {
    $sql = "SELECT * FROM `receipts` WHERE id = '$id'";
    $result = mysqli_query($link, $sql);
    return mysqli_fetch_assoc($result);
  }

  // -------------------------------------
  //Logs
  function getAllLogs($link, $userId) {
    if ($userId != -1) {
      $sql = "SELECT * FROM logs WHERE `id_user` = '$userId' ORDER BY timestamp DESC;";
    } else {
      $sql = "SELECT * FROM `logs` ORDER BY timestamp DESC;";
    }
    $result = mysqli_query($link, $sql);
    $rows = [];

    while($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }

    return $rows;
  }

  function rentMovieLog($link, $userId, $movieId) {
    $message = getName($link, $userId) . " a inchiriat filmul " . getMovieName($link, $movieId); 
    $sql = "INSERT INTO `logs` (`id`, `id_user`, `log`) VALUES (NULL, '$userId', '$message');";
    mysqli_query($link, $sql);
  }

  function returnMovieLog($link, $userId, $movieId) {
    $message = getName($link, $userId) . " a returnat filmul " . getMovieName($link, $movieId); 
    $sql = "INSERT INTO `logs` (`id`, `id_user`, `log`) VALUES (NULL, '$userId', '$message');";
    mysqli_query($link, $sql);
  }

  function addMovieLog($link, $userId, $movieId) {
    $message = getName($link, $userId) . " a adaugat filmul " . getMovieName($link, $movieId) . " ($movieId)"; 
    $sql = "INSERT INTO `logs` (`id`, `id_user`, `log`) VALUES (NULL, '$userId', '$message');";
    mysqli_query($link, $sql);
  }

  function deactivateMovieLog($link, $userId, $movieName) {
    $message = getName($link, $userId) . " a scos filmul " . $movieName . " (dezactivat)"; 
    $sql = "INSERT INTO `logs` (`id`, `id_user`, `log`) VALUES (NULL, '$userId', '$message');";
    mysqli_query($link, $sql);
  }

  function activateMovieLog($link, $userId, $movieName) {
    $message = getName($link, $userId) . " a bagat filmul " . $movieName . " inapoi spre inchiriere (activat)"; 
    $sql = "INSERT INTO `logs` (`id`, `id_user`, `log`) VALUES (NULL, '$userId', '$message');";
    mysqli_query($link, $sql);
  }

  function registerUserLog($link, $userName, $email) {
    $message = $userName . ' (' . $email . ') s-a inregistrat.';
    $sql = "INSERT INTO `logs` (`id`, `id_user`, `log`) VALUES (NULL, '-1', '$message');";
    mysqli_query($link, $sql);
  }

  function deactivateAccountLog($link, $userId, $managedUserId) {
    $message = getName($link, $userId) . " a dezactivat contul " . getName($link, $managedUserId); 
    $sql = "INSERT INTO `logs` (`id`, `id_user`, `log`) VALUES (NULL, '$userId', '$message');";
    mysqli_query($link, $sql);
  }

  function activateAccountLog($link, $userId, $managedUserId) {
    $message = getName($link, $userId) . " a activat contul " . getName($link, $managedUserId); 
    $sql = "INSERT INTO `logs` (`id`, `id_user`, `log`) VALUES (NULL, '$userId', '$message');";
    mysqli_query($link, $sql);
  }

  function addMoneyLog($link, $userId, $targetedUserId, $sum) {
    if ($userId == $targetedUserId) {
      $message = getName($link, $userId) . " a adaugat " . $sum . " RON"; 
    }
    $message = getName($link, $userId) . " a adaugat " . $sum . " RON pe contul " . getName($link, $targetedUserId);
    $sql = "INSERT INTO `logs` (`id`, `id_user`, `log`) VALUES (NULL, '$userId', '$message');";
    mysqli_query($link, $sql);
  }
