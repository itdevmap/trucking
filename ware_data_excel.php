<?php
include "koneksi.php";
include "lib.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=WARE_QUOTATION.xls");

$quo_no = isset($_GET['quo_no']) ? trim($_GET['quo_no']) : '';

if ($quo_no == '') {
    die("Invalid Quotation Number");
}
?>

<table border="0">
    <tr>
        <th colspan="7" style="font-size:24px; text-align:left;">Ware Quotation</th>
    </tr>
    <tr>
        <th style="font-size:12px; width:90px; text-align:left;">No QUO : </th>
        <th colspan="7" style="font-size:12px; text-align:left;"><?= htmlspecialchars($quo_no) ?></th>
    </tr>
    <tr><td colspan="8">&nbsp;</td></tr>
</table>

<table border="1">
    <tr>
        <th style="font-size:12px; text-align:center;">NO</th>
        <th style="font-size:12px; text-align:center;">ITEMCODE</th>
        <th style="font-size:12px; text-align:center;">DESCRIPTION</th>
        <th style="font-size:12px; text-align:center;">UOM</th>
        <th style="font-size:12px; text-align:center;">WEIGHT</th>
        <th style="font-size:12px; text-align:center;">LENGTH</th>
        <th style="font-size:12px; text-align:center;">WIDE</th>
        <th style="font-size:12px; text-align:center;">HEIGHT</th>
        <th style="font-size:12px; text-align:center;">VOL</th>
    </tr>

<?php
$query = "
    SELECT 
        tw.*,
        twq.quo_no AS quo_code,
        twq.project_code
    FROM t_ware tw
    LEFT JOIN t_ware_quo twq ON twq.id_quo = tw.id_quo
    WHERE twq.quo_no = ?
";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $quo_no);
$stmt->execute();
$result = $stmt->get_result();

$no = 1;
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td style='text-align:center;'>{$no}</td>
        <td style='text-align:center;'>{$row['kode']}</td>
        <td style='text-align:left;'>{$row['nama']}</td>
        <td style='text-align:center;'>{$row['unit']}</td>
        <td style='text-align:right;'>{$row['berat']}</td>
        <td style='text-align:right;'>{$row['panjang']}</td>
        <td style='text-align:right;'>{$row['lebar']}</td>
        <td style='text-align:right;'>{$row['tinggi']}</td>
        <td style='text-align:right;'>{$row['vol']}</td>
    </tr>";
    $no++;
}

$stmt->close();
?>
</table>
