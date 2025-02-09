<!DOCTYPE html>
<html lang="pl">
<head>
<title>Create New Account</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Roboto|Courgette|Pacifico:400,700" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/signup.css">

</head>
<body>
    <div class="container">
        <!-- Formularz rejestracji użytkownika -->
<div class="signup-form">
        <form action="" method="post">
            <div class="form-header">
                <h2>Sign Up</h2>
                <p>Fill out this form and start chating with your friends.</p>
            </div>
            <!-- Pole do wpisania nazwy użytkownika -->
            <div class="form-group">
                <label>Username</label>
                <input type="text" class="form-control" name="user_name" placeholder="User name"
                autocomplete="off" required>
            </div>
            <!-- Pole do wpisania hasła -->
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" name="user_pass" placeholder="Password"
                autocomplete="off" required>
            </div>
            <!-- Pole do wpisania adresu e-mail -->
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" class="form-control" name="user_email" placeholder="someone@site.com"
                autocomplete="off" required>
            </div>
            <!-- Wybór płci użytkownika -->
            <div class="form-group">
                <label>Gender</label>
                <select class="form-control" name="user_gender" required>
                    <option disabled="">Select your Gender</option>
                    <option>Male</option>
                    <option>Female</option>
                    <option>Others</option>
                </select>
            </div>
            <!-- Akceptacja regulaminu i polityki prywatności -->
            <div class="form-group">
                <label class="checkbox-inline"><input type="checkbox" required>I accept the <a href="#"
            >Terms of Use</a> &amp; <a href="#">Privacy Policy</a></label>
            </div>
            <!-- Przycisk do wysłania formularza rejestracji -->
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block btn-lg" name="sign_up">Sign Up</button>
            </div>
            <!-- Dołączenie pliku obsługującego rejestrację użytkownika -->
            <?php include("signup_user.php"); ?>
            <!-- Link do logowania, jeśli użytkownik ma już konto -->
            <div class="text-center small" style="color: #67428B;">Already have an account
            <a href="signin.php" >Signin here</a></div>
            </div>
        </form>

</body>
</html>