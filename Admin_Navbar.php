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

  <title>Admin Panel</title>
  <style>
body {
  font-family: 'Open Sans', sans-serif;
  margin: 0;
  padding: 0;
}

nav {
  position: fixed;
  top: 0;
  left: 0;
  height: 90px;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0;
  z-index: 10;
  transition: top 0.3s ease, background-color 0.3s ease, padding 0.3s ease;
  background-color: black; /* Solid background color by default */
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
}

nav img {
  width: 40px;
  height: 40px;
}

nav a {
  color: white;
  text-decoration: none;
  margin: 0 15px;
  font-size: 12px;
  font-weight: bold;
}

nav button {
  padding: 5px 20px;
  background-color: white;
  color: black;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-weight: bold;
  font-size: 12px;
  margin-left: 30px;
  margin-right: 15px;
}

.hotel-name {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  font-size: 24px;
  font-weight: bold;
  color: white;
  font-family: "Cormorant Garamond", serif;
  font-optical-sizing: auto;
  font-weight: 400;
  font-style: normal;
  text-align: center;
  opacity: 1;
  visibility: visible;
}

.hotel-location {
  display: block; 
  font-size: 14px;
  font-weight: normal;
  margin-top: 0px;
  font-family: "Cormorant Garamond", serif;
  font-optical-sizing: auto;
  font-weight: 400;
  font-style: normal;
  text-align: center;
}

.nav-right {
  display: flex;
  align-items: center;
}

.nav-right a {
  margin-right: 20px;
  font-family: "Cormorant Garamond", serif;
  font-optical-sizing: auto;
  font-weight: 400;
  font-style: normal;
  font-size: 16px;
  text-decoration: none;
  color: white;
  text-align: center;
}

.menu-icon i {
  font-size: 2rem;
  margin-left: 15px;
  color: white;
}

nav.scrolled {
  background-color: black;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
}

.side-menu {
  position: fixed;
  top: 0;
  left: -290px;
  width: 250px;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.95);
  color: white;
  padding-top: 80px;
  display: flex;
  flex-direction: column;
  align-items: start;
  padding-left: 30px;
  transition: left 0.4s ease;
  z-index: 100;
}

.side-menu.show {
  left: 0;
}

.side-menu a {
  color: white;
  text-decoration: none;
  font-size: 20px;
  margin-bottom: 20px;
  font-weight: bold;
  font-family: "Cormorant Garamond", serif;
}

.close-menu {
  position: absolute;
  top: 20px;
  right: 20px;
  background: none;
  border: none;
  color: white;
  font-size: 24px;
  cursor: pointer;
  display: flex;
  align-items: center;
}

.close-menu i {
  font-size: 30px;
}

#sideMenu ul {
  list-style-type: none;
  padding: 0;
  margin: 0;
  width: 100%;
}

#sideMenu ul li {
  margin-bottom: 10px;
}

#sideMenu ul li a {
  text-decoration: none;
  display: block;
  padding: 10px 0;
  border-bottom: 1px solid transparent;
  transition: border-color 0.3s ease, padding-left 0.3s ease;
  font-family: "Cormorant Garamond", serif;
}

#sideMenu ul li a:hover {
  border-bottom: 1px solid white;
  padding-left: 20px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 25px;
  padding-left: 10px;
  margin-bottom: 30px;
  color: white;
  font-size: 20px;
  font-weight: bold;
  font-family: "Cormorant Garamond", serif;
}

#user-icon {
  font-size: 45px;
}

.side-menu-bottom {
  margin-top: auto;
  padding-bottom: 30px;
  padding-left: 10px;
}

.side-menu-bottom a {
  display: flex;
  align-items: center;
  color: white;
  text-decoration: none;
  font-size: 20px;
  gap: 10px;
  font-weight: bold;
  margin-bottom: 90px;
  font-family: "Cormorant Garamond", serif;
}

.content-container {
  margin-top: 100px;
  padding: 20px;
  font-family: 'Open Sans', sans-serif;
}

/* Fix for duplicate styles */
.hidden-menu {
  display: none;
  flex-direction: column;
  background-color: white;
  position: absolute;
  top: 60px;
  right: 0;
  padding: 10px;
  border: 1px solid #ccc;
}

.hidden-menu.show {
  display: flex;
}

.hidden-on-load {
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.3s ease, visibility 0.3s ease;
}

.show-on-scroll {
  opacity: 1;
  visibility: visible;
}

/* Add styles for username display */
.username {
  font-family: "Cormorant Garamond", serif;
  font-weight: 500;
}
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav id="navbar">
    <a href="#" class="menu-icon" onclick="toggleMenu()">
      <i class="bi bi-list" id="menu"></i>
    </a>

    <a href="admin_dashboard.php" class="hotel-name">
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