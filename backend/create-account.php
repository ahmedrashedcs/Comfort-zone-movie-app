<?php
session_start();

// only if the user is logged in they will have the userid set in the DB
// if it is not set it will directly take the user to the login page
if (isset($_SESSION['userid'])) {
  header("Location:view-account.php");
  exit();
}

//get data from post
$user = $_POST['username'] ?? "";
$email = $_POST['email'] ?? "";
$password1 = $_POST['password'] ?? "";
$password2 = $_POST['password2'] ?? "";
$errors = array();

//when form has been submitted
if (isset($_POST['submit'])) {

  //make sure name isn't empty
  if (empty($user)) {
    $errors['username'] = true;
  }
  //verify email is valid
  if (empty(filter_var($email, FILTER_VALIDATE_EMAIL))) {
    $errors['email'] = true;
  }

  //check password strength
  if (strlen($password1) < 10) {
    $errors['p_strength'] = true;
  }

  //check if passwords match
  if ($password1 !== $password2) {
    $errors['p_match'] = true;
  }

  if (empty($errors)) {

    require_once './includes/library.php';
    $pdo = connectdb(); //create database connection

    // hashing the password
    $hashedPassword = password_hash($password1, PASSWORD_DEFAULT);

    // generating an api_key
    $api_key = hash('sha256', $user . $email . uniqid());

    $checkQuery = "SELECT username FROM users WHERE username = ?";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute([$user]);
    $existingUser = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$existingUser) {
      //create query
      $insert = "INSERT INTO
            `users` (username, email, pwd, api_key)
            VALUES (?, ?, ?, ?)";
      $insert = $pdo->prepare($insert);
      $success = $insert->execute([
        $user,
        $email,
        $hashedPassword,
        $api_key
      ]);

      if ($success) {
        $_SESSION['userid'] = $pdo->lastInsertId();
        $_SESSION['username'] = $user;

        //redirect user to view-account
        header("Location: view-account.php");
        exit();
      } else {
        $errors['db'] = "Something went wrong while creating your account.";
      }
    } else {
      $errors['existingUN'] = true;
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account</title>
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
      <h2>Create Account</h2>
      <form id="create-account" method="post" action="">

        <div class="form-item col">
          <label for="username">Username:</label>
          <div class="box">
            <input type="text" id="username" name="username" size="25" value="<?= $user ?>" placeholder="Type your username" />
            <i class="fa-solid fa-user" for="username"></i>
          </div>
          <span class="error <?= !isset($errors['username']) ? 'hidden' : '' ?>">Your username cannot be empty</span>
          <span class="error <?= !isset($errors['existingUN']) ? 'hidden' : '' ?>">Your username already exists</span>
        </div>

        <div class="form-item col">
          <label for="email">Email:</label>
          <div class="box">
            <input type="text" id="email" name="email" size="25" value="<?= $email ?>" placeholder="Type your email" />
            <i class="fa-solid fa-envelope" for="email"></i>
          </div>
          <span class="error <?= !isset($errors['email']) ? 'hidden' : '' ?>">Your email was invalid</span>
        </div>
        <div class="form-item col">
          <label for="password">Password:</label>
          <div class="box">
            <input type="password" id="password" name="password" size="25" placeholder="Type your password" />
            <i class="fa-solid fa-lock" for="password"></i>
          </div>
          <span class="error <?= !isset($errors['p_strength']) ? 'hidden' : '' ?>">Your passwords was not strong enough</span>
        </div>
        <div class="form-item col">
          <label for="password2">Verify Password:</label>
          <div class="box">
            <input type="password" id="password2" name="password2" size="25" placeholder="Type your password again" />
            <i class="fa-solid fa-lock" for="password2"></i>
          </div>
          <span class="error <?= !isset($errors['p_match']) ? 'hidden' : '' ?>">Your passwords do not match</span>
        </div>

        <button id="submit" name="submit">Create Account</button><br>
        <a href="login.php" class="have_Account">Already Have An Account</a>
      </form>
    </section>
    </div>
  </main>

  <footer>&copy; COIS 3430, Inc. 2024 &mdash; Built by Ahmed Rashed</footer>


</body>

</html>