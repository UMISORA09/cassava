<?php
/**
 * Logout Controller
 */

session_start();
session_destroy();
header('Location: ../../index.php');
exit;

?>
