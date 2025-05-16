<?php
/**
 * Displays a popup notification for success messages using the value stored in $_SESSION['Success'].
 * The notification automatically fades out after 3 seconds.
 */
?>


<link rel="stylesheet" href="styles/ErrorModule.css">

<div id="popup-notification" class="popupsucc" role="alert">
  <?php echo $_SESSION['Success']; ?>
</div>

<script>
  setTimeout(() => {
    document.getElementById('popup-notification').style.opacity = '0';
  }, 3000);
</script>