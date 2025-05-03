
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
    <style>
      body, html {
    margin: 0;
    padding: 0;
    height: 100%;
    width: 100%;
    font-family: "Cormorant Garamond", serif;
    font-optical-sizing: auto;
    font-weight: 600;
    background-image: url('https://drive.google.com/thumbnail?id=1GXytGrNIa9HMRXBQ7y61vWJ-px4xPIrk&sz=s800');
  background-repeat: repeat;
  background-size: auto; /* or 'contain' if you want each tile to fit fully */
  padding-top: 90px; /* height of your nav */
    
}

.main-image {
    width: 100%;
    height: 40vh;
    object-fit: cover;
    display: block;
    filter: brightness(40%); 
    position: relative;
    z-index: 1;
  }



  nav {
    position: fixed;
    top: 0;
    margin-top: 0;
    left: 0;
    height: 90px;
    right: 0;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0;
    z-index: 10; 
    transition: top 0.3s ease, background-color 0.3s ease, padding 0.3s ease;
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
  
.menu-icon i {
  font-size: 2rem; 
}

nav.scrolled {
  background-color: black;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
  padding-top: 0;
  padding-bottom: 0;
}

  

.hidden-on-load {
    opacity: 0;
    transition: opacity 0.5s ease;
  }
  

  .show-on-scroll {
    opacity: 1;
    transition: opacity 1.2s ease; 
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
    font-size: 18px;
    margin-bottom: 20px;
    font-weight: bold;
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

  .side-menu-name {
    margin-right: 120px; /* Move the text further to the left */
    font-size: 18px;
    font-weight: bold;
    color: white;
    text-decoration: none;
    margin-top: 20px;
  }
  li {
    text-decoration: none;
    margin-top: 20px;
  }

 

#sideMenu ul {
  list-style-type: none; 
  padding: 0;
  margin: 0;
}

#sideMenu ul li {
  margin-bottom: 10px; 
}

#sideMenu ul li a {
  text-decoration: none;
  display: block;
  padding: 10px 0;
  border-bottom: 1px solid transparent; 
  transition: border-color 0.3s ease;
}

#sideMenu ul li a:hover {
  border-bottom: 1px solid white; 
  padding-left: 20px;
}

#Login-dialog h3{
  text-align: center;
  margin-top: 20px;
  font-family: "Cormorant Garamond", serif;
  font-optical-sizing: auto;
  font-weight: 600;
}


.reservation-container {
  padding: 40px;
  background-color: #ffffffcc;
  margin: 80px auto;
  max-width: 1500px;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  margin-top: 30px; /* space for nav + some breathing room */
}

.reservation-container h2 {
  text-align: center;
  font-family: "Quicksand", sans-serif;
  margin-bottom: 30px;
}

.reservation-table {
  width: 100%;
  border-collapse: collapse;
  font-family: "Quicksand", sans-serif;
}

.reservation-table th, .reservation-table td {
  padding: 14px 16px;
  border-bottom: 1px solid #ddd;
  text-align: center;
}

.reservation-table th {
  background-color: #000000;
  color: white;
}

.reservation-table tr:hover {
  background-color: #f5f5f5;
}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropbtn {
  background-color: #000;
  color: white;
  padding: 8px 14px;
  font-size: 14px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: white;
  min-width: 120px;
  box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
  z-index: 1;
  border-radius: 6px;
}

.dropdown-content a {
  color: black;
  padding: 10px 12px;
  text-decoration: none;
  display: block;
  font-size: 14px;
}

.dropdown-content a:hover {
  background-color: #f1f1f1;
}

.dropdown:hover .dropdown-content {
  display: block;
}

.dropdown:hover .dropbtn {
  background-color: #333;
}


    </style>
  </head>
  <body>
    
  <nav id="navbar" class="scrolled">

      <a href="#" class="menu-icon" onclick="toggleMenu()">
        <i class="bi bi-list" id="menu"></i>
      </a>

      <a href="#" class="hotel-name ">
        LA GINTA REAL
        <span class="hotel-location">PHILIPPINES</span>
      </a>

      <div class="nav-right">
        <a href="#">MY RESERVATION</a>
        <a href="#">BOOK</a>
      </div>
    </nav>
    <div id="sideMenu" class="side-menu">
      <button class="close-menu" onclick="toggleMenu()">
      <a href="#" class="side-menu-name">
  <?php echo $username ? "Hi, " . htmlspecialchars($username) . "!" : "LA GINTA REAL"; ?>
</a>

        <i class="bi bi-x"></i>
      </button>
      <ul>
        <li><a href="Home.php">Home</a></li>
        <li><a href="Rooms&Suites.php">Rooms & Suites</a></li>
        <li><a href="#">Exlusive Offers</a></li>
        <li><a href="AboutUs.php">About Us</a></li>
        <li><a href="#">Contact Us</a></li>
        <li><a href="#">My Reservation</a></li>
        <?php if ($username): ?>
    <li>
      <a href="Logout.php">
        <i class="bi bi-box-arrow-right"></i> Logout
      </a>
    </li>
  <?php else: ?>
    <li>
      <a href="Login.php">
        <i class="bi bi-box-arrow-in-right"></i> Login
      </a>
    </li>
  <?php endif; ?>

       
      </ul>
    </div>

    
    <div class="reservation-container">
  <h2>Your Reservations</h2>
  <table class="reservation-table">
    <thead>
      <tr>
        <th>Room No.</th>
        <th>Check-In</th>
        <th>Check-Out</th>
        <th>Price</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>101</td>
        <td>2025-05-10</td>
        <td>2025-05-15</td>
        <td>$500</td>
        <td>
          <div class="dropdown">
            <button class="dropbtn">Actions</button>
            <div class="dropdown-content">
              <a href="#">View</a>
              <a href="#">Edit</a>
              <a href="#">Cancel</a>
            </div>
          </div>
        </td>
      </tr>
      <!-- Add more rows as needed -->
    </tbody>
  </table>
</div>

       
    <script>

        // Menu Toggle Functionality
        function toggleMenu() {
            var sideMenu = document.getElementById('sideMenu');
            sideMenu.classList.toggle('show');
        }

     
    </script>
</body>
</html>
