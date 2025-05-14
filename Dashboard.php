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
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Quicksand:wght@300..700&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&family=Roboto:ital,wght@0,100..900;1,100..900&family=Satisfy&display=swap"
      rel="stylesheet"
    />
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        
        .chart-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
            height: 100%;
            transition: transform 0.3s ease;
        }
        .chart-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
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
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0069d9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .footer {
            background-color: #343a40;
            color: #fff;
            padding: 20px 0;
            margin-top: 30px;
        }
        .dashboard-header {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
            margin-top: 150px;
        }
        .dashboard-title {
            color: #343a40;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .social-icons a {
            transition: transform 0.3s ease;
            display: inline-block;
        }
        .social-icons a:hover {
            transform: translateY(-3px);
        }
        .nav-link {
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: #17a2b8 !important;
        }
    </style>
    
</head>
<body>
<?php include 'Admin_Navbar.php'; ?>

<!-- Main Content -->
<div class="container py-5">
    <div class="dashboard-header">
        <h2 class="dashboard-title">Reservation Analytics Dashboard</h2>
        <p class="text-muted">View comprehensive statistics on room reservations, user bookings, and popular dates.</p>
    </div>

    <div class="row">
        <!-- First row with two charts side by side -->
        <div class="col-md-6 mb-4">
            <div class="chart-container">
                <h4 class="chart-title"><i class="fas fa-bed me-2"></i>Reservations per Room</h4>
                <div id="Rooms" style="height: 350px;"></div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="chart-container">
                <h4 class="chart-title"><i class="fas fa-users me-2"></i>Reservations by Users</h4>
                <div id="Users" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Second row with one chart taking full width -->
        <div class="col-md-12 mb-6">
            <div class="chart-container">
                <h4 class="chart-title"><i class="fas fa-calendar me-2"></i>Reservations Per Check-In Date</h4>
                <div id="Dates" style="height: 650px;"></div>
            </div>
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

    // Reset chart size on window resize for responsiveness
    window.addEventListener('resize', function() {
        ReservationPerRoom();
        ReservationPerUsers();
        ReservationPerDates();
    });

    function ReservationPerRoom() {
        // PHP to dynamically generate the data array
        var data = google.visualization.arrayToDataTable([
            ['Room Name', 'Number of Reservations'],
            <?php 
                if (!empty($roomNames)) {
                    foreach ($roomNames as $room) {
                        $count = count($conn->query("SELECT * FROM MyReservation where RaSid = $room[RaSid]")->fetch_all(MYSQLI_ASSOC));
                        echo "['" . $room['RoomName'] . "', " . $count . "],";
                    }
                } else {
                    echo "['No Data', 0],";
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
            colors: ['#4e73df'],
            legend: { position: 'none' },
            animation: {
                startup: true,
                duration: 1000,
                easing: 'out'
            }
        };

        var chart = new google.visualization.BarChart(document.getElementById('Rooms'));
        chart.draw(data, options);
    }

    function ReservationPerUsers() {
        // PHP to dynamically generate the data array
        var data = google.visualization.arrayToDataTable([
            ['Username', 'Number of Reservations'],
            <?php 
                if (!empty($userNames)) {
                    foreach ($userNames as $user) {
                        $count = count($conn->query("SELECT * FROM MyReservation where UserID = $user[UserID]")->fetch_all(MYSQLI_ASSOC));
                        echo "['" . $user['username'] . "', " . $count . "],";
                    }
                } else {
                    echo "['No Data', 0],";
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
            colors: ['#1cc88a'],
            legend: { position: 'none' },
            animation: {
                startup: true,
                duration: 1000,
                easing: 'out'
            }
        };

        var chart = new google.visualization.BarChart(document.getElementById('Users'));
        chart.draw(data, options);
    }

    function ReservationPerDates() {
      
    var data = google.visualization.arrayToDataTable([
        ['Check-In Date', 'Number of Reservations'],
        <?php 
            if (!empty($resDates)) {
                foreach ($resDates as $date) {
                    $checkInDate = $date['CheckIn'];
                    $result = $conn->query("SELECT COUNT(*) AS count FROM MyReservation WHERE CheckIn = '$date[CheckIn]'");
                    $count = $result->fetch_assoc()['count'];
                    echo "['" . $checkInDate . "', " . $count . "],";
                }
            } else {
                echo "['No Data', 0],";
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
            colors: ['#f6c23e'],
            legend: { position: 'none' },
            animation: {
                startup: true,
                duration: 1000,
                easing: 'out'
            }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('Dates'));
        chart.draw(data, options);
    }
</script>

</body>
</html>