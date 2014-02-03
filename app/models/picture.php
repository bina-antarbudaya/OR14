<?php

// steps
// 1. upload original through ->upload() then ->save()
// 2. resize using jQuery on a dedicated page
// 3. crop using ->crop()
// 4. generate thumbnail using ->generate_thumbnail()
// 5. ->save()
// 6. associate with user

// all files are saved under the path defined in conf picture_upload_path

require __DIR__ . '/../vendor/autoload.php';

use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;

class Picture extends HeliumRecord {
	
	public $upload_path = '';
	public $public_path = '';
	public $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
	public $width_ratio = 3;
	public $height_ratio = 4;

	public $original_filename;
	public $cropped_filename;
	public $thumbnail_filename;
	public $format;
	public $original_width;
	public $original_height;
	public $cropped_width;
	public $cropped_height;
	public $processed = false;
	public $applicant_id = 0;

	public $upload_error = '';

	// Azure internal variables
	private $use_azure;
	private $blobRestProxy;

	public function init() {
		$this->upload_path = Helium::conf('picture_upload_path');
		$this->public_path = Helium::conf('picture_public_path');
		// $this->belongs_to('applicant');

		$this->use_azure = Helium::conf('use_azure_storage');
	}

	public function generate_filename() {
		return sha1(microtime()) . '_' . sha1(mt_rand());
	}

	public function upload_original($file_array) { // $file_array is $_FILE['userfile']
		extract($file_array);

		// check upload validity
		if (!is_uploaded_file($tmp_name)) {
			$this->upload_error = 'not_uploaded_file';
			return false;
		}

		// Find out the image dimensions and type
		list($original_width, $original_height, $type) = getimagesize($tmp_name);

		$format = image_type_to_extension($type, false);

		// check format validity
		// let's just check extensions here
		if (!in_array($format, $this->allowed_types)) {
			$this->upload_error = 'invalid_format';
			return false;
		}

		$filename = $this->generate_filename() . '.' . $format;

		// Move the uploaded file to persistent storage
		$move = $this->move_uploaded_file($tmp_name, $filename);
		if (!$move) {
			$this->upload_error = 'failed_move';
			return false;
		}

		// Success!

		$this->original_filename = $filename;
		$this->format = $format;
		$this->original_width = $original_width;
		$this->original_height = $original_height;

		return true;
	}

	public function get_source_image($filename) {
		if ($this->use_azure) {
			$blobRestProxy = $this->getBlobRestProxy();
			$container = Helium::conf('azure_storage_picture_container');

			try {
				$blob = $blobRestProxy->getBlob($container, $filename);
				$length = $blob->getProperties()->getContentLength();
				$contents = fread($blob->getContentStream(), $length);
				$source = @imagecreatefromstring($contents);
			}
			catch (ServiceException $e) {
				$code = $e->getCode();
				$error_message = $e->getMessage();
				echo $code . ': ' . $error_message;
				return false;
			}
		}
		else {
			$full_path = $this->upload_path . '/' . $filename;
			$contents = file_get_contents($full_path);
			$source = @imagecreatefromstring($contents);
		}

		return $source;
	}

	public function crop($params = array()) {

		// adjust memory limits
		// ini_set('memory_limit', '128M');
		
		// // debug
		// extract($params);
		// $original_full_path = $this->upload_path . '/' . $this->original_filename;
		// 
		// $source = imagecreatefromjpeg($original_full_path);
		// var_dump($source);
		// imagedestroy($source);
		// exit;

		$ideal = $this->get_default_crop_dimensions();

		$mw = $this->original_width;
		$mh = $this->original_height;
		$format = $this->format;

		$checks = array();
		$checks[] = $params['x'] <= $mw;
		$checks[] = $params['y'] <= $mh;
		$checks[] = $params['width'] >= 60;
		$checks[] = $params['height'] >= 80;
		$checks[] = $params['width'] <= $mw;
		$checks[] = $params['height'] <= $mh;
		
		foreach ($checks as $check) {
			if (!$check) {
				$params = $ideal;
				break;
			}
		}

		extract($params);
		
		$max_width = 900;
		$max_height = 1200;
		$end_width = $width;
		$end_height = $height;
		if ($width > $max_width || $height > $max_height) {
			$end_width = $max_width;
			$end_height = $max_height;
		}

		$source = $this->get_source_image($this->original_filename);

		// Initiate canvas
		$canvas = imagecreatetruecolor($end_width, $end_height);

		// paint to canvas
		$paint = @imagecopyresampled($canvas, $source, 0, 0, $x, $y, $end_width, $end_height, $width, $height);
		if (!$paint)
			return false;

		$raw_filename = strstr($this->original_filename, '.', true);
		$cropped_filename = $raw_filename . '_cropped.jpg';
		$this->cropped_filename = $cropped_filename;

		$save = $this->save_image($canvas, $cropped_filename);
		if (!$save)
			return false;

		$this->cropped_width = $width;
		$this->cropped_height = $height;
		$this->save();
		
		// delete the original image. bye!
		$this->delete_image($this->original_filename);
		return true;
	}
	
	public function generate_thumbnail() {
		if (!$this->cropped_filename)
			return false;

		$source = $this->get_source_image($this->cropped_filename);
		$canvas = @imagecreatetruecolor(150, 200); // thumbnail is 150x200
		
		// this is enough
		$resize = @imagecopyresampled($canvas, $source, 0, 0, 0, 0, 150, 200, $this->cropped_width, $this->cropped_height);
		if (!$resize)
			return false;

		$raw_filename = strstr($this->original_filename, '.', true);
		$thumbnail_filename = $raw_filename . '_thumb.jpg';
		$this->thumbnail_filename = $thumbnail_filename;

		$save = $this->save_image($canvas, $thumbnail_filename);
		if (!$save)
			return false;

		$this->save();

		return true;
	}

	private function delete_image($filename) {
		if ($this->use_azure) {

		}
		else {
			@unlink($this->upload_path . '/' . $filename);
		}
	}

	private function move_uploaded_file($tmp_name, $destination) {
		if ($this->use_azure) {
			return $this->upload_to_azure($tmp_name, $destination);
		}
		else {
			return move_uploaded_file($tmp_name, $this->upload_path . '/' . $destination);
		}
	}

	private function getBlobRestProxy() {
		if (!$this->blobRestProxy) {
			$connectionString = Helium::conf('azure_storage_connection_string');
			$this->blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);
		}

		return $this->blobRestProxy;
	}

	private function save_image($canvas, $filename) {
		if ($this->use_azure) {
			// Azure save magic here
			$temp_filename = tempnam(sys_get_temp_dir(), Helium::conf('app_name'));
			$temp_save = @imagejpeg($canvas, $temp_filename);
			if ($temp_save) {
				// Proceed to uploading to blob storage
				$save_cloud = $this->upload_to_azure($temp_filename, $filename);
				return $save_cloud;
			}
			else {
				return false;
			}
		}
		else {
			$save = @imagejpeg($canvas, $this->upload_path . '/' . $filename);
			return $save;
		}
	}

	private function upload_to_azure($original_filename, $destination_filename) {
		$connectionString = Helium::conf('azure_storage_connection_string');
		$container = Helium::conf('azure_storage_picture_container');

		$blobRestProxy = $this->getBlobRestProxy();
		$content = fopen($original_filename, 'r');
		$blob_name = $destination_filename;

		try {
			$blobRestProxy->createBlockBlob($container, $blob_name, $content);
		}
		catch (ServiceException $e) {
			$code = $e->getCode();
			$error_message = $e->getMessage();
			var_dump($container);
			echo $code . ': ' . $error_message;
			return false;
		}

		return true;
	}

	public function process($params) {
		return $this->crop($params) && $this->generate_thumbnail();
	}

	public function get_default_crop_dimensions() {
		$width = $this->original_width;
		$height = $this->original_height;

		// get default crop dimensions
		$ideal = compact('width', 'height');
		$ideal['x'] = $ideal['y'] = 0;
		$ratio = $width / $height;
		if ($ratio < 0.75) {
			// image is taller than 3:4.
			$h = $ideal['height'] = floor((4/3) * $width);
			$ideal['y'] = floor(($height - $h) / 2);
		}
		elseif ($ratio > 0.75) {
			// image is wider than 3:4
			$w = $ideal['width'] = ceil((3/4) * $height);
			$ideal['x'] = floor(($width - $w) / 2);
		}
		
		return $ideal;
	}

	public function get_original_url() {
		return $this->public_path . '/' . $this->original_filename;
	}

	public function get_cropped_url() {
		return $this->public_path . '/' . $this->cropped_filename;
	}
	
	public function get_cropped_path() {
		return $this->upload_path . '/' . $this->cropped_filename;
	}

	public function get_thumbnail_url() {
		return $this->public_path . '/' . $this->thumbnail_filename;
	}

	public function delete_files() {
		$vars = array('original_filename', 'cropped_filename', 'thumbnail_filename');
		foreach ($vars as $var) {
			$file = $this->upload_path . '/' . $this->$var;
			if (file_exists($file))
				@unlink($file);
		}
	}

	public function before_destroy() {
		$this->delete_files();
	}

	public function __wakeup() {
		parent::__wakeup();
		$this->init();
	}
}