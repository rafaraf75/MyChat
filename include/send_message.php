<?php
include("connection.php");

// Sprawdzenie połączenia z bazą danych
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Włączenie raportowania błędów
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sprawdzenie, czy dane są wysyłane
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    echo "POST request received<br>";
    print_r($_POST); // Wyświetlenie przesłanych danych

    if (isset($_POST['logged_in_user']) && isset($_POST['chat_with_user']) && isset($_POST['msg_content'])) {
        $logged_in_user = mysqli_real_escape_string($con, $_POST['logged_in_user']);
        $chat_with_user = mysqli_real_escape_string($con, $_POST['chat_with_user']);
        $msg_content = mysqli_real_escape_string($con, $_POST['msg_content']);

        $query = "INSERT INTO messages (sender_username, receiver_username, msg_content, msg_status, msg_date)
                  VALUES ('$logged_in_user', '$chat_with_user', '$msg_content', 'unread', NOW())";

        if (mysqli_query($con, $query)) {
            echo "Message sent";
        } else {
            echo "SQL Error: " . mysqli_error($con);
        }
    } else {
        echo "Missing required fields!";
    }
} else {
    echo "Invalid request method";
}
?>
