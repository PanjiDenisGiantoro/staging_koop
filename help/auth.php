<?
if ($_SESSION['user_id'] == "") {
	print '<script>	';
	print '	window.location.href = "index.php";';
	print '</script>';
	exit();
} else {
$userLogged = $_SESSION['user'];
$IDUser = $_SESSION['iduser'];
}
?>