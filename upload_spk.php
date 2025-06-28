<?php
session_start(); 
include "session_log.php"; 
include("koneksi.php");

$valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp'); 
$path = 'spk/'; 

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
			$pq = mysqli_query($koneksi, "select * from t_spk where id_spk = '$id' ");
			$rq=mysqli_fetch_array($pq);	
			unlink($rq['photo']);
			
			$path =  "spk/$final_image";
			$sql = "update t_spk set photo = '$path'  where id_spk = '$id' ";
			$hasil=mysqli_query($koneksi, $sql);
		}
	} 
	else 
	{
		echo 'invalid';
	}
}

?>