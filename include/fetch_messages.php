<?php
include("connection.php");

// Sprawdzenie, czy zostały przekazane dane zalogowanego użytkownika i rozmówcy
if (isset($_POST['logged_in_user']) && isset($_POST['chat_with_user'])) {

    // Pobranie i zabezpieczenie danych wejściowych przed atakami SQL Injection
    $logged_in_user = mysqli_real_escape_string($con, $_POST['logged_in_user']);
    $chat_with_user = mysqli_real_escape_string($con, $_POST['chat_with_user']);

    // Oznacz wiadomości jako przeczytane, gdy użytkownik je odbiera
    $update_status_query = "UPDATE messages
                            SET msg_status = 'read'
                            WHERE receiver_username = '$logged_in_user'
                            AND sender_username = '$chat_with_user'
                            AND msg_status = 'unread'";
    mysqli_query($con, $update_status_query);

    // Pobierz wiadomości pomiędzy zalogowanym użytkownikiem a wybranym rozmówcą
    $query = "SELECT * FROM messages
              WHERE (sender_username='$logged_in_user' AND receiver_username='$chat_with_user')
                 OR (sender_username='$chat_with_user' AND receiver_username='$logged_in_user')
              ORDER BY msg_date ASC";
    $result = mysqli_query($con, $query);

    // Inicjalizacja tablicy do przechowywania wiadomości
    $messages = [];

    // Pobranie wszystkich wiadomości i zapisanie ich do tablicy
    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row;
    }

    // Przekazanie wiadomości w formacie JSON do klienta (AJAX)
    echo json_encode($messages);
}
?>
