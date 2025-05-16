<?php
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
?>
<!-- Navbar and Side Menu -->
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
/>

<link rel="stylesheet" href="styles/Navbar.css">

<nav id="navbar">
      <a href="#" class="menu-icon" onclick="toggleMenu()">
        <i class="bi bi-list" id="menu"></i>
      </a>

      <a href="Home.php" class="hotel-name hidden-on-load">
        LA GINTA REAL
        <span class="hotel-location">PHILIPPINES</span>
      </a>

      <div class="nav-right">
        <a href="ManageReservation.php">MY RESERVATION</a>
        <a href="Rooms&Suites.php">BOOK</a>
      </div>
    </nav>
    <div id="sideMenu" class="side-menu">

  <!-- User icon and name at the top -->
   
  <div class="user-info">
    <a href="UserAccount.php">
    <i class="bi bi-person-circle" id="user-icon">
    </i>
  </a> 
    <span class="username">
      <?php echo $username ? htmlspecialchars($username) : "Guest"; ?>
    </span>
  </div>



  <!-- Close button -->
  <button class="close-menu" onclick="toggleMenu()">
    <i class="bi bi-x"></i>
  </button>

  <!-- Navigation menu -->
  <ul>
    <li><a href="Home.php">Home</a></li>
    <li><a href="Rooms&Suites.php">Rooms & Suites</a></li>
    <li><a href="AboutUs.php">About Us</a></li>
    <li><a href="ManageReservation.php">My Reservation</a></li>
    <li><a href="UserAccount.php">Account</a></li>
  </ul>

  <!-- Login/Logout at the bottom -->
  <div class="side-menu-bottom">
    <?php if ($username): ?>
      <a href="Logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    <?php else: ?>
      <a href="Login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
    <?php endif; ?>
  </div>
</div>



<script>
  function toggleMenu() {
    const menu = document.getElementById("sideMenu");
    menu.classList.toggle("show");
  }

  window.addEventListener('scroll', function() {
    var navbar = document.querySelector('nav');
    
    if (window.scrollY > 50) {  
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
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
</script>
