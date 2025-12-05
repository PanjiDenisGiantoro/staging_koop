<?php
error_reporting (E_ALL ^ E_NOTICE);
//session_start();
include('adodb.inc.php');


$DB_hostname = "localhost";
$DB_username = "root";
$DB_password = "";
$DB_dbtype = "mysql";
$DB_dbname = "demokoop";

// ====== START DATABASE CONFIG =======
// Access
//$DB_dbtype='access'; $DB_hostname='iDoor'; $DB_username=''; $DB_password=''; $DB_dbname='';
//ODBC_MSSQL
//$DB_dbtype="odbc_mssql"; $DB_hostname="ska-dsn"; $DB_username="ska-user"; $DB_password="ska"; $DB_dbname=""; // same mechine
//$DB_dbtype="odbc_mssql"; $DB_hostname="localhost"; $DB_username="sa"; $DB_password="sa"; $DB_dbname="skamps"; //server
//$DB_dbtype="mysql"; $DB_hostname="localhost"; $DB_username="root"; $DB_password=""; $DB_dbname="idoor";
// ====== END DATABASE CONFIG ======


$conn->debug=1;
$conn = &ADONEWConnection($DB_dbtype);
$conn->Pconnect($DB_hostname, $DB_username, $DB_password, $DB_dbname);
/*
$conn = ADONewConnection('odbc_mssql');
$dsn = "Driver={SQL Server};Server=ska-dsn;Database=;";
$conn->Connect($dsn,'dbskamps','');
*/


foreach($_POST as $key=>$val){ $$key = $val; }
foreach($_GET as $key=>$val){ $$key = $val; }

function tohtml($strValue)
{
  return htmlspecialchars($strValue);
}

function tourl($strValue)
{
  return urlencode($strValue);
}

function get_param($ParamName)
{
  global $HTTP_POST_VARS;
  global $HTTP_GET_VARS;

  $ParamValue = "";
  if(isset($HTTP_POST_VARS[$ParamName]))
    $ParamValue = $HTTP_POST_VARS[$ParamName];
  else if(isset($HTTP_GET_VARS[$ParamName]))
    $ParamValue = $HTTP_GET_VARS[$ParamName];

  return $ParamValue;
}

function get_session($ParamName)
{
  global $HTTP_POST_VARS;
  global $HTTP_GET_VARS;
  global ${$ParamName};
  $ParamValue = "";
  if(!isset($HTTP_POST_VARS[$ParamName]) && !isset($HTTP_GET_VARS[$ParamName]) && session_is_registered($ParamName))
     $ParamValue = ${$ParamName};
  return $ParamValue;
}

function set_session($ParamName, $ParamValue)
{
  global ${$ParamName};
  if(session_is_registered($ParamName))
    session_unregister($ParamName);
  ${$ParamName} = $ParamValue;
  session_register($ParamName);
}

function is_number($string_value)
{
  if(is_numeric($string_value) || !strlen($string_value))
    return true;
  else
    return false;
}

function is_param($param_value)
{
  if($param_value)
    return 1;
  else
    return 0;
}

function tosql($value, $type="Text")
{
  if($value == "")
  {
    return "NULL";
  }
  else
  {
    if($type == "Number")
      return doubleval($value);
    else
    {
      if(get_magic_quotes_gpc() == 0)
      {
        $value = str_replace("'","''",$value);
        $value = str_replace("\\","\\\\",$value);
      }
      else
      {
        $value = str_replace("\\'","''",$value);
        $value = str_replace("\\\"","\"",$value);
      }
      return "'" . $value . "'";
     }
   }
}

function strip($value)
{
  if(get_magic_quotes_gpc() == 0)
    return $value;
  else
    return stripslashes($value);
}

function get_checkbox_value($sVal, $CheckedValue, $UnCheckedValue)
{
  if(!strlen($sVal))
    return tosql($UnCheckedValue);
  else
    return tosql($CheckedValue);
}

function dlookup($Table, $fName, $sWhere)
{
  global $conn;
  $sSQL = "";

  $sSQL = "SELECT " . $fName . " FROM " . $Table . " WHERE " . $sWhere;
  $rs2 = &$conn->Execute($sSQL);
  if ($rs2) {
    $_SESSION["group"] = $rs2->fields($fName);
    return $rs2->fields($fName);
  }
  else
    return "";
}

function set_pg($Page)
{
  $Page = 30;
  global ${$Page};
}

function get_pg()
{
  $Page = 30;
  global ${$Page};
  return;
}

function displayDate($varDate)
{
 $dateTime = explode(" ",$varDate);
 $date = $dateTime[0];
 $time = $dateTime[1];
 $dateParts = split("-",$date);
 $day = $dateParts[2];
 $month = $dateParts[1];
 $year = $dateParts[0]; 
 if (($day==NULL) || ($month==NULL) || ($year==NULL) || ($day=="00") || ($month=="00") || ($year=="00"))
 	$convertDate = "";
 else
 	$convertDate = $day."/".$month."/".$year;
 return $convertDate;
 }
 
 function displayDateTime($varDate)
{
 $dateTime = explode(" ",$varDate);
 $date = $dateTime[0];
 $time = $dateTime[1];
 $dateParts = split("-",$date);
 $day = $dateParts[2];
 $month = $dateParts[1];
 $year = $dateParts[0]; 
 if (($day==NULL) || ($month==NULL) || ($year==NULL))
 	$convertDate = "";
 else
 	$convertDate = $day."/".$month."/".$year." ".$time;
 return $convertDate;
 }
 
 function saveDate($varDate)
{
 $dateParts = split("-",$varDate);
 	if ($dateParts[1] == NULL) {
		$dateParts = split("/",$varDate);
    }
 $day = $dateParts[2];
 $month = $dateParts[1];
 $year = $dateParts[0]; 
 $day = trim($day);
 $month = trim($month);
 $year = trim($year);
 $convertDate = $day."-".$month."-".$year;
 return $convertDate;
 }
 
 function getDay($varDate){
 $dateParts = split("-",$varDate);
 	if ($dateParts[1] == NULL) {
		$dateParts = split("/",$varDate);
    }
 $day = $dateParts[2];
 $month = $dateParts[1];
 $year = $dateParts[0]; 
 $day = trim($day);
 $month = trim($month);
 $year = trim($year);
 $stringDay = date("l", mktime(0, 0, 0, $month, $day, $year));
 return $stringDay;
 }

function stri_replace($find,$replace,$string)
{
       if(!is_array($find)) $find = array($find);
       if(!is_array($replace))
       {
               if(!is_array($find)) $replace = array($replace);
               else
               {
                       // this will duplicate the string into an array the size of $find
                       $c = count($find);
                       $rString = $replace;
                       unset($replace);
                       for ($i = 0; $i < $c; $i++)
                       {
                               $replace[$i] = $rString;
                       }
               }
       }
       foreach($find as $fKey => $fItem)
       {
               $between = explode(strtolower($fItem),strtolower($string));
               $pos = 0;
               foreach($between as $bKey => $bItem)
               {
                       $between[$bKey] = substr($string,$pos,strlen($bItem));
                       $pos += strlen($bItem) + strlen($fItem);
               }
               $string = implode($replace[$fKey],$between);
       }
       return($string);
}

function displayBulan($Bulan) {
if($Bulan == 1 ) { $BulanDisp = "Januari"; }
elseif($Bulan == 2 ) { $BulanDisp = "Februari"; }
elseif($Bulan == 3 ) { $BulanDisp = "Mac"; }
elseif($Bulan == 4 ) { $BulanDisp = "April"; }
elseif($Bulan == 5 ) { $BulanDisp = "Mei"; }
elseif($Bulan == 6 ) { $BulanDisp = "Jun"; }
elseif($Bulan == 7 ) { $BulanDisp = "Julai"; }
elseif($Bulan == 8 ) { $BulanDisp = "Ogos"; }
elseif($Bulan == 9 ) { $BulanDisp = "September"; }
elseif($Bulan == 10 ) { $BulanDisp = "Oktober"; }
elseif($Bulan == 11 ) { $BulanDisp = "November"; }
elseif($Bulan == 12 ) { $BulanDisp = "Disember"; }
return $BulanDisp;
}

function displayHari($hari) {
if ($hari == "Mon") $hariDisplay = "Isnin";
elseif ($hari == "Tue") $hariDisplay = "Selasa";
elseif ($hari == "Wed") $hariDisplay = "Rabu";
elseif ($hari == "Thu") $hariDisplay = "Khamis";
elseif ($hari == "Fri") $hariDisplay = "Jumaat";
elseif ($hari == "Sat") $hariDisplay = "Sabtu";
elseif ($hari == "Sun") $hariDisplay = "Ahad";
return $hariDisplay;
}

function arrow($sql, $by) {
if (!$page) $page = $PHP_SELF;
//echo $sql;
$img1 =  '&nbsp;&nbsp;<a href="'.$page.'?sql='.$sql.'&by='.$by.'&sort=1"><img src="images/asc.gif" width="15" height="15" border="0"></a>';
$img2 = '<a href="'.$page.'?sql='.$sql.'&by='.$by.'&sort=2"><img src="images/desc.gif" width="15" height="15" border="0"></a>';
return $img1.$img2;
}

function arrow2($by, $sql, $page, $pagenumber) {
print '
&nbsp;<a href="#" onClick="sortby(\'1\',\''.$by.'\',\''.$pagenumber.'\')"><img src="images/asc.gif" width="15" height="15" border="0"></a>
<a href="#" onClick="sortby(\'2\',\''.$by.'\',\''.$pagenumber.'\')"><img src="images/desc.gif" width="15" height="15" border="0"></a>';
}

function sort_image($by, $pagenumber) {
print '
&nbsp;<a href="#" onClick="sortby(\'1\',\''.$by.'\',\''.$pagenumber.'\')"><img src="images/asc.gif" width="15" height="15" border="0"></a>
<a href="#" onClick="sortby(\'2\',\''.$by.'\',\''.$pagenumber.'\')"><img src="images/desc.gif" width="15" height="15" border="0"></a>';
}

function date_diff($start_date, $end_date, $returntype="d")
{
   if ($returntype == "s")
       $calc = 1;
   if ($returntype == "m")
       $calc = 60;
   if ($returntype == "h")
       $calc = (60*60);
   if ($returntype == "d")
       $calc = (60*60*24);
       
   $_d1 = explode("-", $start_date);
   $y1 = $_d1[0];
   $m1 = $_d1[1];
   $d1 = $_d1[2];
   
   $_d2 = explode("-", $end_date);
   $y2 = $_d2[0];
   $m2 = $_d2[1];
   $d2 = $_d2[2];
   
   if (($y1 < 1970 || $y1 > 2037) || ($y2 < 1970 || $y2 > 2037))
   {
       return 0;
   } else
   {
       $today_stamp    = mktime(0,0,0,$m1,$d1,$y1);
       $end_date_stamp    = mktime(0,0,0,$m2,$d2,$y2);
       $difference    = round(($end_date_stamp-$today_stamp)/$calc);
       return $difference;
   }
}

function FormValidation($strFormName, $strFormValue, $strFormValidMethod)
{
	//global $strErrMsg;
	
	if ($strFormValue == "") {
		if ($strFormValidMethod == "CheckBlank") {
			$errorMsg = '- <font class=redText>Sila isi ruangan '.$strFormName.'.</font><br>';
		}
			$errorMsg = '- <font class=redText>Sila isikan ruangan '.$strFormName.'.</font><br>';
	} else {
		if ($strFormValidMethod == "CheckNumeric") {
			if(eregi("[^0-9]",$strFormValue)) { 
				$errorMsg = '- <font class=redText>'.$strFormName.' Mesti Nombor.</font><br>';
			}
		}
		if ($strFormValidMethod == "CheckDecimal") {
			if(eregi("[^-0-9.^0-9]",$strFormValue)) { 
				$errorMsg = '- <font class=redText>'.$strFormName.' Must Be Numeric.</font><br>';
			}
		}
		if ($strFormValidMethod == "CheckEmailAddress") {
			if (!ereg("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@([0-9a-z][0-9a-z-]*[0-9a-z]\.)+[a-z]{2}[mtgvu]?$", $strFormValue)) {
				$errorMsg = '- <font class=redText>Alamat Emel Tidak Sah.</font><br>';
			}
		}
		if ($strFormValidMethod == "CheckDate") {
			if (ValidDate($strFormValue) == "N") {
				$errorMsg = '- <font class=redText>Tarikh Tidak Sah.</font><br>';
			}
		}
	}
	return $errorMsg;
}

function ValidDate($value) { 
	$strData = strtok($value, "/"); 
	$intCount = 1; 
	while ($strData) { 
		if ($intCount == 1) $tmpday = $strData; 
		if ($intCount == 2) $tmpmonth = $strData; 
		if ($intCount == 3) $tmpyear = $strData; 
		$intCount = $intCount + 1; 
		$strData = strtok("/"); 
	}
	if (checkdate($tmpmonth,$tmpday,$tmpyear)) return "Y";  else return "N"; 
} 

function bilHariCuti($tarikh_mula, $tarikh_tamat) {
	global $conn;
	$conn->debug=true;
	$sql = "SELECT COUNT(*) AS bil_hari FROM lib_cuti
			 WHERE tarikh BETWEEN '".$tarikh_mula."' AND '".$tarikh_tamat."' 
			 AND DATE_FORMAT(tarikh,'%w')<>0 AND DATE_FORMAT(tarikh,'%w')<>6";
	$rs = $conn->Execute($sql);
	if ($rs->RecordCount()>0)
		return $rs->fields(bil_hari);
	else
		return 0;		
}

function bilHariWeekend($tarikh_mula, $tarikh_tamat) {
		$x=0;
		$weekendCount=0;
		$tempoh = date_diff($tarikh_mula, $tarikh_tamat);
		$year = substr($tarikh_mula,0,4);
		$month = substr($tarikh_mula,5,2);
		$day = substr($tarikh_mula,8,2);
		while ($x<$tempoh) {
			//echo date("Y-m-d",mktime(0,0,0,$month,$day+$x,$year));
			$hariP = date("w",mktime(0,0,0,$month,$day+$x,$year));
			if (($hariP=="0") || ($hariP=="6"))
				$weekendCount = $weekendCount+1;
			$x++;
			//echo "<br>";
		}
		//echo "Weekend :".$weekendCount;
		return $weekendCount;
}

function checkCuti($tarikh) {
	global $conn;
	//$conn->debug=true;
	//echo $tarikh;
	$sql = "SELECT COUNT(*) AS bil_hari FROM lib_cuti
			 WHERE tarikh = '".$tarikh."' AND DATE_FORMAT(tarikh,'%w')<>0 AND DATE_FORMAT(tarikh,'%w')<>6";
	$rs = $conn->Execute($sql);
	$cuti=0;
	if ($rs->fields(bil_hari)>0) {
		$cuti=1;
		//echo "Hari Cuti Umum";
	} else {
		$cuti=0;
		$year = substr($tarikh,0,4);
		$month = substr($tarikh,5,2);
		$day = substr($tarikh,8,2);
		$hariP = date("w",mktime(0,0,0,$month,$day+$x,$year));
		if (($hariP=="0") || ($hariP=="6")) {
			$cuti=1;
			//echo "Hari Weekend";
		} else
			$cuti=0;
	}
	return $cuti;
}

function getTarikhPulang($tarikhPinjam, $tempohPinjam) {
	$x=0;
	$year = substr($tarikhPinjam,0,4);
	$month = substr($tarikhPinjam,5,2);
	$day = substr($tarikhPinjam,8,2);
	while ($bil_hari_bekerja<$tempohPinjam) {
		$hariP = date("Y-m-d",mktime(0,0,0,$month, $day+$x, $year));
		
		if (checkCuti($hariP)==0) 
			$bil_hari_bekerja = $bil_hari_bekerja + 1;
		$x++;
		//echo "<br>";
	}
	return $hariP;
}

function getBilHariBekerja($tarikhMula, $tarikhTamat) {
	$tempoh = date_diff($tarikhMula, $tarikhTamat);
	$x=0;
	$bil_hari_bekerja=0;
	$year = substr($tarikhMula,0,4);
	$month = substr($tarikhMula,5,2);
	$day = substr($tarikhMula,8,2);
	while ($x<$tempoh) {
		$hariP = date("Y-m-d",mktime(0,0,0,$month, $day+$x, $year));
		
		if (checkCuti($hariP)==0) 
			$bil_hari_bekerja = $bil_hari_bekerja + 1;
	}
	return $bil_hari_bekerja;
}

function getRateDenda(){
	global $conn;
	$getRate  = "SELECT * FROM lib_general WHERE general_name = 'denda'";
	$rsRate = $conn->Execute($getRate);
	$rate_denda = $rsRate->fields(general_value);
	return $rate_denda;
}

function getStatus($status) {
	if ($status=="A") 
		return "AKTIF"; 
	elseif ($status=="B") 
		return "<font color=blue>BERHENTI</font>";
	elseif ($status=="G") 
		return "<font color=red>DIGANTUNG</font>";
	else
		return "AKTIF"; 
}

function getNegeri($kod) {
	  if ($kod=="1") $negeri = "Johor";
	  elseif ($kod=="2") $negeri = "Johor";	  
	  elseif ($kod=="3") $negeri = "Kelantan";	  
	  elseif ($kod=="4") $negeri = "Melaka";	  
	  elseif ($kod=="5") $negeri = "N.Sembilan";	  
	  elseif ($kod=="6") $negeri = "Pahang";	  	  	  	  	  
	  elseif ($kod=="7") $negeri = "Pulau Pinang";	  	  
	  elseif ($kod=="8") $negeri = "Perak";	  
	  elseif ($kod=="9") $negeri = "Perlis";	  	  	  
	  elseif ($kod=="10") $negeri = "Selangor";	  
	  elseif ($kod=="11") $negeri = "Terengganu";	  
	  elseif ($kod=="12") $negeri = "Sabah";	  
	  elseif ($kod=="13") $negeri = "Sarawak";	  	  
	  elseif ($kod=="14") $negeri = "WP Kuala Lumpur";	  	  
	  elseif ($kod=="15") $negeri = "WP Labuan";	  	  
	  return $negeri;
}
?>


