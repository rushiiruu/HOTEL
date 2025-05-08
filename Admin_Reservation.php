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

$errorMsg = [];
$successMsg = [];

// Handle updates
if (isset($_POST['update'])) {
    $reservationID = $_POST['ReservationID'];
    $newRoomID = $_POST['roomID']; // Fetch RoomID from hidden input
    $newCheckIn = $_POST['checkIn'];
    $newCheckOut = $_POST['checkOut'];

    // Validate Check-In and Check-Out dates
    if (strtotime($newCheckIn) > strtotime($newCheckOut)) {
        echo "<p>Error: Check-Out date must be after Check-In date.</p>";
        return;
    }

    // Check for conflicting reservations in MyReservation
    $conflictQuery = $conn->prepare("
        SELECT * FROM MyReservation 
        WHERE RoomID = ? 
        AND ReservationID != ? 
        AND (
            (CheckIn < ? AND CheckOut > ?) OR 
            (CheckIn < ? AND CheckOut > ?) OR
            (CheckIn >= ? AND CheckOut <= ?)
        )
    ");
    $conflictQuery->bind_param("iissssss", $newRoomID, $reservationID, $newCheckOut, $newCheckIn, $newCheckOut, $newCheckIn, $newCheckIn, $newCheckOut);
    $conflictQuery->execute();
    $conflictResult = $conflictQuery->get_result();

    if ($conflictResult->num_rows > 0) {
        echo "<p>Error: The selected dates conflict with an existing reservation for this room.</p>";
        $conflictQuery->close();
        return;
    }
    $conflictQuery->close();

    // Fetch the price per night
    $stmt = $conn->prepare("SELECT Price FROM Rooms WHERE RoomID = ?");
    $stmt->bind_param("i", $newRoomID);
    $stmt->execute();
    $stmt->bind_result($price);
    if ($stmt->fetch()) {
        $stmt->close();

        // Calculate total price
        $days = (strtotime($newCheckOut) - strtotime($newCheckIn)) / (60 * 60 * 24);
        if ($days < 1) {
            $days = 1; // Minimum of 1 day for same-day reservations
        }

        $totalPrice = $days * $price;

        // Update the reservation
        $stmt = $conn->prepare("UPDATE MyReservation SET CheckIn=?, CheckOut=?, TotalPrice=? WHERE ReservationID=?");
        $stmt->bind_param("ssii", $newCheckIn, $newCheckOut, $totalPrice, $reservationID);
        if ($stmt->execute()) {
            echo "<p>Reservation updated successfully!</p>";
        } else {
            echo "<p>Error: Failed to update reservation.</p>";
        }
        $stmt->close();
    } else {
        echo "<p>Error: Room not found or invalid RoomID.</p>";
        $stmt->close();
    }
}

// Handle cancellations
if (isset($_POST['cancel'])) {
    $reservationID = $_POST['ReservationID'];
    $stmt = $conn->prepare("DELETE FROM MyReservation WHERE ReservationID = ?");
    $stmt->bind_param("i", $reservationID);
    $stmt->execute();
    $stmt->close();

    echo "<p>Reservation cancelled.</p>";
}

// Search by Room ID
$roomSearchResults = [];
if (isset($_POST['searchRoom'])) {
    $roomID = $_POST['searchRoomID'];
    $stmt = $conn->prepare("SELECT RoomID, Price, roomtype FROM Rooms WHERE RoomID = ?");
    $stmt->bind_param("i", $roomID);
    $stmt->execute();
    $result = $stmt->get_result();
    $roomSearchResults = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$reservation = $conn->query("SELECT * FROM MyReservation");

if (!$reservation) {
    die("Query failed: " . $conn->error); // Debugging output
}

// Fetch results if the query is successful
$reservationData = $reservation->fetch_all(MYSQLI_ASSOC);
?>

<h2>Manage Reservations</h2>
<table border="1">
  <tr>
    <th>Reservation ID</th>
    <th>Guest</th>
    <th>Room ID</th>
    <th>Rooms And Suites ID</th>
    <th>Check-In</th>
    <th>Check-Out</th>
    <th>Total Price</th>
    <th colspan = '2'>Actions</th>
  </tr>
    <form method="post">
      <?php 
        if (empty($reservationData)) {
            echo "<tr><td colspan='8'>No reservations found.</td></tr>";
        } else {
            foreach ($reservationData as $row) {
                echo "<tr>";
                echo "<form method='post'>"; // Start a new form for each row
                echo "<td>" . htmlspecialchars($row['ReservationID']) . "</td>";
                echo "<td>" . htmlspecialchars($row['UserID']) . "</td>";
                echo "<td>" . htmlspecialchars($row['RoomID']) . "</td>"; // Display RoomID as plain text
                echo "<input type='hidden' name='roomID' value='" . htmlspecialchars($row['RoomID']) . "'>"; // Hidden input for RoomID
                echo "<td>" . htmlspecialchars($row['RaSid']) . "</td>";
                echo "<td><input type='date' name='checkIn' value='" . htmlspecialchars($row['CheckIn']) . "' required></td>";
                echo "<td><input type='date' name='checkOut' value='" . htmlspecialchars($row['CheckOut']) . "' required></td>";
                echo "<td>₱" . htmlspecialchars($row['TotalPrice']) . "</td>";
                echo "<td>
                        <input type='hidden' name='ReservationID' value='" . htmlspecialchars($row['ReservationID']) . "'>
                        <button type='submit' name='update'>Update</button>
                      </td>";
                echo "<td>
                        <input type='hidden' name='ReservationID' value='" . htmlspecialchars($row['ReservationID']) . "'>
                        <button type='submit' name='cancel'>Cancel</button>
                      </td>";
                echo "</form>"; // End the form for this row
                echo "</tr>";
            }
        }
      ?>
    </form>
 
</table>

<h2>Search Room by ID</h2>
<form method="post">
  <input type="number" name="searchRoomID" required>
  <button type="submit" name="searchRoom">Search</button>
</form>

<?php if (!empty($roomSearchResults)): ?>
<table border="1">
  <tr><th>Room ID</th><th>Room Type</th><th>Price</th></tr>
  <?php foreach ($roomSearchResults as $room): ?>
    <tr>
      <td><?= $room['RoomID'] ?></td>
      <td><?= $room['roomtype'] ?></td>
      <td>₱<?= $room['Price'] ?></td>
    </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
