

  window.addEventListener('scroll', function() {
    var navbar = document.querySelector('nav');
    
    if (window.scrollY > 50) {  
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });
  
  window.addEventListener('scroll', () => {
    const scrollY = window.scrollY;
    const hotelName = document.querySelector('.hotel-name');
    const centeredTitle = document.querySelector('.centered-title');

    if (scrollY > 50) {
      hotelName.classList.add('show-on-scroll');
      centeredTitle.classList.remove('show-on-scroll');
      centeredTitle.classList.add('hidden-on-load');
    } else {
      hotelName.classList.remove('show-on-scroll');
      centeredTitle.classList.remove('hidden-on-load');
      centeredTitle.classList.add('show-on-scroll');
    }
  });

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

  function toggleMenu() {
    const sideMenu = document.getElementById("sideMenu");
    sideMenu.classList.toggle("show");
  }

  
  function openForm() {
    document.getElementById("myForm").style.display = "block";
  }
  
  function closeForm() {
    document.getElementById("myForm").style.display = "none";
  }
