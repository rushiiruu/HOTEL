<?php 
/**
 * Purpose:
 *   - Provides an admin interface for managing hotel rooms and suites.
 *   - Allows administrators to view, edit, and update room details (capacity, beds, utilities).
 *   - Enables updating the availability status of individual rooms.
 *   - Integrates with the database to fetch and update real-time room data.
 */
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

  if(isset($_POST['UpdateAvail'])) {
      $roomID = $_POST['roomID'];
      $roomAvailability = $_POST['RoomAvail'];

      // Update the room availability in the database
      $stmt = $conn->prepare("UPDATE Rooms SET Avail = ? WHERE RoomID = ?");
      $stmt->bind_param("si", $roomAvailability, $roomID);

      if ($stmt->execute()) {
          $successMsg = "Room availability updated successfully.";
      } else {
          $errorMsg[] = "Error updating room availability: " . $stmt->error;
      }
      $stmt->close();
  }
  
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management - Luxe Hotels</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Quicksand:wght@300..700&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&family=Roboto:ital,wght@0,100..900;1,100..900&family=Satisfy&display=swap"
      rel="stylesheet"
    />
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            width: 95%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            
            color: #fff;
            padding: 20px 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #c8a97e;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info span {
            margin-right: 15px;
        }

        .user-info a {
            color: #fff;
            text-decoration: none;
            padding: 8px 15px;
            background-color: #c8a97e;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .user-info a:hover {
            background-color: #b89b6f;
        }

        h1 {
            margin-bottom: 20px;
            color: #343a40;
            font-size: 28px;
            border-bottom: 2px solid #c8a97e;
            padding-bottom: 10px;
            display: inline-block;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        .panel {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            height: fit-content;
        }

        .panel h3 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            color: #333;
            font-size: 18px;
        }

        .room-list {
            max-height: 500px;
            overflow-y: auto;
        }

        .room-item {
            padding: 15px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #c8a97e;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 4px;
        }

        .room-item:hover {
            background-color: #f1f1f1;
            transform: translateX(5px);
        }

        .room-item h4 {
            color: #333;
            margin-bottom: 5px;
        }

        .room-item h6 {
            color: #666;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .edit-form h2 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            color: #333;
            font-size: 22px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus, .form-group select:focus {
            border-color: #c8a97e;
            outline: none;
            box-shadow: 0 0 5px rgba(200, 169, 126, 0.3);
        }

        .btn {
            background-color: #c8a97e;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn:hover {
            background-color: #b89b6f;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color:rgb(0, 0, 0);
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
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

        .room-availability {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .room-availability select {
            flex-grow: 1;
            margin-right: 10px;
        }

        .room-availability button {
            flex-shrink: 0;
        }

        .room-details-list {
            max-height: 500px;
            overflow-y: auto;
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c8a97e;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #b89b6f;
        }

        footer {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 80px;
            }
    </style>
</head>
<body>
    <?php include 'Admin_Navbar.php'; ?>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">LA GINTA REAL</div>
                <div class="user-info">
                    <?php if ($username): ?>
                        <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
                        <a href="logout.php">Logout</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <h1>Room Management</h1>
        
        <?php if ($successMsg): ?>
            <div class="alert alert-success">
                <?php echo $successMsg; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errorMsg as $err): ?>
                        <li><?php echo $err; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="dashboard-grid">
            <!-- Room Selection Panel -->
            <div class="panel">
                <h3><i class="fas fa-door-open"></i> Room Selection</h3>
                <div class="room-list">
                    <?php foreach ($rooms as $room): ?>
                        <div class="room-item" onclick="populateFields('<?php echo addslashes($room['RoomName']); ?>', '<?php echo addslashes($room['RoomAccomodation']); ?>', '<?php echo addslashes($room['Beds']); ?>', '<?php echo addslashes($room['Utilities']); ?>', '<?php echo $room['RaSid']; ?>')">
                            <h4><?php echo htmlspecialchars($room['RoomName']); ?></h4>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Edit Form Panel -->
            <div class="panel edit-form">
                <h2 id="roomName">Select a Room to Edit</h2>
                <form action="" method="post">
                    <div class="form-group">
                        <label for="capacity"><i class="fas fa-users"></i> Capacity</label>
                        <input type="text" name="capacity" id="capacity" placeholder="Room capacity">            
                    </div>

                    <div class="form-group">
                        <label for="beds"><i class="fas fa-bed"></i> Beds</label>
                        <input type="text" name="beds" id="beds" placeholder="Bed configuration">                 
                    </div>

                    <div class="form-group">
                        <label for="utilities"><i class="fas fa-concierge-bell"></i> Utilities</label>
                        <input type="text" name="utilities" id="utilities" placeholder="Available utilities">            
                    </div>
                    
                    <div class="form-group">
                        <input type="hidden" name="roomID" id="roomID">
                        <button type="submit" name="Update" class="btn">Update Room Details</button>
                    </div>
                </form>
            </div>

            <!-- Room Details Panel -->
            <div class="panel room-details">
                <h3><i class="fas fa-list-alt"></i> Room Details</h3>
                <form action="" method="post" class="form-group">
                    <input type="hidden" name="roomlistID" id="roomlistID">
                    <button type="submit" name="query" class="btn btn-secondary">
                         View Room Availability
                    </button>
                </form>
                
                <div class="room-details-list">
                    <?php 
                        if (isset($_POST['query'])) {
                            $listID = isset($_POST['roomlistID']) ? $_POST['roomlistID'] : 0;

                            $stmt = $conn->prepare("SELECT * FROM rooms WHERE RaSid = ?");
                            $stmt->bind_param("i", $listID);
                            $stmt->execute();
                            $roomlist = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                            $stmt->close();

                            if ($roomlist) {
                                foreach ($roomlist as $room) {
                                    echo "<form action='' method='post'>";
                                    echo "<div class='room-item'>";
                                    echo "<h4>Room ID: {$room['RoomID']}</h4>";
                                    echo "<h6>Room Type: {$room['roomtype']}</h6>";
                                    echo "<div class='room-availability'>";
                                    echo "<input type='hidden' name='roomID' value='{$room['RoomID']}'>";
                                    echo "<select name='RoomAvail' id='RoomAvail'>";
                                    echo "<option value='Available'" . ($room['Avail'] === 'Available' ? " selected" : "") . ">Available</option>";
                                    echo "<option value='Not Available'" . ($room['Avail'] === 'Not Available' ? " selected" : "") . ">Not Available</option>";
                                    echo "</select>";
                                    echo "<button type='submit' name='UpdateAvail' class='btn'>Update</button>";
                                    echo "</div>";
                                    echo "</div>"; 
                                    echo "</form>";     
                                }
                            } else {
                                echo "<div class='alert alert-info'>No rooms found for this selection.</div>";
                            }
                        } else {
                            echo "<div class='alert alert-info'>Select a room type first to view its availability details.</div>";
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <footer>
   © 2025 La Ginta Real Philippines. All rights reserved.
    </footer>

    <script>
        function populateFields(roomName, capacity, beds, utilities, roomID) {
            document.getElementById('roomName').innerHTML = roomName;
            document.getElementById('capacity').value = capacity;
            document.getElementById('beds').value = beds;
            document.getElementById('utilities').value = utilities;
            document.getElementById('roomID').value = roomID;
            document.getElementById('roomlistID').value = roomID;
        }
    </script>
</body>
</html>