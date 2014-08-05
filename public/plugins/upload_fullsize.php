<?php
    
	$ThumbMaxWidth 			= 500; //Thumbnail width
	$ThumbMaxHeight 		= 500; //Thumbnail Height
	$BigImageMaxWidth 		= $_GET['maxw']; //Resize Image width to
	$BigImageMaxHeight 		= 500; //Resize Image height to
	$ThumbPrefix			= ""; //Normal thumb Prefix
	$DestinationDirectory	= '../uploads/'.$_GET['dir'].'/'; //Upload Directory
	$jpg_quality 			= 90;
	
	$newName 		= $_GET['imgname']; // NEW file name.
    $RandomNumber 	= rand(0, 9999999999); // We need same random name for both files.    
	$ImageName 		= strtolower($_FILES['userfile']['name']);
	$ImageSize 		= $_FILES['userfile']['size']; 
	$TempSrc	 	= $_FILES['userfile']['tmp_name'];
	$ImageType	 	= $_FILES['userfile']['type'];
	$process 		= true;
	$tmp_img		= imagecreatefrompng($_FILES['userfile']['tmp_name']);

	switch(strtolower($ImageType))
	{
		case 'image/png':
			$CreatedImage = imagecreatefrompng($_FILES['userfile']['tmp_name']);
			break;		
		case 'image/gif':
			$CreatedImage = imagecreatefromgif($_FILES['userfile']['tmp_name']);
			break;
		case 'image/jpeg':
			$CreatedImage = imagecreatefromjpeg($_FILES['userfile']['tmp_name']);
			break;
		default:
			die('Unsupported File!'); //output error
	}

	//get Image Size
	list($CurWidth,$CurHeight)=getimagesize($TempSrc);
	
	//get file extension, this will be added after random name
	$ImageExt = substr($ImageName, strrpos($ImageName, '.'));
  	$ImageExt = str_replace('.','',$ImageExt);
	
	//Set the Destination Image path with Random Name
	$thumb_DestRandImageName 	= $DestinationDirectory.$newName.'.'.$ImageExt; //Thumb name
	$DestRandImageName 			= $DestinationDirectory.$newName.'.'.$ImageExt; //Name for Big Image
	
	//Resize image to our Specified Size by calling our resizeImage function.
	if(resizeImage($CurWidth,$CurHeight,$BigImageMaxWidth,$BigImageMaxHeight,$DestRandImageName,$CreatedImage,$ImageType))
	{
		echo $newName.'.'.$ImageExt;
	}else{
		die('Resize Error'); //output error
	}


function resizeImage($CurWidth,$CurHeight,$MaxWidth,$MaxHeight,$DestFolder,$SrcImage,$ImageType)
{
	$ImageScale      	= min($MaxWidth/$CurWidth, $MaxHeight/$CurHeight);
	$NewWidth  			= ceil($ImageScale*$CurWidth);
	$NewHeight 			= ceil($ImageScale*$CurHeight);
	$NewCanves 			= imagecreatetruecolor($NewWidth, $NewHeight);
	
   switch(strtolower($ImageType))
	{
		case 'image/png':
				imagealphablending($NewCanves, false);
				imagesavealpha($NewCanves, true); 
				imagealphablending($SrcImage, true);
					// Resize Image
				if(imagecopyresampled($NewCanves, $SrcImage,0, 0, 0, 0, $NewWidth, $NewHeight, $CurWidth, $CurHeight))
				{
					// copy file
					if(imagepng($NewCanves,$DestFolder,9))
					{
						imagedestroy($NewCanves);
						return true;
					}
				}
			break;		
		case 'image/gif':
				imagealphablending($NewCanves, false);
				imagesavealpha($NewCanves, true); 
				imagealphablending($SrcImage, true);
				if(imagecopyresampled($NewCanves, $SrcImage,0, 0, 0, 0, $NewWidth, $NewHeight, $CurWidth, $CurHeight))
				{
					// copy file
					if(imagepng($NewCanves,$DestFolder,9))
					{
						imagedestroy($NewCanves);
						return true;
					}
				}
			break;
		case 'image/jpeg':
				if(imagecopyresampled($NewCanves, $SrcImage,0, 0, 0, 0, $NewWidth, $NewHeight, $CurWidth, $CurHeight))
				{
					// copy file
					if(imagejpeg($NewCanves,$DestFolder,100))
					{
						imagedestroy($NewCanves);
						return true;
					}
				}
			break;
		default:
			die('Unsupported File!'); //output error
	}
}




?>