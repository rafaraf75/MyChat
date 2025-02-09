<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include("include/connection.php");

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['user_email'])) {
    header("location: signin.php");
    exit();
}

// Pobranie danych zalogowanego użytkownika
$user_email = $_SESSION['user_email'];
$get_user = "SELECT * FROM users WHERE user_email='$user_email'";
$run_user = mysqli_query($con, $get_user);
$row = mysqli_fetch_array($run_user);

$logged_in_user = $row['user_name'];
$logged_in_user_image = $row['profile_pic'];

// Pobranie rozmówcy, jeśli został wybrany
$chat_with_user = isset($_GET['chat_with']) ? $_GET['chat_with'] : null;
if ($chat_with_user) {

    // Pobranie danych rozmówcy
    $get_chat_user = "SELECT * FROM users WHERE user_name='$chat_with_user'";
    $run_chat_user = mysqli_query($con, $get_chat_user);

    if ($run_chat_user && mysqli_num_rows($run_chat_user) > 0) {
        $row_chat_user = mysqli_fetch_array($run_chat_user);
        $chat_with_image = $row_chat_user['profile_pic'];
    } else {
        $chat_with_user = null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Chat - HOME</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/home.css">
</head>

<body>
    <div class="container main-section">
        <div class="row">
            <!-- Lewa kolumna - Sidebar -->
            <div class="col-md-3 col-sm-3 col-xs-12 left-sidebar">
                <div class="input-group searchbox">
                    <div class="input-group-btn">
                        <center><a href="include/find_friends.php?user_name=<?php echo urlencode($logged_in_user); ?>">
                                <button class="btn btn-default search-icon" name="search_user" type="submit">Add new user</button>
                            </a>
                        </center>
                    </div>
                </div>
                <!-- Lista znajomych użytkownika -->
                <div class="left-chat">
                    <ul>
                        <?php include("include/get_users_data.php"); ?>
                    </ul>
                </div>
            </div>
            <!-- Sekcja czatu -->
            <div class="col-md-9 col-sm-9 col-xs-12 right-sidebar">
                <div class="row">
                    <div class="col-md-12 right-header">
                        <div class="right-header-img">
                            <img src="<?php echo $logged_in_user_image; ?>" alt="User Image">
                        </div>
                        <div class="right-header-detail">
                            <p>WELCOME, <?php echo $logged_in_user; ?></p>
                            <p>CHATTING WITH: <?php echo $chat_with_user ?? "Select a user"; ?></p>
                        </div>
                        <!-- Przycisk ustawień i wylogowania -->
                        <div class="right-header-buttons">
                            <a href="account_setting.php" class="btn btn-primary">Settings</a>
                            <form method="post" style="display: inline-block; margin-right: 10px;">
                                <button name="logout" class="btn btn-danger">Logout</button>
                            </form>
                        </div>
                        <!-- Obsługa wylogowania -->
                        <?php
                        if (isset($_POST['logout'])) {
                            mysqli_query($con, "UPDATE users SET log_in='Offline' WHERE user_name='$logged_in_user'");
                            header("Location:logout.php");
                            exit();
                        }
                        ?>
                    </div>
                </div>
                <!-- Sekcja wiadomości czatu -->
                <div class="row">
                    <div id="scrolling_to_bottom" class="col-md-12 right-header-contentChat">
                        <!-- Wiadomości będą pobierane za pomocą AJAX -->
                    </div>
                </div>
                <!-- Pole do wpisywania wiadomości -->
                <div class="row">
                    <div class="col-md-12 right-chat-textbox">
                        <?php if ($chat_with_user): ?>
                            <form id="messageForm">
                                <input autocomplete="off" type="text" id="msg_content" name="msg_content" placeholder="Write your message...">
                                <button class="btn" type="submit"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                            </form>
                        <?php else: ?>
                            <p class="text-center">No user selected for chat.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skrypt AJAX do obsługi czatu -->
    <script>
        // Funkcja do pobierania i odświeżania wiadomości
        function refreshMessages() {
            $.ajax({
                url: "include/fetch_messages.php",
                method: "POST",
                data: {
                    logged_in_user: "<?php echo $logged_in_user; ?>",
                    chat_with_user: "<?php echo $chat_with_user; ?>"
                },
                dataType: "json",
                success: function(data) {
                    var chatBox = $("#scrolling_to_bottom");
                    chatBox.html("");
                    $.each(data, function(index, message) {
                        if (message.sender_username === "<?php echo $logged_in_user; ?>") {
                            chatBox.append(`
        <div class='rightside-right-chat chat-bubble'>
            <small>You <span class="timestamp">${message.msg_date}</span></small>
            <p>${message.msg_content}</p>
        </div>
    `);
                        } else {
                            chatBox.append(`
        <div class='rightside-left-chat chat-bubble'>
            <small>${message.sender_username} <span class="timestamp">${message.msg_date}</span></small>
            <p>${message.msg_content}</p>
        </div>
    `);
                        }

                    });
                    chatBox.scrollTop(chatBox[0].scrollHeight); // Przewiń do dołu
                },
                error: function() {
                    console.error("Nie udało się pobrać wiadomości.");
                }
            });
        }
        // Wysyłanie wiadomości
        $("#messageForm").submit(function(e) {
            e.preventDefault();
            var message = $("#msg_content").val();
            if (message.trim() !== "") {
                $.ajax({
                    url: "include/send_message.php",
                    method: "POST",
                    data: {
                        logged_in_user: "<?php echo $logged_in_user; ?>",
                        chat_with_user: "<?php echo $chat_with_user; ?>",
                        msg_content: message
                    },
                    success: function() {
                        $("#msg_content").val("");
                        refreshMessages();
                    },
                    error: function() {
                        console.error("Failed to send message.");
                    }
                });
            }
        });
        // Automatyczne odświeżanie wiadomości co sekundę
        setInterval(refreshMessages, 1000);

        $(document).ready(function() {
            $("#scrolling_to_bottom").scrollTop($("#scrolling_to_bottom")[0].scrollHeight);
        });
    </script>
    <!-- Skrypt do dostosowania wysokości elementów na stronie -->
    <script type="text/javascript">
        $(document).ready(function() {
            var height = $(window).height();
            $('.left-chat').css('height', (height - 92) + 'px');
            $('.right-header-contentChat').css('height', (height - 163) + 'px');
        });
    </script>
</body>

</html>