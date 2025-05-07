<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    echo <<<HTML
    <style>
        #login-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        #login-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            max-width: 300px;
            box-shadow: 0 0 20px rgba(0,0,0,0.4);
        }
        #login-box button {
            padding: 10px 20px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }
        #login-box button:hover {
            background-color:rgb(40, 40, 40);
        }
    </style>

    <div id="login-overlay">
        <div id="login-box">
        <img src="icons/logo-login (1).png" alt="Lock Icon" style="width: 80px; height: 80px; margin-bottom: 20px;">
            <h3>You must be logged in to continue.</h3>
            <button onclick="window.location.href='Login.php'">Login</button>
        </div>
    </div>
HTML;
    exit();
}
?>
