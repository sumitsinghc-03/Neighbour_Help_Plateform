<?php
require 'inc/config.php';
require 'inc/auth.php';
require_login();
$user = current_user();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $area = trim($_POST['area'] ?? $user['area']);
    $location = trim($_POST['location'] ?? '');
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;

    if (!$title || !$area) $errors[] = 'Title and area required.';
    if (!$location) $errors[] = 'Location required.';
    if (!$latitude || !$longitude) $errors[] = 'Please use current GPS location.';

    $category = "Other";

    if($description){

        $ai_url = "http://127.0.0.1:5000/predict";

        $data = json_encode([
            "text" => $description
        ]);

        $options = [
            "http" => [
                "header"  => "Content-Type: application/json\r\n",
                "method"  => "POST",
                "content" => $data
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($ai_url, false, $context);

        if ($result !== FALSE) {
            $response = json_decode($result, true);
            $category = $response['category'] ?? "Other";
        }
    }

    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['png','jpg','jpeg','gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = 'Invalid image type.';
        } else {
            $imageName = uniqid() . '.' . $ext;
            $target = __DIR__ . '/assets/uploads/' . $imageName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $errors[] = 'Failed to upload image.';
            }
        }
    }

    if (empty($errors)) {

        $stmt = $pdo->prepare("INSERT INTO posts 
        (user_id, title, description, category, image, area, location, latitude, longitude) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $user['id'],
            $title,
            $description,
            $category,
            $imageName,
            $area,
            $location,
            $latitude,
            $longitude
        ]);

        header('Location: community.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>New Post - Neighborhood Help</title>

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
color:#2f855a;
}

.container{
max-width:650px;
margin:60px auto;
background:white;
padding:35px;
border-radius:20px;
box-shadow:0 20px 50px rgba(0,0,0,0.1);
}

h2{
text-align:center;
margin-bottom:25px;
}

.form-group{
margin-bottom:18px;
}

form input,
form textarea,
form select{
width:100%;
padding:14px;
border-radius:12px;
border:1px solid #ddd;
font-size:14px;
background:#f9fafb;
}

textarea{
resize:none;
min-height:100px;
}

button{
width:100%;
padding:14px;
border-radius:12px;
border:solid;
background:#2f855a;
color:black;
font-weight:250;
cursor:pointer;
margin-top:10px;
}

.gps-btn{
background:none;
margin-top:4px;
width:auto;
display:inline-block;
border:#2f855a;
}

.error{
background:#ffe5e5;
color:#c0392b;
padding:12px;
border-radius:10px;
margin-bottom:18px;
}
</style>
</head>

<body>

<nav>
<div><a href="community.php">Neighborhood Help</a></div>
<div>
<?php if($user): ?>
<span>Hi, <?php echo htmlspecialchars($user['name']); ?></span>
<a href="logout.php" style="color:#c0392b;">Logout</a>
<?php endif; ?>
</div>
</nav>

<div class="container">

<h2>Create a New Problem Post</h2>

<?php if($errors): ?>
<div class="error">
<?php foreach($errors as $er) echo htmlspecialchars($er).'<br>'; ?>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" onsubmit="return checkGPS()">

<div class="form-group">
<label>Title</label>
<input name="title" required>
</div>

<div class="form-group">
<label>Description</label>
<textarea name="description"></textarea>
</div>

<div class="form-group">
<label>Area</label>
<select name="area" required>
<option value="<?php echo htmlspecialchars($user['area']); ?>">
<?php echo htmlspecialchars($user['area']); ?> (Your area)
</option>
<option>Central</option>
<option>North</option>
<option>South</option>
<option>East</option>
<option>West</option>
</select>
</div>

<div class="form-group">
<label>Location</label>
<input type="text" name="location" required>
<button type="button" class="gps-btn" onclick="getLocation()">Use Current GPS</button>
</div>

<input type="hidden" name="latitude" id="latitude">
<input type="hidden" name="longitude" id="longitude">

<div class="form-group">
<label>Upload Image</label>
<input type="file" name="image" accept="image/*">
</div>

<button type="submit">Post</button>

</form>

</div>

<script>

function getLocation() {
if (navigator.geolocation) {
navigator.geolocation.getCurrentPosition(function(position) {
document.getElementById("latitude").value = position.coords.latitude;
document.getElementById("longitude").value = position.coords.longitude;
alert("GPS Location Captured Successfully!");
});
} else {
alert("Geolocation not supported.");
}
}

function checkGPS(){

let lat = document.getElementById("latitude").value;
let lon = document.getElementById("longitude").value;

if(lat === "" || lon === ""){
alert("Please click 'Use Current GPS' before posting.");
return false;
}

return true;
}

</script>

</body>
</html>