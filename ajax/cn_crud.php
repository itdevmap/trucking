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
	
	if($stat == 'Unpaid')
	{
		$stat = '0';
	}else if($stat == 'Paid')
	{
		$stat = '1';
	}
	
	if($field == 'CN No')
	{
		$f = 't_jo_tagihan.no_tagihan';
	}else if($field == 'JO No'){
		$f = 't_jo.jo_no';		
	}else if($field == 'Agent'){
		$f = 'm_cust.nama_cust';	
	}else{
		$f = 't_jo_tagihan.no_tagihan';
	}
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>					
					<th rowspan="2" width="7%" style="text-align: center;">CN DATE</th>
					<th rowspan="2" width="10%" style="text-align: center;">CN NO</th>
					<th rowspan="2" width="7%" style="text-align: center;">JO NO</th>
					<th rowspan="2" width="30%" style="text-align: center;">AGENT</th>
					<th rowspan="2" width="7%" style="text-align: center;">DUE DATE</th>
					<th rowspan="2" width="3%" style="text-align: center;">CUR</th>
					<th rowspan="2" width="7%" style="text-align: center;">BILL</th>
					<th rowspan="2" width="7%" style="text-align: center;">PAYMENT</th>
					<th rowspan="2" width="7%" style="text-align: center;">BALANCE</th>
					<th rowspan="2" width="6%" style="text-align: center;">STATUS</th>
					<th colspan="3" width="6%" style="text-align: center;">ACTION</th>	
				</tr>
				<tr>	
					<th width="2%" style="text-align: center;">PRINT</th>
					<th width="2%" style="text-align: center;">ADD PAY</th>		
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
		$SQL = "select t_jo_tagihan.*, m_cust.nama_cust, t_jo.jo_no  from 
			  t_jo_tagihan inner join m_cust on  t_jo_tagihan.id_cust = m_cust.id_cust
			  left join t_jo on t_jo_tagihan.id_jo = t_jo.id_jo
			  where t_jo_tagihan.no_tagihan <> '' and t_jo_tagihan.tgl_tagihan between '$tgl1x' and '$tgl2x' and
			  $f LIKE '%$cari%' and t_jo_tagihan.jenis = '5' order by t_jo_tagihan.tgl_tagihan desc, t_jo_tagihan.no_tagihan desc
			  LIMIT $offset, $jmlperhalaman";
	}else{
		$SQL = "select t_jo_tagihan.*, m_cust.nama_cust, t_jo.jo_no  from 
			  t_jo_tagihan inner join m_cust on  t_jo_tagihan.id_cust = m_cust.id_cust
			  left join t_jo on t_jo_tagihan.id_jo = t_jo.id_jo
			  where t_jo_tagihan.no_tagihan <> '' and t_jo_tagihan.tgl_tagihan between '$tgl1x' and '$tgl2x' and
			  $f LIKE '%$cari%' and t_jo_tagihan.jenis = '5' and t_jo_tagihan.status = '$stat' 
			  order by t_jo_tagihan.tgl_tagihan desc, t_jo_tagihan.no_tagihan desc
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
			$tanggal = ConverTgl($row['tgl_tagihan']);
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
			$tagihan = number_format($row['tagihan'],2);
			$bayar = number_format($row['bayar'],2);
			$sisax = number_format($sisa,2);
			
			$t_tagihan = $t_tagihan + $row['tagihan'];
			$t_bayar = $t_bayar + $row['bayar'];
			
			if($sisa <= 0)
			{
				if($row['tagihan'] > 0 )
				{
					$label = 'success';
					$status = 'Paid';
					$s = '1';
				}else{
					$label = 'danger';
					$status = 'Unpaid';
					$s = '0';
				}
				
			}else{
				$label = 'danger';
				$status = 'Unpaid';
				$s = '0';
			}
			$posisi++;		
			
			$update = mysqli_query($koneksi, "update t_jo_tagihan set status = '$s' where id_tagihan = '$row[id_tagihan]'  ");
			
			
			$xy1="View|$row[id_jo]";
			$xy1=base64_encode($xy1);
			$link_jo = "jo_re.php?id=$xy1";
			
			$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>';
				if($row['bayar'] <= 0)
				{
					$data .= '<td style="text-align:center"><a href="#" onclick="GantiTanggal('.$row['id_tagihan'].');">'.$tanggal.'</a></td>';
				}else{
					$data .= '<td style="text-align:center">'.$tanggal.'</td>';	
				}	
				$data .= '<td style="text-align:center">'.$row['no_tagihan'].'</td>';	
				$data .= '<td style="text-align:center">'.$row['jo_no'].'</td>
				<td style="text-align:left">'.$row['nama_cust'].'</td>
				<td style="text-align:left">
					<button type="button" class="btn btn-'.$label_aging.'" style="width:100%;padding:1px;margin:-3px">'.$due_date.'</button>
				</td>
				<td style="text-align:center">'.$row['cur'].'</td>
				<td style="text-align:right;background:#e48f0f;color:#fff">'.$tagihan.'</td>
				<td style="text-align:right;background:#406a94;color:#fff">'.$bayar.'</td>
				<td style="text-align:right;background:#4bc343;color:#fff">'.$sisax.'</td>
				<td style="text-align:center">
					<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
				</td>';	
				$xy1="$row[id_tagihan]";
				$xy1=base64_encode($xy1);
				if($row['jenis_jo'] == '0')
				{
					$link = "'cetak_cn.php?id=$xy1'";
				}
				else if($row['jenis_jo'] == '1')
				{
					$link = "'cetak_cn_konsol.php?id=$xy1'";
				}
				else if($row['jenis_jo'] == '2')
				{
					$link = "'cetak_cn_konsol_jo.php?id=$xy1'";
				}
				
				$data .= '<td>
					<button class="btn btn-block btn-default"  title="Print"
						style="margin:-3px;border-radius:0px" type="button" 									
						onClick="window.open('.$link.') ">
						<span class="fa fa-print " ></span>
					</button></td>';
				
				if($m_add == '1' && $s == '0'){					
					$data .= '<td>
						<button class="btn btn-block btn-default" title="Add Payment"
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="javascript:GetPayment('.$row['id_tagihan'].')">
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
						onClick="javascript:ListPayment('.$row['id_tagihan'].')"  >
						<span class="glyphicon glyphicon-search" ></span>
					</button>	
				</td>';	
				$data .='</tr>';
    		$number++;
    	}		
		
		$t_tagihanx = number_format($t_tagihan,2);
		$t_bayarx = number_format($t_bayar,2);
		$t_sisa = $t_tagihan - $t_bayar;
		$t_sisax = number_format($t_sisa,2);
		$data .= '<tr>';
		$data .= '<td colspan= "7" style="text-align:right;background:#eaebec;color:#000"><b>Total  :&nbsp;&nbsp;&nbsp;</b></td>	
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
					$pq = mysqli_query($koneksi, "select count(t_jo_tagihan.id_tagihan) as jml from 
					  t_jo_tagihan inner join m_cust on  t_jo_tagihan.id_cust = m_cust.id_cust
					  left join t_jo on t_jo_tagihan.id_jo = t_jo.id_jo
					  where t_jo_tagihan.no_tagihan <> '' and t_jo_tagihan.tgl_tagihan between '$tgl1x' and '$tgl2x' and
					  $f LIKE '%$cari%' and t_jo_tagihan.jenis = '5'  ");
				}else{
					$pq = mysqli_query($koneksi, "select count(t_jo_tagihan.id_tagihan) as jml from 
					  t_jo_tagihan inner join m_cust on  t_jo_tagihan.id_cust = m_cust.id_cust
					  left join t_jo on t_jo_tagihan.id_jo = t_jo.id_jo
					  where t_jo_tagihan.no_tagihan <> '' and t_jo_tagihan.tgl_tagihan between '$tgl1x' and '$tgl2x' and
					  $f LIKE '%$cari%' and t_jo_tagihan.jenis = '5' and t_jo_tagihan.status = '$stat' ");
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

}else if ($_POST['type'] == "GetTanggal"){
	$id = $_POST['id'];	
    $query = "select * from t_jo_tagihan where id_tagihan = '$id'";
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

}else if ($_POST['type'] == "UpdateTanggalInv"){		
	if($_POST['id'] != '' )
	{	
		$id = $_POST['id'];
		$tanggal = $_POST['tanggal_inv'];
		$due_date = $_POST['due_date'];
		$tanggal = ConverTglSql($tanggal);	
		$due_date = ConverTglSql($due_date);
		$sql = "update t_jo_tagihan set tgl_tagihan = '$tanggal', due_date = '$due_date' where id_tagihan = '$id'	";
		$hasil = mysqli_query($koneksi, $sql);
		if (!$hasil) {
	        			
			//exit(mysql_error());
			echo "Data Error...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}	
	
}else if($_GET['type'] == "Read_CN")
{
	$id_jo = $_GET['id_jo'];
	$id_joc = $_GET['id_joc'];
	$mode = $_GET['mode'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>					
					<th rowspan="2" width="8%" style="text-align: center;">DATE</th>
					<th rowspan="2" width="10%" style="text-align: center;">CN. NO</th>				
					<th rowspan="2" width="48%" style="text-align: center;">AGENT</th>
					<th rowspan="2" width="3%" style="text-align: center;">CUR</th>	
					<th rowspan="2" width="8%" style="text-align: center;">BILL</th>					
					<th rowspan="2" width="6%" style="text-align: center;">STATUS</th>	
					<th colspan="3" width="6%" style="text-align: center;">ACTION</th>
				</tr>
				<tr>	
					<th  width="2%" style="text-align: center;">EDIT</th>
					<th  width="2%" style="text-align: center;">DEL</th>
					<th  width="2%" style="text-align: center;">PRINT</th>	
				</tr>	
			</thead>';	
	
	$t1 = "select t_jo_tagihan.*, m_cust.nama_cust
		   from 
		   t_jo_tagihan left join m_cust on t_jo_tagihan.id_cust = m_cust.id_cust 
		   left join t_jo on t_jo_tagihan.id_jo = t_jo.id_jo
		   where t_jo_tagihan.id_jo = '$id_jo' and t_jo_tagihan.jenis = '5' or t_jo_tagihan.id_jo = '$id_joc' and t_jo_tagihan.jenis = '5' and t_jo_tagihan.jenis_jo = '2'
		   order by t_jo_tagihan.id_tagihan";
	$h1 = mysqli_query($koneksi, $t1);       
	while ($d1=mysqli_fetch_array($h1)){
		$n++;
		if($d1['no_tagihan'] == '')
		{
			$tanggal = '';
		}else{
			$tanggal = ConverTgl($d1['tgl_tagihan']);
		}
		$tagihan = number_format($d1['tagihan'],2);
		if($d1['status'] == '1')
		{
			$label = 'success';
			$status = 'Paid';
		}else if($d1['status'] == '0'){
			$label = 'danger';
			$status = 'Unpaid';
		}
		$data .= '<tr>';							
			$data .= '<td style="text-align:center">'.$n.'.</td>';			
			$data .= '<td style="text-align:center">'.$tanggal.'</td>';
			$data .= '<td style="text-align:center">'.$d1['no_tagihan'].'</td>';
			$data .= '<td style="text-align:left">'.$d1['nama_cust'].'</td>';
			$data .= '<td style="text-align:center">'.$d1['cur'].'</td>';
			$data .= '<td style="text-align:right;background:#4bc343;color:#fff">'.$tagihan.'</td>';
			$data .= '<td style="text-align:center">
				<button type="button" class="btn btn-'.$label.'" style="width:100%;padding:1px;margin:-3px">'.$status.'</button>
				</td>';
			
			if($mode == 'Edit' && $d1['bayar'] <= '0' && $d1['jenis_jo'] == '0' ){
				$xy1="$d1[jenis_jo]|Edit|$id_jo|$d1[id_tagihan]";
				$xy1=base64_encode($xy1);
				$link = "'cn_data.php?id=$xy1'";
				$data .= '<td>
						<button class="btn btn-block btn-default"  title="Edit"
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="window.location.href = '.$link.' "  >
						<span class="fa fa-edit " ></span>
						</button></td>';
						
				$data .= '<td>
							<button class="btn btn-block btn-default"  title="Delete"
							style="margin:-3px;border-radius:0px" type="button" 
							onClick="javascript:Delete('.$d1['id_tagihan'].')"  >
							<span class="fa fa-close " ></span>
						</button></td>';		
			}
			else
			{
				$data .='<td></td>';
				$data .='<td></td>';
			}	
			
			$xy1="$d1[id_tagihan]";
			$xy1=base64_encode($xy1);
			if($d1['jenis_jo'] == '2')
			{
				$link = "'cetak_cn_konsol_jo.php?id=$xy1'";
			}else if($d1['jenis_jo'] == '0'){
				$link = "'cetak_cn.php?id=$xy1'";
			}
			
			$data .= '<td>
					<button class="btn btn-block btn-default"  title="Print"
					style="margin:-3px;border-radius:0px" type="button" 
					onClick="window.location.href = '.$link.' "  >
					<span class="fa fa-print " ></span>
					</button></td>';		
		$data .= '</tr>';	
	}		
    $data .= '</table>';
    echo $data;	


}else if($_GET['type'] == "ReadDetil")
{
	$id_tagihan = $_GET['id_tagihan'];
	$mode = $_GET['mode'];
	
	$pq = mysqli_query($koneksi, "select * from t_jo_tagihan where id_tagihan = '$id_tagihan'  ");
	$rq = mysqli_fetch_array($pq);	
	$id_jo = $rq['id_jo'];
	$ppn = $rq['ppn'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="46%" style="text-align: center;">DESCRIPTION</th>
					<th rowspan="2" width="10%" style="text-align: center;">QTY</th>
					<th rowspan="2" width="10%" style="text-align: center;">UNIT</th>
					<th rowspan="2" width="12%" style="text-align: center;">PRICE</th>
					<th rowspan="2" width="15%" style="text-align: center;">AMOUNT</th>
					<th colspan="2" width="4%" style="text-align: center;">ACTION</th>						
				</tr>
				<tr>
					<th width="2%" style="text-align: center;">EDIT</th>
					<th width="2%" style="text-align: center;">DEL</th>
				</tr>	
			</thead>';	
	$SQL = "select  t_jo_tagihan_detil.*,m_cost.nama_cost from 
			 t_jo_tagihan_detil inner join m_cost on  t_jo_tagihan_detil.id_cost = m_cost.id_cost 
			where  t_jo_tagihan_detil.id_tagihan = '$id_tagihan' order by  t_jo_tagihan_detil.id_detil";
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
			$jumlah = $row['qty'] * $row['price'];	
			$total = $total + $jumlah;
			$jumlahx = number_format($jumlah,2);
			$price = number_format($row['price'],2);			
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
			$totalx = number_format($total,2);
			$data .= '<td></td>';
			$data .= '<td></td>';
			$data .= '<td colspan="3" style="text-align:right;background:#eee;color:#000">Total Amount  :</td>	
						<td style="text-align:right;background:#eee;color:#000">'.$totalx.'</td>';
			
			$data .= '<td></td>';
			$data .= '<td></td>';
			$data .= '</tr>';
		
			$nilai_ppn = ($ppn/100) * $total;
			$nilai_ppnx = number_format($nilai_ppn,2);
			$data .= '<td></td>';
			$data .= '<td></td>';
			$data .= '<td colspan="3" style="text-align:right;background:#eee;color:#000">PPN ('.$ppn.'%)  :</td>	
						<td style="text-align:right;background:#eee;color:#000">'.$nilai_ppnx.'</td>';
			
			$data .= '<td></td>';
			$data .= '<td></td>';
			$data .= '</tr>';
			
			$total = $total + $nilai_ppn;
			$totalx = number_format($total,2);
			$data .= '<td></td>';
			$data .= '<td></td>';
			$data .= '<td colspan="3" style="text-align:right;background:#eee;color:#000"><b>Total  :</b></td>	
						<td style="text-align:right;background:#008d4c;color:#fff"><b>'.$totalx.'</b></td>';			
			$data .= '<td></td>';
			$data .= '<td></td>';
			$data .= '</tr>';
		}else{
			
			$totalx = number_format($total,2);
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
	
	$sql = "UPDATE t_jo_tagihan set tagihan = '$total'	where id_tagihan = '$id_tagihan' ";
	$hasil = mysqli_query($koneksi, $sql);

    $data .= '</table>';
	
	Update_Profit($id_jo);
	
    echo $data;	
	
}else if ($_POST['type'] == "Del_Tagihan"){
	$id = $_POST['id']; 	
	$jo_no = $_POST['jo_no']; 			
	$pq = mysqli_query($koneksi, "select * from t_jo_tagihan where id_tagihan = '$id' ");
	$rq = mysqli_fetch_array($pq);		
	Del_Jurnal($rq['id_jurnal']);
	
	$tanggalku = date("Y-m-d h:i:sa");
	$ket = "DEL CN JOB ORDER NO. $jo_no ";
	$audit = mysqli_query($koneksi,"INSERT INTO m_audit (tanggal,created,ket) values ('$tanggalku','$id_user','$ket')" );
	
	$del = mysqli_query($koneksi, "delete from t_jo_tagihan_detil where id_tagihan = '$id' ");
    $query = "DELETE FROM t_jo_tagihan WHERE id_tagihan = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }	
	
}else if ($_POST['type'] == "Detil_Data"){
	$id = $_POST['id'];	
    $query = "select  t_jo_tagihan_detil.*,m_cost.nama_cost, t_jo.jo_no,m_cust.nama_cust from 
			 t_jo_tagihan_detil inner join m_cost on  t_jo_tagihan_detil.id_cost = m_cost.id_cost 
			 left join t_jo on t_jo_tagihan_detil.id_jo_kecil = t_jo.id_jo
			 left join m_cust on t_jo.id_cust = m_cust.id_cust
			where t_jo_tagihan_detil.id_detil  = '$id'";
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
	
	$pq = mysqli_query($koneksi,"select  t_jo_tagihan_detil.*,m_cost.nama_cost from 
			 t_jo_tagihan_detil inner join m_cost on  t_jo_tagihan_detil.id_cost = m_cost.id_cost 
			where t_jo_tagihan_detil.id_detil  = '$id' ");
	$rq=mysqli_fetch_array($pq);	
	$nama_cost = $rq['nama_cost'];
	
	$mode = strtoupper($mode);
	$tanggalku = date("Y-m-d h:i:sa");
	$ket = "DEL ITEM CN $nama_cost JOB ORDER NO. $jo_no";
	$audit = mysqli_query($koneksi,"INSERT INTO m_audit (tanggal,created,ket) values ('$tanggalku','$id_user','$ket')" );
	
    $query = "DELETE FROM t_jo_tagihan_detil WHERE id_detil = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }
	
}else if ($_POST['type'] == "Add_Data"){		
	if($_POST['mode'] != '' )
	{	
		$id = $_POST['id'];
		$id_cost = $_POST['id_cost'];
		$id_jo = $_POST['id_jo'];
		$id_tagihan = $_POST['id_tagihan'];
		$unit = $_POST['unit'];
		$jo_no = $_POST['jo_no'];
		$nama_cost = $_POST['nama_cost'];
		$qty = $_POST['qty'];
		$price = $_POST['price'];
		$mode = $_POST['mode'];
		$note = addslashes($_POST['note']);		
		$qty = str_replace(",","", $qty);
		$price = str_replace(",","", $price);
		
		if($mode == 'Add')
		{			
			$sql = "INSERT INTO t_jo_tagihan_detil (id_tagihan, id_cost, qty, unit, price, note, id_jo_kecil) values
					('$id_tagihan', '$id_cost','$qty','$unit','$price', '$note', '$id_jo')";
			$hasil= mysqli_query($koneksi, $sql);
		}
		else
		{
			$sql = "update t_jo_tagihan_detil set 
					id_cost = '$id_cost',
					unit = '$unit',
					qty = '$qty',
					price = '$price',
					note = '$note'
					where id_detil = '$id'	";
			$hasil=mysqli_query($koneksi,$sql);
		}
		if (!$hasil) {
	       
			echo "Data Error...!";
	    }
		else
		{	
			$mode = strtoupper($mode);
			$tanggalku = date("Y-m-d h:i:sa");
			$ket = "$mode ITEM CN $nama_cost JOB ORDER NO. $jo_no";
			$audit = mysqli_query($koneksi,"INSERT INTO m_audit (tanggal,created,ket) values ('$tanggalku','$id_user','$ket')" );
			echo "Data saved!";
		}
	}		
	
	
}else if ($_POST['type'] == "GetPayment"){
	$id = $_POST['id'];	
    $query = "select t_jo_tagihan.*, m_cust.nama_cust from
				t_jo_tagihan inner join m_cust on  t_jo_tagihan.id_cust = m_cust.id_cust  
			    where t_jo_tagihan.id_tagihan = '$id' ";
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
	if($_POST['id_tagihan'] != '' )
	{		
		$id_tagihan = $_POST['id_tagihan'];
		$cur_tagihan = $_POST['cur_tagihan'];
		$tanggal = $_POST['tanggal'];
		$jumlah = $_POST['jumlah'];
		$cur_bayar = $_POST['cur_bayar'];
		$rate = $_POST['rate'];
		$pph = $_POST['pph'];
		$id_bank = $_POST['id_bank'];
		$total = $_POST['total'];
		$inv_no = $_POST['inv_no'];
		$nama = $_POST['nama'];
		$biaya_bank = $_POST['biaya_bank'];
		$tagihan_idr = $_POST['tagihan_idr'];
		
		$tagihan_idr = str_replace(",","", $tagihan_idr);
		$jumlah = str_replace(",","", $jumlah);
		$jumlah_bayar = str_replace(",","", $jumlah);
		$rate = str_replace(",","", $rate);
		$pph = str_replace(",","", $pph);
		$total = str_replace(",","", $total);
		$biaya_bank = str_replace(",","", $biaya_bank);
		$tanggalx = ConverTglSql($tanggal);	
	
		//JURNAL BAYAR
		$jenis_jurnal = '1';
		$tgl_jurnal = $tanggal;		
		$ket = "PAYMENT CN $nama ($inv_no)";	
		
		$tagihan_bayar = ($jumlah_bayar+$pph) *$rate;
		if($tagihan_bayar < $tagihan_idr && $cur_tagihan == 'USD' && $cur_bayar == 'USD')
		{
			$jumlah6 = ($tagihan_idr - $tagihan_bayar) / $rate;
			$id_coa6 = $kurs_untung;
			$id_coa2 = $id_bank ;
			$jumlah2 = $jumlah_bayar - $jumlah6;	
		}else if($tagihan_bayar > $tagihan_idr && $cur_tagihan == 'USD' && $cur_bayar == 'USD'){
				
			$jumlah3 = ($tagihan_bayar - $tagihan_idr) / $rate;
			$id_coa3 = $kurs_rugi;	
			$id_coa2 = $id_bank ;
			$jumlah2 = $jumlah_bayar + $jumlah3;
		}else{
			$id_coa2 = $id_bank ;
			$jumlah2 = $jumlah_bayar;
		}
		
		$id_coa1 = $s_hutang_agent ;
		$jumlah1 = $jumlah_bayar;	
		
		
		$cur = $cur_bayar;
		$jumlah = $jumlah_bayar;		
		$jenis = '1';
		$kurs = $rate;
		$id_jurnal = Add_Jurnal($tgl_jurnal,$jenis,$ket,$cur,$kurs,$jumlah,
						$id_coa1,$jumlah1,$id_coa2,$jumlah2,$id_coa3,$jumlah3,$id_coa4,$jumlah4,$id_coa5,$jumlah5,
						$id_coa6,$jumlah6,$id_coa7,$jumlah7,$id_coa8,$jumlah8, $id_user );
		
			
		$bayar = $jumlah_bayar;
		
		$sql = "INSERT INTO t_jo_tagihan_bayar (id_tagihan,id_jurnal,cur_bayar,kurs,bayar,pph) 
		    values ('$id_tagihan','$id_jurnal','$cur_bayar','$rate','$bayar','$pph')";
		$hasil=mysqli_query($koneksi, $sql);
		
		if($cur_tagihan == $cur_bayar)
		{
			$bayar = $jumlah_bayar;
		}
		else if($cur_tagihan == 'USD' && $cur_bayar == 'IDR')
		{
			$bayar = ($jumlah_bayar/$rate);
		}else{
			$bayar = ($jumlah_bayar*$rate);
		}
		
		$sql = "UPDATE t_jo_tagihan set bayar =  bayar + '$bayar' where id_tagihan ='$id_tagihan' ";
		$hasil = mysqli_query($koneksi, $sql);
		
		if (!$hasil) {
	        			
			exit(mysqli_error());
			echo "Data Invalid...!";
	    }
		else
		{	
			$mode = strtoupper($mode);
			$tanggalku = date("Y-m-d h:i:sa");
			$ket = "ADD PAYMENT CN NO. $inv_no";
			$audit = mysqli_query($koneksi,"INSERT INTO m_audit (tanggal,created,ket) values ('$tanggalku','$id_user','$ket')" );
			echo "Data Saved...!";
		}
	}	

}else if($_GET['type'] == "ListPayment")
{
	$id = $_GET['id'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="5%" style="text-align: center;">NO</th>
					<th rowspan="2" width="12%" style="text-align: center;">DATE</th>
					<th rowspan="2" width="10%" style="text-align: center;">NO. JOURNAL</th>
					<th rowspan="2" width="29%" style="text-align: center;">CASH/BANK</th>	
					<th colspan="2" width="24%" style="text-align: center;">PAYMENT</th>
					<th rowspan="2" width="10%" style="text-align: center;">Exc.RATE</th>	
					<th rowspan="2" width="14%" style="text-align: center;">AMOUNT</th>
					<th rowspan="2" width="15%" style="text-align: center;">CREATED</th>
					<th rowspan="2" width="5%" style="text-align: center;">DEL</th>				
				</tr>
				<tr>	
					<th width="14%" style="text-align: center;">IDR</th>
					<th width="10%" style="text-align: center;">USD</th>	
				</tr>	
			</thead>';
	$t1="select t_jo_tagihan_bayar.*, t_jurnal.id_user, t_jurnal.jumlah, t_jurnal.tgl_jurnal, t_jurnal.no_jurnal, t_jurnal.cur, m_coa.nama_coa,
			t_jo_tagihan.cur as cur_tagihan from
	        t_jo_tagihan_bayar inner join t_jurnal on t_jo_tagihan_bayar.id_jurnal = t_jurnal.id_jurnal
			left join t_jurnal_detil on t_jurnal.id_jurnal = t_jurnal_detil.id_jurnal and t_jurnal_detil.status = 'K'
			left join m_coa on t_jurnal_detil.id_coa = m_coa.id_coa 
			inner join m_bank on t_jurnal_detil.id_coa = m_bank.id_bank
			left join t_jo_tagihan on t_jo_tagihan_bayar.id_tagihan = t_jo_tagihan.id_tagihan
			where t_jo_tagihan_bayar.id_tagihan = '$id' order by t_jurnal.tgl_jurnal";
	$h1=mysqli_query($koneksi, $t1);   
	while ($d1=mysqli_fetch_array($h1))		
	{
		$cur_tagihan = $d1['cur_tagihan'];
		$cur_bayar = $d1['cur_bayar'];
		if($d1['cur_bayar'] == 'IDR')
		{
			$idr = $d1['bayar'] ;	
			$idrx = number_format($idr,2);
			$usdx = '-';
		}else{
			$usd = $d1['bayar'] ;	
			$usdx = number_format($usd,2);
			$idrx = '-';
		}
		$ratex = number_format($d1['kurs'],2);
		$tanggal=ConverTgl($d1['tgl_jurnal']);	
		
		if($cur_tagihan == 'USD' && $cur_bayar == 'IDR')
		{
			$amount = $d1['bayar'] / $d1['kurs'];
		}
		else if($cur_tagihan == 'IDR' && $cur_bayar == 'USD')
		{
			$amount = $d1['bayar'] * $d1['kurs'];
		}
		else
		{
			$amount = $d1['bayar'];
		}	
		
		$amountx = number_format($amount,2);
		$n++;
		$total = $total + $amount;
		$data .= '<tr>						
			<td style="text-align:center">'.$n.'.</td>
			<td style="text-align:center">'.$tanggal.'</td>	
			<td style="text-align:center">'.$d1['no_jurnal'].'</td>
			<td style="text-align:left">'.$d1['nama_coa'].'</td>	
			<td style="text-align:right">'.$idrx.'</td>
			<td style="text-align:right">'.$usdx.'</td> 
			<td style="text-align:right">'.$ratex.'</td>
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
	$totalx = number_format($total,2);
	$data .= '<tr>							
			<td colspan = "5" style="text-align:center;background:#eaebec"></td>	
			<td colspan= "2" style="text-align:right;background:#406a94;color:#fff">Total Payment</td> 
			<td style="text-align:right;background:#406a94;color:#fff">'.$totalx.'</td>';	
	$data .='</tr>';
	$pq = mysqli_query($koneksi, "select * from t_jo_tagihan where id_tagihan = '$id'  ");
	$rq=mysqli_fetch_array($pq);	
	$tagihanx = number_format($rq['tagihan'],2);
	$sisa = $rq['tagihan'] - $total;
	$data .= '<tr>							
			<td colspan = "5" style="text-align:center;background:#eaebec"></td>	
			<td colspan= "2" style="text-align:right;background:#e48f0f;color:#fff">Bill Invoice</td> 
			<td style="text-align:right;background:#e48f0f;color:#fff">'.$tagihanx.'</td>';	
	$data .='</tr>';
	$sisax = number_format($sisa,2);
	$data .= '<tr>							
			<td colspan = "5" style="text-align:center;background:#eaebec"></td>	
			<td colspan= "2" style="text-align:right;background:#4bc343;color:#fff"><b>Balance</b></td> 
			<td style="text-align:right;background:#4bc343;color:#fff"><b>'.$sisax.'</b></td>';	
	$data .='</tr>';
    $data .= '</table>';
	
	$sql = "update t_jo_tagihan set bayar = '$total' where id_tagihan = '$id'	";
	$hasil=mysqli_query($koneksi, $sql);
			
    echo $data;	

}else if ($_POST['type'] == "DelPayment"){
	$id = $_POST['id']; 
	$pq = mysqli_query($koneksi, "select t_jo_tagihan_bayar.*, t_jurnal.jumlah, t_jurnal.cur, t_jo_tagihan.cur as cur_tagihan, t_jo_tagihan.no_tagihan from 
						t_jo_tagihan_bayar inner join t_jurnal on t_jo_tagihan_bayar.id_jurnal = t_jurnal.id_jurnal 
						inner join t_jo_tagihan on t_jo_tagihan_bayar.id_tagihan = t_jo_tagihan.id_tagihan
						where t_jo_tagihan_bayar.id_bayar = '$id'  ");
	$rq=mysqli_fetch_array($pq);	
	$no_tagihan = $rq['no_tagihan'];
	$cur_tagihan = $rq['cur_tagihan'];
	$cur_bayar = $rq['cur_bayar'];
		
	if($cur_tagihan == 'USD' && $cur_bayar == 'IDR')
	{
		$bayar = $rq['bayar']/$rq['kurs'] ;
	}
	else if($cur_tagihan == 'IDR' && $cur_bayar == 'USD')
	{
		$bayar = $rq['bayar']*$rq['kurs'] ;	
	}else{
		$bayar = $rq['bayar'];
	}
	
	$update = mysqli_query($koneksi, "UPDATE t_jo_tagihan set bayar =  bayar - '$bayar'  where id_tagihan ='$rq[id_tagihan]' ");

	$id_jurnal = $rq['id_jurnal'];
	Del_Jurnal($id_jurnal);
	
	$mode = strtoupper($mode);
	$tanggalku = date("Y-m-d h:i:sa");
	$ket = "DEL PAYMENT CN NO. $no_tagihan";
	$audit = mysqli_query($koneksi,"INSERT INTO m_audit (tanggal,created,ket) values ('$tanggalku','$id_user','$ket')" );
	
    $query = "DELETE FROM  t_jo_tagihan_bayar WHERE id_bayar = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysql_error());
    }		
	
}

?>