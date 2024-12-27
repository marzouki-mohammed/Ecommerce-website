<?php 
  session_start();
  if($_SERVER["REQUEST_METHOD"] == "POST"  && isset($_SESSION['user_id_reviews'])){
    include "../../php/db_connect.php";
            if (!isset($conn)) {
                echo "Database connection is not set.";
                exit;
            }
    $id=$_SESSION['user_id_reviews'];
     $sql="UPDATE reviews 
            SET permission_status = 'granted'
            WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    header("Location: ../../../index.php");
    exit;
  }else{
    header("Location: ../loginPermission.php?error=eroor");
    exit;

  }
  
   
?>