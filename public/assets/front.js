// Lazy loading
$(function() {
    $('.lazy').Lazy();
});
// Il faut ajouter la class lazy aux img, leur mettre un gif de chargement en src
// et leur mettre le chemin normal:  data-src = {{ assert('') }}

// Quitter automatiquement le petit message de l'addFlash
let ms = 0;
let exit = document.getElementsByClassName("quitter")[0];

if (exit) {
    setInterval(afficheMsg, 1000);
    function afficheMsg() {
        if(ms >= 5000) {
            exit.remove();
        }
        else {
            ms+=1000;
        }
    }
}

// Carousel
$('.carousel').carousel({

    pause: "null"

})

// bar de recherche
$("#searchbar").autocomplete({
    source: function (request, response) {
        $.get({
            url: "{{ path('app_search_autocomplete')}}",
            data: {
                query: request.term,
            },
            dataType: 'json',
            method: 'get'
        }).done(function (data) {
            response(data);
        });
    },
    delay: 100,
    minLength: 2
})

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