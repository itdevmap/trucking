<?php
session_start(); 
include "session_log.php"; 
include("koneksi.php");

$valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp'); 


$id  = $_POST['id3'];

if(isset($_FILES['bpkp']))
{
	$path = 'mobil/'; 
	$img = $_FILES['bpkp']['name'];
	$tmp = $_FILES['bpkp']['tmp_name'];		
	$final = rand(1000,1000000).$img;		
	$gambar = $path.strtolower($final); 			
	if(move_uploaded_file($tmp,$gambar)) 
	{
		//HAPUS DATA LAMA
		$pq = mysqli_query($koneksi, "select * from m_mobil_tr where id_mobil = '$id' ");
		$rq = mysqli_fetch_array($pq);	
		unlink($rq['bpkp']);
	
		$nama_file =  "mobil/$final";
		$sql = "update m_mobil_tr set	bpkp = '$nama_file'  where id_mobil = '$id' ";
		$hasil = mysqli_query($koneksi, $sql);	
	}
}

if(isset($_FILES['stnk']))
{
	$path = 'mobil/'; 
	$img = $_FILES['stnk']['name'];
	$tmp = $_FILES['stnk']['tmp_name'];		
	$final = rand(1000,1000000).$img;		
	$gambar = $path.strtolower($final); 			
	if(move_uploaded_file($tmp,$gambar)) 
	{
		//HAPUS DATA LAMA
		$pq = mysqli_query($koneksi,"select * from m_mobil_tr where id_mobil = '$id' ");
		$rq = mysqli_fetch_array($pq);	
		unlink($rq['stnk']);
	
		$nama_file =  "mobil/$final";
		$sql = "update m_mobil_tr set	stnk = '$nama_file'  where id_mobil = '$id' ";
		$hasil = mysqli_query($koneksi, $sql);	
	}
}

if(isset($_FILES['kir']))
{
	$path = 'mobil/'; 
	$img = $_FILES['kir']['name'];
	$tmp = $_FILES['kir']['tmp_name'];		
	$final = rand(1000,1000000).$img;		
	$gambar = $path.strtolower($final); 			
	if(move_uploaded_file($tmp,$gambar)) 
	{
		//HAPUS DATA LAMA
		$pq = mysqli_query($koneksi, "select * from m_mobil_tr where id_mobil = '$id' ");
		$rq = mysqli_fetch_array($pq);	
		unlink($rq['kir']);
	
		$nama_file =  "mobil/$final";
		$sql = "update m_mobil_tr set	kir = '$nama_file'  where id_mobil = '$id' ";
		$hasil = mysqli_query($koneksi, $sql);	
	}
}

?>