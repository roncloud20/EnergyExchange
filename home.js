"use strict";
const hamburgar = document.querySelector(".hamburger");
const menu = document.querySelector("#menu");

// hamburger menu
const hamburgerMenu = function () {
  if (menu.style.display === "flex") {
    menu.style.display = "none";
  } else {
    menu.style.display = "flex";
  }
};

hamburgar.addEventListener("click", hamburgerMenu);

// menu link opening
// menu.addEventListener("click", function (e) {
//   e.preventDefault();
// });
