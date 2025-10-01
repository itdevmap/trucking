<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=STOK BARANG.xls");

$idx = $_GET['id'];	
$x=base64_decode($idx);
$pecah = explode("|", $x);
$field= $pecah[3];
$cari= $pecah[4];
$field1 = $pecah[5];
$cari1 = $pecah[6];

	if($field == 'Kode Barang'){
		$f = 't_ware.kode';	
	}else if($field == 'Nama Barang'){
		$f = 't_ware.nama';	
	}else if($field == 'Quo No'){
		$f = 't_ware_quo.quo_no';		
	}else if($field == 'Customer'){
		$f = 'm_cust_tr.nama_cust';	
	}else{
		$f = 't_ware.nama';	
	}
	
	if($field1 == 'Kode Barang'){
		$f1 = 't_ware.kode';	
	}else if($field1 == 'Nama Barang'){
		$f1 = 't_ware.nama';	
	}else if($field1 == 'Quo No'){
		$f1 = 't_ware_quo.quo_no';		
	}else if($field1 == 'Customer'){
		$f1 = 'm_cust_tr.nama_cust';	
	}else{
		$f1 = 't_ware.nama';	
	}
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">Data Barang</th>
	</tr>
	<tr>
		<th style="font-size:12; width:90px;text-align:left"></th>
		<th colspan="7" style="font-size:12; text-align:left"></th>
	</tr>
</table>

<table border="1">
	<tr>
		<th style="font-size:12; width:90px;text-align:center">NO</th>
		<th style="font-size:12; width:90px;text-align:center">ITEM NUMBER</th>
		<th style="font-size:12; width:600px;text-align:center">ITEM DESCRIPTION</th>
		<th style="font-size:12; width:600px;text-align:center">UOM</th>
		<th style="font-size:12; width:700px;text-align:center">BERAT</th>
		<th style="font-size:12; width:700px;text-align:center">PANJANG</th>
		<th style="font-size:12; width:700px;text-align:center">LEBAR</th>
		<th style="font-size:12; width:700px;text-align:center">TINGGI</th>
		<th style="font-size:12; width:700px;text-align:center">VOL</th>
		<th style="font-size:12; width:90px;text-align:center">QUO NO</th>
		<th style="font-size:12; width:90px;text-align:center">CUSTOMER</th>
		<th style="font-size:12; width:700px;text-align:center">IN</th>
		<th style="font-size:12; width:700px;text-align:center">OUT</th>
		<th style="font-size:12; width:700px;text-align:center">WHSE</th>
	</tr>
	
	<?php
	$t1 = "SELECT t_ware.*, t_ware_quo.quo_no,	m_cust_tr.nama_cust
			from t_ware 
			left join t_ware_quo on t_ware.id_quo = t_ware_quo.id_quo
		   	left join m_cust_tr on t_ware_quo.id_cust = m_cust_tr.id_cust
		   	where  t_ware_quo.status = '1' and $f LIKE '%$cari%' and $f1 LIKE '%$cari1%' 
			order by t_ware.nama";	
	

	$h1=mysqli_query($koneksi, $t1);       
	while ($d1=mysqli_fetch_array($h1))
	{
		$n++;
		$sisa  = $d1['masuk'] - $d1['keluar'];
	?>
	
		<tr>
			<td style="text-align:center"><?php echo "$n.";?></b></td>			
			
			<td style="text-align:center"><?php echo "$d1[kode]";?></b></td>
			<td style="text-align:left"><?php echo "$d1[nama]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[unit]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[berat]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[panjang]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[lebar]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[tinggi]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[vol]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[quo_no]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[nama_cust]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[masuk]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[keluar]";?></b></td>
			<td style="text-align:center"><?php echo "$sisa";?></b></td>
		</tr>

	<?php }
	$t_sisa = $t_tag - $t_bayar;
	?>
	
	
</table>	
