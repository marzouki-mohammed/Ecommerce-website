<!DOCTYPE html>
<html lang="en">
<head>
<style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      background-color: white;
      font-family: Arial, sans-serif;
    }

    .form-container {
      background: #fff;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width: 300px;     
          
    }

    h1 {
      
      margin-bottom: 20px;
      color: #333;
      text-align: center;
    }
    .form-container label {
            display: block;
            margin-bottom: 5px;
            color: #555;
    }

    select{
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 0.25rem;
      box-sizing: border-box;
    }

    button {
      width: 100%;
      padding: 0.75rem;
      border: none;
      border-radius: 0.25rem;
      background-color: #70c489;
      color: #ffffff;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button:hover {
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
  </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attributes un Rôle</title>
</head>
<body>
  <div class="form-container">



    <h1>Attributes un Rôle</h1>
    <?php if(isset($_GET['error'])){ ?>
                    <div class="alert alert-danger" role="alert" >
                        <?php echo $_GET['error']; ?>
                    </div>
    <?php } ?>
    <?php if(isset($_GET['success'])){ ?>
                    <div class="alert alert-success" role="alert" >
                        <?php echo $_GET['success']; ?>
                    </div>
    <?php } ?>
    <form action="" method="POST" >
        <label for="Admin_id">Sélectionnez un Admin :</label>
        <select name="Admin_id" id="Admin_id" required>
            <?php
            
// Database connection
   
              include "../../php/db_connect.php";
              if (!isset($conn)) {
                     echo "Database connection is not set.";
                     exit;
               }
            // Récupération des rôles depuis la base de données
            $sql = "SELECT id, username FROM admin where username !='root'";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            // Vérifier s'il y a des résultats et les afficher
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['username']) . "</option>";
                }
            } else {
                echo "<option value=''>Aucun rôle disponible</option>";
            }
            ?>
        </select>
        <button type="submit" name="choix_Admin">choix l'admin</button>
    </form>
    </div>
    <?php
    // Database connection
    session_start();
   
    include "../../php/db_connect.php";
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['choix_Admin']) && isset($_POST['Admin_id'])) {
        $role_id = intval($_POST['Admin_id']);
        if(!empty($role_id)){
            $_SESSION['Admin_id']=$role_id;
            header("Location: AttributesSuite.php?success=La choix du l'admin avec succès.");
            exit;

        }else {
        header("Location: Attributes.php?error=Aucun Admin sélectionné ");
        exit;
        }
    }else{
        header("Location: Attributes.php?error=Error ");
        exit;
    }
}    

?>

</body>
</html>
