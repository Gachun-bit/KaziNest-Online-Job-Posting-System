<?php

session_start();
require_once 'config.php';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $checkEmail = $conn->query("SELECT email FROM users WHERE email = '$email'");
    if ($checkEmail->num_rows > 0) {
        $_SESSION['register_error'] = 'Email is already registered!';
        $_SESSION['active_form'] = 'register';
    } else {
        $conn->query("INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')");
        $_SESSION['active_form'] = 'login';
        $_SESSION['login_error'] = 'You can now log in with your credentials.';
    }

    header("Location: register.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_id'] = $user['id'];  // Assuming `$user['id']` is fetched from DB

            if($user['role'] === 'company') {
                header("Location: company_page.php");
            } else {
                header("Location: jobseeker_page.php");
            }
            exit();
        }
    }

    $_SESSION['login_error'] = 'Incorrect email or password';
    $_SESSION['active_form'] = 'login';
    header("Location: register.php");
    exit();
}

?>