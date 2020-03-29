<?php

Class Apps {

	function __construct(){
	}

	public function jsonDecode($str) {
		if($str == NULL) {
			return [];
		}

		return json_decode($str, 1);
	}

	public function redirect($r) {
		header("Location: ".$r);
	}


	public function html($str) {
		return htmlspecialchars_decode(stripslashes($str));
	}

	public function prettyDateToTS(string $date) {
		$date = explode('/', $date);

		return $date[2] . '-' . $date[0] . '-' . $date[1];
	}

	public function now() {
		return date('Y-m-d h:i:s');
	}

	public function imgSize($file,$w,$h) {
	// File and new size
		$filename = $file;

		if(preg_match("/.jpg/i", "$file")){
		   header('Content-type: image/jpg');
		}
		if(preg_match("/.jpeg/i", "$file")){
		   header('Content-type: image/jpeg');
		}
		if (preg_match("/.png/i", "$file")){
		   header('Content-type: image/png');
		}
		if (preg_match("/.gif/i", "$file")){
		   header('Content-type: image/gif');
		}

		// Content type

		// Get new sizes
		list($width, $height) = getimagesize($filename);
		$newwidth = $w;
		$newheight = $h;

		// Load
		$thumb = imagecreatetruecolor($newwidth, $newheight);
		if(preg_match("/.jpg/i", "$file")){
		  $source = imagecreatefromjpeg($filename);
		}
		if(preg_match("/.jpeg/i", "$file")){
		  $source = imagecreatefromjpeg($filename);
		}
		if (preg_match("/.png/i", "$file")){
		   $source = imagecreatefrompng($filename);
		}
		if (preg_match("/.gif/i", "$file")){
		   $source = imagecreatefromgif($filename);
		}

		// Resize
		imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

		// Output
		if(preg_match("/.jpg/i", "$file")){
			imagejpeg($thumb);
		}
		if(preg_match("/.jpeg/i", "$file")){
			imagejpeg($thumb);
		}
		if (preg_match("/.png/i", "$file")){
			imagepng($thumb);
		}
		if(preg_match("/.gif/i", "$file")){
			imagegif($thumb);
		}

	}

	public function format_telephone($phone_number) {
	    $cleaned = preg_replace('/[^[:digit:]]/', '', $phone_number);
	    preg_match('/(\d{3})(\d{3})(\d{4})/', $cleaned, $matches);
	    return "({$matches[1]}) {$matches[2]}-{$matches[3]}";
	}

	public function permaLink($url) {
		# Prep string with some basic normalization
	    $url = strtolower($url);
	    $url = strip_tags($url);
	    $url = stripslashes($url);
	    $url = html_entity_decode($url);

	    # Remove quotes (can't, etc.)
	    $url = str_replace('\'', '', $url);

	    # Replace non-alpha numeric with hyphens
	    $match = '/[^a-z0-9]+/';
	    $replace = '-';
	    $url = preg_replace($match, $replace, $url);

	    $url = trim($url, '-');

	    return $url;
	}

	public function visitor_country() {
		$ip = $_SERVER["REMOTE_ADDR"];
		if(filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)){
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		if(filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)){
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		$result = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip))->geoplugin_countryName;
		return $result <> NULL ? $result : "Unknown";
	}

	public function is_crawler($user_agent) {
		$crawlers=''
				. 'AbachoBOT|accoona|AcioRobot|AdsBot-Google|AltaVista|ASPSeek|Baidu|'
						. 'Charlotte|Charlotte t|CocoCrawler|DotBot|Dumbot|eStyle|'
								. 'FeedFetcher-Google|GeonaBot|Gigabot|Google|Googlebot|IDBot|Java VM|'
										. 'LiteFinder|Lycos|msnbot|msnbot-media|MSRBOT|QihooBot|Rambler|Scooter|'
												. 'ScrubbyBloglines subscriber|Sogou head spider|Sogou web spider|'
														. 'Sosospider|Superdownloads Spiderman|WebAlta Crawler|Yahoo|'
																. 'Yahoo! Slurp China|Yeti|YoudaoBot|' ;
		//$is_crawler = (preg_match("/$crawlers/i", $user_agent) > 0); // 1 million reps = 15.2711 secs
		$is_crawler = ((stripos($crawlers, $user_agent) !== false) ? true : false); // 1 million reps = 13.9157 secs
		if($is_crawler){
			return $is_crawler;
		}else{
			return false;
		}
	}

	public function addPageStat($pageid) {
		if(is_crawler($_SERVER['HTTP_USER_AGENT'])==1){
			$insert_q = 'INSERT INTO cms_stats_robots (userip,useragent,pageid,country,datetime,date,month,year) VALUES ("'.$_SERVER['REMOTE_ADDR'].'","'.$_SERVER['HTTP_USER_AGENT'].'","'.$pageid.'","'.visitor_country().'","'.date("Y-m-d H:i:s").'","'.date("d").'","'.date("m").'","'.date("Y").'")';
			$insert_r = mysql_query($insert_q);
		}else{
			$insert_q = 'INSERT INTO cms_stats (userip,useragent,pageid,country,datetime,date,month,year) VALUES ("'.$_SERVER['REMOTE_ADDR'].'","'.$_SERVER['HTTP_USER_AGENT'].'","'.$pageid.'","'.visitor_country().'","'.date("Y-m-d H:i:s").'","'.date("d").'","'.date("m").'","'.date("Y").'")';
			$insert_r = mysql_query($insert_q);
		}
	}

	public function sett() {

		$s_q = 'SELECT * FROM crm_settings';
		$s_r = mysql_query($s_q);
		$s = mysql_fetch_array($s_r);

		$sett = array();
		$sett['email'] = $s['email'];
		$sett['address'] = $s['address'];
		$sett['address2'] = $s['address2'];
		$sett['city'] = $s['city'];
		$sett['stateprov'] = $s['stateprov'];
		$sett['postcode'] = $s['postcode'];
		$sett['phone'] = $s['phone'];
		$sett['fax'] = $s['fax'];
		$sett['logo'] = $s['logo'];
		$sett['sms'] = $s['sms'];
		$sett['meta_keywords'] = $s['meta_keywords'];
		$sett['meta_description'] = $s['meta_description'];
		$sett['name'] = $s['name'];

		return $sett;
	}

	public function isJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	public function age($d) {
		$dob = strtotime($d);
		$current_time = time();

		$age_years = date('Y',$current_time) - date('Y',$dob);
		$age_months = date('m',$current_time) - date('m',$dob);
		$age_days = date('d',$current_time) - date('d',$dob);

		if ($age_days < 0) {
			$days_in_month = date('t',$current_time);
			$age_months--;
			$age_days= $days_in_month + $age_days;
		}

		if ($age_months < 0) {
		$age_years--;
		$age_months = 12 + $age_months;
		}

		return ($age_years > 0 ?$age_years.' years and ':'') .
			   ($age_months > 0?$age_months . ' months and ':'') .
			   ($age_days > 0?$age_days . ' days':'');
	}

	public function searchForId($id, $array, $column) {
	   foreach ($array as $key => $val) {
	       if ($val[$column] == $id) {
	           return $array[$key];
	       }
	   }
	   return null;
	}

	public function timeElapsed($time) {

	    $b = strtotime("now"); //get timestamp when tweet created
		$c = strtotime($time); //get difference
		$d = $b - $c; //calculate different time values
		$minute = 60;
		$hour = $minute * 60;
		$day = $hour * 24;
		$week = $day * 7;
		if(is_numeric($d) && $d > 0) { 
			if($d < $minute) return "just now"; 
			//if less than 2 minutes
			if($d < $minute * 2) return "about a minute ago"; 
			//if less than hour
			if($d < $hour) return floor($d / $minute) . " minutes ago"; 
			//if less than 2 hours
			if($d < $hour * 2) return "about 1 hour ago"; 
			//if less than a day
			if($d < $day) return floor($d / $hour) . " hours ago"; 
			//if more than a day, but less than 2 days
			if($d > $day && $d < $day * 2) return "yesterday"; 
			//if less than a year
			if($d < $day * 365) return floor($d / $day) . " days ago"; 
			//else return more than a year
			return "over a year ago";
		}
	}

	public function html_substr($posttext, $minimum_length = 200, $length_offset = 20, $cut_words = FALSE, $dots = TRUE) {

    // $minimum_length:
    // The approximate length you want the concatenated text to be


    // $length_offset:
    // The variation in how long the text can be in this example text
    // length will be between 200 and 200-20=180 characters and the
    // character where the last tag ends

    // Reset tag counter & quote checker
    $tag_counter = 0;
    $quotes_on = FALSE;
    // Check if the text is too long
    if (strlen($posttext) > $minimum_length) {
        // Reset the tag_counter and pass through (part of) the entire text
        $c = 0;
        for ($i = 0; $i < strlen($posttext); $i++) {
            // Load the current character and the next one
            // if the string has not arrived at the last character
            $current_char = substr($posttext,$i,1);
            if ($i < strlen($posttext) - 1) {
                $next_char = substr($posttext,$i + 1,1);
            }
            else {
                $next_char = "";
            }
            // First check if quotes are on
            if (!$quotes_on) {
                // Check if it's a tag
                // On a "<" add 3 if it's an opening tag (like <a href...)
                // or add only 1 if it's an ending tag (like </a>)
                if ($current_char == '<') {
                    if ($next_char == '/') {
                        $tag_counter += 1;
                    }
                    else {
                        $tag_counter += 3;
                    }
                }
                // Slash signifies an ending (like </a> or ... />)
                // substract 2
                if ($current_char == '/' && $tag_counter <> 0) $tag_counter -= 2;
                // On a ">" substract 1
                if ($current_char == '>') $tag_counter -= 1;
                // If quotes are encountered, start ignoring the tags
                // (for directory slashes)
                if ($current_char == '"') $quotes_on = TRUE;
            }
            else {
                // IF quotes are encountered again, turn it back off
                if ($current_char == '"') $quotes_on = FALSE;
            }

            // Count only the chars outside html tags
            if($tag_counter == 2 || $tag_counter == 0){
                $c++;
            }

            // Check if the counter has reached the minimum length yet,
            // then wait for the tag_counter to become 0, and chop the string there
            if ($c > $minimum_length - $length_offset && $tag_counter == 0 && ($next_char == ' ' || $cut_words == TRUE)) {
                $posttext = substr($posttext,0,$i + 1);
                if($dots){
                   $posttext .= '...';
                }
                return $posttext;
            }
        }
    }
    return $posttext;
}

	public function getGUID() {

	    if (function_exists('com_create_guid')) {
	        return com_create_guid();
	    } else {
	        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
	        $charid = strtoupper(md5(uniqid(rand(), true)));
	        $hyphen = chr(45);// "-"
	        $uuid = chr(123)// "{"
	            .substr($charid, 0, 8).$hyphen
	            .substr($charid, 8, 4).$hyphen
	            .substr($charid,12, 4).$hyphen
	            .substr($charid,16, 4).$hyphen
	            .substr($charid,20,12)
	            .chr(125);// "}"
	        return $uuid;
	    }
	}
}

?>
