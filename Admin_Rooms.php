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
    <title>Room Management - Luxe Hotels</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: linear-gradient(to right, #1a1a1a, #333);
            color: #fff;
            padding: 20px 0;
            margin-bottom: 30px;
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

        .room-management {
            display: flex;
            gap: 30px;
            margin-top: 20px;
        }

        .room-list {
            flex: 1;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-height: 600px;
            overflow-y: auto;
        }

        .room-list h3 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            color: #333;
            font-size: 18px;
        }

        .room-item {
            padding: 15px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #c8a97e;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .room-item:hover {
            background-color: #f1f1f1;
            transform: translateX(5px);
        }

        .room-item h4 {
            color: #333;
            margin-bottom: 5px;
        }

        .edit-form {
            flex: 2;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 25px;
        }

        .edit-form h2 {
            margin-bottom: 25px;
            padding-bottom: 15px;
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

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .form-group input:focus {
            border-color: #c8a97e;
            outline: none;
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
        }

        .btn:hover {
            background-color: #b89b6f;
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
    </style>
</head>
<body>
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
        
        <div class="room-management">
            <div class="room-list">
                <h3>Select a Room to Edit</h3>
                <?php foreach ($rooms as $room): ?>
                    <div class="room-item" onclick="populateFields('<?php echo addslashes($room['RoomName']); ?>', '<?php echo addslashes($room['RoomAccomodation']); ?>', '<?php echo addslashes($room['Beds']); ?>', '<?php echo addslashes($room['Utilities']); ?>', '<?php echo $room['RaSid']; ?>')">
                        <h4><?php echo htmlspecialchars($room['RoomName']); ?></h4>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="edit-form">
                <h2 id="roomName">Select a Room from the List</h2>
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
        </div>
    </div>

    <script>
        function populateFields(roomName, capacity, beds, utilities, roomID) {
            document.getElementById('roomName').innerHTML = roomName;
            document.getElementById('capacity').value = capacity;
            document.getElementById('beds').value = beds;
            document.getElementById('utilities').value = utilities;
            document.getElementById('roomID').value = roomID;
        }
    </script>
</body>
</html>