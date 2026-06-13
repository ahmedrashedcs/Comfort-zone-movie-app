<?php
session_start();

// only if the user is logged in they will have the userid set in the DB
// if it is not set it will directly take the user to the login page
if (!isset($_SESSION['userid'])) {
    header("Location:login.php");
    exit();
}

require_once './includes/library.php';
$pdo = connectdb();

$query = "SELECT userID, email, api_key FROM users WHERE username = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$_SESSION['username']]);
$dbrow = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dbrow) {
    die("User not found in database.");
}

$api_key = $dbrow['api_key'] ?? "";

//when form has been submitted we make new api and Update DB
if (isset($_POST['submit'])) {
    $api_key = hash('sha256', $_SESSION['username'].$dbrow['email'].uniqid());

    $updateQuery = "UPDATE users SET api_key = ? WHERE username = ?";
    $stmtU = $pdo->prepare($updateQuery);
    $stmtU->execute([$api_key, $_SESSION['username']]);
}

if (isset($_POST['centered'])) {
    session_destroy();
    header("Location:login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>view account</title>
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
            <h2>Welcome <?= $_SESSION['username']?>!</h2>
            <form id="view-account" method="post">

                <div class="form-item col">
                    <label for="API">Here is your API key</label><br>
                    <div class="box">
                        <input type="text" name="API" id="API" value="<?= $api_key ?>" disabled>
                        <i class="fa-solid fa-key" for="API"></i>
                    </div>
                </div>

                <button id="submit" name="submit">Regenerate</button><br>
                <button id="centered" name="centered" class="centered">Logout</button>
            </form>
        </section>
    </main>
    <footer>&copy; COIS 3430, Inc. 2024 &mdash; Built by Ahmed Rashed</footer>

</body>

</html>