
<?php 
session_start();

include "../../php/db_connect.php";
if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}
//ADMIN
$sql = "SELECT COUNT(*) AS total_links FROM categories   ";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetch();

$total_linkscategorie = $result['total_links'];
?>


<!DOCTYPE html>
<html lang="en">
<head>

<style>
    * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: sans-serif;
            font-size: 13px;
    }
    body {
         margin: 0;
         padding: 0;
         background-color: white;
         color: #4a4a4d;
         font-family: 'Montserrat', sans-serif;
    }
   
   
    .containerrr {
          display:flex;
          
          width: 100%;
          flex-direction: column;
          height: 100vh;
               
    }

    .data {
    display: grid;
    grid-template-columns: 50% 50%;
    align-items: center; /* Centre verticalement les éléments dans chaque cellule */
    justify-items: center; /* Centre horizontalement les éléments dans chaque cellule */
    width: 100%;
    padding-top: 0;
    margin-top: 0;
    }
    .card {
         display: flex;
         flex-direction: column;
         justify-content: space-around;
         margin:10px ;

         padding: 5px;
         border-radius: 5px;
         width: 80%;
        }
    .card:nth-child(2) {
    background-color: #ff6d00;
    }
    .card-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    }
    .card-inner > .material-icons-outlined {
        font-size: 45px;
    }

    .tab{
        width: 100%;
        height: 100vh;
        align-items: center; /* Centre verticalement les éléments dans chaque cellule */
        justify-items: center;

    }
    
    .table {
            width: 94vw;
            height: 80vh;
           margin-left: 25px;
           
            background-color: #D9E8D8;
            backdrop-filter: blur(7px);
            box-shadow: 0 .4rem .8rem #0005;
            border-radius: .8rem;
            overflow: hidden;
    }
    .table__header {
            width: 100%;
            height: 15%;
            background-color: rgba(255, 255, 255, 0.333);
            padding: .8rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
    }
    
    .table__header .input-group {
            width: 35%;
            height: 100%;
            background-color: #D9E8D8;
            padding: 0 .8rem;
            border-radius: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: .2s;
        }
        .table__header .input-group:hover {
            width: 45%;
            background-color: #fff8;
            box-shadow: 0 .1rem .4rem #0002;
        }
        .table__header .input-group img {
            width: 1.2rem;
            height: 1.2rem;
        }
        .table__header .input-group input {
            width: 100%;
            padding: .5rem .8rem .5rem .8rem;
            background-color: transparent;
            border: none;
            outline: none;
        }
        .table__body {
            width: 95%;
            max-height: calc(89% - 1.6rem);
            background-color: #fffb;
            margin: .8rem auto;
            border-radius: .6rem;
            overflow: auto;
            overflow: overlay;
        }
        .table__body::-webkit-scrollbar {
            width: 0.5rem;
            height: 0.5rem;
        }
        .table__body::-webkit-scrollbar-thumb {
            border-radius: .5rem;
            background-color: #0004;
            visibility: hidden;
        }
        .table__body:hover::-webkit-scrollbar-thumb {
            visibility: visible;
        }
        table {
            width: 100%;
        }
        table, th, td {
            border-collapse: collapse;
            padding: 1rem;
            text-align: left;
        }
        thead th {
            position: sticky;
            top: 0;
            left: 0;
            background-color: #a2db9e;
            cursor: pointer;
            text-transform: capitalize;
        }
        tbody tr:nth-child(even) {
            background-color: #0000000b;
        }
        tbody tr {
            --delay: .1s;
            transition: .5s ease-in-out var(--delay), background-color 0s;
        }
        tbody tr.hide {
            opacity: 0;
            transform: translateX(100%);
        }
        tbody tr:hover {
            background-color: #fff6 !important;
        }
        tbody tr td,
        tbody tr td p,
        tbody tr td img {
            transition: .2s ease-in-out;
        }
        tbody tr.hide td,
        tbody tr.hide td p {
            padding: 0;
            font: 0 / 0 sans-serif;
            transition: .2s ease-in-out .5s;
        }
        tbody tr.hide td {
            width: 0;
            height: 0;
            transition: .2s ease-in-out .5s;
        }
        @media (max-width: 1000px) {
            td:not(:first-of-type) {
                min-width: 12.1rem;
            }
        }
        thead th span.icon-arrow {
            display: inline-block;
            width: 1.3rem;
            height: 1.3rem;
            border-radius: 50%;
            border: 1.4px solid transparent;
            text-align: center;
            font-size: 1rem;
            margin-left: .5rem;
            transition: .2s ease-in-out;
        }
        thead th:hover span.icon-arrow {
            border: 1.4px solid #6c00bd;
        }
        thead th:hover {
            color: #6c00bd;
        }
        thead th.active span.icon-arrow {
            background-color: #6c00bd;
            color: #fff;
        }
        thead th.asc span.icon-arrow {
            transform: rotate(180deg);
        }
        thead th.active, tbody td.active {
            color: #6c00bd;
        }
        .export__file {
            position: relative;
        }
        .export__file .export__file-btn {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            background: #fff6 url(../images/export.png) center / 80% no-repeat;
            border-radius: 50%;
            transition: .2s ease-in-out;
        }
        .export__file .export__file-btn:hover {
            background-color: #fff;
            transform: scale(1.15);
            cursor: pointer;
        }
        .export__file input {
            display: none;
        }
        .export__file .export__file-options {
            position: absolute;
            right: 0;
            width: 12rem;
            border-radius: .5rem;
            overflow: hidden;
            text-align: center;
            opacity: 0;
            transform: scale(.8);
            transform-origin: top right;
            box-shadow: 0 .2rem .5rem #0004;
            transition: .2s;
        }
        .export__file input:checked + .export__file-options {
            opacity: 1;
            transform: scale(1);
            z-index: 100;
        }
        .export__file .export__file-options label {
            display: block;
            width: 100%;
            padding: .6rem 0;
            background-color: #f2f2f2;
            display: flex;
            justify-content: space-around;
            align-items: center;
            transition: .2s ease-in-out;
        }
        .export__file .export__file-options label:first-of-type {
            padding: 1rem 0;
            background-color: #86e49d !important;
        }
        .export__file .export__file-options label:hover {
            transform: scale(1.05);
            background-color: #fff;
            cursor: pointer;
        }
        .export__file .export__file-options img {
            width: 2rem;
            height: auto;
        }
        .status {
            padding: .4rem 0;
            border-radius: 2rem;
            text-align: center;
        }
        .status.delivered {
            background-color: #86e49d;
            color: #006b21;
        }
        .status.cancelled {
            background-color: #d893a3;
            color: #b30021;
        }
        .status.pending {
            background-color: #ebc474;
        }
        .status.shipped {
            background-color: #6fcaea;
        }
        .container {
            display: block;
            height: 1.5em;
            width: 1.5em;
            cursor: pointer;
            position: relative;
        }
        .container input {
            position: absolute;
            transform: scale(0);
        }
        .container input:checked ~ .checkmark {
            transform: rotate(45deg);
            height: 2em;
            width: .7em;
            border-color: #32cd32;
            border-top-color: transparent;
            border-left-color: transparent;
            border-radius: 0;
        }
        .container .checkmark {
            display: block;
            width: inherit;
            height: inherit;
            border: 2px solid #32cd32;
            border-radius: 4px;
            transition: all .3s;
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
     
  @media screen and (max-width:700px) {

    
    .table{
        width: 100%;
        margin-left: 0;
    }

    
  }
  #cont{
    overflow: hidden;
  }
 



</style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
     
    <!-- Montserrat Font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    
</head>
<body>
    <div class="containerrr" id="cont">
           <div class="data">
                   <h3>DASHBOARD categories</h3>
                    <div class="card">
                        <div class="card-inner">
                            <h4>users</h4>
                        <span class="material-icons-outlined">groups</span>
                        </div>
                        <h4><?php echo  $total_linkscategorie ?></h4>
                    </div>
            </div>
            <div class="tab">
            
                <main class="table">
                                    <section class="table__header">
                                        <h1>users Panel </h1>
                                        <div class="input-group">
                                            <input type="search" placeholder="Search...">
                                            <img src="../images/search.png" alt="">
                                        </div>
                                        <div class="export__file">
                                            <label for="export-file" class="export__file-btn" title="Export File"></label>
                                            <input type="checkbox" id="export-file">
                                            <div class="export__file-options">
                                                <label>Export As &nbsp; &#10140;</label>
                            
                                                <label for="export-file" id="toJSON">JSON <img src="../images/json.png" alt=""></label>
                                                <label for="export-file" id="toCSV">CSV <img src="../images/csv.png" alt=""></label>
                                                <label for="export-file" id="toEXCEL">EXCEL <img src="../images/excel.png" alt=""></label>
                                            </div>
                                        </div>
                                    </section>
                                    <section class="table__body">
                                            <table class="tble1">
                                                <thead>
                                                    <tr>
                                                        <th>Id <span class="icon-arrow">&UpArrow;</span></th>
                                                        <th>Name <span class="icon-arrow">&UpArrow;</span></th>
                                                        <th> Parent Id<span class="icon-arrow">&UpArrow;</span></th>
                           
                                                        <th> nombre du pro<span class="icon-arrow">&UpArrow;</span></th>
                                                        <th>Date created <span class="icon-arrow">&UpArrow;</span></th>
                                                        
                                                        <th>Date updated<span class="icon-arrow">&UpArrow;</span></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                                    <?php


                                                                    // Requête SQL pour obtenir les données
                                                                    $sql = "SELECT id, name , parent_id,created_at,updated_at   FROM categories  ";
                                                                    $stmt = $conn->prepare($sql);
                                                                    $stmt->execute();

                                                                    // Vérifier s'il y a des résultats et les afficher
                                                                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                                    if (count($rows) > 0) {
                                                                        foreach ($rows as $row) {
                                                                            $id=$row['id'];
                                                                            echo "<tr>";
                                                                            echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                                                                            echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                                                                            echo "<td>" . $row["parent_id"]. "</td>";
                                                                            $subsql="SELECT  COUNT(product_id) AS product_count 
                                                                                    FROM 
                                                                                        product_categories 
                                                                                    where  
                                                                                        category_id=:id";
                                                                            $substmt = $conn->prepare($subsql);
                                                                            $substmt->execute(['id'=>$id]);
                                                                            $nbrpro = $substmt->fetch();
                                                                            echo "<td>" . htmlspecialchars($nbrpro["product_count"]) . "</td>";

                                                                            
                                                                                                                                                                echo "<td>" . htmlspecialchars($row["created_at"]) . "</td>";
                                                                            echo "<td>" . htmlspecialchars($row["updated_at"]) . "</td>";
                                                                            echo "</tr>";
                                                
                                                                        }
                                                                           
                                                                    } else {
                                                                        echo "<tr><td colspan='9'>No data available</td></tr>";
                                                                    }
                                                                    ?>
                                                    
                                                </tbody>
                                            </table>
                                    </section>
                </main>
            </div>
            </div>

    <!-- Scripts -->
    <!-- ApexCharts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>

    <!-- SheetJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>

  
     <script>
const search = document.querySelector('.input-group input'),
    table_rows = document.querySelectorAll('tbody tr'),
    table_headings = document.querySelectorAll('thead th');

// 1. Searching for specific data of HTML table
search.addEventListener('input', searchTable);

function searchTable() {
    table_rows.forEach((row, i) => {
        let table_data = row.textContent.toLowerCase(),
            search_data = search.value.toLowerCase();

        row.classList.toggle('hide', table_data.indexOf(search_data) < 0);
        row.style.setProperty('--delay', i / 25 + 's');
    });

    document.querySelectorAll('tbody tr:not(.hide)').forEach((visible_row, i) => {
        visible_row.style.backgroundColor = (i % 2 == 0) ? 'transparent' : '#0000000b';
    });
}

// 2. Sorting | Ordering data of HTML table
table_headings.forEach((head, i) => {
    let sort_asc = true;
    head.onclick = () => {
        table_headings.forEach(head => head.classList.remove('active'));
        head.classList.add('active');

        document.querySelectorAll('td').forEach(td => td.classList.remove('active'));
        table_rows.forEach(row => {
            row.querySelectorAll('td')[i].classList.add('active');
        });

        head.classList.toggle('asc', sort_asc);
        sort_asc = head.classList.contains('asc') ? false : true;

        sortTable(i, sort_asc);
    }
});

function sortTable(column, sort_asc) {
    [...table_rows].sort((a, b) => {
        let first_row = a.querySelectorAll('td')[column].textContent.toLowerCase(),
            second_row = b.querySelectorAll('td')[column].textContent.toLowerCase();

        return sort_asc ? (first_row < second_row ? 1 : -1) : (first_row < second_row ? -1 : 1);
    })
    .map(sorted_row => document.querySelector('tbody').appendChild(sorted_row));
}


// 4. Converting HTML table to JSON
const json_btn = document.querySelector('#toJSON');
const toJSON = function () {
    let table_data = [],
        t_head = [],
        t_headings = document.querySelectorAll('thead th'),
        t_rows = document.querySelectorAll('tbody tr');

    for (let t_heading of t_headings) {
        let actual_head = t_heading.textContent.trim().split(' ');
        t_head.push(actual_head.splice(0, actual_head.length - 1).join(' ').toLowerCase());
    }

    t_rows.forEach(row => {
        const row_object = {},
            t_cells = row.querySelectorAll('td');

        t_cells.forEach((t_cell, cell_index) => {
            row_object[t_head[cell_index]] = t_cell.textContent.trim();
        });
        table_data.push(row_object);
    });

    const json = JSON.stringify(table_data, null, 4);
    downloadFile(json, 'json', 'table_data.json');
}
json_btn.onclick = toJSON;

// 5. Converting HTML table to CSV
const csv_btn = document.querySelector('#toCSV');
const toCSV = function () {
    const t_heads = document.querySelectorAll('thead th'),
        tbody_rows = document.querySelectorAll('tbody tr');

    const headings = [...t_heads].map(head => head.textContent.trim()).join(',') + '\n';
    const table_data = [...tbody_rows].map(row => {
        const cells = row.querySelectorAll('td');
        return [...cells].map(cell => cell.textContent.trim()).join(',');
    }).join('\n');

    const csv = headings + table_data;
    downloadFile(csv, 'csv', 'table_data.csv');
}
csv_btn.onclick = toCSV;

// 6. Converting HTML table to EXCEL
const excel_btn = document.querySelector('#toEXCEL');
const toExcel = function () {
    const ws = XLSX.utils.table_to_sheet(document.querySelector('table'));
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
    XLSX.writeFile(wb, 'table_data.xlsx');
}
excel_btn.onclick = toExcel;

// Function to download file
const downloadFile = function (data, fileType, fileName) {
    const a = document.createElement('a');
    a.download = fileName;
    const mime_types = {
        'json': 'application/json',
        'csv': 'text/csv',
        'excel': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    }
    a.href = `data:${mime_types[fileType]};charset=utf-8,${encodeURIComponent(data)}`;
    document.body.appendChild(a);
    a.click();
    a.remove();
}
</script>

   

</body>
</html>