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
            max-height: 90vh; /* Ensure the container doesn't exceed the viewport height */
            overflow-y: auto; /* Add scroll to the container */
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
        select, button {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 0.25rem;
            box-sizing: border-box;
        }
        button {
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
        .roles-list {
            max-height: 200px; /* Limit the height of the roles list */
            overflow-y: auto; /* Add scroll to the roles list */
            margin-bottom: 1rem;
        }
        .roles-list table {
            width: 100%;
            border-collapse: collapse;
        }
        .roles-list th, .roles-list td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attribuer un Rôle</title>
</head>
<body>
    <div class="form-container">
        <h1>Attribuer un Rôle</h1>
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <p>Les rôles de cet admin :</p>
        <div class="roles-list">
            <table class="table">
                <?php 
                    session_start();
                    include "../../php/db_connect.php";
                    if (!isset($conn)) {
                        echo "Database connection is not set.";
                        exit;
                    }

                    if(isset($_SESSION['Admin_id'])){
                        $Admin_id = $_SESSION['Admin_id'];
                        $sql = "SELECT role.id, role.name
                                FROM role
                                INNER JOIN admin_role ON role.id = admin_role.role_id
                                INNER JOIN admin ON admin.id = admin_role.admin_id
                                WHERE admin.id = :id";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute(['id' => $Admin_id]);
                        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if (count($rows) > 0) {
                            foreach ($rows as $row) {
                                echo "<tr><td>" . htmlspecialchars($row['name']) . "</td></tr>";
                            }
                        } else {
                            echo "<tr><td>Aucun rôle disponible</td></tr>";
                        }
                    } else {
                        header("Location: Attributes.php?error=La sélection de l'admin est vide");
                        exit;
                    }
                ?>
            </table>
        </div>

        <form action="" method="POST">
            <label for="role_id">Sélectionnez un Rôle :</label>
            <select name="role_id" id="role_id" required>
                <?php
                    $sql = "SELECT id, name
                            FROM role
                            WHERE id NOT IN (
                                SELECT role.id
                                FROM role
                                INNER JOIN admin_role ON role.id = admin_role.role_id
                                WHERE admin_role.admin_id = :id
                            )";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute(['id' => $Admin_id]);
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (count($rows) > 0) {
                        foreach ($rows as $row) {
                            echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>Aucun rôle disponible</option>";
                    }
                ?>
            </select>
            <button type="submit" name="assign_role">Assigner le rôle</button>
        </form>
    </div>

    <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_role'])) {
            $role_id = $_POST['role_id'];
            if (!empty($role_id)) {
                $sql = "INSERT INTO admin_role (admin_id, role_id) VALUES (:admin_id, :role_id)";
                $stmt = $conn->prepare($sql);
                if ($stmt->execute(['admin_id' => $Admin_id, 'role_id' => $role_id])) {
                    header("Location: Attributes.php?success=Rôle attribué avec succès");
                    exit;
                } else {
                    header("Location: Attributes.php?error=Erreur lors de l'attribution du rôle");
                    exit;
                }
            } else {
                header("Location: Attributes.php?error=Aucun rôle sélectionné");
                exit;
            }
        }
    ?>
</body>
</html>
