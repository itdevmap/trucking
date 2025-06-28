<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=PART IN.xls");

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

if($field == 'Item Number')
	{
		$f = 'kode';	
	}else if($field == 'Description'){
		$f = 'nama';	
	}else{
		$f = 'nama';	
	}
	
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">Data Sparepart Masuk</th>
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
		<th style="font-size:12; width:600px;text-align:center">NO PO</th>
		<th style="font-size:12; width:90px;text-align:center">ITEM NUMBER</th>
		<th style="font-size:12; width:600px;text-align:center">ITEM DESCRIPTION</th>
		<th style="font-size:12; width:90px;text-align:center">QTY</th>
		<th style="font-size:12; width:700px;text-align:center">UNIT</th>
	</tr>
	
	<?php
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);
	
	$t1 = "select m_part_masuk.*, m_part.nama, m_part.kode, m_part.unit
				from  m_part_masuk left join m_part on m_part_masuk.id_part = m_part.id_part 
				where m_part_masuk.tanggal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' 
				order  by m_part_masuk.tanggal asc";	

	$h1=mysqli_query($koneksi, $t1);       
	while ($d1=mysqli_fetch_array($h1))
	{
		$n++;
		
	?>
	
		<tr>
			<td style="text-align:center"><?php echo "$n.";?></b></td>
			<td style="text-align:center"><?php echo "$d1[tanggal]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_po]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[kode]";?></b></td>
			<td style="text-align:left"><?php echo "$d1[nama]";?></b></td>
			<td style="text-align:center"><?php echo "$d1[qty]";?></b></td>
			<td style="text-align:left"><?php echo "$d1[unit]";?></b></td>
		</tr>

		
	<?php }
	$t_sisa = $t_tag - $t_bayar;
	?>
	
	
</table>	
