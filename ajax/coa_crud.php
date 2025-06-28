<?php
session_start(); 
include "../session_log.php"; 
include("../koneksi.php");
include "../lib.php";


$pq = mysqli_query($koneksi, "select * from m_role_akses where id_role = '$id_role'  and id_menu ='14' ");
$rq=mysqli_fetch_array($pq);	
$m_edit = $rq['m_edit'];
$m_add = $rq['m_add'];
$m_del = $rq['m_del'];
$m_view = $rq['m_view'];
$m_exe = $rq['m_exe'];

if ($_GET['type'] == "read")
{
	$tahun = $_GET['tahun'];
	$search_name = $_GET['search_name'];
	$field = $_GET['field'];
	$hal = $_GET['hal'];
	
	$data = '<table class="table table-hover table-striped" style="width:100%">
	<thead style="font-weight:500px !important">
		<tr>							
		<th  width="86%" style="text-align: center;">ACCOUNT NAME</th>	
		<th  width="3%" style="text-align: center;"></th>
		<th  width="3%" style="text-align: center;">ADD<br>SUB</th>
		<th  width="3%" style="text-align: center;">EDIT</th>		
		<th  width="3%" style="text-align: center;">DEL</th>			
		</tr>
	</thead>';			
			
	
	$query1 = mysqli_query($koneksi, "select * from m_coa where id_parent ='0' order by id_coa ");		
	while($row1 = mysqli_fetch_assoc($query1))
    {
		if($row1['kunci'] == '1')
		{
			$def = '<img border="0" src="./img/key.png" width="10px" height="12px" style="margin:-5px;" >';
		}
		else
		{
			$def = '';
		}
		$type_coa = $row1['type'];
		$data .='<tr>';			
		$data .='<td  style="text-align: left;"><b>'.$row1['kode_coa'].' - '.$row1['nama_coa'].'</b></th>';
		$data .='<td>'.$def.'</td>';
		$data .= '<td>
					<button class="btn btn-block btn-default" 
					style="margin:-3px;border-radius:0px" type="button" 
					onClick="javascript:AddSub('.$row1['id_coa'].')"  >
					<span class="fa   fa-plus-square-o" ></span>
					</button>
				</td>';
		$data .='<td></td>';
		$data .='<td></td>';
		$data .='</tr>';	
		
		$query2 = mysqli_query($koneksi, "select * from m_coa where id_parent ='$row1[id_coa]' order by id_coa");		
		while($row2 = mysqli_fetch_assoc($query2))
		{
			$sw = "update m_coa set type = '$type_coa' where id_coa = '$row2[id_coa]' ";
			$hw = mysqli_query($koneksi, $sw);			
			if($row2['kunci'] == '1')
			{
				$def = '<img border="0" src="./img/key.png" width="10px" height="12px" style="margin:-5px;" >';
			}
			else
			{
				$def = '';
			}
			$data .='<tr>';				
			$data .='<td  style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img border="0" src="./img/bullet1.png" >&nbsp;&nbsp;<b>'.$row2['kode_coa'].' - '.$row2['nama_coa'].'</b></th>';			
			$data .='<td>'.$def.'</td>';			
			if($row2['id_parent'] <> '3' and $row2['id_parent'] <> '4' and $row2['id_parent'] <> '5' ){
			$data .= '<td>
					<button class="btn btn-block btn-default" 
					style="margin:-3px;border-radius:0px" type="button" 
					onClick="javascript:AddSub('.$row2['id_coa'].')"  >
					<span class="fa   fa-plus-square-o" ></span>
					</button>
				</td>';
			}else{
				$data .= '<td></td>';
			}	
			if($m_edit == '1' and $row2['kunci'] <> '1'){			
				$data .= '<td>
					  <button class="btn btn-block btn-default" 
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="javascript:GetData('.$row2['id_coa'].')"  >
						<span class="fa fa-edit " ></span>
				  	 </button></td>';
			}else{
				$data .='<td></td>';
			}			
			if($row2['sub'] <> '1' and $m_del == '1' and $row2['kunci'] <> '1'){
			$data .= '<td>
					<button class="btn btn-block btn-default" 
					style="margin:-3px;border-radius:0px" type="button" 
					onClick="javascript:DelCoa('.$row2['id_coa'].')"  >
					<span class="fa fa-close " ></span>
					</button></td>';
			}
			else
			{
					$data .='<td></td>';
			}
			$data .='</tr>';
			
			$query3 = mysqli_query($koneksi, "select * from m_coa where id_parent ='$row2[id_coa]' order by id_coa");		
			while($row3 = mysqli_fetch_assoc($query3))
			{
				$sw = "update m_coa set type = '$type_coa' where id_coa = '$row3[id_coa]' ";
				$hw = mysqli_query($koneksi, $sw);			
				if($row3['kunci'] == '1')
				{
					$def = '<img border="0" src="./img/key.png" width="10px" height="12px" style="margin:-5px;" >';
				}
				else
				{
					$def = '';
				}		
				$data .='<tr>';
				$data .='<td  style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img border="0" src="./img/bullet1.png" >&nbsp;&nbsp;'.$row3['kode_coa'].' - '.$row3['nama_coa'].'</th>';			
				$data .='<td>'.$def.'</td>';
				if($row2['id_parent'] <> '3' and $row2['id_parent'] <> '4' and $row2['id_parent'] <> '5' and $row2['id_parent'] <> '6' ){
					$data .= '<td>
						<button class="btn btn-block btn-default" 
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="javascript:AddSub('.$row3['id_coa'].')"  >
						<span class="fa   fa-plus-square-o" ></span>
						</button>
					</td>';
				}else{
					$data .='<td></td>';
				}
				if($m_edit == '1' and $row3['kunci'] <> '1'){			
					$data .= '<td>
					  <button class="btn btn-block btn-default" 
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="javascript:GetData('.$row3['id_coa'].')"  >
						<span class="fa fa-edit " ></span>
				  	 </button></td>';
				}else{
					$data .='<td></td>';
				}
				if($row3['sub'] <> '1' and $m_del == '1' and $row3['kunci'] <> '1' ){
					$data .= '<td>
					<button class="btn btn-block btn-default" 
					style="margin:-3px;border-radius:0px" type="button" 
					onClick="javascript:DelCoa('.$row3['id_coa'].')"  >
					<span class="fa fa-close " ></span>
					</button></td>';
					}
					else
					{
						$data .='<td></td>';
					}
				$data .='</tr>';
				
				$query4 = mysqli_query($koneksi, "select * from m_coa where id_parent ='$row3[id_coa]' order by id_coa");		
				while($row4 = mysqli_fetch_assoc($query4))
				{
					$sw = "update m_coa set type = '$type_coa' where id_coa = '$row4[id_coa]' ";
					$hw = mysqli_query($koneksi, $sw);			
					if($row3['kunci'] == '1')
					{
						$def = '<img border="0" src="./img/key.png" width="10px" height="12px" style="margin:-5px;" >';
					}
					else
					{
						$def = '';
					}					
					$data .='<tr>';
					$data .='<td  style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img border="0" src="./img/bullet1.png" >&nbsp;&nbsp;'.$row4['kode_coa'].' - '.$row4['nama_coa'].'</th>';				
					$data .='<td>'.$def.'</td>';
					$data .= '<td>
					</td>';
					if($m_edit == '1' and $row4['kunci'] <> '1'){			
						$data .= '<td>
						  <button class="btn btn-block btn-default" 
							style="margin:-3px;border-radius:0px" type="button" 
							onClick="javascript:GetData('.$row4['id_coa'].')"  >
							<span class="fa fa-edit " ></span>
						 </button></td>';
					}else{
						$data .='<td></td>';
					}
					if($row4['sub'] <> '1' and $m_del == '1' and $row4['kunci'] <> '1' ){
						$data .= '<td>
						<button class="btn btn-block btn-default" 
						style="margin:-3px;border-radius:0px" type="button" 
						onClick="javascript:DelCoa('.$row4['id_coa'].')"  >
						<span class="fa fa-close " ></span>
						</button></td>';
						}
						else
						{
							$data .='<td></td>';
						}
					$data .='</tr>';	
					
				}				
			}	
		}				
	}
	
    echo $data;

}else if ($_POST['type'] == "simpan"){		
	if($_POST['mode'] != '' )
	{		
		$id = $_POST['id'];
		$nama_sub = $_POST['nama_sub'];		
		$jenis = $_POST['jenis'];
		$level = $_POST['level'];
		$kode_sub = $_POST['kode_sub'];
		$saldo = $_POST['saldo'];
		$mode = $_POST['mode'];		
		$kode_sub = strtoupper($kode_sub);
		
		
		$noUrut = (int) $level;   
		$noUrut++; 
		if($mode == 'Add')
		{			
			$sql = "INSERT INTO m_coa (id_parent,kode_coa,nama_coa,level,sub,type,saldo,id_user) values
					('$id','$kode_sub','$nama_sub','$noUrut','0','$jenis','$saldo','$_SESSION[id_user]') ";
			$hasil=mysqli_query($koneksi, $sql);
			
			$sql = "update m_coa set sub ='1' where id_coa = '$id'";
			$hasil=mysqli_query($koneksi, $sql);			
		}
		else
		{
			$sql = "update m_coa set
					nama_coa = '$nama_sub',
					kode_coa = '$kode_sub'
					where id_coa = '$id'
					";
			$hasil=mysqli_query($koneksi, $sql);
		}
		if (!$hasil) {
	        //exit(mysql_error());
			echo "Data error...!";
	    }
		else
		{
			echo "Data saved!";
		}
	}	
}else if ($_POST['type'] == "detil"){
	$id = $_POST['id'];	
    $query = "select * from m_coa where id_coa  = '$id'";
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
	
}else if ($_POST['type'] == "delData"){
	$id = $_POST['id'];   
	
	$pqx = mysqli_query($koneksi, "select * from t_jurnal_detil where id_coa = '$id' ");
	$rqx = mysqli_fetch_array($pqx);
	
	if(empty($rqx['id']))
	{
		$pq = mysqli_query($koneksi, "select * from m_coa where id_coa = '$id' ");
		$rq=mysqli_fetch_array($pq);	
		
		$pq1 = mysqli_query($koneksi, "select count(*) as jml from m_coa where id_parent = '$rq[id_parent]' ");
		$rq1=mysqli_fetch_array($pq1);
		
		if($rq1['jml'] == '1')
		{
			$sql = "update m_coa set sub = '0'	where id_coa = '$rq[id_parent]'	";
			$hasil = mysqli_query($koneksi, $sql);
		}	
		$query = "DELETE FROM m_coa WHERE id_coa = '$id' ";
		if (!$result = mysqli_query($koneksi, $query)) {
			exit(mysqli_error());
		}
	}else{
		echo "the account already has a transaction..";
	}
	
	


}else if ($_GET['type'] == "ListCoa")
{
	
	$cari = $_GET['cari'];
	$data = '<table class="table table-hover table-striped" style="width:100%">
			<thead style="font-weight:500px !important">
				<tr>	
					<th rowspan="2" width="100%" style="text-align: center;">ACCOUNT NAME</th>
				</tr>
			</thead>';	
	$SQL = "select * from m_coa where sub = '0' and  nama_coa LIKE '%$cari%'order by nama_coa LIMIT 0,15";
	$query = mysql_query($SQL);	
	if (!$result = $query) {
        exit(mysql_error());
    }
    if(mysql_num_rows($result) > 0)
    {
    	while($row = mysql_fetch_assoc($result))
    	{	
			$posisi++;	
			$data .= '<tr>	
				<td style="text-align:left"><a href="#" onclick="PilihCoa('.$row['id_coa'].')" >'.$row[nama_coa].'</a></td>';	
				$data .='</tr>';
    		$number++;
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