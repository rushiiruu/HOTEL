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
    <title>Document</title>
    <script src="https://www.gstatic.com/charts/loader.js"></script>

    <style>
    .main-cont { /* Fixed typo */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        margin: 20px;
        max-width: 100%;
    }
    .sub-cont1 {
        display: flex;
        flex-direction: row;
        max-width: 100%;
        
    }

    .sub-cont2 {
        max-width: 100%;
    }

    #Rooms { /* Fixed case sensitivity */
        width: 700px;
        height: 400px;

    }
    #Users {
        width: 700px;
        height: 400px;
    }

    #Dates {
        width: 1600px;        
    }
    </style>
</head>
<body>

<div class = "main-cont">
     
    <div class= "sub-cont1">
        <div id="Rooms"></div>
       <div id="Users"></div> 
    </div>
    <div class ="sub-cont2">
        <div id="Dates"></div>  
    </div>
</div>





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
            chartArea: {width: '50%'},
            hAxis: {
                title: 'Number of Reservations',
                minValue: 0
            },
            vAxis: {
                title: 'Room Name'
            }
        };

        var chart = new google.visualization.BarChart(document.getElementById('Rooms'));

        chart.draw(data, options);
    }

    function ReservationPerUsers() {
        // PHP to dynamically generate the data array
        var data = google.visualization.arrayToDataTable([
            ['Room Name', 'Number of Reservations'],
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
            chartArea: {width: '50%'},
            hAxis: {
                title: 'Number of Reservations',
                minValue: 0
            },
            vAxis: {
                title: 'Room Name'
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
        chartArea: {width: '50%'},
        hAxis: {
            title: 'Check-In Date',
            slantedText: true, // Optional: Slant text for better readability
            slantedTextAngle: 45 // Optional: Adjust angle of slanted text
        },
        vAxis: {
            title: 'Number of Reservations',
            minValue: 0
        }
    };

    // Use ColumnChart instead of BarChart
    var chart = new google.visualization.ColumnChart(document.getElementById('Dates'));

    chart.draw(data, options);
    }



</script>

</body>
</html>