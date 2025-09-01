<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=DataSewa.xls");

$idx = $_GET['id'];	
$x=base64_decode($idx);
$pecah = explode("|", $x);
$tgl1= $pecah[0];
$tgl2= $pecah[1];
$stat= $pecah[2];
$field= $pecah[3];
$cari= $pecah[4];
$field1 = $pecah[5];
$cari1 = $pecah[6];

if($stat == 'In Progress')
	{
		$stat = '0';
	}
	else if($stat == 'Executed')
	{
		$stat = '1';
	}
	
	if($field == 'No SO')
	{
		$f = 't_ware_sewa.no_sewa';	
	}else if($field == 'Customer'){
		$f = 'm_cust_tr.nama_cust';		
	}else if($field == 'Jenis Sewa'){
		$f = 'm_cost_tr.nama_cost';		
	}else{
		$f = 't_ware_sewa.no_sewa';	
	}
	
	if($field1 == 'No SO')
	{
		$f1 = 't_ware_sewa.no_sewa';	
	}else if($field1 == 'Customer'){
		$f1 = 'm_cust_tr.nama_cust';		
	}else if($field1 == 'Jenis Sewa'){
		$f1 = 'm_cost_tr.nama_cost';		
	}else{
		$f1 = 't_ware_sewa.no_sewa';	
	}
	
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">Data Sewa</th>
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
		<th style="font-size:12; width:600px;text-align:center">NO. SO</th>
		<th style="font-size:12; width:600px;text-align:center">CUSTOMER</th>
		<th style="font-size:12; width:90px;text-align:center">JENIS SEWA</th>
		<th style="font-size:12; width:600px;text-align:center">TAGIHAN</th>
	</tr>
	
	<?php
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);
	
	
	if($stat == 'All')
	{
		$t1 = "select t_ware_sewa.*, m_cust_tr.nama_cust, m_cost_tr.nama_cost
		  from 
		  t_ware_sewa left join m_cust_tr on t_ware_sewa.id_cust = m_cust_tr.id_cust
		  left join m_cost_tr on t_ware_sewa.id_cost = m_cost_tr.id_cost
		  where t_ware_sewa.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%'  and $f1 LIKE '%$cari1%'";
	}else{
		$t1 = "select t_ware_sewa.*, m_cust_tr.nama_cust, m_cost_tr.nama_cost
		  from 
		  t_ware_sewa left join m_cust_tr on t_ware_sewa.id_cust = m_cust_tr.id_cust
		  left join m_cost_tr on t_ware_sewa.id_cost = m_cost_tr.id_cost
		  where t_ware_sewa.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%'  and $f1 LIKE '%$cari1%' and t_ware_sewa.status = '$stat'";
	}
	
	
			
	$h1=mysqli_query($koneksi, $t1);       
	while ($d1=mysqli_fetch_array($h1))
	{
		$tanggal = strtotime($d1['tanggal']);
		$n++;
		
	?>
	
		<tr>
			<td style="text-align:center"><?php echo "$n.";?></b></td>
			<td style="text-align:center"><?php echo "$d1[tanggal]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_sewa]";?></b></td>
			<td style="text-align:left"><?php echo "$d1[nama_cust]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[nama_cost]";?></b></td>
			<td style="text-align:right"><?php echo "$d1[tagihan]";?></b></td>
		</tr>

		
	<?php }
	$t_sisa = $t_tag - $t_bayar;
	?>
	
	
</table>	
