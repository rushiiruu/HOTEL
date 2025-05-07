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
    $reservationID = $_POST['reservationID'];
    $newRoomID = $_POST['roomID'];
    $newCheckIn = $_POST['checkIn'];
    $newCheckOut = $_POST['checkOut'];

    // Fetch the price per night
    $stmt = $conn->prepare("SELECT Price FROM Rooms WHERE RoomID = ?");
    $stmt->bind_param("i", $newRoomID);
    $stmt->execute();
    $stmt->bind_result($price);
    $stmt->fetch();
    $stmt->close();

    $days = (strtotime($newCheckOut) - strtotime($newCheckIn)) / (60 * 60 * 24);
    $totalPrice = $days * $price;

    $stmt = $conn->prepare("UPDATE MyReservation SET RoomID=?, CheckIn=?, CheckOut=?, TotalPrice=? WHERE ReservationID=?");
    $stmt->bind_param("issii", $newRoomID, $newCheckIn, $newCheckOut, $totalPrice, $reservationID);
    $stmt->execute();
    $stmt->close();

    echo "<p>Reservation updated successfully!</p>";
}

// Handle cancellations
if (isset($_POST['cancel'])) {
    $reservationID = $_POST['reservationID'];
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

// Fetch reservations
$reservations = $conn->query("SELECT r.ReservationID, u.Fname, u.Lname, r.RoomID, r.CheckIn, r.CheckOut, r.TotalPrice FROM MyReservation r JOIN UserAccount u ON r.UserID = u.UserID");
?>

<h2>Manage Reservations</h2>
<table border="1">
  <tr>
    <th>Reservation ID</th>
    <th>Guest</th>
    <th>Room ID</th>
    <th>Check-In</th>
    <th>Check-Out</th>
    <th>Total Price</th>
    <th>Actions</th>
  </tr>
  <?php while ($row = $reservations->fetch_assoc()): ?>
  <tr>
    <form method="post">
      <td><?= $row['ReservationID'] ?></td>
      <td><?= $row['Fname'] . ' ' . $row['Lname'] ?></td>
      <td><input type="number" name="roomID" value="<?= $row['RoomID'] ?>"></td>
      <td><input type="date" name="checkIn" value="<?= $row['CheckIn'] ?>"></td>
      <td><input type="date" name="checkOut" value="<?= $row['CheckOut'] ?>"></td>
      <td>₱<?= $row['TotalPrice'] ?></td>
      <td>
        <input type="hidden" name="reservationID" value="<?= $row['ReservationID'] ?>">
        <button type="submit" name="update">Update</button>
        <button type="submit" name="cancel">Cancel</button>
      </td>
    </form>
  </tr>
  <?php endwhile; ?>
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
