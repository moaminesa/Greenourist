<?php
require_once 'includes/config.php';

// Delete activity if requested
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM activities WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $success = "Activity deleted successfully!";
    } else {
        $error = "Error deleting activity.";
    }
}

// Get all activities
$activities = $conn->query("SELECT * FROM activities ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Activities</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 2rem;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .admin-title {
            color: #2d5016;
            font-size: 2rem;
        }
        
        .add-btn {
            background: linear-gradient(135deg, #4a7c25, #6ba832);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 124, 37, 0.3);
        }
        
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
            background: linear-gradient(135deg, #2d5016, #4a7c25);
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
            <div class="admin-header">
                <h1 class="admin-title">Manage Activities</h1>
                <a href="add_activity.php" class="add-btn">
                    <i class="fa fa-plus"></i> Add New Activity
                </a>
            </div>
            
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
            
            <table class="activities-table">
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
                                    <img src="<?php echo $activity['picture']; ?>" alt="<?php echo $activity['title']; ?>" class="activity-img">
                                </td>
                                <td><?php echo htmlspecialchars($activity['title']); ?></td>
                                <td><?php echo substr(htmlspecialchars($activity['description']), 0, 100) . '...'; ?></td>
                                <td>
                                    <span class="difficulty-badge <?php echo $activity['difficulty']; ?>">
                                        <?php 
                                            echo ucfirst($activity['difficulty']);
                                            if ($activity['difficulty'] === 'easy') echo '/Beginner';
                                            elseif ($activity['difficulty'] === 'moderate') echo '/Intermediate';
                                            else echo '/Advanced';
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit_activity.php?id=<?php echo $activity['id']; ?>" class="action-btn edit-btn">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <a href="admin_activities.php?delete=<?php echo $activity['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this activity?');">
                                        <i class="fa fa-trash"></i> Delete
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
        </div>
    </main>

    <script>
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