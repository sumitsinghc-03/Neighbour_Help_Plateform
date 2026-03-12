<?php
require 'inc/config.php';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT id,password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: community.php');
            exit;
        } else {
            $err = 'Invalid credentials.';
        }
    } else {
        $err = 'Enter email and password.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Login - Neighborhood Help</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background: linear-gradient(135deg,#a8d5c2,#c9e4ca);
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.container{
    width:900px;
    height:520px;
    background:white;
    border-radius:25px;
    display:flex;
    overflow:hidden;
    box-shadow:0 15px 40px rgba(0,0,0,0.1);
}

.left{
    width:50%;
    background:#f4f9f8;
    display:flex;
    justify-content:center;
    align-items:center;
}

.left img{
    width:85%;
}

.right{
    width:50%;
    padding:50px;
    background: linear-gradient(to bottom,#c9e4ca,#a8d5c2);
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.right h2{
    text-align:center;
    margin-bottom:30px;
}

form{
    display:flex;
    flex-direction:column;
}

/* ===== Input Styling with Glow ===== */
form input{
    padding:14px;
    margin-bottom:18px;
    border-radius:30px;
    border:1px solid #ddd;
    outline:none;
    font-size:14px;
    background:#fff;
    transition:0.3s ease;
}

form input:focus{
    border-color:#2f855a;
    box-shadow:0 0 10px rgba(47,133,90,0.6);
}

/* ===== Button ===== */
button{
    padding:14px;
    border-radius:30px;
    border:none;
    background:#f28c63;
    color:white;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#e76f51;
}

.error{
    background:#ffe5e5;
    color:#c0392b;
    padding:10px;
    border-radius:10px;
    margin-bottom:15px;
    text-align:center;
    font-size:14px;
}

p{
    text-align:center;
    margin-top:15px;
    font-size:14px;
}

a{
    color:blue;
    text-decoration:none;
    font-weight:600;
}
</style>

</head>
<body>

<div class="container">

    <div class="left">
        <img src="community.png" alt="Community Image">
    </div>

    <div class="right">
        <h2>Login</h2>

        <?php if($err): ?>
            <div class="error"><?php echo htmlspecialchars($err); ?></div>
        <?php endif; ?>

        <form method="post">
            <input name="email" type="email" placeholder="Email Address" required>
            <input name="password" type="password" placeholder="Password" required>
            <button type="submit">Log In</button>
        </form>

        <p>Don't have account? <a href="register.php">Register</a></p>
    </div>

</div>

</body>
</html>