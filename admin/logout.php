<?php
session_start();
session_destroy();
// Redireciona para a página root
header('Location: ../');
exit();
?>
