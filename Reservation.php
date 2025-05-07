
<?php
  session_start();
  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

  
  $servername = "localhost";
  $dbuser = "root";
  $password = "";
  $dbname = "hotel_db";
  $errorMsg = [];
  $successMsg = "";
  $roomForReserve = "";

  // Connect to MySQL server
  $conn = new mysqli($servername, $dbuser, $password, $dbname);
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
  $room_id = intval($_SESSION['room_id']);

  $rooms = $conn->query("Select * from MyReservation where RaSid = $room_id")->fetch_all(MYSQLI_ASSOC);
  $avail = $conn->query("Select * from Rooms where RaSid = $room_id and Avail = 'Available'")->fetch_all(MYSQLI_ASSOC);
  $stmt = $conn->prepare("SELECT UserID FROM UserAccount WHERE Username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $userResult = $stmt->get_result();
  $userRow = $userResult->fetch_assoc();
  $UserID = $userRow['UserID'] ?? null;
  $stmt->close();

  $standardPrice = $conn->query("SELECT Price FROM Rooms WHERE RaSid = $room_id AND RoomType = 'Standard Room' LIMIT 1")->fetch_assoc()['Price'];
  $deluxePrice = $conn->query("SELECT Price FROM Rooms WHERE RaSid = $room_id AND RoomType = 'Deluxe Room' LIMIT 1")->fetch_assoc()['Price'];
  
if (isset($_POST['reserve'])) {
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $adults = $_POST['adults'];
    $children = $_POST['children'];
    $room_type = $_POST['room-type'];
    $totalPrice = $_POST['total_price'];
    $roomForReserve = 0;

    // Check for conflicting reservations and room availability
    $conflictQuery = $conn->prepare("
        SELECT * FROM MyReservation 
        WHERE RoomID = ? 
        AND (
            (CheckIn < ? AND CheckOut > ?) OR 
            (CheckIn < ? AND CheckOut > ?) OR
            (CheckIn >= ? AND CheckOut <= ?)
        )
    ");


    foreach ($avail as $row) {
    $roomID = $row['RoomID'];
    $RaSid = $row['RaSid'];

    // Ensure the RaSid matches the $room_id
    if ($RaSid != $room_id) {
        continue; // Skip this room if RaSid does not match
    }

    // Check if the room type matches
    $roomTypeQuery = $conn->prepare("SELECT roomtype FROM Rooms WHERE RoomID = ?");
    $roomTypeQuery->bind_param("i", $roomID);
    $roomTypeQuery->execute();
    $roomTypeResult = $roomTypeQuery->get_result();
    $roomTypeRow = $roomTypeResult->fetch_assoc();

    if (!$roomTypeRow) {
        error_log("Room type query returned no results for RoomID: $roomID");
        continue; // Skip if no room type is found
    }

    if (trim(strtolower($roomTypeRow['roomtype'])) !== trim(strtolower($room_type))) {
        error_log("Room type mismatch: DB value = " . $roomTypeRow['roomtype'] . ", Form value = $room_type");
        continue; // Skip this room if the room type does not match
    }

    // Check if the room is available
    $availabilityQuery = $conn->prepare("SELECT Avail FROM Rooms WHERE RoomID = ?");
    $availabilityQuery->bind_param("i", $roomID);
    $availabilityQuery->execute();
    $availabilityResult = $availabilityQuery->get_result();
    $availabilityRow = $availabilityResult->fetch_assoc();

    if (!$availabilityRow) {
        error_log("Availability query returned no results for RoomID: $roomID");
        continue; // Skip if no availability data is found
    }

    if (strtolower($availabilityRow['Avail']) !== 'available') {
        error_log("Room not available: RoomID = $roomID, Avail = " . $availabilityRow['Avail']);
        continue; // Room is not available, skip this room
    }

    // Check for conflicting reservations
    $conflictQuery->bind_param("issssss", $roomID, $checkout, $checkin, $checkout, $checkin, $checkin, $checkout);
    $conflictQuery->execute();
    $conflictResult = $conflictQuery->get_result();

    if ($conflictResult->num_rows > 0) {
        error_log("Conflict found for RoomID: $roomID");
        continue; // Conflict found, skip this room
    }

    // No conflict and room is available, reserve this room
    $roomForReserve = $roomID;
    break;
}

$conflictQuery->close();

if ($roomForReserve != 0) {
    // Insert the reservation
    $stmt = $conn->prepare("
        INSERT INTO MyReservation (UserID, RoomID, RaSid, CheckIn, CheckOut, NoOFAdults, NoOFChildren, TotalPrice) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiissiii", $UserID, $roomForReserve, $room_id, $checkin, $checkout, $adults, $children, $totalPrice);
    $stmt->execute();
    $stmt->close();

    
}
}

if (isset($_POST['reserve'])) {
  // Your reservation logic here...

  // If reservation is successful
  header("Location: Reservation.php?success=1");
  exit();
}
?>

<?php include 'Check_Login.php'; ?>


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
    <link rel="stylesheet" href="styles/Reservation.css">
  </head>
  <body>
    <script src="Home.js"></script>
    <?php include 'Navbar.php'; ?>

    <img
      src="https://www.ft.com/__origami/service/image/v2/images/raw/https%3A%2F%2Fd1e00ek4ebabms.cloudfront.net%2Fproduction%2F54089c95-8f88-4f62-a702-b77d2cc3a6c4.jpg?source=next-article&fit=scale-down&quality=highest&width=700&dpr=1"
      class="main-image"
      alt="bg"
    />
    <span class =span-menu>
      <a href="Home.php">LA GINTA REAL</a>
      <a href="Rooms&Suites.php">ROOMS & SUITES</a>
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

<form action="" method="post">
    <div class="room-form">
        <label for="checkin">Check-in Date:</label>
        <input type="date" id="checkin" name="checkin" required>

        <label for="checkout">Check-out Date:</label>
        <input type="date" id="checkout" name="checkout" required>

        <label for="room-type">Room Type:</label>
        <select id="room-type" name="room-type" required>
            <option value="" disabled selected>Select a room type</option>
            <option value="Standard Room">Standard Room</option>
            <option value="Deluxe Room">Deluxe Room</option>
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
        <input type="hidden" name="total_price" id="total_price">
         <div class="price-output">
    <p>Total Price: <span id="calculated-price">₱0</span></p>
  </div>
        <div class="reservation-footer">
        <button type="button" class="reserve-btn" onclick="openConfirmReservationModal()">Confirm Reservation</button>

 
</div>

</form>



<?php if (isset($_GET['success'])): ?>
<div id="popup-notification" class="popup">
  Reservation successfully made.
</div>
<script>
  setTimeout(() => {
    document.getElementById('popup-notification').style.opacity = '0';
  }, 3000);
</script>
<?php endif; ?>

    <!-- First Popup: Confirm Reservation -->
    <div id="confirm-reservation-modal" class="modal">
  <div class="modal-content">
    <p>Do you want to proceed with the reservation?</p>
    <button class="confirm-btn" onclick="openPaymentModal()">Yes, Proceed</button>
    <button class="close-btn" onclick="closeModal('confirm-reservation-modal')">Cancel</button>
  </div>
</div>
<!-- Second Popup: Payment -->
<div id="payment-modal" class="slide-modal">
  <div class="slide-modal-content">
    <!-- Close Button -->
    <span class="close-icon" onclick="closeModal('payment-modal')">&times;</span>
    <p>Please confirm your payment to reserve the room.</p>
    <button class="confirm-btn" onclick="showSuccessPopup()">Pay</button>
    <button class="close-btn" onclick="closeModal('payment-modal')">Cancel</button>
  </div>
</div>

<!-- Third Popup: Successfully Reserved -->
<div id="success-popup" class="popup">
  Reservation successfully made!
</div>

    <script>
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

          if(roomType === 'Standard Room') {
            roomPrice = <?php echo $standardPrice; ?> // Standard Room price
          } else if(roomType === 'Deluxe Room') {
            roomPrice = <?php echo $deluxePrice; ?> // Deluxe Room price
          }
          const timeDiff = checkout - checkin;
      let nights = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

      // Handle invalid date range (checkout before checkin)
      if (timeDiff < 0) {
        document.getElementById('calculated-price').textContent = '₱0';
        return;
      }

      // Treat same-day check-in and check-out as 1 night
      if (nights === 0) {
        nights = 1;
      }


          const totalPrice = roomPrice * nights;
          document.getElementById('calculated-price').textContent = `₱${totalPrice}`;
          document.getElementById('total_price').value = totalPrice

        }

      document.getElementById('room-type').addEventListener('change', calculatePrice);
      document.getElementById('checkin').addEventListener('change', calculatePrice);
      document.getElementById('checkout').addEventListener('change', calculatePrice);

      function openConfirmReservationModal() {
          const modal = document.getElementById('confirm-reservation-modal');
          modal.style.display = 'flex';
        }

       
        function showSuccessPopup() {
          closeModal('payment-modal'); // Close the payment modal
          const successPopup = document.getElementById('success-popup');
          successPopup.style.display = 'block';

          // Automatically fade out the success popup after 3 seconds
          setTimeout(() => {
            successPopup.classList.add('fade-out');
            setTimeout(() => successPopup.style.display = 'none', 500); // Remove after fade-out
          }, 3000);
        }

      

        // Close modal if user clicks outside of it
        window.onclick = function(event) {
        const confirmModal = document.getElementById('confirm-reservation-modal');
        const paymentModal = document.getElementById('payment-modal');
        if (event.target === confirmModal) {
          closeModal('confirm-reservation-modal');
        }
        if (event.target === paymentModal) {
          closeModal('payment-modal');
        }
      };

      // Prevent clicks inside the modal content from closing the modal
      document.querySelectorAll('.modal-content, .slide-modal-content').forEach(modalContent => {
        modalContent.onclick = function(event) {
          event.stopPropagation();
        };
      });

        function openPaymentModal() {
        const modal = document.getElementById('payment-modal');
        modal.style.display = 'block'; // Ensure it's visible
        modal.classList.add('show'); // Add the slide-in effect
      }

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modalId === 'payment-modal') {
    modal.classList.remove('show'); // Slide out
    setTimeout(() => {
      modal.style.display = 'none'; // Hide after sliding out
    }, 500); // Match the transition duration
  } else {
    modal.style.display = 'none';
  }
}

    </script>


</body>
</html>
