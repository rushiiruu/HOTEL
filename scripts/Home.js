
// Adds/removes 'scrolled' class to navbar when user scrolls past 50px
  window.addEventListener('scroll', function() {
    var navbar = document.querySelector('nav');
    
    if (window.scrollY > 50) {  
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });
  
// Handles showing/hiding the hotel name and main title based on scroll position
  window.addEventListener("scroll", () => {
    const scrollY = window.scrollY;
    const hotelName = document.querySelector(".hotel-name");
    const centeredTitle = document.querySelector(".centered-title");

    if (scrollY > 50) {
      hotelName.classList.add("show-on-scroll");
      centeredTitle.classList.remove("show-on-scroll");
      centeredTitle.classList.add("hidden-on-load");
    } else {
      hotelName.classList.remove("show-on-scroll");
      centeredTitle.classList.remove("hidden-on-load");
      centeredTitle.classList.add("show-on-scroll");
    }
  });

// Toggles the visibility of the side menu
  function toggleMenu() {
    const sideMenu = document.getElementById("sideMenu");
    sideMenu.classList.toggle("show");
  }

// Opens the form popup by setting its display to 'block'

  function openForm() {
    document.getElementById("myForm").style.display = "block";
  }
  
// Closes the form popup by setting its display to 'none'
  function closeForm() {
    document.getElementById("myForm").style.display = "none";
  }
