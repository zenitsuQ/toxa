<?php
	class ReCaptchaResponse
	{
		public $success;
		public $errorCodes;
	}

	class ReCaptcha
	{
		private static $_signupUrl     = "https://www.google.com/recaptcha/admin";
		private static $_siteVerifyUrl = "https://www.google.com/recaptcha/api/siteverify?";
		private $_secret;
		private static $_version       = "php_1.0";

		function __construct($secret)
		{
			if ($secret == null || $secret == "") {
				die("Для использования reCAPTCHA получите API-ключ здесь: <a href='"
					. self::$_signupUrl . "'>" . self::$_signupUrl . "</a>");
			}

			$this->_secret=$secret;
		}

		private function _encodeQS($data)
		{
			$req = "";
			foreach ($data as $key => $value)
			{
				$req .= $key . '=' . URLEncode(stripslashes($value)) . '&';
			}

			// Cut the last '&'
			$req = SubStr($req, 0, StrLen($req) - 1);

			return $req;
		}

		private function _submitHTTPGet($path, $data)
		{
			$req = $this->_encodeQS($data);
			$response = file_get_contents($path . $req);

			return $response;
		}

		public function verifyResponse($remoteIp, $response)
		{
			// Discard empty solution submissions
			if ($response == null || strlen($response) == 0)
			{
				$recaptchaResponse = new ReCaptchaResponse();
				$recaptchaResponse->success = false;
				$recaptchaResponse->errorCodes = 'missing-input';

				return $recaptchaResponse;
			}

			$getResponse = $this->_submitHttpGet(
				self::$_siteVerifyUrl,
				array (
					'secret' => $this->_secret,
					'remoteip' => $remoteIp,
					'v' => self::$_version,
					'response' => $response
				)
			);

			$answers = json_decode($getResponse, true);
			$recaptchaResponse = new ReCaptchaResponse();
			
			if (Trim($answers['success']) == true)
			{
				$recaptchaResponse->success = true;
			}
			else
			{
				$recaptchaResponse->success = false;
				$recaptchaResponse->errorCodes = $answers[error-codes];
			}

			return $recaptchaResponse;
		}
	}

	$rc_response = null;
	$reCaptcha   = new ReCaptcha($rc_secret);
	
	if (isset($_POST["g-recaptcha-response"]))
	{
		$rc_response = $reCaptcha->verifyResponse(
			$_SERVER["REMOTE_ADDR"],
			$_POST["g-recaptcha-response"]);
	}