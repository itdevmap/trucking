<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Inbound.xls");

$idx = $_GET['id'];	
$x=base64_decode($idx);
$pecah = explode("|", $x);
$tgl1= $pecah[0];
$tgl2= $pecah[1];
$field= $pecah[2];
$cari= $pecah[3];
$field1 = $pecah[4];
$cari1 = $pecah[5];
$stat = $pecah[6];

if($field == 'Customer')
	{
		$f = 'm_cust_tr.nama_cust';	
	}else if($field == 'No Doc'){
		$f = 't_ware_data.no_doc';	
	}else if($field == 'Container'){
		$f = 't_ware_data_detil.no_cont';		
	}else if($field == 'Item Number'){
		$f = 't_ware.kode';		
	}else if($field == 'Description'){
		$f = 't_ware.nama';		
	}else{
		$f = 't_ware.nama';
	}
	
	if($field1 == 'Customer')
	{
		$f1 = 'm_cust_tr.nama_cust';	
	}else if($field1 == 'No Doc'){
		$f1 = 't_ware_data.no_doc';	
	}else if($field1 == 'Container'){
		$f1 = 't_ware_data_detil.no_cont';		
	}else if($field1 == 'Item Number'){
		$f1 = 't_ware.kode';		
	}else if($field1 == 'Description'){
		$f1 = 't_ware.nama';		
	}else{
		$f1 = 't_ware.nama';
	}
	
	if($stat == 'In Progress')
	{
		$stat = '0';
	}
	else if($stat == 'Executed')
	{
		$stat = '1';
	}
	
	
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">Data Inboud Barang</th>
	</tr>
	<tr>
		<th style="font-size:12; width:90px;text-align:left"></th>
		<th colspan="7" style="font-size:12; text-align:left"></th>
	</tr>
</table>


<table border="1">
	
	<tr>
		<th style="font-size:12; width:90px;text-align:center">NO</th>
		<th style="font-size:12; width:90px;text-align:center">DATE</th>
		<th style="font-size:12; width:90px;text-align:center">NO DOC</th>
		<th style="font-size:12; width:90px;text-align:center">BATCH NUMBER</th>		
		<th style="font-size:12; width:90px;text-align:center">ITEM NO</th>
		<th style="font-size:12; width:600px;text-align:center">ITEM DESCRIPTION</th>
		<th style="font-size:12; width:600px;text-align:center">CUSTOMER</th>
		<th style="font-size:12; width:90px;text-align:center">QTY</th>
		<th style="font-size:12; width:90px;text-align:center">CBM</th>
		<th style="font-size:12; width:90px;text-align:center">LOKASI</th>
	</tr>
	
	<?php
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);
	
	if($stat == 'All')
	{
		$t1 = "select t_ware_data.*, t_ware_data_detil.id_detil, t_ware_data_detil.no_cont, t_ware_data_detil.masuk, t_ware_data_detil.keluar, t_ware.nama, t_ware.kode, 
			t_ware.vol, t_ware.unit, m_cust_tr.nama_cust, m_lokasi_ware.nama as nama_lokasi
			from  
			t_ware_data left join t_ware_data_detil on t_ware_data.id_data = t_ware_data_detil.id_data
			left join t_ware on t_ware_data_detil.id_ware = t_ware.id_ware 
			left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
			left join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust
			left join m_lokasi_ware on t_ware_data_detil.id_lokasi = m_lokasi_ware.id_lokasi
			where t_ware_data.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' and t_ware_data.jenis = '0'
			order  by t_ware_data.tanggal desc ";
	}else{
		$t1 = "select t_ware_data.*, t_ware_data_detil.id_detil, t_ware_data_detil.no_cont, t_ware_data_detil.masuk, t_ware_data_detil.keluar, t_ware.nama, t_ware.kode, 
			t_ware.vol, t_ware.unit, m_cust_tr.nama_cust, m_lokasi_ware.nama as nama_lokasi
			from  
			t_ware_data left join t_ware_data_detil on t_ware_data.id_data = t_ware_data_detil.id_data
			left join t_ware on t_ware_data_detil.id_ware = t_ware.id_ware 
			left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
			left join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust
			left join m_lokasi_ware on t_ware_data_detil.id_lokasi = m_lokasi_ware.id_lokasi
			where t_ware_data.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' and t_ware_data.jenis = '0' 
			and t_ware_data.status = '$stat'
		order  by t_ware_data.tanggal desc ";
	}
	
	$h1=mysqli_query($koneksi, $t1);       
	while ($d1=mysqli_fetch_array($h1))
	{
		$n++;
		$ptgl = explode("-", $d1['tanggal']);
		$th = $ptgl[0];
		$bl = $ptgl[1];		
		$tg = $ptgl[2];
		$year = substr($th,2,2);
		$batch = "$tg.$bl.$year $d1[no_cont]";	
		$sisa  = $d1['masuk'] - $d1['keluar'];
		$cbm = $sisa * $d1['vol'];
	?>
		<tr>
			<td style="text-align:center"><?php echo "$n.";?></b></td>
			<td style="text-align:center"><?php echo "$d1[tanggal]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_doc]";?></b></td>
			<td style="text-align:center"><?php echo "$batch";?></b></td>
			<td style="text-align:center"><?php echo "$d1[kode]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[nama]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[nama_cust]";?></b></td>
			<td style="text-align:center"><?php echo "$sisa";?></b></td>
			<td style="text-align:center"><?php echo rtrim(rtrim(number_format($cbm, 10, '.', ''), '0'), '.'); ?></td>
			<td style="text-align:center"><?php echo "$d1[nama_lokasi]";?></b></td>
		</tr>

	<?php }
	$t_sisa = $t_tag - $t_bayar;
	?>
	
	
</table>	
