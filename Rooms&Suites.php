
<?php
  /**
   * Purpose:
   *   - This page displays all available rooms and suites for La Ginta Real Hotel.
   *   - Allows users to browse, search, and view details for each room or suite.
   *   - Provides a "BOOK NOW" button for each room, redirecting users to the reservation page.
   *   - Fetches room data dynamically from the 'roomsandsuites' table in the database.
 */
  session_start();
  // Get the current username from session if logged in
  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

  // Database connection configuration
  $servername = "localhost";
  $dbuser = "root";
  $password = "";
  $dbname = "hotel_db";
  $errorMsg = [];
  $successMsg = "";

  // Connect to MySQL server and select database
  $conn = new mysqli($servername, $dbuser, $password, $dbname);
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  // Handle booking form submission
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Quicksand:wght@300..700&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&family=Roboto:ital,wght@0,100..900;1,100..900&family=Satisfy&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="styles/Rooms&Suites.css">
</head>

<body>
  <?php include 'Navbar.php'; ?>
  <script src="scripts/RS.js"></script>

  <main>
    <img
      src="https://www.ft.com/__origami/service/image/v2/images/raw/https%3A%2F%2Fd1e00ek4ebabms.cloudfront.net%2Fproduction%2F54089c95-8f88-4f62-a702-b77d2cc3a6c4.jpg?source=next-article&fit=scale-down&quality=highest&width=700&dpr=1"
      class="main-image"
      alt="bg"
    />

       <?php include 'SpanMenu.php'; ?>

    <section class="search-section">
      <div class="search-container">
        <input type="text" id="roomSearch" placeholder="Search for rooms or suites..." onkeyup="filterRooms()" />
        <i class="bi bi-search search-icon"></i>
      </div>
    </section>

    <header>
      <h1>ROOMS & SUITES</h1>
      <p class="room-description">
        Discover our refined selection of rooms and suites, each designed with elegance, comfort, and style to make your stay truly unforgettable.
      </p>
    </header>

    <section class="room-container">
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
          echo '<button type="submit" name="book">BOOK NOW</button>';
          echo '</div></div>';
          echo '</form>';
        }
      ?>
    </section>
  </main>
  <?php include 'Footer.php'; ?>

</body>
</html>