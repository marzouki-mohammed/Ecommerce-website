<?php 
session_start();
include "../../php/db_connect.php";

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier la connexion à la base de données et l'existence de la catégorie à supprimer
if (!isset($conn) || !isset($_SESSION['id_cat_delete']) || empty($_SESSION['id_cat_delete'])) {
    echo !isset($conn) ? "Database connection is not set." : "Category ID is not set or empty.";
    exit;
}

$idcatedelete = $_SESSION['id_cat_delete'];

// Récupérer les informations des produits de la catégorie et toutes les catégories
$sql = "SELECT p.id, p.product_name, pc.category_id 
        FROM products p
        INNER JOIN product_categories pc ON p.id = pc.product_id
        WHERE pc.category_id = :id_cat";
$stmt = $conn->prepare($sql);
$stmt->execute(['id_cat' => $idcatedelete]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql_categories = "SELECT id, name FROM categories";
$stmt_categories = $conn->prepare($sql_categories);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['transfer'], $_POST['new_category_id']) && !empty($_POST['new_category_id'][$_POST['transfer']])) {
        $productId = $_POST['transfer'];
        $newCategoryId = $_POST['new_category_id'][$productId];

        $sql_test = "SELECT 1 FROM product_categories WHERE product_id = :id_pro_test AND category_id = :id_cat_test";
        $stm_test = $conn->prepare($sql_test);
        $stm_test->execute(['id_pro_test' => $productId, 'id_cat_test' => $newCategoryId]);
        $exists = $stm_test->fetchColumn();

        if (!$exists) {
            $sql_update = "UPDATE product_categories SET category_id = :id_cat_attribute 
                           WHERE product_id = :id_pro AND category_id = :id_catdelete";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->execute([
                'id_cat_attribute' => $newCategoryId, 
                'id_pro' => $productId, 
                'id_catdelete' => $idcatedelete
            ]);
        } else {
            $sql_delete = "DELETE FROM product_categories 
                           WHERE product_id = :id_pro AND category_id = :id_catdelete";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->execute([
                'id_pro' => $productId, 
                'id_catdelete' => $idcatedelete
            ]);
        }
        
        header("Location: manage_pro_attribit.php");
        exit;
    } elseif (isset($_POST['attribute'])) {
        header("Location: manage_categorie.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Products</title>
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
<h1>Manage Products in Category</h1>
<form method="post" action="" enctype="multipart/form-data">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Current Category</th>
                <th>Transfer to Category</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['id']); ?></td>
                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                <td><?php echo htmlspecialchars($product['category_id']); ?></td>
                <td>
                    <select name="new_category_id[<?php echo htmlspecialchars($product['id']); ?>]">
                        <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id']); ?>" <?php echo ($category['id'] == $idcatedelete) ? 'disabled' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <button type="submit" name="transfer" value="<?php echo htmlspecialchars($product['id']); ?>">Transfer</button>
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
