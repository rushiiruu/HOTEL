
<?php
  session_start();
  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

  $servername = "localhost";
  $dbuser = "root";
  $password = "";
  $dbname = "hotel_db";
  $errorMsg = [];
  $successMsg = "";

  // Connect to MySQL server
  $conn = new mysqli($servername, $dbuser, $password, $dbname);
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  if (isset($_POST['book'])) {
      $room_id = $_POST['roomId'];
      $_SESSION['room_id'] = $room_id; // Store room ID in session for later use
      header("Location: Reservation.php");
      exit();
  }
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


  
  h1 {
    text-align: justify;
  font-family: "Quicksand", sans-serif;
  font-optical-sizing: auto;
  font-weight: 400;
  font-style: normal;
  text-align: center;
  line-height: 1.6;
  }

  .center-logo {
    width: 100px;
    height: auto;
    margin-bottom: 15px;
  }
  
  .centered-title h1 {
    font-size: 60px;
    margin: 0;
    letter-spacing: 2px;
  }
  
  .centered-title h2 {
    font-size: 24px;
    margin: 10px 0 0 0;
    font-weight: 300;
    letter-spacing: 3px;
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


span {
    display: flex;
    justify-content: center; 
    align-items: center;     
    margin-top: 20px;        
    width: 100%;
}

span a {
    color: black;
    text-decoration: none;
    margin: 0 15px;
    font-size: 12px;
    font-weight: bold;
    position: relative;
}

span a:hover {
    border-bottom: 1px solid black;
}

.pop-offers {
    text-align: center;
    margin-top: 80px;
}

.long-line {
    width: 100%;
    height: 1px;
    background-color: #e0e2e1;
    margin-top: 20px;
}
.room-container {
  max-width: 100%;
  margin: 0;
  display: flex;
  flex-direction: column;
}

.room-section {
  display: flex;
  width: 100%;
  margin-bottom: 30px
}

.room-image {
  width: 50%;
  height: 70vh;
  display: block;
  object-fit: cover;
}

.room-info {
  width: 35%;
  padding: 40px;
  display: flex;
  flex-direction: column;
  justify-content: center;
 
  
}

.room-info h2 {
  color: #d0b683;
  font-family: "Cormorant Garamond", serif;
  font-optical-sizing: auto;
  font-weight: 600;
  font-style: italic;
  font-size: 32px;
  margin-bottom: 20px;
}

.room-info p {
  text-align: justify;
  font-family: "Quicksand", sans-serif;
  font-optical-sizing: auto;
  font-weight: 400;
  font-style: normal;
  font-size: 16px;
  line-height: 1.6;
}

.room-info ul {
  list-style: none;
  padding: 0;
  font-size: 16spx;
  margin-bottom: 20px;
}

.room-info ul li {
  margin-bottom: 5px;
}

.room-info button {
  padding: 15px 35px;
  background-color: black;
  color: white;
  border: none;
  font-weight: bold;
  margin-top: 40px;
  cursor: pointer;
  transition: background 0.3s ease;
  width: fit-content;
  max-width: 200px; /* adjust as needed */
  white-space: nowrap; /* keeps text in one line */
}

.room-info button:hover {
  background-color: #333; /* dark gray instead of red */
}

.search-container {
  max-width: 500px;
  margin: 40px auto 20px;
  display: flex;
  align-items: center;
  position: relative;
}

.search-container input {
  width: 100%;
  padding: 12px 40px 12px 15px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 25px;
  outline: none;
  font-family: "Quicksand", sans-serif;
}

.search-container .search-icon {
  position: absolute;
  right: 15px;
  font-size: 18px;
  color: #aaa;
}

.room-description {
  text-align: center;
  font-family: "Quicksand", sans-serif;
  font-size: 16px;
  font-weight: 400;
  color: #333;
  margin-top: 10px;
  margin-bottom: 40px;
  max-width: 700px;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.6;
}




    </style>
  </head>
  <body>
    <script src="Home.js"></script>
    <nav id="navbar">
      <a href="#" class="menu-icon" onclick="toggleMenu()">
        <i class="bi bi-list" id="menu"></i>
      </a>

      <a href="#" class="hotel-name hidden-on-load">
        LA GINTA REAL
        <span class="hotel-location">PHILIPPINES</span>
      </a>

      <div class="nav-right">
        <a href="ManageReservation.php">MY RESERVATION</a>
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
        <li><a href="ManageReservation.php">My Reservation</a></li>
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

    <img
      src="https://www.ft.com/__origami/service/image/v2/images/raw/https%3A%2F%2Fd1e00ek4ebabms.cloudfront.net%2Fproduction%2F54089c95-8f88-4f62-a702-b77d2cc3a6c4.jpg?source=next-article&fit=scale-down&quality=highest&width=700&dpr=1"
      class="main-image"
      alt="bg"
    />
    <span class =span-menu>
      <a href="Home.php">LA GINTA REAL</a>
      <a href="Rooms&Suites.php">ROOMS & SUITES</a>
      <a href="">OFFERS</a>
      <a href="AboutUs.php">ABOUT US</a>
    </span>
    <div class="long-line"></div>

    <div class="search-container">
  <input type="text" id="roomSearch" placeholder="Search for rooms or suites..." onkeyup="filterRooms()" />
  <i class="bi bi-search search-icon"></i>
</div>

<h1>ROOMS & SUITES</h1>
<p class="room-description">
  Discover our refined selection of rooms and suites, each designed with elegance, comfort, and style to make your stay truly unforgettable.
</p>

<div class="room-container">    
     <?php
       $rows = $conn->query("SELECT * FROM roomsandsuites")->fetch_all(MYSQLI_ASSOC);
 
       foreach ($rows as $room){
         echo '<form action="" method="post">';
         echo '<div class="room-section">';
         echo '<img src="' . htmlspecialchars($room['Img']) . '" alt="Room Image" class="room-image">';
         echo '<div class="room-info">';
         echo '<h2>' . htmlspecialchars($room['RoomName']) . '</h2>';
         echo '<p>' . htmlspecialchars($room['RoomDesc']) . '</p>';
         echo '<ul>';
         echo '<li><i class="bi bi-rulers" style="font-size: 1.2rem;"></i> ' . htmlspecialchars($room['RoomSize']) . '</li>';
         echo '<li><i class="bi bi-person-lines-fill" style="font-size: 1.2rem;"></i> ' . htmlspecialchars($room['RoomAccomodation']) . '</li>';
         echo '<li><i class="bi bi-basket" style="font-size: 1.2rem;"></i> ' . htmlspecialchars($room['Beds']) . '</li>';
          echo '<li><i class="bi bi-gear" style="font-size: 1.2rem;"></i> ' . htmlspecialchars($room['Utilities']) . '</li>';
         echo '</ul>';
         echo '<input type="hidden" name="roomId" value="' . htmlspecialchars($room['RaSid']) . '">';
         echo '<button type="submit" name="book">BOOK NOW</button></a>';
         echo '</div></div>';
         echo '</form>';
       }
     ?>
 </div>
 
 
 <script>
 function filterRooms() {
   const input = document.getElementById("roomSearch").value.toLowerCase();
   const rooms = document.querySelectorAll(".room-section");
 
   rooms.forEach(room => {
     const text = room.innerText.toLowerCase();
     room.style.display = text.includes(input) ? "flex" : "none";
   });
 }
 
 
 </script>
 
   </body>
 </html>