/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
// Vu que l'on a initialisé web pack, babel, jquery dans notre web pack.config on peut appeler nos librairies comme select2
// si l'on décide d'utiliser cette method on cache les éléments dans notre vue property show
// et on dit que si on clique sur le bouton les éléments apparaissent en diapositive vers le haut ou vers le bas
let $ = require('jquery')
require('../css/app.css');
require('select2')

$('select').select2()
let $contactButton = $('#contactButton')
$contactButton.click(e => {
    e.preventDefault();
    $('#contactForm').slideDown();
    $(this).slideUp();
})

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');

// console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
