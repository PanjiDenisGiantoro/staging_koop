<?
function load($filelocation){
	if (file_exists($filelocation)){
		$newfile = fopen($filelocation,"r");
		$file_content = fread($newfile, filesize($filelocation));
		fclose($newfile);
		return $file_content;
		}
	}

/*
	Function save($file,$content)
	writes the content to the file and generates it if needed
*/
function save($filelocation,$newdatas){
	$newfile = fopen($filelocation,"w+");
	fwrite($newfile, $newdatas);
	fclose($newfile);
	}

/*
	Function reverse($array)
	reverses an array
*/
function reverse($srcarray){
	$backarray=array();
	for ($i=sizeof($srcarray);$i>0;$i--){
		$backarray[] = $srcarray[$i];
		}
	return $backarray;
	}

/*
	Function namefiler($array,$filter)
	filters out all the items that apply to filter and returns the cleaned array
*/	
function namefilter($array,$filter){
	$temparray=array();
	$searchsize=strlen($filter);
		for ($r=0;$r<sizeof($array);$r++){
			if (substr($array[$r],0,$searchsize) != $filter){$temparray[]=$array[$r];}
		}
	return $temparray;
	}

/*
	Function directory($directory,$filters)
	reads the content of $directory, takes the files that apply to $filter and returns an 
	array of the filenames.
	You can specify which files to read, for example
	$files = directory(".","jpg,gif");
		gets all jpg and gif files in this directory.
	$files = directory(".","all");
		gets all files.
*/	
function directory($dir,$filters){
	$handle=opendir($dir);
	$files=array();
	if ($filters == "all"){while(($file = readdir($handle))!==false){$files[] = $file;}}
	if ($filters != "all"){
		$filters=explode(",",$filters);
		while (($file = readdir($handle))!==false) {
			for ($f=0;$fsizeof($filters);$f++):
				$system=explode(".",$file);
				if ($system[1] == $filters[$f]){$files[] = $file;}
			endfor;
		}
	}
	closedir($handle);
	return $files;
	}

/*
	Function createimage($name,$filename,$makeimg,$new_w,$new_h,$backrgb,$border,$borderrgb)
	creates a resized image
	variables:
	$name		Original filename
	$filename	Filename of the resized image
	$makeimg	type of image to be generated (jpg|png|gif)
	$new_w		width of resized image
	$new_h		height of resized image
	$backrgb	html color "3399cc" of background
	$borderrgb	html color "3366ff" of border 
	$border		boolean value of border (1|0) 1 pixel rectangle around image
*/	
function createimage($name,$filename,$makeimg,$new_w,$new_h,$backrgb,$border,$borderrgb){
# get HTML colors and convert them to RGB
	$r =  hexdec(substr($backrgb, 0, 2)); 
	$g =  hexdec(substr($backrgb, 2, 2)); 
	$b =  hexdec(substr($backrgb, 4, 2)); 
	$br = hexdec(substr($borderrgb, 0, 2)); 
	$bg = hexdec(substr($borderrgb, 2, 2)); 
	$bb = hexdec(substr($borderrgb, 4, 2)); 
# create new image
	$dst_img=ImageCreateTrueColor($new_w,$new_h);
# allocate colors for background and border
	$bg=ImageColorAllocate($dst_img,$r,$g,$b);
	$borcol=ImageColorAllocate($dst_img,$br,$bg,$bb);
# initialise margins for resized image
	$margin_x=0;
	$margin_y=0;
# fill image with background color
	imagefill($dst_img,0,0,$bg);
# get file extension of source image file and read it accordingly
	$system=explode(".",$name);
	if ($system[1] == "jpeg" or $system[1] == "jpg" or $system[1] == "JPEG" or $system[1] == "JPG"){$src_img=imagecreatefromjpeg("../products/".$name);}
	if ($system[1] == "gif" or $system[1] == "GIF"){$src_img=imagecreatefromgif("../products/".$name);}
	if ($system[1] == "png" or $system[1] == "PNG"){$src_img=imagecreatefrompng("../products/".$name);}
# get dimensions of old image
	$old_x=imagesx($src_img);
	$old_y=imageSY($src_img);
# Resizing algo, checks which value is bigger and centers the new image accordingy
	if ($old_x > $old_y) {
		$thumb_w=$new_w+1;
		$thumb_h=$old_y*($new_h/$old_x);
		$margin_x=0;
		$margin_y=($new_h-$thumb_h)/2;
		}
	if ($old_x < $old_y) {
		$thumb_w=$old_x*($new_w/$old_y);
		$thumb_h=$new_h+1;
		$margin_x=($new_w-$thumb_w)/2;
		$margin_y=0;
		}
	if ($old_x == $old_y) {
		$thumb_w=$new_w;
		$thumb_h=$new_h;
		$margin_x=0;
		$margin_y=0;
		}
# resize source image and place the copy in the destination image
	imagecopyresampled($dst_img,$src_img,$margin_x,$margin_y,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
# if there is a border set, draw a rectangle around the thumbnail
	if ($border==1){imageRectangle($dst_img,0,0,$new_w-1,$new_h-1,$borcol);}
# get file name, add tn_ and create thumbnail according to $makeimg
	$system=explode(".",$name);
	if ($makeimg=="jpg"){
		$filename="tn_".$system[0].".jpg";
		imagejpeg($dst_img,"carian_images/".$filename); 
		}
	if ($makeimg=="gif"){
		$filename="tn_".$system[0].".gif";
		imagegif($dst_img,"carian_images/".$filename); 
		}
	if ($makeimg=="png"){
		$filename="tn_".$system[0].".png";
		imagepng($dst_img,"carian_images/".$filename); 
		}
# destroy both image objects (to save memory)
	imagedestroy($dst_img); 
	imagedestroy($src_img); 
# initialise name
	$name="";
	}

/*
	Function untag($string,$tag,mode){
	written by Chris Heilmann (info@onlinetools.org)
	filters the content of tag $tag from $string 
	when mode is 1 the content gets returned as an array
	otherwise as a string
*/
function untag($string,$tag,$mode){
	$tmpval="";
	$preg="/<".$tag.">(.*?)<\/".$tag.">/si";
	preg_match_all($preg,$string,$tags); 
	foreach ($tags[1] as $tmpcont){
		if ($mode==1){$tmpval[]=$tmpcont;}
		else {$tmpval.=$tmpcont;}
		}
	return $tmpval;
}	
?>