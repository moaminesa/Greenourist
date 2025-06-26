<?php

session_start();
require_once __DIR__ . '/../includes/config.php';

// Redirect if not admin - THIS SHOULD BE AT THE TOP
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Handle actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

// Initialize variables
$error = '';
$success = '';
$activity = null;

// Handle different actions
switch ($action) {
    case 'add':
    case 'edit':
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $difficulty = $_POST['difficulty'];
            $id = $_POST['id'] ?? 0;
            
            // Validate inputs
            if (empty($title) || empty($description)) {
                $error = "Please fill in all required fields.";
            } else {
                // Handle file upload if new file was selected
                $picture_path = $_POST['existing_picture'] ?? '';
                
                if (!empty($_FILES["picture"]["name"])) {
                    $target_dir = "../assets/images/activities/";
                    
                    // Create directory if it doesn't exist
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0755, true);
                    }
                    
                    $filename = uniqid() . '_' . basename($_FILES["picture"]["name"]);
                    $target_file = $target_dir . $filename;
                    $uploadOk = true;
                    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                    
                    // Check if image file is valid
                    $check = getimagesize($_FILES["picture"]["tmp_name"]);
                    if ($check === false) {
                        $error = "File is not an image.";
                        $uploadOk = false;
                    }
                    
                    // Check file size (max 2MB)
                    if ($_FILES["picture"]["size"] > 2000000) {
                        $error = "Sorry, your file is too large (max 2MB).";
                        $uploadOk = false;
                    }
                    
                    // Allow certain file formats
                    $allowed = ['jpg', 'jpeg', 'png'];
                    if (!in_array($imageFileType, $allowed)) {
                        $error = "Only JPG, JPEG, PNG files are allowed.";
                        $uploadOk = false;
                    }
                    
                    // Upload file if everything is ok
                    if ($uploadOk) {
                        if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file)) {
                            $picture_path = "assets/images/activities/" . $filename;
                        } else {
                            $error = "Sorry, there was an error uploading your file.";
                        }
                    }
                }
                
                if (empty($error)) {
                    try {
                        if ($action === 'add') {
                            // Insert new activity
                            $sql = "INSERT INTO activities (title, description, picture, difficulty, featured) 
                                    VALUES (?, ?, ?, ?, 0)";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ssss", $title, $description, $picture_path, $difficulty);
                        } else {
                            // Update existing activity
                            $sql = "UPDATE activities SET title = ?, description = ?, picture = ?, difficulty = ? 
                                    WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ssssi", $title, $description, $picture_path, $difficulty, $id);
                        }
                        
                        if ($stmt->execute()) {
                            $success = "Activity " . ($action === 'add' ? 'added' : 'updated') . " successfully!";
                            
                            // If adding new, redirect to edit page
                            if ($action === 'add') {
                                $new_id = $conn->insert_id;
                                header("Location: admin_page.php?action=edit&id=$new_id");
                                exit();
                            }
                        } else {
                            throw new Exception("Database error: " . $conn->error);
                        }
                    } catch (Exception $e) {
                        // Delete the uploaded file if database operation failed
                        if (!empty($target_file)) {
                            unlink($target_file);
                        }
                        $error = $e->getMessage();
                    }
                }
            }
        }

        // For edit action, fetch the activity
        if ($action === 'edit' && $id) {
            $sql = "SELECT * FROM activities WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $activity = $result->fetch_assoc();
            
            if (!$activity) {
                $error = "Activity not found.";
                $action = 'list'; // Fall back to list view
            }
        }
        break;
        
    // ... rest of your switch cases remain the same ...
        
case 'list':
default:
    // Fetch all activities for listing
$activities = $conn->query("SELECT * FROM activities ORDER BY id DESC");
    break;

case 'toggle_featured':
    if ($id) {
        $sql = "UPDATE activities SET featured = NOT featured WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: admin_page.php");
        exit();
    }
    break;

    case 'delete':
    if ($id) {
        try {
            // First, get the picture path to delete the file
            $sql = "SELECT picture FROM activities WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $activity = $result->fetch_assoc();
            
            if ($activity) {
                // Delete the record from database
                $sql = "DELETE FROM activities WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
            
                if ($stmt->execute()) {
                    // Delete the associated image file if it exists
                    if (!empty($activity['picture'])) {
                        $file_path = '../' . $activity['picture'];
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                    $success = "Activity deleted successsfully!";
                } else {
                    throw new Exception("Database error: " . $conn->error);
                }
            } else {
                $error = "Activity not found.";
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        
        // Redirect back to list view
        header("Location: admin_page.php");
        exit();
    }
    break;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Greenourist</title>
    <link rel="stylesheet" href="../assets/styleold.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <style>
        /* Admin-specific styles */
        .admin-container {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .admin-title {
            color: #2d506b;
            text-align: center;
            margin: 1rem 0 2rem;
            font-size: 2rem;
            position: relative;
        }
        
        .admin-title::after {
            content: '';
            display: block;
            width: 100px;
            height: 4px;
            background: linear-gradient(to right, #2d506b, #5a8f3d);
            margin: 1rem auto 0;
            border-radius: 2px;
        }
        
        .add-btn {
            background: linear-gradient(45deg,#2d506b,#5a8f3d);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(45, 80, 107, 0.3);
        }
        
        /* Form styles */
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
            color: #2d506b;
        }
        
        .form-control {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            background: #f9f9f9;
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
            background: linear-gradient(45deg,#2d506b,#5a8f3d);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(45, 80, 107, 0.3);
        }
        
        /* Table styles */
        .activities-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .activities-table th, 
        .activities-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .activities-table th {
            background: linear-gradient(45deg, #2d506b, #5a8f3d);
            color: white;
            font-weight: 600;
        }
        
        .activities-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .activity-img {
            width: 100px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
        }
        .star-btn { color: #ccc; 
        
        }
.featured-btn { color: gold;

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
            margin-top: 10px;
            border: 2px solid #ddd;
        }
        
        .current-image {
            font-size: 0.9rem;
            color: #666;
        }
        
        /* Difficulty badges */
        .difficulty-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-block;
        }
        
        .easy { background: #90ee90; color: #2d5016; }
        .moderate { background: #ffd700; color: #8b4513; }
        .hard { background: #ff6b6b; color: white; }
        
        /* Action buttons */
        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            margin-right: 5px;
            transition: all 0.2s ease;
        }
        
        .edit-btn {
            background: #4299e1;
            color: white;
        }
        
        .edit-btn:hover {
            background: #3182ce;
        }
        
        .delete-btn {
            background: #e53e3e;
            color: white;
        }
        
        .delete-btn:hover {
            background: #c53030;
        }
        
        /* Alerts */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <header style="background: linear-gradient(45deg,#2d506b,#5a8f3d);">
        <nav class="container">
            <div class="logo-container">
                <a href="../index.php" class="text-logo">
                    <img src="../assets/images/Greenourist-logo.png" alt="Greenourist Logo" class="logo">
                </a> 
            </div>
            <div class="header-title">
                <h1>Greenourist Admin</h1>
            </div>
            <ul class="nav-links">
                <li><a href="../index.php">View Site</a></li>
                <li><a href="admin_page.php">Dashboard</a></li>
                <li><a href="admin_page.php?action=add">Add Activity</a></li>
                <li><a href="logout.php" class="auth-btn logout-btn">Logout</a></li>
                <li><span class="welcome-msg">Welcome, <?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?></span></li>
            </ul>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
        <div class="mobile-menu" id="mobileMenu">
            <a href="../index.php">View Site</a>
            <a href="admin_page.php">Dashboard</a>
            <a href="admin_page.php?action=add">Add Activity</a>
            <a href="logout.php" class="auth-btn logout-btn">Logout</a>
        </div>
    </header>

    <main>
        <div class="admin-container">
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
            
            <?php if ($action === 'list'): ?>
                <div class="admin-header">
                    <h1 class="admin-title">Manage Activities</h1>
                    <a href="admin_page.php?action=add" class="add-btn">
                        <i class="fas fa-plus"></i> Add New Activity
                    </a>
                </div>
                
                <table class="activities-table">
                    <th>Featured</th>

                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Difficulty</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($activities->num_rows > 0): ?>
                            <?php while($activity = $activities->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <img src="../<?php echo htmlspecialchars($activity['picture']); ?>" alt="<?php echo htmlspecialchars($activity['title']); ?>" class="activity-img">
                                    </td>
                                    <td><?php echo htmlspecialchars($activity['title']); ?></td>
                                    <td><?php echo substr(htmlspecialchars($activity['description']), 0, 100) . '...'; ?></td>
                                    <td>
                                        <span class="difficulty-badge <?php echo htmlspecialchars($activity['difficulty']); ?>">
                                            <?php 
                                                echo ucfirst(htmlspecialchars($activity['difficulty']));
                                                if ($activity['difficulty'] === 'easy') echo '/Beginner';
                                                elseif ($activity['difficulty'] === 'moderate') echo '/Intermediate';
                                                else echo '/Advanced';
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="admin_page.php?action=edit&id=<?php echo $activity['id']; ?>" class="action-btn edit-btn">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="admin_page.php?action=delete&id=<?php echo $activity['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this activity?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                    
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem;">No activities found. Add your first activity!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
            <?php elseif ($action === 'add' || $action === 'edit'): ?>
                <h1 class="admin-title"><?php echo $action === 'add' ? 'Add New' : 'Edit'; ?> Activity</h1>
                
                <form class="admin-form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $activity['id'] ?? ''; ?>">
                    <input type="hidden" name="existing_picture" value="<?php echo $activity['picture'] ?? ''; ?>">
                    
                    <div class="form-group">
                        <label for="title">Activity Title</label>
                        <input type="text" id="title" name="title" class="form-control" 
                               value="<?php echo htmlspecialchars($activity['title'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" required><?php 
                            echo htmlspecialchars($activity['description'] ?? ''); 
                        ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="difficulty">Difficulty Level</label>
                        <select id="difficulty" name="difficulty" class="form-control" required>
                            <option value="easy" <?php echo ($activity['difficulty'] ?? '') === 'easy' ? 'selected' : ''; ?>>Easy/Beginner</option>
                            <option value="moderate" <?php echo ($activity['difficulty'] ?? '') === 'moderate' ? 'selected' : ''; ?>>Moderate/Intermediate</option>
                            <option value="hard" <?php echo ($activity['difficulty'] ?? '') === 'hard' ? 'selected' : ''; ?>>Hard/Advanced</option>
                        </select>
                    </div>
                    
                    <div class="form-group file-upload">
                        <label for="picture">Activity Image</label>
                        <?php if ($action === 'edit' && !empty($activity['picture'])): ?>
                            <p class="current-image">Current image: 
                                <a href="../<?php echo htmlspecialchars($activity['picture']); ?>" target="_blank">View</a>
                            </p>
                            <img id="imagePreview" class="file-upload-preview" 
                                 src="../<?php echo htmlspecialchars($activity['picture']); ?>" 
                                 alt="Current Image">
                        <?php else: ?>
                            <img id="imagePreview" class="file-upload-preview" style="display: none;">
                        <?php endif; ?>
                        <input type="file" id="picture" name="picture" accept="image/*">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <?php echo $action === 'add' ? 'Add' : 'Update'; ?> Activity
                    </button>
                    
                    <a href="admin_page.php" class="btn" style="background: #f1f1f1; color: #333; text-align: center;">
                        Cancel
                    </a>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>© <?php echo date('Y'); ?> Greenourist - Admin Panel 🌍</p>
        </div>
    </footer>

    <script>
        // Image preview functionality
        document.getElementById('picture')?.addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            }
        });
        
        // Mobile menu toggle
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');
        
        hamburger?.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });
        
        document.querySelectorAll('.mobile-menu a')?.forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('active');
            });
        });
        
        document.addEventListener('click', (e) => {
            if (hamburger && mobileMenu && !hamburger.contains(e.target) && !mobileMenu.contains(e.target)) {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('active');
            }
        });
    </script>
</body>
</html>