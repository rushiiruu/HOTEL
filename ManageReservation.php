
<?php
  session_start();
  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

  // Database connection
  $servername = "localhost";
  $dbuser = "root";
  $password = "";
  $dbname = "hotel_db";
  $conn = new mysqli($servername, $dbuser, $password, $dbname);
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
  
  $stmt = $conn->prepare("SELECT UserID FROM UserAccount WHERE Username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $userResult = $stmt->get_result();
  $userRow = $userResult->fetch_assoc();
  $UserID = $userRow['UserID'];
  $stmt->close();
  // Handle cancel request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
  $roomIDToCancel = $_POST['room_id'];
  $sqlDelete = "DELETE FROM MyReservation WHERE RoomID = ?";
  $stmt = $conn->prepare($sqlDelete);
  $stmt->bind_param("i", $roomIDToCancel);
  $stmt->execute();
  $stmt->close();
  // Refresh to reflect the changes
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

?>

<?php include 'check_login.php'; ?>

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
    <link rel="stylesheet" href="styles/ManageReservation.css">
    
  </head>
  <body>
    
  <nav id="navbar" class="scrolled">

  <a href="#" class="menu-icon" onclick="toggleMenu()">
        <i class="bi bi-list" id="menu"></i>
      </a>

      <a href="#" class="hotel-name">
        LA GINTA REAL
        <span class="hotel-location">PHILIPPINES</span>
      </a>

      <div class="nav-right">
        <a href="ManageReservation.php">MY RESERVATION</a>
        <a href="Rooms&Suites.php">BOOK</a>
      </div>
    </nav>
    <div id="sideMenu" class="side-menu">

  <!-- User icon and name at the top -->
  <div class="user-info">
    <i class="bi bi-person-circle" id="user-icon"></i>
    <span class="username">
      <?php echo $username ? htmlspecialchars($username) : "Guest"; ?>
    </span>
  </div>

  <!-- Close button -->
  <button class="close-menu" onclick="toggleMenu()">
    <i class="bi bi-x"></i>
  </button>

  <!-- Navigation menu -->
  <ul>
    <li><a href="Home.php">Home</a></li>
    <li><a href="Rooms&Suites.php">Rooms & Suites</a></li>
    <li><a href="#">Exclusive Offers</a></li>
    <li><a href="AboutUs.php">About Us</a></li>
    <li><a href="#">Contact Us</a></li>
    <li><a href="ManageReservation.php">My Reservation</a></li>
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


    
<div class="reservation-container">
  <h2>My Reservations</h2>
  <table class="reservation-table">
    <thead>
      <tr>
        <th>Room ID</th>
        <th>Price</th>
        <th>Check-In</th>
        <th>Check-Out</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php 
       $result = $conn->query("SELECT * FROM MyReservation WHERE UserID = $UserID");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $roomID = $row['RoomID'];
                $price = $row['TotalPrice'];
                $checkIn = $row['CheckIn'];
                $checkOut = $row['CheckOut'];

                echo "<tr>";
                echo "<td>" . htmlspecialchars($roomID) . "</td>";
                echo "<td>" . htmlspecialchars($price) . "</td>";
                echo "<td>" . htmlspecialchars($checkIn) . "</td>";
                echo "<td>" . htmlspecialchars($checkOut) . "</td>";
                echo "<td>";
                echo "<form method='POST' onsubmit=\"return confirm('Are you sure you want to cancel this reservation?');\">";
                echo "<input type='hidden' name='action' value='cancel'>";
                echo "<input type='hidden' name='room_id' value='" . htmlspecialchars($roomID) . "'>";
                
                echo "<button type='button' class='dropbtn' onclick=\"openModal(" . htmlspecialchars($roomID) . ")\">Cancel</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No reservations found.</td></tr>";
        }
      ?>
    </tbody>
  </table>
</div>

<div id="cancel-modal" class="modal">
  <div class="modal-content">
    <p>Are you sure you want to cancel this reservation?</p>
    <form method="POST" id="cancel-form">
      <input type="hidden" name="action" value="cancel">
      <input type="hidden" name="room_id" id="room-id-input">
      <button type="submit" class="confirm-btn">Yes, Cancel</button>
      <button type="button" class="close-btn" onclick="closeModal()">No, Go Back</button>
    </form>
  </div>
</div>
       
    <script>

        // Menu Toggle Functionality
        function toggleMenu() {
            var sideMenu = document.getElementById('sideMenu');
            sideMenu.classList.toggle('show');
        }

        function openModal(roomID) {
          // Show the modal
          const modal = document.getElementById('cancel-modal');
          modal.style.display = 'flex';
      
          // Set the room ID in the hidden input
          const roomIdInput = document.getElementById('room-id-input');
          roomIdInput.value = roomID;
        }
      
        function closeModal() {
          // Hide the modal
          const modal = document.getElementById('cancel-modal');
          modal.style.display = 'none';
        }
      
        // Close the modal if the user clicks outside of it
        window.onclick = function(event) {
          const modal = document.getElementById('cancel-modal');
          if (event.target === modal) {
            closeModal();
          }
        };
     
    </script>
</body>
</html>
