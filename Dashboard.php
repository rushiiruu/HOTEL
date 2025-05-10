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

  $roomNames = $conn->query("SELECT RoomName, RaSid FROM roomsandsuites")->fetch_all(MYSQLI_ASSOC);
  $userNames = $conn->query("SELECT username, UserID FROM useraccount Where usertype = 'Guest'")->fetch_all(MYSQLI_ASSOC);
  $resDates = $conn->query("SELECT Distinct CheckIn FROM MyReservation")->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Analytics</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Charts -->
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand {
            font-weight: bold;
            color: #fff;
        }
        .chart-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        .chart-title {
            color: #343a40;
            font-weight: bold;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .footer {
            background-color: #343a40;
            color: #fff;
            padding: 20px 0;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#">La Ginta Real</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="rooms.php"><i class="fas fa-bed"></i> Rooms</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reservation.php"><i class="fas fa-calendar-check"></i> Reservations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#"><i class="fas fa-chart-bar"></i> Analytics</a>
                </li>
                <?php if ($username): ?>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php"><i class="fas fa-user"></i> <?php echo $username; ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h2 class="text-center mb-4">Reservation Analytics Dashboard</h2>
            <p class="text-center text-muted">View comprehensive statistics on room reservations, user bookings, and popular dates.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="chart-container">
                <h4 class="chart-title"><i class="fas fa-bed me-2"></i>Reservations per Room</h4>
                <div id="Rooms" style="height: 400px;"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="chart-container">
                <h4 class="chart-title"><i class="fas fa-users me-2"></i>Reservations by Users</h4>
                <div id="Users" style="height: 400px;"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="chart-container">
                <h4 class="chart-title"><i class="fas fa-calendar me-2"></i>Reservations Per Check-In Date</h4>
                <div id="Dates" style="height: 400px;"></div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12 text-center">
            <a href="reservation.php" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Back to Reservations</a>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="container text-center">
        <p>&copy; <?php echo date('Y'); ?> Hotel Reservation System. All rights reserved.</p>
        <div class="social-icons">
            <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
            <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
        </div>
    </div>
</footer>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    google.charts.load('current', {packages: ['corechart', 'bar']});
    google.charts.setOnLoadCallback(ReservationPerRoom);
    google.charts.setOnLoadCallback(ReservationPerUsers);
    google.charts.setOnLoadCallback(ReservationPerDates);

    function ReservationPerRoom() {
        // PHP to dynamically generate the data array
        var data = google.visualization.arrayToDataTable([
            ['Room Name', 'Number of Reservations'],
            <?php 
                foreach ($roomNames as $room) {
                    $count = count($conn->query("SELECT * FROM MyReservation where RaSid = $room[RaSid]")->fetch_all(MYSQLI_ASSOC));
                    echo "['" . $room['RoomName'] . "', " . $count . "],";
                }
            ?>
        ]);

        var options = {
            title: 'Reservations per Room',
            chartArea: {width: '60%', height: '75%'},
            hAxis: {
                title: 'Number of Reservations',
                minValue: 0
            },
            vAxis: {
                title: 'Room Name'
            },
            colors: ['#007bff'],
            legend: { position: 'none' }
        };

        var chart = new google.visualization.BarChart(document.getElementById('Rooms'));
        chart.draw(data, options);
    }

    function ReservationPerUsers() {
        // PHP to dynamically generate the data array
        var data = google.visualization.arrayToDataTable([
            ['Username', 'Number of Reservations'],
            <?php 
                foreach ($userNames as $user) {
                    $count = count($conn->query("SELECT * FROM MyReservation where UserID = $user[UserID]")->fetch_all(MYSQLI_ASSOC));
                    echo "['" . $user['username'] . "', " . $count . "],";
                }
            ?>
        ]);

        var options = {
            title: 'Reservations by Users',
            chartArea: {width: '60%', height: '75%'},
            hAxis: {
                title: 'Number of Reservations',
                minValue: 0
            },
            vAxis: {
                title: 'Username'
            },
            colors: ['#28a745'],
            legend: { position: 'none' }
        };

        var chart = new google.visualization.BarChart(document.getElementById('Users'));
        chart.draw(data, options);
    }

    function ReservationPerDates() {
        var data = google.visualization.arrayToDataTable([
            ['Check-In Date', 'Number of Reservations'],
            <?php 
                foreach ($resDates as $date) {
                    $checkInDate = $date['CheckIn'];
                    $result = $conn->query("SELECT COUNT(*) AS count FROM MyReservation WHERE CheckIn = '$date[CheckIn]'");
                    $count = $result->fetch_assoc()['count'];
                    echo "['" . $checkInDate . "', " . $count . "],";
                }
            ?>
        ]);

        var options = {
            title: 'Reservations Per Check-In Date',
            chartArea: {width: '80%', height: '70%'},
            hAxis: {
                title: 'Check-In Date',
                slantedText: true,
                slantedTextAngle: 45
            },
            vAxis: {
                title: 'Number of Reservations',
                minValue: 0
            },
            colors: ['#fd7e14'],
            legend: { position: 'none' }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('Dates'));
        chart.draw(data, options);
    }
</script>

</body>
</html>