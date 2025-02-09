<!DOCTYPE html>
<?php
session_start();
include("include/connection.php");

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['user_email'])) {
    header("location: signin.php");
    exit();
}

// Pobranie danych użytkownika z bazy
$user = $_SESSION['user_email'];
$get_user = "SELECT id, user_name, user_email, user_gender, user_pass FROM users WHERE user_email=?";
$user_stmt = $con->prepare($get_user);
$user_stmt->bind_param("s", $user);
$user_stmt->execute();
$user_stmt->bind_result($user_id, $user_name, $user_email, $user_gender, $user_pass);
$user_stmt->fetch();
$user_stmt->close();

// Funkcja do zapisu logów (przechowuje akcje użytkownika, np. zmianę hasła)
function addLog($con, $user_email, $action) {
    $log_sql = "INSERT INTO logs (user_email, action, timestamp) VALUES (?, ?, NOW())";
    $log_stmt = $con->prepare($log_sql);
    $log_stmt->bind_param("ss", $user_email, $action);
    $log_stmt->execute();
}

// Aktualizacja profilu użytkownika
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_profile"])) {
    $new_name = $_POST["u_name"];
    $new_email = $_POST["u_email"];
    $new_gender = $_POST["u_gender"];

    // Aktualizacja `users`
    $update_sql = "UPDATE users SET user_name=?, user_email=?, user_gender=? WHERE id=?";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->bind_param("sssi", $new_name, $new_email, $new_gender, $user_id);

    if ($update_stmt->execute()) {
        // Zapis ustawień użytkownika do tabeli `settings`
        $settings_sql = "INSERT INTO settings (user_email, setting_name, notification_pref)
                VALUES (?, 'email', ?), (?, 'gender', ?)
                ON DUPLICATE KEY UPDATE notification_pref = VALUES(notification_pref)";
        $settings_stmt = $con->prepare($settings_sql);
        $settings_stmt->bind_param("ssss", $new_email, $new_email,  $new_email, $new_gender);
        $settings_stmt->execute();


        // Zapis do `logs`
        addLog($con, $new_email, "Updated profile settings(Email: $new_email, Gender: $new_gender)");
        // Powiadomienie o sukcesie i przekierowanie
        echo "<script>
                alert('Profile updated successfully!');
                window.location.href = 'account_setting.php';
              </script>";
        exit();
    } else {
        echo "<script>alert('Update error. Please try again.');</script>";
    }
}

// Zmiana hasła + zapis do logs
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_password"])) {
    $old_password = $_POST["old_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];
    // Weryfikacja starego hasła
    if (!password_verify($old_password, $user_pass)) {
        echo "<script>alert('The old password is incorrect!');</script>";
    } elseif ($new_password !== $confirm_password) {
        echo "<script>alert('The new password and confirmation are not the same!');</script>";
    } else {
        // Szyfrowanie nowego hasła
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET user_pass=? WHERE id=?";
        $update_stmt = $con->prepare($update_sql);
        $update_stmt->bind_param("si", $hashed_new_password, $user_id);
        $update_stmt->execute();
        // Zapis do `logs`
        addLog($con, $user_email, "Changed password");
        // Powiadomienie o sukcesie i przekierowanie
        echo "<script>
            alert('Password changed successfully!');
            window.location.href = 'home.php';
        </script>";
        exit();
    }
}
?>

<html>
<head>
    <title>Account Settings</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/setting.css">
</head>
<body>
    <!-- Nawigacja -->
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
        <div class="container">
            <div class="d-flex justify-content-center w-100">
                <a href="home.php?user_name=<?php echo $user_name; ?>" class="btn btn-primary">Chat</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
                <!-- Lewa kolumna -->
            <div class="col-md-2 bg-secondary text-white d-none d-md-block left-column">
                <h5 class="text-center mt-3">My Chat</h5>
            </div>

                <!-- Główna sekcja -->
            <div class="col-md-8 settings-container">
                <h2 class="text-center">Change Account Settings</h2>
                 <!-- Formularz zmiany danych profilu -->
                <form action="" method="post">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <td><b>Change Your Username</b></td>
                            <td><input type="text" name="u_name" class="form-control" required value="<?php echo $user_name; ?>"></td>
                        </tr>
                        <tr>
                            <td><b>Change Your Email</b></td>
                            <td><input type="email" name="u_email" class="form-control" required value="<?php echo $user_email; ?>"></td>
                        </tr>
                        <tr>
                            <td><b>Gender</b></td>
                            <td>
                                <select class="form-control" name="u_gender">
                                    <option><?php echo $user_gender; ?></option>
                                    <option>Male</option>
                                    <option>Female</option>
                                    <option>Others</option>
                                </select>
                            </td>
                        </tr>
                        <tr align="center">
                            <td colspan="2">
                                <button type="submit" name="update_profile" class="btn btn-info">Update Profile</button>
                            </td>
                        </tr>
                    </table>
                </form>
                <!-- Formularz zmiany hasła -->
                <form action="" method="post">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <td><b>Change Password</b></td>
                            <td>
                                <input type="password" name="old_password" class="form-control" required placeholder="Enter old password">
                                <input type="password" name="new_password" class="form-control mt-2" required placeholder="Enter new password">
                                <input type="password" name="confirm_password" class="form-control mt-2" required placeholder="Confirm new password">
                                <button type="submit" name="change_password" class="btn btn-warning mt-2">Change Password</button>
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
