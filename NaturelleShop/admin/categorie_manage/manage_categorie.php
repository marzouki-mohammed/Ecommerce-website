<?php 
session_start();
include "../../php/db_connect.php";

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérification de la connexion à la base de données et de la catégorie à supprimer
if (empty($conn) || empty($_SESSION['id_cat_delete'])) {
    echo empty($conn) ? "Database connection is not set." : "Category ID is not set or empty.";
    exit;
}

$parentCatId = $_SESSION['id_cat_delete'];

// Récupération des sous-catégories de la catégorie spécifiée et des catégories disponibles pour le transfert
$sql = "
    SELECT id, name FROM categories WHERE parent_id = :parent_id;
    SELECT id, name FROM categories WHERE id != :parent_id;
";
$stmt = $conn->prepare($sql);
$stmt->execute(['parent_id' => $parentCatId]);
$subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->nextRowset(); // Passer au second résultat
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['transfer'], $_POST['new_category_id'])) {
        $subcategoryId = $_POST['transfer'];
        $newParentCategoryId = $_POST['new_category_id'][$subcategoryId] ?? null;

        if ($newParentCategoryId) {
            // Mettre à jour la catégorie parente de la sous-catégorie
            $sql_update = "UPDATE categories SET parent_id = :new_parent_id WHERE id = :subcategory_id";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->execute([
                'new_parent_id' => $newParentCategoryId,
                'subcategory_id' => $subcategoryId
            ]);

            header("Location: manage_categorie.php");
            exit;
        }
    } elseif (isset($_POST['attribute'])) {
        // Vérification des associations avec des produits ou sous-catégories
        $sql_check = "
            SELECT COUNT(*) FROM product_categories WHERE category_id = :id_cat_delete;
            SELECT COUNT(*) FROM categories WHERE parent_id = :id_cat_delete;
        ";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute(['id_cat_delete' => $parentCatId]);
        $productCount = $stmt_check->fetchColumn();
        $stmt_check->nextRowset(); // Passer au second résultat
        $subcategoryCount = $stmt_check->fetchColumn();

        if ($productCount > 0) {
            header("Location: manage_pro.php");
            exit;
        } elseif ($subcategoryCount > 0) {
            header("Location: manage_categorie.php");
            exit;
        } else {
            // Suppression de la galerie associée et de la catégorie
            $sql_delete = "
                DELETE FROM gallery WHERE categorie_id = :id_cat_delete;
                DELETE FROM categories WHERE id = :id_cat_delete;
            ";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->execute(['id_cat_delete' => $parentCatId]);

            header("Location: delete.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subcategories</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        select {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 15px;
            background-color: #70c489;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .submit-button-container {
            text-align: right;
            margin-top: 1rem;
        }
        .submit-button {
            padding: 0.5rem 1rem;
            background-color: #32cd32;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .submit-button:hover {
            background-color: #28a745;
        }
    </style>
</head>
<body>
<h1>Manage Subcategories of Category</h1>
<form method="post" action="">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Subcategory Name</th>
                <th>Current Parent Category</th>
                <th>Transfer to Category</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subcategories as $subcategory): ?>
            <tr>
                <td><?php echo htmlspecialchars($subcategory['id']); ?></td>
                <td><?php echo htmlspecialchars($subcategory['name']); ?></td>
                <td><?php echo htmlspecialchars($parentCatId); ?></td>
                <td>
                    <select name="new_category_id[<?php echo htmlspecialchars($subcategory['id']); ?>]">
                        <?php foreach ($categories as $category): ?>
                        
                        <option value="<?php echo htmlspecialchars($category['id']); ?>" <?php echo ($category['id'] == $subcategory['id']) ? 'disabled' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                        <?php  endforeach; ?>
                    </select>
                </td>
                <td>
                    <button type="submit" name="transfer" value="<?php echo htmlspecialchars($subcategory['id']); ?>">Transfer</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="submit-button-container">
        <button type="submit" name="attribute" class="submit-button">GO TO THE NEXT</button>
    </div>
</form>
</body>
</html>
