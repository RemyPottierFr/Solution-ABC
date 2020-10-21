/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require("../scss/app.scss");

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');
require("bootstrap");

// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');

const burger = document.getElementById("burger");
const headerMenu = document.getElementById("Header__menu");
burger.onclick = () => headerMenu.classList.toggle("hidden");

const recommendationOwnedSummaryHeader = document.getElementById(
  "summaryOwnedHeader"
);
const recommendationOwnedSummaryContent = document.getElementById(
  "summaryOwnedContent"
);
recommendationOwnedSummaryHeader &&
  (recommendationOwnedSummaryHeader.onclick = () =>
    recommendationOwnedSummaryContent.classList.toggle("hidden"));

const recommendationRecievedSummaryHeader = document.getElementById(
  "summaryRecievedHeader"
);
const recommendationRecievedSummaryContent = document.getElementById(
  "summaryRecievedContent"
);
recommendationRecievedSummaryHeader &&
  (recommendationRecievedSummaryHeader.onclick = () =>
    recommendationRecievedSummaryContent.classList.toggle("hidden"));

const resetHeaderMenu = () => {
  if (window.matchMedia("(max-width: 1000px)").matches) {
    headerMenu.classList = "Header__menu overflow-image z-50 hidden";
  }
};

window.onload = resetHeaderMenu;

window.addEventListener("DOMContentLoaded", () => {
  resetHeaderMenu();
});
