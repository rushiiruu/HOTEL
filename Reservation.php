
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
  $room_id = $_SESSION['room_id'];

  $rooms = $conn->query("Select * from MyReservation where RaSid = $room_id")->fetch_all(MYSQLI_ASSOC);
  $avail = $conn->query("Select * from Rooms where RaSid = $room_id and Avail = 'Available'")->fetch_all(MYSQLI_ASSOC);
  $UserID = $conn->query("select UserID from UserAccount where Username = '$username'")->fetch_all(MYSQLI_ASSOC);

  if (isset($_POST['submit'])) {
      $checkin = $_POST['checkin'];
      $checkout = $_POST['checkout'];
      $adults = $_POST['adults'];
      $children = $_POST['children'];
      $room_type = $_POST['room-type'];
      $check1 = $_POST['checkin'];
      $check2 = $_POST['checkout'];
   


      foreach ($avail as $row) {
        foreach ($rooms as $row2) {
          if ($row['RoomID'] == $row2['RoomID']) {   
            $count = 0;
            while ($check1 != $check2) {
              if ($row['CheckIn'] == $check1) {
                $count++;
                $check1 = date('Y-m-d', strtotime($check1 . ' +1 day'));
              } 
            }
            if ($count == 0){
              $conn->query("Insert Into MyReservation (CheckIn, CheckOut, Adults, Children, UserID,) Values ('$checkin', '$checkout', '$adults', '$children', '$room_type')");
            }
          }
        }
      }

      // Validate inputs
      if (empty($checkin) || empty($checkout) || empty($adults) || empty($children) || empty($room_type)) {
          $errorMsg[] = "All fields are required.";
      } else {
          // Insert into database
          $stmt = $conn->prepare("INSERT INTO MyReservation (CheckIn, CheckOut, Adults, Children, RoomType) VALUES (?, ?, ?, ?, ?)");
          $stmt->bind_param("sssss", $checkin, $checkout, $adults, $children, $room_type);
          if ($stmt->execute()) {
              $successMsg = "Reservation successful!";
          } else {
              $errorMsg[] = "Error: " . $stmt->error;
          }
          $stmt->close();
      }
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
  height: auto;
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

.room-banner {
  position: relative;
  width: 100%;
  height: 60vh;
  overflow: hidden;
}

.room-banner img.main-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: brightness(50%);
}

.room-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 5%;
  color: white;
}

.room-title {
  font-size: 50px;
  font-weight: 600;
  font-family: "Cormorant Garamond", serif;
}

.room-form {
  background-color: rgba(255, 255, 255, 0.9);
  padding: 20px;
  border-radius: 10px;
  color: black;
  font-family: "Quicksand", sans-serif;
  display: flex;
  flex-direction: column;
  gap: 10px;
  max-width: 300px;
  margin-left: 100px;
}

.room-form label {
  display: flex;
  flex-direction: column;
  font-size: 14px;
}

.room-form select,
.room-form input {
  padding: 8px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.reserve-btn {
  padding: 12px;
  background-color: black;
  color: white;
  border: none;
  cursor: pointer;
  font-weight: bold;
  transition: background 0.3s ease;
  margin-top: 10px;
}

.reserve-btn:hover {
  background-color: #333;
}

.people-inputs {
  margin-top: 10px;
}

.counter {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 15px;
}

.counter button {
  background-color: #d0b683;
  color: white;
  border: none;
  padding: 5px 10px;
  font-size: 18px;
  cursor: pointer;
  border-radius: 5px;
  font-weight: bold;
}

.counter span {
  min-width: 20px;
  text-align: center;
  font-size: 16px;
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
      <a href="Rooms&Suites.">ROOMS & SUITES</a>
      <a href="">OFFERS</a>
      <a href="AboutUs.php">ABOUT US</a>
    </span>
    <div class="long-line"></div>
    <?php
        if (isset($_SESSION['room_id']) && is_numeric($_SESSION['room_id'])) {
            $room_id = intval($_SESSION['room_id']); // Sanitize the room ID
            $result = $conn->query("SELECT * FROM RoomsandSuites WHERE RaSid = $room_id");

            if ($result && $result->num_rows > 0) {
                $room = $result->fetch_assoc(); // Fetch a single row
                $room_name = $room['RoomName'] ?? 'No room selected';
                $room_description = $room['RoomDesc'] ?? 'No description available.';
                $room_image = $room['Img'] ?? 'https://via.placeholder.com/500';
            } else {
                $room_name = 'No room selected';
                $room_description = 'No description available.';
                $room_price = 'Price not available.';
                $room_image = 'https://via.placeholder.com/500';
            }
        } else {
            $room_name = 'No room selected';
            $room_description = 'No description available.';
            $room_price = 'Price not available.';
            $room_image = 'https://via.placeholder.com/500';
}

        echo "<h1>{$room_name}</h1>";
        echo "<p>{$room_description}</p>"
        ?>
      
  
<div class="room-container">
        <!-- Room Display Section -->
        <?php


        echo "
        <div class='room-section'>
            <img class='room-image' src='{$room_image}' alt='{$room_name}'>
          ";
        ?>

        <!-- Reservation Form -->
    <div class="room-form">
        <label for="checkin">Check-in Date:</label>
        <input type="date" id="checkin" name="checkin">
        <label for="checkout">Check-out Date:</label>
        <input type="date" id="checkout" name="checkout">
        <label for="room-type">Room Type:</label>
        <select id="room-type" name="room-type">
          <option value="" disabled selected>Select a room type</option>
          <option value="standard">Standard Room</option>
          <option value="deluxe">Deluxe Room</option>
        </select>

    <div class="people-inputs">
      <label>Adults:</label>
      <div class="counter">
        <button type="button" onclick="decrement('adult')">-</button>
        <span id="adult-count">1</span>
        <button type="button" onclick="increment('adult')">+</button>
      </div>

      <label>Children:</label>
      <div class="counter">
        <button type="button" onclick="decrement('child')">-</button>
        <span id="child-count">0</span>
        <button type="button" onclick="increment('child')">+</button>
      </div>
    </div>

<!-- Hidden inputs to submit values -->
<input type="hidden" name="adults" id="adultsInput" value="1">
<input type="hidden" name="children" id="childrenInput" value="0">

            <button type="submit" class="reserve-btn">Confirm Reservation</button>
        </div>
    </div>

    <div class="price-output">
  <p>Total Price: <span id="calculated-price">₱0</span></p>
</div>

    <script>

  const baseRoomPrice = <?php echo isset($_GET['room_price']) ? (int)$_GET['room_price'] : 0; ?>;
        // Menu Toggle Functionality
        function toggleMenu() {
            var sideMenu = document.getElementById('sideMenu');
            sideMenu.classList.toggle('show');
        }

        let adultCount = 1;
  let childCount = 0;

  function increment(type) {
    if (type === 'adult') {
      adultCount++;
      document.getElementById('adult-count').textContent = adultCount;
      document.getElementById('adultsInput').value = adultCount;
    } else if (type === 'child') {
      childCount++;
      document.getElementById('child-count').textContent = childCount;
      document.getElementById('childrenInput').value = childCount;
    }
  }

  function decrement(type) {
    if (type === 'adult' && adultCount > 1) {
      adultCount--;
      document.getElementById('adult-count').textContent = adultCount;
      document.getElementById('adultsInput').value = adultCount;
    } else if (type === 'child' && childCount > 0) {
      childCount--;
      document.getElementById('child-count').textContent = childCount;
      document.getElementById('childrenInput').value = childCount;
    }
  }

  function calculatePrice() {
  const roomType = document.getElementById('room-type').value;
  const checkin = new Date(document.getElementById('checkin').value);
  const checkout = new Date(document.getElementById('checkout').value);

  if (isNaN(checkin.getTime()) || isNaN(checkout.getTime()) || !roomType) {
    document.getElementById('calculated-price').textContent = '₱0';
    return;
  }

  const timeDiff = checkout - checkin;
  const nights = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

  if (nights <= 0) {
    document.getElementById('calculated-price').textContent = '₱0';
    return;
  }

  let pricePerNight = baseRoomPrice;
  if (roomType === 'deluxe') {
    pricePerNight += 1000;
  }

  const totalPrice = pricePerNight * nights;
  document.getElementById('calculated-price').textContent = `₱${totalPrice}`;
}

document.getElementById('room-type').addEventListener('change', calculatePrice);
document.getElementById('checkin').addEventListener('change', calculatePrice);
document.getElementById('checkout').addEventListener('change', calculatePrice);

    </script>
</body>
</html>
