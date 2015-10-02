<?php

//==============================================================================================
function upload($FILES, $file_id, $folder="", $types="",$maxsize=1000000000000000000,$filename='') {
	if(!$FILES[$file_id]['name']) return array('','No file specified');

	$file_title = $FILES[$file_id]['name'];
	$file_size = $FILES[$file_id]['size'];

	if($file_size>$maxsize)
	{
		$result = "'".$FILES[$file_id]['name']."' File Size Is larger than Max Size:".round($maxsize/1024)." Kb"; //Show error if any.
		return array('',$result);
	}

	//Get file extension
	$ext_arr = split("\.",basename($file_title));
	$ext = strtolower($ext_arr[count($ext_arr)-1]); //Get the last extension

	//Not really uniqe - but for all practical reasons, it is
	$uniqer = substr(md5(uniqid(rand(),1)),0,5);
	if($file_name=='') $file_name= $uniqer . '_' . $file_title;//Get Unique Name
	else  $file_name.=".".ext;

	$all_types = explode(",",strtolower($types));
	if($types) {
		if(in_array($ext,$all_types));
		else {
			$result = "'".$FILES[$file_id]['name']."' is not a valid file."; //Show error if any.
			return array('',$result);
		}
	}

	//Where the file must be uploaded to
	if($folder) $folder .= '/';//Add a '/' at the end of the folder
	$uploadfile = $folder . $file_name;


	$result = '';
	//Move the file from the stored location to the new location
	if (!move_uploaded_file($FILES[$file_id]['tmp_name'], $uploadfile)) {
		$result = "Cannot upload the file '".$FILES[$file_id]['name']."'"; //Show error if any.
		if(!file_exists($folder)) {
			$result .= " : Folder don't exist.";
		} elseif(!is_writable($folder)) {
			$result .= " : Folder not writable.";
		} elseif(!is_writable($uploadfile)) {
			$result .= " : File not writable.";
		}
		$file_name = '';

	} else {
		if(!$FILES[$file_id]['size']) { //Check if the file is made
			@unlink($uploadfile);//Delete the Empty file
			$file_name = '';
			$result = "Empty file found - please use a valid file."; //Show the error message
		} else {
			chmod($uploadfile,777);//Make it universally writable.
		}
	}

	return array($file_name,$result);
}

//resize -> 0 no resizing , 1 -> resize image with the first width w ,and height H,
//2 -> resize image with the width w and height H  , and make a Thumb copy whith
// width w1 height H1
function UploadImage($FILES, $file_id, $folder="", $types="",$maxsize=1000000000000000000,$file_name='',$resize=0,$w=0,$h=0,$w1=0,$h1=0,$related_ratio=0,$aspect_w=0,$aspect_h=0,$validity=0.05) {
	$windows=1;
	if(!$FILES[$file_id]['name']) return array('','No file specified');

	$file_title = $FILES[$file_id]['name'];
	$file_size = $FILES[$file_id]['size'];

	if($file_size>$maxsize)
	{
		$result = "'".$FILES[$file_id]['name']."' File Size Is larger than Max Size:".round($maxsize/1024)." Kb"; //Show error if any.
		return array('',$result);
	}

	if($related_ratio==1)
	{
		list($width, $height, $type, $attr) = getimagesize($FILES[$file_id]['tmp_name']);

		$exp_width=$width/$aspect_w;
		$exp_height=$exp_width*$aspect_h;
		if(abs($exp_height-$height)>$validity*$exp_height)
		{
			if($aspect_h ==$aspect_w)
			$result = "'".$FILES[$file_id]['name']."Sorry the image upload for (".$FILES[$file_id][name].") failed: image ratio/size does not match the required aspect ratio 1:1 (Square Image). Please resize to upload."; //Show error if any.
			else

			$result = "Sorry the image upload for (".$FILES[$file_id][name].") failed: image ratio/size does not match the required aspect ratio $aspect_w:$aspect_h. Please resize to upload."; //Show error if any.
			return array('',$result);
		}

	}

	//Get file extension
	$ext_arr = split("\.",basename($file_title));
	$ext = strtolower($ext_arr[count($ext_arr)-1]); //Get the last extension

	//Not really uniqe - but for all practical reasons, it is
	$uniqer = rand() .rand() . rand();
	if($file_name=='') $file_name= $uniqer.'.'.$ext; //. '_' . $file_title;//Get Unique Name
	else  $file_name.=".".$ext;
	$file_name=str_replace(" ","_",$file_name);
	$special = array('/','!','&','*','~','#','$','%','^','(',')','-','<','>','?','@','+','|','=','/','\\','"','\'','[',']','{','}',':',';',',');
	$file_name = str_replace($special,'_', $file_name);

	$all_types = explode(",",strtolower($types));
	if($types) {
		if(in_array($ext,$all_types));
		else {
			$result = "'".$FILES[$file_id]['name']."' is not a valid file."; //Show error if any.
			return array('',$result);
		}
	}

	//Where the file must be uploaded to
	if($folder) $folder .= '/';//Add a '/' at the end of the folder
	$uploadfile = $folder . $file_name;
	$thumb_uploadfile = $folder .'thumb_'. $file_name;

	$result = '';
	//Move the file from the stored location to the new location
	if (!move_uploaded_file($FILES[$file_id]['tmp_name'], $uploadfile)) {
		$result = "Cannot upload the file '".$FILES[$file_id]['name']."'"; //Show error if any.
		if(!file_exists($folder)) {
			$result .= " : Folder don't exist.";
		} elseif(!is_writable($folder)) {
			$result .= " : Folder not writable.";
		} elseif(!is_writable($uploadfile)) {
			$result .= " : File not writable.";
		}
		$file_name = '';

	} else {
		if(!$FILES[$file_id]['size']) { //Check if the file is made
			@unlink($uploadfile);//Delete the Empty file
			$file_name = '';
			$result = "Empty file found - please use a valid file."; //Show the error message
		}
		else
		{

			chmod($uploadfile,777);//Make it universally writable.

			//Resizing the image
			
			if($resize == 2)
			{
				if(!smart_resize_image($uploadfile,$w1,$h1,true,$thumb_uploadfile))
				$result .= 'Could not resize original image to ' . $w1 . 'x' . $h1 . ' ( Image ' . "[$cmdStatus] )";
			}



		}
	}

	return array($file_name,$result);
}

function smart_resize_image( $file, $width = 0, $height = 0, $proportional = false, $output = 'file')
{
	if ( $height <= 0 && $width <= 0 ) {
		return false;
	}


	$info = getimagesize($file);
	$image = '';


	$final_width = 0;
	$final_height = 0;
	list($width_old, $height_old) = $info;

	if (
	$proportional) {
		if ($width == 0) $factor = $height/$height_old;
		elseif ($height == 0) $factor = $width/$width_old;
		else $factor = min ( $width / $width_old, $height / $height_old);


		$final_width = round ($width_old * $factor);
		$final_height = round ($height_old * $factor);

	}
	else {

		$final_width = ( $width <= 0 ) ? $width_old : $width;
		$final_height = ( $height <= 0 ) ? $height_old : $height;
	}

	switch (
	$info[2] ) {
		case IMAGETYPE_GIF:
			$image = imagecreatefromgif($file);
			break;
		case IMAGETYPE_JPEG:
			$image = imagecreatefromjpeg($file);
			break;
		case IMAGETYPE_PNG:
			$image = imagecreatefrompng($file);
			break;
		default:
			return false;
	}

	$image_resized = imagecreatetruecolor( $final_width, $final_height );

	if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
		$trnprt_indx = imagecolortransparent($image);

		// If we have a specific transparent color
		if ($trnprt_indx >= 0) {

			// Get the original image's transparent color's RGB values
			$trnprt_color    = imagecolorsforindex($image, $trnprt_indx);

			// Allocate the same color in the new image resource
			$trnprt_indx    = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);

			// Completely fill the background of the new image with allocated color.
			imagefill($image_resized, 0, 0, $trnprt_indx);

			// Set the background color for new image to transparent
			imagecolortransparent($image_resized, $trnprt_indx);


		}
		// Always make a transparent background color for PNGs that don't have one allocated already
		elseif ($info[2] == IMAGETYPE_PNG) {

			// Turn off transparency blending (temporarily)
			imagealphablending($image_resized, false);

			// Create a new transparent color for image
			$color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);

			// Completely fill the background of the new image with allocated color.
			imagefill($image_resized, 0, 0, $color);

			// Restore transparency blending
			imagesavealpha($image_resized, true);
		}
	}


	imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);

	if ( $delete_original ) {
		if ( $use_linux_commands )
		exec('rm '.$file);
		else
		@unlink($file);
	}

	switch ( strtolower($output) ) {
		case 'browser':
			$mime = image_type_to_mime_type($info[2]);
			header("Content-type: $mime");
			$output = NULL;
			break;
		case 'file':
			$output = $file;
			break;
		case 'return':
			return $image_resized;
			break;
		default:
			break;
	}

	switch (
	$info[2] ) {
		case IMAGETYPE_GIF:
			imagegif($image_resized, $output);
			break;
		case IMAGETYPE_JPEG:
			imagejpeg($image_resized, $output);
			break;
		case IMAGETYPE_PNG:
			imagepng($image_resized, $output);
			break;
		default:
			return false;
	}

	return
	true;
}
?>
