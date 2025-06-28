<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses where id_role = '$id_role'  and id_menu ='21' ");
$rq=mysqli_fetch_array($pq);	
$m_edit = $rq['m_edit'];
$m_add = $rq['m_add'];
$m_del = $rq['m_del'];
$m_view = $rq['m_view'];
$m_exe = $rq['m_exe'];

if ($_GET['type'] == "Read")
{
	$tgl1 = $_GET['tgl1'];
	$tgl2 = $_GET['tgl2'];
	$cari = trim($_GET['cari']);
	$field = $_GET['field'];
	$stat = $_GET['stat'];
	$hal = $_GET['hal'];
	$paging = $_GET['paging'];
	
	$tgl1x = ConverTglSql($tgl1);
	$tgl2x = ConverTglSql($tgl2);
	
	if($field == 'No Jurnal')
	{
		$f = 'no_jurnal';
	}else if($field == 'Keterangan'){
		$f = 'ket';
	}else{
		$f = 'no_jurnal';
	}
	
	if($stat == 'Post')
	{
		$stat = '1';
	}else if($stat == 'Unpost')
	{
		$stat = '0';
	}		
	$data .= '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="7%" style="text-align: center;">TANGGAL</th>
					<th rowspan="2" width="6%" style="text-align: center;">#NO<br>JURNAL</th>	
					<th rowspan="2" width="59%" style="text-align: center;">KETERANGAN</th>
					<th rowspan="2" width="6%" style="text-align: center;">JUMLAH</th>
					<th rowspan="2" width="6%" style="text-align: center;">CREATED</th>
					<th rowspan="2" width="5%" style="text-align: center;">STATUS</th>
					<th rowspan="2" width="2%" style="text-align: center;">EDIT</th>
					<th rowspan="2" width="2%" style="text-align: center;">POST</th>
					<th rowspan="2" width="2%" style="text-align: center;">VIEW</th>
					<th rowspan="2" width="2%" style="text-align: center;">DEL</th>';
				$data .='</tr>
				
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
		$SQL = "SELECT * FROM t_jurnal where tgl_jurnal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' order by tgl_jurnal desc, no_jurnal  desc 
			LIMIT $offset, $jmlperhalaman";	
	}else{
		$SQL = "SELECT * FROM t_jurnal where tgl_jurnal between '$tgl1x' and '$tgl2x' and $f LIKE '%$cari%' and status = '$stat' order by tgl_jurnal desc, no_jurnal  desc 
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
			$tanggal = ConverTgl($row['tgl_jurnal']);
			if(empty($row['jumlah']))
			{
				$jumlah = '<span class="label label-danger" style="font-weight:normal;font-size:11px;text-shadow:none;padding:3px;">
						 &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No Balance&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						  </span>';
			}else{
				$jumlah=number_format($row['jumlah'],0);
			}
			$kurs=number_format($row['kurs'],0);
			if($row['cur'] == 'IDR')
			{
				$idr = $row['jumlah'] ;
			}else{
				$idr = $row['jumlah'] * $row['kurs'];
			}
			$idrx = number_format($idr,2);
			$total = $total + $row['jumlah'];
			$posisi++;		
				$data .= '<tr style="margin:-3px">			
				<td   style="text-align:center">'.$posisi.'.</td>	
				<td   style="text-align:center">'.$tanggal.'</td>	
				<td   style="text-align:center">'.$row['no_jurnal'].'</td>	
				<td   style="text-align:left">'.$row['ket'].'</td>
				<td   style="text-align:right">'.$jumlah.'</td>
				<td   style="text-align:center">'.$row['id_user'].'</td>';
				if($row['status'] =='0'){
					$data .= '<td style="text-align:center;">
					<span class="label label-danger" style="margin:-3px;font-weight:normal;font-size:11px;text-shadow:none;padding:3px;">
						&nbsp;Unpost&nbsp;
					</span>
					</td>';
					} else if($row['status'] =='1'){
					$data .= '<td style="text-align:center">
					<span class="label label-success" style="margin:-3px;font-weight:normal;font-size:11px;text-shadow:none;padding:3px;">
					&nbsp;&nbsp;&nbsp; Post &nbsp;&nbsp;
					</span>
					</td>';
				}
				if($m_edit == '1' && $row['status'] == '0' ) {
					$xy1="Edit|$row[id_jurnal]";
					$xy1=base64_encode($xy1);
					$link = "'jurnal_data.php?id=$xy1'";
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
				if($row['status'] == '0' and $row['jumlah'] <> 0 ){
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Posting"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="javascript:Posting('.$row['id_jurnal'].')"  >
									<span class="fa fa-check-square-o" ></span>
								</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}
				$data .='<td   style="text-align:center">				
					<button class="btn btn-block btn-default" title="View"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="javascript:View('.$row['id_jurnal'].')"  >
									<span class="glyphicon glyphicon-search" ></span>
								</button>	
				</td>';
				if($m_del == '1' && $row['jenis'] != '1' ){
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Delete"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="javascript:DelJurnal('.$row['id_jurnal'].')"  >
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
		/*
		$totalx = number_format($total,2);
			$data .= '<tr>';
		$data .= '<td colspan= "4" style="text-align:right;background:#eaebec;color:#000"><b>Total  :&nbsp;&nbsp;&nbsp;</b></td>	
					<td style="text-align:right;background:#008d4c;color:#fff"><b>'.$totalx.'</b></td>
					<td colspan= "5" style="text-align:right;background:#eaebec"</td>	';
		$data .= '</tr>';			
		*/
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
					$pq = mysqli_query($koneksi, "select count(id_jurnal) as jml from t_jurnal  where  tgl_jurnal between '$tgl1x' and '$tgl2x' 
					 and $f LIKE '%$cari%' ");
				}else{
					$pq = mysqli_query($koneksi, "select count(id_jurnal) as jml from t_jurnal  where  tgl_jurnal between '$tgl1x' and '$tgl2x' 
					 and $f LIKE '%$cari%' and status = '$stat' ");
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
	
	
}else if ($_POST['type'] == "Del_Jurnal"){
	$id = $_POST['id'];   
	
	$pq = mysqli_query($koneksi,"select * from t_jurnal where id_jurnal = '$id'   ");
	$rq=mysqli_fetch_array($pq);	
	$no = $rq['no_jurnal'];
	
	
	$id_jurnal = $id;
	Del_Jurnal($id_jurnal);
	
	
			
	$mode = strtoupper($mode);
	$tanggalku = date("Y-m-d h:i:sa");
	$ket = "DEL JURNAL NO. $no";
	$audit = mysqli_query($koneksi,"INSERT INTO m_audit (tanggal,created,ket) values ('$tanggalku','$id_user','$ket')" );
			
    $query = "DELETE FROM t_jurnal WHERE id_jurnal = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }	

}else if ($_GET['type'] == "view")
{
	$id = $_GET['id'];
	$edit = $_GET['edit'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="60%" style="text-align: center;">ACCOUNT NAME</th>
					<th rowspan="2" width="15%" style="text-align: center;">DEBET</th>	
					<th rowspan="2" width="15%" style="text-align: center;">CREDIT</th>';
					
					if(!empty($edit)){	
					$data.='<th rowspan="2" width="5%" style="text-align: center;">EDIT</th>	
					<th rowspan="2" width="5%" style="text-align: center;">DEL</th>	';
					}
			$data .= '</tr>
			</thead>';	

	$SQL = "select t_jurnal_detil.*,m_coa.nama_coa from t_jurnal_detil
			inner join m_coa on t_jurnal_detil.id_coa = m_coa.id_coa where t_jurnal_detil.id_jurnal = '$id'  order by t_jurnal_detil.status, id ";	
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{
			$pq = mysqli_query($koneksi, "select * from m_bank where id_bank = '$row[id_bank]' ");
			$rq=mysqli_fetch_array($pq);	
			if(!empty($rq['nama_bank']))
			{
				//$bank = "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$rq[nama_bank] ($rq[cur]) - $rq[no_bank]";
			}else{
				$bank='';
			}
			if($row['status']=='D')
			{
				$d =number_format($row['jumlah'],0);
				$k ='';
				$coa = $row['nama_coa'];
				$td = $td + $row['jumlah'];
			}
			else
			{
				$k =number_format($row['jumlah'],0);
				$d ='';
				$coa = '<img border="0" src="./img/bullet1.png" >&nbsp;&nbsp;'.$row['nama_coa'].'';
				$tk = $tk + $row['jumlah'];
			}
			$posisi++;		
				$data .= '<tr>							
				<td style="text-align:left">'.$coa.' '.$bank.'</td>	
				<td style="text-align:right">'.$d.'</td>
				<td style="text-align:right">'.$k.'</td>';	
				if(!empty($edit))
				{
					$data .= '<td>
							<button class="btn btn-block btn-default" 
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:GetData('.$row['id'].')"  >
								<span class="fa fa-edit " ></span>
							</button></td>';
					$data .= '<td>
							<button class="btn btn-block btn-default" 
								style="margin:-3px;border-radius:0px" type="button" 
								onClick="javascript:DelData('.$row['id'].')"  >
								<span class="fa fa-close " ></span>
							</button></td>';		
				}
				$data .='</tr>';
    	}		
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }	
	$tkx =number_format($tk,0);
	$tdx =number_format($td,0);	
	$data .= '<tr>							
				<td style="text-align:right;background:#eaebec;"><b>Total :</b> &nbsp;&nbsp;</td>	
				<td style="text-align:right;background:#e48f0f;color:#fff">'.$tdx.'</td>
				<td style="text-align:right;background:#406a94;color:#fff">'.$tkx.'</td>';	
	$data .='</tr>';
	
	if($tkx == $tdx && $td != 0)
	{
		$data .= '<tr>							
					<td style="text-align:right;background:#eaebec;"></td>	
					<td colspan="2" style="text-align:center;background:#00a65a ;color:#fff">Balance</td>';	
		$data .='</tr>';
		$sql = "update t_jurnal set jumlah = '$tk' where id_jurnal = '$id'";
		$hasil=mysqli_query($koneksi, $sql);	
	}else{
		$data .= '<tr>							
					<td style="text-align:right;background:#eaebec;"></td>	
					<td colspan="2" style="text-align:center;background:#dd4b39 ;color:#fff">No Balance</td>';	
		$data .='</tr>';
		$sql = "update t_jurnal set jumlah = '0' where id_jurnal = '$id'";
		$hasil=mysqli_query($koneksi, $sql);	
	}
	$data .= '</table>';
    echo $data;		
	
}else if ($_POST['type'] == "Posting"){		
	if($_POST['id'] != '' )
	{	
		$id = $_POST['id'];
		
		$pq = mysqli_query($koneksi, "select * from t_jurnal where id_jurnal = '$id' ");
		$rq = mysqli_fetch_array($pq);	
		$cur = $rq['cur'];
		$ptgl = explode("-", $rq['tgl_jurnal']);
		$thn_jurnal = $ptgl[0];
		$bln_jurnal = $ptgl[1];	
		
		$qs = "SELECT max(right(no_jurnal,5)) as maxID FROM t_jurnal where year(tgl_jurnal) = '$thn_jurnal' and month(tgl_jurnal) = '$bln_jurnal'  ";
		$hs = mysqli_query($koneksi, $qs);    
		$ds  = mysqli_fetch_array($hs);
		$idMax = $ds['maxID'];
		if ($idMax == '99999'){
			$idMax='00000';
		}
		$noUrut = (int) $idMax;   
		$noUrut++;  
		if(strlen($noUrut)=='1'){
			$noUrut="0000$noUrut";
			}elseif(strlen($noUrut)=='2'){
			$noUrut="000$noUrut";
			}elseif(strlen($noUrut)=='3'){
			$noUrut="00$noUrut";
			}elseif(strlen($noUrut)=='4'){
			$noUrut="0$noUrut";
		}   
		$ptgl = explode("-", $tgl_jurnal);
		$th = $ptgl[2];
		$year = substr($thn_jurnal,2,2);
		$no_jurnal = "$year$bln_jurnal$noUrut";
		
		
		$tx="select * from t_jurnal_detil where id_jurnal ='$id'  ";
		$hx=mysqli_query($koneksi, $tx);       
		while ($dx=mysqli_fetch_array($hx)){
			
			$id_coa = $dx['id_coa'];
			$nilai = $dx['jumlah'];
			$status = $dx['status'];
			Jurnal_Sum($id_coa, $nilai, $cur, '$status' , $bln_jurnal, $thn_jurnal);
		}
		$sql = "update t_jurnal set status = '1', no_jurnal = '$no_jurnal' where id_jurnal = '$id' ";
		mysqli_query($koneksi, $sql);
	}
	
}else if($_GET['type'] == "ListCoa")
{
	$cari = $_GET['cari'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>			
					<th rowspan="2" width="90%" style="text-align: center;">ACCOUNT NAME</th>
					<th rowspan="2" width="10%" style="text-align: center;">ADD</th>					
				</tr>
			</thead>';	
	$jmlperhalaman = 20;		
	$SQL = "select * from m_coa where  nama_coa  LIKE '%$cari%'	and sub ='0' order by nama_coa LIMIT 0, $jmlperhalaman";
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$n++;
			$tanggal=ConverTgl($row['jo_date']);
			$data .= '<tr>	
				<td style="text-align:left"><a href="#" onclick="PilihCoa('.$row['id_coa'].')" >'.$row['nama_coa'].'</a></td>';
				$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihCoa('.$row['id_coa'].')" 
					style="margin:-3px;width:100%;padding:1px;border-radius:1px">Add</button>
					</td>';
				$data .='</tr>';
    		$number++;
    	}	
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
    $data .= '</table>';
    echo $data;		

}else if ($_POST['type'] == "detil_coa"){
	$id = $_POST['id'];	
    $query = "select * from m_coa where id_coa = '$id' ";
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

}else if ($_POST['type'] == "AddAcount"){		
	if($_POST['mode'] != '' )
	{		
		$id = $_POST['id'];
		$id_coa = $_POST['id_coa'];
		$jumlah = $_POST['jumlah'];
		$id_jurnal = $_POST['id_jurnal'];
		$stat = $_POST['stat'];
		$mode = $_POST['mode'];
		$jumlah = str_replace(",","", $jumlah);
		
		if($mode == 'Add')
		{			
			$sql = "INSERT INTO t_jurnal_detil (id_jurnal,id_coa,id_bank,jumlah,status) values
					('$id_jurnal','$id_coa','$id_coa','$jumlah','$stat') ";
			$hasil=mysqli_query($koneksi, $sql);
		}else{
			$sql = "update t_jurnal_detil set
					id_coa = '$id_coa',
					id_bank = '$coa',
					jumlah= '$jumlah',
					status = '$stat'
					where id = '$id'
					";
			$hasil=mysqli_query($koneksi, $sql);
		}
		if (!$hasil) {
	        //exit(mysql_error());
			echo "Data Account sudah terdaftar...!";
	    }
		else
		{
			echo "Data saved! ";
		}
	}	
	
}else if ($_POST['type'] == "detil"){
	$id = $_POST['id'];	
    $query = "select t_jurnal_detil.*, m_coa.nama_coa from t_jurnal_detil inner join m_coa on t_jurnal_detil.id_coa = m_coa.id_coa where t_jurnal_detil.id  = '$id'";
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
	
}else if ($_POST['type'] == "DelDetil"){
	$id = $_POST['id'];   
    $query = "DELETE FROM t_jurnal_detil WHERE id = '$id' ";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error());
    }	
	
}

?>