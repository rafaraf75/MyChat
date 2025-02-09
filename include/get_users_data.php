<?php
// Sprawdzenie, czy sesja jest aktywna, jeśli nie, to jej rozpoczęcie
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include("connection.php");

// Pobranie emaila zalogowanego użytkownika z sesji
$user_email = $_SESSION['user_email'];

// Pobranie ID użytkownika na podstawie emaila
$get_user_id = $con->prepare("SELECT id FROM users WHERE user_email = ?");
$get_user_id->bind_param("s", $user_email);
$get_user_id->execute();
$get_user_id->bind_result($user_id);
$get_user_id->fetch();
$get_user_id->close();

// Pobranie znajomych zalogowanego użytkownika
$get_users = "SELECT users.* FROM users
              JOIN contacts ON users.id = contacts.friend_id
              WHERE contacts.user_id = ?";
// Przygotowanie i wykonanie zapytania SQL
$stmt = $con->prepare($get_users);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
// Przetworzenie wyników zapytania i generowanie HTML dla listy znajomych
while ($row_user = $result->fetch_assoc()) {
    $friend_id = $row_user['id'];
    $user_name = $row_user['user_name'];
    $user_profile = $row_user['profile_pic'];
    $user_status = $row_user['log_in'];

    // Jeśli użytkownik aktualnie rozmawia z danym znajomym, dodaj klasę `active-chat`
    $active_class = (isset($_GET['chat_with']) && $_GET['chat_with'] == $user_name) ? 'active-chat' : '';

    // Generowanie kodu HTML dla pojedynczego znajomego w liście
    echo "
        <li class='$active_class'>
            <div class='chat-left-img'>
                <img src='$user_profile' alt='$user_name'>
            </div>
            <div class='chat-left-detail'>
                <p><a href='home.php?chat_with=$user_name'>$user_name</a></p>";
    // Wyświetlenie statusu użytkownika (Online lub Offline)
    if ($user_status == 'Online') {
        echo "<span><i class='fa fa-circle' aria-hidden='true'></i> Online</span>";
    } else {
        echo "<span><i class='fa fa-circle' style='color: gray;' aria-hidden='true'></i> Offline</span>";
    }
    echo "</div></li>";
}
// Zamknięcie zapytania SQL
$stmt->close();
?>
