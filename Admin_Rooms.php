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

if (isset($_POST['Update'])) {
    $roomID = $_POST['roomID'];
    $capacity = $_POST['capacity'];
    $beds = $_POST['beds'];
    $utilities = $_POST['utilities'];

    // Update the room details in the database
    $stmt = $conn->prepare("UPDATE RoomsandSuites SET RoomAccomodation = ?, Beds = ?, Utilities = ? WHERE RaSid = ?");
    $stmt->bind_param("sssi", $capacity, $beds, $utilities, $roomID);

    if ($stmt->execute()) {
        $successMsg = "Room details updated successfully.";
    } else {
        $errorMsg[] = "Error updating room details: " . $stmt->error;
    }
    $stmt->close();
}


  $rooms = $conn->query("SELECT * FROM RoomsandSuites")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
    foreach ($rooms as $room) {
        echo "<div onclick=\"populateFields('" . addslashes($room['RoomName']) . "', '" . addslashes($room['RoomAccomodation']) . "', '" . addslashes($room['Beds']) . "', '" . addslashes($room['Utilities']) . "', '" . $room['RaSid'] . "')\">";
        echo "<h2>" . htmlspecialchars($room['RoomName']) . "</h2>";
        echo "</div>";
    }
?>
<div>
    <form action="" method="post">
        <h2 id="roomName">Room Name</h2>
        <div>
            <label for="capacity">Capacity</label>
            <input type="text" name="capacity" id="capacity">            
        </div>

        <div>
            <label for="beds">Beds</label>
            <input type="text" name="beds" id="beds">                 
        </div>

        <div>
            <label for="utilities">Utilities</label>
            <input type="text" name="utilities" id="utilities">            
        </div>
        <div>
            <input type="hidden" name="roomID" id="roomID">
            <button type="submit" name="Update">Update</button>
        </div>
    </form>
</div>

<script>
    function populateFields(roomName, capacity, beds, utilities, roomID) {
        document.getElementById('roomName').innerHTML = roomName;
        document.getElementById('capacity').value = capacity;
        document.getElementById('beds').value = beds;
        document.getElementById('utilities').value = utilities;
        document.getElementById('roomID').value = roomID; // Dynamically set the roomID
    }
</script>
</body>
</html>