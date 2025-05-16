<link rel="stylesheet" href="styles/ErrorModule.css">

<div id="popup-notification" class="popuperror" role="alert">
  <?php 
    echo $_SESSION['Error']; 
    unset($_SESSION['Error']);
  ?>
</div>

<script>
  setTimeout(() => {
    document.getElementById('popup-notification').style.opacity = '0';
  }, 3000);
</script>
