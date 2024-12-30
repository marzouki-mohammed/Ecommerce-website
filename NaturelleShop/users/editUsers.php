<?php
// Connexion à la base de données
include "../php/db_connect.php";
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

// Initialisation des variables
$firstName = $lastName = $phoneNumber = $Email = "";
$error = "";

// Vérifier si l'utilisateur est sélectionné
if (isset($_GET['users_id']) && $_GET['users_id'] != -1 && !empty($_GET['users_id'])) {
    $id = intval($_GET['users_id']);
    
    // Récupération des données de l'utilisateur
    $sql = "SELECT * FROM users WHERE id=:user_id";
    $stm = $conn->prepare($sql);
    $stm->execute(['user_id' => $id]);
    $data_users = $stm->fetch();

    if ($data_users) {
        $firstName = $data_users['first_name'];
        $lastName = $data_users['last_name'];
        $phoneNumber = $data_users['phone_number'];
        $Email = $data_users['email'];
    } else {
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name_filde']);
    $last_name = trim($_POST['last_name_filde']);
    $phone_number = trim($_POST['phone_number_filde']);
    $email = trim($_POST['email_filde']);
    $password = trim($_POST['password_hash_filde']);
    
    // Vérifier si les champs ne sont pas vides
    if (!empty($first_name) && !empty($last_name) && !empty($phone_number) && !empty($email)) {
        try {
            // Mise à jour des informations de l'utilisateur
            $sql_edit = "
                UPDATE users
                SET 
                    first_name = :first_name,
                    last_name = :last_name,
                    phone_number = :phone_number,
                    email = :email
                WHERE id = :user_id
            ";
            $stmt_edit = $conn->prepare($sql_edit);
            $stmt_edit->execute([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'phone_number' => $phone_number,
                'email' => $email,
                'user_id' => $id
            ]);

            // Mise à jour du mot de passe si fourni
            if (!empty($password)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $sql_edit_password = "
                    UPDATE users
                    SET 
                        password_hash  = :password
                    WHERE id = :user_id
                ";
                $stmt_edit_password = $conn->prepare($sql_edit_password);
                $stmt_edit_password->execute([
                    'password' => $password_hash,
                    'user_id' => $id
                ]);
            }

            // Mise à jour de l'image si un fichier est téléchargé
            if (isset($_FILES['image_filde']) && $_FILES['image_filde']['error'] === UPLOAD_ERR_OK) {
                $img_name = $_FILES['image_filde']['name'];
                $tmp_name = $_FILES['image_filde']['tmp_name'];
                $img_ex = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
                $allowed_exs = ['jpg', 'jpeg', 'png'];

                if (in_array($img_ex, $allowed_exs)) {
                    // Supprimer l'image existante
                    $imgData = "SELECT image FROM users WHERE id = :user_id";
                    $exe = $conn->prepare($imgData);
                    $exe->execute(['user_id' => $id]);
                    $imageData = $exe->fetch();
                    if ($imageData) {
                        $image_name = $imageData['image'];
                        $image_path = '../upload/' . $image_name;
                        if (file_exists($image_path)) {
                            unlink($image_path);
                        }
                    }

                    // Télécharger la nouvelle image
                    $new_img_name = uniqid($first_name, true) . '.' . $img_ex;
                    $img_upload_path = '../upload/' . $new_img_name;
                    move_uploaded_file($tmp_name, $img_upload_path);

                    // Mise à jour du chemin de l'image
                    $sql_edit_img = "
                        UPDATE users
                        SET 
                            image = :image
                        WHERE id = :user_id
                    ";
                    $stmt_edit_img = $conn->prepare($sql_edit_img);
                    $stmt_edit_img->execute([
                        'image' => $new_img_name,
                        'user_id' => $id
                    ]);
                } else {
                    $error = "You can't upload files of this type";
                }
            }

            if (empty($error)) {
                header("Location: ../index.php");
                exit;
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "One or more empty fields";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NaturelleShop</title>

    <!-- Montserrat Font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <!-- Lien vers le fichier CSS externe -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<section class="container">
    <header class="title">Edit</header>
    <?php if ($error) { ?>
        <div class="alert" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php } ?>
    <form action="" method="post" enctype="multipart/form-data" class="form">
        <div class="column">
            <div class="input-box">
                <label>First Name</label>
                <input type="text" name="first_name_filde" id="first_name" placeholder="Enter full name" value="<?php echo htmlspecialchars($firstName); ?>"/>
            </div>

            <div class="input-box">
                <label>Last Name</label>
                <input type="text" name="last_name_filde" id="last_name" placeholder="Enter full name" value="<?php echo htmlspecialchars($lastName); ?>"/>
            </div>
        </div>

        <div class="input-box">
            <label>Phone Number</label>
            <input type="tel" name="phone_number_filde" id="phone_number" placeholder="Enter your phone number" value="<?php echo htmlspecialchars($phoneNumber); ?>"/>
        </div>

        <div class="input-box">
            <label>Email</label>
            <input type="email" name="email_filde" id="email" placeholder="example@gmail.com" value="<?php echo htmlspecialchars($Email); ?>"/>
        </div>

        <div class="input-box">
            <label>Password</label>
            <input type="password" name="password_hash_filde" id="password_hash" placeholder="Enter your password"/>
        </div>

        <div class="field" style="border: none;">
            <input type="file" id="image" name="image_filde">
            <label for="image">
                Choose a photo
                <ion-icon name="camera-outline"></ion-icon>
            </label>
        </div>

        <button class="submit">Submit</button>
        <p class="signin">Back to main page? <a href="../index.php">Back</a></p>
    </form>
</section>

<!-- Ionicon link -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
