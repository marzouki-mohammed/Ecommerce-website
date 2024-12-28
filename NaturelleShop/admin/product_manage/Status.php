
<?php
    session_start();
    include "../../php/db_connect.php";

    // Vérification de la connexion à la base de données
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <style>
        body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        
        
        }

        .menu {
            background-color:#ffffff;
            padding: 10px;
        }

        .menu ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .menu ul li {
            margin-bottom: 10px;
        }

        .menu ul li button {
            width: 100%;
            padding: 10px;
            background-color: #555;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            text-align: left;
        }

        .menu ul li button:hover {
            background-color: #777;
        }

       

    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Menu de Gestion</title>
   
</head>
<body>
    <nav class="menu">
        <p><strong>Menu</strong></p>
        <ul>           
            <li><button onclick="redirect('active_pro/Update_status_comp.php')">Update status product composer</button></li>   
            <li><button onclick="redirect('active_pro/Update_status.php')">Update status product simple</button></li>  
            <li><button onclick="redirect('active_pro/Update_stock.php')">Update stock product composer</button></li>                        
        </ul>
    </nav>

    

    <script>
        // Fonction de redirection vers une nouvelle page PHP
        function redirect(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
