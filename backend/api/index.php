<?php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-API-KEY');
    http_response_code(200);
    exit();
}

$uri = $_SERVER['REQUEST_URI'];

$uri = parse_url($_SERVER['REQUEST_URI']);

define('__BASE__', '/~ahmedrashed/3430/assn/assn2-AhmedRashed2004-1/api/');
$endpoint = str_replace(__BASE__, "", $uri["path"]);

$method = $_SERVER['REQUEST_METHOD'];

require_once('../includes/library.php');
$pdo = connectdb();

if ($method == 'GET') {

    if ($endpoint == 'movies') {
        // should return all movies, but not all data.
        $per_page = 21;
        $page_start = 1;

        $sort = $_GET['sort'] ?? 'Popular';
        $genre = $_GET['genre'] ?? null;
        $search = $_GET['search'] ?? null;

        switch ($sort) {
            case 'Top Rated':
                $sortBy = 'vote_average DESC';
                break;
            case 'Popular':
                $sortBy = 'vote_count DESC';
                break;
            case 'New':
                $sortBy = 'release_date DESC';
                break;
            case 'Old':
                $sortBy = 'release_date ASC';
                break;
            default:
                $sortBy = 'vote_count DESC';
                break;
        }

        if (isset($_GET['page'])) {
            $page_start = (int)$_GET['page'];
        }

        $offset = ($page_start - 1) * $per_page;

        $sql = "SELECT m.movieID, m.title, m.poster, m.vote_average, m.vote_count, m.release_date, GROUP_CONCAT(g.name) AS genres FROM movies m LEFT JOIN movie_genres mg ON m.movieID = mg.movieID LEFT JOIN genres g ON mg.genreID = g.genreID ";

        $parameters = [];

        $whereAdded = false;

        if ($genre) {
            $sql .= "WHERE g.name = ?";
            array_push($parameters, $genre);
            $whereAdded = true;
        }

        if ($search) {
            if ($whereAdded) {
                $sql .= " AND m.title LIKE ?";
            } else {
                $sql .= "WHERE m.title LIKE ?";
            }
            array_push($parameters, '%' . $search . '%');
        }

        array_push($parameters, $per_page, $offset);

        $sql .= " GROUP BY m.movieID ORDER BY $sortBy LIMIT ? OFFSET ?";

        $movies = query($sql, $parameters);

        // returning the data
        json_response(200, $movies);
    } elseif (preg_match('/^movies\/(\d+)$/', $endpoint, $matches)) {

        //returns the all columns of movie data for a specific movie.

        $movieID = $matches[1];

        $movie = query("SELECT movieID, title, tagline, overview, original_language, poster, runtime, vote_average, vote_count, budget, revenue, homepage, release_date FROM movies WHERE movieID = ?", [$movieID]);

        if (!$movie) {
            json_response(404, ["error" => "Movie not found"]);
        }

        $genres = query("SELECT genres.name FROM genres JOIN movie_genres ON genres.genreID = movie_genres.genreID WHERE movie_genres.movieID = ?", [$movieID]);

        $movie[0]['genres'] = array_column($genres, 'name');

        json_response(200, $movie[0]);
    } elseif (preg_match('/^movies\/(\d+)\/rating$/', $endpoint, $matches)) {

        // returns the rating value for a specific movie.

        $movieID = $matches[1];

        $movie = query("SELECT movieID, vote_average, vote_count FROM `movies` WHERE movieID = ?", [$movieID]);

        // returning the data
        json_response(200, $movie);
    } elseif ($endpoint == 'towatchlist/entries') {

        // requires an api key and returns all entries on the user's toWatchList

        $user =  apiCheck($_SERVER['HTTP_X_API_KEY']);

        $userWatchList = query("SELECT toWatchList.movieID, toWatchList.userID, toWatchList.priority, toWatchList.notes, movies.title, movies.overview, movies.poster, movies.vote_average, movies.release_date FROM toWatchList JOIN movies ON toWatchList.movieID = movies.movieID WHERE toWatchList.userID = ?", [$user['userID']]);

        // returning the data
        json_response(200, $userWatchList);
    } elseif ($endpoint == 'completedwatchlist/entries') {

        // requires an api key and returns all entries on the user's completedWatchList.

        $user =  apiCheck($_SERVER['HTTP_X_API_KEY']);

        $completedWatchList = query("SELECT compWatchListID,
               completedWatchList.movieID,
               completedWatchList.userID,
               completedWatchList.rating,
               completedWatchList.notes,
               completedWatchList.watch_num,
               movies.title,
               movies.overview,
               movies.poster,
               movies.vote_average,
               movies.release_date
        FROM completedWatchList
        JOIN movies ON completedWatchList.movieID = movies.movieID
        WHERE completedWatchList.userID = ?", [$user['userID']]);


        // returning the data
        json_response(200, $completedWatchList);
    } elseif (preg_match('/completedwatchlist\/entries\/(\d+)\/times-watched/', $endpoint, $matches)) {

        // requires an api key and returns the number of times the user has watched the given movie

        $user =  apiCheck($_SERVER['HTTP_X_API_KEY']);
        $movieID = $matches[1];

        $timesWatched = query("SELECT movieID, watch_num FROM completedWatchList WHERE movieID = ? AND userID = ?", [$movieID, $user['userID']]);

        // returning the data
        json_response(200, $timesWatched);
    } elseif (preg_match('/completedwatchlist\/entries\/(\d+)\/rating/', $endpoint, $matches)) {

        // requires an api key and returns the user's rating for this specific movie

        $user =  apiCheck($_SERVER['HTTP_X_API_KEY']);
        $movieID = $matches[1];

        $rating = query("SELECT movieID, rating FROM completedWatchList WHERE movieID = ? AND userID = ?", [$movieID, $user['userID']]);

        // returning the data
        json_response(200, $rating);
    } elseif (preg_match('/users\/(\d+)\/stats/', $endpoint, $matches)) {

        // returns basic watching stats for the provided user. You can chose the stats, but you should have at least 4. e.g. total time watched, average score, planned time to watch, etc.

        $userID = $matches[1];

        // Total Time Watched
        $totalTimeWatched = query("SELECT SUM(movies.runtime * completedWatchList.watch_num) AS totalTimeWatched FROM completedWatchList JOIN movies ON completedWatchList.movieID = movies.movieID WHERE completedWatchList.userID = ?", [$userID]);

        $totalTimeWatchedValue = $totalTimeWatched[0]['totalTimeWatched'] ?? 0;

        // Average Score
        $ratings = query("SELECT rating FROM completedWatchList WHERE userID = ?", [$userID]);

        $rateSum = 0;
        $validCount = 0;

        foreach ($ratings as $rate) {
            if ((int)$rate['rating'] > 0) {
                $rateSum += $rate['rating'];
                $validCount++;
            }
        }

        $avgRate = $validCount > 0 ? $rateSum / $validCount : 0;

        // Planned Time

        $planedTimeQ = query("SELECT movies.runtime AS runTime FROM toWatchList JOIN movies ON toWatchList.movieID = movies.movieID WHERE toWatchList.userID = ?", [$userID]);

        $planedTime = array_sum(array_column($planedTimeQ, 'runTime'));

        // Last Watched Movie
        // I am just getting the lattest date from the last_watch Column in the Completed watchlist to check when was the last completed movie the user watched
        $lastWatchedTime = query("SELECT MAX(last_watch) AS lastWatchedMovie FROM completedWatchList WHERE userID = ?", [$userID]);

        $lastWatchedTimeValue = $lastWatchedTime[0]['lastWatchedMovie'] ?? 0;

        // returning the data
        json_response(200, array("totalTimeWatched" => $totalTimeWatchedValue, "averageScore" => $avgRate, "plannedTime" => $planedTime, "lastWatchedMovie" => $lastWatchedTimeValue));
    } else {
        // Invalid endpoint error
        json_response(404, array("error" => "Invalid endpoint for the given method"));
    }
} elseif ($method == 'POST') {

    if ($endpoint == 'towatchlist/entries') {

        //requires an api key and all other data necessary for the toWatchList table, validates then inserts the data.

        $user =  apiCheck($_SERVER['HTTP_X_API_KEY']);
        parse_str(file_get_contents('php://input'), $requestData);
        $requestData = json_decode(file_get_contents('php://input'), true);
        $movieID = $requestData['movieID'] ?? 0;
        $userID = $user['userID'] ?? 0;
        $priority = $requestData['priority'] ?? 0;
        $notes = $requestData['notes'] ?? "";

        query("INSERT INTO `toWatchList`(`movieID`, `userID`, `priority`, `notes`) VALUES (? ,? ,? ,? )", [$movieID, $userID, $priority, $notes]);

        // success code with a message
        json_response(200, array("message" => "Insert successful"));
    } elseif ($endpoint == 'completedwatchlist/entries') {

        // requires an api key and all other data necessary for the completedWatchList table, validates then inserts the data. It should also recompute and update the rating for the appropriate movie.

        // here we are inserting new data for a recently completed movie
        // first get the request data and check on the sent api key
        // we will need to validate all the requestData sent 
        // we will need to do some calculation before inserting the rating
        // then we insert all the data we have

        $user =  apiCheck($_SERVER['HTTP_X_API_KEY']);
        parse_str(file_get_contents('php://input'), $requestData);
        $requestData = json_decode(file_get_contents('php://input'), true);
        $movieID = $requestData['movieID'] ?? 0;
        $userID = $user['userID'] ?? 0;
        $nRating = $requestData['rating'] ?? 0;
        $notes = $requestData['notes'] ?? "";

        $existing = query("SELECT * FROM completedWatchList WHERE movieID = ? AND userID = ?", [$movieID, $userID]);

        if ($existing) {
            json_response(409, ["error" => "This movie is already in your completed list"]);
        }

        if (!($nRating <= 10 && $nRating >= 0)) {
            json_response(400, array("error" => "Update was not successful, enter a valid rating"));
        }

        // we send queries to fetch all the neccery data from the database to calculate our new avg

        $oldRating = $oRating[0]['rating'] ?? 0;

        if ($oldRating != 0) {
            // we fetch the old vote average and count from the database before updating it      
            $movieAvgRate = query("SELECT vote_average, vote_count FROM movies WHERE movieID = ?", [$movieID]);

            // validating and extracting the values from the array and objects
            $vote_average = $movieAvgRate[0]['vote_average'] ?? 0;
            $vote_count = $movieAvgRate[0]['vote_count'] ?? 0;

            // we have now all the values needed for calculating the new avg
            $newAvgRate = (($vote_average * $vote_count) + $nRating) / ($vote_count + 1);

            // now we insert the completedWatchList database with the the new user's rating and ->
            query("INSERT INTO `completedWatchList`(`movieID`, `userID`, `rating`, `notes`, `initial_watch`, `last_watch`, `watch_num`) VALUES (?, ?, ?, ?, NOW(), NOW(), 1)", [$movieID, $userID, $nRating, $notes]);

            // we update the movies table by Inserting the calculated vote average and incrementing Vote count
            query("UPDATE movies SET vote_average = ?, vote_count = ? WHERE movieID = ?", [$newAvgRate, ($vote_count + 1), $movieID]);

            // success code with a message
            json_response(200, array("message" => "Insert successful"));
        } else {
            //we just insert if the new rating is 0 no updating in the movie db
            query("INSERT INTO `completedWatchList`(`movieID`, `userID`, `rating`, `notes`, `initial_watch`, `last_watch`, `watch_num`) VALUES (?, ?, ?, ?, NOW(), NOW(), 1)", [$movieID, $userID, $nRating, $notes]);

            // success code with a message
            json_response(200, array("message" => "Insert successful"));
        }
    } elseif ($endpoint == 'users/session') {

        // accepts a username and password, verifies these credentials and returns the corresponding API key.

        parse_str(file_get_contents('php://input'), $requestData);
        $requestData = json_decode(file_get_contents('php://input'), true);
        $username = $requestData['username'] ?? "";
        $password = $requestData['password'] ?? "";
        // this is an SQL to fetch the userID, pwd and api key from the users table where the username matches the recived username
        $dbrow = query("SELECT userID, pwd, api_key FROM users WHERE username = ? LIMIT 1", [$username]);

        // validating and extracting the values from the array and objects
        $passwordDB = $dbrow[0]['pwd'] ?? "";

        if ($dbrow) {
            if (password_verify($password, $passwordDB)) {

                // sends the api key if the pass was correct
                json_response(200, ["api_key" => $dbrow[0]['api_key'], "userID" => $dbrow[0]['userID']]);
            } else {
                // sends an error otherwise
                json_response(401, array("error" => "Invalid Password"));
            }
        } else {
            // this means that the username is incorrect
            json_response(401, array("error" => "Invalid Username"));
        }
    } else {
        // Invalid endpoint error
        json_response(404, array("error" => "Invalid endpoint for the given method"));
    }
} elseif ($method == 'PATCH') {

    // requires an api key and new priority and updates the user's priority for the appropriate movie.

    if (preg_match('/towatchlist\/entries\/(\d+)\/priority/', $endpoint, $matches)) {
        parse_str(file_get_contents('php://input'), $requestData);
        $requestData = json_decode(file_get_contents('php://input'), true);
        $user =  apiCheck($_SERVER['HTTP_X_API_KEY']);
        $movieID = $matches[1];
        $priority = $requestData['priority'] ?? 1;

        query("UPDATE `toWatchList` SET `priority`= ? WHERE movieID = ? AND userID = ?", [$priority, $movieID, $user['userID']]);

        // success code with a message
        json_response(200, array("message" => "Priority update was successful"));
    } elseif (preg_match('/completedwatchlist\/entries\/(\d+)\/rating/', $endpoint, $matches)) {

        // requires an api key and new rating and updates the rating for the appropriate movie in the completedWatchList table, then recalculates the movie's rating and updates the movies table.

        // we are updating an existing rating for movie the user completed
        // steps will be as follows:
        // first get the request data and check on the sent api key
        // if the api key exists the function will send back the userID as well as the API key in an array
        //we validate all the variables
        parse_str(file_get_contents('php://input'), $requestData);
        $requestData = json_decode(file_get_contents('php://input'), true);
        $user =  apiCheck($_SERVER['HTTP_X_API_KEY']);
        $movieID = $matches[1];
        $nRating = $requestData['rating'] ?? 0;

        if (!($nRating <= 10 && $nRating >= 0)) {
            json_response(400, array("error" => "Update was not successful, enter a valid rating"));
        }

        // we send queries to fetch all the neccery data from the database to calculate our new avg
        $oRating = query("SELECT rating FROM completedWatchList WHERE movieID = ? AND userID = ?", [$movieID, $user['userID']]);

        $oldRating = $oRating[0]['rating'] ?? 0;

        if ($oldRating != 0) {
            // here we are getting the old user's rating before updating it

            // then we fetch the old vote average and count from the database before updating it
            $movieAvgRate = query("SELECT vote_average, vote_count FROM movies WHERE movieID = ?", [$movieID]);

            // validating and extracting the values from the array and objects
            $oRatingV = $oRating[0]['rating'] ?? 0;
            $vote_average = $movieAvgRate[0]['vote_average'] ?? 0;
            $vote_count = $movieAvgRate[0]['vote_count'] ?? 0;

            // we have now all the values needed for calculating the new avg
            $newAvgRate = (($vote_average * $vote_count) - $oRatingV + $nRating) / $vote_count;

            // now we update the completedWatchList database with the the new user's rating and ->
            query("UPDATE `completedWatchList` SET `rating`= ? WHERE movieID = ? AND userID = ? ", [$nRating, $movieID, $user["userID"]]);

            // the movies database with the vote_average (vote_count will stay the same since it is only an update)
            query("UPDATE movies SET vote_average = ? WHERE movieID = ?", [$newAvgRate, $movieID]);

            // success code with a message
            json_response(200, array("message" => "Update was successful"));
        } else {
            // here we are getting the old user's rating before updating it

            // then we fetch the old vote average and count from the database before updating it
            $movieAvgRate = query("SELECT vote_average, vote_count FROM movies WHERE movieID = ?", [$movieID]);

            // validating and extracting the values from the array and objects
            $oRatingV = $oRating[0]['rating'] ?? 0;
            $vote_average = $movieAvgRate[0]['vote_average'] ?? 0;
            $vote_count = $movieAvgRate[0]['vote_count'] ?? 0;

            // we have now all the values needed for calculating the new avg
            $newAvgRate = (($vote_average * $vote_count) - $oRatingV + $nRating) / $vote_count;

            // now we update the completedWatchList database with the the new user's rating and ->
            query("UPDATE `completedWatchList` SET `rating`= ? WHERE movieID = ? AND userID = ? ", [$nRating, $movieID, $user["userID"]]);

            // we update the movies table by Inserting the calculated vote average and incrementing Vote count since his vote was not there to begin with
            query("UPDATE movies SET vote_average = ?, vote_count = ? WHERE movieID = ?", [$newAvgRate, ($vote_count + 1), $movieID]);

            // success code with a message
            json_response(200, array("message" => "Update was successful"));
        }

        json_response(400, array("error" => "Update was not successful, something went wrong"));
    } elseif (preg_match('/completedwatchlist\/entries\/(\d+)\/times-watched/', $endpoint, $matches)) {

        // requires an api key and increments the number of times watched and updates the last date watched of the appropriate movie.

        $user =  apiCheck($_SERVER['HTTP_X_API_KEY']);
        $movieID = $matches[1];

        query("UPDATE `completedWatchList` SET last_watch = NOW(), watch_num = watch_num + 1 WHERE movieID = ? AND userID = ?", [$movieID, $user['userID']]);

        // success code with a message
        json_response(200, array("message" => "Update was successful"));
    } else {
        // Invalid endpoint error
        json_response(404, array("error" => "Invalid endpoint for the given method"));
    }
} elseif ($method == 'PUT') {

    if (preg_match('/towatchlist\/entries\/(\d+)/', $endpoint, $matches)) {

        // requires an api key and all other data necessary for the toWatchList table and replaces the entire record in the database

        $user =  apiCheck($_SERVER['HTTP_X_API_KEY']);
        parse_str(file_get_contents('php://input'), $requestData);
        $requestData = json_decode(file_get_contents('php://input'), true);
        $movieID = $matches[1];
        $priority = $requestData['priority'] ?? 1;
        $notes = $requestData['notes'] ?? "";

        query("INSERT INTO `toWatchList`(`movieID`, `userID`, `priority`, `notes`) 
        VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE 
        movieID = VALUES(movieID), 
        userID = VALUES(userID), 
        priority = VALUES(priority), 
        notes = VALUES(notes)", [$movieID, $user['userID'], $priority, $notes]);

        // success code with a message
        json_response(200, array("message" => "Change Made"));
    } else {
        // Invalid endpoint error
        json_response(404, array("error" => "Invalid endpoint for the given method"));
    }
} elseif ($method == 'DELETE') {

    if (preg_match('/towatchlist\/entries\/(\d+)/', $endpoint, $matches)) {

        // requires and api key and movieID and deletes the appropriate movie from the user's watchlist.

        $user =  apiCheck($_SERVER['HTTP_X_API_KEY']);
        $movieID = $matches[1];

        query("DELETE FROM `toWatchList` WHERE userID = ? AND movieID = ?", [$user['userID'], $movieID]);

        // success code with a message
        json_response(200, array("message" => "Deleted successfully"));
    } elseif (preg_match('/completedwatchlist\/entries\/(\d+)/', $endpoint, $matches)) {

        // requires and api key and movieID and deletes the appropriate movie from the completedWatchList.

        $user =  apiCheck($_SERVER['HTTP_X_API_KEY']);
        $movieID = $matches[1];

        $entry = query("SELECT rating FROM completedWatchList WHERE userID = ? AND movieID = ?", [$user['userID'], $movieID]);

        if (empty($entry)) {
            json_response(404, array("error" => "Completed entry not found"));
        }

        $userRating = $entry[0]['rating'];

        $movieStats = query("SELECT vote_average, vote_count FROM movies WHERE movieID = ?", [$movieID]);
        $vote_average = $movieStats[0]['vote_average'] ?? 0;
        $vote_count = $movieStats[0]['vote_count'] ?? 0;

        if ($vote_count > 1) {
            $newAvg = (($vote_average * $vote_count) - $userRating) / ($vote_count - 1);
        } else {
            $newAvg = 0;
        }

        query("UPDATE movies SET vote_average = ?, vote_count = ? WHERE movieID = ?", [$newAvg, max(0, $vote_count - 1), $movieID]);

        query("DELETE FROM `completedWatchList` WHERE userID = ? AND movieID = ?", [$user['userID'], $movieID]);

        // success code with a message
        json_response(200, array("message" => "Deleted successfully"));
    } else {
        // Invalid endpoint error
        json_response(404, array("error" => "Invalid endpoint for the given method"));
    }
} else {
    // Invalid request error
    json_response(404, array("error" => "We are unable to respond to this request"));
}

// this function checks if the api key is valid by taking in the api key
//it returns an array with userID and the api_key if the api key is anywhere to be found in the data base
function apiCheck($user)
{
    // checks if the api key that was sent is set or empty
    // if it is not set or empty ->
    if (!isset($user) || empty($user)) {
        // we send a jason response error 400 with an error message
        json_response(400, array("error" => "You must provide an API key"));
    }

    // if the previos conditon was not satisfied we do the following:
    // connect to DB
    // get the first userID and API that matched the recived api
    // we prepare and execute to avoid SQL enjection
    // we fetch the userID and API and put it into a var
    $pdo = connectdb();
    $query = "SELECT userID, api_key FROM `users` WHERE `api_key` = ? limit 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user]);
    $dbrow = $stmt->fetch();

    // if dbrow is empty then the api was not found in the database and this sends an error as follows
    if (!$dbrow) {
        json_response(400, array("error" => "You must provide a valid API key"));
    }

    //if not the array is returned
    return $dbrow;
}

// this function saves alot of space and time it is:
// a query function that takes in a query and an optional array
function query($query, $executeParameters = [])
{
    // it connects to DB
    // plugs in the recived query
    // prepares and execute (the array could have variables or coud be empty) to avoid SQL enjection
    // it fetch all the returned data according to the SQL and put it into a var
    $pdo = connectdb();

    $stmt = $pdo->prepare($query);
    $stmt->execute($executeParameters);
    $result = $stmt->fetchall(PDO::FETCH_ASSOC);

    // then returns the array
    return $result;
}


function json_response($status, $data)
{
    $jsonString = json_encode($data);
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Content-Length: ' . strlen($jsonString));
    http_response_code($status);
    echo $jsonString;
    exit();
}
