<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ajouter un Rôle</title>
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

    input[type="text"] {
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
</head>
<body>
  <div class="form-container">
    <h1>Ajouter un Rôle</h1>

    <?php
    // Database connection
   
    include "../../php/db_connect.php";
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the role name from the form
        $role_name = $_POST['role_name'] ?? '';

        // Check if the role name is provided
        if (!empty($role_name)) {
            $sql = "SELECT * FROM role  WHERE name= ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$role_name]);
            if ($stmt->rowCount() > 0) {
                header("Location: addrole.php?error=La Role existe déjà.");
                exit;            
            }else{
                // Prepare the SQL statement
                $sql = "INSERT INTO role (name) VALUES (:name)";
                $stmt = $conn->prepare($sql);

                // Bind the role name parameter
                $stmt->bindParam(':name', $role_name);

                // Execute the statement
                $stmt->execute();
                header("Location: addrole.php?success=Le rôle a été ajouté avec succès");
                exit;
            }
        } else {
            header("Location: addrole.php?error=Le nom du rôle est requis.");
            exit;
           
        }
    }
    ?>
    
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

    <form action="" method="POST">
      <label for="role_name">Nom du Rôle</label>
      <input type="text" id="role_name" name="role_name" required>
      <button type="submit">Ajouter le Rôle</button>
    </form>
  </div>
</body>
</html>
