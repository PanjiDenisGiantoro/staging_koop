<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	checkIC.php
 *          Date 		: 	29/03/2004
 *********************************************************************************/
//include("common.php");	
include("setupinfo.php");
if (!isset($pk)) $pk = 0;

if (isset($edthl)) {
        $xcstr = "&edthl=1";
} else {
        $xcstr = '';
}

$title     = 'Informasi anggota';
$max_size = "1048576"; // Max size in BYTES (1MB)

if ($action == 'upload') {
        $strFile = $_FILES["filename"]["name"];
        if ($strFile == '') {
                print '<script>
					alert ("Tiada fail dimasukkan.");
					window.location.href="?vw=uploadwin&mn=' . $mn . '";
				</script>';
        }
        if ($_FILES["filename"]["size"] > $max_size) die("<b>File Terlalu Besar!  Sila cuba lagi...</b>");
        copy($_FILES["filename"]["tmp_name"], "upload_images/" . $_FILES["filename"]["name"]) or die("<b>Unknown error!</b>");
        echo "<b>Fail Sudah Diterima.</b>";
        $strF = "upload_images/" . $strFile;
        print '<script language="javascript">';
        if (!$pk) {
                print 'window.location.href="?vw=memberApply&pic=' . $strFile . '"';
        } else {
                if ($edthl == 1) {
                        print 'window.location.href="?vw=memberEditHL&mn=910&pk=' . $pk . '&pic=' . $strFile . '"';
                } elseif ($update <> 1) print 'window.location.href="?vw=memberEdit' . $xcstr . '&mn=' . $mn . '&pk=' . $pk . '&pic=' . $strFile . '"';
                else print 'window.location.href="?vw=memberUpdate&pic=' . $strFile . '"';
        }
        print '</script>';
}


print '<h4 class="card-title">' . strtoupper($title) . '</h4>
                            <hr class="hr1">

<input type="hidden" name="action">
<div class="table-responsive">
        <table class="table mb-3">
                <tr class="table-success">
                        <td class=Header>Sila Masukkan Foto anda:</td>
                </tr>
                <tr class="table-light">
                        <td class="Data">                                
                                <form action="?vw=uploadwin' . $xcstr . '&mn=' . $mn . '&action=upload" method=post  enctype="multipart/form-data">
                                File (max size: ' . $max_size . ' bytes/' . ($max_size / 1024) . ' kb):<br>
                                <input type="file" class="form-controlx" name="filename"><br>
                                <input type="hidden" name="action" value="upload">
                                <input type="hidden" name="pk" value="' . $pk . '">
                                <input type="hidden" name="update" value="' . $up . '">
                                <input type="submit" class="btn btn-secondary" value="Upload File">
                                </form>					
                                        
                        </td>
                </tr>
        </table>		
		
</div>';

print $detail;
include("footer.php");
