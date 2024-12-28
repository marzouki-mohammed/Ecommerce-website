<?php
session_start();
include "../../php/db_connect.php";

if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name_filed']) && isset($_POST['parent_id_filed'])) {
    $name = $_POST['name_filed'] ?? '';
    $parent_id = $_POST['parent_id_filed'] ?? 1;

    if (!empty($name)) {
        // Vérifier si la catégorie existe déjà
        $sql_verify = "SELECT * FROM categories WHERE name = :name";
        $stmt_verify = $conn->prepare($sql_verify);
        $stmt_verify->execute(['name' => $name]);

        if ($stmt_verify->rowCount() > 0) {
            header("Location: addcategorie.php?error=La catégorie existe déjà.");
            exit;
        } else {
            // Insérer la nouvelle catégorie
            $sql_insert = "INSERT INTO categories (name, parent_id) VALUES (:name, :parent_id)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->execute(['name' => $name, 'parent_id' => $parent_id]);
            
            // Récupérer l'ID de la nouvelle catégorie
            $category_id = $conn->lastInsertId();

            // Gérer le téléchargement de l'image
            if (isset($_FILES['image_filde']) && $_FILES['image_filde']['error'] === UPLOAD_ERR_OK) {
                $img_name = $_FILES['image_filde']['name'];
                $tmp_name = $_FILES['image_filde']['tmp_name'];
                $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                $img_ex_to_lc = strtolower($img_ex);
                $allowed_exs = ['jpg', 'jpeg', 'png'];
                
                // Vérifier l'extension de l'image
                if (in_array($img_ex_to_lc, $allowed_exs)) {
                    $new_img_name = $name. '.' . $img_ex_to_lc;
                    $img_upload_path = '../../images/categorie/' . $new_img_name;
                    move_uploaded_file($tmp_name, $img_upload_path);
                    
                    // Insérer l'image dans la base de données
                    $sql_gallery = "INSERT INTO gallery (categorie_id, image, placeholder) VALUES (:categorie_id, :image, :placeholder)";
                    $stmt_gallery = $conn->prepare($sql_gallery);
                    $stmt_gallery->execute([
                        'categorie_id' => $category_id,
                        'image' => $new_img_name,
                        'placeholder' => $name
                    ]);
                } else {
                    header("Location: addcategorie.php?error=Vous ne pouvez pas uploader des fichiers de ce type.");
                    exit;
                }
            }
            header("Location: addcategorie.php?success=L'insertion a été effectuée avec succès.");
            exit;
        }
    } else {
        header("Location: addcategorie.php?error=Le champ du nom est vide.");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        h1 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group input[type="submit"] {
            background-color: #70c489;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .form-group input[type="submit"]:hover {
            background-color: #5ab774;
        }
        .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .field {
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #e0e0e0;
            margin-bottom: 15px;
            width: calc(100% );
            box-sizing: border-box; /* Ensure padding is included in width calculation */
        }
        input[type="file"] {
            display: none;
        }
        .field label {
            background-color: #bbfdae;
            padding: 5px;
            border-radius: 4px;
            color: #706f6f;
            border: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Add a New Category</h1>
        <?php
        if (isset($_GET['error'])) {
            echo "<div class='alert alert-danger'>" . htmlspecialchars($_GET['error']) . "</div>";
        } elseif (isset($_GET['success'])) {
            echo "<div class='alert alert-success'>" . htmlspecialchars($_GET['success']) . "</div>";
        }
        ?>
        <form id="categoryForm" action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Category Name:</label>
                <input type="text" id="name" name="name_filed" required>
            </div>
            <div class="form-group">
                <label for="parent_id">Parent Category (optional):</label>
                <select id="parent_id" name="parent_id_filed">
                    
                    <?php
                    $sql = "SELECT id, name FROM categories";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($rows as $row) {
                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="field" style="border: none;">
                <input type="file" id="image" name="image_filde">
                <label for="image">
                    Choose a photo
                    <ion-icon name="camera-outline"></ion-icon>
                </label>
            </div>
            <div class="form-group">
                <input type="submit" value="Add Category">
            </div>
        </form>
    </div>
    <!-- Ionicon link -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
