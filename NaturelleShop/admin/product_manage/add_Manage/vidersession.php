<?php
session_start();



// Rediriger vers une page de confirmation ou une autre page
header("Location: ../add.php?message=Sessions%20vidées%20avec%20succès.");
exit();
?>
