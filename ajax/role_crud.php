<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";



if ($_GET['type'] == "read")
{
	$hal = $_GET['hal'];
	$search_name = $_GET['search_name'];	
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="3%" style="text-align: center;">NO</th>
					<th rowspan="2" width="89%" style="text-align: center;">NAMA ROLE</th>
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
	
	$SQL = "select * from m_role_tr where nama_role LIKE '%$search_name%' order by nama_role ";
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
				<td style="text-align:left">'.$row['nama_role'].'</td> ';
				if($row['status'] =='0'){
				$data .= '<td style="text-align:center">
					<span class="label label-danger" style="font-weight:normal;font-size:11px;text-shadow:none;padding:3px;">
						&nbsp;&nbsp;Tidak Aktif&nbsp;&nbsp;
					</span>
					</td>';
				} else if($row['status'] =='1'){
				$data .= '<td style="text-align:center">
					<span class="label label-success" style="font-weight:normal;font-size:11px;text-shadow:none;padding:3px;">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Aktif &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</span>
					</td>';
				}
				
					$xy1="Edit|$row[id_role]|$page";
					$xy1=base64_encode($xy1);
					$link = "'role_data.php?id=$xy1'";
					$data .= '<td>
							<button class="btn btn-block btn-default" 
								style="margin:0px;margin-bottom:3px;margin-left:-3px;border-radius:0px;" type="button" 
							onClick="window.location.href = '.$link.' " >
							<span class="fa fa-edit"></span>
				</button>
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

}else if ($_POST['type'] == "add"){		
	if($_POST['mode'] != '' )
	{		
		$id = $_POST['id'];
		$nama_cabang = $_POST['nama_cabang'];
		$pimpinan = $_POST['pimpinan'];
		$alamat = $_POST['alamat'];
		$telp = $_POST['telp'];
		$email = $_POST['email'];
		$stat = $_POST['stat'];
		$mode = $_POST['mode'];	
		$nama_cabang = strtoupper($nama_cabang);
		
		$tgl = date("Y-m-d h:i:s");
		if($mode == 'Add')
		{			
			$sql = "INSERT INTO m_cabang (nama_cabang,pimpinan,alamat,telp,date,email,status) values
					('$nama_cabang','$pimpinan','$alamat','$telp','$tgl','$email','$stat') ";
			$hasil=mysql_query($sql);
		}
		else
		{
			$sql = "update m_cabang set nama_cabang = '$nama_cabang', pimpinan ='$pimpinan', alamat = '$alamat',
					telp = '$telp', email = '$email',status = '$stat' where id_cabang = '$id'	";
			$hasil=mysql_query($sql);
		}
		
		
		if (!$hasil) {
	        			
			exit(mysql_error());
			echo "Data telah terdaftar...!";
	    }
		else
		{	
			echo "Data saved!";
		}
	}	
}else if ($_POST['type'] == "detil"){
	$id = $_POST['id'];	
    $query = "select * from m_cabang where id_cabang  = '$id'";
    if (!$result = mysql_query($query)) {
        exit(mysql_error());
    }
    $response = array();
    if(mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) {
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