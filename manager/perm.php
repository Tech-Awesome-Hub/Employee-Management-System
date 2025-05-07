<?php
      session_start();
      include('header.php');
      include('includes/connection.php');
      
      if (empty($_SESSION['name']) || $_SESSION['role'] != 3) {
          header("Location: ../index.php");
          exit();
      }
      
      $supervisor_id = $_SESSION['id'];
      $dept = $_SESSION['department'];

?>

<div class="page-wrapper">
    <div class="content">
        <div class="container mt-2">

        
        </div>
    </div>
</div>
