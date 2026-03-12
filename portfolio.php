<?php
session_start();
require_once 'inc/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/portfolio.css">
</head>
<body>
    <nav>
        <div class="container">
            <h1>Portfolio</h1>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="portfolio.php" class="active">Portfolio</a></li>
                <li><a href="community.php">Community</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main class="container">
        <section class="hero">
            <h2>Welcome to My Portfolio</h2>
            <p>Showcasing my work and projects</p>
        </section>

        <section class="about">
            <h3>About Me</h3>
            <p>I'm a passionate developer focused on creating meaningful community-driven applications.</p>
        </section>

        <section class="projects">
            <h3>Projects</h3>
            <div class="project-grid">
                <div class="project-card">
                    <h4>Neighborhood Help</h4>
                    <p>A community platform connecting neighbors to help each other.</p>
                    <a href="home.php">View Project</a>
                </div>
                <div class="project-card">
                    <h4>Project 2</h4>
                    <p>Description of your second project goes here.</p>
                    <a href="#">View Project</a>
                </div>
                <div class="project-card">
                    <h4>Project 3</h4>
                    <p>Description of your third project goes here.</p>
                    <a href="#">View Project</a>
                </div>
            </div>
        </section>

        <section class="skills">
            <h3>Skills</h3>
            <div class="skill-list">
                <span class="skill">HTML</span>
                <span class="skill">CSS</span>
                <span class="skill">JavaScript</span>
                <span class="skill">PHP</span>
                <span class="skill">MySQL</span>
            </div>
        </section>

        <section class="contact">
            <h3>Contact</h3>
            <p>Email: <a href="mailto:your@email.com">your@email.com</a></p>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Portfolio. All rights reserved.</p>
    </footer>
</body>
</html>
