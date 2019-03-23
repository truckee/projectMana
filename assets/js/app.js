/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// require jQuery normally
const $ = require('jquery');

// create global $ and jQuery variables
global.$ = global.jQuery = $;

// JS is equivalent to the normal "bootstrap" package
// no need to set this to a variable, just require it
require('bootstrap');

import '../css/app.scss';


// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');
 
 require('@fortawesome/fontawesome-free/js/all.js');

 import './jquery-ui.min.js';
 import './project.js';
 
$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});