<?php
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
  
  // Redirect to login if not logged in
  if (!$username) {
    header("Location: Login.php");
    exit();
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="styles/navbar.css"/>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Quicksand:wght@300..700&family=Roboto:ital,wght@0,100..900;1,100..900&family=Satisfy&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/Admin_Navbar.css">
  <title>Admin Panel</title>

</head>
<body>
  <!-- Navbar -->
  <nav id="navbar">
    <a href="#" class="menu-icon" onclick="toggleMenu()">
      <i class="bi bi-list" id="menu"></i>
    </a>

    <a href="Dashboard.php" class="hotel-name">
      LA GINTA REAL
      <span class="hotel-location">ADMIN PANEL</span>
    </a>

    <div class="nav-right">
      <a href="Dashboard.php">DASHBOARD</a>
      <a href="Admin_Rooms.php">ROOMS</a>
      <a href="Admin_Reservation.php">RESERVATIONS</a>
    </div>
  </nav>

  <!-- Side Menu -->
  <div id="sideMenu" class="side-menu">
    <!-- User icon and name at the top -->
    <div class="user-info">
      <i class="bi bi-person-circle" id="user-icon"></i>
      <span class="username">
        <?php echo htmlspecialchars($username); ?>
      </span>
    </div>

    <!-- Close button -->
    <button class="close-menu" onclick="toggleMenu()">
      <i class="bi bi-x"></i>
    </button>

    <!-- Navigation menu -->
    <ul>
      <li><a href="Dashboard.php">Dashboard</a></li>
      <li><a href="Admin_Rooms.php">Manage Rooms</a></li>
      <li><a href="Admin_Reservation.php">Manage Reservations</a></li>
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
  </script>
</body>
</html>