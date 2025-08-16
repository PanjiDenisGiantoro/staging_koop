<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	checkIC.php
 *          Date 		: 	29/03/2004
 *********************************************************************************/
//include("common.php");	
include("koperasiQry.php");

$title     = 'Muat Naik Jawatan';
$max_size = "1048576"; // Max size in BYTES (1MB)

if (get_session("Cookie_groupID") == 0) {
	$pk = get_session('Cookie_userID');
	$refFileName = '?vw=uploadwinljwtn&mn=' . $mn . '&action=upload';
} else {
	$userID = dlookup("loans", "userID", "loanID=" . tosql($pk, "Text"));
	$refFileName = '?vw=uploadwinjwtn&mn=' . $mn . '&pk=' . $pk . '&userID=' . $userID . '&action=upload';
}

if ($action == 'upload') {
	$filename = $_FILES["filename"]["name"];
	$file_basename = substr($filename, 0, strripos($filename, '.')); // get file extention
	$file_ext = substr($filename, strripos($filename, '.')); // get file name
	$filesize = $_FILES["filename"]["size"];
	$allowed_file_types = array('.doc', '.docx', '.rtf', '.pdf', '.jpg', '.png', '.gif');

	if (in_array($file_ext, $allowed_file_types) && ($filesize < 1048576)) {
		// Rename file
		$newfilename = md5($file_basename) . $file_ext;
		if (file_exists("upload_jwtn/" . $newfilename)) {
			// file already exists error
			echo '<font color="red">Anda telah pun memuat naik fail ini.</font>';
		} else {
			move_uploaded_file($_FILES["filename"]["tmp_name"], "upload_jwtn/" . $newfilename);

			if (get_session("Cookie_groupID") == 0) {
				$sSQL = "";
				$sWhere = "";
				$sWhere = "userID=" . tosql($pk, "Text");
				$sWhere = " WHERE (" . $sWhere . ")";
				$sSQL	= "UPDATE userloandetails SET " .
					"desc_jwtn='" . $desc_jwtn . "'," .
					" jwtn_img= '" . $newfilename . "' ";
				$sSQL = $sSQL . $sWhere;
				$rs = &$conn->Execute($sSQL);
				echo "File uploaded successfully.";
				print '<script language="javascript">';

				print 'window.location.href="?vw=biayaEdit&mn=' . $mn . '&userID=' . $pk . '&pic=' . $newfilename . '&action=view"';
				print '</script>';
			} else {
				$sSQL = "";
				$sWhere = "";
				$sWhere = "userID=" . tosql($userID, "Text");
				$sWhere = " WHERE (" . $sWhere . ")";
				$sSQL	= "UPDATE userloandetails SET " .
					"desc_jwtn='" . $desc_jwtn . "'," .
					" jwtn_img= '" . $newfilename . "' ";

				$sSQL = $sSQL . $sWhere;
				$rs = &$conn->Execute($sSQL);
				echo "File uploaded successfully.";
				print '<script language="javascript">';

				print 'window.location.href="?vw=biayaEdit&mn=' . $mn . '&pk=' . $pk . '&userID=' . $userID . '&pic=' . $newfilename . '&action=view"';
				print '</script>';
			}
		}
	} elseif (empty($file_basename)) {
		// file selection error
		echo '<font color="red">Sila pilih fail untuk dimuat naik.</font>';
	} elseif ($filesize > 1048576) {
		// file size error
		echo '<font color="red">Fail yang anda cuba muat naik terlalu besar.</font>';
	} else {
		// file type error
		echo '<font color="red">Hanya fail jenis ini dibenarkan untuk dimuat naik: ' . implode(', ', $allowed_file_types);
		'</font>';
		unlink($_FILES["file"]["tmp_name"]);
	}
}

?>

<body>
	<style>
		body {
			font-family: sans-serif;
			background-color: #eeeeee;
		}

		.file-upload {
			background-color: #ffffff;
			width: 600px;
			margin: 0 auto;
			padding: 20px;
		}

		.file-upload-btn {
			width: 100%;
			margin: 0;
			color: #fff;
			background: #495057;
			border: none;
			padding: 10px;
			border-radius: 4px;
			/* border-bottom: 4px solid #35a989; */
			transition: all .2s ease;
			outline: none;
			/* text-transform: uppercase; */
			/* font-weight: 500; */
		}

		.file-upload-btn:hover {
			background: #35a989;
			color: #ffffff;
			transition: all .2s ease;
			cursor: pointer;
		}

		.file-upload-btn:active {
			border: 0;
			transition: all .2s ease;
		}

		.file-upload-content {
			display: none;
			text-align: center;
		}

		.file-upload-input {
			position: absolute;
			margin: 0;
			padding: 0;
			width: 100%;
			height: 100%;
			outline: none;
			opacity: 0;
			cursor: pointer;
		}

		.image-upload-wrap {
			margin-top: 20px;
			border: 2px dashed #BEBEBE;
			position: relative;
		}

		.image-dropping,
		.image-upload-wrap:hover {
			background-color: #ffffff;
			/* border: 1px dashed #ffffff; */
		}

		.image-title-wrap {
			padding: 0 15px 15px 15px;
			color: #222;
		}

		.drag-text {
			text-align: center;
		}

		.drag-text i {
			text-align: center;
			line-height: 1;
			font-size: 70px;
			vertical-align: middle;
			margin-top: 100px;
		}

		.drag-text h4 {
			text-align: center;
			font-size: 20px;
			margin-bottom: 100px;
		}


		.file-upload-image {
			max-height: 200px;
			max-width: 200px;
			margin: auto;
			padding: 20px;
		}

		.remove-image {
			width: 200px;
			margin: 0;
			color: #fff;
			background: #cd4535;
			border: none;
			padding: 10px;
			border-radius: 4px;
			/* border-bottom: 4px solid #b02818; */
			transition: all .2s ease;
			outline: none;
			/* text-transform: uppercase;
  font-weight: 700; */
		}

		.remove-image:hover {
			background: #c13b2a;
			color: #ffffff;
			transition: all .2s ease;
			cursor: pointer;
		}

		.remove-image:active {
			border: 0;
			transition: all .2s ease;
		}
	</style>

	<script>
		function readURL(input) {
			if (input.files && input.files[0]) {

				var reader = new FileReader();

				reader.onload = function(e) {
					$('.image-upload-wrap').hide();

					$('.file-upload-image').attr('src', e.target.result);
					$('.file-upload-content').show();

					$('.image-title').html(input.files[0].name);
				};

				reader.readAsDataURL(input.files[0]);

			} else {
				removeUpload();
			}
		}

		function removeUpload() {
			$('.file-upload-input').replaceWith($('.file-upload-input').clone());
			$('.file-upload-content').hide();
			$('.image-upload-wrap').show();
		}
		$('.image-upload-wrap').bind('dragover', function() {
			$('.image-upload-wrap').addClass('image-dropping');
		});
		$('.image-upload-wrap').bind('dragleave', function() {
			$('.image-upload-wrap').removeClass('image-dropping');
		});
	</script>
</body>
<?php

?>
<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<?php
print '<h4 class="card-title"><i class="fas fa-upload"></i>&nbsp;' . strtoupper($title) . '</h4>
		<hr class="hr1 text-secondary">
		<input type="hidden" name="action">
		<div class="table-responsive">
		<form action="' . $refFileName . '" method=post  enctype="multipart/form-data">
		File (max size: ' . $max_size . ' bytes/' . ($max_size / 1024) . ' kb):<br>
		<input type="hidden" name="action" value="upload">
        <input type="hidden" name="pk" value="' . $pk . '">
        <input type="hidden" name="update" value="' . $up . '">';
?>
<div class="file-upload">
	<!-- <button class="file-upload-btn" type="button" onclick="$('.file-upload-input').trigger( 'click' )">Add Image</button> -->

	<div class="image-upload-wrap">
		<input class="file-upload-input" name="filename" type='file' onchange="readURL(this);" accept="image/*" />
		<div class="drag-text">
			<i class="bx bx-cloud-upload" style="color: #BEBEBE; margin-bottom: -5px;"></i>
			<h4 style="margin-top: -5px;">Drag fail di sini atau klik untuk memuat naik.</h4>
		</div>
	</div>
	<div class="file-upload-content">
		<img class="file-upload-image" src="#" alt="your image" />
		<div class="image-title-wrap">
			<button type="button" onclick="removeUpload()" class="remove-image">Remove <span class="image-title">Uploaded Image</span></button>
		</div>
	</div>
	<div class="mt-3">
		Description :
		<input type="text" lenght="30" size="86" class="form-control" id="desc_jwtn" name="desc_jwtn">
	</div>
</div>
<?php
print '
	<center>
		<input type="button" class="btn waves-effect waves-light" style="background-color: white; color: #495057; border: 1px solid gray;" value="Kembali" onClick="window.location.href=\'?vw=biayaEdit&mn=' . $mn . '\';">
		<input type="submit" class="btn btn-primary w-md waves-effect waves-light" value="Muat Naik Fail">													
	</center>
</form>';
include("footer.php");
