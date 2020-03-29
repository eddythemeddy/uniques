<?php

require_once 'libs/vendor/sproutvideo/sproutvideo/lib/SproutVideo/Autoloader.php';

class VideoHelper {

	const SPROUT_API_KEY = '9d36fe0aeef553b9e077b5104723b252';
	protected $allowedTypes = array('video/mp4','video/quicktime', 'application/octet-stream');
	protected $lib;
	protected $maxFileSize = '30000000';	
	
    public function __construct() {
	
		global $eqDb;
		$this->eqDb = $eqDb;
	}

	public function loadVideos($id) {

		$cols = ['title', 'description', 'sprout_code', 'token', 'thumbnail'];
		$this->eqDb->where('user_id', $id);
		$users = $this->eqDb->get ("videos", null, $cols);
		echo json_encode($users);
	}

	public function upload($file) {

		SproutVideo_Autoloader::register();
		SproutVideo::$api_key = self::SPROUT_API_KEY;

		$dir = dirname($file["tmp_name"]);
		$destination = $dir . '/' . $file["name"];
		rename($file["tmp_name"], $destination);
		$a = SproutVideo\Video::create_video("{$destination}");

		return $a;
	}

	public function createPlaylist($username) {

		SproutVideo_Autoloader::register();
		SproutVideo::$api_key = self::SPROUT_API_KEY;

		$a = SproutVideo\Playlist::create_playlist(
			array(
				'title' => $username . ' Playlist',
				'privacy' => 2
			)
		);
		
		if(isset($a['id'])) {
			return $a['id'];
		}

		return false;
	}

	public function uploadVideo($title, $description, $file)
	{

		$mimeType = mime_content_type($file["tmp_name"]);
		$shaKey = $this->shaKey($file);
		$fileSize = $file["size"];

		if(!in_array($mimeType, $this->allowedTypes)) {
			$result = ['r'=>'error', 'm' => '<strong>Error: </strong> Wrong file type'];
			echo json_encode($result);
			exit;
		}

		if($fileSize > $this->maxFileSize) {
			$result = ['r'=>'error', 'm' => '<strong>Error: </strong> Wrong Email or PasswordSorry file size is too big. Max 10 MB.'];
			echo json_encode($result);
			exit;
		}

		if($this->existsShaKey($shaKey)) {
			$result = ['r'=>'error', 'm' => '<strong>Error: </strong> Sorry this is a duplicate video.'];
			echo json_encode($result);
			exit;
		}

		$upload = $this->upload($file);

		if(!isset($upload['id']) && !isset($upload['security_token'])) {
			$result = [
						'r'=>'error',
						'm' => 'Sorry there was an error (212)', 
						'd' => $upload
					  ];
			echo json_encode($result);
			exit;
		}

		$insert = $this->insertVideo($title, $description, $upload['id'], $upload['security_token'], $shaKey, $upload['assets']['thumbnails'][1]);

		$result = [
			'r' => 'success', 
			'd' => [
				'd' => $insert,
				'id' => $upload['id'], 
				'title' => $title, 
				'description' => $description, 
				'sprout' => $upload,
				'thumbnails' => $upload['assets']['thumbnails'][1],
				'description' => $description,
				'security_token' => $upload['security_token']
			]
		];

		echo json_encode($result);
	}

	public function insertVideo($title, $description, $shortCode, $token, $shaKey, $thumbnail) {
		
		global $eqDb;

		$time = date("Y-m-d H:i:s");
		
		$data = Array (
			"sha_key" => $shaKey,
            "user_id" => $_SESSION['scouty_user_id'],
			"datetime" => $time,
			"title" => $title,
			"description" => $description,
			"sprout_code" => $shortCode,
			"thumbnail" => $thumbnail,
			"token" => $token
		);

		$id = $eqDb->insert('videos', $data);
	}

	public function deleteVideo($code) {

		SproutVideo_Autoloader::register();
		SproutVideo::$api_key = self::SPROUT_API_KEY;

		$deleted = SproutVideo\Video::delete_video($code);
		if($deleted) {
			return true;
		}
	}

	public function shaKey($file) {
		return sha1_file($file['tmp_name']);
	}

	public function existsShaKey($key) {		

		global $eqDb;

		$eqDb->where ('sha_key', $key);
		$total = $eqDb->get ('videos',null, 'COUNT(*) AS total');

		return $total[0]['total'] == 0 ? false : true;
	}
}