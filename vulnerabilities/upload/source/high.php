<?php

if (isset($_POST['Upload'])) {
	$upload = $_FILES['uploaded'] ?? null;
	$saved = false;

	if (is_array($upload) &&
		($upload['error'] ?? null) === UPLOAD_ERR_OK &&
		is_string($upload['name'] ?? null) &&
		is_string($upload['tmp_name'] ?? null) &&
		is_int($upload['size'] ?? null) &&
		$upload['size'] > 0 && $upload['size'] < 100000 &&
		is_uploaded_file($upload['tmp_name'])) {
		$extension = strtolower(pathinfo($upload['name'], PATHINFO_EXTENSION));
		$imageInfo = @getimagesize($upload['tmp_name']);
		$imageType = $imageInfo[2] ?? null;
		$width = $imageInfo[0] ?? 0;
		$height = $imageInfo[1] ?? 0;
		$validType = ($imageType === IMAGETYPE_JPEG && in_array($extension, ['jpg', 'jpeg'], true)) ||
			($imageType === IMAGETYPE_PNG && $extension === 'png');

		if ($validType && $width > 0 && $height > 0 && $width * $height <= 16000000) {
			$image = $imageType === IMAGETYPE_JPEG
				? @imagecreatefromjpeg($upload['tmp_name'])
				: @imagecreatefrompng($upload['tmp_name']);

			if ($image !== false) {
				$directory = realpath(DVWA_WEB_PAGE_TO_ROOT . 'hackable/uploads');
				if ($directory !== false && is_writable($directory)) {
					$safeExtension = $imageType === IMAGETYPE_JPEG ? 'jpg' : 'png';
					$filename = bin2hex(random_bytes(16)) . '.' . $safeExtension;
					$targetPath = $directory . DIRECTORY_SEPARATOR . $filename;
					$saved = $imageType === IMAGETYPE_JPEG
						? @imagejpeg($image, $targetPath, 90)
						: @imagepng($image, $targetPath, 9);
					if ($saved) {
						$html .= '<pre><a href="../../hackable/uploads/' . $filename . '">' . $filename . '</a> successfully uploaded!</pre>';
					} elseif (is_file($targetPath)) {
						unlink($targetPath);
					}
				}
				imagedestroy($image);
			}
		}
	}

	if (!$saved) {
		$html .= '<pre>Your image was not uploaded. We can only accept JPEG or PNG images.</pre>';
	}
}

?>
