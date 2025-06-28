<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=FCL.xls");

$idx = $_GET['id'];	
$x=base64_decode($idx);
$pecah = explode("|", $x);
$tgl1= $pecah[0];
$tgl2= $pecah[1];
$stat = $pecah[2];
$field = $pecah[3];
$cari = $pecah[4];
$field = $pecah[5];
$cari = $pecah[6];

if($field == 'No Order')
	{
		$f = 't_jo_tr.no_jo';	
	}else if($field == 'No PO'){
		$f = 't_jo_tr.no_po';	
	}else if($field == 'Customer'){
		$f = 'm_cust_tr.nama_cust';	
	}else if($field == 'Asal'){
		$f = 'm_kota_tr.nama_kota';
	}else if($field == 'Tujuan'){
		$f = 'm_kota1.nama_kota';	
	}else if($field == 'Jenis'){
		$f = 't_jo_detil_tr.jenis_mobil';	
	}else if($field == 'No SJ'){
		$f = 't_jo_sj_tr.no_sj';		
	}else{
		$f = 't_jo_tr.no_jo';
	}
	
	if($field1 == 'No Order')
	{
		$f1 = 't_jo_tr.no_jo';	
	}else if($field1 == 'No PO'){
		$f1 = 't_jo_tr.no_po';	
	}else if($field1 == 'Customer'){
		$f1 = 'm_cust_tr.nama_cust';	
	}else if($field1 == 'Asal'){
		$f1 = 'm_kota_tr.nama_kota';
	}else if($field1 == 'Tujuan'){
		$f1 = 'm_kota1.nama_kota';	
	}else if($field1 == 'Jenis'){
		$f1 = 't_jo_detil_tr.jenis_mobil';	
	}else if($field1 == 'No SJ'){
		$f1 = 't_jo_sj_tr.no_sj';		
	}else{
		$f1 = 't_jo_tr.no_jo';
	}
	
	if($stat == 'Open')
	{
		$stat = '0';
	}
	else if($stat == 'Close')
	{
		$stat = '1';
	}
	
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">Data Order FCL</th>
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
		<th style="font-size:12; width:90px;text-align:center">CUSTOMER</th>
		<th style="font-size:12; width:600px;text-align:center">NO. SJ</th>
		<th style="font-size:12; width:90px;text-align:center">ROUTE</th>
		<th style="font-size:12; width:700px;text-align:center">PRICE</th>
		<th style="font-size:12; width:700px;text-align:center">CONTAINER</th>
		<th style="font-size:12; width:700px;text-align:center">FEET</th>
		<th style="font-size:12; width:700px;text-align:center">NO. POLISI</th>
		<th style="font-size:12; width:700px;text-align:center">DRIVER</th>
		<th style="font-size:12; width:700px;text-align:center">UANG JALAN</th>
		<th style="font-size:12; width:700px;text-align:center">RITASE</th>
	</tr>
	
	<?php
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);
	
	if($stat == 'All')
	{
		$t1 = "select t_jo_tr.*, t_jo_detil_tr.status as status_sj, t_jo_detil_tr.id_detil, t_jo_detil_tr.jenis_mobil, t_jo_detil_tr.biaya_kirim, t_jo_detil_tr.biaya_lain, 
			m_kota_tr.nama_kota as asal,	m_kota1.nama_kota as tujuan, m_cust_tr.nama_cust, 
			t_jo_sj_tr.no_sj, t_jo_sj_tr.id_sj, t_jo_sj_tr.no_cont, t_jo_sj_tr.uj, t_jo_sj_tr.ritase,
			m_mobil_tr.no_polisi,
			m_supir_tr.nama_supir
			from 
			t_jo_tr left join m_cust_tr on t_jo_tr.id_cust = m_cust_tr.id_cust
			left join t_jo_detil_tr on t_jo_tr.id_jo = t_jo_detil_tr.id_jo
			left join m_kota_tr on t_jo_detil_tr.id_asal = m_kota_tr.id_kota
			left join m_kota_tr as m_kota1 on t_jo_detil_tr.id_tujuan = m_kota1.id_kota
			left join t_jo_sj_tr on t_jo_detil_tr.id_sj = t_jo_sj_tr.id_sj
			left join m_mobil_tr on t_jo_sj_tr.id_mobil = m_mobil_tr.id_mobil
			left join m_supir_tr on t_jo_sj_tr.id_supir = m_supir_tr.id_supir
			where t_jo_tr.tgl_jo between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' and t_jo_tr.tipe = 'FCL'	
			order by t_jo_tr.tgl_jo asc";			
	}else{
		$t1 = "select t_jo_tr.*, t_jo_detil_tr.status as status_sj, t_jo_detil_tr.id_detil, t_jo_detil_tr.jenis_mobil, t_jo_detil_tr.biaya_kirim, t_jo_detil_tr.biaya_lain, 
			m_kota_tr.nama_kota as asal,	m_kota1.nama_kota as tujuan, m_cust_tr.nama_cust, t_jo_sj_tr.no_sj, t_jo_sj_tr.id_sj
			from 
			t_jo_tr left join m_cust_tr on t_jo_tr.id_cust = m_cust_tr.id_cust
			left join t_jo_detil_tr on t_jo_tr.id_jo = t_jo_detil_tr.id_jo
			left join m_kota_tr on t_jo_detil_tr.id_asal = m_kota_tr.id_kota
			left join m_kota_tr as m_kota1 on t_jo_detil_tr.id_tujuan = m_kota1.id_kota
			left join t_jo_sj_tr on t_jo_detil_tr.id_sj = t_jo_sj_tr.id_sj
			where t_jo_tr.tgl_jo between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' and t_jo_tr.tipe = 'FCL'	and t_jo_detil_tr.status = '$stat'
			order by t_jo_tr.tgl_jo asc";	
	}

	$h1=mysqli_query($koneksi, $t1);       
	while ($d1=mysqli_fetch_array($h1))
	{
		$n++;
		
	?>
	
		<tr>
			<td style="text-align:center"><?php echo "$n.";?></b></td>
			<td style="text-align:center"><?php echo "$d1[tgl_jo]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_jo]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_po]";?></b></td>
			<td style="text-align:left"><?php echo "$d1[nama_cust]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_sj]";?></b></td>
			<td style="text-align:right"><?php echo "$d1[tujuan]";?></b></td>
			<td style="text-align:right"><?php echo "$d1[biaya_kirim]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_cont]";?></b></td>
			<td style="text-align:right"><?php echo "$d1[jenis_mobil]";?></b></td>
			<td style="text-align:right"><?php echo "$d1[no_polisi]";?></b></td>
			<td style="text-align:right"><?php echo "$d1[nama_supir]";?></b></td>
			<td style="text-align:right"><?php echo "$d1[uj]";?></b></td>
			<td style="text-align:right"><?php echo "$d1[ritase]";?></b></td>
		</tr>

		
	<?php }
	$t_sisa = $t_tag - $t_bayar;
	?>
	
	
</table>	
