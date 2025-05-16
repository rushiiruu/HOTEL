
<?php
/**
 * Purpose:
 *   - Presents the story, values, founders, and journey of La Ginta Real Hotel.
 *   - Shares the hotel's mission, vision, and commitment to Filipino hospitality and excellence.
 *   - Highlights the founders and key milestones in the hotel's history.
 *   - Provides guests with insight into the hotel's identity and unique qualities.
 */
  session_start();
  $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rooms & Suites</title>

    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Quicksand:wght@300..700&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&family=Roboto:ital,wght@0,100..900;1,100..900&family=Satisfy&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="styles/AboutUs.css">
  </head>
  <body>
    <body>
  <?php include 'Navbar.php'; ?>

  <!-- Hero Section -->
  <section class="hero">
    <img
      src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80"
      class="hero-image"
      alt="La Ginta Real Hotel"
    />
    <div class="hero-overlay">
      <img
        src="icons/logo-2.png"
        alt="Logo"
        class="center-logo"
      />
      <h1 class="hero-title">ABOUT LA GINTA REAL</h1>
      <h2 class="hero-subtitle">THE ESSENCE OF PHILIPPINE ELEGANCE</h2>
    </div>
  </section>

  <!-- Navigation Menu -->
  <div class="span-menu">
    <a href="Home.php">LA GINTA REAL</a>
    <a href="Rooms&Suites.php">ROOMS & SUITES</a>
    <a href="">ABOUT US</a>
  </div>

  <!-- About Section -->
  <section class="about-section">
    <div class="section-divider"></div>
    <h2 class="section-title">OUR STORY</h2>
    
    <div class="about-content">
      <p class="about-text fade-in">
        Nestled in the heart of timeless charm and coastal elegance, Hotel La Ginta Real offers a refined escape for travelers seeking comfort, sophistication, and warm Filipino hospitality. Our hotel blends classic design with modern amenities, creating a serene sanctuary where every guest feels truly welcomed.
      </p>
      
      <p class="about-text fade-in">
        From our carefully curated rooms and suites to the personalized service provided by our dedicated staff, every detail at La Ginta Real reflects our commitment to quality and relaxation. Whether you're here for a family vacation, romantic getaway, or business retreat, we ensure your experience is as luxurious as it is memorable.
      </p>
      
      <p class="about-text fade-in">
        At Hotel La Ginta Real, we believe that true hospitality means creating moments that stay with you long after you leave. Our mission is to showcase the beauty of Philippine culture through exceptional service, distinctive design, and unforgettable experiences that connect our guests to the heart and soul of our beloved country.
      </p>
    </div>
  </section>

  <!-- Our Values Section -->
  <section class="about-section">
    <div class="section-divider"></div>
    <h2 class="section-title">OUR VALUES</h2>
    
    <div class="values-container">
      <div class="value-card fade-in">
        <i class="bi bi-star-fill value-icon"></i>
        <h3 class="value-title">Excellence</h3>
        <p class="value-description">We strive for excellence in every detail, from the quality of our accommodations to the warmth of our service, ensuring an exceptional experience for all our guests.</p>
      </div>
      
      <div class="value-card fade-in">
        <i class="bi bi-house-heart value-icon"></i>
        <h3 class="value-title">Hospitality</h3>
        <p class="value-description">We embrace the Filipino tradition of genuine care and warmth, treating every guest as family and creating a home away from home.</p>
      </div>
      
      <div class="value-card fade-in">
        <i class="bi bi-globe value-icon"></i>
        <h3 class="value-title">Sustainability</h3>
        <p class="value-description">We are committed to preserving our natural environment through responsible practices and sustainable initiatives that honor the beauty of our surroundings.</p>
      </div>
    </div>
  </section>

  <!-- Founders Section -->
  <section class="founders-section">
    <div class="founders-content">
      <div class="section-divider"></div>
      <h2 class="section-title">OUR FOUNDERS</h2>
      
      <div class="founders-container">
        <div class="founder-card fade-in">
          <h3 class="founder-name">Rusyl Espina</h3>
          <p class="founder-title">Co-Founder & Chief Executive Officer</p>
          <p class="founder-bio">
            With a passion for hospitality excellence and a deep appreciation for Filipino heritage, Rusyl Espina co-founded La Ginta Real with a vision to create a luxury hotel experience that showcases the best of Philippine culture. His innovative approach to hospitality management and unwavering commitment to guest satisfaction have established La Ginta Real as a premier destination.
          </p>
        </div>
        
        <div class="founder-card fade-in">
          <h3 class="founder-name">Kenji Daymiel</h3>
          <p class="founder-title">Co-Founder & Creative Director</p>
          <p class="founder-bio">
            Kenji Daymiel brings artistic vision and creative excellence to La Ginta Real. As Co-Founder and Creative Director, his unique eye for design and commitment to authenticity have shaped the hotel's distinctive aesthetic, blending traditional Filipino elements with contemporary luxury. His dedication to creating immersive, meaningful guest experiences drives the hotel's innovative approach.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Our Journey Timeline -->
  <section class="timeline-section">
    <div class="section-divider"></div>
    <h2 class="section-title">OUR JOURNEY</h2>
    
    <div class="timeline">
      <div class="timeline-item fade-in">
        <div class="timeline-content">
          <span class="timeline-year">2018</span>
          <h3 class="timeline-title">The Beginning</h3>
          <p>La Ginta Real was envisioned by founders Rusyl Espina and Kenji Daymiel with a dream to create a luxury hotel that celebrates Philippine heritage.</p>
        </div>
      </div>
      
      <div class="timeline-item fade-in">
        <div class="timeline-content">
          <span class="timeline-year">2020</span>
          <h3 class="timeline-title">Breaking Ground</h3>
          <p>Construction began on our flagship property, designed to blend traditional Filipino architecture with modern luxury amenities.</p>
        </div>
      </div>
      
      <div class="timeline-item fade-in">
        <div class="timeline-content">
          <span class="timeline-year">2022</span>
          <h3 class="timeline-title">Grand Opening</h3>
          <p>La Ginta Real welcomed its first guests, introducing a new standard of luxury hospitality in the Philippines.</p>
        </div>
      </div>
      
      <div class="timeline-item fade-in">
        <div class="timeline-content">
          <span class="timeline-year">2023</span>
          <h3 class="timeline-title">Award-Winning Excellence</h3>
          <p>Recognized for outstanding service and design, La Ginta Real received its first industry accolades and certifications.</p>
        </div>
      </div>
      
      <div class="timeline-item fade-in">
        <div class="timeline-content">
          <span class="timeline-year">2025</span>
          <h3 class="timeline-title">Looking Forward</h3>
          <p>With a commitment to continual improvement and expansion, La Ginta Real embarks on the next chapter of its journey in luxury hospitality.</p>
        </div>
      </div>
    </div>
  </section>

  <?php include 'Footer.php'; ?>
  
  <script>
    // Intersection Observer for fade-in animations
    const faders = document.querySelectorAll('.fade-in');
    
    const appearOptions = {
      threshold: 0.15,
      rootMargin: "0px 0px -100px 0px"
    };
    
    const appearOnScroll = new IntersectionObserver(function(entries, appearOnScroll) {
      entries.forEach(entry => {
        if (!entry.isIntersecting) {
          return;
        } else {
          entry.target.classList.add('appear');
          appearOnScroll.unobserve(entry.target);
        }
      });
    }, appearOptions);
    
    faders.forEach(fader => {
      appearOnScroll.observe(fader);
    });
    
    // Menu Toggle Functionality
    function toggleMenu() {
      var sideMenu = document.getElementById('sideMenu');
      sideMenu.classList.toggle('show');
    }
  </script>
</body>
</html>