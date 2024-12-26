// js for the main page 


 // Modal variables
 const modal = document.querySelector('[data-modal]');
 const modalCloseBtn = document.querySelector('[data-modal-close]');
 const modalCloseOverlay = document.querySelector('[data-modal-overlay]');
 const btn_accuont=document.getElementById('btn_singup');
 const btn_accuont_2=document.getElementById('btn_singup2');




     //modal function
        // Function to show the modal
        const showModal = () => {
           modal.style.display = 'flex';
            
        };
        // Function to hide the modal
        const hideModal = () => {
          modal.style.display = 'none';



        };

        btn_accuont.addEventListener('click',showModal);
        btn_accuont_2.addEventListener('click',showModal);







        // Event listeners
        modalCloseOverlay.addEventListener('click', hideModal);
        modalCloseBtn.addEventListener('click', hideModal);
        


 //menu categorie 
 //const openMenu1Btn = document.getElementById('open-menu-1');      
 const openMenu6Btn = document.getElementById('open-menu-cat');
 const menu1 = document.getElementById('menu-cat');
 const closeMenu1Btn = document.getElementById('close-menu-cat');

        
        const  btn_open_cart= document.getElementById('btn_cart');
        const btn_open_cart2 = document.getElementById('btn_cart2');

        
        
        const menu2 = document.getElementById('menu-2');
        const closeMenu2Btn = document.getElementById('close-menu-2');  
      

// Fonction pour ouvrir un menu

const openMenu = (menu) => {
    menu.classList.add('active');
    overlay.classList.add('active');
};


// Fonction pour fermer tous les menus
const closeAllMenus = () => {
    menu1.classList.remove('active');
    menu2.classList.remove('active');
    //overlay.classList.remove('active');
};

// Ajouter les écouteurs d'événements pour ouvrir les menus

openMenu6Btn.addEventListener('click', () => openMenu(menu1));

btn_open_cart.addEventListener('click', () => openMenu(menu2));
btn_open_cart2.addEventListener('click', () => openMenu(menu2));



// Ajouter les écouteurs d'événements pour fermer les menus
closeMenu1Btn.addEventListener('click', closeAllMenus);
closeMenu2Btn.addEventListener('click', closeAllMenus);




// accordion variables
const accordionBtn = document.querySelectorAll('[accordion-btn]');
const accordion = document.querySelectorAll('[accordion-data]');

for (let i= 0; i < accordionBtn.length; i++) {

  accordionBtn[i].addEventListener('click' , function () {

    const clickedBtn = this.nextElementSibling.classList.contains('active');

    for (let j = 0; j < accordion.length; j++) {

      if (clickedBtn) break;

      if (accordion[j].classList.contains('active')) {

        accordion[j].classList.remove('active');
        accordionBtn[i].classList.remove('active');

      }

    }

    this.nextElementSibling.classList.toggle('active');
    this.classList.toggle('active');

  });

}


document.getElementById("header_btn").addEventListener("click", function() {
  document.getElementById("header").scrollIntoView({ behavior: "smooth" });
});

document.getElementById('searchForm').addEventListener('keydown', function(event) {
  if (event.key === 'Enter') {
      event.preventDefault(); 
      this.submit(); 
  }
});

 



