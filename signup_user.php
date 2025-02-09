<?php
include("include/connection.php");

if (isset($_POST['sign_up'])) {
    // Pobieranie i zabezpieczanie danych z formularza
    $name = htmlentities(mysqli_real_escape_string($con, $_POST['user_name']));
    $pass = htmlentities(mysqli_real_escape_string($con, $_POST['user_pass']));
    $email = htmlentities(mysqli_real_escape_string($con, $_POST['user_email']));
    $gender = htmlentities(mysqli_real_escape_string($con, $_POST['user_gender']));
    $rand = rand(1, 6);

    // Walidacja danych
    if ($name == '') {
        echo "<script>alert('We cannot verify your name');</script>";
        exit();
    }
    // Sprawdzenie, czy hasło ma co najmniej 8 znaków
    if (strlen($pass) < 8) {
        echo "<script>alert('Password should be minimum 8 characters!');</script>";
        exit();
    }

    // Sprawdzenie, czy e-mail istnieje już w bazie danych
    $check_email = "SELECT * FROM users WHERE user_email='$email'";
    $run_email = mysqli_query($con, $check_email);
    $check = mysqli_num_rows($run_email);

    if ($check == 1) {
        echo "<script>alert('Email already exists');</script>";
        echo "<script>window.open('signup.php', '_self')</script>";
        exit();
    }

    // Wybór zdjęcia profilowego
    switch ($rand) {
        case 1:
            $profile_pic = "images/prof1.png";
            break;
        case 2:
            $profile_pic = "images/prof2.png";
            break;
        case 3:
            $profile_pic = "images/prof3.png";
            break;
        case 4:
            $profile_pic = "images/prof4.png";
            break;
        case 5:
            $profile_pic = "images/prof5.png";
            break;
        default:
            $profile_pic = "images/prof6.png";
    }

    // Szyfrowanie hasła
    $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);

    // Wstawianie danych użytkownika do tabeli
    $insert = "INSERT INTO users (user_name, user_pass, user_email, profile_pic, user_gender)
               VALUES ('$name', '$hashed_pass', '$email', '$profile_pic', '$gender')";

    $query = mysqli_query($con, $insert);

    // Sprawdzanie wyniku zapytania
    if ($query) {
        echo "<script>alert('Congratulations $name, your account has been created successfully');</script>";
        echo "<script>window.open('signin.php', '_self')</script>";
    } else {
        echo "<script>alert('Registration failed. Please try again.');</script>";
        echo "<script>window.open('signup.php', '_self')</script>";
    }
}
?>
