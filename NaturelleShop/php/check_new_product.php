    <?php 

        // Inclure le fichier de connexion à la base de données
        include "./db_connect.php";

        // Vérifier si la connexion à la base de données est établie
        if (!isset($conn)) {
            echo "Database connection is not set.";
            exit;
        }
         // Récupérer le dernier produit ajouté aujourd'hui
    $sql = "SELECT p.id AS product_id, p.product_name, p.created_at, g.image AS image_path
    FROM products p
    LEFT JOIN variant_options v ON p.id = v.product_id
    LEFT JOIN gallery g ON v.id = g.product_variant_id
    WHERE DATE(p.created_at) = CURDATE()
    ORDER BY p.created_at DESC, g.is_thumbnail DESC, g.created_at ASC
    LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
// Si l'image est NULL, utiliser une image par défaut
if (is_null($result['image_path'])) {
    $result['image_path'] = 'default.webp';  // Nom de l'image par défaut
}

// Retourner les données sous forme de JSON
echo json_encode($result);
} else {
echo json_encode(['error' => 'No products added today.']);
}


   
     
    ?>

