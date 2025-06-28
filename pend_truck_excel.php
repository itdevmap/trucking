<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=TRUCK.xls");

$idx = $_GET['id'];	
$tahun=base64_decode($idx);

$pq = mysqli_query($koneksi, "select count(id_sales) as jml from  m_sales_tr ");					
$rq=mysqli_fetch_array($pq);
$jml_sales = $rq['jml'] + 1;
$lebar = 67/$jml_sales;

	
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">PENDAPATAN TRUCK </th>
	</tr>
	<tr>
		<th style="font-size:12; width:90px;text-align:left">Tahun</th>
		<th colspan="7" style="font-size:12; text-align:left">: <?php echo "$tahun"; ?></th>
	</tr>
	<tr>
		<th style="font-size:12; width:90px;text-align:left"></th>
		<th colspan="7" style="font-size:12; text-align:left"></th>
	</tr>
</table>


<table border="1">
	
	<tr>
		<th style="font-size:12; width:90px;text-align:center">NO</th>
		<th style="font-size:12; width:600px;text-align:center">NO. POLISI</th>
		<th style="font-size:12; width:600px;text-align:center">PENDAPATAN</th>
		<th style="font-size:12; width:90px;text-align:center">BIAYA</th>
		<th style="font-size:12; width:90px;text-align:center">MARGIN</th>
	</tr>
	
	<?php
	$t1 = "select * from m_mobil_tr order by no_polisi";	

	$h1=mysqli_query($koneksi, $t1);       
	while ($d1=mysqli_fetch_array($h1))
	{
		$n++;
		$pq = mysqli_query($koneksi, "select sum(tagihan) as pend, sum(uj+uj_lain+ritase) as biaya 
					  from  
					  tr_jo  where id_mobil = '$d1[id_mobil]' and status = '1' 
					  and year(tgl_jo) = '$tahun' ");					
		$rq=mysqli_fetch_array($pq);
		$pend = $rq['pend'] ;
		$biaya = $rq['biaya'] ;
		$margin = $pend - $biaya;
		
	?>
	
		<tr>
			<td style="text-align:center"><?php echo "$n.";?></b></td>
			<td style="text-align:center"><?php echo "$d1[no_polisi]";?></b></td>
			<td style="text-align:right"><?php echo "$pend";?></b></td>
			<td style="text-align:right"><?php echo "$biaya";?></b></td>
			<td style="text-align:right"><?php echo "$margin";?></b></td>
		</tr>

		
	<?php }
	$t_sisa = $t_tag - $t_bayar;
	?>
		
</table>	
