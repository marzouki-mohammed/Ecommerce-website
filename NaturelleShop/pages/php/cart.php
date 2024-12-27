<?php
session_start();
include "../../php/db_connect.php"; // Adjust path if necessary

if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

// Check if the session cart is not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // Initialize the cart if it doesn't exist
}

// Process the form if it is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST"
    && isset($_POST['typpro']) && !empty($_POST['typpro'])
    && isset($_POST['idprocart']) && !empty($_POST['idprocart'])
    && isset($_POST['prixcat']) && !empty($_POST['prixcat'])
    && isset($_POST['qty']) && !empty($_POST['qty'])
) {
    $type = intval($_POST['typpro']);
    $id = intval($_POST['idprocart']);
    $prix = floatval($_POST['prixcat']);
    $quantity = intval($_POST['qty']);
    $options = [];

    if ($type == 1) {
         // Fetch product details
         $sql = "SELECT * FROM products WHERE id=?";
         $stmt = $conn->prepare($sql);
         $stmt->execute([$id]);
         $result = $stmt->fetch();
        if($result && $result['stock_quantity'] >= $quantity ){
            
            // Handle the case where there is a single product variant
            if (isset($_POST['idprokeycart']) && !empty($_POST['idprokeycart']) && isset($_POST['product']) && !empty($_POST['product'])) {
                $idpro = intval($_POST['idprokeycart']);
                $idvar = intval($_POST['product']);
                $test="SELECT quantity FROM variant_options WHERE id=?";
                $stmtest = $conn->prepare($test);
                $stmtest->execute([$idvar]);
                $datatest = $stmtest->fetch();
                if($datatest && $datatest['quantity'] >= $quantity ){
                    $title = $result['product_name'];
                    $options[$idpro] = $idvar;

                    // Add to cart
                    $_SESSION['cart'][] = [
                        'type' => $type,
                        'id' => $id,
                        'title' => $title,
                        'quantity' => $quantity,
                        'price' => $prix,
                        'option' => $options
                    ];

                }else{
                    header("Location: ../productpage.php?errorcart=Insufficient stock or data retrieval error");
                    exit;
                }

                
            } else {
                header("Location: ../productpage.php?errorcart=Invalid form data");
                exit;
            }

        }else{
            header("Location: ../productpage.php?errorcart=Insufficient stock or data retrieval error");
            exit;
        }
        
    } elseif ($type == 2) {

         // Handle the case where there are multiple product components
         $sql = "SELECT * FROM components WHERE id=?";
         $stmt = $conn->prepare($sql);
         $stmt->execute([$id]);
         $result = $stmt->fetch();
        if($result && $result['stock_quantity'] >= $quantity ){
    
            $title = $result['component_name']; // Adjust field name if necessary
            $i = 1;

            while (isset($_POST["idprokeycart$i"]) && !empty($_POST["idprokeycart$i"]) && isset($_POST["productCopm$i"]) && !empty($_POST["productCopm$i"])) {
                $idpro = intval($_POST["idprokeycart$i"]);
                $idvar = intval($_POST["productCopm$i"]);
                $options[$idpro] = $idvar;
                $i++;
            }

            // Add to cart
            $_SESSION['cart'][] = [
                'type' => $type,
                'id' => $id,
                'title' => $title,
                'quantity' => $quantity,
                'price' => $prix,
                'option' => $options
            ];

        }else{
            header("Location: ../productpage.php?errorcart=Insufficient stock or data retrieval error");
            exit;
        }
        
    }

    // Redirect after adding to cart
    header("Location: ../productpage.php");
    exit();
} else {
    header("Location: ../productpage.php?errorcart=error");
    exit();
}
?>
