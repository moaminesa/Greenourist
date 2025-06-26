<?php
require_once 'includes/config.php';

// Get activity data if editing
$activity = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM activities WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $activity = $result->fetch_assoc();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $difficulty = $_POST['difficulty'];
    
    // Check if new image was uploaded
    if ($_FILES["picture"]["size"] > 0) {
        // Handle file upload
        $target_dir = "assets/images/activities/";
        $target_file = $target_dir . basename($_FILES["picture"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is valid
        $check = getimagesize($_FILES["picture"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
            $error = "File is not an image.";
        }
        
        // Check file size (max 2MB)
        if ($_FILES["picture"]["size"] > 2000000) {
            $uploadOk = 0;
            $error = "Sorry, your file is too large.";
        }
        
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $uploadOk = 0;
            $error = "Only JPG, JPEG, PNG files are allowed.";
        }
        
        // Upload file if everything is ok
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file)) {
                $picture_path = "assets/images/activities/" . basename($_FILES["picture"]["name"]);
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        // Keep the existing image
        $picture_path = $_POST['existing_picture'];
    }
    
    if (!isset($error)) {
        // Update in database
        $sql = "UPDATE activities SET title = ?, description = ?, picture = ?, difficulty = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $title, $description, $picture_path, $difficulty, $id);
        
        if ($stmt->execute()) {
            $success = "Activity updated successfully!";
            // Refresh activity data
            $sql = "SELECT * FROM activities WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $activity = $result->fetch_assoc();
        } else {
            $error = "Error updating activity: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit Activity</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .admin-container {
            max-width: 800px;
            margin: 100px auto 50px;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .admin-form {
            display: grid;
            gap: 1.5rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .form-group label {
            font-weight: 600;
            color: #2d5016;
        }
        
        .form-control {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4a7c25, #6ba832);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 124, 37, 0.3);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .file-upload {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .file-upload-preview {
            max-width: 300px;
            max-height: 200px;
            border-radius: 8px;
        }
        
        .current-image {
            font-size: 0.9rem;
            color: #666;
        }
         footer {
            background: linear-gradient(45deg, #2d506b, #5a8f3d);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }
    </style>
</head>
<body>
    <!-- Header (same as add_activity.php) -->
    <header style="background: rgba(45, 80, 22, 0.95);">
        <nav class="container">
            <div class="logo-container">
                <a href="index.html" class="text-logo"><img src="images/Greenourist-logo.png" alt="" class="logo"></a> 
            </div>
            <div class="header-title">
                <h1>Greenourist Admin</h1>
            </div>
            <ul class="nav-links">
                <li><a href="activities.html">View Site</a></li>
                <li><a href="admin_activities.php">Manage Activities</a></li>
                <li><a href="add_activity.php">Add Activity</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
        <div class="mobile-menu" id="mobileMenu">
            <a href="activities.html">View Site</a>
            <a href="admin_activities.php">Manage Activities</a>
            <a href="add_activity.php">Add Activity</a>
            <a href="logout.php">Logout</a>
        </div>
    </header>

    <main>
        <div class="admin-container fade-in-up">
            <h2 style="color: #2d5016; text-align: center; margin-bottom: 2rem;">Edit Activity</h2>
            
            <?php if(isset($success)): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if($activity): ?>
                <form class="admin-form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $activity['id']; ?>">
                    <input type="hidden" name="existing_picture" value="<?php echo $activity['picture']; ?>">
                    
                    <div class="form-group">
                        <label for="title">Activity Title</label>
                        <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($activity['title']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" required><?php echo htmlspecialchars($activity['description']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="difficulty">Difficulty Level</label>
                        <select id="difficulty" name="difficulty" class="form-control" required>
                            <option value="easy" <?php echo $activity['difficulty'] === 'easy' ? 'selected' : ''; ?>>Easy/Beginner</option>
                            <option value="moderate" <?php echo $activity['difficulty'] === 'moderate' ? 'selected' : ''; ?>>Moderate/Intermediate</option>
                            <option value="hard" <?php echo $activity['difficulty'] === 'hard' ? 'selected' : ''; ?>>Hard/Advanced</option>
                        </select>
                    </div>
                    
                    <div class="form-group file-upload">
                        <label for="picture">Activity Image</label>
                        <p class="current-image">Current image: <a href="<?php echo $activity['picture']; ?>" target="_blank">View</a></p>
                        <input type="file" id="picture" name="picture" accept="image/*">
                        <img id="imagePreview" class="file-upload-preview" src="<?php echo $activity['picture']; ?>" alt="Current Image">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Activity</button>
                </form>
            <?php else: ?>
                <div class="alert alert-error">
                    Activity not found.
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Image preview functionality
        document.getElementById('picture').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                
                reader.readAsDataURL(file);
            }
        });
        
        // Mobile menu toggle (same as your existing script)
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');
        
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });
        
        document.querySelectorAll('.mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('active');
            });
        });
    </script>
</body>
</html>