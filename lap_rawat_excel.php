<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=PERAWATAN.xls");

$idx = $_GET['id'];	
$tahun=base64_decode($idx);



	
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">PERAWATAN TRUCK</th>
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
		<th style="font-size:12; width:600px;text-align:center">JAN</th>
		<th style="font-size:12; width:90px;text-align:center">FEB</th>
		<th style="font-size:12; width:90px;text-align:center">MAR</th>
		<th style="font-size:12; width:90px;text-align:center">APR</th>
		<th style="font-size:12; width:90px;text-align:center">MEI</th>
		<th style="font-size:12; width:90px;text-align:center">JUN</th>
		<th style="font-size:12; width:90px;text-align:center">JUL</th>
		<th style="font-size:12; width:90px;text-align:center">AUG</th>
		<th style="font-size:12; width:90px;text-align:center">SEP</th>
		<th style="font-size:12; width:90px;text-align:center">OKT</th>
		<th style="font-size:12; width:90px;text-align:center">NOV</th>
		<th style="font-size:12; width:90px;text-align:center">DES</th>
		<th style="font-size:12; width:90px;text-align:center">TOTAL</th>
	</tr>
	
	<?php
	$t1 = "select * from m_mobil_tr order by no_polisi";	

	$h1=mysqli_query($koneksi, $t1);       
	while ($d1=mysqli_fetch_array($h1))
	{
		$n++;
		
		
	?>
	
		<tr>
			<td style="text-align:center"><?php echo "$n.";?></b></td>
			<td style="text-align:left"><?php echo "$d1[no_polisi]";?></b></td>
			
			<?php
			$total = 0;
			for ($x = 1; $x <= 12; $x++) {
				if(strlen($x) == 1)
				{
					$bln = "0$x";
				}else{
					$bln = $x;
				}
				
				$pq = mysqli_query($koneksi, "select count(id_spk) as jml 
					  from  
					  t_spk  where  year(tanggal) = '$tahun' and month(tanggal) = '$bln' and id_mobil = '$d1[id_mobil]' ");					
				$rq=mysqli_fetch_array($pq);
				$pend = $rq['jml'] ;
				$total = $total + $pend;
			?>
			<td style="text-align:right"><?php echo "$pend";?></b></td>
			<?php } ?>
			
			<td style="text-align:right"><?php echo "$total";?></b></td>
		</tr>

		
	<?php }
	$t_sisa = $t_tag - $t_bayar;
	?>
		
</table>	
