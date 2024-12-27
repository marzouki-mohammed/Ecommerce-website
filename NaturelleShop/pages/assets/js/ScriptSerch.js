// SIDEBAR TOGGLE
           
let sidebarOpen = false;
const sidebar = document.getElementById('sidebar');


function openSidebar() {
if (!sidebarOpen) {
    sidebar.classList.add('sidebar-responsive');
    sidebarOpen = true;
}
}

function closeSidebar() {
if (sidebarOpen) {
    sidebar.classList.remove('sidebar-responsive');
    sidebarOpen = false;
}
}


// Sélectionnez la div avec l'id "menuu" et l'icône widgets
const menuu = document.getElementById('menuu');
const widgetsIcon = document.getElementById('menu_open');

// Variable pour suivre l'état du menu (ouvert ou fermé)
let menuOpen = false;

// Fonction pour ouvrir ou fermer le menu
function toggleMenu() {
    if (menuOpen) {
        menuu.style.visibility = "hidden";
        menuOpen = false;
    } else {
        menuu.style.visibility = "visible";
        menuOpen = true;
    }
}

// Ajoutez un événement de clic à l'icône widgets pour basculer le menu
widgetsIcon.addEventListener('click', toggleMenu);

document.getElementById('searchForm').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault(); 
        this.submit(); 
    }
});

/*
const account_div=document.getElementById('account_div');
const button_account_1 =document.getElementById('btn-account-1');

function GetAccount(button) {
  button.addEventListener("click", function() {
    if (account_div.style.display === "none") {
      account_div.style.display = "block";


    }else{
      account_div.style.display = "none";

    }

  });

}
GetAccount(button_account_1);


  const button_orders_1 =document.getElementById('btn_orders_1');
  const divElement_wishlist=document.getElementById('wishlist_div');
  const closde_btn_wishlist=document.getElementById('closde_wishlist');
  const titre_name=document.getElementById('titre');
  const content=document.getElementById('contenu_wishlist');


closde_btn_wishlist.addEventListener('click', function(){
divElement_wishlist.style.display = "none";
})

function Getorders(button) {
button.addEventListener("click", function() {
if (divElement_wishlist.style.display === "none") {
    titre_name.innerHTML = 'Orders';

    if (ordersIds.length > 0) {
        fetch('users_orders.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 'orders_ids': ordersIds })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            content.innerHTML = ''; 
            
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(item => {
                    const date = item.created_at;
                    
                    content.innerHTML += `
                    <li class="menu-wishlist">
                       
                                                 
                            <p class="wishlist-title">${date}</p>                         
                       
                        <div class="list-info" >
                            <p class="ventePrice-title">Order</p>
                        </div>
                    </li>`;
                });
                
            } else {
                console.error('Aucun produit trouvé ou données invalides.');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            content.innerHTML = 'Erreur lors de la récupération des données.';
        });

        divElement_wishlist.style.display = "block";
    } else {
        console.error('Aucun produit dans la wishlist.');
    }
} else {
    divElement_wishlist.style.display = "none";
}
});
}

Getorders(button_orders_1);



// Sélection des éléments
const dropdownButton = document.getElementById('dropdownButton');
const dropdownMenu = document.getElementById('dropdownMenu');
const dropdownIcon = document.querySelector('.dropdown-icon');

// Fonction pour toggler le menu déroulant
function toggleDropdown() {
dropdownMenu.classList.toggle('show');
dropdownIcon.classList.toggle('rotate');
}

// Fermer le menu si on clique en dehors
function closeDropdown(event) {
if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
dropdownMenu.classList.remove('show');
dropdownIcon.classList.remove('rotate');
}
}

// Ajouter les événements
dropdownButton.addEventListener('click', toggleDropdown);
window.addEventListener('click', closeDropdown);

*/





