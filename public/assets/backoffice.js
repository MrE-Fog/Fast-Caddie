// Affiche le choix de l'unité si la catégorie alimentaire à été selectionné
let choixCat = document.getElementsByClassName("types")[0];

if(choixCat) {
    choixCat.onchange = function() {
        let selection = (choixCat.options[choixCat.selectedIndex].text).toLowerCase();

        if(selection === "fruits et légumes" || selection === "tout le bio") {
            document.getElementById("divType").style.display = "flex";
        }
        else {
            document.getElementById("divType").style.display = "none";
        }
    }
}

// Quitter automatiquement le petit message de l'addFlash
let ms = 0;
let exit = document.getElementsByClassName("quitter")[0];

if (exit) {
    let temps = setInterval(afficheMsg, 1000);
    function afficheMsg() {
        if(ms >= 5000) {
            exit.remove();
        }
        else {
            ms+=1000;
        }
    }
}

// Charge l'image choisi pour l'article avant l'envoi du formulaire pour que l'user soit sur de celle qu'il envoie
var loadFile = function(event){
    var image = document.getElementById('image');
    image.src = URL.createObjectURL(event.target.files[0]);
};

$(function(){ $('.select2').select2() });

// Lazy loading
$(function() {
    $('.lazy').Lazy();
});

