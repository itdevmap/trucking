<?php
session_start(); 
include "session_log.php"; 
include("koneksi.php");

$valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp'); 
$id  = $_POST['id3'];

if(isset($_FILES['ktp']))
{
	$path = 'supir/'; 
	$img = $_FILES['ktp']['name'];
	$tmp = $_FILES['ktp']['tmp_name'];		
	$final = rand(1000,1000000).$img;		
	$gambar = $path.strtolower($final); 			
	if(move_uploaded_file($tmp,$gambar)) 
	{
		//HAPUS DATA LAMA
		$pq = mysqli_query($koneksi, "select * from m_supir_tr where id_supir = '$id' ");
		$rq = mysqli_fetch_array($pq);	
		unlink($rq['ktp']);
	
		$nama_file =  "supir/$final";
		$sql = "update m_supir_tr set	ktp = '$nama_file'  where id_supir = '$id' ";
		$hasil = mysqli_query($koneksi, $sql);	
	}
}

if(isset($_FILES['kk']))
{
	$path = 'supir/'; 
	$img = $_FILES['kk']['name'];
	$tmp = $_FILES['kk']['tmp_name'];		
	$final = rand(1000,1000000).$img;		
	$gambar = $path.strtolower($final); 			
	if(move_uploaded_file($tmp,$gambar)) 
	{
		//HAPUS DATA LAMA
		$pq = mysqli_query($koneksi, "select * from m_supir_tr where id_supir = '$id' ");
		$rq=mysqli_fetch_array($pq);	
		unlink($rq['kk']);
	
		$nama_file =  "supir/$final";
		$sql = "update m_supir_tr set	kk = '$nama_file'  where id_supir = '$id' ";
		$hasil=mysqli_query($koneksi, $sql);	
	}
}

if(isset($_FILES['sim']))
{
	$path = 'supir/'; 
	$img = $_FILES['sim']['name'];
	$tmp = $_FILES['sim']['tmp_name'];		
	$final = rand(1000,1000000).$img;		
	$gambar = $path.strtolower($final); 			
	if(move_uploaded_file($tmp,$gambar)) 
	{
		//HAPUS DATA LAMA
		$pq = mysqli_query($koneksi, "select * from m_supir_tr where id_supir = '$id' ");
		$rq=mysqli_fetch_array($pq);	
		unlink($rq['sim']);
	
		$nama_file =  "supir/$final";
		$sql = "update m_supir_tr set	sim = '$nama_file'  where id_supir = '$id' ";
		$hasil=mysqli_query($koneksi, $sql);	
	}
}


?>