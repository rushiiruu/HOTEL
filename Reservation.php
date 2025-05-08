<?php
session_start();

// Database connection
$servername = "localhost";
$dbuser = "root";
$password = "";
$dbname = "hotel_db";

$conn = new mysqli($servername, $dbuser, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

if ($username) {
    $stmt = $conn->prepare("SELECT Fname, Lname FROM UserAccount WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $userResult = $stmt->get_result();
    $userRow = $userResult->fetch_assoc();

    $firstName = $userRow['Fname'] ?? 'Guest';
    $lastName = $userRow['Lname'] ?? '';
    $stmt->close();
} else {
    $firstName = 'Guest';
    $lastName = '';
}

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

    // Validate that check-in is earlier than check-out
    if (strtotime($checkin) > strtotime($checkout)) {
        echo "<script>alert('Check-in date must be earlier than the check-out date.');</script>";
        return;
    }

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

    echo "<script>alert('Reservation confirmed!');</script>";
} else {
    echo "<script>alert('No available rooms for the selected dates or room type. Please choose different dates or room type.');</script>";
}
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
    <link rel="stylesheet" href="styles/Reservation.css" />
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
        <div class="form-row">
            <div class="form-column">
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
            </div>

            <div class="price-column">
                <div class="price-output">
                    <p>Total Price: <span id="calculated-price">₱0</span></p>
                </div>
            </div>
        </div>

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

        <div class="reservation-footer">
        <button type="button" class="reserve-btn" onclick="showConfirmationModal()">Confirm Reservation</button>
        
        </div>
    </div>
</form>

<div id="confirm-modal" class="modal" style="display: none;">
  <div class="modal-content">
    <p>Do you want to proceed to payment?</p>
    <button class="confirm-btn" onclick="showSidebarSummary()">Yes, Proceed</button>
    <button class="close-btn" onclick="closeModal()">Cancel</button>
  </div>
</div>

<div id="sidebar-summary" class="sidebar" style="display: none;">
  <h3>Reservation Summary</h3>
  <p><strong>First Name:</strong> <?php echo htmlspecialchars($firstName); ?></p>
  <p><strong>Last Name:</strong> <?php echo htmlspecialchars($lastName); ?></p>
  <p><strong>Check-in:</strong> <span id="summary-checkin"></span></p>
  <p><strong>Check-out:</strong> <span id="summary-checkout"></span></p>
  <p><strong>Room Type:</strong> <span id="summary-room"></span></p>
  <p><strong>Adults:</strong> <span id="summary-adults"></span></p>
  <p><strong>Children:</strong> <span id="summary-children"></span></p>
  <p><strong>Total:</strong> ₱<span id="summary-total"></span></p>
  <form method="post">
    <!-- Hidden fields to pass back the values -->
    <input type="hidden" name="checkin" id="pay-checkin">
    <input type="hidden" name="checkout" id="pay-checkout">
    <input type="hidden" name="room-type" id="pay-room">
    <input type="hidden" name="adults" id="pay-adults">
    <input type="hidden" name="children" id="pay-children">
    <input type="hidden" name="total_price" id="pay-total">
    <button type="submit" name="reserve" class="pay-btn">Pay</button>
    <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
  </form>
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

    // Ensure check-in is earlier than check-out
    if (checkin > checkout) {
        alert("Check-in date must be earlier than the check-out date.");
        document.getElementById('calculated-price').textContent = '₱0';
        return;
    }

    let roomPrice = 0;
    if (roomType === 'Standard Room') {
        roomPrice = <?php echo $standardPrice; ?>; // Standard Room price
    } else if (roomType === 'Deluxe Room') {
        roomPrice = <?php echo $deluxePrice; ?>; // Deluxe Room price
    }

    const timeDiff = checkout - checkin;
    let nights = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

    // Treat same-day check-in and check-out as 1 night
    if (nights === 0) {
        nights = 1;
    }

    const totalPrice = roomPrice * nights;
    document.getElementById('calculated-price').textContent = `₱${totalPrice}`;
    document.getElementById('total_price').value = totalPrice;
}

document.getElementById('room-type').addEventListener('change', calculatePrice);
document.getElementById('checkin').addEventListener('change', calculatePrice);
document.getElementById('checkout').addEventListener('change', calculatePrice);


function showConfirmationModal() {
    document.getElementById('confirm-modal').style.display = 'block';
}

function closeModal() {
    document.getElementById('confirm-modal').style.display = 'none';
}

function showSidebarSummary() {
    closeModal();

    // Collect data from form
    const checkin = document.getElementById('checkin').value;
    const checkout = document.getElementById('checkout').value;
    const roomType = document.getElementById('room-type').value;
    const adults = document.getElementById('adult-count').innerText;
    const children = document.getElementById('child-count').innerText;
    const total = document.getElementById('calculated-price').innerText.replace('₱', '');

    // Display in sidebar
    document.getElementById('summary-checkin').innerText = checkin;
    document.getElementById('summary-checkout').innerText = checkout;
    document.getElementById('summary-room').innerText = roomType;
    document.getElementById('summary-adults').innerText = adults;
    document.getElementById('summary-children').innerText = children;
    document.getElementById('summary-total').innerText = total;

    // Populate hidden form inputs
    document.getElementById('pay-checkin').value = checkin;
    document.getElementById('pay-checkout').value = checkout;
    document.getElementById('pay-room').value = roomType;
    document.getElementById('pay-adults').value = adults;
    document.getElementById('pay-children').value = children;
    document.getElementById('pay-total').value = total;

    // Show sidebar
    document.getElementById('sidebar-summary').style.display = 'block';
}

function closeModal() {
    document.getElementById('confirm-modal').style.display = 'none';
    document.getElementById('sidebar-summary').style.display = 'none';
}
    </script>
</body>
</html>