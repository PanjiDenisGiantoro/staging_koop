<!--<script src="assets/plugins/bootstrap-sweetalert/sweetalert.min.js"></script>-->
<?php
/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	common.php
 *********************************************************************************/

session_cache_limiter(FALSE);
error_reporting (E_ALL ^ E_NOTICE);
//error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
//session_start();
$errPage        = 'Session denied...!';
$setLevel        = 3;

/*************  DATABASE CONNECTION       *************/
include('adodb.inc.php');
if ($HTTP_COOKIE_VARS["selDB"] == "") {
    $DB_dbtype = 'mysql';
    $DB_hostname = 'localhost';
    $DB_username = 'root';
    $DB_password = '';
    // $DB_dbname = 'demotri';
   $DB_dbname = 'demokoop';
} else {
    $DB_dbtype = 'mysql';
    $DB_hostname = 'localhost';
    $DB_username = 'root';
    $DB_password = '';
    $DB_dbname = $HTTP_COOKIE_VARS["selDB"];
}

$conn = &ADONEWConnection($DB_dbtype);
$conn->Pconnect($DB_hostname, $DB_username, $DB_password, $DB_dbname);


/************* END   *************/

$sSQL = 'SELECT * FROM setup where setupID = 1';
$rs = &$conn->Execute($sSQL);
//--- End   : Get Setup Information ----------------------------------------------------------------
//--- Begin : Set Application variables ------------------------------------------------------------
if (!$rs->EOF) {
    $emaNetis            = tohtml($rs->fields('siteName'));
    $siteCSS             = tohtml($rs->fields('css'));
    $retooFetis        = $rs->fields('footer');
    $siteKeyword        = tohtml($rs->fields('metaKeyword'));
    $siteDesc             = tohtml($rs->fields('metaDesc'));
    $yVZcSz2OuGE5U        = $rs->fields('registerName');
    $T5ZZPpvAKXOsI        = $rs->fields('registerID');
    $gsURgLGTUOAMI        = $rs->fields('licenceKey');
} else {
    for ($i = 0; $i < count($e4iQwRsWtQ); $i++) {
        print chr($e4iQwRsWtQ[$i]);
    }
    exit;
}

/*************  COMMON FUNCTION       *************/

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
    if (isset($HTTP_POST_VARS[$ParamName]))
        $ParamValue = $HTTP_POST_VARS[$ParamName];
    else if (isset($HTTP_GET_VARS[$ParamName]))
        $ParamValue = $HTTP_GET_VARS[$ParamName];

    return $ParamValue;
}

function get_session($ParamName)
{
    global $HTTP_POST_VARS;
    global $HTTP_GET_VARS;
    global ${$ParamName};
    $ParamValue = "";
    if (!isset($HTTP_POST_VARS[$ParamName]) && !isset($HTTP_GET_VARS[$ParamName]) && session_is_registered($ParamName))
        $ParamValue = ${$ParamName};

    return $ParamValue;
}

function set_session($ParamName, $ParamValue)
{
    global ${$ParamName};
    if (session_is_registered($ParamName))
        session_unregister($ParamName);
    ${$ParamName} = $ParamValue;
    session_register($ParamName);
}

function carianheader($by, $opt = array(1, 2, 3), $dept = '', $deptList = '', $deptVal = '')
{
    echo '<div clas="row">Carian Melalui 
        <select name="by" class="form-select-sm">';
    if (in_array(1, $opt)) if ($by == 1)    print '<option value="1" selected>Nombor Anggota</option>';
    else print '<option value="1">Nombor Anggota</option>';
    if (in_array(2, $opt)) if ($by == 2)    print '<option value="2" selected>Nama Anggota</option>';
    else print '<option value="2">Nama Anggota</option>';
    if (in_array(3, $opt)) if ($by == 3)    print '<option value="3" selected>Kad Pengenalan</option>';
    else print '<option value="3">Kad Pengenalan</option>';
    if (in_array(4, $opt)) if ($by == 4) print '<option value="4" selected>Peringkat</option>';
    else print '<option value="4">Peringkat</option>';
    if (in_array(5, $opt)) if ($by == 5) print '<option value="5" selected>Nombor Rujukan</option>';
    else print '<option value="5">Nombor Rujukan</option>';
    if (in_array(6, $opt)) if ($by == 6) print '<option value="6" selected>Nombor Kenderaan</option>';
    else print '<option value="6">Nombor Kenderaan</option>';
    if (in_array(7, $opt)) if ($by == 7) print '<option value="7" selected>Jumlah Hari Tempoh Tamat Insuran</option>';
    else print '<option value="7">Jumlah Hari Tempoh Tamat Insuran</option>';

    print '</select>
        <input type="text" name="q" value="" maxlength="50" size="20" class="form-controlx form-control-sm">
        <input type="submit" class="btn btn-sm btn-secondary" value="Cari">&nbsp;&nbsp;&nbsp;';
    if (is_array($deptList)) {
        echo ' 
        Cawangan/Zon
            <select name="dept" class="form-select-sm" onchange="document.MyForm.submit();">
                    <option value="">- Semua -';
        for ($i = 0; $i < count($deptList); $i++) {
            print '<option value="' . $deptVal[$i] . '" ';
            if ($dept == $deptVal[$i]) print ' selected';
            print '>' . $deptList[$i];
        }
        print '</select>&nbsp;&nbsp; ';
    }
    echo '</div>';
}

function papar_ms($pg)
{
    $ms = '&nbsp;&nbsp;Paparan
                <SELECT name="pg" class="form-select-xs" onchange="doListAll();">';
    if ($pg == 5)    $ms = $ms . '<option value="5" selected>5</option>';
    else $ms = $ms . '<option value="5">5</option>';
    if ($pg == 10) $ms = $ms . '<option value="10" selected>10</option>';
    else $ms = $ms . '<option value="10">10</option>';
    if ($pg == 20) $ms = $ms . '<option value="20" selected>20</option>';
    else $ms = $ms . '<option value="20">20</option>';
    if ($pg == 30) $ms = $ms . '<option value="30" selected>30</option>';
    else $ms = $ms . '<option value="30">30</option>';
    if ($pg == 40) $ms = $ms . '<option value="40" selected>40</option>';
    else $ms = $ms . '<option value="40">40</option>';
    if ($pg == 50) $ms = $ms . '<option value="50" selected>50</option>';
    else $ms = $ms . '<option value="50">50</option>';
    if ($pg == 100) $ms = $ms . '<option value="100" selected>100</option>';
    else $ms = $ms . '<option value="100">100</option>';
    if ($pg == 200) $ms = $ms . '<option value="200" selected>200</option>';
    else $ms = $ms . '<option value="200">200</option>';
    if ($pg == 300) $ms = $ms . '<option value="300" selected>300</option>';
    else $ms = $ms . '<option value="300">300</option>';
    if ($pg == 400) $ms = $ms . '<option value="400" selected>400</option>';
    else $ms = $ms . '<option value="400">400</option>';
    if ($pg == 500) $ms = $ms . '<option value="500" selected>500</option>';
    else $ms = $ms . '<option value="500">500</option>';
    if ($pg == 1000) $ms = $ms . '<option value="1000" selected>1000</option>';
    else $ms = $ms . '<option value="1000">1000</option>';
    $ms = $ms . '</select>&nbsp;&nbsp;per mukasurat.';
    return $ms;
}

function is_number($string_value)
{
    if (is_numeric($string_value) || !strlen($string_value))
        return true;
    else
        return false;
}


function alert($msj, $sts = '')
{
    if ($sts == "") {
        $cls = "";
    } elseif ($sts == "1") {
        $cls = ",'success'";
    } else {
        $cls = ",'error'";
    }

    echo '<script type="text/javascript">';
    echo "setTimeout(function () { swal('$msj','' $cls);";
    echo '}, 100);</script>';
}

function gopage($page, $timer = 0)
{
    //echo "<meta http-equiv=\"refresh\" content=\"$timer;".
    //		 "url=$page\">";
?>

    <script type="text/javascript">
        function pageRedirect() {
            window.location.replace("<?php echo $page ?>");
        }
        setTimeout("pageRedirect()", <?php echo $timer ?>);
    </script>
<?php
}

function is_param($param_value)
{
    if ($param_value)
        return 1;
    else
        return 0;
}

function toYN($strValue)
{
    $YN = "Tidak";
    if ($strValue) $YN = "Ya";
    return $YN;
}

function tosql($value, $type = "Text")
{
    if ($value == "0") {
        return "0";
    } elseif ($value == "") {
        return "NULL";
    } else {
        if ($type == "Number")
            return doubleval($value);
        else {
            if (get_magic_quotes_gpc() == 0) {
                $value = str_replace("'", "''", $value);
                $value = str_replace("\\", "\\\\", $value);
            } else {
                $value = str_replace("\\'", "''", $value);
                $value = str_replace("\\\"", "\"", $value);
            }
            return "'" . $value . "'";
        }
    }
}

function strip($value)
{
    if (get_magic_quotes_gpc() == 0)
        return $value;
    else
        return stripslashes($value);
}

function get_checkbox_value($sVal, $CheckedValue, $UnCheckedValue)
{
    if (!strlen($sVal))
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
    if ($rs2)
        return $rs2->fields($fName);
    else
        return "";
}

function createLog($user, $event, $type)
{
    global $conn;
    $dateEvent = date("Y-m-d H:i:s");
    $sSQL = "";
    $sSQL    = "insert into LOGS (" .
        "userName," .
        "eventText," .
        "dateEvent," .
        "groupName)" .
        " values (" .
        tosql($user, "Text") . "," .
        tosql($event, "Text") . "," .
        tosql($dateEvent, "Text") . "," .
        tosql($type, "Text") . ")";
    $rs2 = &$conn->Execute($sSQL);
}


function toDateMK($dateformatstring, $mysqlstring)
{
    if ($mysqlstring <> "")
        return date($dateformatstring, mktime(substr($mysqlstring, 11, 2), substr($mysqlstring, 14, 2), substr($mysqlstring, 17, 2), substr($mysqlstring, 5, 2), substr($mysqlstring,  8, 2), substr($mysqlstring, 0, 4)));
}

function toDate($format, $varDate)
{
    $dateTime = explode(" ", $varDate);
    $date = $dateTime[0];
    $time = $dateTime[1];
    $dateParts = split("-", $date);
    $day = $dateParts[2];
    $month = $dateParts[1];
    $year = $dateParts[0];
    if (($day == NULL) || ($month == NULL) || ($year == NULL))
        $convertDate = "";
    else
        $convertDate = $day . "/" . $month . "/" . $year;
    return $convertDate;
}

function toTime($format, $varDate)
{
    $dateTime = explode(" ", $varDate);
    $date = $dateTime[0];
    $time = $dateTime[1];
    $timeParts = split(":", $time);
    $hour = $timeParts[0];
    $minute = $timeParts[1];
    $second = $timeParts[2];
    if (($hour == NULL) || ($minute == NULL) || ($second == NULL))
        $convertTime = "";
    else
        $convertTime = $hour . ":" . $minute . ":" . $second;
    return $convertTime;
}

function convertDate($varDate)
{
    $dateTime = explode(" ", $varDate);
    $date = $dateTime[0];
    $time = $dateTime[1];
    $dateParts = split("-", $date);
    $day = $dateParts[2];
    $month = $dateParts[1];
    $year = $dateParts[0];
    if (($day == NULL) || ($month == NULL) || ($year == NULL))
        $convertDate = "";
    else
        $convertDate = $day . "/" . $month . "/" . $year . " " . $time;
    return $convertDate;
}

function saveDate($varDate)
{
    $dateParts = split("-", $varDate);
    if ($dateParts[1] == NULL) {
        $dateParts = split("/", $varDate);
    }
    $day = $dateParts[2];
    $month = $dateParts[1];
    $year = $dateParts[0];
    $day = trim($day);
    $month = trim($month);
    $year = trim($year);
    $convertDate = $day . "-" . $month . "-" . $year;
    return $convertDate;
}

function saveDateDb($varDate)
{
    $dateParts = split("/", $varDate);
    $day = $dateParts[0];
    $month = $dateParts[1];
    $year = $dateParts[2];
    $day = trim($day);
    $month = trim($month);
    $year = trim($year);
    $convertDate = $year . "-" . $month . "-" . $day;
    return $convertDate;
}

function convertNewIC($varNewIC)
{
    if ($varNewIC <> '' && strlen($varNewIC) > 11) {
        $val = substr($varNewIC, 0, 6) . '-' . substr($varNewIC, 6, 2) . '-' . substr($varNewIC, 8, 4);
    } else {
        $val = $varNewIC;
    }
    return $val;
}

function CheckQuotes($strValue)
{
    if (!get_magic_quotes_gpc()) {
        $strValue = addslashes($strValue);
    }

    return $strValue;
}

function activityLog($sql, $report, $id, $logid, $status)
{
    global $conn;
    $report = trim($report);
    $sqlTemp = trim($sql);
    $sql = addslashes($sqlTemp);
    $sqlTemp = explode(" ", $sqlTemp);
    $type = $sqlTemp[0];
    $updatedDate = date("Y-m-d H:i:s");
    if ($userID == '') $userID = 0;
    $sqlAct =
        "INSERT INTO activitylog (`report`, `sqlType`, `sql`, `byID`, `activityDate`, `activityBy`, `status`)" .
        " VALUES (" . tosql($report, "Text") . ", " . tosql($type, "Text") . ",'" . $sql . "', " . tosql($id, "Text") . "," . tosql($updatedDate, "Text") . ", " . tosql($logid, "Text") . ", " . tosql($status, "Text") . ")";
    //print $sqlAct;
    $rs = &$conn->Execute($sqlAct);
}

function displayBulan($Bulan)
{
    if ($Bulan == 1) {
        $BulanDisp = "Januari";
    }
    if ($Bulan == 2) {
        $BulanDisp = "Februari";
    }
    if ($Bulan == 3) {
        $BulanDisp = "Mac";
    }
    if ($Bulan == 4) {
        $BulanDisp = "April";
    }
    if ($Bulan == 5) {
        $BulanDisp = "Mei";
    }
    if ($Bulan == 6) {
        $BulanDisp = "Jun";
    }
    if ($Bulan == 7) {
        $BulanDisp = "Julai";
    }
    if ($Bulan == 8) {
        $BulanDisp = "Ogos";
    }
    if ($Bulan == 9) {
        $BulanDisp = "September";
    }
    if ($Bulan == 10) {
        $BulanDisp = "Oktober";
    }
    if ($Bulan == 11) {
        $BulanDisp = "November";
    }
    if ($Bulan == 12) {
        $BulanDisp = "Disember";
    }
    return $BulanDisp;
}

function hangPar($text)
{
    $hangP = "<P style=text-indent:-2em;margin-left" . ":2em;margin-top:0;margin-bottom:0>";
    $pre = "^(?!<P\\b)(?!\\t)(.+)";
    while (preg_match("/^(.*)<hang>(.*?)<\\/hang>(.*)$/s", $text, $matches)) {
        // turn lines followed by tabbed lines into hanging indents
        $hang = preg_replace("/$pre(((\\n\\t).*)+)/mi", "$hangP$1<br >$2</P >", $matches[2]);
        // turn singleton lines without tabs into solo paragraphs
        $hang = preg_replace("/$pre\\n(?!\\t)/mi", "$hangP$1</P >\n", $hang);
        // break between sections
        $hang = preg_replace("/\\n\\n+/", "\n<br >", $hang);
        $text = $matches[1] . $hang . $matches[3];
    }
    return $text;
}

function insertspace($txt1)
{
    $temp = explode(" ", $txt1);
    $i = 0;
    foreach ($temp as $word) {
        $temptags = $word;
        if ($temptags == '')
            $newWord[$i] = "&nbsp;";
        else
            $newWord[$i] = $word;
        $i++;
    }
    $spaceWord = implode(" ", $newWord);
    return $spaceWord;
}

class clsStringValue
{
    //------------------------------------------------
    // 
    // Obj : class to handle amount value to string
    //------------------------------------------------

    var $value;
    var $len;
    var $cent;
    var $puluh_ratus;

    function setLen($val)
    {
        $this->len = strlen($val);
    }

    function getLen()
    {
        return $this->len;
    }

    function setCent($val)
    {
        $this->cent = ' dan sen ' . $this->nilaiPuluh($val);
    }

    function getCent()
    {
        return $this->cent;
    }

    function setPuluhRatus($val)
    {
        $length = strlen($val);
        if ($length == 1) $this->puluh_ratus = $this->nilaiSa($val);
        elseif ($length == 2) $this->puluh_ratus = $this->nilaiPuluh($val);
        elseif ($length == 3) $this->puluh_ratus = $this->nilaiRatus($val);
    }

    function getPuluhRatus()
    {
        return $this->puluh_ratus;
    }

    function setValue($val)
    {
        if (!is_int($val)) {
            $val2 = explode(".", $val);
            $val3 = strval($val2[1]);
            if (strlen($val3) < 2) $val3 = sprintf("%s0", $val3);
            $this->setCent($val3);
            $val = $val2[0];
        }
        $this->setLen($val);
        $length =  $this->getLen();
        if ($length == 1) $this->value = $this->nilaiSa($val);
        elseif ($length == 2) $this->value = $this->nilaiPuluh($val);
        elseif ($length == 3) $this->value = $this->nilaiRatus($val);
        elseif ($length == 4) $this->value = $this->nilaiRibu($val);
        elseif ($length == 5) {
            $this->setPuluhRatus(substr($val, 0, 2));
            if (substr($val, 2, 3) > 0) $this->value = $this->getPuluhRatus() . ' ribu ' . $this->nilaiRatus(substr($val, 2, 3));
            else $this->value = $this->getPuluhRatus() . ' ribu';
        } elseif ($length == 6) {
            $this->setPuluhRatus(substr($val, 0, 3));
            if (substr($val, 3, 3) > 0) $this->value = $this->getPuluhRatus() . ' ribu ' . $this->nilaiRatus(substr($val, 3, 3));
            else $this->value = $this->getPuluhRatus() . ' ribu';
        } elseif ($length == 7) {
            //1,232,323
            if (substr($val, 1, 3) > 0 && substr($val, 4, 3) > 0) {
                $this->setPuluhRatus(substr($val, 1, 3));
                $this->value = $this->nilaiSa(substr($val, 0, 1)) . ' juta ' . $this->getPuluhRatus() . ' ribu ' . $this->nilaiRatus(substr($val, 4, 3));
            } elseif (substr($val, 1, 3) > 0) {
                $this->setPuluhRatus(substr($val, 1, 3));
                $this->value = $this->nilaiSa(substr($val, 0, 1)) . ' juta ' . $this->getPuluhRatus() . ' ribu';
            } elseif (substr($val, 4, 3) > 0) {
                $this->setPuluhRatus(substr($val, 1, 3));
                $this->value = $this->nilaiSa(substr($val, 0, 1)) . ' juta ' . $this->nilaiRatus(substr($val, 4, 3));
            } else {
                $this->value = $this->nilaiSa(substr($val, 0, 1)) . ' juta ';
            }
        } elseif ($length == 8) {
            //21,232,323
            if (substr($val, 2, 3) > 0 && substr($val, 5, 3) > 0) {
                $this->setPuluhRatus(substr($val, 0, 2));
                $this->value = $this->getPuluhRatus() . ' juta ';
                $this->setPuluhRatus(substr($val, 2, 3));
                $this->value .= $this->getPuluhRatus() . ' ribu ' . $this->nilaiRatus(substr($val, 5, 3));
            } elseif (substr($val, 2, 3) > 0) {
                $this->setPuluhRatus(substr($val, 0, 2));
                $this->value = $this->getPuluhRatus() . ' juta ';
                $this->setPuluhRatus(substr($val, 2, 3));
                $this->value .= $this->getPuluhRatus() . ' ribu ';
            } elseif (substr($val, 5, 3) > 0) {
                $this->setPuluhRatus(substr($val, 0, 2));
                $this->value = $this->getPuluhRatus() . ' juta ';
                $this->value .= $this->nilaiRatus(substr($val, 5, 3));
            } else {
                $this->setPuluhRatus(substr($val, 0, 2));
                $this->value = $this->getPuluhRatus() . ' juta ';
            }
        } elseif ($length == 9) {
            //421,232,323
            if (substr($val, 3, 3) > 0 && substr($val, 6, 3) > 0) {
                $this->setPuluhRatus(substr($val, 0, 3));
                $this->value = $this->getPuluhRatus() . ' juta ';
                $this->setPuluhRatus(substr($val, 3, 3));
                $this->value .= $this->getPuluhRatus() . ' ribu ' . $this->nilaiRatus(substr($val, 6, 3));
            } elseif (substr($val, 3, 3) > 0) {
                $this->setPuluhRatus(substr($val, 0, 3));
                $this->value = $this->getPuluhRatus() . ' juta ';
                $this->setPuluhRatus(substr($val, 3, 3));
                $this->value .= $this->getPuluhRatus() . ' ribu ';
            } elseif (substr($val, 6, 3) > 0) {
                $this->setPuluhRatus(substr($val, 0, 3));
                $this->value = $this->getPuluhRatus() . ' juta ';
                $this->value .= $this->nilaiRatus(substr($val, 6, 3));
            } else {
                $this->setPuluhRatus(substr($val, 0, 3));
                $this->value = $this->getPuluhRatus() . ' juta ';
            }
        } else $this->value = '';
        if (intval($val3) <> 0) $this->value .= $this->getCent();
    }

    function getValue()
    {
        return $this->value;
    }

    function nilaiSa($val)
    {
        $nilai = array('kosong', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'lapan', 'sembilan');
        return $nilai[$val];
    }

    function nilaiPuluh($val)
    {
        $val = strval($val);
        $sa = substr($val, -1);
        $puluh = substr($val, -2, 1);
        if (strlen(intval($val)) < 2) {
            $strval = $this->nilaiSa($sa);
        } elseif ($puluh == 1) {
            if ($sa == 0) $strval = 'sepuluh';
            elseif ($sa == 1) $strval = 'sebelas';
            elseif ($sa > 1 && $sa <= 9) $strval = $this->nilaiSa($sa) . ' belas';
        } elseif (($puluh >= 2 && $puluh <= 9) && $sa == 0) {
            $strval = $this->nilaiSa($puluh) . ' puluh';
        } else {
            $strval = $this->nilaiSa($puluh) . ' puluh ' . $this->nilaiSa($sa);
        }
        return $strval;
    }

    function nilaiRatus($val)
    {
        $val = strval($val);
        $puluh = substr($val, -2);
        $ratus = substr($val, -3, 1);
        if (strlen(intval($val)) < 3) {
            $strval = $this->nilaiPuluh($puluh);
        } elseif ($ratus == 1) {
            if ($puluh == '00') $strval = 'seratus';
            else $strval = 'seratus ' . $this->nilaiPuluh($puluh);
        } else {
            if ($puluh == '00') $strval = $this->nilaiSa($ratus) . ' ratus ';
            else $strval = $this->nilaiSa($ratus) . ' ratus ' . $this->nilaiPuluh($puluh);
        }
        return $strval;
    }

    function nilaiRibu($val)
    {
        $val = strval($val);
        $ratus = substr($val, -3);
        $ribu = substr($val, -4, 1);
        if ($ribu == 1) {
            if ($ratus == '000') $strval = 'seribu';
            else $strval = 'seribu ' . $this->nilaiRatus($ratus);
        } else {
            if ($ratus == '000') $strval = $this->nilaiSa($ribu) . ' ribu ';
            else $strval = $this->nilaiSa($ribu) . ' ribu ' . $this->nilaiRatus($ratus);
        }
        return $strval;
    }
} //end this class

$clsRM = new clsStringValue();

function thousand($val)
{
    return number_format($val, 2);
}
//************************************ BASIC LIST  *****************************************************

$basicList  = array(
    'Kode Negara',
    'Kode Cabang/Zona',
    'Kode Majikan PTJ',
    'Jenis Pembiayaan',
    'Kode Objek & Akun',
    'Kode Suku Bangsa',
    'Kode Agama',
    'Kode Pekerjaan',
    'Kode Provinsi',
    'Kode Bank Anggota',
    'Kode Kebajikan',
    'Kode Komoditas',
    'Kode Pendapatan',
    'Kode Pengeluaran',
    'Skala Gaji',
    'Skala Usia',
    'Kode Produk Simpanan'
);

$basicVal = array('A', 'B', 'U', 'C', 'J', 'E', 'F', 'L', 'H', 'Z', 'S', 'X', 'P', 'Q', 'M', 'N','Y');

$basicListACC    = array(
    'Kode Klasifikasi',
    'Kode Bagan Akun',
    'Kode Kreditur',
    'Kode Debitur',
    'Kode Kelompok',
    'Kode Bank',
    'Kode Batch',
    'Kode Departemen',
    'Kode Projek',
    'Kode Perusahaan Investasi',
    'Kode Syarat Pembayaran',
    'Kode Arus Kas'
);

$basicValACC    = array('AJ', 'AA', 'AB', 'AC', 'AE', 'AF', 'AG', 'AH', 'AI', 'AK', 'AL', 'AM');



// Storing group information
$groupList    = array('Anggota', 'Staf', 'Pengurus');
$groupVal    = array('0', '1', '2',);

// Storing group information
$groupAList    = array('Staf', 'Pengurus');
$groupAVal    = array('1', '2');

// Storing surat/email information
$suratList    = array(
    'Notis Pemberitahuan Sistem',
    'Permohonan Menjadi Anggota',
    'Permohonan Berhenti Anggota',
    'Permohonan Pembiayaan Anggota',
    'Tunggakan Bayar Balik Pembiayaan',
    'Pengesahan Baki Pembiayaan',
    'Pengesahan Penerimaan Bayaran Dividen',
    'Makluman Dividen Bonus'
);
$suratVal    = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
$suratGroup    = array(0, 0, 1, 2, 2, 2, 3, 3);

// Storing senarai information
$senaraiList    = array(
    'Permohonan Anggota',
    'Kelulusan Keanggotaan',
    'Pembatalan Anggota',
    'Permohonan Pembiayaan',
    'Kelulusan Pembiayaan',
    'Pembatalan Pembiayaan',
    'Permohonan Syer',
    'Kelulusan Permohonan Syer',
    'Pembatalan Permohonan Syer',
    'Permohonan Penjualan Syer',
    'Kelulusan Penjualan Syer',
    'Pembatalan Penjualan Syer',
    'Urusniaga Bulanan'
);
$senaraiVal    = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M');

// Storing Status Information
$statusList    =    array('Dalam Proses', 'Diluluskan', 'Ditolak', 'Berhenti', 'Bersara', 'Dibatalkan');
$statusVal    =    array('0', '1', '2', '3', '4', '5');

// Storing Caruman Status Information
$carumanStatusList    =    array('Proses Semakan', 'Lulus & Proses Baucer', 'Ditolak', 'Selesai');
$carumanStatusVal    =    array('0', '1', '2', '3');

// Storing Caruman Type Information
$carumanTypeList    =    array('placeholder', 'Yuran', 'Syer');
$carumanTypeVal        =    array('0', '1', '2');

// Storing Terminate Information
$terminateList    =    array('Berhenti', 'Bersara');
$terminateVal    =    array('0', '1');

// Storing Source Information
$sourceList    =    array('Manual', 'Angkasa', 'Dokumen', 'Pindahan Data', 'Lain-Lain');
$sourceVal    =    array('0', '1', '2', '3', '4');

// Storing Post Information
$postList    =    array('Dalam Proses', 'Diposkan', 'Ditolak');
$postVal    =    array('0', '1', '2');

// Storing Dividen Information
$dividenList    =    array('Yuran', 'Syer');
$dividenVal        =    array('0', '1');

// Storing Status Information
$biayaList    =    array('Dalam Proses', 'Disediakan', 'Disemak', 'Diluluskan', 'Ditolak', 'Dibatalkan', 'Hutang Lapuk', 'Selesai');
$biayaVal    =    array('0', '1', '2', '3', '4', '5', '7', '9');

// Storing Status Information
$bajikanList =    array('Dalam Proses', 'Diluluskan', 'Ditolak', 'Selesai');
$bajikanVal     =    array('0', '1', '2', '9');

// Member status
$activeList = array('Aktif', 'Tidak Aktif');
$activeVal = array('1', '0');

// Storing surat/email information
$lapList    = array(
    'Laporan Anggota',
    'Laporan Pembiayaan',
    'Laporan Surat/Emel',
    'Laporan Akaun'
);
$lapVal    = array('A', 'B', 'C', 'D');
//*****************************************************************************************************

$getKod = "SELECT ID
			FROM codegroup a, general b
			WHERE a.codeNo = b.code
			AND groupNo = 'BRG'";
$rsKod = $conn->Execute($getKod);

$i = 0;
$kodPotongan = "";
while (!$rsKod->EOF) {
    if ($i == 0)
        $kodPotongan = $rsKod->fields(ID);
    else
        $kodPotongan = $kodPotongan . "," . $rsKod->fields(ID);
    $rsKod->MoveNext();
    $i++;
}

$kodBrg = $kodPotongan;

$getKod = "SELECT ID
			FROM `codegroup` a, general b
			WHERE a.codeNo = b.code
			AND groupNo = 'KDRN'";
$rsKod = $conn->Execute($getKod);

$i = 0;
$kodPotongan = "";
while (!$rsKod->EOF) {
    if ($i == 0)
        $kodPotongan = $rsKod->fields(ID);
    else
        $kodPotongan = $kodPotongan . "," . $rsKod->fields(ID);
    $rsKod->MoveNext();
    $i++;
}

$kodKdrn = $kodPotongan;

$getKod = "SELECT ID
			FROM `codegroup` a, general b
			WHERE a.codeNo = b.code
			AND groupNo = 'PRBD'";
$rsKod = $conn->Execute($getKod);

$i = 0;
$kodPotongan = "";
while (!$rsKod->EOF) {
    if ($i == 0)
        $kodPotongan = $rsKod->fields(ID);
    else
        $kodPotongan = $kodPotongan . "," . $rsKod->fields(ID);
    $rsKod->MoveNext();
    $i++;
}
$kodPrbd = $kodPotongan;
?>