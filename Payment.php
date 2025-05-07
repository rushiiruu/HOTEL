<?php
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Check if the user is logged in
if (!$username) {
    echo "<script>alert('Please log in first.'); window.location.href='Login.php';</script>";
    exit;
}

// Database connection
$servername = "localhost";
$dbuser = "root";
$password = "";
$dbname = "hotel_db";

$conn = new mysqli($servername, $dbuser, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user details
$stmt = $conn->prepare("SELECT UserID, Fname, Lname FROM UserAccount WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$userResult = $stmt->get_result();
$userRow = $userResult->fetch_assoc();

if (!$userRow) {
    echo "<script>alert('User not found. Please log in again.'); window.location.href='Login.php';</script>";
    exit;
}

$UserID = $userRow['UserID'];
$FirstName = $userRow['Fname'];
$LastName = $userRow['Lname'];
$stmt->close();

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    $roomID = intval($_POST['room_id']);
    $checkIn = $_POST['check_in'];
    $checkOut = $_POST['check_out'];
    $totalPrice = floatval($_POST['price']);
    $adults = intval($_POST['adults']);
    $children = intval($_POST['children']);

    // Validate input
    if (empty($roomID) || empty($checkIn) || empty($checkOut) || empty($totalPrice) || empty($adults)) {
        echo "<script>alert('Invalid input. Please try again.');</script>";
    } else {
        // Insert reservation into the database
        $stmt = $conn->prepare("
            INSERT INTO MyReservation (UserID, RoomID, RaSid, CheckIn, CheckOut, NoOfAdults, NoOfChildren, TotalPrice) 
            VALUES (?, ?, (SELECT RaSid FROM Rooms WHERE RoomID = ?), ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iiissiii", $UserID, $roomID, $roomID, $checkIn, $checkOut, $adults, $children, $totalPrice);

        if ($stmt->execute()) {
            echo "<script>alert('Payment successful and reservation saved!'); window.location.href='ManageReservation.php';</script>";
        } else {
            echo "<script>alert('Error saving reservation. Please try again.');</script>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="styles/Payment.css">
</head>
<body>
    <div class="payment-container">
        <h2>Payment Summary</h2>
        <table class="payment-table">
            <tr><th>First Name</th><td><?php echo htmlspecialchars($FirstName); ?></td></tr>
            <tr><th>Last Name</th><td><?php echo htmlspecialchars($LastName); ?></td></tr>
            <tr><th>Room ID</th><td><?php echo htmlspecialchars($_POST['room_id'] ?? ''); ?></td></tr>
            <tr><th>Check-In</th><td><?php echo htmlspecialchars($_POST['check_in'] ?? ''); ?></td></tr>
            <tr><th>Check-Out</th><td><?php echo htmlspecialchars($_POST['check_out'] ?? ''); ?></td></tr>
            <tr><th>Total Price</th><td>₱<?php echo htmlspecialchars($_POST['price'] ?? ''); ?></td></tr>
            <tr><th>Adults</th><td><?php echo htmlspecialchars($_POST['adults'] ?? ''); ?></td></tr>
            <tr><th>Children</th><td><?php echo htmlspecialchars($_POST['children'] ?? ''); ?></td></tr>
        </table>

        <form method="POST">
            <input type="hidden" name="pay" value="1">
            <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($_POST['room_id'] ?? ''); ?>">
            <input type="hidden" name="check_in" value="<?php echo htmlspecialchars($_POST['check_in'] ?? ''); ?>">
            <input type="hidden" name="check_out" value="<?php echo htmlspecialchars($_POST['check_out'] ?? ''); ?>">
            <input type="hidden" name="price" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
            <input type="hidden" name="adults" value="<?php echo htmlspecialchars($_POST['adults'] ?? ''); ?>">
            <input type="hidden" name="children" value="<?php echo htmlspecialchars($_POST['children'] ?? ''); ?>">
            <button type="submit" class="pay-btn">Pay Now</button>
        </form>
    </div>
</body>
</html>