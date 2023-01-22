/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.scss";
import * as bootstrap from "./js/bootstrap.bundle.min.js";
require("../node_modules/startbootstrap-sb-admin/src/js/scripts.js");

// start the Stimulus application
import "./bootstrap";

// Tooltip
var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
);
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// dropdown
// const dropdownElementList = document.querySelectorAll(".dropdown-toggle");
// const dropdownList = [...dropdownElementList].map(
//     (dropdownToggleEl) => new bootstrap.Dropdown(dropdownToggleEl)
// );

// lightbox
import Lightbox from "bs5-lightbox";

document
    .querySelectorAll(".lightbox")
    .forEach((el) => el.addEventListener("click", Lightbox.initialize));
