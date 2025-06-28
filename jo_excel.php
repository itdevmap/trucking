<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=DATAORDER.xls");

$idx = $_GET['id'];	
$x=base64_decode($idx);
$pecah = explode("|", $x);
$tgl1= $pecah[0];
$tgl2= $pecah[1];
$stat = $pecah[2];
$field = $pecah[3];
$cari = $pecah[4];
$field1 = $pecah[5];
$cari1 = $pecah[6];

if($field == 'No Order')
	{
		$f = 'tr_jo.no_jo';	
	}else if($field == 'No Quo'){
		$f = 'tr_quo.quo_no';	
	}else if($field == 'No DO'){
		$f = 'tr_jo.no_do';		
	}else if($field == 'Customer'){
		$f = 'm_cust_tr.nama_cust';		
	}else if($field == 'Asal'){
		$f = 'm_kota_tr.nama_kota';
	}else if($field == 'Tujuan'){
		$f = 'm_kota1.nama_kota';	
	}else if($field == 'No Cont'){
		$f = 'tr_jo.no_cont';	
	}else if($field == 'Supir'){
		$f = 'm_supir_tr.nama_supir';		
	}else if($field == 'No Polisi'){
		$f = 'm_mobil_tr.no_polisi';			
	}else{
		$f = 't_jo_tr.no_jo';
	}
	
	if($field1 == 'No Order')
	{
		$f1 = 'tr_jo.no_jo';	
	}else if($field1 == 'No Quo'){
		$f1 = 'tr_quo.quo_no';	
	}else if($field1 == 'No DO'){
		$f1 = 'tr_jo.no_do';		
	}else if($field1 == 'Customer'){
		$f1 = 'm_cust_tr.nama_cust';		
	}else if($field1 == 'Asal'){
		$f1 = 'm_kota_tr.nama_kota';
	}else if($field1 == 'Tujuan'){
		$f1 = 'm_kota1.nama_kota';	
	}else if($field1 == 'No Cont'){
		$f1 = 'tr_jo.no_cont';	
	}else if($field1 == 'Supir'){
		$f1 = 'm_supir_tr.nama_supir';		
	}else if($field1 == 'No Polisi'){
		$f1 = 'm_mobil_tr.no_polisi';			
	}else{
		$f1 = 't_jo_tr.no_jo';
	}
	
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">Data Order </th>
	</tr>
	<tr>
		<th style="font-size:12; width:90px;text-align:left">Date</th>
		<th colspan="7" style="font-size:12; text-align:left">: <?php echo "$tgl1  to  $tgl2 "; ?></th>
	</tr>
	<tr>
		<th style="font-size:12; width:90px;text-align:left"></th>
		<th colspan="7" style="font-size:12; text-align:left"></th>
	</tr>
</table>


<table border="1">
	
	<tr>
		<th style="font-size:12; width:90px;text-align:center">NO</th>
		<th style="font-size:12; width:90px;text-align:center">TANGGAL</th>
		<th style="font-size:12; width:600px;text-align:center">NO ORDER</th>
		<th style="font-size:12; width:600px;text-align:center">NO. PO</th>
		<th style="font-size:12; width:700px;text-align:center">NO. POLISI</th>
		<th style="font-size:12; width:700px;text-align:center">DRIVER</th>
		<th style="font-size:12; width:700px;text-align:center">FEET</th>
		<th style="font-size:12; width:700px;text-align:center">NO CONTAINER</th>
		<th style="font-size:12; width:90px;text-align:center">CUSTOMER</th>
		<th style="font-size:12; width:90px;text-align:center">GUDANG PENERIMA</th>
		<th style="font-size:12; width:90px;text-align:center">ROUTE</th>
		<th style="font-size:12; width:700px;text-align:center">PRICE</th>
		<th style="font-size:12; width:700px;text-align:center">UANG JALAN</th>
		<th style="font-size:12; width:700px;text-align:center">RITASE</th>
	</tr>
	
	<?php
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);
	
	$t1 = "select tr_jo.*, tr_quo.quo_no, m_cust_tr.nama_cust, m_kota_tr.nama_kota as asal, m_kota1.nama_kota as tujuan,
			m_mobil_tr.no_polisi, m_supir_tr.nama_supir
			from 
			tr_jo left join tr_quo_data on tr_jo.id_detil_quo = tr_quo_data.id_detil
			left join tr_quo on tr_quo_data.id_quo = tr_quo.id_quo
			left join m_kota_tr on tr_jo.id_asal = m_kota_tr.id_kota
			left join m_kota_tr as m_kota1 on tr_jo.id_tujuan = m_kota1.id_kota
			left join m_cust_tr on tr_jo.id_cust = m_cust_tr.id_cust
			left join m_mobil_tr on tr_jo.id_mobil = m_mobil_tr.id_mobil
			left join m_supir_tr on tr_jo.id_supir = m_supir_tr.id_supir
			where tr_jo.tgl_jo between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' 
			  order by tr_jo.tgl_jo desc, tr_jo.no_jo desc";	

	$h1=mysqli_query($koneksi, $t1);       
	while ($d1=mysqli_fetch_array($h1))
	{
		$n++;
		
	?>
	
		<tr>
			<td style="text-align:center"><?php echo "$n.";?></b></td>
			<td style="text-align:center"><?php echo "$d1[tgl_jo]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_jo]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_do]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_polisi]";?></b></td>
			<td style="text-align:left"><?php echo "$d1[nama_supir]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[jenis_mobil]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_cont]";?></b></td>
			<td style="text-align:left"><?php echo "$d1[nama_cust]";?></b></td>
			<td style="text-align:left"><?php echo "$d1[penerima]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[tujuan]";?></b></td>
			<td style="text-align:right"><?php echo "$d1[biaya_kirim]";?></b></td>
			<td style="text-align:right"><?php echo "$d1[uj]";?></b></td>
			<td style="text-align:right"><?php echo "$d1[ritase]";?></b></td>
		</tr>

		
	<?php }
	$t_sisa = $t_tag - $t_bayar;
	?>
	
	
</table>	
