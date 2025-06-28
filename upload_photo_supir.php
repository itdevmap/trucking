<?php
session_start(); 
include "session_log.php"; 
include("koneksi.php");

$valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp'); 
$path = 'supir/'; 

if(isset($_FILES['image']))
{
	$img = $_FILES['image']['name'];
	$tmp = $_FILES['image']['tmp_name'];
	$photo_lama  = $_POST['photo_lama'];
	$id  = $_POST['idy'];
	$ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
	$final_image = rand(1000,1000000).$img;	
	if(in_array($ext, $valid_extensions)) 
	{   
		$path = $path.strtolower($final_image); 			
		if(move_uploaded_file($tmp,$path)) 
		{
			echo "<img src='$path' />";
			
			//HAPUS DATA LAMA
			$pq = mysqli_query($koneksi, "select * from m_supir_tr where id_supir = '$id' ");
			$rq=mysqli_fetch_array($pq);	
			unlink($rq['photo']);
			
			$path =  "supir/$final_image";
			$sql = "update m_supir_tr set	photo = '$path'  where id_supir = '$id' ";
			$hasil=mysqli_query($koneksi, $sql);
		}
	} 
	else 
	{
		echo 'invalid';
	}
}

?>