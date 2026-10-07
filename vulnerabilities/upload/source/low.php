<?php

if (isset($_POST['Upload'])) {
	$upload = $_FILES['uploaded'] ?? null;
	$image = false;
	$type = false;

	if ($upload && $upload['error'] === UPLOAD_ERR_OK &&
		$upload['size'] > 0 && $upload['size'] < 100000 &&
		is_uploaded_file($upload['tmp_name'])) {
		$info = @getimagesize($upload['tmp_name']);
		$type = $info ? $info[2] : false;
		if ($type === IMAGETYPE_JPEG || $type === IMAGETYPE_PNG) {
			$image = @imagecreatefromstring(file_get_contents($upload['tmp_name']));
		}
	}

	if ($image === false) {
		$html .= '<pre>Your image was not uploaded. We can only accept JPEG or PNG images.</pre>';
	} else {
		$extension = $type === IMAGETYPE_JPEG ? '.jpg' : '.png';
		$target_path = DVWA_WEB_PAGE_TO_ROOT . 'hackable/uploads/' . bin2hex(random_bytes(16)) . $extension;
		$saved = $type === IMAGETYPE_JPEG
			? imagejpeg($image, $target_path, 90)
			: imagepng($image, $target_path, 9);
		imagedestroy($image);

		if ($saved) {
			$html .= "<pre>{$target_path} successfully uploaded!</pre>";
		} else {
			$html .= '<pre>Your image was not uploaded.</pre>';
		}
	}
}

?>
