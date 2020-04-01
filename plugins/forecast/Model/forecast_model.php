<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Forecast_Model extends Model {

	const DEFAULT_COLOR_BG = '#3395ed';
	const DEFAULT_COLOR_PAST_BG = 'rgba(62, 156, 237, 0.54)';
	const DEFAULT_COLOR_TEXT = '#fff';
	
	public function __construct() {

		global $eqDb;
	
		parent::__construct();

		$this->eqDb = $eqDb;
		$this->apps = new Apps();
	}

	public function sendMail() {
		$to =      $this->eqDb->escape($_POST['to']);
		$subject = $this->eqDb->escape($_POST['subject']);
		$message = $this->eqDb->escape($_POST['message']);
		// $mail = new PHPMailer;
		// the message
		$msg = "First line of text\nSecond line of text<img src=\"https://lh3.googleusercontent.com/-e640AMqonrk/AAAAAAAAAAI/AAAAAAAAAAA/AAKWJJMe97GoInxVx21WTiTO9rR0NPXjig/photo.jpg?sz=46\">";
		// use wordwrap() if lines are longer than 70 characters
		$msg = wordwrap($msg,70);
	/*			$headers  = "Reply-To: The Sender <sender@sender.com>\r\n";
		$headers .= "Return-Path: The Sender <sender@sender.com>\r\n";
		$headers .= "From: The Sender <senter@sender.com>\r\n";
		$headers .= "Organization: Sender Organization\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= "X-Priority: 3\r\n";
		$headers .= "X-Mailer: PHP". phpversion() ."\r\n";
		// send email
		mail($to, $subject, $msg, $headers);
die;
*/
			$mail = new PHPMailer;

			//Tell PHPMailer to use SMTP
			$mail->isSMTP();

			//Enable SMTP debugging
			// SMTP::DEBUG_OFF = off (for production use)
			// SMTP::DEBUG_CLIENT = client messages
			// SMTP::DEBUG_SERVER = client and server messages
			$mail->SMTPDebug = SMTP::DEBUG_SERVER;

			//Set the hostname of the mail server
			$mail->Host = 'smtp.gmail.com';
			// use
			// $mail->Host = gethostbyname('smtp.gmail.com');
			// if your network does not support SMTP over IPv6

			//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
			$mail->Port = 587;

			//Set the encryption mechanism to use - STARTTLS or SMTPS
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

			//Whether to use SMTP authentication
			$mail->SMTPAuth = true;

			//Username to use for SMTP authentication - use full email address for gmail
			$mail->Username = 'eddythemeddy@gmail.com';

			//Password to use for SMTP authentication
			$mail->Password = '247Ilovemomandad';

			//Set who the message is to be sent from
			$mail->setFrom('eddythemeddy@gmail.com', 'Anubir Singh');

			//Set an alternative reply-to address
			$mail->addReplyTo('eddythemeddy@gmail.com', 'Anubir Singh');

			//Set who the message is to be sent to
			$mail->addAddress($to, 'John Doe');

			//Set the subject line
			$mail->Subject = $subject;

			//Read an HTML message body from an external file, convert referenced images to embedded,
			//convert HTML into a basic plain-text alternative body
			$mail->msgHTML('<!DOCTYPE html>
			<html lang="en">
			<head>
			  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			  <title>PHPMailer Test</title>
			</head>
			<body>
			<div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
			  <h1>This is a test of PHPMailer.</h1>
			  <div align="center">
				<a href="https://github.com/PHPMailer/PHPMailer/"><img src="images/phpmailer.png" height="90" width="340" alt="PHPMailer rocks"></a>
			  </div>
			  <p>This example uses <strong>HTML</strong>.</p>
			  <p>ISO-8859-1 text: éèîüçÅñæß</p>
			</div>
			</body>
			</html>');

			//Replace the plain text body with one created manually
			$mail->AltBody = 'This is a plain-text message body';

			//Attach an image file
			$mail->addAttachment('images/phpmailer_mini.png');

			//send the message, check for errors
			if (!$mail->send()) {
				echo 'Mailer Error: '. $mail->ErrorInfo;
			} else {
				echo 'Message sent!';
				//Section 2: IMAP
				//Uncomment these to save your message in the 'Sent Mail' folder.
				#if (save_mail($mail)) {
				#    echo "Message saved!";
				#}
			}

	}

	public function getDateArrayFromRange(string $dateRange) {

		$dates = explode("|", $dateRange);

		return $dates;
	}

	public function prettifyDateRange(string $dateRange) {

		$dates     = $this->getDateArrayFromRange($dateRange);
		$startDate = $dates[0];
		$endDate   = $dates[1];

		$startDateYear = date('Y', strtotime($startDate));
		$endDateYear   = date('Y', strtotime($endDate));

		$startDateMonth = date('M', strtotime($startDate));
		$endDateMonth   = date('M', strtotime($endDate));

		$startDateDates = date('d', strtotime($startDate));
		$endDateDates   = date('d', strtotime($endDate));

		$showStartDateMonth = ($endDateMonth != $startDateMonth ? ' ' . $startDateMonth : '');
		$showStartDateYear  = ($endDateYear > $startDateYear ? ', ' . $startDateYear : '');

		$str = $startDateDates . $showStartDateMonth . $showStartDateYear . '-' .
				$endDateDates . ' ' . $endDateMonth . ', ' . $endDateYear;

		return $str;
	}

	public function deleteEvent() {

		$start     = $this->eqDb->escape($_POST['start']);
		$end       = $this->eqDb->escape($_POST['end']);
		$channel   = $this->eqDb->escape($_POST['channel']);
		$date      = $this->eqDb->escape($_POST['date']);
		$dateRange = $this->eqDb->escape($_POST['dateRange']);
		$uniqueId  = empty($_POST['eventUniqueId']) ? null : $this->eqDb->escape($_POST['eventUniqueId']);
		$fromRep   = empty($_POST['fromRepeat']) ? null : $this->eqDb->escape($_POST['fromRepeat']);

		if($fromRep != null || $fromRep != 0) {
			//if it is a repeated event
			$this->eqDb->where('channel_id', $channel);
	        $this->eqDb->where('date', $date);
	        $this->eqDb->where('updated_from_repeat', 1);

			$response = $this->eqDb->get('forecast', null, '*');

			if(count($response)) {

				$data = [
					'deleted' => 1
				];

				$this->eqDb->where('channel_id', $channel);
		        $this->eqDb->where('date', $date);
		        $this->eqDb->where('updated_from_repeat', 1);
				$this->eqDb->update('forecast', $data);

			} else {

				$data = [
					'date'                => $date,
		            'channel_id'          => $channel,
					'start_time'          => $start,
					'end_time'            => $end,
					'date_range'          => $dateRange,
					'updated_from_repeat' => 1,
					'deleted'             => 1
				];

				$this->eqDb->insert('forecast', $data);
			}
		} else {

			$data = [
				'deleted' => 1
			];

			$this->eqDb->where('id', $uniqueId);
			$this->eqDb->update('forecast', $data);
		}
	}

	public function forecastViaChannel() {

		$start   = $this->eqDb->escape($_POST['start']);
		$end     = $this->eqDb->escape($_POST['end']);
		$channel = $this->eqDb->escape($_POST['channel']);
		$date    = $this->eqDb->escape($_POST['date']);

        $this->eqDb->where('id', $channel);

		$response = $this->eqDb->getOne('channels', null, '*');

		$channelColor = empty($response['channel_color']) ? self::DEFAULT_COLOR_BG : $response['channel_color'];
		$channelText  = empty($response['channel_text']) ? self::DEFAULT_COLOR_TEXT : $response['channel_text'];

		$array = [
			'name'          => $response['name'],
			'channel_color' => $channelColor,
			'channel_text'  => $channelText,
			'main_contact'  => $response['main_contact'],
			'phone'         => $response['phone']
		];

		return $array;
	}

	public function updateForecastTime() {

		$eventType = $this->eqDb->escape($_POST['eventType']);
		$start     = $this->eqDb->escape($_POST['start']);
		$end       = $this->eqDb->escape($_POST['end']);
		$channel   = $this->eqDb->escape($_POST['channel']);
		$date      = $this->eqDb->escape($_POST['date']);
		$dateRange = $this->eqDb->escape($_POST['dateRange']);
		$notes     = empty($_POST['notes']) ? null : $this->eqDb->escape($_POST['notes']);;
		$uniqueId  = empty($_POST['eventUniqueId']) ? null : $this->eqDb->escape($_POST['eventUniqueId']);
		$recipes   = empty($_POST['recipes']) ? '[]' : $this->eqDb->escape($_POST['recipes']);
		$fromRep   = empty($_POST['fromRepeat']) ? null : $this->eqDb->escape($_POST['fromRepeat']);


		$recipes = stripcslashes($recipes);
		$recipes = json_decode($recipes, 1);

        $this->eqDb->where('channel_id', $channel);
        $this->eqDb->where('date', $date);

		$response = $this->eqDb->get('forecast', null, '*');

		if($uniqueId != null) {

	        $this->eqDb->where('id', $uniqueId);
	        $this->eqDb->where('channel_id', $channel);
	        $this->eqDb->where('date', $date);
			$this->eqDb->update('forecast', [
				'start_time' => $start,
				'end_time'   => $end,
				'notes'      => $notes,
				'event_type' => $eventType,
				'date_range' => $dateRange
			]);

			$id = $uniqueId;

		} else {

			$id = $this->eqDb->insert('forecast', [
				'date'                => $date,
	            'channel_id'          => $channel,
				'start_time'          => $start,
				'end_time'            => $end,
				'notes'               => $notes,
				'event_type' 		  => $eventType,
				'date_range'          => $dateRange,
				'updated_from_repeat' => $fromRep == null ? 0 : 1,
				'company_id'          => $_SESSION['scouty_company_id']
			]);
		}

		if($_POST['recipes']) {
			$this->insertUpdateForecastRecipes($id, $recipes, $dateRange);
		}
	}

	public function getForecastsByRange($range) {

		$this->eqDb->where('f.date_range', $range);
		$this->eqDb->where('f.deleted', 0);
		$this->eqDb->where('company_id', $_SESSION['scouty_company_id']);
		$this->eqDb->orderBy('date', 'ASC');
		$this->eqDb->orderBy('start_time', 'ASC');
		$response = $this->eqDb->get('forecast f', null, '*');
		
		$array = [];
		foreach($response as $key => $val) {
			
			$this->eqDb->where('company_id', $_SESSION['scouty_company_id']);
			$this->eqDb->where('id',$val['channel_id']);
			$channel = $this->eqDb->get('channels c', null, '*');
		
			$this->eqDb->where('company_id', $_SESSION['scouty_company_id']);
			$this->eqDb->where('forecast_id',$val['id']);
			$recipes = $this->eqDb->get('forecast_recipes r', null, '*');
			
			if(count($recipes) > 0) {
		
				foreach($recipes as $key2 => $recipe) {
		
					$recipeIng = $this->eqDb->subQuery("s");
					$recipeIng->get('ingredients', null, 'id, ppw, name');
		
					$this->eqDb->join($recipeIng, "s.id = rs.ingredient_id", "LEFT");
		
					$this->eqDb->where('recipe_sub_id', $recipe['sub_recipe_id']);
					$recipesSubIngs = $this->eqDb->get('recipes_sub_ingredients rs', null, '*');
		
					// $recipes[$key2]['ingredients'] = [];
					$recipes[$key2]['ingredients'] = $recipesSubIngs;
		
					foreach($recipesSubIngs as $key3 => $recipesSubIng) {
						$recipes[$key2]['ingredients'][$key3]['total'] = $recipe['total'] * 
						$recipesSubIng['ingredient_weight'];
					}
		
				}
			
				array_push($array, [
						'date'    => $val['date'],
						'start'   => $val['start_time'],
						'end'     => $val['end_time'],
						'recipes' => $recipes,
						'channel' => $channel
					]
				);
			}
		}
		
		$group = [];
		
		foreach ($array as $key => $value) {
			$group[$value['channel'][0]['name']][] = $value;
		}
		
		foreach ($group as $key => $value ) {
			foreach($value as $key2 => $val2) {
				foreach($val2['recipes'] as $key3 => $val3) {
					$group[$key][$key2]['recipes'][$val3['recipe_name']][] = $val3;
					unset($group[$key][$key2]['recipes'][$key3]);
				}
			}
		}
		
		foreach ( $group as $key => $value) {
			$group[$key]['dates']       = [];
			$group[$key]['channelInfo'] = (object)[];
		
			foreach($value as $key2 => $val2) {
				$group[$key]['channelInfo'] = $val2['channel'];
				unset($val2['channel']);
				$group[$key]['dates'][$val2['date']][] = $val2;
				unset($group[$key][$key2]);
			}
		}
		
		foreach ( $group as $key => $value) {
		
			$group[$key]['ingredients'] = [];
		
			foreach($value['dates'] as $key2 => $val2) {
		
				foreach($val2 as $key3 => $val3) {
		
					$group[$key]['dates'][$key2][$key3]['ingredients'] = [];
				
					foreach($val3['recipes'] as $key4 => $val4) {
		
						foreach($val4 as $key5 => $val5) {
							foreach($group[$key]['dates'][$key2][$key3]['recipes'][$key4][$key5]['ingredients'] as $ing => $ingr) {
		
								if(array_key_exists($ingr['ingredient_id'], $group[$key]['ingredients'])) {
									$group[$key]['ingredients'][$ingr['ingredient_id']]['total'] = $group[$key]['ingredients'][$ingr['ingredient_id']]['total'] + $ingr['total'];
								} else {
									$group[$key]['ingredients'][$ingr['ingredient_id']] = [
										'ingredient_id' => $ingr['ingredient_id'],
										'name' => $ingr['name'],
										'total' => $ingr['total']
									];
								}
		
								if(array_key_exists($ingr['ingredient_id'], $group[$key]['dates'][$key2][$key3]['ingredients'])) { 
									$group[$key]['dates'][$key2][$key3]['ingredients'][$ingr['ingredient_id']]['total'] = $group[$key]['dates'][$key2][$key3]['ingredients'][$ingr['ingredient_id']]['total'] + $ingr['total'];
		
								} else {
									$group[$key]['dates'][$key2][$key3]['ingredients'][$ingr['ingredient_id']] = [
										'ingredient_id' => $ingr['ingredient_id'],
										'name' => $ingr['name'],
										'total' => $ingr['total']
									];
								}
							}
						}
					}
				}
			}
		}
		
		return [
			'rangeUgly' => $range,
			'range'     => $this->prettifyDateRange($range),
			'data'      => $group
		];
	}

	public function createPurchaseOrder($channelRange) {

		$range = $this->eqDb->escape($channelRange);

		$this->eqDb->where('date_range', $range);
		$this->eqDb->where('company_id', $_SESSION['scouty_company_id']);

		$forecastRecipes = $this->eqDb->get('forecast_recipes', null, '*');

        if (!count($forecastRecipes)) {
            header('location: ' . _SITEROOT_);
            exit;
        }

		$array = [];
		$totalExpenditure = 0;
		foreach($forecastRecipes as $key => $val) {

			$recipeSub = $this->eqDb->subQuery("s");
			$recipeSub->get("ingredients", null, 'id,ppw,name');

			$this->eqDb->join($recipeSub, "s.id = r.ingredient_id", "LEFT");

			$this->eqDb->where('recipe_sub_id', $val['sub_recipe_id']);
			$ingredients = $this->eqDb->get('recipes_sub_ingredients r', null, '*');
			
			if(count($ingredients) > 0)

			foreach($ingredients as $ing => $ings) {

				$total              = floatval($val['total']);
				$ingredientWeight   = floatval($ings['ingredient_weight']);
				$pricePerIngredient = floatval($ings['ppw']);

				$totalIngWeight   = $total * $ingredientWeight;
				$totalPrice       = $pricePerIngredient * $totalIngWeight;
				$totalExpenditure = $totalExpenditure + $totalPrice;

				if(array_key_exists($ings['id'], $array)) {

					$totalIngWeight = $array[$ings['id']]['totalIngs'] + $totalIngWeight;
					$totalPrice     = $array[$ings['id']]['totalPrice'] + $totalPrice;

					$array[$ings['id']]['totalIngs']  = $totalIngWeight;
					$array[$ings['id']]['totalPrice'] = $totalPrice;
				} else {
					$array[$ings['id']] = [
						'name'       => $ings['name'],
						'rate'       => $pricePerIngredient,
						'totalIngs'  => $totalIngWeight,
						'totalPrice' => $totalPrice
					];
				}
			}
		}

		return [
			'rangeUgly'        => $range,
			'range'            => $this->prettifyDateRange($range),
			'data'             => $array,
			'totalExpenditure' => $totalExpenditure
		];
	}

	public function getSpecificDateAndChannel($channelRange) {

		$rangeAndChannels = explode('/', $channelRange);
		$invoiceNo        = str_replace('|','',str_replace('/','',str_replace('-', '', $channelRange)));

		$channel   = $this->eqDb->escape($rangeAndChannels[0]);
		$dateRange = $this->eqDb->escape($rangeAndChannels[1]);
		$today     = date('m/d/Y');

		if(!$this->validateDateRangeIsCorrect($dateRange)) {
            header('location: ' . _SITEROOT_ . 'forecast');
            exit;
		}

		$this->eqDb->where('id', $channel);
		$client   = $this->eqDb->get('channels', null, '*');

		if(!count($client)) {
            header('location: ' . _SITEROOT_ . 'forecast');
            exit;
		}

		$this->eqDb->where('f.company_id', $channel);
		$this->eqDb->where('f.date_range', $dateRange);
		$response = $this->eqDb->get('forecast f', null, '*');

		if(!count($response)) {
            header('location: ' . _SITEROOT_ . 'foredast');
            exit;
		}

		$client[0]['address'] = $this->prettifyAddress($client[0]['address']);

		$array = [];
		foreach($response as $key => $val) {

			$recipeSub = $this->eqDb->subQuery("s");
			$recipeSub->get("recipes_sub", null, 'id,price');

			$this->eqDb->join($recipeSub, "s.id = r.sub_recipe_id", "LEFT");

			$this->eqDb->where('forecast_id', $val['id']);
			$recipes = $this->eqDb->get('forecast_recipes r', null, '*');
			
			if(count($recipes) > 0) {			
				array_push($array, [
						'date'    => $val['date'],
						'start'   => $val['start_time'],
						'end'     => $val['end_time'],
						'recipes' => $recipes
					]
				);
			}
		}

		$recipesOnlyArray = [];
		foreach($array as $key => $val) {
			foreach($val['recipes'] as $key1 => $val1) {
				$val1['date'] = $val['date'];
				array_push($recipesOnlyArray, $val1);
			}
		}

		$group = [];
		foreach ( $recipesOnlyArray as $key => $value ) {
		    $group[$value['date']][$value['recipe_name']][$value['sub_recipe_name']][] = $value;
		}

		$total = 0;
		foreach($group as $key => $byDate) {
			foreach($byDate as $key2 => $val2) {
				foreach($val2 as $key3 => $val3) {
					$total = $total + ($val3[0]['price'] * $val3[0]['total']);
				}
			}
		}

		return [
			'today'      => $today,
			'invoiceNum' => $invoiceNo,
			'client'     => $client[0],
			'data'       => $group,
			'total'      => $total
		];
	}

	public function loadTransactions2($channelRange) {

		$rangeAndChannels = explode('/', $channelRange);
		$invoiceNo        = str_replace('|','',str_replace('/','',str_replace('-', '', $channelRange)));

		$client    = $this->eqDb->escape($rangeAndChannels[0]);
		$dateRange = $this->eqDb->escape($rangeAndChannels[1]);

		$this->eqDb->where('date_range', $dateRange);
		$this->eqDb->where('client_id', $client);
		
		$transactions = $this->eqDb->get('sales', null, '*');

		$invDetails = $this->getSpecificDateAndChannel($channelRange);

		$total = 0;
		foreach($transactions as $key => $val) {
			$total = $total + floatval($val['amount']);
		}

		$difference = $invDetails['total'] - $total;

		return [
			'transactions' => $transactions,
			'totalPaid'    => floatval($total),
			'difference'   => floatval($difference)
		];
	}

	public function prettifyAddress($address) {

		$address = explode(',',$address);

		$address[count($address) - 2] = $address[count($address) - 2] . ' ' . $address[count($address) - 1];

		unset($address[count($address) - 1]);

		return implode('<br/>', $address);
	}

	public function insertUpdateForecastRecipes(int $id, array $recipes, $dateRange) {

		$this->eqDb->rawQuery('DELETE FROM forecast_recipes WHERE forecast_id = "' . $id . '"');

		foreach($recipes as $recipeId => $recipe) {

			foreach($recipe['variations_picked'] as $key => $variation) {

				$subRecipeId    = $key;
				$subName        = $variation['sub_name'];
				$subPrice       = $variation['sub_price'];
				$amount         = $variation['amount'];
				$recipeName     = $recipe['recipe_name'];

				$subs = $this->eqDb->subQuery ('i');
				$subs->where('company_id', $_SESSION['scouty_company_id']);
				$subs->get('ingredients', null, 'id, name');

				$this->eqDb->join($subs, 'sr.ingredient_id = i.id', 'LEFT');

				$this->eqDb->where('sr.recipe_sub_id', $subRecipeId);
				$instantaneousIngredients = $this->eqDb->get('recipes_sub_ingredients sr',null,[
					'i.id',
					'sr.recipe_sub_id',
					'i.id as ingredient_id',
					'ingredient_weight',
					'i.name AS name', 
					'(ingredient_weight * ' . $variation['amount'] . ') AS total'
				]);

				$instIngWeights = addslashes(json_encode($instantaneousIngredients));

				$this->eqDb->insert('forecast_recipes', [
					'forecast_id'                         => $id,
					'sub_recipe_name'                     => $subName,
					'recipe_name'                         => $recipeName,
					'sub_recipe_id'                       => $subRecipeId,
					'recipe_id'                           => $recipeId,
					'total'                               => $amount,
					'total_actual'                        => 0,
					'date_range'                          => $dateRange,
					'company_id'                          => $_SESSION['scouty_company_id'],
					'instantaneous_subrecipe_price'       => $subPrice,
					'instantaneous_subrecipe_ing_weights' => $instIngWeights
				]);
			}
		}
	}

	public function getForecastListView($dateRange = null) {

		$dateRange = $this->eqDb->escape($dateRange);

        $mondayThisWeek    = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek         = date('Y-m-d', strtotime($mondayThisWeek . ' + 6 days'));
        $dateRangeThisWeek = $mondayThisWeek . '|' . $endOfWeek;

		if($dateRange == null) {
			$dateRange = $dateRangeThisWeek;
		} else {
			
			if(!$this->validateDateRangeIsCorrect($dateRange)) {
	            header('location: ' . _SITEROOT_);
	            exit;
			}
		}

		$dateRangeArray = explode('|', $dateRange);

		$previousWeekMon   = date('Y-m-d', strtotime($dateRangeArray[0] . ' - 7 days'));
		$endOfPreviousWeek = date('Y-m-d', strtotime($previousWeekMon . ' + 6 days'));
		$previousRange     = $previousWeekMon . '|' . $endOfPreviousWeek;

		$nextWeekMon   = date('Y-m-d', strtotime($dateRangeArray[0] . ' + 7 days'));
		$endOfNextWeek = date('Y-m-d', strtotime($nextWeekMon . ' + 6 days'));
		$nextRange     = $nextWeekMon . '|' . $endOfNextWeek;

		return [
			'previous'  => $previousRange,
			'next'      => $nextRange,
			'today'     => $dateRangeThisWeek,
			'rangeUgly' => $dateRange,
			'range'     => $this->prettifyDateRange($dateRange),
			'forecasts' => $this->getForecastsByRange($dateRange)
		];
	}

	public function validateDateRangeIsCorrect(string $dateRange) {

		$dateRangeArray = explode('|', $dateRange);

		if(count($dateRangeArray) !== 2) {
            return false;
		}

		if(!$this->validateDateFormat($dateRangeArray[0]) || 
			!$this->validateDateFormat($dateRangeArray[1])) {
            return false;
		}

		if(!$this->validateDagteRangeDayOfWeek($dateRange)) {
			return false;
		}

		return true;
	}	

	/*
	 * Validates the array of dates are starting from a monday
	 * and ending exactly on THE NEXT SUNDAY
	 */

	public function validateDagteRangeDayOfWeek(string $dateRange) {

		$dateRangeArray = explode('|', $dateRange);

		$firstDatesDay = date('l',strtotime($dateRangeArray[0]));

		// make sure the first date is a Monday
		if($firstDatesDay !== 'Monday') {
			return false;
		}

		// now taht we know the first date is a Monday
		// make sure the difference of the two are +6 as we are
		// including the first day in the count as well
		$date1 = date_create($dateRangeArray[0]);
		$date2 = date_create($dateRangeArray[1]);
		$diff = date_diff($date1,$date2);
		$dateDifference = $diff->format("%R%a");

		// validate the date difference is +6
		if($dateDifference !== '+6') {
			return false;
		}

		return true;
	}

	/*
	 * Validates that abs(number) date is in YYYY-MM-DD format
	 */

	public function validateDateFormat($dateRange) {

		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $dateRange)) {
		    return true;
		}

		return false;
	}

	public function fetchForecast($dateRange = false) {

        $page       = ($_POST['start']/$_POST['length']) + 1;
        $eventType  = !empty($_POST['event_type']) ? $this->eqDb->escape($_POST['event_type']) : 0;
        $eventState = !empty($_POST['state']) ? $this->eqDb->escape($_POST['state']) : 'upcoming';
        $listAll    = !empty($_POST['listAll']) ? $this->eqDb->escape($_POST['listAll']) : '';
        $rangeView  = !empty($_POST['rangeView']) ? $this->eqDb->escape($_POST['rangeView']) : '';

        $colsordering = [
        	"eventId",
        	"dateTime",
        	"clientName",
        	"event_type",
        	"event_progress",
        	"total_orders", 
        	"total"
        ];

		$currentDate = date('Y-m-d');
		$yesterday   = date('Y-m-d', strtotime('yesterday'));
		$currentTime = date('H:i:s');

        $orderBy  = $colsordering[$_POST['order'][0]['column']];
        $orderDir = $_POST['order'][0]['dir'];

        $searchStr = '';
		if(!empty($_POST['search']['value'])) {
			$search = $_POST['search']['value'];
			$searchStr = ' AND (c.name LIKE "%' . $search . '%" OR CONCAT(\'EV-\',LPAD(f.`id`, 7, 0)) LIKE "%' . $search . '%" OR f.event_type LIKE "%' . $search . '%") ';
		}

		$dateRangeStr = '';
		$limitString  = '';
		if($eventState && $listAll) {
			switch ($eventState) {
				case 'upcoming':
					$dateRangeStr = 'date > "' . $yesterday . '" AND ';
					break;
				
				case 'all':
					$dateRangeStr = '';
					break;
				
				case 'past':
					$dateRangeStr = 'date <= "' . $yesterday . '" AND ';
					break;
			}
			$limitString = ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		if($rangeView) {
			$dateRangeStr = 'date_range = "' . $dateRange . '" AND ';
		}

		$eventTypeStr = '';
		if($eventType) {
			$eventTypeStr = 'event_type = "' . $eventType . '" AND ';
		}

		$query = "SELECT SQL_CALC_FOUND_ROWS
            	f.`id`,
            	CONCAT('<span class=\"label ',IF(event_type = 'public','bg-warning text-black','bg-info text-white'),' p-b-5\">',CONCAT(UCASE(LEFT(f.`event_type`, 1)),SUBSTRING(f.`event_type`, 2)),'</span>') AS eventType,
            	event_type,
            	CONCAT('<a href=\"" . _SITEROOT_ . "forecast/event/',LPAD(f.`id`, 7, 0),'\" class=\"btn btn-tag no-margin\">','#EV-',LPAD(f.`id`, 7, 0),'</a>') AS eventIdPretty,
            	CONCAT('EV-',LPAD(f.`id`, 7, 0)) AS eventId,
            	CONCAT('<p class=\"no-margin\"udpateForecastSubRecipe>',DATE_FORMAT(f.`date`,'%b %D,%Y'),' ',CONCAT(f.`start_time`),'</p>') AS datePretty,
                fr.`forecast_id`, 
                IF(fr.total_orders IS NOT NULL,fr.`total_orders`,0) AS total_orders,
                IF(fr.`total` IS NULL,0,fr.total) AS total,
                CONCAT('<strong class=\"text-black-50 font-montserrat fs-15\">$',IF(fr.`total` IS NULL,'0.00',fr.`total`),'</strong><br/>'
                ) AS totalPrice, 
                IF(event_type = \"public\",
                		CONCAT('<span class=\"tip\" data-toggle=\"tooltip\" data-original-title=\"',ROUND(fr.winRate,1),'% of your forecasted sales have been made!','\"><small class=\"hint-text pull-right m-l-5\">(',ROUND(fr.winRate,1),'%)</small>','<small class=\"pull-right block text-primary\">$',fr.total_actual,'</small></span>'),
                		''
                ) AS winRatePretty,
                fr.total_actual,
                f.`start_time`, 
                f.`date`,
                CONCAT(f.`start_time`,'-',f.`end_time`) AS time, 
                CONCAT(f.`date`,CONCAT(f.`start_time`,'-',f.`end_time`)) AS dateTime,
                c.name AS clientName,
                CONCAT('<a target=\"_blank\" href=\"" . _SITEROOT_ . "clients/edit/',f.`channel_id`,'\">',c.`name`,'</a>') AS client,
                IF(
                	event_type = 'private',
                	IF(
						date < \"" . $currentDate . "\",
						CASE
		                    WHEN paid IS NULL THEN '<span class=\"label text-uppercase label-important p-b-5\">Unpaid</span>'
		                    WHEN paid = 0 THEN '<span class=\"label text-uppercase label-important p-b-5\">Unpaid</span>'
		                    WHEN paid = 1 THEN '<span class=\"label text-uppercase label-inverse p-b-5\">Paid</span>'
		                    WHEN paid = 2 THEN '<span class=\"label text-uppercase label-warning text-black p-b-5\">Canceled</span>'
		                END,
		            	'<span class=\"label label-success p-b-5 tip\" data-toggle=\"tooltip\" data-original-title=\"Event not started, payment not required!\">ENS</span>'
		            ),
		            '<span class=\"label label-primary p-b-5 tip\" data-toggle=\"tooltip\" data-original-title=\"Public events do not require an invoice!\">NA</span>'
		        ) AS statusPretty,
                IF(
					date < \"" . $currentDate . "\", 'past</span>', 
					IF(
						date = \"" . $currentDate . "\",
						IF(
							\"" . $currentTime . "\" >= start_time,
							IF(
								\"" . $currentTime . "\" <= end_time,
								'upcoming',
								'past'
							),
							'upcoming'
						),
						'upcoming'
					)
				) AS event_progress_dirty,
                IF(
					date < \"" . $currentDate . "\", '<span class=\"label label-primary p-b-5\">Event Passed!</span>', 
					IF(
						date = \"" . $currentDate . "\",
						IF(
							\"" . $currentTime . "\" >= start_time,
							IF(
								\"" . $currentTime . "\" <= end_time,
								'<span class=\"label label-warning text-black p-b-5\">In Progress</span>',
								'<span class=\"label label-primary p-b-5\">Event Passed!</span>'
							),
							'<span class=\"label label-inverse p-b-5\">Upcoming Event</span>'
						),
						'<span class=\"label label-inverse p-b-5\">Upcoming Event</span>'
					)
				) AS event_progress
            FROM forecast AS f
            LEFT JOIN (
               select forecast_id, 
               SUM(total * instantaneous_subrecipe_price) AS total,
               SUM(IF(total_actual IS NULL,0,total_actual) * instantaneous_subrecipe_price) AS total_actual,
               SUM(total) AS total_orders,
               SUM(IF(total_actual IS NULL,0,total_actual) * instantaneous_subrecipe_price)/SUM(total * instantaneous_subrecipe_price) AS winRate,
               COUNT(*) AS total_recipes
               FROM forecast_recipes 
               WHERE total > 0
               group by forecast_id) AS fr ON f.`id` = fr.`forecast_id`
            LEFT JOIN (
                SELECT id, name
                FROM channels
            ) AS c ON c.`id` = f.`channel_id`
            WHERE 
                (
                	fr.`total` > 0 AND
                    deleted = 0 AND 
                    " . $dateRangeStr . "
                    " . $eventTypeStr . "
                    company_id = '" . $_SESSION['scouty_company_id'] . "'
                    " . $searchStr . "
                )
            ORDER BY " . $orderBy . " " . $orderDir . $limitString;

        $array = $this->eqDb->withTotalCount()->rawQuery($query);

		$response = [
			'totalPages'           => 1,
			'iTotalDisplayRecords' => $this->eqDb->totalCount,
			'iTotalRecords'        => $this->eqDb->totalCount,
			'data'                 => $array
		];

		return $response;
	}

	public function udpateForecastSubRecipe() {

		$forRecId = $this->eqDb->escape($_POST['forRecId']);
		$amt      = $this->eqDb->escape($_POST['amt']);
		$subId    = $this->eqDb->escape($_POST['subRecipeId']);
		$actual   = $_POST['actual'] == true ? 1 : 0;


		$subs = $this->eqDb->subQuery ('i');
		$subs->where('company_id', $_SESSION['scouty_company_id']);
		$subs->get('ingredients', null, 'id, name');

		$this->eqDb->join($subs, 'sr.ingredient_id = i.id', 'LEFT');

		$this->eqDb->where('sr.recipe_sub_id', $subId);
		$instantaneousIngredients = $this->eqDb->get('recipes_sub_ingredients sr',null,[
			'i.id',
			'sr.recipe_sub_id', 
			'i.id as ingredient_id', 
			'ingredient_weight',
			'i.name AS name', 
			'(ingredient_weight * ' .$amt . ') AS total'
		]);

		if($actual == true) {
			$data = [
				'total_actual' => $amt,
				'instantaneous_subrecipe_ing_weights_actual' => json_encode($instantaneousIngredients)
			];
		} else {
			$data = [
				'total' => $amt,
				'instantaneous_subrecipe_ing_weights' => json_encode($instantaneousIngredients)
			];
		}

		// update the instantaneous weights as you are changint he amount you have picked

        $this->eqDb->where('id', $forRecId);
        $this->eqDb->where('company_id', $_SESSION['scouty_company_id']);
		if($this->eqDb->update('forecast_recipes', $data)) {
			return [
				'r' 	  => 'success',
				'message' => '<strong>Success!</strong> Amount Updated!'
			];
		}

		return [
			'r' => 'error',
			'message' => '<strong>Error!</strong> Internal Error (21233)'
		];

	}

	public function addRecipeToEvent(string $eventId) {

		$eventId = ltrim($eventId, 0);

		$recipeId = $this->eqDb->escape($_POST['recipe_id']);
		$subId    = $this->eqDb->escape($_POST['sub_recipe_id']);

		//first check if this type of 
		$this->eqDb->where('company_id', $_SESSION['scouty_company_id']);
		$this->eqDb->where('forecast_id', $eventId);
		$this->eqDb->where('sub_recipe_id', $subId);
		$this->eqDb->where('recipe_id', $recipeId);
		$check = $this->eqDb->get('forecast_recipes', null, '*');

		if($check) {
			return [
				'r' => 'error',
				'message' => '<strong>Error!</strong> Sorry this sub recipe already exists for this event!'
			];
		} else {

			$this->eqDb->where('company_id', $_SESSION['scouty_company_id']);
			$this->eqDb->where('id', $eventId);
			$eventDetails = $this->eqDb->getOne('forecast',null,'*');

		    $subs = $this->eqDb->subQuery ('r');
			$subs->get('recipes', null, 'id, name');
			$this->eqDb->join ($subs, 'sr.recipe_id = r.id', 'LEFT');

			$this->eqDb->where('sr.id', $subId);
			$this->eqDb->where('sr.company_id', $_SESSION['scouty_company_id']);
			$subDetails = $this->eqDb->get('recipes_sub sr',null,[
				'CONCAT(container_size," oz ",container) AS sub_name',
				'CONCAT(container_size," oz ",container," ",r.name) AS sub_name_return', 
				'recipe_id',
				'price',
				'r.name AS recipe_name'
			]);

			$subs = $this->eqDb->subQuery ('i');
			$subs->where('company_id', $_SESSION['scouty_company_id']);
			$subs->get('ingredients', null, 'id, name');

			$this->eqDb->join($subs, 'sr.ingredient_id = i.id', 'LEFT');

			$this->eqDb->where('sr.recipe_sub_id', $subId);
			$instantaneousIngredients = $this->eqDb->get('recipes_sub_ingredients sr',null,'i.id,  sr.recipe_sub_id, i.id as ingredient_id, ingredient_weight,i.name AS name, (ingredient_weight * 1) AS total');

			$forecastRecipe = $this->eqDb->insert('forecast_recipes', [
				'forecast_id'                         => $eventId,
				'sub_recipe_name'                     => $subDetails[0]['sub_name'],
				'recipe_name'                         => $subDetails[0]['recipe_name'],
				'sub_recipe_id'                       => $subId,
				'recipe_id'                           => $subDetails[0]['recipe_id'],
				'total'                               => 1,
				'total_actual'                        => 0,
				'date_range'                          => $eventDetails['date_range'],
				'company_id'                          => $_SESSION['scouty_company_id'],
				'instantaneous_subrecipe_price'       => $subDetails[0]['price'],
				'instantaneous_subrecipe_ing_weights' => addslashes(json_encode($instantaneousIngredients))
			]);

			return [
				'r'    		 => 'success',
				'event_type' => $eventDetails['event_type'],
				'data' 		 => [
					'sub_recipe_id'                 => $subId,
					'instantaneous_subrecipe_price' => $subDetails[0]['price'],
					'forecast_recipe_id'            => $forecastRecipe,
					'sub_recipe_name'               => $subDetails[0]['sub_name_return'],
					'net_total' 	                => $subDetails[0]['price']
				]
			];
		}
	}

	/*
	 Deletes a sub recipe or recipe from each event
	 */
	public function deleteForecastSubRecipe() {

		$forRecId = $this->eqDb->escape($_POST['forRecId']);

        $this->eqDb->where('id', $forRecId);
        $this->eqDb->where('company_id', $_SESSION['scouty_company_id']);
		if($this->eqDb->delete('forecast_recipes')) {
			return [
				'r' => 'success'
			];
		}
	}


	/*
	 Ajax call for calendar to load weekly forecasts
	 */
	public function loadForecasts() {

		$start   = $this->eqDb->escape($_POST['start']);
		$end     = $this->eqDb->escape($_POST['end']);
		$channel = $this->eqDb->escape($_POST['channel']);

        if($channel != '' || $channel != null) {
        	$this->eqDb->where('id', $channel);
        }
        $this->eqDb->where('company_id', $_SESSION['scouty_company_id']);
		$response = $this->eqDb->get('channels', null, [
			'id', 
			'name', 
			'days',
			'main_contact',
			'phone',
			'days_plan',
			'channel_color',
			'channel_text'
		]);

		$mondayThisWeek = date('Y-m-d', strtotime('monday this week'));

		$begin    = new DateTime($start);
	    $end      = new DateTime($end);
	    // Because DatePeriod does not include the last date specified add one day.
	    $end      = $end->modify('+1 day');
	    $interval = new DateInterval('P1D');

	    $datesOfSelectedWeek = iterator_to_array(new DatePeriod($begin, $interval, $end));
	    
	    $weekDates = [];
		foreach($response as $key => $resp) {

			$dates = $response[$key]['days'] = !empty($resp['days']) ? json_decode($resp['days'], 1) : [];
			$today = date('Y-m-d');
			$times = !empty($resp['days_plan']) ? unserialize($resp['days_plan']) : [];

		    foreach($datesOfSelectedWeek as $date) {
		    	
		    	$dateOfEvent    = $date->format('Y-m-d');
		    	$eventBgColor   = empty($resp['channel_color']) ? self::DEFAULT_COLOR_BG : $resp['channel_color'];
	    		$eventTextColor = empty($resp['channel_text']) ? self::DEFAULT_COLOR_TEXT : $resp['channel_text'];

	    	  	$editable = $dateOfEvent >= $mondayThisWeek ? true : false;

		    	if(in_array($date->format('D'), $dates)) {

		    		// var_dump($times[$date->format('D')]['event_type']);

		    		$startTime = $times[$date->format('D')]['start'];
		    		$endTime   = $times[$date->format('D')]['end'];
		    		$eventType = !array_key_exists('event_type', $times[$date->format('D')]) ? 'private' : $times[$date->format('D')]['event_type'];

					$id = rand().rand();
					$id = (int)$id;

		    		$array = [
		    			'id'              => rand().rand(),
		    			'day'             => $date->format('d'),
		    			'channel'         => $resp['id'],
		    			'backgroundColor' => self::DEFAULT_COLOR_PAST_BG,
		    			'event_type' 	  => $eventType,
		    			'borderColor'     => self::DEFAULT_COLOR_PAST_BG,
		    			'textColor'       => self::DEFAULT_COLOR_TEXT,
		    			'title'           => $resp['name'],
	                    'start'           => $dateOfEvent . 'T' . $startTime,
	                    'end'             => $dateOfEvent . 'T' . $endTime,
	                    'editable'   	  => $editable,
	                    'unique_id'  	  => null,
						'from_repeat'	  => 1,
	                    'other' 		  => [
	                    	'contact' => $resp['main_contact'],
	                    	'phone'   => $resp['phone'],
	                        //You can have your custom list of attributes here
	                        'notes'   => '',
	                        'recipes' => []
	          			]
		    		];

		    		// THESE ARE FOR EVENTS THAT ARE REPEATS AND ARE UPDATED
			        $this->eqDb->where("channel_id", $resp['id']);
			        $this->eqDb->where("date", $dateOfEvent);
			        $this->eqDb->where("updated_from_repeat", 1);

					$editedForecast = $this->eqDb->get("forecast", null, '*');

					if(count($editedForecast) > 0) {

						$this->eqDb->where("forecast_id", $editedForecast[0]['id']);
						$forecastRecipes = $this->eqDb->get("forecast_recipes", null, '*');

						$array['id']               = rand().rand();
		    			$array['backgroundColor']  = count($forecastRecipes) ? $eventBgColor : self::DEFAULT_COLOR_PAST_BG;
						$array['borderColor']      = count($forecastRecipes) ? $eventBgColor : self::DEFAULT_COLOR_PAST_BG;
						$array['textColor']		   = count($forecastRecipes) ? $eventTextColor : self::DEFAULT_COLOR_TEXT;
						$array['from_repeat']      = $editedForecast[0]['updated_from_repeat'];
						$array['event_type']       = $editedForecast[0]['event_type'];
						$array['unique_id']        = $editedForecast[0]['id'];
						$array['start']            = $editedForecast[0]['date'] . 'T'  . $editedForecast[0]['start_time'];
						$array['end']              = $editedForecast[0]['date'] . 'T'  . $editedForecast[0]['end_time'];
						$array['other']['recipes'] = $forecastRecipes;
						$array['other']['notes']   = $editedForecast[0]['notes'];

						if($editedForecast[0]['deleted'] == 1) {
							$array = null;
						}
					}
					if($array != null) {
		    			array_push($weekDates, $array);
					}
		    	}

		    	// THESE ARE FOR EVENTS ADDED THROUGH THE CALENDAR KEY HERE IS 'updated_from_repeat = 0'
		    	$this->eqDb->where("channel_id", $resp['id']);
		        $this->eqDb->where("date", $dateOfEvent);
		        $this->eqDb->where("updated_from_repeat", 0);

				$addedForecast = $this->eqDb->get("forecast f", null, '*');

				foreach($addedForecast as $keyAdd => $added) {

					$this->eqDb->where("forecast_id", $added['id']);
					$forecastRecipes = $this->eqDb->get("forecast_recipes", null, '*');

					$id = rand().rand();
					$id = (int)$id;

					$array = [
		    			'id'                  => rand().rand(),
		    			'day'                 => $date->format('d'),
						'channel'             => $resp['id'],
						'backgroundColor'     => count($forecastRecipes) ? $eventBgColor : self::DEFAULT_COLOR_PAST_BG,
		    			'borderColor'     	  => count($forecastRecipes) ? $eventBgColor : self::DEFAULT_COLOR_PAST_BG,
		    			'textColor'       	  => count($forecastRecipes) ? $eventTextColor : self::DEFAULT_COLOR_TEXT,
		    			'event_type'          => $added['event_type'],
		    			'title'               => $resp['name'],
		    			//you can just also manually set below to 0 because it should always be 0
		    			'from_repeat'         => $added['updated_from_repeat'], 
		    			'unique_id'           => $added['id'],
	                    'start'               => $dateOfEvent . 'T' . $added['start_time'],
	                    'end'                 => $dateOfEvent . 'T' . $added['end_time'],
	                    'editable'            => $editable,
		    			'updated_from_repeat' => 0,
	                    'other'               => [
	                    	'contact' => $resp['main_contact'],
	                    	'phone'   => $resp['phone'],
	                        //You can have your custom list of attributes here
	                        'notes'   => $added['notes'],
	                        'recipes' => $forecastRecipes
	                    ]
		    		];

		    		if($added['deleted'] != 1) {
		    			array_push($weekDates, $array);
					}
				}
		    }
		}

		return $weekDates;
	}

	public function loadUpGCEvents() {
		// // All events coming from Google Calendar
		$start   = $this->eqDb->escape($_POST['start']);
		$end     = $this->eqDb->escape($_POST['end']);
		$events = [];
		if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
			$client = new Google_Client();
			$client->addScope(Google_Service_Calendar::CALENDAR);
			$guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
			$client->setHttpClient($guzzleClient);
			$this->client = $client;
			$this->client->setAccessToken($_SESSION['access_token']);
			$service = new Google_Service_Calendar($this->client);

			$calendarId = 'primary';
			
			$optParams['timeMin'] = date("c", strtotime(date($start . ' H:i:s')));
			$optParams['timeMax'] = date("c", strtotime(date($end . ' H:i:s')));
			$results = $service->events->listEvents($calendarId, $optParams);
			
			foreach ($results->getItems() as $event) {
				
				$gATitle = $event->getSummary();
				$dateOfEvent = date("Y-m-d", strtotime($event->getStart()->dateTime));
				$gaStart = date("H:i", strtotime($event->getStart()->dateTime));
				$gaEnd   = date("H:i", strtotime($event->getEnd()->dateTime));

				array_push($events, [
					'id'                  => rand().rand(),
					'day'                 => date("d",strtotime($event->getStart()->dateTime)),
					'event'               => $event,
					'backgroundColor'     => "orange",
					'borderColor'     	  => self::DEFAULT_COLOR_PAST_BG,
					'textColor'       	  => self::DEFAULT_COLOR_TEXT,
					'title'               => "Google Calendar: " . $event->getSummary(),
					'unique_id'           => rand().rand(),
					'start'               => $dateOfEvent . 'T' . $gaStart,
					'end'                 => $dateOfEvent . 'T' . $gaEnd,
					'updated_from_repeat' => 0,
					'other'               => []
				]);
			}

		}
		// $gCal->calByDate($dateOfEvent);

		return $events;
	}

	public function loadSubRecipes() {

	    $subs = $this->eqDb->subQuery ('r');
		$subs->get('recipes', null, 'id, name');

		$this->eqDb->orderBy ('sr.id','asc');
		$this->eqDb->orderBy ('container','asc');
		$this->eqDb->join ($subs, 'sr.recipe_id = r.id', 'LEFT');

		$this->eqDb->where ('company_id', $_SESSION['scouty_company_id']);

		$subRecipes = $this->eqDb->get ('recipes_sub sr', null, [
			'sr.id', 
			'sr.recipe_id',
			'sr.price',
			'CONCAT(sr.container_size," oz") AS containerSize', 
			'sr.container AS container', 
			'r.name'
		]);

		$arr = [];

		foreach($subRecipes as $sub) {

			array_push($arr, [
				'id'            => $sub['id'],
				'recipe_id'     => $sub['recipe_id'],
				'price'         => number_format($sub['price'],2),
				'name'          => $sub['name'],
				'container'     => $sub['container'],
				'containerSize' => $sub['containerSize']
			]);
		}

		$group = [];

		foreach ( $arr as $value ) {
		    $group[$value['name']][] = $value;
		}

		return $group;
	}

	public function loadSubRecipesIngredients() {

		$subRecipeQ = $this->eqDb->subQuery ("s");
		$subRecipeQ->get('recipes_sub', null, 'id, container, container_size');

		$ingQ = $this->eqDb->subQuery ("i");
		$ingQ->get('ingredients', null, 'id, name');
		
		$this->eqDb->join($ingQ, 'r.ingredient_id = i.id', 'LEFT');
		$this->eqDb->join($subRecipeQ, 'r.recipe_sub_id = s.id', 'LEFT');

		$this->eqDb->orderBy('r.id','asc');
		$sub_recipe_ing_weights = $this->eqDb->get('recipes_sub_ingredients r', null, [
			'r.id', 
			'r.ingredient_weight', 
			'r.recipe_sub_id', 
			'r.ingredient_id', 
			'i.name', 
			's.container', 
			's.container_size'
		]);

		$group = [];

		foreach ( $sub_recipe_ing_weights as $value ) {
		    $group[$value['recipe_sub_id']][] = $value;
		}

		return $group;
	}

	public function rangeWeek () {

		$today = date('Y-m-d');
		date_default_timezone_set (date_default_timezone_get());
		$dt = strtotime ($today);
		return [
		 "start" => date ('N', $dt) == 1 ? date ('Y-m-d', $dt) : 
		 	date ('Y-m-d', strtotime ('last monday', $dt)),
		 "end" => date('N', $dt) == 7 ? date ('Y-m-d', $dt) : 
		 	date ('Y-m-d', strtotime ('next sunday', $dt))
		];
	}

	public function getEventDetails(string $eventId) {

		$eventId = ltrim($eventId, 0);

		$currentDate = date('Y-m-d');
		$currentTime = date('H:i:s');

		$channelJoin = $this->eqDb->subQuery ("c");
		$channelJoin->get('channels', null, 'id, name, address');
		$this->eqDb->join($channelJoin, 'f.channel_id = c.id', 'LEFT');

		$this->eqDb->where('f.company_id', $_SESSION['scouty_company_id']);
		$this->eqDb->where('f.deleted', 0);
		$this->eqDb->where('f.id', $eventId);
		$event = $this->eqDb->get('forecast f',null, [
			'f.id as eventId',
			'c.name as client',
			'c.address as clientAddress',
			'IF(
				date < "' . $currentDate . '", "past", 
				IF(
					date = "' . $currentDate . '",
					IF(
						"' . $currentTime . '" >= start_time,
						IF(
							"' . $currentTime . '" <= end_time,
							"in progress",
							"past"
						),
						"future"
					),
					"future"
				)
			) AS event_status',
			'event_type',
            'CASE
                WHEN paid IS NULL THEN "unresolved"
                WHEN paid = 0 THEN "unresolved"
                WHEN paid = 1 THEN "resolved"
                WHEN paid = 2 THEN "cancelled"
            END AS paid',
        	"CONCAT('<span class=\"label ',IF(event_type = 'public','label-warning text-black ','bg-info text-white '),'p-b-5\">',CONCAT(UCASE(LEFT(f.`event_type`, 1)),SUBSTRING(f.`event_type`, 2)),'</span>') AS eventType",
            "CASE
                WHEN paid IS NULL THEN '<span class=\"label text-uppercase label-danger p-b-5\"><i class=\"fa fa-exclamation\"></i>&nbsp;Unresolved</span>'
                WHEN paid = 0 THEN '<span class=\"label text-uppercase label-danger p-b-5\"><i class=\"fa fa-exclamation\"></i>&nbsp;Unresolved</span>'
                WHEN paid = 1 THEN '<span class=\"label text-uppercase label-inverse p-b-5\"><i class=\"fa fa-check\"></i>&nbsp;Resolved</span>'
                WHEN paid = 2 THEN '<span class=\"label text-uppercase label-warning text-black p-b-5\">Cancelled</span>'
            END AS paidPretty",
			'date_range',
			'date',
			'DATE_FORMAT(date,"%b %D, %Y") AS datePretty',
			'start_time',
			'end_time'
		]);

		$this->eqDb->where('forecast_id', $eventId);
		$forecastRecs = $this->eqDb->get('forecast_recipes f',null,[
			'id as forecast_recipe_id',
			'sub_recipe_name',
			'sub_recipe_id',
			'recipe_name',
			'forecast_id',
			'instantaneous_subrecipe_price',
			'total',
			'IF(total_actual IS NULL,0,total_actual) AS total_actual',
			'instantaneous_subrecipe_price * total AS net_total',
			'instantaneous_subrecipe_price * IF(total_actual IS NULL,0,total_actual) AS net_total_actual'
		]);

		$total 	     = 0;
		$totalActual = 0;
		foreach($forecastRecs as $key => $val) {
			$total 		 = $total + $val['net_total'];
			$totalActual = $totalActual + $val['net_total_actual'];
		}

		$event[0]['total']  	   = $total;
		$event[0]['totalActual']   = $totalActual;
		$event[0]['winRate']	   = number_format(($totalActual/$total) * 100,2);
		$event[0]['recipes']       = $forecastRecs;

		if(count($event) == 0) {
			header('location: ' . _SITEROOT_);
            exit;
		}

		return $event[0];
	}

	public function eventStatusPretty(string $status) {

		switch ($status) {
			case 'past':
				$string = '<span class="label label-danger p-b-5">Event Passed!</span>';
				break;
			case 'in progress':
				$string = '<span class="label label-warning p-b-5 text-black">In Progress</span>';
				break;
			case 'future':
				$string = '<span class="label label-inverse p-b-5">Upcoming Event</span>';
				break;
		}

		return $string;
	}

	public function loadChannels() {

        $this->eqDb->where('company_id', $_SESSION['scouty_company_id']);
        $this->eqDb->where('active', 1);
        $this->eqDb->orderBy('name', 'ASC');
		$response = $this->eqDb->get('channels', null, [
			"id", 
			"name", 
			"active", 
			"description", 
			"address"
		]);

		return $response;
	}

}
