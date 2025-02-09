<?php
session_start();
include("find_friends_function.php");
// Sprawdzenie, czy użytkownik jest zalogowany, jeśli nie - przekierowanie na stronę logowania
if (!isset($_SESSION['user_email'])) {
    header("location: signin.php");
    exit();
}
else { ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search For Friends</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/find_people.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark justify-content-center">
        <?php
        $user = $_SESSION['user_email'];
        $get_user = "SELECT * FROM users WHERE user_email='$user'";
        $run_user = mysqli_query($con, $get_user);
        $row = mysqli_fetch_array($run_user);

        $user_name = $row['user_name'];
        echo "<a href='../home.php?user_name=$user_name' class='btn btn-primary mx-2'>Chat</a>";
        ?>
        <a href="../account_setting.php" class="btn btn-secondary mx-2">Settings</a>
    </nav><br>

    <!-- Główna zawartość z kolumnami -->
    <div class="container-fluid">
        <div class="row">
            <!-- Lewa kolumna -->
            <div class="col-md-2 bg-secondary text-white d-none d-md-block left-column">
                <h5 class="text-center mt-3">My Chat</h5>
            </div>

            <!-- Główna kolumna -->
            <div class="col-md-8 content-column">
                <!-- Wyszukiwarka -->
                <div class="row">
                    <div class="col-md-12">
                        <form class="search_form" action="">
                            <input type="text" name="search_query" placeholder="Search Friends" autocomplete="off" required>
                            <button class="btn btn-primary" type="submit" name="search_btn">Search</button>
                        </form>
                    </div>
                </div><br><br>
                <!-- Wyniki wyszukiwania -->
                <div class="container">
                    <?php search_user(); ?>
                </div>
            </div>

            <!-- Prawa kolumna -->
            <div class="col-md-2 bg-secondary text-white d-none d-md-block right-column">
                <h5 class="text-center mt-3">My Chat</h5>
            </div>
        </div>
    </div>
</body>
</html>
<?php } ?>
