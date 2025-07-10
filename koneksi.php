<?php
   error_reporting(error_reporting() & ~E_NOTICE);

   $koneksi = mysqli_connect("192.168.1.210", "root", "Tjap54.000", "fw_dummy");

   if (mysqli_connect_errno()){
      echo "Koneksi database mysqli gagal!!! : " . mysqli_connect_error();
   }
?>