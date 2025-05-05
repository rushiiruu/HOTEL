
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
  $conn = new mysqli($servername, $dbuser, $password);
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  // Create DB if not exists
  $db_check = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
  if ($db_check->num_rows == 0) {
      $conn->query("CREATE DATABASE $dbname");
  }

  $conn = new mysqli($servername, $dbuser, $password, $dbname);

  // Create UserAccount table if not exists
  $conn->query("CREATE TABLE IF NOT EXISTS UserAccount (
      UserID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      Fname VARCHAR(50) NOT NULL,
      Lname VARCHAR(50) NOT NULL,
      username VARCHAR(50) NOT NULL UNIQUE,
      password VARCHAR(255) NOT NULL,
      usertype VARCHAR(10) NOT NULL,
      Birthday DATE
  )");

  if($conn->query("SELECT COUNT(*) FROM UserAccount")->fetch_row()[0] == 0) {
    $conn->query("INSERT INTO UserAccount (Fname, Lname, username, password, usertype, Birthday) VALUES (
    'John', 'Doe', 'Admin1', '" . password_hash('admin1', PASSWORD_BCRYPT) . "', 'Admin', '1990-01-01')");
  
    $conn->query("INSERT INTO UserAccount (Fname, Lname, username, password, usertype, Birthday) VALUES (
    'Jane', 'Doe', 'Admin2', '" . password_hash('admin2', PASSWORD_BCRYPT) . "', 'Admin', '1990-01-01')");
  }
  $conn->query("CREATE TABLE IF NOT EXISTS RoomsandSuites (
      RaSid INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      Img VARCHAR(300) NOT NULL,
      RoomName VARCHAR(50) NOT NULL,
      RoomDesc VARCHAR(500) NOT NULL,
      RoomSize VARCHAR(50) NOT NULL,
      RoomAccomodation VARCHAR(50) NOT NULL,
      Beds VARCHAR(50) NOT NULL,
      Utilities VARCHAR(100) NOT NULL
  )");

  if ($conn->query("SELECT COUNT(*) FROM RoomsandSuites")->fetch_row()[0] == 0) {
      $conn ->query("Insert into RoomsandSuites (Img, RoomName, RoomDesc, RoomSize, RoomAccomodation, Beds, Utilities)
      values ('https://static-new.lhw.com/HotelImages/Final/LW6003/lw6003_28072680_960x540.jpg',
      'DELUXE KING ROOM',
      'Enjoy a tranquil stay in this 35 sqm room with a plush king bed, modern amenities, and a view of Cebu\'s serene mountain range.',
      '35 sqm', '2 adults', 'King Bed', 'Aircon, Flat-screen TV, Mini Fridge, Wi-Fi, Coffee Maker')");

      $conn ->query("Insert into RoomsandSuites (Img, RoomName, RoomDesc, RoomSize, RoomAccomodation, Beds, Utilities)
      values ('https://static-new.lhw.com/HotelImages/Rooms/Final/7006/room_7006_C2T_1_300x240.jpg',
      'PREMIUM TWIN ROOM',
      'Wake up to fresh sea breeze and ocean views in this 38 sqm room with two twin beds—ideal for friends or colleagues.',
      '38 sqm', '2 adults', ' 2 Twin Beds', 'Aircon, TV, Work Desk, Mini Bar, High-Speed Wi-Fi')");

      $conn ->query("Insert into RoomsandSuites (Img, RoomName, RoomDesc, RoomSize, RoomAccomodation, Beds, Utilities)
      values ('https://images.trvl-media.com/lodging/1000000/440000/438500/438418/0a9e7004.jpg?impolicy=resizecrop&rw=575&rh=575&ra=fill',
      'COURTYARD QUEEN ROOM',
      'This 30 sqm room offers cozy comfort, a private courtyard view, and a queen bed—perfect for couples or solo travelers.',
      '30 sqm', '2 adults', ' Queen Bed', 'Aircon, Smart TV, Wi-Fi, Electric Kettle, Hair Dryer')");

      $conn ->query("Insert into RoomsandSuites (Img, RoomName, RoomDesc, RoomSize, RoomAccomodation, Beds, Utilities)
      values ('https://res.cloudinary.com/lastminute/image/upload/q_auto/v1675611494/unqsnclmptl05ifemape.jpg',
      'EXECUTIVE SUITE',
      'Unwind in this spacious 50 sqm suite with a separate living area, panoramic views, and a luxurious king bed..',
      '50 sqm', '2 adults, 1 child', 'King Bed + Sofa Bed', 'Aircon, Two Smart TVs, Living Area, Mini Bar, Bathtub, Nespresso Machine')");

      $conn ->query("Insert into RoomsandSuites (Img, RoomName, RoomDesc, RoomSize, RoomAccomodation, Beds, Utilities)
      values ('https://static-new.lhw.com/HotelImages/Rooms/Final/7006/room_7006_C1Q_1_300x240.jpg',
      'HONEYMOON SUITE',
      'Celebrate love in this romantic 45 sqm suite featuring ocean views, soft lighting, and an indulgent king bed.',
      '45 sqm', '2 adults', 'King Bed', 'Aircon, Mood Lighting, Jacuzzi, Private Balcony, Bluetooth Speaker, Wine Fridge')");

      $conn ->query("Insert into RoomsandSuites (Img, RoomName, RoomDesc, RoomSize, RoomAccomodation, Beds, Utilities)
      values ('https://static-new.lhw.com/HotelImages/Final/LW6003/lw6003_80216878_790x490.jpg',
      'PRESIDENTIAL SUITE',
      'Our most luxurious 70 sqm suite features a private garden terrace, elegant interiors, and ample space for family stays.',
      '70 sqm', '2 adults, 2 children', '2 King Bed + 2 Single Beds', 'Aircon, Multiple Smart TVs, Kitchenette, Dining Area, Jacuzzi, Private Garden, Butler Service')");
  }


  $conn->query("CREATE TABLE IF NOT EXISTS Rooms (
      RoomID INT UNSIGNED PRIMARY KEY,
      Price DECIMAL(10, 2) NOT NULL,
      roomtype VARCHAR(50) NOT NULL,
      RaSid INT UNSIGNED NOT NULL,
      FOREIGN KEY (RaSid) REFERENCES RoomsandSuites(RaSid),
      Avail VARCHAR(50) NOT NULL DEFAULT 'Available'
  )");


  if($conn->query("SELECT COUNT(*) FROM Rooms")->fetch_row()[0] == 0) {
    for($i = 1; $i <= 60; $i++) {
      if($i <=5){
        $conn->query("INSERT INTO Rooms (RoomID, Price, roomtype, RaSid) VALUES ($i, 2000, 'Standard Room', 1)");
      }
      elseif($i > 5 && $i <= 10){
        $conn->query("INSERT INTO Rooms (RoomID, Price, roomtype, RaSid) VALUES ($i, 3000, 'Deluxe Room', 1)");
      }
      elseif($i > 10 && $i <= 15){
        $conn->query("INSERT INTO Rooms (RoomID, Price, roomtype, RaSid) VALUES ($i, 2500, 'Standard Room', 2)");
      }
      elseif($i > 15 && $i <= 20){
        $conn->query("INSERT INTO Rooms (RoomID, Price, roomtype, RaSid) VALUES ($i, 3500, 'Deluxe Room', 2)");
      }
      elseif($i > 20 && $i <= 25){
        $conn->query("INSERT INTO Rooms (RoomID, Price, roomtype, RaSid) VALUES ($i, 3100, 'Standard Room', 3)");
      }
      elseif($i > 25 && $i <= 30){
        $conn->query("INSERT INTO Rooms (RoomID, Price, roomtype, RaSid) VALUES ($i, 4100, 'Deluxe Room', 3)");
      }
      elseif($i > 30 && $i <= 35){
        $conn->query("INSERT INTO Rooms (RoomID, Price, roomtype, RaSid) VALUES ($i, 3500, 'Standard Room', 4)");
      }
      elseif($i > 35 && $i <= 40){
        $conn->query("INSERT INTO Rooms (RoomID, Price, roomtype, RaSid) VALUES ($i, 4500, 'Deluxe Room', 4)");
      }
      elseif($i > 40 && $i <= 45){
        $conn->query("INSERT INTO Rooms (RoomID, Price, roomtype, RaSid) VALUES ($i, 4100, 'Standard Room', 5)");
      }
      elseif($i > 45 && $i <= 50){
        $conn->query("INSERT INTO Rooms (RoomID, Price, roomtype, RaSid) VALUES ($i, 5100, 'Deluxe Room', 5)");
      }
      elseif($i > 50 && $i <= 55){
        $conn->query("INSERT INTO Rooms (RoomID, Price, roomtype, RaSid) VALUES ($i, 4500, 'Standard Room', 6)");
      }
      elseif($i > 55 && $i <= 60){
        $conn->query("INSERT INTO Rooms (RoomID, Price, roomtype, RaSid) VALUES ($i, 5500, 'Deluxe Room', 6)");
      }
    }
  }

  $conn->query("CREATE TABLE IF NOT EXISTS MyReservation (
      ReservationID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      UserID INT UNSIGNED NOT NULL,
      RoomID INT UNSIGNED NOT NULL,
      RaSid INT UNSIGNED NOT NULL,
      CheckIn DATE NOT NULL,
      CheckOut DATE NOT NULL,
      TotalPrice INT NOT NULL,
      FOREIGN KEY (UserID) REFERENCES UserAccount(UserID),
      FOREIGN KEY (RoomID) REFERENCES Rooms(RoomID),
      FOREIGN KEY (RaSid) REFERENCES RoomsandSuites(RaSid)
  )");
?>


<!DOCTYPE html
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home</title>
    
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
 
    
}

.main-image {
    width: 100%;
    height: 100vh;
    object-fit: cover;
    display: block;
    filter: brightness(60%); 
    position: relative;
    z-index: 1;
  }
  .accommodations-container {
  display: flex;
  width: 100%;
  min-height: 100vh;
  background-image: url('icons/bg-1.png');
}

.accommodations-desc {
  flex: 1;
  margin: 120px 60px 0 60px;
}

.accommodations-image {
  flex: 1;
}

.books-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.books-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}


.acc-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.acc-desc-title {
  color: #d0b683;
  font-family: "Cormorant Garamond", serif;
  font-optical-sizing: auto;
  font-weight: 600;
  font-style: italic;
  font-size: 32px;
  margin-bottom: 20px;
}

.acc-desc {
  text-align: justify;
  font-family: "Quicksand", sans-serif;
  font-optical-sizing: auto;
  font-weight: 400;
  font-style: normal;
  font-size: 16px;
  line-height: 1.6;
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

.pop-offers-text {
    margin-left: 200px;
    margin-right: 200px;
    text-align: center;
}

.offers-cards {
    display: flex;
    justify-content: space-around;
    margin: 50px 0;
    padding: 0 130px;
    height: 170px;
}

.card {
    width: 300px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    padding: 20px;
    transition: transform 0.3s ease;
}

.card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.card h3 {
    font-size: 18px;
    margin-top: 20px;
    font-weight: bold;
}

.card p {
    font-size: 14px;
    margin-top: 10px;
    color: #555;
}

.book-now {
    display: flex;
    justify-content: center;
    margin-top: 40px; 
}

.book-now .book {
    padding: 15px 30px;
    background-color: #3d3d3d; 
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
    transition: background-color 0.3s;
}

.book-a-stay-content h2 {
  font-family: "Cormorant Garamond", serif;
  font-optical-sizing: auto;
  font-weight: 600;
  font-style: italic;
  font-size: 32px;
  margin-bottom: 20px;
  color: #781924; 
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

.centered-title {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: white;
    z-index: 2;
    font-family: "Cormorant Garamond", serif;
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

  .hotel-desc {
    font-family: "Satisfy", cursive;
  font-weight: 400;
  font-style: normal;
  text-align: center;
  font-size: 35px;
  margin-top: 90px;
  color: #781924;
  margin-bottom: 70px;
  }

  .second-image {
    width: 100%;
    height: 70vh;
    object-fit: cover;
    display: block;
  }

  .acc-button {
    border: none;
    background-color: white;
    font-family: "Cormorant Garamond", serif;
    font-optical-sizing: auto;
    font-weight: 600;
    font-size: 20px;
    margin-top: 20px;
    color: black;
    padding-bottom: 10px;
    border-bottom: 2px solid black;
}

.book-button {
    border: none;
    background-color: white;
    font-family: "Cormorant Garamond", serif;
    font-optical-sizing: auto;
    font-weight: 600;
    font-size: 20px;
    margin-top: 20px;
    color: black;
    padding-bottom: 10px;
    border-bottom: 2px solid black;
}

.book-button:hover {
    color: #d0b683;
    border-bottom: 2px solid #d0b683; 
}

.acc-button:hover {
    color: #d0b683;
    border-bottom: 2px solid #d0b683; 
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


.books-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.book-a-stay {
  background-image: url('icons/bg-1.png');
  background-repeat: repeat;
  background-size: contain;
  padding: 90px;
  color: white;
}


.book-a-stay-content h2 {
  font-size: 2.5rem;
  margin-bottom: 20px;
}

.book-a-stay-content p {
  text-align: justify;
  font-family: "Quicksand", sans-serif;
  font-optical-sizing: auto;
  font-weight: 400;
  font-style: normal;
  font-size: 16px;
  line-height: 1.6;
  color: black
}

.book-a-stay .acc-button {
  font-size: 1rem;
  padding: 10px 25px;
  background-color: #ffffff;
  color: #000;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-weight: bold;
}

.book-a-stay .acc-button:hover {
  background-color: #dddddd;
}

.exclusive-offers {
  
  background-image: url('icons/bg-2.png');
  background-repeat: repeat;
  background-size: auto; /* or 'contain' if you want each tile to fit fully */
  background-position: top left;
  padding: 60px 20px;
  color: white;
}

.offers-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 20px;
}

.offer-card {
  background-color: rgba(255, 255, 255, 0.9);
  padding: 20px;
  border-radius: 10px;
  width: 300px;
  text-align: center;
  color: black;
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
      src="https://www.outthere.travel/wp-content/uploads/2019/07/LM2_FIN.jpg"
      class="main-image"
      alt="bg"
    />

    <div class="centered-title show-on-scroll">
      <img
        src="icons/logo-2.png"
        alt="Logo"
        class="center-logo"
      />
      <h1>LA GINTA REAL</h1>
      <h2>PHILIPPINES</h2>
    </div>

    <span class="span-menu">
      <a href="Home.php">LA GINTA REAL</a>
      <a href="Rooms&Suites.php">ROOMS & SUITES</a>
      <a href="">OFFERS</a>
      <a href="AboutUs.php">ABOUT US</a>
    </span>
    <div class="long-line"></div>

    <h2 class="hotel-desc">
      True to its singular identity, <br />La Ginta Real is constantly
      reinventing itself, fashioning a unique magic woven <br />
      of elegance and exception.
    </h2>
    <img
      src="https://northafricapost.com/wp-content/uploads/2018/11/la-mamounia-hotel.jpg"
      alt=""
      class="second-image"
    />
    <section class="accommodations-container">
      <div class="accommodations-desc">
        <h3 class="acc-desc-title">
          Experience La Ginta Real, a sensory journey
        </h3>
        <p class="acc-desc">
          From the moment you arrive, you will be captivated by the harmony of
          the surroundings, the opulence of the materials and the excellence of
          the craftsmanship revealed in each and every detail. <br />
          <br />
          Wherever you look, you will be struck by a majestic beauty. A beauty
          that you will long to touch, to feel beneath your fingertips: the
          softness of velvet and leather, the contours of sculpted plaster and
          zellige tilework, the freshness of cool marble.
        </p>
        <button class="acc-button">ROOMS & SUITES</button>
      </div>

      <div class="accommodations-image">
        <img
          src="https://www.telegraph.co.uk/content/dam/Travel/hotels/africa/morocco/marrakech/la-mamounia-marrakech-bedroom-2.jpg"
          alt=""
          class="acc-image"
        />
      </div>
  </section>
<!-- Exclusive Offers Section -->
<section class="exclusive-offers">
  <h2 style="text-align: center; color: white; font-size: 2.5rem; margin-bottom: 40px;">Exclusive Offers</h2>
  <div class="offers-container" >
    <!-- Card 1 -->
    <div class="offer-card" >
      <h3>Offer Title 1</h3>
      <p>Description for offer 1 goes here.</p>
    </div>
    <!-- Card 2 -->
    <div class="offer-card" >
      <h3>Offer Title 2</h3>
      <p>Description for offer 2 goes here.</p>
    </div>
    <!-- Card 3 -->
    <div class="offer-card" >
      <h3>Offer Title 3</h3>
      <p>Description for offer 3 goes here.</p>
    </div>
  </div>
</section>


<section class="accommodations-container">
      

      <div class="accommodations-image">
      <img
      src="https://mamounia.com/media/cache/jadro_resize/rc/in9iLpVz1743575366/jadroRoot/medias/653fcee154467/6540e50e0c796/6540e5783a736/accueil-entree.jpeg"
      alt=""
      class="acc-image"
    />
      </div>
      <div class="accommodations-desc">
        <h2 class="acc-desc-title">
        To return to La Ginta Real time and time again
        </h2>
        <p class="acc-desc">
        We are both honoured and inspired to have been nominated the best hotel in the world on several occasions.<br><br>
      Our excellence is the result of tireless endeavour, the unfailing commitment of our 800 employees, constant self-questioning, and innovations that are daring yet respectful of the past. This is how La Mamounia remains a place of legend that moves with the times while always remaining in tune with the here and now.<br><br>
      Our greatest reward is the loyalty of our customers, who love coming back here because they are made to feel so at home.<br />
         
        </p>
        <button class="acc-button">BOOK A STAY</button>
      </div>
  </section>
  </body>
</html>
