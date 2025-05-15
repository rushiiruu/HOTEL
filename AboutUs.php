
<?php
  session_start();
  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rooms & Suites</title>
    
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
    <link rel="stylesheet" href="styles/AboutUs.css">
  </head>
  <body>
    <script src="Home.js"></script>
    <?php include 'Navbar.php'; ?>
    <img
      src="https://media.istockphoto.com/id/1680455174/photo/a-profile-portrait-of-a-woman.jpg?s=612x612&w=0&k=20&c=axGw-raQ8jVJA5SSd6QmFOIOow6LNEpF874hIdZVErg="
      class="main-image"
      alt="bg"
    />
    <div class="centered-title show-on-scroll">
      <img
        src="icons/logo-2.png"
        alt="Logo"
        class="center-logo"
      />
      <h1>ABOUT LA GINTA REAL</h1>
      <h2>PHILIPPINES</h2>
    </div>
    <span class =span-menu>
      <a href="Home.php">LA GINTA REAL</a>
      <a href="Rooms&Suites.php">ROOMS & SUITES</a>
      <a href="">OFFERS</a>
      <a href="">ABOUT US</a>
    </span>
    <div class="long-line"></div>
    <h1>THE ESSENCE OF ELEGANCE</h1>
      <p class="room-description">
      Nestled in the heart of timeless charm and coastal elegance, Hotel La Ginta Real offers a refined escape for travelers seeking comfort, sophistication, and warm Filipino hospitality. Our hotel blends classic design with modern amenities, creating a serene sanctuary where every guest feels truly welcomed.

<br><br>From our carefully curated rooms and suites to the personalized service provided by our dedicated staff, every detail at La Ginta Real reflects our commitment to quality and relaxation. Whether you're here for a family vacation, romantic getaway, or business retreat, we ensure your experience is as luxurious as it is memorable.

At Hotel La Ginta Real, we believe that true hospitality means creating moments that stay with you long after you leave.</p>

    
<?php include 'Footer.php'; ?>
    
    <script>
        // Menu Toggle Functionality
        function toggleMenu() {
            var sideMenu = document.getElementById('sideMenu');
            sideMenu.classList.toggle('show');
        }
    </script>
</body>
</html>
