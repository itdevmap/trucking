<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses_tr where id_role = '$id_role'  and id_menu ='16' ");
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
					<th rowspan="2" width="10%" style="text-align: center;">#ID USER</th>					
					<th rowspan="2" width="35%" style="text-align: center;">USER NAME</th>
					<th rowspan="2" width="10%" style="text-align: center;">ROLE</th>
					<th rowspan="2" width="10%" style="text-align: center;">NO. HANDPHONE</th>
					<th rowspan="2" width="23%" style="text-align: center;">EMAIL</th>
					<th rowspan="2" width="6%" style="text-align: center;">STATUS</th>
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
	$SQL = "select m_user_tr.*, m_role_tr.nama_role from 
			m_user_tr inner join m_role_tr on m_user_tr.id_role = m_role_tr.id_role 
			where m_user_tr.nama_user LIKE '%$cari%' and m_user_tr.id_role <> '0' order by m_user_tr.nama_user
			 LIMIT $offset, $jmlperhalaman";	
	$query = mysqli_query($koneksi, $SQL);	
	if (!$result = $query) {
        exit(mysqli_error($koneksi));
    }
    if(mysqli_num_rows($result) > 0)
    {
    	while($row = mysqli_fetch_assoc($result))
    	{	
			$posisi++;		
				$data .= '<tr>							
				<td style="text-align:center">'.$posisi.'.</td>	
				<td style="text-align:center"><b>'.$row['id_user'].'</b></td>
				<td style="text-align:left">'.$row['nama_user'].'</td>
				<td style="text-align:center">'.$row['nama_role'].'</td>
				<td style="text-align:center">'.$row['telp'].'</td>
				<td style="text-align:center">'.$row['email'].'</td>';					
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
				if($m_edit == '1'  && $row['id_user'] != 'admin'){
					$data .= '<td>
								<button class="btn btn-block btn-default" title="Edit"
									style="margin:-3px;margin-left:1px;border-radius:0px" type="button" 
									onClick="javascript:GetData('.$row['id'].')"  >
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
				$pq = mysqli_query($koneksi, "select count(*) as jml from m_user_tr where nama_user LIKE '%$cari%' ");					
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
		$id_user = $_POST['id_user'];
		$nama_user = $_POST['nama_user'];
		$reset = $_POST['reset'];
		$password = $_POST['password'];
		$pass = $_POST['password'];
		$role = $_POST['role'];
		$email = $_POST['email'];
		$telp = $_POST['telp'];
		$nama_bank = $_POST['nama_bank'];
		$no_rek = $_POST['no_rek'];
		$stat = $_POST['stat'];
		$mode = $_POST['mode'];			
		$password=md5($password);
		$nama_user = strtoupper($nama_user);
		
		$tgl = date("Y-m-d h:i:s");
		if($mode == 'Add')
		{			
			$sql = "INSERT INTO m_user_tr (id_user,nama_user,password,pass,id_role,id_pt,email,create_date,status,photo,telp,nama_bank,no_rek) values
					('$id_user','$nama_user','$password','$pass','$role','$id_pt','$email','$tgl','$stat','photo/no.jpg','$telp','$nama_bank','$no_rek') ";
			$hasil=mysqli_query($koneksi, $sql);
		}
		else
		{
			$sql = "update m_user_tr set nama_user = '$nama_user',  id_role = '$role',email = '$email',status = '$stat',
				    telp='$telp',nama_bank='$nama_bank', no_rek='$no_rek' where id = '$id' 	";
			$hasil=mysqli_query($koneksi, $sql);
			
			if($reset == '1')
			{
				$password=md5($id_user);
				$sql = "update m_user_tr set password = '$password', pass ='$id_user' where id = '$id' 	";
				$hasil=mysqli_query($koneksi, $sql);
			}
		}
		
		
		if (!$hasil) {
	        			
			exit(mysqli_error($koneksi));
			echo "Data telah terdaftar...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}	
	
}else if ($_POST['type'] == "detil"){
	$id = $_POST['id'];	
    $query = "select * from m_user_tr where id  = '$id'";
    if (!$result = mysqli_query($koneksi, $query)) {
        exit(mysqli_error($koneksi));
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

	
}

?>