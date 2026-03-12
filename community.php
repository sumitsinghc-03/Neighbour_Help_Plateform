<?php
require 'inc/config.php';
$user = current_user();

$filter_area = $_GET['area'] ?? 'all';

if ($filter_area === 'all') {
    $stmt = $pdo->query("SELECT p.*, u.name FROM posts p 
                         JOIN users u ON p.user_id = u.id 
                         ORDER BY p.created_at DESC");
} else {
    $stmt = $pdo->prepare("SELECT p.*, u.name FROM posts p 
                           JOIN users u ON p.user_id = u.id 
                           WHERE p.area = ? 
                           ORDER BY p.created_at DESC");
    $stmt->execute([$filter_area]);
}

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Neighborhood Help - Feed</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background: linear-gradient(135deg,#a8d5c2,#c9e4ca);
    min-height:100vh;
}

nav{
    background:white;
    padding:15px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
}

nav a{
    text-decoration:none;
    font-weight:600;
    margin-right:15px;
}

.logo{
    font-size:18px;
    font-weight:700;
    color:#2f855a;
}

.nav-btn{
    background:#2f855a;
    color:white;
    padding:8px 14px;
    border-radius:8px;
}

.container{
    max-width:900px;
    margin:50px auto;
    padding:20px;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

select{
    padding:10px 14px;
    border-radius:10px;
    border:1px solid #ddd;
    background:#fff;
}

.card{
    background:white;
    padding:20px;
    border-radius:18px;
    box-shadow:0 12px 30px rgba(0,0,0,0.08);
    margin-bottom:20px;
    transition:0.3s;
}

.card:hover{
    transform:translateY(-3px);
}

.meta{
    font-size:13px;
    color:#666;
    margin-top:4px;
}

.description{
    margin-top:12px;
    line-height:1.6;
}

.post-image{
    margin-top:12px;
}

.post-image img{
    max-width:100%;
    height:auto;          /* Natural height */
    display:block;
    border-radius:12px;
}

.status{
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.solved{
    background:#d4edda;
    color:#155724;
}

.progress{
    background:#fff3cd;
    color:#856404;
}

.pending{
    background:#e2e3e5;
    color:#383d41;
}

.actions{
    margin-top:12px;
}

.actions a{
    margin-right:12px;
    font-size:14px;
    color:#2f855a;
}

.empty{
    background:white;
    padding:20px;
    border-radius:15px;
    text-align:center;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}
</style>
</head>

<body>

<nav>
    <div>
        <span class="logo">Neighborhood Help</span>
        <a href="new_post.php" class="nav-btn">New Post</a>
    </div>

    <div>
        <?php if($user): ?>
            <span>Hi, <?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($user['area']); ?>)</span>
            <a href="logout.php" style="color:#c0392b;">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php" style="color:#2f855a;">Register</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">

    <div class="header">
        <h2>Community Feed</h2>

        <form method="get">
            <select name="area" onchange="this.form.submit()">
                <option value="all" <?php if($filter_area=='all') echo 'selected'; ?>>All areas</option>
                <option value="Central" <?php if($filter_area=='Central') echo 'selected'; ?>>Central</option>
                <option value="North" <?php if($filter_area=='North') echo 'selected'; ?>>North</option>
                <option value="South" <?php if($filter_area=='South') echo 'selected'; ?>>South</option>
                <option value="East" <?php if($filter_area=='East') echo 'selected'; ?>>East</option>
                <option value="West" <?php if($filter_area=='West') echo 'selected'; ?>>West</option>
            </select>
        </form>
    </div>

    <?php if(empty($posts)): ?>
        <div class="empty">No posts found.</div>
    <?php endif; ?>

    <?php foreach($posts as $p): ?>

        <div class="card">

            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <strong><?php echo htmlspecialchars($p['title']); ?></strong>
                    <div class="meta">
                        Posted by <?php echo htmlspecialchars($p['name']); ?> |
                        <?php echo htmlspecialchars($p['area']); ?> |
                        <?php echo htmlspecialchars($p['created_at']); ?>
                    </div>
                </div>

                <div class="status 
                    <?php
                        if($p['status']=='solved') echo 'solved';
                        elseif($p['status']=='in_progress') echo 'progress';
                        else echo 'pending';
                    ?>">
                    <?php echo htmlspecialchars(ucfirst($p['status'])); ?>
                </div>
            </div>

            <div class="description">
                <?php echo nl2br(htmlspecialchars($p['description'])); ?>
            </div>

            <?php if($p['image']): ?>
                <div class="post-image">
                    <img src="assets/uploads/<?php echo htmlspecialchars($p['image']); ?>">
                </div>
            <?php endif; ?>

            <div class="actions">
                <a href="view_post.php?id=<?php echo $p['id']; ?>">View</a>

                <?php if($user && $user['role']=='admin'): ?>
                    <a href="admin.php?id=<?php echo $p['id']; ?>">Manage</a>
                <?php endif; ?>
            </div>

        </div>

    <?php endforeach; ?>

</div>

</body>
</html>