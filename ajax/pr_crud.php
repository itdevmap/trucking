<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses where id_role = '$id_role'  and id_menu ='18' ");
$rq=mysqli_fetch_array($pq);	
$m_edit = $rq['m_edit'];
$m_add = $rq['m_add'];
$m_del = $rq['m_del'];
$m_view = $rq['m_view'];
$m_exe = $rq['m_exe'];

if ($_GET['type'] == "Read")
{
	$cari = trim($_GET['cari']);
	$hal = $_GET['hal'];
	$paging = $_GET['paging'];
	$tgl1 = $_GET['tgl1'];
	$tgl2 = $_GET['tgl2'];
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);	
	$field = $_GET['field'];
	$stat = $_GET['stat'];
	
	if($stat == 'Belum Lunas')
	{
		$stat = '0';
	}else if($stat == 'Lunas')
	{
		$stat = '1';
	}
	
	if($field == 'No PR')
	{
		$f = 't_jo_pr.no_pr';
	}else if($field == 'No SJ'){
		$f = 't_jo_mobil.no_sj';		
	}else if($field == 'Vendor'){
		$f = 'm_vendor.nama_vendor';	
	}else{
		$f = 't_jo_pr.no_pr';
	}
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="7%" style="text-align: center;">TANGGAL</th>
					<th rowspan="2" width="7%" style="text-align: center;">NO PR</th>
					<th rowspan="2" width="4%" style="text-align: center;">JENIS</th>
					<th rowspan="2" width="7%" style="text-align: center;">NO SJ</th>
					<th rowspan="2" width="38%" style="text-align: center;">VENDOR</th>
					<th rowspan="2" width="7%" style="text-align: center;">TAGIHAN</th>
					<th rowspan="2" width="7%" style="text-align: center;">BAYAR</th>
					<th rowspan="2" width="7%" style="text-align: center;">SISA</th>
					<th rowspan="2" width="7%" style="text-align: center;">STATUS</th>
					<th colspan="4" width="6%" style="text-align: center;">ACTION</th>	
				</tr>
				<tr>	
					<th width="2%" style="text-align: center;">PRINT</th>
					<th width="2%" style="text-align: center;">BAYAR</th>		
					<th width="2%" style="text-align: center;">DETAIL</th>		
				</tr>
			</thead>';			
	if(!isset($_GET['hal'])){ 
		$page = 1;       
		} else { 
		$page = $_GET['hal']; 
		$posisi=0;
	}
	$jmlperhalaman = $paging;
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman); 
	
	
	if($stat == 'All')
	{
		$SQL = "select  t_jo_pr.*, m_vendor.nama_vendor, t_jo_mobil.no_sj from  
			   t_jo_pr left join m_vendor on   t_jo_pr.id_vendor = m_vendor.id_vendor 
			   left join t_jo_mobil on t_jo_pr.id_jo_mobil = t_jo_mobil.id_detil
			  where t_jo_pr.tgl_pr between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%'  
			  order by t_jo_pr.tgl_pr desc, t_jo_pr.no_pr desc
			  LIMIT $offset, $jmlperhalaman";
	}else{
		$SQL = "select  t_jo_pr.*, m_vendor.nama_vendor, t_jo_mobil.no_sj from  
			   t_jo_pr left join m_vendor on   t_jo_pr.id_vendor = m_vendor.id_vendor 
			   left join t_jo_mobil on t_jo_pr.id_jo_mobil = t_jo_mobil.id_detil
			  where t_jo_pr.tgl_pr between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%'  and t_jo_pr.status = '$stat'
			  order by t_jo_pr.tgl_pr desc, t_jo_pr.no_pr desc
			  LIMIT $offset, $jmlperhalaman";
	}
		
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$tanggal = ConverTgl($row['tgl_pr']);
			$sisa = $row['tagihan'] - $row['bayar'];
			$tagihan = number_format($row['tagihan'],0);
			$bayar = number_format($row['bayar'],0);
			$sisax = number_format($sisa,0);
			
			$t_tagihan = $t_tagihan + $row['tagihan'];
			$t_bayar = $t_bayar + $row['bayar'];
			if($row['jenis'] == '1')
			{
				$jenis = 'LCL';
			}else{
				$jenis = 'FCL';
			}				
			if($sisa <= 0)
			{
				if($row['tagihan'] > 0 )
				{
					$label = 'success';
					$status = 'Lunas';
					$s = '1';
				}else{
					$label = 'danger';
					$status = 'Belum Lunas';
					$s = '0';
				}
				
			}else{
				$label = 'danger';
				$status = 'Belum Lunas';
				$s = '0';
			}
			$posisi++;		
			
			$update = mysqli_query($koneksi, "update t_jo_pr set status = '$s' where id_pr = '$row[id_pr]'  ");
			
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>';
				$data .= '<td style="text-align:center">'.$tanggal.'</td>';	
				$data .= '<td style="text-align:center">'.$row['no_pr'].'</td>';	
				$data .= '<td style="text-align:center">'.$jenis.'</td>';					
				$data .= '<td style="text-align:center">'.$row['no_sj'].'</td>
				<td style="text-align:left">'.$row['nama_vendor'].'</td>	
				<td style="text-align:right;background:#e48f0f;color:#fff">'.$tagihan.'</td>
				<td style="text-align:right;background:#406a94;color:#fff">'.$bayar.'</td>
				<td style="text-align:right;background:#4bc343;color:#fff">'.$sisax.'</td>
				<td style="text-align:center">
					<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
				</td>';	
				
				$xy1="$row[id_pr]";
				$xy1=base64_encode($xy1);
				$link = "'cetak_pr.php?id=$xy1'";
				$data .= '<td>
					<button class="btn btn-block btn-default"  title="Print"
						style="margin:-3px;border-radius:0px" type="button" 									
						onClick="window.open('.$link.') ">
						<span class="fa fa-print " ></span>
					</button></td>';
								
				if($m_add == '1' && $s == '0' ){					
					$data .= '<td>
						<button class="btn btn-block btn-default" title="Add Payment"
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="javascript:GetPayment('.$row['id_pr'].')">
									<span class="fa  fa-plus-square" ></span>
						</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}	
				$data .='<td   style="text-align:center">
					<button class="btn btn-block btn-default" title="List Payment"
						style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
						onClick="javascript:ListPayment('.$row['id_pr'].')"  >
						<span class="glyphicon glyphicon-search" ></span>
					</button>	
					</td>';	
				
				$data .='</tr>';
    		$number++;
    	}		
		
		$t_tagihanx = number_format($t_tagihan,0);
		$t_bayarx = number_format($t_bayar,0);
		$t_sisa = $t_tagihan - $t_bayar;
		$t_sisax = number_format($t_sisa,0);
		$data .= '<tr>';
		$data .= '<td colspan= "6" style="text-align:right;background:#eaebec;color:#000"><b>Total  :&nbsp;&nbsp;&nbsp;</b></td>	
					<td style="text-align:right;background:#e48f0f;color:#fff"><b>'.$t_tagihanx.'</b></td>
					<td style="text-align:right;background:#406a94;color:#fff"><b>'.$t_bayarx.'</b></td>
					<td style="text-align:right;background:#4bc343;color:#fff"><b>'.$t_sisax.'</b></td>
					<td colspan= "5" style="text-align:right;background:#eaebec"</td>	';
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
    $data .= '</table>';
	
	$data .= '<div class="paginate paginate-dark wrapper">
				<ul>';
				
				if($stat == 'All')
				{
					$pq = mysqli_query($koneksi, "select  count(t_jo_pr.id_pr) as jml from  
					   t_jo_pr left join m_vendor on   t_jo_pr.id_vendor = m_vendor.id_vendor 
					   left join t_jo_mobil on t_jo_pr.id_jo_mobil = t_jo_mobil.id_detil
					  where t_jo_pr.tgl_pr between '$tgl1x' and '$tgl2x' 
					  and $f LIKE '%$cari%' ");
				}else{
					$pq = mysqli_query($koneksi, "select  count(t_jo_pr.id_pr) as jml from  
					   t_jo_pr left join m_vendor on   t_jo_pr.id_vendor = m_vendor.id_vendor 
					   left join t_jo_mobil on t_jo_pr.id_jo_mobil = t_jo_mobil.id_detil
					  where t_jo_pr.tgl_pr between '$tgl1x' and '$tgl2x' 
					  and $f LIKE '%$cari%'  and t_jo_pr.status = '$stat'");
				}
									
				$rq=mysqli_fetch_array($pq);
				$total_record = $rq['jml'];										
				$total_halaman = ceil($total_record / $jmlperhalaman);					
				if ($total_record > $jmlperhalaman){
					$perhal=4;
					if($hal > 1){ 
						$prev = ($page - 1); 
						$data .='<li><a href=# onclick="ReadData('.$prev.')">Prev</a></li> '; 
					}
					if($total_halaman<=$jmlperhalaman){
						$hal1=1;
						$hal2=$total_halaman;
						}else{
						$hal1=$hal-$perhal;
						$hal2=$hal+$perhal;
					}
					if($hal<=5){
						$hal1=1;
					} 
					if($hal<$total_halaman){
						$hal2=$hal+$perhal;
						}else{
						$hal2=$hal;
					}
					for($i = $hal1; $i <= $hal2; $i++){ 
						if(($hal) == $i){ 
							$data .='<li><a href="#" class="active">'.$i.'</a></li> '; 
							}else{ 
							if($i<=$total_halaman){
								$data .='<li><a href=# onclick="ReadData('.$i.')">'.$i.'</a></li> ';
							}
						} 
					}
					if($hal < $total_halaman){ 
						$next = ($page + 1); 
						$data .='<li><a href=# onclick="ReadData('.$next.')">Next</a></li> '; 
					} 
				}
				$data .= '</ul></div>';				
    echo $data;

	
}else if($_GET['type'] == "Read_PR")
{
	$id_jo = $_GET['id_jo'];
	$mode = $_GET['mode'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>					
					<th rowspan="2" width="7%" style="text-align: center;">TANGGAL</th>
					<th rowspan="2" width="6%" style="text-align: center;">NO. PR</th>		
					<th rowspan="2" width="7%" style="text-align: center;">NO. SJ</th>		
					<th rowspan="2" width="44%" style="text-align: center;">VENDOR</th>
					<th rowspan="2" width="6%" style="text-align: center;">TAGIHAN</th>	
					<th rowspan="2" width="6%" style="text-align: center;">BAYAR</th>	
					<th rowspan="2" width="6%" style="text-align: center;">SISA</th>					
					<th rowspan="2" width="7%" style="text-align: center;">STATUS</th>	
					<th colspan="3" width="6%" style="text-align: center;">ACTION</th>
				</tr>
				<tr>
					<th  width="2%" style="text-align: center;">EDIT</th>
					<th  width="2%" style="text-align: center;">DEL</th>
					<th  width="2%" style="text-align: center;">PRINT</th>	
				</tr>	
			</thead>';	
	
	$t1 = "select  t_jo_pr.*, m_vendor.nama_vendor, t_jo_mobil.no_sj from  
		   t_jo_pr left join m_vendor on   t_jo_pr.id_vendor = m_vendor.id_vendor 
		   left join t_jo_mobil on t_jo_pr.id_jo_mobil = t_jo_mobil.id_detil
		   where t_jo_mobil.id_jo = '$id_jo' order by t_jo_pr.id_pr";
	$h1 = mysqli_query($koneksi, $t1);       
	while ($d1=mysqli_fetch_array($h1)){
		$n++;
		$tanggal = ConverTgl($d1['tgl_pr']);
		$sisa = $d1['tagihan'] - $d1['bayar'];
		$tagihanx = number_format($d1['tagihan'],0);
		$bayarx = number_format($d1['bayar'],0);
		$sisax = number_format($sisa,0);
		if($d1['status'] == '1')
		{
			$label = 'success';
			$status = 'Lunas';
		}else if($d1['status'] == '0'){
			$label = 'danger';
			$status = 'Belum Lunas';
		}	
		$data .= '<tr>';							
			$data .= '<td style="text-align:center">'.$n.'.</td>';			
			$data .= '<td style="text-align:center">'.$tanggal.'</td>';
			$data .= '<td style="text-align:center">'.$d1['no_pr'].'</td>';
			$data .= '<td style="text-align:center">'.$d1['no_sj'].'</td>';
			$data .= '<td style="text-align:left">'.$d1['nama_vendor'].'</td>';
			$data .= '<td style="text-align:right;background:#e48f0f;color:#fff">'.$tagihanx.'</td>';
			$data .= '<td style="text-align:right;background:#406a94;color:#fff">'.$bayarx.'</td>';
			$data .= '<td style="text-align:right;background:#4bc343;color:#fff">'.$sisax.'</td>';
			$data .= '<td style="text-align:center">
				<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
				</td>';
			
			if($mode == 'Edit' && $d1['bayar'] <= '0' ){
				$xy1="Edit|$id_jo|$d1[id_pr]";
				$xy1=base64_encode($xy1);
				if($d1['jenis'] == '1')
				{
					$link = "'pr_data_ship.php?id=$xy1'";
				}else{
					$link = "'pr_data.php?id=$xy1'";
				}
				
				$data .= '<td>
						<button class="btn btn-block btn-default"  title="Edit"
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="window.location.href = '.$link.' "  >
						<span class="fa fa-edit " ></span>
						</button></td>';
						
				$data .= '<td>
							<button class="btn btn-block btn-default"  title="Delete"
							style="margin:-3px;border-radius:0px" type="button" 
							onClick="javascript:Delete('.$d1['id_pr'].')"  >
							<span class="fa fa-close " ></span>
						</button></td>';		
			}
			else
			{
				$data .='<td></td>';
				$data .='<td></td>';
			}	
			
			$xy1="$d1[id_pr]";
				$xy1=base64_encode($xy1);
				$link = "'cetak_pr.php?id=$xy1'";
				$data .= '<td>
					<button class="btn btn-block btn-default"  title="Print"
						style="margin:-3px;border-radius:0px" type="button" 									
						onClick="window.open('.$link.') ">
						<span class="fa fa-print " ></span>
					</button></td>';
					
		$data .= '</tr>';	
	}		
    $data .= '</table>';
    echo $data;	

}else if ($_POST['type'] == "Del_PR"){
	$id = $_POST['id']; 	
	
	$pq = mysqli_query($koneksi, "select * from t_jo_pr where id_pr = '$id' ");
	$rq = mysqli_fetch_array($pq);		
	Del_Jurnal($rq['id_jurnal']);
	
	$del = mysqli_query($koneksi, "delete from t_jo_pr_detil where id_pr = '$id' ");
    $query = "DELETE FROM t_jo_pr WHERE id_pr = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }	
	
}else if($_GET['type'] == "ReadDetil")
{
	$id_pr = $_GET['id_pr'];
	$mode = $_GET['mode'];
	
	$pq = mysqli_query($koneksi, "select t_jo_pr.*, m_vendor.nama_vendor, t_jo_mobil.no_sj, t_jo_mobil.rate_vendor, t_jo.no_jo
			from 
			t_jo_pr left join m_vendor on  t_jo_pr.id_vendor = m_vendor.id_vendor 
			left join t_jo_mobil on t_jo_pr.id_jo_mobil = t_jo_mobil.id_detil
		   left join t_jo on t_jo_mobil.id_jo = t_jo.id_jo
		   where t_jo_pr.id_pr = '$id_pr'  ");
	$rq=mysqli_fetch_array($pq);	
	$no_sj = $rq['no_sj'];
	$id_jo_mobil = $rq['id_jo_mobil'];
	$no_jo = $rq['no_jo'];
	$id_jurnal = $rq['id_jurnal'];
	$nama_vendor = $rq['nama_vendor'];
	$tgl_pr = ConverTgl($rq['tgl_pr']);
	$ppn = $rq['ppn'];
	$no_pr = $rq['no_pr'];
	$rate_vendor_lama = $rq['rate_vendor'];
	Del_Jurnal($id_jurnal);
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="5%" style="text-align: center;">NO</th>
					<th rowspan="2" width="51%" style="text-align: center;">KETERANGAN</th>
					<th rowspan="2" width="8%" style="text-align: center;">QTY</th>
					<th rowspan="2" width="10%" style="text-align: center;">UNIT</th>
					<th rowspan="2" width="10%" style="text-align: center;">HARGA</th>
					<th rowspan="2" width="12%" style="text-align: center;">JUMLAH</th>
					<th colspan="2" width="4%" style="text-align: center;">ACTION</th>						
				</tr>
				<tr>
					<th width="2%" style="text-align: center;">EDIT</th>
					<th width="2%" style="text-align: center;">DEL</th>
				</tr>	
			</thead>';	
	$SQL = "select  t_jo_pr_detil.*,m_cost.nama_cost from 
			 t_jo_pr_detil inner join m_cost on  t_jo_pr_detil.id_cost = m_cost.id_cost 
			where  t_jo_pr_detil.id_pr = '$id_pr' order by  t_jo_pr_detil.id_detil";
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
		$idr = 0;
		$usd =0;
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;				
			$jumlah = $row['qty'] * $row['harga'];	
			$total = $total + $jumlah;
			$jumlahx = number_format($jumlah,0);
			$price = number_format($row['harga'],0);			
			$data .= '<tr>						
				<td style="text-align:center">'.$posisi.'.</td>
				<td style="text-align:left">'.$row['nama_cost'].'</td>	
				<td style="text-align:center">'.$row['qty'].'</td>
				<td style="text-align:center">'.$row['unit'].'</td>
				<td style="text-align:right">'.$price.'</td>
				<td style="text-align:right">'.$jumlahx.'</td>';
				
				if($mode == 'Edit' ){
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Edit"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:GetData('.$row['id_detil'].')"  >
									<span class="fa fa-edit " ></span>
								</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}
				if($mode == 'Edit'){
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Delete"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:DelData('.$row['id_detil'].')"  >
									<span class="fa fa-close " ></span>
								</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}	
				$data .='</tr>';
    		$number++;
    	}		
		
		if($ppn > 0)
		{
			$jumlah1 = $total;
			$totalx = number_format($total,0);
			$data .= '<td></td>';
			$data .= '<td></td>';
			$data .= '<td colspan="3" style="text-align:right;background:#eee;color:#000">Total Amount  :</td>	
						<td style="text-align:right;background:#eee;color:#000">'.$totalx.'</td>';			
			$data .= '<td></td>';
			$data .= '<td></td>';
			$data .= '</tr>';
		
			$nilai_ppn = ($ppn/100) * $total;
			$nilai_ppnx = number_format($nilai_ppn,0);
			$data .= '<td></td>';
			$data .= '<td></td>';
			$data .= '<td colspan="3" style="text-align:right;background:#eee;color:#000">PPN ('.$ppn.'%)  :</td>	
						<td style="text-align:right;background:#eee;color:#000">'.$nilai_ppnx.'</td>';			
			$data .= '<td></td>';
			$data .= '<td></td>';
			$data .= '</tr>';
			
			$total = $total + $nilai_ppn;
			$totalx = number_format($total,0);
			$data .= '<td></td>';
			$data .= '<td></td>';
			$data .= '<td colspan="3" style="text-align:right;background:#eee;color:#000"><b>Total  :</b></td>	
						<td style="text-align:right;background:#008d4c;color:#fff"><b>'.$totalx.'</b></td>';			
			$data .= '<td></td>';
			$data .= '<td></td>';
			$data .= '</tr>';
		}else{
			$jumlah1 = $total;
			$totalx = number_format($total,0);
			$data .= '<td colspan="3"></td>';
			$data .= '<td colspan="2" style="text-align:right;background:#eee;color:#000"><b>Total  :</b></td>	
						<td style="text-align:right;background:#008d4c;color:#fff"><b>'.$totalx.'</b></td>';
			$data .= '<td colspan="2"></td>';			
			$data .= '</tr>';
		}
		
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }	
	
	$sql = "UPDATE t_jo_pr set tagihan = '$total'	where id_pr = '$id_pr' ";
	$hasil = mysqli_query($koneksi, $sql);

	
	
    $data .= '</table>';
	
	if($total > 0)
	{		
		$tgl_jurnal =  $tgl_pr;
		$ket = "PR $nama_vendor ($no_pr)";
		$jumlah  = $total;		
		$id_coa1 = $s_hpp_fcl;
		$jumlah1 = $jumlah1;		
		$jumlah2  = $total;
		$id_coa2 = $s_hutang_vendor;	
		if(!empty($ppn))
		{
			$id_coa3 = $s_ppn_masukan;
			$jumlah3 = $nilai_ppn;
		}
		$jenis = '1';
		$id_jurnal = Add_Jurnal($tgl_jurnal,$jenis,$ket,$cur,$kurs,$jumlah,
						$id_coa1,$jumlah1,$id_coa2,$jumlah2,$id_coa3,$jumlah3,$id_coa4,$jumlah4,$id_coa5,$jumlah5,
						$id_coa6,$jumlah6,$id_coa7,$jumlah7,$id_coa8,$jumlah8, $id_user );
		$sql="UPDATE t_jo_pr set  id_jurnal = '$id_jurnal' where id_pr = '$id_pr'  "; 
		$hasil=mysqli_query($koneksi, $sql);	
	}
	
	
    echo $data;	
	
	
}else if ($_POST['type'] == "Detil_Data"){
	$id = $_POST['id'];	
    $query = "select  t_jo_pr_detil.*,m_cost.nama_cost from 
			 t_jo_pr_detil inner join m_cost on  t_jo_pr_detil.id_cost = m_cost.id_cost  
			where t_jo_pr_detil.id_detil  = '$id'";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }
    $response = array();
    if(mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $response = $row;
        }
    }
    else
    {
        $response['status'] = 200;
        $response['message'] = "Data not found!";
    }
    echo json_encode($response);
	
}else if ($_POST['type'] == "Del_Data"){
	$id = $_POST['id']; 
	$jo_no = $_POST['jo_no']; 	
	
    $query = "DELETE FROM t_jo_pr_detil WHERE id_detil = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }
	
}else if ($_POST['type'] == "Add_Data"){		
	if($_POST['mode'] != '' )
	{	
		$id = $_POST['id'];
		$id_cost = $_POST['id_cost'];
		$id_pr = $_POST['id_pr'];
		$id_jo_mobil = $_POST['id_jo_mobil'];
		$qty = $_POST['qty'];
		$unit = $_POST['unit'];
		$price = $_POST['price'];
		$mode = $_POST['mode'];	
		$qty = str_replace(",","", $qty);
		$price = str_replace(",","", $price);
		
		if($mode == 'Add')
		{			
			$sql = "INSERT INTO t_jo_pr_detil (id_pr, id_cost, qty, unit, harga) values
					('$id_pr', '$id_cost','$qty','$unit','$price')";
			$hasil= mysqli_query($koneksi, $sql);
		}
		else
		{
			$sql = "update t_jo_pr_detil set 
					id_cost = '$id_cost',
					unit = '$unit',
					qty = '$qty',
					harga = '$price'
					where id_detil = '$id'	";
			$hasil=mysqli_query($koneksi,$sql);
		}
		if (!$hasil) {
	       
			echo "Data Error...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}		
	
	
}else if ($_POST['type'] == "Detil_PR"){
	$id = $_POST['id'];	
    $query = "select t_jo_pr.*, m_vendor.nama_vendor from
				t_jo_pr inner join m_vendor on  t_jo_pr.id_vendor = m_vendor.id_vendor 
			    where t_jo_pr.id_pr = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }
    $response = array();
    if(mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $response = $row;
        }
    }
    else
    {
        $response['status'] = 200;
        $response['message'] = "Data not found!";
    }
    echo json_encode($response);
	
	
}else if ($_POST['type'] == "AddPayment"){		
	if($_POST['id_pr'] != '' )
	{		
		$id_pr = $_POST['id_pr'];
		$tanggal = $_POST['tanggal'];
		$jumlah = $_POST['jumlah'];
		$pph = $_POST['pph'];
		$id_bank = $_POST['id_bank'];
		$no_pr = $_POST['no_pr'];
		$nama = $_POST['nama'];
		
		$jumlah = str_replace(",","", $jumlah);
		$jumlah_bayar = str_replace(",","", $jumlah);
		$pph = str_replace(",","", $pph);
		$tanggalx = ConverTglSql($tanggal);	
	
		//JURNAL BAYAR
		$jenis_jurnal = '1';
		$tgl_jurnal = $tanggal;		
		$ket = "PEMBAYARAN PR $nama ($no_pr)";	
		
		$id_coa1 = $s_hutang_vendor;
		$jumlah1 = $jumlah_bayar + $pph;
		
		$id_coa2 = $id_bank ;
		$jumlah2 = $jumlah_bayar;	
		
		if($pph > 0)
		{
			$id_coa4 = $pph23_keluaran;
			$jumlah4 = $pph;
			$id_bank4 =0;
		}
		
				
		$cur = 'IDR';
		$jumlah = $jumlah_bayar + $pph ;		
		$jenis = '1';
		$kurs = $rate;
		$id_jurnal = Add_Jurnal($tgl_jurnal,$jenis,$ket,$cur,$kurs,$jumlah,
						$id_coa1,$jumlah1,$id_coa2,$jumlah2,$id_coa3,$jumlah3,$id_coa4,$jumlah4,$id_coa5,$jumlah5,
						$id_coa6,$jumlah6,$id_coa7,$jumlah7,$id_coa8,$jumlah8, $id_user );
	
		$sql = "INSERT INTO t_jo_pr_bayar (id_pr, id_jurnal, bayar, pph, id_bank) 
		    values ('$id_pr','$id_jurnal','$jumlah_bayar','$pph','$id_bank')";
		$hasil=mysqli_query($koneksi, $sql);
	
		$bayar = $jumlah_bayar + $pph;
		$sql = "UPDATE t_jo_pr set bayar =  bayar + '$bayar' where id_pr ='$id_pr' ";
		$hasil = mysqli_query($koneksi, $sql);
		
		if (!$hasil) {
	        			
			exit(mysqli_error());
			echo "Data Invalid...!";
	    }
		else
		{	
			echo "Data Saved...!";
		}
	}	

}else if($_GET['type'] == "ListPayment")
{
	$id = $_GET['id'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="12%" style="text-align: center;">DATE</th>
					<th rowspan="2" width="9%" style="text-align: center;">NO. JURNAL</th>
					<th rowspan="2" width="27%" style="text-align: center;">CASH/BANK</th>		
					<th rowspan="2" width="12%" style="text-align: center;">BAYAR</th>	
					<th rowspan="2" width="12%" style="text-align: center;">PPH23</th>
					<th rowspan="2" width="13%" style="text-align: center;">JUMLAH</th>	
					<th rowspan="2" width="10%" style="text-align: center;">CREATED</th>
					<th rowspan="2" width="2%" style="text-align: center;">DEL</th>				
				</tr>
			</thead>';
	$t1="select t_jo_pr_bayar.*, t_jurnal.id_user, t_jurnal.jumlah, t_jurnal.tgl_jurnal, t_jurnal.no_jurnal,m_bank.nama_bank, m_bank.no_bank from
	        t_jo_pr_bayar inner join t_jurnal on t_jo_pr_bayar.id_jurnal = t_jurnal.id_jurnal
			left join m_bank on t_jo_pr_bayar.id_bank = m_bank.id_bank
			where t_jo_pr_bayar.id_pr = '$id' order by t_jurnal.tgl_jurnal";
	$h1=mysqli_query($koneksi, $t1);   
	while ($d1=mysqli_fetch_array($h1))		
	{
		$tanggal=ConverTgl($d1['tgl_jurnal']);	
		
		$bayar = number_format($d1['bayar'],0);
		$pph23 = number_format($d1['pph'],0);		
		$amount = $d1['bayar'] + $d1['pph'];
		$amountx = number_format($amount,0);
		$total = $total + $amount;
		$n++;
		$data .= '<tr>							
			<td style="text-align:center">'.$n.'.</td>
			<td style="text-align:center">'.$tanggal.'</td>	
			<td style="text-align:center">'.$d1['no_jurnal'].'</td>	
			<td style="text-align:left">'.$d1['nama_bank'].' ('.$d1['no_bank'].')</td>	
			<td style="text-align:right">'.$bayar.'</td>	
			<td style="text-align:right">'.$pph23.'</td>
			<td style="text-align:right">'.$amountx.'</td> 
			<td style="text-align:center">'.$d1['id_user'].'</td>';	
		
			if($m_del == '1' ){
				$data .= '<td>
					<button class="btn btn-block btn-default"  title="Delete"
					style="margin:-3px;border-radius:0px" type="button" 
					onClick="javascript:DelPayment('.$d1['id_bayar'].')"  >
		     		<span class="fa fa-close " ></span>
					</button></td>';
			}
			else
			{
				$data .='<td></td>';
			}				
		$data .='</tr>';
	}
	$totalx = number_format($total,0);
	$data .= '<tr>							
			<td colspan = "4" style="text-align:center;background:#eaebec"></td>	
			<td colspan= "2" style="text-align:right;background:#406a94;color:#fff">Total Pembayaran</td> 
			<td style="text-align:right;background:#406a94;color:#fff">'.$totalx.'</td>';	
	$data .='</tr>';
	$pq = mysqli_query($koneksi, "select * from t_jo_pr where id_pr = '$id'  ");
	$rq=mysqli_fetch_array($pq);	
	$tagihanx = number_format($rq['tagihan'],0);
	$sisa = $rq['tagihan'] - $total;
	$data .= '<tr>							
			<td colspan = "4" style="text-align:center;background:#eaebec"></td>	
			<td colspan= "2" style="text-align:right;background:#e48f0f;color:#fff">Tagihan</td> 
			<td style="text-align:right;background:#e48f0f;color:#fff">'.$tagihanx.'</td>';	
	$data .='</tr>';
	$sisax = number_format($sisa,0);
	$data .= '<tr>							
			<td colspan = "4" style="text-align:center;background:#eaebec"></td>	
			<td colspan= "2" style="text-align:right;background:#4bc343;color:#fff"><b>Sisa</b></td> 
			<td style="text-align:right;background:#4bc343;color:#fff"><b>'.$sisax.'</b></td>';	
	$data .='</tr>';
    $data .= '</table>';
    echo $data;	

}else if ($_POST['type'] == "DelPayment"){
	$id = $_POST['id']; 
	$pq = mysqli_query($koneksi, "select * from t_jo_pr_bayar where id_bayar = '$id'  ");
	$rq=mysqli_fetch_array($pq);
	$id_jurnal = $rq['id_jurnal'];
	Del_Jurnal($id_jurnal);
	$jumlah = $rq['bayar'] + $rq['pph'];	
	
	$update = mysqli_query($koneksi, "UPDATE t_jo_pr set bayar =  bayar - '$jumlah'  where id_pr = '$rq[id_pr]' ");
    $query = "DELETE FROM  t_jo_pr_bayar WHERE id_bayar = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysql_error());
    }		
	
}

?>