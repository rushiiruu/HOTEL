
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

  // Connect to the database
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

  // Insert default admin accounts if table is empty
  if($conn->query("SELECT COUNT(*) FROM UserAccount")->fetch_row()[0] == 0) {
    $conn->query("INSERT INTO UserAccount (Fname, Lname, username, password, usertype, Birthday) VALUES (
    'John', 'Doe', 'Admin1', '" . password_hash('admin1', PASSWORD_BCRYPT) . "', 'Admin', '1990-01-01')");
  
    $conn->query("INSERT INTO UserAccount (Fname, Lname, username, password, usertype, Birthday) VALUES (
    'Jane', 'Doe', 'Admin2', '" . password_hash('admin2', PASSWORD_BCRYPT) . "', 'Admin', '1990-01-01')");
  }

  // Create RoomsandSuites table if not exists
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

  // Insert default room data if table is empty
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

  // Create Rooms table if not exists
  $conn->query("CREATE TABLE IF NOT EXISTS Rooms (
      RoomID INT UNSIGNED PRIMARY KEY,
      Price DECIMAL(10, 2) NOT NULL,
      roomtype VARCHAR(50) NOT NULL,
      RaSid INT UNSIGNED NOT NULL,
      FOREIGN KEY (RaSid) REFERENCES RoomsandSuites(RaSid),
      Avail VARCHAR(50) NOT NULL DEFAULT 'Available'
  )");

  // Insert default room data if table is empty
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

  // Create MyReservation table if not exists
  $conn->query("CREATE TABLE IF NOT EXISTS MyReservation (
      ReservationID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      UserID INT UNSIGNED NOT NULL,
      RoomID INT UNSIGNED NOT NULL,
      RaSid INT UNSIGNED NOT NULL,
      CheckIn DATE NOT NULL,
      CheckOut DATE NOT NULL,
      NoOfAdults INT NOT NULL,
      NoOfChildren INT NOT NULL,
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
    <link rel= "stylesheet" href="styles/Home.css">
  </head>
  <body>
    <script src="Home.js"></script>
    <?php include 'Navbar.php'; ?>


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
  <h2 style="text-align: center; color: #781924; font-size: 2.5rem; margin-bottom: 40px;">What makes La Ginta Real so unique in their eyes?  </h2>
  <p>An art of living inherited from a long tradition of care and attention. A unique atmosphere perfumed with the delicate scent of Moroccan dates. Welcoming smiles and thoughtful touches.

  <br><br>A sense of hospitality that translates into a thousand and one details. From your first visit, we remember your preferences: your ideal room temperature, your favourite coffee, the drinks and dishes you like...

  <br><br> So, we know not only what will please you during your next stay, but also what will surprise you and make you want to come back for an experience that is always fresh and new. </p>
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


  <footer>
    <div class="footer-container">
      <div class="footer-logo">
        <img src="icons/logo-login (1).png" class="footer-logo-img" alt="">
        <h1>LA GINTA REAL</h1>
        <h3>PHILIPPINES</h3>
      </div>
      <div class="footer-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
        <a href="#">Contact Us</a>
      </div>
    </div>
    <p>&copy; 2023 La Ginta Real. All rights reserved.</p>
  </footer>

  
  </body>
</html>
