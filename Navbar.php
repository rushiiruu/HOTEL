<?php
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
?>
<!-- Navbar and Side Menu -->
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
/>

<style>
  /* === Navbar & Side Menu CSS (Extracted) === */
  nav {
    position: fixed;
    top: 0;
    left: 0;
    height: 90px;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0;
    z-index: 10;
    transition: top 0.3s ease, background-color 0.3s ease, padding 0.3s ease;
  }

  nav img {
    width: 40px;
    height: 40px;
  }

  nav a {
    color: white;
    text-decoration: none;
    margin: 0 15px;
    font-size: 12px;
    font-weight: bold;
  }

  nav button {
    padding: 5px 20px;
    background-color: white;
    color: black;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    font-size: 12px;
    margin-left: 30px;
    margin-right: 15px;
  }

  .hotel-name {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    font-size: 24px;
    font-weight: bold;
    color: white;
    font-family: "Cormorant Garamond", serif;
    text-align: center;
  }

  .hotel-location {
    display: block;
    font-size: 14px;
    font-weight: normal;
    margin-top: 0px;
    font-family: "Cormorant Garamond", serif;
    text-align: center;
  }

  .nav-right {
    display: flex;
    align-items: center;
  }

  .nav-right a {
    margin-right: 20px;
    font-family: "Cormorant Garamond", serif;
    font-size: 16px;
    text-decoration: none;
    color: white;
  }

  .menu-icon i {
    font-size: 2rem;
  }

  nav.scrolled {
    background-color: black;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
  }

  .side-menu {
    position: fixed;
    top: 0;
    left: -290px;
    width: 250px;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.95);
    color: white;
    padding-top: 80px;
    display: flex;
    flex-direction: column;
    align-items: start;
    padding-left: 30px;
    transition: left 0.4s ease;
    z-index: 100;
  }

  .side-menu.show {
    left: 0;
  }

  .side-menu a {
    color: white;
    text-decoration: none;
    font-size: 18px;
    margin-bottom: 20px;
    font-weight: bold;
  }

  .close-menu {
    position: absolute;
    top: 20px;
    right: 20px;
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
  }

  .close-menu i {
    font-size: 30px;
  }

  #sideMenu ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
  }

  #sideMenu ul li {
    margin-bottom: 10px;
  }

  #sideMenu ul li a {
    text-decoration: none;
    display: block;
    padding: 10px 0;
    border-bottom: 1px solid transparent;
    transition: border-color 0.3s ease;
  }

  #sideMenu ul li a:hover {
    border-bottom: 1px solid white;
    padding-left: 20px;
  }
</style>

<nav id="navbar">
  <a href="#" class="menu-icon" onclick="toggleMenu()">
    <i class="bi bi-list" id="menu"></i>
  </a>

  <a href="#" class="hotel-name hidden-on-load">
    LA GINTA REAL
    <span class="hotel-location">PHILIPPINES</span>
  </a>

  <div class="nav-right">
    <a href="#">MY RESERVATION</a>
    <a href="#">BOOK</a>
  </div>
</nav>

<div id="sideMenu" class="side-menu">
  <button class="close-menu" onclick="toggleMenu()">
    <i class="bi bi-x-lg"></i>
  </button>
  <ul>
    <li><a href="#">Home</a></li>
    <li><a href="#">Rooms</a></li>
    <li><a href="#">About</a></li>
    <li><a href="#">Contact</a></li>
    <?php if ($username): ?>
      <li><a href="#"><?= htmlspecialchars($username) ?> (Logout)</a></li>
    <?php else: ?>
      <li><a href="#">Login</a></li>
    <?php endif; ?>
  </ul>
</div>

<script>
  function toggleMenu() {
    const menu = document.getElementById("sideMenu");
    menu.classList.toggle("show");
  }
</script>
