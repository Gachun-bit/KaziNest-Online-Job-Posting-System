<?php

session_start();

$errors = [
    'register' => $_SESSION['register_error'] ?? '',
    'login' => $_SESSION['login_error'] ?? ''
];
$activeForm = $_SESSION['active_form'] ?? 'register';

session_unset();

function showError($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ? 'active' : '';
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>webpage Design</title>
    <link rel="stylesheet" href="style.css">
</head>
 <body>
    <div class="register-page"> 
    <div class="navbar">
        <div class="icon">
            <h2 class="logo">KaziNest</h2>
        </div>
        <div class="menu">
            <ul>
                <li><a href="index.html">HOME</a></li>
                <li><a href="about.html">ABOUT</a></li>
                <li><a href="register.php">REGISTER</a></li>
                <li><a href="faq.html">FAQ</a></li>   
            </ul>
        </div>
    </div> 
    <div class="Register <?= isActiveForm('register', $activeForm); ?>" id="register-form">
        <form action="login_register.php" method="post">
            <h1>REGISTER</h1>
            <?= showError($errors['register']); ?>
            <label>Username</label>
            <input type="text" name="username">
            <label>Email</label>
            <input type="text" name="email">
            <label>Password</label>
            <input type="password" name="password">
            <label>Role</label>
            <select name="role" required>
                <option value=""></option>--Select Role--</option>
                <option value="jobseeker">Job Seeker</option>
                <option value="company">Company</option>
            </select>
            <button type="submit" name="register">Register</button>
            <p>Already have an account<a href="#" onclick="showform('login-form')"> Login</a> </p>
        </form>
    </div>
    <div class="Register <?= isActiveForm('login', $activeForm); ?>" id="login-form">
        <form action="login_register.php" method="post">
            <h1>WELCOME</h1>
            <?= showError($errors['login']); ?>
            <label>Email</label>
            <input type="text" name="email">
            <label>Password</label>
            <input type="password" name="password">
             <button type="submit" name="login">Login</button>
            <p> Don't have an account<a href="#" onclick="showform('register-form')"> Register</a> </p>
        </form>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>