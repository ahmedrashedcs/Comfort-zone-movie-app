<?php
session_start();

// only if the user is logged in they will have the userid set in the DB
// if it is not set it will directly take the user to the login page
if (isset($_SESSION['userid'])) {
  header("Location:view-account.php");
  exit();
}

//get data from post
$username = $_POST['username'] ?? "";
$password = $_POST['password'] ?? "";
$errors = array();

//when form has been submitted
if (isset($_POST['submit'])) {
  require_once("./includes/library.php");
  $pdo = connectdb();

  $query = "SELECT userID, pwd, api_key FROM users WHERE username = ?";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$username]);
  $dbrow = $stmt->fetch();

  if ($dbrow) {
    // password verification
    if (password_verify($password, $dbrow['pwd'])) {

      $_SESSION['username'] = $username;
      $_SESSION['userid'] = $dbrow['userID'];
      header("Location: view-account.php");
      exit();
    } else {
      //Invalid password
      $errors['password'] = true;
    }
  } else {
    //Invalid username
    $errors['username'] = true;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="./styles/main.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
    rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">
  <script src="https://kit.fontawesome.com/4afc56af50.js" crossorigin="anonymous"></script>
</head>

<body>
  <header>
    <h1>My Movie API</h1>
  </header>
  <main>
    <section class="container">
      <div id="center-container">
        <h2>Login</h2>
        <form id="login" method="post" action="">
          <div class="form-item col">
            <label for="username">Username:</label>
            <div class="box">
              <input type="text" id="username" name="username" size="25" value="<?= $username ?>" placeholder="Type your username" />
              <i class="fa-solid fa-user" for="username"></i>
            </div>
            <span class="error <?= !isset($errors['username']) ? 'hidden' : '' ?>">Your username was invalid</span>
          </div>

          <div class="form-item col">
            <label for="password">Password:</label>
            <div class="box">
              <input type="password" id="password" name="password" size="25" placeholder="Type your password" />
              <i class="fa-solid fa-lock" for="password"></i>
            </div>
            <span class="error <?= !isset($errors['password']) ? 'hidden' : '' ?>">Your password was invalid</span>
          </div>

          <button id="submit" name="submit" class="centered">Login</button><br>
          <a href="create-account.php" class="centered">Create a New Account</a>
        </form>
    </section>

    </div>
  </main>

  <footer>&copy; COIS 3430, Inc. 2024 &mdash; Built by Ahmed Rashed</footer>

</body>

</html>