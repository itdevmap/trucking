<?php
include "../koneksi.php";

$id_jo = isset($_POST['id_jo']) ? $_POST['id_jo'] : '';

if ($id_jo != '') {
	$sql = "SELECT attachment FROM tr_jo_attachment WHERE id_jo = '$id_jo'";
	$result = mysqli_query($koneksi, $sql);

	if ($result && mysqli_num_rows($result) > 0) {
		$data = [];
		while ($row = mysqli_fetch_assoc($result)) {
			$data[] = $row['attachment'];
		}

		$response['status'] = 200;
		$response['msg'] = 'Success';
		$response['data'] = $data;
	}
}

echo json_encode($response);
