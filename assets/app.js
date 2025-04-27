/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import "./styles/form.css";
import 'flowbite/';

const dropdownButtonList = document.querySelectorAll("[role='toggle']");

for (const button of dropdownButtonList) {
    const dropdown = button.parentElement;
    const content = dropdown.querySelectorAll("[role='content']")[0];
    content.toggle = () => content.classList.toggle("hidden");
    button.addEventListener("click", () => content.toggle());
}