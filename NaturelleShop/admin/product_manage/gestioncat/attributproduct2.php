<?php
    session_start();
    include "../../../php/db_connect.php";

    // Vérification de la connexion à la base de données
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }

    // Vérification de l'ID du produit dans la session
    if (!isset($_SESSION['idprosimplefunction']) || empty($_SESSION['idprosimplefunction'])) {
        header("Location: ../selectionpro.php?error=Erreur: Aucune sélection de produit.");
        exit();
    }

    $idpro = intval($_SESSION['idprosimplefunction']);
?>

<!DOCTYPE html>
<html lang="fr">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gestion des Catégories du Produit</title>
    <link rel="stylesheet" href="../assets/css/styleselecte.css">
    <style>
        /* Ajout de styles pour les messages */
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <main class="table">
        <section class="table__header">
            <div class="input-group">
                <input type="search" placeholder="Recherche..." id="searchInput">
                <img src="../../images/search.png" alt="Icone de recherche">
            </div>
            <div class="export__file">
                <label for="export-file" class="export__file-btn" title="Exporter le fichier"></label>
                <input type="checkbox" id="export-file">
                <div class="export__file-options">
                    <label>Exporter en &nbsp; &#10140;</label>
                    <label for="export-file" id="toJSON">JSON <img src="../../images/json.png" alt="JSON"></label>
                    <label for="export-file" id="toCSV">CSV <img src="../../images/csv.png" alt="CSV"></label>
                    <label for="export-file" id="toEXCEL">EXCEL <img src="../../images/excel.png" alt="EXCEL"></label>
                </div>
            </div>
        </section>

        <!-- Affichage des messages d'erreur ou de succès -->
        <?php
        /*
        if (isset($_GET['error'])) {
            echo '<div class="message error-message"><p>' . htmlspecialchars($_GET['error']) . '</p></div>';
        }
        if (isset($_GET['success'])) {
            echo '<div class="message success-message"><p>' . htmlspecialchars($_GET['success']) . '</p></div>';
        }*/
        ?>

        <section class="table__body">
            <!-- Formulaire pour Supprimer des Catégories -->
            <form method="post" action="prossAttrubu2.php">
                <h2>Catégories Assignées</h2>
                <table class="myTable">
                    <thead>
                        <tr>
                            <th>Id <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Nom <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Parent Id <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Date de création <span class="icon-arrow">&UpArrow;</span></th>                                                       
                            <th>Date de mise à jour <span class="icon-arrow">&UpArrow;</span></th>   
                            <th>Supprimer <span class="icon-arrow">&UpArrow;</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            // Récupérer les catégories assignées au produit
                            $sql1 = "SELECT category_id FROM product_categories WHERE product_id = ?";
                            $stmt1 = $conn->prepare($sql1);
                            $stmt1->execute([$idpro]);
                            $rows1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);

                            if (count($rows1) > 0) {
                               
                                foreach ($rows1 as $row) {
                                    $category_id = intval($row['category_id']);
                                    
                                    // Récupérer les détails de la catégorie
                                    $subsql = "SELECT * FROM categories WHERE id = ?";
                                    $substm = $conn->prepare($subsql);
                                    $substm->execute([$category_id]);
                                    $catdeja = $substm->fetch(PDO::FETCH_ASSOC);
                                    
                                    if ($catdeja) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($catdeja["id"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($catdeja["name"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($catdeja["parent_id"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($catdeja["created_at"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($catdeja["updated_at"]) . "</td>";
                                        echo "<td>";
                                        echo "<label class='container'>";
                                        echo "<input type='checkbox' name='select_idcat_delete[]' value='" . htmlspecialchars($catdeja["id"]) . "' aria-label='Supprimer catégorie " . htmlspecialchars($catdeja["name"]) . "'>";
                                        echo "<div class='checkmark'></div>";
                                        echo "</label>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                }
                            } else {
                                echo "<tr><td colspan='6'>Aucune catégorie assignée.</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='6'>Erreur lors de la récupération des catégories assignées.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <div class="submit-button-container">
                    <button type="submit" name="delete_categories" class="submit-button">Supprimer les Catégories Sélectionnées</button>
                </div>
            </form>

            <!-- Formulaire pour Attribuer de Nouvelles Catégories -->
            <form method="post" action="prossAttrubu2.php">
                <h2>Attribuer de Nouvelles Catégories</h2>
                <table class="myTable">
                    <thead>
                        <tr>
                            <th>Id <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Nom <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Parent Id <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Date de création <span class="icon-arrow">&UpArrow;</span></th>                                                       
                            <th>Date de mise à jour <span class="icon-arrow">&UpArrow;</span></th>   
                            <th>Attribuer <span class="icon-arrow">&UpArrow;</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            // Préparer les IDs des catégories déjà assignées
                            $assigned_ids = array_map(function($row) {
                                return intval($row['category_id']);
                            }, $rows1);

                            if (count($assigned_ids) > 0) {
                                // Créer une liste sécurisée pour la requête
                                $placeholders = implode(',', array_fill(0, count($assigned_ids), '?'));
                                $sql2 = "SELECT * FROM categories WHERE id NOT IN ($placeholders)";
                                $stmt2 = $conn->prepare($sql2);
                                $stmt2->execute($assigned_ids);
                            } else {
                                // Si aucune catégorie n'est assignée, sélectionner toutes les catégories
                                $sql2 = "SELECT * FROM categories";
                                $stmt2 = $conn->prepare($sql2);
                                $stmt2->execute();
                            }

                            $rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                            if (count($rows2) > 0) {
                                
                                foreach ($rows2 as $row) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["parent_id"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["created_at"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["updated_at"]) . "</td>";
                                    echo "<td>";
                                    echo "<label class='container'>";
                                    echo "<input type='checkbox' name='select_idcat_attribuer[]' value='" . htmlspecialchars($row["id"]) . "' aria-label='Attribuer catégorie " . htmlspecialchars($row["name"]) . "'>";
                                    echo "<div class='checkmark'></div>";
                                    echo "</label>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>Aucune catégorie disponible pour attribution.</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='6'>Erreur lors de la récupération des catégories disponibles.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <div class="submit-button-container">
                    <button type="submit" name="assign_categories" class="submit-button">Attribuer les Catégories Sélectionnées</button>
                </div>
            </form>
        </section>
    </main>
    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>
    <script>
    // Sélectionner toutes les tables avec la classe 'myTable'
    const tables = document.querySelectorAll('.myTable');
    const search = document.querySelector('.input-group input');
    tables.forEach((table, index) => {
  const sheetName = `Sheet${index + 1}`;
  console.log(sheetName); // Affiche: "Sheet1", "Sheet2", etc.
});

    // Fonction de recherche dans les tables
    function searchTable() {
        tables.forEach(table => {
            const rows = table.querySelectorAll('tbody tr');
            const searchValue = search.value.toLowerCase();

            rows.forEach((row, i) => {
                const cells = row.querySelectorAll('td');
                const rowText = Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(' ');
                const isVisible = rowText.includes(searchValue);
                row.classList.toggle('hide', !isVisible);
                row.style.setProperty('--delay', i / 25 + 's');
            });

            // Alterner la couleur de fond des lignes visibles
            table.querySelectorAll('tbody tr:not(.hide)').forEach((visible_row, i) => {
                visible_row.style.backgroundColor = (i % 2 === 0) ? 'transparent' : '#0000001b';
            });
        });
    }

    // Ajouter l'événement de recherche
    search.addEventListener('input', searchTable);





    // Fonction de tri
function sortTable(column, sort_asc, tbody) {
    const rowsArray = Array.from(tbody.querySelectorAll('tr')); // Convertir NodeList en tableau

    rowsArray.sort((a, b) => {
        // Récupérer le texte des cellules à comparer
        let first_row = a.querySelectorAll('td')[column].textContent.trim();
        let second_row = b.querySelectorAll('td')[column].textContent.trim();

        // Vérifier si les valeurs sont numériques
        let first_number = parseFloat(first_row.replace(/,/g, '')); // Retirer les virgules des nombres
        let second_number = parseFloat(second_row.replace(/,/g, ''));

        // Comparer en fonction du type (nombre ou texte)
        if (!isNaN(first_number) && !isNaN(second_number)) {
            return sort_asc ? first_number - second_number : second_number - first_number;
        } else {
            return sort_asc ? first_row.localeCompare(second_row) : second_row.localeCompare(first_row);
        }
    });

    // Réinsérer les lignes triées dans le tableau
    rowsArray.forEach(row => tbody.appendChild(row));
}

// 2. Sorting | Ordering data of HTML table

tables.forEach(table => {
    const table_headings = table.querySelectorAll('thead th');
    const tbody = table.querySelector('tbody'); // Utiliser querySelector pour obtenir un seul tbody

    table_headings.forEach((head, i) => {
        let sort_asc = true;  // Variable pour contrôler l'ordre de tri
        head.onclick = () => {
            // Supprimer la classe 'active' de tous les en-têtes
            table_headings.forEach(h => h.classList.remove('active', 'asc', 'desc'));
            // Ajouter la classe 'active' à l'en-tête cliqué
            head.classList.add('active');
            head.classList.toggle('asc', sort_asc);
            head.classList.toggle('desc', !sort_asc);

            // Trier les lignes du tableau
            sortTable(i, sort_asc, tbody);

            // Inverser l'ordre de tri pour le prochain clic
            sort_asc = !sort_asc;
        };
    });
});



// Fonction pour télécharger un fichier
function downloadFile(content, type, filename) {
    const blob = new Blob([content], { type: `application/${type}` });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    URL.revokeObjectURL(url);
}

// Fonction pour convertir la table HTML en JSON
function toJSON() {
    tables.forEach(table => {
        let tableData = [];
        const tHeadings = table.querySelectorAll('thead th');
        const tRows = table.querySelectorAll('tbody tr');

        // Collecte des en-têtes de colonnes
        let tHead = [];
        tHeadings.forEach(tHeading => {
            let actualHead = tHeading.textContent.trim().split(' ');
            tHead.push(actualHead.splice(0, actualHead.length - 1).join(' ').toLowerCase());
        });

        // Collecte des données des lignes
        tRows.forEach(row => {
            const rowObject = {};
            const tCells = row.querySelectorAll('td');

            tCells.forEach((tCell, cellIndex) => {
                rowObject[tHead[cellIndex]] = tCell.textContent.trim();
            });
            tableData.push(rowObject);
        });

        const json = JSON.stringify(tableData, null, 4);
        downloadFile(json, 'json', 'table_data.json');
    });
}

// Ajouter l'événement de clic au bouton JSON
const jsonBtn = document.querySelector('#toJSON');
jsonBtn.addEventListener('click', toJSON);



// Fonction pour convertir la table HTML en CSV
function toCSV() {
    tables.forEach(table => {
        const tHeads = table.querySelectorAll('thead th');
        const tbodyRows = table.querySelectorAll('tbody tr');
        
        // Collecte des en-têtes de colonnes
        const headings = [...tHeads].map(head => head.textContent.trim()).join(',') + '\n';
        
        // Collecte des données des lignes
        const tableData = [...tbodyRows].map(row => {
            const cells = row.querySelectorAll('td');
            return [...cells].map(cell => cell.textContent.trim()).join(',');
        }).join('\n');
        
        const csv = headings + tableData;
        downloadFile(csv, 'csv', 'table_data.csv');
    });
}

// Ajouter l'événement de clic au bouton CSV
const csvBtn = document.querySelector('#toCSV');
csvBtn.addEventListener('click', toCSV);


function toExcel() {
    const wb = XLSX.utils.book_new(); // Crée un nouveau workbook

    tables.forEach((table, index) => {
        const ws = XLSX.utils.table_to_sheet(table); // Convertit chaque table en feuille
        const sheetName = `Sheet${index + 1}`; // Nom unique pour chaque feuille
        XLSX.utils.book_append_sheet(wb, ws, sheetName); // Ajoute la feuille au workbook
    });

    XLSX.writeFile(wb, 'table_data.xlsx'); // Télécharge le fichier Excel
}

// Ajouter l'événement de clic au bouton EXCEL
const excelBtn = document.querySelector('#toEXCEL');
excelBtn.addEventListener('click', toExcel);


document.querySelector('form[action="prossAttrubu2.php"]').addEventListener('submit', function(e) {
    const confirmDelete = confirm("Êtes-vous sûr de vouloir supprimer les catégories sélectionnées ?");
    if (!confirmDelete) {
        e.preventDefault(); // Annuler la soumission du formulaire si l'utilisateur ne confirme pas
    }
});




</script>

</body>
</html>





