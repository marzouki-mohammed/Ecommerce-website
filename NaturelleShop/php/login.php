<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';

    if (!empty($email) && !empty($pass)) {
        include "./db_connect.php";
        if (!isset($conn)) {
           echo "Database connection is not set.";
           exit;
        }

        $sql = "SELECT * FROM admin WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);

        if ($stmt->rowCount() === 1) {
            $admin = $stmt->fetch();
            $adminusername = $admin['username'];
            $password = $admin['password_hash'];
            $adminemail = $admin['email'];
            $adminfull_name = $admin['full_name'];

            if($adminemail === $email && $pass === $password) {
                $_SESSION['full_name'] = $adminfull_name;
                $_SESSION['admin_username'] = $adminusername;
                $_SESSION['user_id'] = $admin['id']; // Assurez-vous que la table admin a une colonne 'id'
                header("Location: ../admin/login.php");
                exit;
            }
            else{
                $em = "Incorrect username or password";
                header("Location: ../../index.php?error=$em");
                exit;
            }
        } else {
            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$email]);

            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch();
                $image = $user['image'];
                $useremail = $user['email'];
                $password = $user['password_hash'];

                if($useremail === $email && password_verify($pass, $password)) {
                    $_SESSION['image_filed'] = $image;
                    $_SESSION['user_id'] = $user['id']; // Assurez-vous que la table users a une colonne 'id'
                    
                    header("Location: ../../index.php");
                    exit;
                }else{
                    header("Location: ../../index.php?error=Incorrect username or password");
                    exit;
                }

            } else {
                $em = "Incorrect username or password";
                header("Location: ../../index.php?error=$em");
                exit;
            }
        }
    } else {
        $em = "User or admin Email and Password are required";
        header("Location: ../../index.php?error=$em");
        exit;
    }
} else {
    header("Location: ../../index.php?error=error");
    exit;
}
