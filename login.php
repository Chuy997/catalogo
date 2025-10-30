<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username == "panda" && $password == "password_admin") {
        $_SESSION['user_role'] = 'admin';
        header("Location: index.php");
        exit();
    } elseif ($username == "operator" && $password == "password_consulta") {
        $_SESSION['user_role'] = 'consulta';
        header("Location: index.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form action="login.php" method="post">
        <label for="username">Usuario:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">Login</button>

        <?php
        if (isset($error)) {
            echo "<p>$error</p>";
        }
        ?>
    </form>
</body>
</html>
