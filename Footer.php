<?php
/**
 * Footer component for La Ginta Real Philippines Hotel website
 */
?>

<link rel="stylesheet" href="styles/Footer.css">

<footer class="site-footer">
  <div class="footer-container">
    <div class="footer-column brand-column">
      <h3 class="footer-logo">LA GINTA REAL</h3>
      <p class="footer-tagline">PHILIPPINES</p>
      <p class="footer-description">Experience luxury, comfort, and exceptional service in the heart of the Philippines.</p>
      <div class="social-icons">
        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        <a href="#" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
        <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
      </div>
    </div>

    <div class="footer-column">
      <h4>Quick Links</h4>
      <ul class="footer-links">
        <li><a href="Home.php">Home</a></li>
        <li><a href="Rooms&Suites.php">Rooms & Suites</a></li>
        <li><a href="AboutUs.php">About Us</a></li>
      </ul>
    </div>

    <div class="footer-column">
      <h4>Contact Us</h4>
      <address>
        <p><i class="bi bi-geo-alt-fill"></i> 123 Sunset Boulevard<br>Manila, Philippines</p>
        <p><i class="bi bi-telephone-fill"></i> +63 2 1234 5678</p>
        <p><i class="bi bi-envelope-fill"></i> <a href="mailto:info@lagintareal.ph">info@lagintareal.ph</a></p>
      </address>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="footer-bottom-content">
      <p class="copyright">&copy; <?php echo date('Y'); ?> La Ginta Real Philippines. All rights reserved.</p>
      <ul class="footer-legal">
        <li><a href="#">Privacy Policy</a></li>
        <li><a href="#">Terms of Service</a></li>
        <li><a href="#">Cookies Policy</a></li>
      </ul>
    </div>
  </div>
</footer>

