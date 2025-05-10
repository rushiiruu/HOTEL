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
  
// Flag to track if reservation was successful
$reservation_success = false;

if (isset($_POST['reserve'])) {
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];

    // Validate that check-in is earlier than check-out
    if (strtotime($checkin) > strtotime($checkout)) {
        // We'll handle this through the JavaScript validation instead
        // No alert here
        $error_message = "Check-in date must be earlier than the check-out date.";
    } else {
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
            
            // Set the success flag
            $reservation_success = true;
        } else {
            $error_message = "No available rooms for the selected dates or room type. Please choose different dates or room type.";
        }
    }
}
?>

<?php include 'Check_Login.php'; ?>

<!DOCTYPE html>

<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reservation  </title>

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

<div id="input-modal" class="modal" style="display: none;">
  <div class="modal-content">
    <p>Please input necessary details</p>
    <button class="confirm-btn" onclick="closeIModal()">Okay</button>
  </div>
</div>

<div id="sidebar-summary" class="sidebar-summary" style="display: none;">
  <h3>Reservation Summary</h3>
  <div class="summary-content">
    <p><strong>First Name:</strong> <?php echo htmlspecialchars($firstName); ?></p>
    <p><strong>Last Name:</strong> <?php echo htmlspecialchars($lastName); ?></p>
    <p><strong>Check-in:</strong> <span id="summary-checkin"></span></p>
    <p><strong>Check-out:</strong> <span id="summary-checkout"></span></p>
    <p><strong>Room Type:</strong> <span id="summary-room"></span></p>
    <p><strong>Adults:</strong> <span id="summary-adults"></span></p>
    <p><strong>Children:</strong> <span id="summary-children"></span></p>
    <p><strong>Total:</strong> ₱<span id="summary-total"></span></p>
  </div>
  <form method="post" class="summary-actions">
    <input type="hidden" name="checkin" id="pay-checkin">
    <input type="hidden" name="checkout" id="pay-checkout">
    <input type="hidden" name="room-type" id="pay-room">
    <input type="hidden" name="adults" id="pay-adults">
    <input type="hidden" name="children" id="pay-children">
    <input type="hidden" name="total_price" id="pay-total">
    <button type="submit" name="reserve" class="pay-btn">Pay Now</button>
    <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
  </form>
</div>

<div id="reservation-popup" class="popup" style="display: none;">
  Reservation confirmed successfully!
</div>

<!-- Error message popup -->
<div id="error-popup" class="popup" style="display: none; background-color: #e74c3c;">
  <span id="error-message"></span>
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
    showErrorPopup("Check-in date must be earlier than the check-out date.");
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
const price = document.getElementById('calculated-price').innerText;
if (price === '₱0') {
button_disabled(); // this already disables and shows modal
}

else 
{
    document.getElementById('confirm-modal').style.display = 'block';
}
}

function button_disabled() {
const button = document.getElementById('reserve-btn');
document.getElementById('input-modal').style.display = 'block';
button.disabled = true; // disable the button// show the modal
}

function closeModal() {
document.getElementById('confirm-modal').style.display = 'none';
document.getElementById('sidebar-summary').style.display = 'none';
}

function closeIModal() {
document.getElementById('input-modal').style.display = 'none';
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

function showReservationPopup() {
const popup = document.getElementById('reservation-popup');
popup.style.display = 'block';

// Automatically hide the popup after 3 seconds
setTimeout(() => {
popup.classList.add('fade-out');
setTimeout(() => {
popup.style.display = 'none';
popup.classList.remove('fade-out');
}, 500); // Wait for the fade-out transition to complete
}, 3000); // Show for 3 seconds
}

function showErrorPopup(message) {
const popup = document.getElementById('error-popup');
document.getElementById('error-message').textContent = message;
popup.style.display = 'block';

// Automatically hide the popup after 3 seconds
setTimeout(() => {
popup.classList.add('fade-out');
setTimeout(() => {
popup.style.display = 'none';
popup.classList.remove('fade-out');
}, 500); // Wait for the fade-out transition to complete
}, 3000); // Show for 3 seconds
}

// Check if reservation was successful and show popup
<?php if ($reservation_success): ?>
document.addEventListener('DOMContentLoaded', function() {
    showReservationPopup();
});
<?php endif; ?>

// Check if there's an error message and show error popup
<?php if (isset($error_message)): ?>
document.addEventListener('DOMContentLoaded', function() {
    showErrorPopup(<?php echo json_encode($error_message); ?>);
});
<?php endif; ?>
</script>

</body>
</html>