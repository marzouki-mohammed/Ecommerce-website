<?php
session_start();
if (isset($_POST['update_image']) && isset($_SESSION['user_id'])) {
    include "./db_connect.php";
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $new_image_name = "new_image_name.png"; // Changez cela avec la logique pour générer un nouveau nom d'image

    $sql = "UPDATE users SET image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$new_image_name, $user_id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['image_filed'] = $new_image_name;
    } else {
        echo "Failed to update image.";
    }
} else {
    echo "No user is logged in or invalid request.";
}
