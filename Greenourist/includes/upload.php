<?php
include('db.php');

if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $difficulty = $_POST['difficulty'];
    
    // Handle image upload
    $target_dir = "../assets/images/";
    $target_file = $target_dir . basename($_FILES["picture"]["name"]);
    move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file);
    $picture_path = "assets/images/" . $_FILES["picture"]["name"];

    // Save to database
    $sql = "INSERT INTO activities (title, description, picture, difficulty) 
            VALUES ('$title', '$description', '$picture_path', '$difficulty')";
    
    if ($conn->query($sql)) {
        header("Location: ../admin/list_activities.php?success=1");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>