
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
  <!-- Room 1: DELUXE KING ROOM -->
  <div class="room-section">
    <img src="https://static-new.lhw.com/HotelImages/Final/LW6003/lw6003_28072680_960x540.jpg" alt="Room 1" class="room-image">
    <div class="room-info">
      <h2>DELUXE KING ROOM</h2>
      <p>Enjoy a tranquil stay in this 35 sqm room with a plush king bed, modern amenities, and a view of Cebu's serene mountain range.</p>
      <ul>
        <li><i class="bi bi-rulers"></i> 35 sqm</li>
        <li><i class="bi bi-person-lines-fill"></i> 2 adults</li>
        <li><i class="bi bi-basket"></i> King Bed</li>
      </ul>
      <a href="Reservation.php?room_id=1&room_name=DELUXE+KING+ROOM&room_size=35sqm&room_capacity=2+adults&bed_types=King+Bed&room_image=https://static-new.lhw.com/HotelImages/Final/LW6003/lw6003_28072680_960x540.jpg&room_price=1500">
        <button>BOOK NOW</button>
      </a>
    </div>
  </div>

  <!-- Room 2: PREMIUM TWIN ROOM -->
  <div class="room-section">
    <img src="https://static-new.lhw.com/HotelImages/Rooms/Final/7006/room_7006_C2T_1_300x240.jpg" alt="Room 2" class="room-image">
    <div class="room-info">
      <h2>PREMIUM TWIN ROOM</h2>
      <p>Wake up to fresh sea breeze and ocean views in this 38 sqm room with two twin beds—ideal for friends or colleagues.</p>
      <ul>
        <li><i class="bi bi-rulers"></i> 38 sqm</li>
        <li><i class="bi bi-person-lines-fill"></i> 2 adults</li>
        <li><i class="bi bi-basket"></i> 2 Twin Beds</li>
      </ul>
      <a href="Reservation.php?room_id=2&room_name=PREMIUM+TWIN+ROOM&room_size=38sqm&room_capacity=2+adults&bed_types=2+Twin+Beds&room_image=https://static-new.lhw.com/HotelImages/Rooms/Final/7006/room_7006_C2T_1_300x240.jpg&room_price=2000">
        <button>BOOK NOW</button>
      </a>
    </div>
  </div>

  <!-- Room 3: COURTYARD QUEEN ROOM -->
  <div class="room-section">
    <img src="https://images.trvl-media.com/lodging/1000000/440000/438500/438418/0a9e7004.jpg?impolicy=resizecrop&rw=575&rh=575&ra=fill" alt="Room 3" class="room-image">
    <div class="room-info">
      <h2>COURTYARD QUEEN ROOM</h2>
      <p>This 30 sqm room offers cozy comfort, a private courtyard view, and a queen bed—perfect for couples or solo travelers.</p>
      <ul>
        <li><i class="bi bi-rulers"></i> 30 sqm</li>
        <li><i class="bi bi-person-lines-fill"></i> 2 adults</li>
        <li><i class="bi bi-basket"></i> Queen Bed</li>
      </ul>
      <a href="Reservation.php?room_id=3&room_name=COURTYARD+QUEEN+ROOM&room_size=30sqm&room_capacity=2+adults&bed_types=Queen+Bed&room_image=https://images.trvl-media.com/lodging/1000000/440000/438500/438418/0a9e7004.jpg&room_price=2500">
        <button>BOOK NOW</button>
      </a>
    </div>
  </div>

  <!-- Suite 1: EXECUTIVE SUITE -->
  <div class="room-section">
    <img src="https://res.cloudinary.com/lastminute/image/upload/q_auto/v1675611494/unqsnclmptl05ifemape.jpg" alt="Executive Suite" class="room-image">
    <div class="room-info">
      <h2>EXECUTIVE SUITE</h2>
      <p>Unwind in this spacious 50 sqm suite with a separate living area, panoramic views, and a luxurious king bed.</p>
      <ul>
        <li><i class="bi bi-rulers"></i> 50 sqm</li>
        <li><i class="bi bi-person-lines-fill"></i> 2 adults, 1 child</li>
        <li><i class="bi bi-basket"></i> King Bed + Sofa Bed</li>
      </ul>
      <a href="Reservation.php?room_id=4&room_name=EXECUTIVE+SUITE&room_size=50sqm&room_capacity=2+adults,+1+child&bed_types=King+Bed+and+Sofa+Bed&room_image=https://res.cloudinary.com/lastminute/image/upload/q_auto/v1675611494/unqsnclmptl05ifemape.jpg&room_price=3500">
        <button>BOOK NOW</button>
      </a>
    </div>
  </div>

  <!-- Suite 2: HONEYMOON SUITE -->
  <div class="room-section">
    <img src="https://static-new.lhw.com/HotelImages/Rooms/Final/7006/room_7006_C1Q_1_300x240.jpg" alt="Honeymoon Suite" class="room-image">
    <div class="room-info">
      <h2>HONEYMOON SUITE</h2>
      <p>Celebrate love in this romantic 45 sqm suite featuring ocean views, soft lighting, and an indulgent king bed.</p>
      <ul>
        <li><i class="bi bi-rulers"></i> 45 sqm</li>
        <li><i class="bi bi-person-lines-fill"></i> 2 adults</li>
        <li><i class="bi bi-basket"></i> King Bed</li>
      </ul>
      <a href="Reservation.php?room_id=5&room_name=HONEYMOON+SUITE&room_size=45sqm&room_capacity=2+adults&bed_types=King+Bed&room_image=https://static-new.lhw.com/HotelImages/Rooms/Final/7006/room_7006_C1Q_1_300x240.jpg&room_price=4000">
        <button>BOOK NOW</button>
      </a>
    </div>
  </div>

  <!-- Suite 3: ROYAL GARDEN SUITE -->
  <div class="room-section">
    <img src="https://static-new.lhw.com/HotelImages/Final/LW6003/lw6003_80216878_790x490.jpg" alt="Royal Garden Suite" class="room-image">
    <div class="room-info">
      <h2>ROYAL GARDEN SUITE</h2>
      <p>Our most luxurious 70 sqm suite features a private garden terrace, elegant interiors, and ample space for family stays.</p>
      <ul>
        <li><i class="bi bi-rulers"></i> 70 sqm</li>
        <li><i class="bi bi-person-lines-fill"></i> 2 adults, 2 children</li>
        <li><i class="bi bi-basket"></i> King Bed + 2 Single Beds</li>
      </ul>
      <a href="Reservation.php?room_id=6&room_name=ROYAL+GARDEN+SUITE&room_size=70sqm&room_capacity=2+adults,+2+children&bed_types=King+and+2+Single+Beds&room_image=https://static-new.lhw.com/HotelImages/Final/LW6003/lw6003_80216878_790x490.jpg&room_price=4500">
        <button>BOOK NOW</button>
      </a>
    </div>
  </div>
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
