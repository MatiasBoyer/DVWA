<?php

if( isset( $_POST[ 'Upload' ] ) ) {
	$uploaded = $_FILES[ 'uploaded' ] ?? null;
	$uploadedPath = is_array( $uploaded ) ? ( $uploaded[ 'tmp_name' ] ?? null ) : null;
	$uploadedSize = is_array( $uploaded ) ? ( $uploaded[ 'size' ] ?? null ) : null;
	$uploadedError = is_array( $uploaded ) ? ( $uploaded[ 'error' ] ?? null ) : null;
	$saved = false;

	if( $uploadedError === UPLOAD_ERR_OK && is_string( $uploadedPath ) &&
		is_int( $uploadedSize ) && $uploadedSize < 100000 &&
		is_uploaded_file( $uploadedPath ) ) {
		$imageInfo = @getimagesize( $uploadedPath );
		$imageType = $imageInfo[ 2 ] ?? null;
		$width = $imageInfo[ 0 ] ?? 0;
		$height = $imageInfo[ 1 ] ?? 0;

		if( $width > 0 && $height > 0 && $width * $height <= 16000000 &&
			in_array( $imageType, [ IMAGETYPE_JPEG, IMAGETYPE_PNG ], true ) ) {
			$image = $imageType === IMAGETYPE_JPEG
				? @imagecreatefromjpeg( $uploadedPath )
				: @imagecreatefrompng( $uploadedPath );

			if( $image !== false ) {
				$extension = $imageType === IMAGETYPE_JPEG ? 'jpg' : 'png';
				$filename = bin2hex( random_bytes( 16 ) ) . '.' . $extension;
				$uploadDirectory = realpath( DVWA_WEB_PAGE_TO_ROOT . 'hackable/uploads/' );
				if( $uploadDirectory !== false ) {
					$targetPath = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;
					$saved = $imageType === IMAGETYPE_JPEG
						? imagejpeg( $image, $targetPath, 90 )
						: imagepng( $image, $targetPath, 9 );
					if( $saved ) {
						$html .= '<pre><a href="../../hackable/uploads/' . $filename . '">' . $filename . '</a> successfully uploaded!</pre>';
					}
				}
				imagedestroy( $image );
			}
		}
	}

	if( !$saved ) {
		$html .= '<pre>Your image was not uploaded. We can only accept JPEG or PNG images.</pre>';
	}
}

?>
