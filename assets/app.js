/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.scss";
// import du node_module de bootstrap
require("bootstrap");
// import du script sidemenu navbar de startbootstrap-sb-admin
require("../node_modules/startbootstrap-sb-admin/src/js/scripts.js");
// start the Stimulus application
import "./bootstrap";

// import du bs5-lightbox
import Lightbox from "bs5-lightbox";

const options = {
    keyboard: true,
    size: "fullscreen",
};

document.querySelectorAll(".lightbox").forEach((el) =>
    el.addEventListener("click", (e) => {
        e.preventDefault();
        const lightbox = new Lightbox(el, options);
        lightbox.show();
    })
);
