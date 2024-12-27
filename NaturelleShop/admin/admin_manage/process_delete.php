<?php
if (isset($_POST['delete'])) {
    include "../../php/db_connect.php";
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }

    // Get the IDs of the selected checkboxes
    $delete_ids = $_POST['delete_ids']??'';

    if (!empty($delete_ids)) {
        // Convert the IDs to a comma-separated string
        $ids = implode(",", $delete_ids);

        // SQL query to delete the selected rows
        $sql = "DELETE FROM admin WHERE id IN ($ids)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        header("Location: Delete.php");
    } else {
        header("Location: Delete.php");
    }
} else {
    header("Location: Delete.php");
}

