window.addEventListener("scroll", function() {

const navbar = document.querySelector(".navigation");

    if(window.scrollY > 50){
        navbar.classList.add("scrolled");
    }else{
        navbar.classList.remove("scrolled");
    }

});

// Ouvrir la modal
document.querySelectorAll("button[data-modal]").forEach(button => {
  button.addEventListener("click", function() {
    const modalId = this.getAttribute("data-modal");
    document.getElementById(modalId).style.display = "block";
  });
});

// Fermer la modal avec le X
document.querySelectorAll(".close").forEach(closeBtn => {
  closeBtn.addEventListener("click", function() {
    this.closest(".modal").style.display = "none";
  });
});

// Fermer en cliquant en dehors
window.addEventListener("click", function(e) {
  document.querySelectorAll(".modal").forEach(modal => {
    if (e.target === modal) {
      modal.style.display = "none";
    }
  });
});

const burger = document.querySelector(".burger");
const menuMobile = document.querySelector(".navButtonMobile");

burger.addEventListener("click", () => {
    menuMobile.classList.toggle("OpenBurger");
    menuMobile.classList.toggle("closeBurger");
});