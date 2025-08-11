<?
$num_record = $rs->fields(bil_rekod);
$noPage=0;
$display = 50;
//$pagenr = $_GET['pagenr'];
if ($num_record > $display) {
	$num_pages = intval($num_record/$display)+1;
	if (!isset($pagenr)){
		$pagenr =1;
		$from = $pagenr*$display - $display;
		$sql= $sql . " LIMIT $from, $display";
	}
	else {
		$from = $pagenr*$display - $display;
		$sql= $sql . " LIMIT $from, $display";
	}
}
else {
	$num_pages = 1;
	$pagenr =1;
}

$rs = $conn->Execute($sql);
?>
