<?php
/**
 * Purpose:
 *   - Provides an admin interface for managing all hotel reservations.
 *   - Allows administrators to view, search, update, and cancel reservations.
 *   - Supports searching reservations by ID and viewing all reservations.
 *   - Enables updating reservation dates and recalculating total price.
 *   - Checks for date conflicts and prevents overlapping bookings.
 *   - Integrates with the database to fetch and update real-time reservation data.
 *   - Displays success and error messages for admin actions.
 */
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
    $newRoomID = $_POST['roomID'];
    $newCheckIn = $_POST['checkIn'];
    $newCheckOut = $_POST['checkOut'];

    if (strtotime($newCheckIn) > strtotime($newCheckOut)) {
        $errorMsg[] = "Check-Out date must be after Check-In date.";
    } else {
        $conflictQuery = $conn->prepare("
            SELECT * FROM MyReservation 
            WHERE RoomID = ? AND ReservationID != ? AND (
                (CheckIn < ? AND CheckOut > ?) OR 
                (CheckIn < ? AND CheckOut > ?) OR
                (CheckIn >= ? AND CheckOut <= ?)
            )
        ");
        $conflictQuery->bind_param("iissssss", $newRoomID, $reservationID, $newCheckOut, $newCheckIn, $newCheckOut, $newCheckIn, $newCheckIn, $newCheckOut);
        $conflictQuery->execute();
        $conflictResult = $conflictQuery->get_result();

        if ($conflictResult->num_rows > 0) {
            $errorMsg[] = "The selected dates conflict with an existing reservation for this room.";
        } else {
            $stmt = $conn->prepare("SELECT Price FROM Rooms WHERE RoomID = ?");
            $stmt->bind_param("i", $newRoomID);
            $stmt->execute();
            $stmt->bind_result($price);
            if ($stmt->fetch()) {
                $stmt->close();
                $days = (strtotime($newCheckOut) - strtotime($newCheckIn)) / (60 * 60 * 24);
                if ($days < 1) $days = 1;
                $totalPrice = $days * $price;

                $stmt = $conn->prepare("UPDATE MyReservation SET CheckIn=?, CheckOut=?, TotalPrice=? WHERE ReservationID=?");
                $stmt->bind_param("ssii", $newCheckIn, $newCheckOut, $totalPrice, $reservationID);
                if ($stmt->execute()) {
                    $successMsg[] = "Reservation #$reservationID updated successfully!";
                } else {
                    $errorMsg[] = "Failed to update reservation: " . $conn->error;
                }
                $stmt->close();
            } else {
                $errorMsg[] = "Room not found or invalid RoomID.";
                $stmt->close();
            }
        }
        $conflictQuery->close();
    }
}

if (isset($_POST['showAllReservations'])) {
  $searchReservationID = null; // Reset the search ID
  $result = $conn->query("SELECT m.*, r.roomtype FROM MyReservation m LEFT JOIN Rooms r ON m.RoomID = r.RoomID ORDER BY m.CheckIn");
  $reservationData = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// Handle cancellations
if (isset($_POST['confirmcancel'])) {
    $reservationID = $_POST['ReservationID'];
    $stmt = $conn->prepare("DELETE FROM MyReservation WHERE ReservationID = ?");
    $stmt->bind_param("i", $reservationID);
    if ($stmt->execute()) {
        $successMsg[] = "Reservation #$reservationID has been cancelled.";
    } else {
        $errorMsg[] = "Failed to cancel reservation: " . $conn->error;
    }
    $stmt->close();
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
    if (empty($roomSearchResults)) {
        $errorMsg[] = "No room found with ID: $roomID";
    }
    $stmt->close();
}

// Search Reservation by ID
$searchReservationID = null;
if (isset($_POST['searchReservation'])) {
    $searchReservationID = $_POST['searchReservationID'];
    $stmt = $conn->prepare("SELECT m.*, r.roomtype FROM MyReservation m LEFT JOIN Rooms r ON m.RoomID = r.RoomID WHERE m.ReservationID = ?");
    $stmt->bind_param("i", $searchReservationID);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservationData = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $result = $conn->query("SELECT m.*, r.roomtype FROM MyReservation m LEFT JOIN Rooms r ON m.RoomID = r.RoomID ORDER BY m.CheckIn");
    $reservationData = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Reservations</title>
    <link rel="stylesheet" href="styles/A_Reservation.css">
</head>
<body>
<?php include 'Admin_Navbar.php'; ?>
  
<div class="container">
    <h1>Manage Reservations</h1>

    <?php foreach ($errorMsg as $msg): ?>
        <div class="alert alert-danger"><?= $msg ?></div>
    <?php endforeach; ?>
    <?php foreach ($successMsg as $msg): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endforeach; ?>

    <div class="section">
        
    </div>

    <div class="section">
        <h2>Search Reservation by ID</h2>
        <form method="post" class="search-container">
            <input type="number" name="searchReservationID" placeholder="Enter Reservation ID" required>
            <button type="submit" name="searchReservation" class="btn btn-search">Search</button>
        </form>
    </div>

    <div class="section">
    <h2>Current Reservations<?= $searchReservationID ? " - Search Result for ID $searchReservationID" : "" ?></h2>
    
    <?php if ($searchReservationID): ?>
        <!-- Show All Reservations Button -->
        <form method="post" class="show-all-container">
            <button type="submit" name="showAllReservations" class="btn btn-search">Show All Reservations</button>
        </form>
    <?php endif; ?>

    <?php if (empty($reservationData)): ?>
        <div class="no-results">No reservations found.</div>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Guest</th>
                <th>Room</th>
                <th>Type</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Total Price</th>
                <th colspan="2">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($reservationData as $row): ?>
                <tr>
                    <form method="post">
                        <td><?= htmlspecialchars($row['ReservationID']) ?></td>
                        <td><?= htmlspecialchars($row['UserID']) ?></td>
                        <td><?= htmlspecialchars($row['RoomID']) ?></td>
                        <td><?= htmlspecialchars($row['roomtype'] ?? 'N/A') ?></td>
                        <td><input type="date" name="checkIn" value="<?= htmlspecialchars($row['CheckIn']) ?>" required></td>
                        <td><input type="date" name="checkOut" value="<?= htmlspecialchars($row['CheckOut']) ?>" required></td>
                        <td>₱<?= number_format($row['TotalPrice'], 2) ?></td>
                        <td>
                            <input type="hidden" name="ReservationID" value="<?= $row['ReservationID'] ?>">
                            <input type="hidden" name="roomID" value="<?= $row['RoomID'] ?>">
                            <button type="submit" name="update" class="btn btn-update">Update</button>
                        </td>
                        <td>
                            <input type="hidden" name="ReservationID" value="<?= $row['ReservationID'] ?>">
                            <button type="button" name="cancel" class="btn btn-cancel" onclick="openModal(<?= $row['ReservationID'] ?>)">Cancel</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>


</div>
</div>

<dialog id="cancel-modal" class="modal">
    <form method="POST" id="cancel-form" class="modal-content">
    <div class="modal-header">
        <h3><i class="bi bi-exclamation-triangle"></i> Cancel Reservation</h3>
    </div>
    <div class="modal-body">
        <p>Are you sure you want to cancel this reservation? This action cannot be undone.</p>
    </div>
    <div class="modal-footer">
        <input type="hidden" name="ReservationID" id="reservation-id-input">
        <input type="hidden" name="action" value="cancel">
        <button type="button" class="btn btn-outline" onclick="closeModal()">Keep Reservation</button>
        <button type="submit" name="confirmcancel" class="btn btn-cancel">Yes, Cancel</button>
    </div>
    </form>
</dialog>

<footer>
   © 2025 La Ginta Real Philippines. All rights reserved.
</footer>

<script>
        function openModal(reservationID) {
        // Show the modal
        const modal = document.getElementById('cancel-modal');
        modal.style.display = 'flex';

        // Set the reservation ID in the hidden input
        const reservationIdInput = document.getElementById('reservation-id-input');
        reservationIdInput.value = reservationID;
    }

    function closeModal() {
        // Hide the modal
        const modal = document.getElementById('cancel-modal');
        modal.style.display = 'none';
    }

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
