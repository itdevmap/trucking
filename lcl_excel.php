<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=LCL.xls");

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

if($stat == 'Open')
	{
		$stat = '0';
	}
	else if($stat == 'Close')
	{
		$stat = '1';
	}
	
	
							
	if($field == 'No Order')
	{
		$f = 't_jo_tr.no_jo';	
	}else if($field == 'Customer'){
		$f = 'm_cust_tr.nama_cust';	
	}else if($field == 'Asal'){
		$f = 'm_kota_tr.nama_kota';
	}else if($field == 'Tujuan'){
		$f = 'm_kota1.nama_kota';	
	}else if($field == 'No SJ'){
		$f = 't_jo_sj_tr.no_sj';		
	}else if($field == 'Nama Barang'){
		$f = 't_jo_tr.nama_barang';	
	}else{
		$f = 't_jo_tr.no_jo';
	}
	
	if($field1 == 'No Order')
	{
		$f1 = 't_jo_tr.no_jo';	
	}else if($field1 == 'Customer'){
		$f1 = 'm_cust_tr.nama_cust';	
	}else if($field1 == 'Asal'){
		$f1 = 'm_kota_tr.nama_kota';
	}else if($field1 == 'Tujuan'){
		$f1 = 'm_kota1.nama_kota';	
	}else if($field1 == 'No SJ'){
		$f1 = 't_jo_sj_tr.no_sj';
	}else if($field1 == 'Nama Barang'){
		$f1 = 't_jo_tr.nama_barang';	
	}else{
		$f1 = 't_jo_tr.no_jo';
	}
	
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">Data Order LCL</th>
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
		<th style="font-size:12; width:90px;text-align:center">CUSTOMER</th>
		<th style="font-size:12; width:600px;text-align:center">ASAL</th>
		<th style="font-size:12; width:90px;text-align:center">TUJUAN</th>
		<th style="font-size:12; width:700px;text-align:center">BARANG</th>
		<th style="font-size:12; width:700px;text-align:center">BERAT</th>
		<th style="font-size:12; width:700px;text-align:center">VOL</th>
		<th style="font-size:12; width:700px;text-align:center">BIAYA KIRIM</th>
	</tr>
	
	<?php
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);
	
	if($stat == 'All')
	{
		$t1 = "select t_jo_tr.*, m_cust_tr.nama_cust, m_kota_tr.nama_kota as asal,
				m_kota1.nama_kota as tujuan, t_jo_sj_tr.no_sj
			  from 
			  t_jo_tr inner join m_cust_tr on  t_jo_tr.id_cust = m_cust_tr.id_cust
			  left join m_kota_tr on t_jo_tr.id_asal = m_kota_tr.id_kota
			  left join m_kota_tr as m_kota1 on t_jo_tr.id_tujuan = m_kota1.id_kota
			  left join t_jo_sj_tr on t_jo_tr.id_sj = t_jo_sj_tr.id_sj
		    where t_jo_tr.tgl_jo between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%'  and t_jo_tr.tipe = 'LCL'
		  order by t_jo_tr.tgl_jo asc, t_jo_tr.no_jo desc";			
	}else{
		$t1 = "select t_jo_tr.*, m_cust_tr.nama_cust, m_kota_tr.nama_kota as asal,
				m_kota1.nama_kota as tujuan, t_jo_sj_tr.no_sj
			  from 
			  t_jo_tr inner join m_cust_tr on  t_jo_tr.id_cust = m_cust_tr.id_cust
			  left join m_kota_tr on t_jo_tr.id_asal = m_kota_tr.id_kota
			  left join m_kota_tr as m_kota1 on t_jo_tr.id_tujuan = m_kota1.id_kota
			  left join t_jo_sj_tr on t_jo_tr.id_sj = t_jo_sj_tr.id_sj
		   where t_jo_tr.tgl_jo between '$tgl1x' and '$tgl2x'  and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%'  and t_jo_tr.tipe = 'LCL'
		and t_jo_tr.status = '$stat'
		 order by t_jo_tr.tgl_jo asc, t_jo_tr.no_jo desc";	
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
			<td style="text-align:left"><?php echo "$d1[nama_cust]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[asal]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[tujuan]";?></b></td>
			<td style="text-align:left"><?php echo "$d1[nama_barang]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[berat]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[vol]";?></b></td>
			<td style="text-align:right"><?php echo "$d1[tagihan]";?></b></td>
		</tr>

		
	<?php }
	$t_sisa = $t_tag - $t_bayar;
	?>
	
	
</table>	
