<?php
class IMGupload extends SplFileObject {
	public function __construct($srcImage, $sku) {
		$DestinationDirectory = '../public/uploads/catalog/'; // Upload Directory
		if (! empty ( $srcImage )) {
			// get the url
			$url = $srcImage;
			// add time to the current filename
			$name = basename ( $url );
			$file = new SplFileInfo ( $url );
			$ext = $file->getExtension ();
			$name = $sku;
			$name = $name . "." . $ext;
			$upload = file_put_contents ( $DestinationDirectory . "$name", file_get_contents ( $url ) );
		}
	}
}
?>