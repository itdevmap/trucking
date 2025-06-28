<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses where id_role = '$id_role'  and id_menu ='17' ");
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
	
	if($field == 'No Inv')
	{
		$f = 't_inv.no_inv';
	}else if($field == 'Customer'){
		$f = 'm_cust.nama_cust';	
	}else if($field == 'Jenis'){
		$f = 't_inv.jenis';	
	}else{
		$f = 't_inv.no_inv';
	}
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="6%" style="text-align: center;">TANGGAL</th>
					<th rowspan="2" width="8%" style="text-align: center;">NO. INV</th>
					<th rowspan="2" width="3%" style="text-align: center;">JENIS</th>
					<th rowspan="2" width="36%" style="text-align: center;">CUSTOMER</th>
					<th rowspan="2" width="7%" style="text-align: center;">DUE DATE</th>
					<th rowspan="2" width="7%" style="text-align: center;">TAGIHAN</th>
					<th rowspan="2" width="7%" style="text-align: center;">BAYAR</th>
					<th rowspan="2" width="7%" style="text-align: center;">SISA</th>
					<th rowspan="2" width="6%" style="text-align: center;">STATUS</th>
					<th colspan="5" width="10%" style="text-align: center;">ACTION</th>	
				</tr>
				<tr>	
					<th width="2%" style="text-align: center;">EDIT</th>
					<th width="2%" style="text-align: center;">DEL</th>
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
		$SQL = "select t_inv.*, m_cust.nama_cust  from 
			  t_inv inner join m_cust on  t_inv.id_cust = m_cust.id_cust
			  where  t_inv.tgl_inv between '$tgl1x' and '$tgl2x' and
			  $f LIKE '%$cari%' order by t_inv.tgl_inv desc
			  LIMIT $offset, $jmlperhalaman";
	}else{
		$SQL = "select t_inv.*, m_cust.nama_cust  from 
			  t_inv inner join m_cust on  t_inv.id_cust = m_cust.id_cust
			  where  t_inv.tgl_inv between '$tgl1x' and '$tgl2x' and
			  $f LIKE '%$cari%' and t_inv.status = '$stat' order by t_inv.tgl_inv desc
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
			$tanggal = ConverTgl($row['tgl_inv']);
			$due_date = ConverTgl($row['due_date']);
			$duey = strtotime($row['due_date']);
			$tgl_sekarang = date('Y-m-d');
			$tgl_sekarang = strtotime($tgl_sekarang);
			$aging = $tgl_sekarang - $duey; 
			$aging = ($aging/24/60/60);
			$aging = round($aging);
			if($aging > 0 )
			{
				$label_aging = 'danger';
			}else{
				$label_aging = 'success';
			}
			$sisa = $row['tagihan'] - $row['bayar'];
			$tagihan = number_format($row['tagihan'],0);
			$bayar = number_format($row['bayar'],0);
			$sisax = number_format($sisa,0);
			
			$t_tagihan = $t_tagihan + $row['tagihan'];
			$t_bayar = $t_bayar + $row['bayar'];
			
			if($sisax <= 0)
			{
				if($row['tagihan'] > 0 )
				{
					$label = 'success';
					$status = 'Lunas';
					$s = '1';
					
					$update = mysqli_query($koneksi, "update t_jo set status = '2' where id_inv = '$row[id_inv]'  ");
					$update = mysqli_query($koneksi, "update t_jo_mobil set status = '2' where id_inv = '$row[id_inv]'  ");
				}else{
					$label = 'danger';
					$status = 'Belum Lunas';
					$s = '0';
					$update = mysqli_query($koneksi, "update t_jo set status = '1' where id_inv = '$row[id_inv]'  ");
					$update = mysqli_query($koneksi, "update t_jo_mobil set status = '1' where id_inv = '$row[id_inv]'  ");
				}
				
			}else{
				$label = 'danger';
				$status = 'Belum Lunas';
				$s = '0';
				$update = mysqli_query($koneksi, "update t_jo set status = '1' where id_inv = '$row[id_inv]'  ");
				$update = mysqli_query($koneksi, "update t_jo_mobil set status = '1' where id_inv = '$row[id_inv]'  ");
			}
			$posisi++;		
			
			$update = mysqli_query($koneksi, "update t_inv set status = '$s' where id_inv = '$row[id_inv]'  ");
			
		
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>';	
				$data .= '<td style="text-align:center">'.$tanggal.'</td>';
				$data .='<td style="text-align:center">'.$row['no_inv'].'</td>';
				$data .='<td style="text-align:center">'.$row['jenis'].'</td>';
				$data .= '<td style="text-align:left">'.$row['nama_cust'].'</td>
				<td style="text-align:left">
					<button type="button" class="btn btn-'.$label_aging.'" style="width:100%;padding:1px;margin:-3px">'.$due_date.'</button>
				</td>
				<td style="text-align:right;background:#e48f0f;color:#fff">'.$tagihan.'</td>
				<td style="text-align:right;background:#406a94;color:#fff">'.$bayar.'</td>
				<td style="text-align:right;background:#4bc343;color:#fff">'.$sisax.'</td>
				<td style="text-align:center">
					<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
				</td>';	
				if($m_edit == '1' && $row['bayar'] <= 0) {
					$xy1="Edit|$row[id_inv]";
					$xy1=base64_encode($xy1);
					$link = "'inv_data.php?id=$xy1'";
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="window.location.href = '.$link.' "  >
									<span class="fa fa-edit " ></span>
								</button></td>';
				}
				else
				{					
						$data .='<td></td>';
				}
				if($m_del == '1' && $row['bayar'] <= '0' ) 	
				{
					$data .= '<td>
							<button class="btn btn-block btn-default"  title="Delete"
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:Delete('.$row['id_inv'].')"  >
								<span class="fa fa-close " ></span>
								</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}
				
				$xy1="$row[id_inv]";
				$xy1=base64_encode($xy1);
				if($row['jenis'] == 'FCL')
				{
					$link = "'cetak_inv_fcl.php?id=$xy1'";
				}else{
					$link = "'cetak_inv_lcl.php?id=$xy1'";
				}					
				
				$data .= '<td>
					<button class="btn btn-block btn-default"  title="Print"
						style="margin:-3px;border-radius:0px" type="button" 									
						onClick="window.open('.$link.') ">
						<span class="fa fa-print " ></span>
					</button></td>';
			
				
				if($m_add == '1' && $s == '0' ){					
					$data .= '<td>
						<button class="btn btn-block btn-default" title="Add Pembayaran"
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="javascript:GetPayment('.$row['id_inv'].')">
									<span class="fa  fa-plus-square" ></span>
						</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}	
				$data .='<td   style="text-align:center">
					<button class="btn btn-block btn-default" title="List Pembayaran"
						style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
						onClick="javascript:ListPayment('.$row['id_inv'].')"  >
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
					$pq = mysqli_query($koneksi, "select count(t_inv.id_inv) as jml  from 
					  t_inv inner join m_cust on  t_inv.id_cust = m_cust.id_cust
					  where  t_inv.tgl_inv between '$tgl1x' and '$tgl2x' and
					  $f LIKE '%$cari%' ");
				}else{
					$pq = mysqli_query($koneksi, "select count(t_inv.id_inv) as jml  from 
					  t_inv inner join m_cust on  t_inv.id_cust = m_cust.id_cust
					  where  t_inv.tgl_inv between '$tgl1x' and '$tgl2x' and
					  $f LIKE '%$cari%' and t_inv.status = '$stat' ");
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
	
	
}else if ($_POST['type'] == "Detil_Inv"){
	$id = $_POST['id'];	
    $query = "select t_inv.*, m_cust.nama_cust
		  from 
		  t_inv inner join m_cust on  t_inv.id_cust = m_cust.id_cust
		  where t_inv.id_inv = '$id'";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysql_error());
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
	

}else if ($_POST['type'] == "Del_Inv"){
	$id = $_POST['id']; 	
	
	$pq = mysqli_query($koneksi, "select t_inv.*, m_cust.nama_cust
		  from 
		  t_inv inner join m_cust on  t_inv.id_cust = m_cust.id_cust
		  where t_inv.id_inv = '$id'  ");
	$rq=mysqli_fetch_array($pq);
	$id_jurnal = $rq['id_jurnal'];
	$jenis = $rq['jenis'];
	Del_Jurnal($id_jurnal);
	
	if($jenis == 'FCL')
	{
		$update = mysqli_query($koneksi, "update t_jo_mobil set id_inv = '0', status = '0' where id_inv = '$id' ");
	}else{
		$update = mysqli_query($koneksi, "update t_jo set id_inv = '0', status = '0' where id_inv = '$id' ");
	}
	
    $query = "DELETE FROM t_inv WHERE id_inv = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }	
	
	
	
	
}else if($_GET['type'] == "Read_Inv_FCL")
{
	$id_inv = $_GET['id_inv'];
	$mode = $_GET['mode'];
	
	$pq = mysqli_query($koneksi, "select t_inv.*, m_cust.nama_cust
		  from 
		  t_inv inner join m_cust on  t_inv.id_cust = m_cust.id_cust
		  where t_inv.id_inv = '$id_inv'  ");
	$rq=mysqli_fetch_array($pq);	
	$no_inv = $rq['no_inv'];
	$tgl_inv = ConverTgl($rq['tgl_inv']);
	$nama_cust = $rq['nama_cust'];
	$ppn = $rq['ppn'];
	$jenis = $rq['jenis'];
	$id_jurnal = $rq['id_jurnal'];
	Del_Jurnal($id_jurnal);
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="7%" style="text-align: center;">TANGGAL</th>	
					<th rowspan="2" width="7%" style="text-align: center;">NO. ORDER</th>	
					<th rowspan="2" width="7%" style="text-align: center;">NO. SJ</th>	
					<th rowspan="2" width="37%" style="text-align: center;">CUSTOMER</th>						
					<th rowspan="2" width="10%" style="text-align: center;">ASAL</th>
					<th rowspan="2" width="10%" style="text-align: center;">TUJUAN</th>
					<th rowspan="2" width="8%" style="text-align: center;">JENIS MOBIL</th>	
					<th rowspan="2" width="8%" style="text-align: center;">TAGIHAN</th>
					<th rowspan="2" width="3%" style="text-align: center;">DEL</th>					
				</tr>
			</thead>';	
	$SQL = "select t_jo.*, t_jo_mobil.id_detil, t_jo_mobil.tgl_sj, t_jo_mobil.rate, t_jo_mobil.rate_lain, t_jo_mobil.no_sj, t_jo_mobil.jenis_mobil, m_kota.nama_kota as asal, 
				m_kota1.nama_kota as tujuan, m_cust.nama_cust, m_mobil.no_polisi, m_supir.nama_supir, m_vendor.nama_vendor
				from 
				t_jo left join t_jo_mobil on t_jo.id_jo = t_jo_mobil.id_jo
				left join m_cust on  t_jo.id_cust = m_cust.id_cust
				left join m_kota on t_jo_mobil.id_asal = m_kota.id_kota
				left join m_kota as m_kota1 on t_jo_mobil.id_tujuan = m_kota1.id_kota
				left join m_mobil on t_jo_mobil.id_mobil = m_mobil.id_mobil
				left join m_supir on t_jo_mobil.id_supir = m_supir.id_supir
				left join m_vendor on t_jo_mobil.id_vendor = m_vendor.id_vendor
			  where t_jo_mobil.id_inv = '$id_inv' order by t_jo.tanggal desc, t_jo.no_jo desc";
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
			$tanggal = ConverTgl($row['tgl_sj']);
			$tagihan = $row['rate'] + $row['rate_lain'];
			$tagihanx = number_format($tagihan,0);
			$t_tagihan = $t_tagihan + $tagihan;
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">'.$tanggal.'</td>	
				<td style="text-align:center">'.$row['no_jo'].'</td>	
				<td style="text-align:center">'.$row['no_sj'].'</td>	
				<td style="text-align:center">'.$row['nama_cust'].'</td>
				<td style="text-align:center">'.$row['asal'].'</td>				
				<td style="text-align:ceter">'.$row['tujuan'].'</td>	
				<td style="text-align:ceter">'.$row['jenis_mobil'].'</td>
				<td style="text-align:right">'.$tagihanx.'</td>		';
				if($mode == 'Edit' ){
					
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Delete"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:Del_Inv_FCL('.$row['id_detil'].')"  >
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
			$t_tagihanx = number_format($t_tagihan,0);
			$data .= '<tr>							
				<td colspan = "7" style="text-align:center;background:#fff "></td>	
				<td colspan = "1" style="text-align:right;background:#eaebec;">Sub Total :</td>
				<td style="text-align:right;background:#eaebec;">'.$t_tagihanx.'</td>
				<td colspan = "1" style="text-align:center;background:#fff "></td>';	
				$data .='</tr>';	
			$nilai_ppn = ($ppn/100) * $t_tagihan;
			$nilai_ppnx = number_format($nilai_ppn,0);
			$data .= '<tr>							
				<td colspan = "7" style="text-align:center;background:#fff "></td>	
				<td colspan = "1" style="text-align:right;background:#eaebec;">PPN ('.$ppn.'%) :</td>
				<td style="text-align:right;background:#eaebec;">'.$nilai_ppnx.'</td>
				<td colspan = "1" style="text-align:center;background:#fff "></td>';	
				$data .='</tr>';
			$t_tagihan = $t_tagihan + $nilai_ppn;
			$t_tagihanx = number_format($t_tagihan,0);
			$data .= '<tr>							
				<td colspan = "7" style="text-align:center;background:#fff "></td>	
				<td colspan = "1" style="text-align:right;background:#00a65a;color:#fff"><b>Total :</b></td>
				<td style="text-align:right;background:#00a65a;color:#fff"><b>'.$t_tagihanx.'</b></td>
				<td colspan = "1" style="text-align:center;background:#fff "></td>';	
				$data .='</tr>';	
				
		}else{
			$t_tagihanx = number_format($t_tagihan,0);
			$data .= '<tr>							
				<td colspan = "7" style="text-align:center;background:#fff "></td>	
				<td colspan = "1" style="text-align:right;background:#00a65a;color:#fff"><b>Total :</b></td>
				<td style="text-align:right;background:#00a65a;color:#fff"><b>'.$t_tagihanx.'</b></td>
				<td colspan = "1" style="text-align:center;background:#fff "></td>';	
				$data .='</tr>';			
		}			
					
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
	
	$total = $t_tagihan;
	$tgl_jurnal =  $tgl_inv;
	$ket = "INV $jenis $nama_cust ($no_inv)";
	$jumlah  = $total;
	$jumlah1  = $total;
	$id_coa1 = $s_piutang_customer;	
	$cur = 'IDR';
	if($jenis == 'FCL')
	{
		$id_coa2 = $s_pendapatan_fcl;
	}
	else if($tipe == 'LCL')
	{
		$id_coa2 = $s_pendapatan_lcl;
	}

	if(!empty($nilai_ppn))
	{
		$jumlah2 = $total - $nilai_ppn;
		$id_coa4 = $s_ppn_keluaran;
		$jumlah4 = $nilai_ppn;
	}else{
		$id_coa4 = '';
		$jumlah2 = $total;
	}
	if($total > 0)
	{	
		$jenis = '1';
		$id_jurnal = Add_Jurnal($tgl_jurnal,$jenis,$ket,$cur,$kurs,$jumlah,
						$id_coa1,$jumlah1,$id_coa2,$jumlah2,$id_coa3,$jumlah3,$id_coa4,$jumlah4,$id_coa5,$jumlah5,
						$id_coa6,$jumlah6,$id_coa7,$jumlah7,$id_coa8,$jumlah8, $id_user );
	}
	
	$sql = "update t_inv set tagihan = '$t_tagihan', id_jurnal = '$id_jurnal' where id_inv = '$id_inv'	";
	$hasil=mysqli_query($koneksi, $sql);
	
    $data .= '</table>';
    echo $data;		
	
}else if ($_POST['type'] == "Add_Inv_FCL"){
	$id = $_POST['id']; 
	$id_inv = $_POST['id_inv']; 	
    $query = "update  t_jo_mobil set id_inv = '$id_inv', status = '1' WHERE id_detil = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }

}else if ($_POST['type'] == "Del_Inv_FCL"){
	$id = $_POST['id']; 	
    $query = "update  t_jo_mobil set id_inv = '0', status = '0' WHERE id_detil = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }	
	
	

}else if($_GET['type'] == "Read_Inv_LCL")
{
	$id_inv = $_GET['id_inv'];
	$mode = $_GET['mode'];
	
	$pq = mysqli_query($koneksi, "select t_inv.*, m_cust.nama_cust
		  from 
		  t_inv inner join m_cust on  t_inv.id_cust = m_cust.id_cust
		  where t_inv.id_inv = '$id_inv'  ");
	$rq=mysqli_fetch_array($pq);	
	$no_inv = $rq['no_inv'];
	$tgl_inv = ConverTgl($rq['tgl_inv']);
	$nama_cust = $rq['nama_cust'];
	$ppn = $rq['ppn'];
	$jenis = $rq['jenis'];
	$id_jurnal = $rq['id_jurnal'];
	Del_Jurnal($id_jurnal);
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="7%" style="text-align: center;">TANGGAL</th>	
					<th rowspan="2" width="7%" style="text-align: center;">NO. ORDER</th>	
					<th rowspan="2" width="25%" style="text-align: center;">CUSTOMER</th>	
					<th rowspan="2" width="13%" style="text-align: center;">NAMA BARANG</th>						
					<th rowspan="2" width="10%" style="text-align: center;">ASAL</th>
					<th rowspan="2" width="10%" style="text-align: center;">TUJUAN</th>
					<th rowspan="2" width="5%" style="text-align: center;">QTY</th>	
					<th rowspan="2" width="5%" style="text-align: center;">BERAT</th>
					<th rowspan="2" width="5%" style="text-align: center;">VOL</th>						
					<th rowspan="2" width="7%" style="text-align: center;">TAGIHAN</th>
					<th rowspan="2" width="3%" style="text-align: center;">DEL</th>					
				</tr>
			</thead>';	
	$SQL = "select t_jo.*, m_kota.nama_kota as asal, m_kota1.nama_kota as tujuan, m_cust.nama_cust
				from 
				t_jo left join m_cust on  t_jo.id_cust = m_cust.id_cust
				left join m_kota on t_jo.id_asal = m_kota.id_kota
				left join m_kota as m_kota1 on t_jo.id_tujuan = m_kota1.id_kota
			  where t_jo.id_inv = '$id_inv' order by t_jo.tanggal desc, t_jo.no_jo desc";
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
			$tanggal = ConverTgl($row['tanggal']);
			$berat = number_format($row['berat'],2);
			$vol = number_format($row['vol'],2);
			$tagihanx = number_format($row['tagihan'],0);
			$t_tagihan = $t_tagihan + $row['tagihan'];
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center">'.$tanggal.'</td>	
				<td style="text-align:center">'.$row['no_jo'].'</td>	
				<td style="text-align:center">'.$row['nama_cust'].'</td>	
				<td style="text-align:center">'.$row['nama_barang'].'</td>
				<td style="text-align:center">'.$row['asal'].'</td>				
				<td style="text-align:ceter">'.$row['tujuan'].'</td>	
				<td style="text-align:ceter">'.$row['qty'].'</td>
				<td style="text-align:ceter">'.$berat.'</td>
				<td style="text-align:ceter">'.$vol.'</td>
				<td style="text-align:right">'.$tagihanx.'</td>		';
				if($mode == 'Edit' ){
					
					$data .= '<td>
								<button class="btn btn-block btn-default"  title="Delete"
									style="margin:-3px;border-radius:0px" type="button" 
									onClick="javascript:Del_Inv_FCL('.$row['id_detil'].')"  >
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
			$t_tagihanx = number_format($t_tagihan,0);
			$data .= '<tr>							
				<td colspan = "8" style="text-align:center;background:#fff "></td>	
				<td colspan = "2" style="text-align:right;background:#eaebec;">Sub Total :</td>
				<td style="text-align:right;background:#eaebec;">'.$t_tagihanx.'</td>
				<td colspan = "1" style="text-align:center;background:#fff "></td>';	
				$data .='</tr>';	
			$nilai_ppn = ($ppn/100) * $t_tagihan;
			$nilai_ppnx = number_format($nilai_ppn,0);
			$data .= '<tr>							
				<td colspan = "8" style="text-align:center;background:#fff "></td>	
				<td colspan = "2" style="text-align:right;background:#eaebec;">PPN ('.$ppn.'%) :</td>
				<td style="text-align:right;background:#eaebec;">'.$nilai_ppnx.'</td>
				<td colspan = "1" style="text-align:center;background:#fff "></td>';	
				$data .='</tr>';
			$t_tagihan = $t_tagihan + $nilai_ppn;
			$t_tagihanx = number_format($t_tagihan,0);
			$data .= '<tr>							
				<td colspan = "8" style="text-align:center;background:#fff "></td>	
				<td colspan = "2" style="text-align:right;background:#00a65a;color:#fff"><b>Total :</b></td>
				<td style="text-align:right;background:#00a65a;color:#fff"><b>'.$t_tagihanx.'</b></td>
				<td colspan = "1" style="text-align:center;background:#fff "></td>';	
				$data .='</tr>';	
				
		}else{
			$t_tagihanx = number_format($t_tagihan,0);
			$data .= '<tr>							
				<td colspan = "8" style="text-align:center;background:#fff "></td>	
				<td colspan = "2" style="text-align:right;background:#00a65a;color:#fff"><b>Total :</b></td>
				<td style="text-align:right;background:#00a65a;color:#fff"><b>'.$t_tagihanx.'</b></td>
				<td colspan = "1" style="text-align:center;background:#fff "></td>';	
				$data .='</tr>';			
		}			
					
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
	
	$total = $t_tagihan;
	$tgl_jurnal =  $tgl_inv;
	$ket = "INV $jenis $nama_cust ($no_inv)";
	$jumlah  = $total;
	$jumlah1  = $total;
	$id_coa1 = $s_piutang_customer;	
	$cur = 'IDR';
	if($jenis == 'FCL')
	{
		$id_coa2 = $s_pendapatan_fcl;
	}
	else if($jenis == 'LCL')
	{
		$id_coa2 = $s_pendapatan_lcl;
	}

	if(!empty($nilai_ppn))
	{
		$jumlah2 = $total - $nilai_ppn;
		$id_coa4 = $s_ppn_keluaran;
		$jumlah4 = $nilai_ppn;
	}else{
		$id_coa4 = '';
		$jumlah2 = $total;
	}
	if($total > 0)
	{	
		$jenis = '1';
		$id_jurnal = Add_Jurnal($tgl_jurnal,$jenis,$ket,$cur,$kurs,$jumlah,
						$id_coa1,$jumlah1,$id_coa2,$jumlah2,$id_coa3,$jumlah3,$id_coa4,$jumlah4,$id_coa5,$jumlah5,
						$id_coa6,$jumlah6,$id_coa7,$jumlah7,$id_coa8,$jumlah8, $id_user );
	}
	
	$sql = "update t_inv set tagihan = '$t_tagihan', id_jurnal = '$id_jurnal' where id_inv = '$id_inv'	";
	$hasil=mysqli_query($koneksi, $sql);
	
    $data .= '</table>';
    echo $data;			
	
}else if ($_POST['type'] == "Add_Inv_LCL"){
	$id = $_POST['id']; 
	$id_inv = $_POST['id_inv']; 	
    $query = "update  t_jo set id_inv = '$id_inv', status = '1' WHERE id_jo = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }

}else if ($_POST['type'] == "Del_Inv_LCL"){
	$id = $_POST['id']; 	
    $query = "update  t_jo set id_inv = '0', status = '0' WHERE id_jo = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }		
	
	
}else if ($_GET['type'] == "ListOrder_FCL")
{	
	$cari = $_GET['cari'];
	$id_cust = $_GET['id_cust'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>
					<th width="6%" style="text-align: center;">NO</th>
					<th width="11%" style="text-align: center;">NO ORDER</th>
					<th width="11%" style="text-align: center;">NO SJ</th>
					<th width="34%" style="text-align: center;">NAMA CUSTOMER</th>
					<th width="11%" style="text-align: center;">JENIS MOBIL</th>
					<th width="11%" style="text-align: center;">TAGIHAN</th>
					<th width="7%" style="text-align: center;">ADD</th>
				</tr>
			</thead>';	
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
	
	$SQL = "select t_jo.*, t_jo_mobil.id_detil, t_jo_mobil.no_sj, t_jo_mobil.rate, t_jo_mobil.rate_lain, t_jo_mobil.jenis_mobil, m_kota.nama_kota as asal, 
				m_kota1.nama_kota as tujuan, m_cust.nama_cust, m_mobil.no_polisi, m_supir.nama_supir, m_vendor.nama_vendor
				from 
				t_jo left join t_jo_mobil on t_jo.id_jo = t_jo_mobil.id_jo
				left join m_cust on  t_jo.id_cust = m_cust.id_cust
				left join m_kota on t_jo_mobil.id_asal = m_kota.id_kota
				left join m_kota as m_kota1 on t_jo_mobil.id_tujuan = m_kota1.id_kota
				left join m_mobil on t_jo_mobil.id_mobil = m_mobil.id_mobil
				left join m_supir on t_jo_mobil.id_supir = m_supir.id_supir
				left join m_vendor on t_jo_mobil.id_vendor = m_vendor.id_vendor
			  where t_jo_mobil.no_sj LIKE '%$cari%' and t_jo_mobil.id_inv = '0' and t_jo.id_cust = '$id_cust' 
			  order by t_jo.tanggal desc, t_jo.no_jo desc LIMIT 0, 25";
	
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;
			$tagihan = $row['rate'] + $row['rate_lain'];
			$tagihanx = number_format($tagihan,0);
			$data .= '<tr>';		
			$data .= '<td style="text-align:center">'.$posisi.'.</td>';
			$data .= '<td style="text-align:center"><a href="#" onclick="PilihOrder('.$row['id_detil'].')" >'.$row['no_jo'].'</a></td>';
			$data .= '<td style="text-align:center"><a href="#" onclick="PilihOrder('.$row['id_detil'].')" >'.$row['no_sj'].'</a></td>';
			$data .= '<td style="text-align:left"><a href="#" onclick="PilihOrder('.$row['id_detil'].')" >'.$row['nama_cust'].'</a></td>';
			$data .= '<td style="text-align:center"><a href="#" onclick="PilihOrder('.$row['id_detil'].')" >'.$row['jenis_mobil'].'</a></td>';
			$data .= '<td style="text-align:right"><a href="#" onclick="PilihOrder('.$row['id_detil'].')" >'.$tagihanx.'</a></td>';
			$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihOrder('.$row['id_detil'].')" 
					style="margin:-3px;width:100%;padding:1px;border-radius:1px">Add</button>
					</td>';		
			$data .='</tr>';
    	}		
    }
    else
    {
    	$data .= '<tr><td colspan="7"></td></tr>';
    }
    $data .= '</table>';
    echo $data;			
	
	
}else if ($_GET['type'] == "ListOrder_LCL")
{	
	$cari = $_GET['cari'];
	$id_cust = $_GET['id_cust'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>
					<th width="6%" style="text-align: center;">NO</th>
					<th width="11%" style="text-align: center;">TANGGAL</th>
					<th width="11%" style="text-align: center;">NO ORDER</th>
					<th width="34%" style="text-align: center;">NAMA CUSTOMER</th>
					<th width="11%" style="text-align: center;">NAMA BARANG</th>
					<th width="11%" style="text-align: center;">TUJUAN</th>
					<th width="11%" style="text-align: center;">TAGIHAN</th>
					<th width="7%" style="text-align: center;">ADD</th>
				</tr>
			</thead>';	
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
	
	$SQL = "select t_jo.*, m_kota.nama_kota as asal, m_kota1.nama_kota as tujuan, m_cust.nama_cust
				from 
				t_jo left join m_cust on  t_jo.id_cust = m_cust.id_cust
				left join m_kota on t_jo.id_asal = m_kota.id_kota
				left join m_kota as m_kota1 on t_jo.id_tujuan = m_kota1.id_kota
			  where t_jo.id_cust = '$id_cust'  and t_jo.jenis = '1' and t_jo.id_inv = '0'
			  order by t_jo.tanggal desc, t_jo.no_jo desc LIMIT 0, 25";
	
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;
			$tanggal = ConverTgl($row['tanggal']);
			$tagihanx = number_format($row['tagihan'],0);
			$data .= '<tr>';		
			$data .= '<td style="text-align:center">'.$posisi.'.</td>';
			$data .= '<td style="text-align:center"><a href="#" onclick="PilihOrder('.$row['id_jo'].')" >'.$tanggal.'</a></td>';
			$data .= '<td style="text-align:center"><a href="#" onclick="PilihOrder('.$row['id_jo'].')" >'.$row['no_jo'].'</a></td>';
			$data .= '<td style="text-align:left"><a href="#" onclick="PilihOrder('.$row['id_jo'].')" >'.$row['nama_cust'].'</a></td>';
			$data .= '<td style="text-align:center"><a href="#" onclick="PilihOrder('.$row['id_jo'].')" >'.$row['nama_barang'].'</a></td>';
			$data .= '<td style="text-align:center"><a href="#" onclick="PilihOrder('.$row['id_jo'].')" >'.$row['tujuan'].'</a></td>';
			$data .= '<td style="text-align:right"><a href="#" onclick="PilihOrder('.$row['id_jo'].')" >'.$tagihanx.'</a></td>';
			$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihOrder('.$row['id_jo'].')" 
					style="margin:-3px;width:100%;padding:1px;border-radius:1px">Add</button>
					</td>';		
			$data .='</tr>';
    	}		
    }
    else
    {
    	$data .= '<tr><td colspan="7"></td></tr>';
    }
    $data .= '</table>';
    echo $data;			


}else if ($_POST['type'] == "AddPayment"){		
	if($_POST['id_inv'] != '' )
	{		
		$id_inv = $_POST['id_inv'];
		$tanggal = $_POST['tanggal'];
		$jumlah = $_POST['jumlah'];
		$pph = $_POST['pph'];
		$id_bank = $_POST['id_bank'];
		$no_inv = $_POST['no_inv'];
		$nama = $_POST['nama'];
		
		$jumlah_bayar = str_replace(",","", $jumlah);
		$pph = str_replace(",","", $pph);
		$tanggalx = ConverTglSql($tanggal);	
	
		//JURNAL BAYAR
		$jenis_jurnal = '1';
		$tgl_jurnal = $tanggal;		
		$ket = "PEMBAYARAN INV $nama ($no_inv)";	
		
		$id_coa1 = $id_bank;
		$jumlah1 = $jumlah_bayar;
		$id_bank1 = $id_bank;
		$id_coa2 = $s_piutang_customer ;
		$jumlah2 = $jumlah_bayar+$pph;	
		
		if($pph > 0)
		{
			$id_coa3 = $pph23_masukan;
			$jumlah3 = $pph;
			$id_bank3 =0;
		}
		
	
		$jumlah = $jumlah_bayar + $pph;		

		$jenis = '1';
		$kurs = '1';
		$cur = 'IDR';
		$id_jurnal = Add_Jurnal($tgl_jurnal,$jenis,$ket,$cur,$kurs,$jumlah,
						$id_coa1,$jumlah1,$id_coa2,$jumlah2,$id_coa3,$jumlah3,$id_coa4,$jumlah4,$id_coa5,$jumlah5,
						$id_coa6,$jumlah6,$id_coa7,$jumlah7,$id_coa8,$jumlah8, $id_user );
						
	
		$sql = "INSERT INTO t_inv_bayar (id_inv, id_jurnal, bayar, pph, id_bank) 
		    values ('$id_inv','$id_jurnal','$jumlah_bayar','$pph','$id_bank')";
		$hasil=mysqli_query($koneksi, $sql);
		
		
		$sql = "UPDATE t_inv set bayar =  bayar + '$jumlah' where id_inv ='$id_inv' ";
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
	$t1="select t_inv_bayar.*, t_jurnal.id_user, t_jurnal.jumlah, t_jurnal.tgl_jurnal, t_jurnal.no_jurnal,m_bank.nama_bank, m_bank.no_bank from
	        t_inv_bayar inner join t_jurnal on t_inv_bayar.id_jurnal = t_jurnal.id_jurnal
			left join m_bank on t_inv_bayar.id_bank = m_bank.id_bank
			where t_inv_bayar.id_inv = '$id' order by t_jurnal.tgl_jurnal";
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
	$pq = mysqli_query($koneksi, "select * from t_inv where id_inv = '$id'  ");
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
	$pq = mysqli_query($koneksi, "select * from t_inv_bayar where id_bayar = '$id'  ");
	$rq=mysqli_fetch_array($pq);
	$id_jurnal = $rq['id_jurnal'];
	Del_Jurnal($id_jurnal);
	$jumlah = $rq['bayar'] + $rq['pph'];	
	
	$update = mysqli_query($koneksi, "UPDATE t_inv set bayar =  bayar - '$jumlah'  where id_inv = '$rq[id_inv]' ");
    $query = "DELETE FROM  t_inv_bayar WHERE id_bayar = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysql_error());
    }		
	
}

?>