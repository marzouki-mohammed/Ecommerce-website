<?php 
session_start();

 include "../../php/db_connect.php";

if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
}

if(!isset($_SESSION['id_cat_delete']) || empty($_SESSION['id_cat_delete'])){
    header("Location: delete.php?error=Category ID is not set.");
     exit;
}
$idcat=intval($_SESSION['id_cat_delete']);
$sql_cat="SELECT *FROM categories  WHERE id=?";
$stm_cat=$conn->prepare($sql_cat);
$stm_cat->execute([$idcat]);
$result_cat=$stm_cat->fetch();
if(!$result_cat){
    header("Location: delete.php?error=Category ID is not set.");
    exit;
}



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
                                }
                                body{
                                    min-height: 100vh;
                                    display: flex;
                                    justify-content: center;
                                    align-items: center;

                                }
                                main.table {
                                    width: 95vw;
                                    height: 90vh;
                                    background-color: #D9E8D8;
                                    backdrop-filter: blur(7px);
                                    box-shadow: 0 .4rem .8rem #0005;
                                    border-radius: .8rem;
                                    overflow: hidden;
                                }
                                .table__header {
                                    width: 100%;
                                    height: 25%;
                                    background-color: rgba(255, 255, 255, 0.333);
                                    padding: .3rem .5rem;
                                    display: grid;
                                    grid-template-columns: 90% 10%;
                                    gap: 5PX;
                                    align-items: center;
                                }
                                .info{
                                display: grid;
                                grid-template-columns: 40% 60% ;
                                }






                        .content_search {
                        display: flex;
                        justify-content: center; 
                        align-items: center;     
                        width: 100%;             
                        }
                        .content_search .input-group {
                        width: 50%;
                        height: 60%;
                        background-color: #D9E8D8;
                        padding: 0 .8rem;
                        border-radius: 2rem;
                        display: flex;
                        justify-content: center; 
                        align-items: center;     /* Centers the content vertically */           
                        transition: .2s;
                        }




                        .content_search .input-group:hover {
                        width: 45%;
                        background-color: #fff8;
                        box-shadow: 0 .1rem .4rem #0002;
                        }
                        .content_search .input-group img {
                        width: 1.1rem;
                        height: 1.1rem;
                        }
                        .content_search .input-group input {
                        width: 100%;
                        padding: .5rem .8rem .5rem .8rem;
                        background-color: transparent;
                        border: none;
                        outline: none;
                        }



                        .table__body {
                        width: 95%;
                        max-height: calc(65% - 1.6rem);
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
                        height: 95%;
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



                        @media (max-width: 800px) {
                        main.table {
                            width: 100vw;
                            height: 100vh;
                        }

                        .info{
                        display: flex;
                            flex-direction: column;
                        }
                        .table__header{
                            height: 40%;
                            padding-top: 0;
                            padding-bottom: 0;
                            
                            padding-right: 5PX;
                            grid-template-columns: 80% 20%;
                        }
                        .content_search .input-group {
                                width: 90%;
                                height: 90%;
                                margin-top: 10PX;
                        }
                        .content_search .input-group:hover {
                        width: 85%;
                        background-color: #fff8;
                        box-shadow: 0 .1rem .4rem #0002;
                        }

                        .export__file-btn{
                        top: 0;
                        }

                        }
                        select {
                        background-color: transparent;
                        border: 2px solid #0ea021;
                        color: #000000;
                        max-width: 200px;
                        padding: .5rem;
                        border-radius: 5px;
                        outline: none;
                        opacity: .8;
                        transition: .2s ease-in-out;
                        }

                        /* From Uiverse.io by boryanakrasteva */ 
                        /* Customize the label (the checkbox-btn) */
                        .checkbox-btn {
                        display: block;
                        position: relative;
                        padding-left: 30px;
                        margin-bottom: 10px;
                        cursor: pointer;
                        -webkit-user-select: none;
                        -moz-user-select: none;
                        -ms-user-select: none;
                        user-select: none;
                        }

                        /* Hide the browser's default checkbox */
                        .checkbox-btn input {
                        position: absolute;
                        opacity: 0;
                        cursor: pointer;
                        height: 0;
                        width: 0;
                        }

                        .checkbox-btn label {
                        cursor: pointer;
                        font-size: 14px;
                        }
                        /* Create a custom checkbox */
                        .checkmark {
                        position: absolute;
                        top: 0;
                        left: 0;
                        height: 25px;
                        width: 25px;
                        border: 2.5px solid #000000;
                        transition: .2s linear;
                        }
                        .checkbox-btn input:checked ~ .checkmark {
                        background-color: transparent;
                        }

                        /* Create the checkmark/indicator (hidden when not checked) */
                        .checkmark:after {
                        content: "";
                        position: absolute;
                        visibility: hidden;
                        opacity: 0;
                        left: 50%;
                        top: 40%;
                        width: 10px;
                        height: 14px;
                        border: 2px solid #0ea021;
                        filter: drop-shadow(0px 0px 10px #0ea021);
                        border-width: 0 2.5px 2.5px 0;
                        transition: .2s linear;
                        transform: translate(-50%, -50%) rotate(-90deg) scale(0.2);
                        }

                        /* Show the checkmark when checked */
                        .checkbox-btn input:checked ~ .checkmark:after {
                        visibility: visible;
                        opacity: 1;
                        transform: translate(-50%, -50%) rotate(0deg) scale(1);
                        animation: pulse 1s ease-in;
                        }

                        .checkbox-btn input:checked ~ .checkmark {
                        transform: rotate(45deg);
                        border: none;
                        }

                        @keyframes pulse {
                        0%,
                        100% {
                            transform: translate(-50%, -50%) rotate(0deg) scale(1);
                        }
                        50% {
                            transform: translate(-50%, -50%) rotate(0deg) scale(1.6);
                        }
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

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<main class="table">
    <section class="table__header">
        <div class="info">
            <div>
                <p><strong>Manage Products</strong></p>
                <p>Category: <?php echo htmlspecialchars($result_cat['name']); ?></p>
                <p>Created at: <?php echo htmlspecialchars($result_cat['created_at']); ?></p>
                <p>Updated at: <?php echo htmlspecialchars($result_cat['updated_at']); ?></p>
            </div>
            <div class="content_search">
                <div class="input-group">
                    <input type="search" placeholder="Search...">
                    <img src="../images/search.png" alt="">
                </div>
            </div>
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
        <form method="post" action="pross_delte_pro"  enctype="multipart/form-data">
            <table class="tble1">
                <thead>
                    <tr>
                        <th>ID<span class="icon-arrow">&UpArrow;</span></th>
                        <th>Name<span class="icon-arrow">&UpArrow;</span></th>
                        <th>Price<span class="icon-arrow">&UpArrow;</span></th>
                        <th>Supplier id<span class="icon-arrow">&UpArrow;</span></th>
                        <th>Supplier Name<span class="icon-arrow">&UpArrow;</span></th>
                        <th>stock_quantity<span class="icon-arrow">&UpArrow;</span></th>
                        <th>created_at<span class="icon-arrow">&UpArrow;</span></th>
                        <th>updated_at<span class="icon-arrow">&UpArrow;</span></th>                        
                        <th>DELETE</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php
                    $sql = "SELECT product_id FROM product_categories WHERE category_id = :id_cat_delet";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute(['id_cat_delet' => $idcat]);
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (count($rows) > 0) {
                        foreach ($rows as $row) {
                            $id = $row["product_id"];
                            $sql_sub = "SELECT * FROM products WHERE id = :id_product";
                            $stmt_sub = $conn->prepare($sql_sub);
                            $stmt_sub->execute(['id_product' => $id]);
                            $rows_sub = $stmt_sub->fetchAll(PDO::FETCH_ASSOC);
                            if (count($rows_sub) > 0) {
                                foreach ($rows_sub as $row_sub) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($id) . "</td>";
                                    echo "<td>" . htmlspecialchars($row_sub["product_name"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row_sub["vente_price"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row_sub["supplier_id"]) . "</td>";
                                    $sypp = $row_sub["supplier_id"];
                                    $sq = "SELECT supplier_name FROM supplier WHERE id=:id_supplier"; 
                                    $st = $conn->prepare($sq);                 
                                    $st->execute(['id_supplier' => $sypp]);
                                    $supplier = $st->fetch();
                                    if ($supplier) {
                                        echo "<td>" . htmlspecialchars($supplier["supplier_name"]) . "</td>";
                                    } else {
                                        echo "<td></td>";
                                    }
                                    echo "<td>" . htmlspecialchars($row_sub["stock_quantity"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row_sub["created_at"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row_sub["updated_at"]) . "</td>";
                                    echo "<td><label class='checkbox-btn'>
                                    <input type='checkbox' name='delete_ids[]' value='" . htmlspecialchars($id) . "'>
                                    <span class='checkmark'></span>
                                    </label></td>";
                                    echo "</tr>";
                                }
                            }
                        }
                    } else {
                        echo "<tr><td colspan='9'>No data available</td></tr>";
                    }
                    ?>
                    
                </tbody>
            </table>
            <div class="submit-button-container">
                <button type="submit" name="delete" class="submit-button">GO TO THE NEXT</button>
            </div>
        </form>
    </section>
</main>
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
