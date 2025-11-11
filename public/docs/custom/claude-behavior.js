/**
 * Claude-inspired smooth behaviors for Scribe Docs
 * Autor: ChatGPT - Adaptació per I Want It API
 */

document.addEventListener("DOMContentLoaded", () => {
  // Highlight active sidebar link based on scroll
  const sections = document.querySelectorAll("h1, h2, h3");
  const navLinks = document.querySelectorAll(".sidebar a");

  window.addEventListener("scroll", () => {
    let current = "";
    sections.forEach((section) => {
      const sectionTop = section.offsetTop;
      if (pageYOffset >= sectionTop - 120) {
        current = section.getAttribute("id");
      }
    });

    navLinks.forEach((a) => {
      a.classList.remove("active");
      if (a.getAttribute("href") === `#${current}`) {
        a.classList.add("active");
      }
    });
  });

  // Smooth scroll for anchor clicks
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute("href"));
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
          block: "start",
        });
      }
    });
  });
});

// make right panel match viewport height and be sticky on large screens
document.addEventListener('DOMContentLoaded', function(){
  const darkBoxes = document.querySelectorAll('.page-wrapper .dark-box');
  function setMinHeight(){
    const h = window.innerHeight - 120;
    darkBoxes.forEach(db => db.style.maxHeight = h + 'px');
  }
  setMinHeight();
  window.addEventListener('resize', setMinHeight);
});