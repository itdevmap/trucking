<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='13' ");
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
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>					
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="60%" style="text-align: center;">NAMA BIAYA</th>
					<th rowspan="2" width="5%" style="text-align: center;">SAP CODE SIM</th>
					<th rowspan="2" width="5%" style="text-align: center;">SAP CODE AMA</th>
					<th rowspan="2" width="5%" style="text-align: center;">SAP CODE PTL</th>
					<th rowspan="2" width="5%" style="text-align: center;">SAP CODE AA</th>
					<th rowspan="2" width="9%" style="text-align: center;">CREATED</th>
					<th rowspan="2" width="5%" style="text-align: center;">STATUS</th>
					<th rowspan="2" width="3%" style="text-align: center;">EDIT</th>	
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
	$SQL = "select * from m_cost_tr where nama_cost LIKE '%$cari%' order by nama_cost  LIMIT $offset, $jmlperhalaman";	
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;		
				$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:left">'.$row['nama_cost'].'</td>
				<td style="text-align:center">'.$row['sapitemcode_sim'].'</td>
				<td style="text-align:center">'.$row['sapitemcode_ama'].'</td>
				<td style="text-align:center">'.$row['sapitemcode_ptl'].'</td>
				<td style="text-align:center">'.$row['sapitemcode_aa'].'</td>
				<td style="text-align:center">'.$row['id_user'].'</td>';					
				if($row['status'] =='0'){
				$data .= '<td style="text-align:center">
					<span class="label label-danger" style="font-weight:normal;font-size:11px;text-shadow:none;padding:3px;">
						&nbsp;&nbsp;In Active&nbsp;&nbsp;
					</span>
					</td>';
				} else if($row['status'] =='1'){
				$data .= '<td style="text-align:center"> 
					<span class="label label-success" style="font-weight:normal;font-size:11px;text-shadow:none;padding:3px;">
					&nbsp;&nbsp;&nbsp;&nbsp; Active &nbsp;&nbsp;&nbsp;
					</span>
					</td>';
				}
				if($m_edit == '1' && $row['id_cost'] !='1' ){
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="javascript:GetData('.$row['id_cost'].')"  >
									<span class="fa fa-edit " ></span>
								</button></td>';
				}
				else
				{
					$data .='<td></td>';
				}
				$data .='</tr>';
    		$number++;
    	}		
    }
    else
    {
    	$data .= '<tr><td colspan="7">Records not found!</td></tr>';
    }
    $data .= '</table>';
	
	$data .= '<div class="paginate paginate-dark wrapper">
				<ul>';
				$pq = mysqli_query($koneksi, "select count(*) as jml from m_cost_tr where nama_cost LIKE '%$cari%' ");					
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

}else if ($_POST['type'] == "add"){		
	if($_POST['mode'] != '' )
	{	
		$id = $_POST['id'];
		$cost_name = addslashes(trim(strtoupper($_POST['cost_name'])));
		$stat = $_POST['stat'];
		$st = $_POST['st'];
		$sim = addslashes(trim(strtoupper($_POST['sim'])));
		$ama = addslashes(trim(strtoupper($_POST['ama'])));
		$ptl = addslashes(trim(strtoupper($_POST['ptl'])));
		$aa = addslashes(trim(strtoupper($_POST['aa'])));
		$mode = $_POST['mode'];
		if($mode == 'Add')
		{			
			$sql = "INSERT INTO m_cost_tr (nama_cost,jenis,id_user,status, sapitemcode_sim, sapitemcode_ama, sapitemcode_ptl, sapitemcode_aa) values
					('$cost_name','$st','$id_user','$stat', '$sim', '$ama', '$ptl', '$aa')";
			$hasil=mysqli_query($koneksi, $sql);
		}
		else
		{
			$sql = "update m_cost_tr set 
					nama_cost = '$cost_name',
					status = '$stat',
					jenis = '$st',
					id_user = '$id_user',
					sapitemcode_sim = '$sim',
					sapitemcode_ama = '$ama',
					sapitemcode_ptl = '$ptl',
					sapitemcode_aa = '$aa'
					where id_cost = '$id'	";
			$hasil=mysqli_query($koneksi, $sql);
		}
		if (!$hasil) {
	        			
			//exit(mysqli_error());
			echo "Cost Name has found...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}	
	
}else if ($_POST['type'] == "detil"){
	$id = $_POST['id'];	
    $query = "select * from m_cost_tr where id_cost  = '$id'";
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


}else if ($_GET['type'] == "ListCost")
{	
	$cari = $_GET['cari'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>
					<th width="6%" style="text-align: center;">NO</th>
					<th width="87%" style="text-align: center;">COST NAME</th>
					<th width="7%" style="text-align: center;">ADD</th>
				</tr>
			</thead>';	
	$offset = (($page * $jmlperhalaman) - $jmlperhalaman);  
	$posisi = (($page * $jmlperhalaman) - $jmlperhalaman);
	
	$SQL = "select * from m_cost_tr where nama_cost LIKE '%$cari%' and status = '1'  order by nama_cost LIMIT 0, 25";
	
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error());
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$n++;
			$data .= '<tr>';
			$data .= '<td style="text-align:center">'.$n.'.</td>';	
			$data .= '<td style="text-align:left"><a href="#" onclick="PilihCost('.$row['id_cost'].')" >'.$row['nama_cost'].'</a></td>';
			$data .= '<td style="text-align:center">
					<button type="button" class="btn btn-default" onClick="javascript:PilihCost('.$row['id_cost'].')" 
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
	
}

?>