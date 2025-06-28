<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=DATA BARANG KELUAR.xls");

$idx = $_GET['id'];	
$id=base64_decode($idx);
	
$pq = mysqli_query($koneksi,"select t_ware_data.*, t_ware_data_detil.id_detil, t_ware_data_detil.no_cont, t_ware_data_detil.masuk, t_ware_data_detil.keluar, t_ware.nama, t_ware.kode, 
			t_ware.vol, t_ware.unit, m_cust_tr.nama_cust, m_lokasi_ware.nama as nama_lokasi
			from  
			t_ware_data left join t_ware_data_detil on t_ware_data.id_data = t_ware_data_detil.id_data
			left join t_ware on t_ware_data_detil.id_ware = t_ware.id_ware 
			left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
			left join m_cust_tr on t_ware_data.id_cust = m_cust_tr.id_cust
			left join m_lokasi_ware on t_ware_data_detil.id_lokasi = m_lokasi_ware.id_lokasi
		    where t_ware_data_detil.id_detil = '$id' ");
$rq=mysqli_fetch_array($pq);

$ptgl = explode("-", $rq['tanggal']);
$th = $ptgl[0];
$bl = $ptgl[1];		
$tg = $ptgl[2];
$year = substr($th,2,2);
$batch = "$tg.$bl.$year $rq[no_cont]";	
		
$no_doc = $rq['no_doc'];
$nama_cust = $rq['nama_cust'];
$kode = $rq['kode'];	
$nama = $rq['nama'];
$qty = $rq['masuk'];
$unit = $rq['unit'];
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">Data Barang Keluar</th>
	</tr>
	<tr>
		<th style="font-size:12; width:90px;text-align:left"></th>
		<th colspan="7" style="font-size:12; text-align:left"></th>
	</tr>
	<tr>
		<th colspan="2" style="font-size:12; width:90px;text-align:left">No Doc</th>
		<th colspan="7" style="font-size:12; text-align:left"><?php echo $no_doc;?></th>
	</tr>
	<tr>
		<th colspan="2" style="font-size:12; width:90px;text-align:left">Item Number</th>
		<th colspan="7" style="font-size:12; text-align:left"><?php echo $kode;?></th>
	</tr>
	<tr>
		<th colspan="2" style="font-size:12; width:90px;text-align:left">Item Description</th>
		<th colspan="7" style="font-size:12; text-align:left"><?php echo $nama;?></th>
	</tr>
	<tr>
		<th colspan="2" style="font-size:12; width:90px;text-align:left">Customer</th>
		<th colspan="7" style="font-size:12; text-align:left"><?php echo $nama_cust;?></th>
	</tr>
	<tr>
		<th colspan="2" style="font-size:12; width:90px;text-align:left">Bact Number</th>
		<th colspan="7" style="font-size:12; text-align:left"><?php echo $batch;?></th>
	</tr>
	<tr>
		<th colspan="2" style="font-size:12; width:90px;text-align:left">Qty Inbound</th>
		<th colspan="7" style="font-size:12; text-align:left"><?php echo "$qty $unit";?></th>
	</tr>
	<tr>
		<th colspan="2" style="font-size:12; width:90px;text-align:left"></th>
		<th colspan="7" style="font-size:12; text-align:left"></th>
	</tr>
</table>


<table border="1">
	
	<tr>
		<th style="font-size:12; width:90px;text-align:center">No</th>
		<th style="font-size:12; width:90px;text-align:center">Date</th>
		<th style="font-size:12; width:90px;text-align:center">No SJ</th>
		<th style="font-size:12; width:90px;text-align:center">Tujuan</th>
		<th style="font-size:12; width:90px;text-align:center">Qty Outbound</th>
	</tr>
	
	<?php
	$total = 0;
	$t1 = "select t_ware_data.*, t_ware_data_detil.masuk, t_ware_data_detil.keluar
		   from 
		   t_ware_data inner join t_ware_data_detil on t_ware_data.id_data = t_ware_data_detil.id_data	
		   where t_ware_data_detil.id_detil_masuk = '$id' and t_ware_data.status = '1'	";
	$h1=mysqli_query($koneksi, $t1);       
	while ($d1=mysqli_fetch_array($h1))
	{
		$n++;
		$total = $total + $d1['keluar'];
		$sisa  = $qty - $d1['keluar'];
		$keluar = $d1['keluar'];
		
	?>
	
		<tr>
			<td style="font-size:12; text-align:center"><?php echo "$n.";?></b></td>
			<td style="font-size:12; text-align:center"><?php echo "$d1[tanggal]";?></b></td>
			<td style="font-size:12; text-align:center"><?php echo "$d1[no_doc]";?></b></td>
			<td style="font-size:12; text-align:center"><?php echo "$d1[gudang]";?></b></td>
			<td style="font-size:12; text-align:center"><?php echo "$keluar";?></b></td>
		</tr>

		
	<?php }	
	//$sisa = $t_in - $t_out;
	?>
	<tr>
		<td colspan="3" style="font-size:12;text-align:center"></b></td>
		<td style="font-size:12;text-align:right">Total Outbound</b></td>
		<td style="font-size:12;text-align:center"><?php echo "$total";?></b></td>
	</tr>
	<tr>
		<td colspan="3" style="font-size:12;text-align:center"></b></td>
		<td style="font-size:12;text-align:right">Qty Inbound</b></td>
		<td style="font-size:12;text-align:center"><?php echo "$qty";?></b></td>
	</tr>
	<tr>
		<td colspan="3" style="font-size:12;text-align:center"></b></td>
		<td style="font-size:12;text-align:right">Sisa</b></td>
		<td style="font-size:12;text-align:center"><?php echo "$sisa";?></b></td>
	</tr>
</table>	
