<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include("connection.php");

if (!isset($_SESSION['user_email'])) {
    header("location: signin.php");
    exit();
}

if (isset($_GET['friend_email'])) {
    $user_email = $_SESSION['user_email'];
    $friend_email = $_GET['friend_email'];

    // Pobranie ID użytkownika
    $get_user_id = $con->prepare("SELECT id FROM users WHERE user_email=?");
    $get_user_id->bind_param("s", $user_email);
    $get_user_id->execute();
    $get_user_id->bind_result($user_id);
    $get_user_id->fetch();
    $get_user_id->close();

    // Pobranie ID znajomego
    $get_friend_id = $con->prepare("SELECT id FROM users WHERE user_email=?");
    $get_friend_id->bind_param("s", $friend_email);
    $get_friend_id->execute();
    $get_friend_id->bind_result($friend_id);
    $get_friend_id->fetch();
    $get_friend_id->close();

    if (!$user_id || !$friend_id) {
        echo "<script>alert('User not found!'); window.location.href='../home.php';</script>";
        exit();
    }

    // Nie pozwala dodać samego siebie jako znajomego!
    if ($user_id === $friend_id) {
        echo "<script>alert('You cannot add yourself as a friend!'); window.location.href='../home.php';</script>";
        exit();
    }

    // Sprawdzenie, czy znajomość już istnieje
    $check_friend = $con->prepare("SELECT * FROM contacts WHERE user_id=? AND friend_id=?");
    $check_friend->bind_param("ii", $user_id, $friend_id);
    $check_friend->execute();
    $result = $check_friend->get_result();
    $check_friend->close();

    if ($result->num_rows > 0) {
        echo "<script>alert('This user is already your friend!'); window.location.href='../home.php';</script>";
        exit();
    }

    // Dodanie znajomego
    $insert_friend = $con->prepare("INSERT INTO contacts (user_id, friend_id) VALUES (?, ?)");
    $insert_friend->bind_param("ii", $user_id, $friend_id);
    $insert_friend->execute();
    $insert_friend->close();

    echo "<script>alert('Friend added successfully!'); window.location.href='../home.php';</script>";
    exit();
}
?>
