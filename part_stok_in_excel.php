<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=PART IN.xls");

$idx = $_GET['id'];	
$id=base64_decode($idx);
$pq = mysqli_query($koneksi,"select * from m_part where id_part = '$id' ");
$rq=mysqli_fetch_array($pq);
$kode = $rq['kode'];
$nama = $rq['nama'];
$unit = $rq['unit'];	
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">Data Sparepart In </th>
	</tr>
	<tr>
		<th colspan="2" style="font-size:12; width:90px;text-align:left">Item Number</th>
		<th colspan="7" style="font-size:12; text-align:left">: <?php echo "$kode "; ?></th>
	</tr>
	<tr>
		<th colspan="2" style="font-size:12; width:90px;text-align:left">Item Description</th>
		<th colspan="7" style="font-size:12; text-align:left">: <?php echo "$nama "; ?></th>
	</tr>
	<tr>
		<th  colspan="2" style="font-size:12; width:90px;text-align:left">UoM</th>
		<th colspan="7" style="font-size:12; text-align:left">: <?php echo "$unit "; ?></th>
	</tr>
	<tr>
		<th colspan="2" style="font-size:12; width:90px;text-align:left"></th>
		<th colspan="7" style="font-size:12; text-align:left"></th>
	</tr>
</table>


<table border="1">
	
	<tr>
		<th style="font-size:12; width:90px;text-align:center">NO</th>
		<th style="font-size:12; width:90px;text-align:center">TANGGAL</th>
		<th style="font-size:12; width:600px;text-align:center">NO PO</th
		<th style="font-size:12; width:90px;text-align:center">QTY</th>
		<th style="font-size:12; width:700px;text-align:center">UNIT</th>
	</tr>
	
	<?php
	
	$t1 = "select m_part_masuk.*, m_part.nama, m_part.kode, m_part.unit
				from  m_part_masuk left join m_part on m_part_masuk.id_part = m_part.id_part 
				where m_part.id_part = '$id'
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
			<td style="text-align:center"><?php echo "$d1[qty]";?></b></td>
			<td style="text-align:left"><?php echo "$d1[unit]";?></b></td>
		</tr>

		
	<?php }
	$t_sisa = $t_tag - $t_bayar;
	?>
	
	
</table>	
