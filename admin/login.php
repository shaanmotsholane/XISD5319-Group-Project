<?php
session_start();

include '../config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $stmt = mysqli_prepare($conn, 'SELECT * FROM admins WHERE username = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $admin = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    $stored = $admin ? (string)$admin['password'] : '';
    $valid = $admin && (password_verify($password, $stored) || hash_equals($stored, $password));
    if ($valid) {
        // Upgrade legacy plaintext admin passwords automatically on successful login.
        if (!password_get_info($stored)['algo']) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $upgrade = mysqli_prepare($conn, 'UPDATE admins SET password = ? WHERE username = ?');
            mysqli_stmt_bind_param($upgrade, 'ss', $hash, $username);
            mysqli_stmt_execute($upgrade);
            mysqli_stmt_close($upgrade);
        }
        session_regenerate_id(true);
        $_SESSION['admin'] = $username;
        header('Location: dashboard.php'); exit;
    }
    $error = 'Invalid admin credentials.';
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="icon" type="image/jpeg" href="../images/logo.jpeg">
    <style>

        body{
            font-family: Arial;
            background: #f4f4f4;
            padding: 40px;
        }

        form{
            background: white;
            width: 400px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
        }

        input{
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }

        button{
            width: 100%;
            padding: 12px;
            background: black;
            color: white;
            border: none;
            margin-top: 10px;
        }

    </style>
</head>
<body>

<form method="POST">
    <?php if ($error): ?><p role="alert" style="color:#9b2020"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>

    <h1>Admin Login</h1>

    <input type="text"
           name="username"
           placeholder="Username"
           required>

    <input type="password"
           name="password"
           placeholder="Password"
           required>

    <button type="submit" name="login">
        Login
    </button>

</form>

</body>
</html>