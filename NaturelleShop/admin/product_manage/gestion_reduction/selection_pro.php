
<?php
    session_start();
    include "../../../php/db_connect.php";

    // Vérification de la connexion à la base de données
    if (!isset($conn)) {
        echo "Database connection is not set.";
        exit;
    }

    if(!isset( $_SESSION['id_reduction']) || empty( $_SESSION['id_reduction'])){
        header("Location: appliquer_reduction.php?error=error dans la recuperetion du coupons id");
        exit;
    }
    $idrud=intval($_SESSION['id_reduction'])

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
    <div>
        <?php
        if (isset($_GET['error'])) {
            echo '<div class="alert alert-danger"><p>' . htmlspecialchars($_GET['error']) . '</p></div>';
        }
        if (isset($_GET['success'])) {
            echo '<div class="alert alert-success"><p>' . htmlspecialchars($_GET['success']) . '</p></div>';
        }
        ?>
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
        

        <section class="table__body">
            <!-- Formulaire pour Supprimer des Catégories -->
            <form method="post" action="pross_appliquer_reduction_simple.php">
                <h2>Produits Simple</h2>
                <table class="myTable">
                    <thead>
                        <tr>
                            <th>Id <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Nom <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Prix <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Prix comparé <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Prix de vente <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Quantité en stock <span class="icon-arrow">&UpArrow;</span></th>
                            <th>status<span class="icon-arrow">&UpArrow;</span></th>

                            <th>Date de création <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Date de mise à jour <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Appliauer</th>
                            <th>Sélectionner</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            // Requête SQL pour obtenir les produits
                            $sql = "SELECT * FROM products";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (count($rows) > 0) {                               
                                foreach ($rows as $row) {                                                            
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["product_name"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["price"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["compare_price"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["vente_price"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["stock_quantity"]) . "</td>";
                                        if($row['active']){
                                            echo "<td><p class='status delivered'>enable</p></td>";
                                        }else{
                                            echo "<td><p class='status cancelled'>disable</p></td>";
                                        }

                                        echo "<td>" . htmlspecialchars($row["created_at"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["updated_at"]) . "</td>";
                                        $sqltest="SELECT * FROM product_coupons  WHERE product_id =? AND coupon_id =?";
                                        $stmtest=$conn->prepare($sqltest);
                                        $stmtest->execute([$row['id'], $idrud]);
                                        $rustest=$stmtest->fetch();
                                        if($rustest){
                                            echo "<td><p class='status delivered'>enable</p></td>";
                                        }else{
                                            echo "<td><p class='status cancelled'>disable</p></td>";
                                        }
                                        echo "<td>";
                                        echo "<label class='container'>";
                                        echo "<input type='checkbox' name='select_id_prosimple_reduction[]' value='" . htmlspecialchars($row["id"]) . "' aria-label='Supprimer catégorie " . htmlspecialchars($row["product_name"]) . "'>";
                                        echo "<div class='checkmark'></div>";
                                        echo "</label>";
                                        echo "</td>";
                                        echo "</tr>";
                                }        
                            } else {
                                echo "<tr><td colspan='6'>Aucune Produit assignée.</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='6'>Erreur lors de la récupération des Produits assignées.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <div class="submit-button-container">
                    <button type="submit" name="selecte_pro_simple" class="submit-button">Appliquer</button>
                </div>
            </form>

            <!-- Formulaire pour Attribuer de Nouvelles Catégories -->
            <form method="post" action="pross_appliquer_reduction_composer.php">
                <h2>Components </h2>
                <table class="myTable">
                    <thead>
                        <tr>
                            <th>Id <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Nom <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Prix <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Prix comparé <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Prix de vente <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Quantité en stock <span class="icon-arrow">&UpArrow;</span></th>
                            <th>status<span class="icon-arrow">&UpArrow;</span></th>

                            <th>Date de création <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Date de mise à jour <span class="icon-arrow">&UpArrow;</span></th>
                            <th>Appliauer</th>
                            <th>Sélectionner</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $sql_components  = "SELECT * FROM components ";
                            $stmt_components  = $conn->prepare($sql_components);
                            $stmt_components->execute();
                            $rows_components = $stmt_components->fetchAll(PDO::FETCH_ASSOC);
                            if (count($rows_components) > 0) {
                                
                                foreach ($rows_components as $row) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["component_name"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["price"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["compare_price"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["vente_price"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["stock_quantity"]) . "</td>";
                                    if($row['is_active']){
                                        echo "<td><p class='status delivered'>enable</p></td>";
                                    }else{
                                        echo "<td><p class='status cancelled'>disable</p></td>";
                                    }
                                    echo "<td>" . htmlspecialchars($row["created_at"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["updated_at"]) . "</td>";
                                    $sqltest="SELECT * FROM product_Composer_coupons   WHERE components_id  =? AND coupon_id  =?";
                                        $stmtest=$conn->prepare($sqltest);
                                        $stmtest->execute([$row['id'], $idrud]);
                                        $rustest=$stmtest->fetch();
                                        if($rustest){
                                            echo "<td><p class='status delivered'>enable</p></td>";
                                        }else{
                                            echo "<td><p class='status cancelled'>disable</p></td>";
                                        }
                                    echo "<td>";
                                    echo "<label class='container'>";
                                    echo "<input type='checkbox' name='select_id_proCopmposer_reduction[]' value='" . htmlspecialchars($row["id"]) . "' aria-label='Attribuer catégorie " . htmlspecialchars($row["component_name"]) . "'>";
                                    echo "<div class='checkmark'></div>";
                                    echo "</label>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>Aucune Components disponible pour attribution.</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='6'>Erreur lors de la récupération des Components disponibles.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <div class="submit-button-container">
                    <button type="submit" name="select_procomposer" class="submit-button">Appliquer</button>
                </div>
            </form>
        </section>
    </main>
    </div>
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








