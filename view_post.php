<?php
require 'inc/config.php';
require 'inc/auth.php';
$user = current_user();

$post_id = $_GET['id'] ?? null;
if (!$post_id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, u.name, u.area as user_area 
                       FROM posts p 
                       JOIN users u ON p.user_id = u.id 
                       WHERE p.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) { echo "Post not found"; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['comment']) && $user) {
    $c = trim($_POST['comment']);
    $stmt = $pdo->prepare("INSERT INTO comments (post_id,user_id,comment) VALUES (?,?,?)");
    $stmt->execute([$post_id,$user['id'],$c]);
    header('Location: view_post.php?id=' . $post_id);
    exit;
}

$cmts = $pdo->prepare("SELECT c.*, u.name 
                       FROM comments c 
                       JOIN users u ON c.user_id = u.id 
                       WHERE c.post_id = ? 
                       ORDER BY c.created_at DESC");
$cmts->execute([$post_id]);
$comments = $cmts->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($post['title']); ?> - Neighborhood Help</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background: linear-gradient(135deg,#a8d5c2,#c9e4ca);
    min-height:100vh;
}

.container{
    max-width:750px;
    margin:60px auto;
    background:white;
    padding:35px;
    border-radius:20px;
    box-shadow:0 20px 50px rgba(0,0,0,0.1);
}

h1{
    margin-bottom:10px;
}

.meta{
    font-size:14px;
    color:#666;
    margin-bottom:20px;
}

.description{
    margin-bottom:20px;
    line-height:1.6;
}

.post-image{
    margin-bottom:20px;
}

.post-image img{
    max-width:100%;
    height:auto;
    border-radius:12px;
}

.status{
    display:inline-block;
    padding:6px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
    background:#e2e3e5;
}

.solved{
    background:#d4edda;
    color:#155724;
}

.section-title{
    margin-top:35px;
    margin-bottom:15px;
    font-weight:600;
}

textarea{
    width:100%;
    padding:14px;
    border-radius:12px;
    border:1px solid #ddd;
    font-size:14px;
    background:#f9fafb;
}

button{
    margin-top:10px;
    padding:10px 18px;
    border:none;
    border-radius:12px;
    background:#2f855a;
    color:white;
    font-weight:600;
    cursor:pointer;
}

.comment-card{
    background:white;
    padding:15px;
    border-radius:14px;
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
    margin-top:12px;
}

.comment-meta{
    font-size:12px;
    color:#777;
    margin-bottom:6px;
}

.back{
    display:inline-block;
    margin-bottom:20px;
    color:#2f855a;
    text-decoration:none;
    font-weight:600;
}
</style>
</head>

<body>

<div class="container">

    <a href="community.php" class="back">← Back to Community</a>

    <h1><?php echo htmlspecialchars($post['title']); ?></h1>

    <div class="meta">
        By <?php echo htmlspecialchars($post['name']); ?> |
        <?php echo htmlspecialchars($post['area']); ?> |
        <?php echo htmlspecialchars($post['created_at']); ?>
    </div>

    <div class="description">
        <?php echo nl2br(htmlspecialchars($post['description'])); ?>
    </div>

    <?php if($post['image']): ?>
        <div class="post-image">
            <img src="assets/uploads/<?php echo htmlspecialchars($post['image']); ?>">
        </div>
    <?php endif; ?>

    <div>
        <strong>Status:</strong>
        <span class="status <?php echo ($post['status']=='solved') ? 'solved' : ''; ?>">
            <?php echo htmlspecialchars($post['status']); ?>
        </span>
    </div>

    <?php if(!empty($post['location'])): ?>
        <div style="margin-top:10px;">
            <strong>Location:</strong>
            <?php echo htmlspecialchars($post['location']); ?>
        </div>
    <?php endif; ?>

    <div class="section-title">Comments</div>

    <?php if($user): ?>
        <form method="post">
            <textarea name="comment" rows="3" placeholder="Write your comment..." required></textarea>
            <button type="submit">Add Comment</button>
        </form>
    <?php else: ?>
        <p>Please <a href="login.php">login</a> to comment.</p>
    <?php endif; ?>

    <?php foreach($comments as $c): ?>
        <div class="comment-card">
            <div class="comment-meta">
                <?php echo htmlspecialchars($c['name']); ?> |
                <?php echo htmlspecialchars($c['created_at']); ?>
            </div>
            <div>
                <?php echo htmlspecialchars($c['comment']); ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if(empty($comments)): ?>
        <div style="margin-top:10px;">No comments yet.</div>
    <?php endif; ?>

</div>

</body>
</html>