<?php
session_start();

//when form has been submitted we make new api and Update DB
if (isset($_POST['submitL'])) {
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
            <h2>A brief about my API</h2>
            <form id="view-account" method="post">
                <p>
                    Our Movie Watchlist API helps you keep track of the movies you want to watch and the ones you've already seen — all in one place! Whether you're planning your next movie night or reviewing your favorites, this tool makes it easy to:
                </p>
                <ul>
                    <li>Save movies to your personal “To Watch” list</li>

                    <li>Mark movies as completed and rate them after watching</li>

                    <li>See your average ratings and how much time you've spent watching</li>

                    <li>Track how many times you’ve rewatched a movie</li>

                    <li>Securely log in with your own API key to access your personalized watchlists</li>
                </ul>

                <p>Everything is handled smoothly through a simple and secure system — giving you full control over your movie journey. </p>

                <button id="submitL" name="submitL">Login</button><br>
            </form>
        </section>
    </main>
    <footer>&copy; COIS 3430, Inc. 2024 &mdash; Built by Ahmed Rashed</footer>

</body>

</html>