<?php
$con = mysqli_connect("localhost", "root", "", "mychat")
    or die("Error connecting to database: ");

function search_user()
{
    global $con;

    if (isset($_GET['search_btn'])) {

        $search_query = htmlentities($_GET['search_query']);
        // Zapytanie wyszukujące użytkowników na podstawie nazwy lub e-maila
        $get_user = "SELECT * FROM users WHERE user_name LIKE '%$search_query%' OR user_email LIKE '%$search_query%'";
    } else {
        // Domyślne zapytanie, które pobiera maksymalnie 5 użytkowników, sortując ich według e-maila i nazwy
        $get_user = "SELECT * FROM users ORDER BY user_email, user_name DESC LIMIT 5";
    }
    $run_user = mysqli_query($con, $get_user);
    // Iteracja przez wyniki wyszukiwania i wyświetlanie użytkowników
    while ($row_user = mysqli_fetch_array($run_user)) {
        $user_name = $row_user['user_name'];
        $user_profile = $row_user['profile_pic'];
        $gender = $row_user['user_gender'];
        // Generowanie dynamicznej karty użytkownika z opcją dodania do znajomych
        echo "
        <div class='container'>
    <div class='row justify-content-center'>
        <div class='col-md-4'>
            <div class='card'>
                <img src='../$user_profile' alt='Profile Picture' class='card-img-top'>
                <div class='card-body text-center'>
                    <h4 class='card-title'><b>$user_name</b></h4>
                    <p class='card-text title'>$gender</p>
                    <a href='../include/add_friend.php?friend_email={$row_user['user_email']}' class='btn btn-success btn-block'>
                        Add $user_name as Friend
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
        ";
    }
}
