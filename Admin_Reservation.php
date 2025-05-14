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
if (isset($_POST['cancel'])) {
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
    <style>
        :root {
            --primary-color:rgb(0, 0, 0);
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --border-radius: 4px;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            max-width: 1500px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: var(--border-radius);
            margin-top: 150px;
        }
        
        h1, h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light-color);
        }
        
        .alert {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
            font-weight: bold;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: white;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: bold;
        }
        
        tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        input[type="date"], 
        input[type="number"],
        input[type="text"] {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 14px;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 15px;
            color: white;
            background-color: var(--secondary-color);
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .btn-update {
            background-color: var(--secondary-color);
        }
        
        .btn-cancel {
            background-color: var(--accent-color);
        }
        
        .btn-search {
            background-color: var(--primary-color);
        }
        
        .search-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .search-container input {
            flex: 1;
        }

        .no-results {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #777;
        }

        .section {
            margin-bottom: 40px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
            
            th, td {
                padding: 8px 10px;
            }
            
            .search-container {
                flex-direction: column;
            }
        }
    </style>
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
                            <button type="submit" name="cancel" class="btn btn-cancel" onclick="return confirm('Cancel this reservation?');">Cancel</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</div>
</body>
</html>
