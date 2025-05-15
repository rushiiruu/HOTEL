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
    <title>My Reservations | Luxury Hotel</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="styles/ManageReservation.css">
   
  </head>
  <body>
    <?php include 'NavbarNoBack.php'; ?>
    
    <header class="page-header">
      <div class="container">
        <h1>My Reservations</h1>
        <p class="page-description">Manage your upcoming stays and booking details</p>
      </div>
    </header>
    
    <main>
      <section class="container">
        <div class="reservation-container">
          <div class="reservation-header">
            <h2>Upcoming Stays</h2>
            <a href="Rooms&Suites.php" class="btn btn-outline">Browse Rooms</a>
          </div>
          
          <?php 
            $result = $conn->query("SELECT * FROM MyReservation WHERE UserID = $UserID ORDER BY CheckIn");
            if ($result->num_rows > 0) {
              echo '<div class="reservation-cards">';
              while ($row = $result->fetch_assoc()) {
                $roomID = $row['RoomID'];
                $price = $row['TotalPrice'];
                $checkIn = date('F j, Y', strtotime($row['CheckIn']));
                $checkOut = date('F j, Y', strtotime($row['CheckOut']));
                
                // Calculate number of nights
                $datetime1 = new DateTime($row['CheckIn']);
                $datetime2 = new DateTime($row['CheckOut']);
                $interval = $datetime1->diff($datetime2);
                $nights = $interval->days;
                
                echo '
                <article class="reservation-card">
                  <header class="card-header">
                    <h3>Room Reservation</h3>
                    <div class="room-id">Room #' . htmlspecialchars($roomID) . '</div>
                  </header>
                  <div class="card-body">
                    <div class="reservation-detail">
                      <span class="detail-label">Check-in</span>
                      <span class="detail-value">' . htmlspecialchars($checkIn) . '</span>
                    </div>
                    <div class="reservation-detail">
                      <span class="detail-label">Check-out</span>
                      <span class="detail-value">' . htmlspecialchars($checkOut) . '</span>
                    </div>
                    <div class="reservation-detail">
                      <span class="detail-label">Duration</span>
                      <span class="detail-value">' . $nights . ' night' . ($nights > 1 ? 's' : '') . '</span>
                    </div>
                    <div class="reservation-detail">
                      <span class="detail-label">Total Price</span>
                      <span class="detail-value">$' . htmlspecialchars(number_format($price, 2)) . '</span>
                    </div>
                  </div>
                  <footer class="card-footer">
                    <button type="button" class="btn btn-danger" onclick="openModal(' . htmlspecialchars($roomID) . ')">
                      <i class="bi bi-x-circle"></i> Cancel Reservation
                    </button>
                  </footer>
                </article>';
              }
              echo '</div>';
            } else {
              echo '
              <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <h3>No Reservations Found</h3>
                <p>You don\'t have any upcoming reservations. Browse our selection of rooms and suites to book your perfect stay.</p>
                <a href="rooms.php" class="btn">Explore Rooms</a>
              </div>';
            }
          ?>
        </div>
      </section>
    </main>

    <!-- Cancel Reservation Modal -->
    <dialog id="cancel-modal" class="modal">
      <form method="POST" id="cancel-form" class="modal-content">
        <div class="modal-header">
          <h3><i class="bi bi-exclamation-triangle"></i> Cancel Reservation</h3>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to cancel this reservation? This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <input type="hidden" name="action" value="cancel">
          <input type="hidden" name="room_id" id="room-id-input">
          <button type="button" class="btn btn-outline" onclick="closeModal()">Keep Reservation</button>
          <button type="submit" class="btn btn-danger">Yes, Cancel</button>
        </div>
      </form>
    </dialog>


    <?php include 'Footer.php'; ?>
       
    <script>
      // Modal functionality
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
      
      // Close modal on escape key
      document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
          closeModal();
        }
      });
    </script>
  </body>
</html>