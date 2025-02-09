<?php
session_start();
include("../include/connection.php");

// Dołączenie biblioteki PHPMailer do obsługi wysyłania e-maili
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Sprawdzenie, czy formularz został przesłany metodą POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reset_password"])) {
    $email = $_POST["email"];

    // Sprawdzenie czy email istnieje w bazie
    $sql = "SELECT * FROM users WHERE user_email = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Generowanie nowego hasła
        $new_password = substr(md5(time()), 0, 8);
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Aktualizacja hasła w bazie
        $update_sql = "UPDATE users SET user_pass = ? WHERE user_email = ?";
        $update_stmt = $con->prepare($update_sql);
        $update_stmt->bind_param("ss", $hashed_password, $email);
        $update_stmt->execute();

        // Pobieranie danych SMTP z bazy danych
        $smtp_query = "SELECT email, password FROM email_config LIMIT 1";
        $smtp_result = mysqli_query($con, $smtp_query);
        $smtp_data = mysqli_fetch_assoc($smtp_result);

        // Sprawdzenie czy pobrano dane SMTP
        if (!$smtp_data) {
            echo "<script>alert('Błąd: Brak konfiguracji SMTP w bazie.');</script>";
            exit();
        }

        // Wysyłka maila z nowym hasłem
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $smtp_data['email']; // Pobranie maila z bazy
            $mail->Password = $smtp_data['password']; // Pobranie hasła z bazy
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Adres nadawcy
            $mail->setFrom($smtp_data['email'], 'MyChat Support');
            // Adres odbiorcy
            $mail->addAddress($email);

            // Treść maila
            $mail->isHTML(true);
            $mail->Subject = 'Resetowanie hasla';
            $mail->Body = "Twoje nowe hasło: <b>$new_password</b><br>Proszę zalogować się i zmienić je na własne.";

            // Wysłanie wiadomości e-mail
            $mail->send();

            // Przekierowanie do strony logowania
            echo "<script>
                alert('A new password has been sent to your e-mail.');
                window.location.href = '../signin.php';
            </script>";
            exit();
        } catch (Exception $e) {
            // Obsługa błędów wysyłania maila
            echo "<script>
                alert('Błąd wysyłania maila: " . $mail->ErrorInfo . "');
                window.location.href = '../signin.php';
            </script>";
            exit();
        }
    } else {
        // Jeśli e-mail nie został znaleziony w bazie
        echo "<script>
            alert('No user found with this email.');
            window.location.href = '../signin.php';
            </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/reset.css">
</head>
<body>
    <!-- Pasek nawigacyjny -->
<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
        <div class="container">
            <div class="d-flex justify-content-center w-100">

            </div>
        </div>
    </nav>
    <!-- Struktura strony -->
    <div class="container-fluid">
        <div class="row">
            <!-- Lewa kolumna -->
            <div class="col-md-2 bg-secondary text-white d-none d-md-block left-column">
                <h5 class="text-center mt-3">My Chat</h5>
            </div>
            <!-- Główna sekcja resetowania hasła -->
            <div class="col-md-8 settings-container">
                <h2 class="text-center">Reset Your Password</h2>
                <form action="" method="post">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <td><b>Enter Your Email:</b></td>
                            <td>
                                <input type="email" name="email" class="form-control" required>
                            </td>
                        </tr>
                        <tr align="center">
                            <td colspan="2">
                                <button type="submit" name="reset_password" class="btn btn-danger">Send Reset Link</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <!-- Prawa kolumna -->
            <div class="col-md-2 bg-secondary text-white d-none d-md-block right-column">
                <h5 class="text-center mt-3">My Chat</h5>
            </div>
        </div>
    </div>
</body>
</html>
