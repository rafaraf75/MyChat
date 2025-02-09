<?php
session_start();
include("include/connection.php");

if (isset($_POST['sign_in'])) {
    // Pobranie i walidacja danych wejściowych
    $email = trim($_POST['email']);
    $pass = trim($_POST['pass']);
    // Sprawdzenie, czy pola nie są puste
    if (empty($email) || empty($pass)) {
        echo "
        <div class='alert alert-danger'>
            <strong>Please fill in all fields!</strong>
        </div>";
        exit();
    }
    // Przygotowane zapytanie SQL dla bezpieczeństwa
    $stmt = $con->prepare("SELECT * FROM users WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    // Jeśli użytkownik istnieje w bazie danych
    if ($row) {
        // Sprawdzenie, czy podane hasło jest zgodne z zapisanym w bazie (hashowane)
        if (password_verify($pass, $row['user_pass'])) {
            // Ustawienie zmiennych sesji użytkownika po zalogowaniu
            $_SESSION['user_email'] = $row['user_email'];
            $_SESSION['user_name'] = $row['user_name'];
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_profile'] = $row['profile_pic'];
            // Aktualizacja statusu użytkownika na "Online"
            $update_msg = $con->prepare("UPDATE users SET log_in = 'Online' WHERE user_email = ?");
            $update_msg->bind_param("s", $email);
            $update_msg->execute();

            // Przekierowanie na stronę główną
            echo "<script>window.open('home.php', '_self');</script>";
        } else {
            // Komunikat o błędnym haśle
            echo "
            <div class='alert alert-danger'>
                <strong>Invalid Password!</strong>
            </div>";
        }
    } else {
        // Komunikat o nieistniejącym użytkowniku
        echo "
        <div class='alert alert-danger'>
            <strong>User does not exist!</strong>
        </div>";
    }
}
?>
