

<link
rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
/>

<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Quicksand:wght@300..700&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&family=Roboto:ital,wght@0,100..900;1,100..900&family=Satisfy&display=swap"
rel="stylesheet"
/>
<link rel="stylesheet" href="styles/NavbarNoBack.css">

<nav id="navbar" class="scrolled">

  <a href="#" class="menu-icon" onclick="toggleMenu()">
        <i class="bi bi-list" id="menu"></i>
      </a>

      <a href="#" class="hotel-name">
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
        <i class="bi bi-person-circle" id="user-icon"></i>
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
    <li><a href="#">Contact Us</a></li>
    <li><a href="ManageReservation.php">My Reservation</a></li>
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

        // Menu Toggle Functionality
        function toggleMenu() {
            var sideMenu = document.getElementById('sideMenu');
            sideMenu.classList.toggle('show');
        }
     
    </script>