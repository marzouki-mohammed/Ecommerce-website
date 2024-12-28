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
// Ajouter l'événement click pour chaque en-tête de colonne
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
      sortTable(i, sort_asc);
  
      // Inverser l'ordre de tri pour le prochain clic
      sort_asc = !sort_asc;
    };
  });
  
  // Fonction de tri
  function sortTable(column, sort_asc) {
    const tbody = document.querySelector('tbody');
    const rowsArray = Array.from(table_rows); // Convertir NodeList en tableau
  
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