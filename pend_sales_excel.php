<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=SALES.xls");

$idx = $_GET['id'];	
$tahun=base64_decode($idx);

$pq = mysqli_query($koneksi, "select count(id_sales) as jml from  m_sales_tr ");					
$rq=mysqli_fetch_array($pq);
$jml_sales = $rq['jml'] + 1;
$lebar = 67/$jml_sales;

	
?>

<table border="0">
	<tr>
		<th colspan="7" style="font-size:24;text-align:left">PENDAPATAN SALES </th>
	</tr>

	<tr>
		<th style="font-size:12; width:90px;text-align:left"></th>
		<th colspan="7" style="font-size:12; text-align:left"></th>
	</tr>
</table>


<table border="1">
	
	<tr>
		<th rowspan="2" style="font-size:12; width:90px;text-align:center"><?php echo $tahun;?></th>
		<th colspan="<?php echo $jml_sales;?>" style="font-size:12; width:90px;text-align:center">TOTAL REVENUE</th>
		<th rowspan="2" style="font-size:12; width:600px;text-align:center">TOTAL PENDAPATAN</th>
		<th rowspan="2" style="font-size:12; width:600px;text-align:center">TOTAL BIAYA</th>
		<th rowspan="2" style="font-size:12; width:90px;text-align:center">RATIO</th>
	</tr>
	<tr>
		<?php
		$t1="select * from m_sales_tr  order by nama";
		$h1=mysqli_query($koneksi, $t1);       
		while ($d1=mysqli_fetch_array($h1)){
		?>
		<th style="font-size:12; width:90px;text-align:center"><?php echo $d1['nama'];?></th>
		<?php } ?>
		<th style="font-size:12; width:90px;text-align:center">INTERCOMPANY</th>
	</tr>	
	<?php
	for ($x = 1; $x <= 12; $x++) {
		if(strlen($x) == 1)
		{
			$bln = "0$x";
		}else{
			$bln = $x;
		}
		if($bln == '01'){
			$bulan = 'Januari';
		}else if($bln == '02'){
			$bulan = 'Februari';
		}else if($bln == '03'){
			$bulan = 'Maret';
		}else if($bln == '04'){
			$bulan = 'April';
		}else if($bln == '05'){
			$bulan = 'Mei';
		}else if($bln == '06'){
			$bulan = 'Juni';
		}else if($bln == '07'){
			$bulan = 'Juli';
		}else if($bln == '08'){
			$bulan = 'Agustus';
		}else if($bln == '09'){
			$bulan = 'September';
		}else if($bln == '10'){
			$bulan = 'Oktober';
		}else if($bln == '11'){
			$bulan = 'November';
		}else if($bln == '12'){
			$bulan = 'Desember';
		}
	?>	
		<tr>
			<td style="text-align:left;font-size:12;"><?php echo "$bulan";?></b></td>
			<?php
			$total_pend = 0;
			$t1="select * from m_sales_tr  order by nama";
			$h1=mysqli_query($koneksi, $t1);       
			while ($d1=mysqli_fetch_array($h1)){  
				$pq = mysqli_query($koneksi, "select sum(tr_jo.tagihan) as jml 
					  from  
					  tr_jo left join tr_quo_data on tr_jo.id_detil_quo = tr_quo_data.id_detil
					  left join tr_quo on tr_quo_data.id_quo = tr_quo.id_quo
					  where tr_quo.sales = '$d1[nama]' and tr_jo.status = '1' 
					  and year(tr_jo.tgl_jo) = '$tahun' and month(tr_jo.tgl_jo) = '$bln'");					
				$rq=mysqli_fetch_array($pq);
				$pend = $rq['jml'] ;	
				$total_pend = $total_pend + $rq['jml'];
			?>
			<td style="text-align:right;font-size:12;"><?php echo "$pend";?></b></td>
			<?php } ?>
			<?php
			$pq = mysqli_query($koneksi, "select sum(tagihan) as jml 
					  from  
					  tr_jo  where id_detil_quo = '0' and status = '1' 
					  and year(tgl_jo) = '$tahun' and month(tgl_jo) = '$bln'");					
			$rq=mysqli_fetch_array($pq);
			$pend_int = $rq['jml'] ;	
			$total_pend = $total_pend + $rq['jml'];
			?>
			<td style="text-align:right;font-size:12;"><?php echo "$pend_int";?></b></td>
			<td style="text-align:right;font-size:12;"><?php echo "$total_pend";?></b></td>
			
			<?php
			$pq = mysqli_query($koneksi, "select sum(uj+uj_lain+ritase) as jml 
					  from  
					  tr_jo  where status = '1' 
					  and year(tgl_jo) = '$tahun' and month(tgl_jo) = '$bln'");					
			$rq=mysqli_fetch_array($pq);
			$biaya = $rq['jml'] ;
			if($biaya != 0)
			{
				$ratio = $biaya / $total_pend;
			}else{
				$ratio =0;
			}
			?>
			<td style="text-align:right;font-size:12;"><?php echo "$biaya";?></b></td>
			<td style="text-align:right;font-size:12;"><?php echo "$ratio";?></b></td>
		</tr>	
		
	<?php } ?>
	
		
</table>	
