<?php
require 'inc/config.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $area = trim($_POST['area'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (!$name || !$email || !$area || !$password) {
        $errors[] = 'Please fill required fields.';
    }

    if ($password !== $password2) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name,email,phone,area,password) VALUES (?,?,?,?,?)");
            $stmt->execute([$name,$email,$phone,$area,$hash]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            header('Location: community.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Register - Neighborhood Help</title>

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
    width:950px;
    height:560px;
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
    padding:40px;
    background: linear-gradient(to bottom,#c9e4ca,#a8d5c2);
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.right h2{
    text-align:center;
    margin-bottom:20px;
}

form{
    display:flex;
    flex-direction:column;
}

/* ===== Input Styling with Glow ===== */
form input,
form select{
    padding:12px;
    margin-bottom:12px;
    border-radius:25px;
    border:1px solid #ddd;
    outline:none;
    font-size:14px;
    background:#fff;
    transition:0.3s ease;
}

form input:focus,
form select:focus{
    border-color:#2f855a;
    box-shadow:0 0 8px rgba(47,133,90,0.6);
}

/* ===== Button ===== */
button{
    padding:12px;
    border-radius:25px;
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
    margin-bottom:10px;
    font-size:14px;
}

p{
    text-align:center;
    margin-top:10px;
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
        <h2>Create Account</h2>

        <?php if($errors): ?>
            <div class="error">
                <?php foreach($errors as $err) echo "<div>".htmlspecialchars($err)."</div>"; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <input name="name" placeholder="Full Name" required>
            <input name="email" type="email" placeholder="Email Address" required>
            <input name="phone" placeholder="Mobile Number">
            
            <select name="area" required>
                <option value="">Select Area</option>
                <option>Central</option>
                <option>North</option>
                <option>South</option>
                <option>East</option>
                <option>West</option>
            </select>

            <input name="password" type="password" placeholder="Password" required>
            <input name="password2" type="password" placeholder="Confirm Password" required>

            <button type="submit">Register</button>
        </form>

        <p>Already have account? <a href="login.php">Login</a></p>
    </div>

</div>

</body>
</html>