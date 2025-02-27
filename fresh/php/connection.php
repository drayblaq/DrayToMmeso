<?php
try {
    $bd = new PDO('mysql:host=localhost;dbname=fishes_online;charset=utf8','root', '', 
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
  );
  } catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
  }
?>