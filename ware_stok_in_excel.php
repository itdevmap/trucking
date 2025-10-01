<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=STOK BARANG MASUK.xls");

$idx = $_GET['id'];	
$id	=base64_decode($idx);

// echo $id;
// die();

$pq 		= mysqli_query($koneksi,"SELECT 
				t_ware_data.*, 
				t_ware_data_detil.id_detil, 
				t_ware_data_detil.no_cont, 
				t_ware_data_detil.masuk, 
				t_ware_data_detil.keluar, 
				t_ware.nama, 
				t_ware.kode, 
				t_ware.vol, 
				t_ware.unit,
				m_cust_tr.nama_cust, 
				m_lokasi_ware.nama AS nama_lokasi
			FROM t_ware_data 
			LEFT JOIN t_ware_data_detil ON t_ware_data.id_data = t_ware_data_detil.id_data
			LEFT JOIN t_ware ON t_ware_data_detil.id_ware = t_ware.id_ware 
			LEFT JOIN t_ware_quo ON t_ware.id_quo = t_ware_quo.id_quo
			LEFT JOIN m_cust_tr ON t_ware_data.id_cust = m_cust_tr.id_cust
			LEFT JOIN m_lokasi_ware ON t_ware_data_detil.id_lokasi = m_lokasi_ware.id_lokasi
		    where t_ware_data_detil.id_detil = '$id'");

$rq			=	mysqli_fetch_array($pq);
$ptgl 		= explode("-", $rq['tanggal']);
$th 		= $ptgl[0];
$bl 		= $ptgl[1];		
$tg 		= $ptgl[2];
$year 		= substr($th,2,2);
$batch 		= "$tg.$bl.$year $rq[no_cont]";	
		
$no_doc 	= $rq['no_doc'];
$nama_cust 	= $rq['nama_cust'];
$kode 		= $rq['kode'];	
$nama 		= $rq['nama'];
$qty 		= $rq['masuk'];
$unit 		= $rq['unit'];
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">Data Barang</th>
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
		<th colspan="2" style="font-size:12; width:90px;text-align:left">Qty</th>
		<th colspan="7" style="font-size:12; text-align:left"><?php echo "$qty $unit";?></th>
	</tr>
	<tr>
		<th colspan="2" style="font-size:12; width:90px;text-align:left"></th>
		<th colspan="7" style="font-size:12; text-align:left"></th>
	</tr>
</table>


<table border="1">
	
	<tr>
		<th style="font-size:12; width:90px;text-align:center">NO</th>
		<th style="font-size:12; width:90px;text-align:center">DATE</th>
		<th style="font-size:12; width:90px;text-align:center">NO SJ</th>
		<th style="font-size:12; width:90px;text-align:center">TUJUAN</th>
		<th style="font-size:12; width:90px;text-align:center">UNIT</th>
		<th style="font-size:12; width:90px;text-align:center">QTY</th>
		<th style="font-size:12; width:90px;text-align:center">BALANCE</th>
		<th style="font-size:12; width:90px;text-align:center">REMARK</th>
	</tr>
	
	<?php
		$n = 0;
		$sisa = $qty;

		$t1 = "SELECT 
					t_ware_data.*, 
					t_ware_data_detil.masuk, 
					t_ware_data_detil.keluar
				FROM t_ware_data 
				INNER JOIN t_ware_data_detil ON t_ware_data.id_data = t_ware_data_detil.id_data    
				WHERE t_ware_data_detil.id_detil_masuk = '$id' 
					AND t_ware_data.status = '1'
				ORDER BY t_ware_data.tanggal ASC";

		$h1 = mysqli_query($koneksi, $t1);       

		while ($d1 = mysqli_fetch_array($h1)) {
			$n++;
			$keluar = (int)$d1['keluar'];
			$masuk  = (int)$d1['masuk'];
			$sisa = $sisa + $masuk - $keluar;
	?>
			<tr>
				<td style="text-align:center"><?php echo $n . "."; ?></td>
				<td style="text-align:center"><?php echo $d1['tanggal']; ?></td>
				<td style="text-align:center"><?php echo $d1['no_doc']; ?></td>
				<td style="text-align:center"><?php echo $d1['gudang']; ?></td>
				<td style="text-align:center"><?php echo $unit; ?></td>
				<td style="text-align:center"><?php echo $keluar; ?></td>
				<td style="text-align:center"><?php echo $sisa; ?></td>
				<td style="text-align:center"><?php echo $d1['rem']; ?></td>
			</tr>
	<?php
		}
	?>

	
</table>	
