<?php
class PaypalIpn {

	protected static $socketUrl = 'ssl://www.paypal.com';
	protected static $testSocketUrl = 'tls://www.sandbox.paypal.com';

	protected static $socketPort = 443;
	protected static $socketTimeout = 30;
	protected static $socketResource;

	protected static $logDir = "logs/ipn/";
	protected static $logFile;
	protected static $logResource;
	protected static $logFailed = false;

	protected static $eol = "\r\n";

	public static function isStatusCompleted() {
		if (self::verify()) {
			if (self::checkPostVal('payment_status', 'Completed')) {
				self::log('Payment Status Complete');
				return true;
			} else {
				self::log('Payment Status not complete, set to `' . self::getPostVal('payment_status') . '`');
			}
		}
		return false;
	}

/**
 * Verifies an PayPal IPN connection
 *
 * @return bool;
 **/
	public static function verify() {
		self::log('Received IPN');
		try {
			self::sendSocketRequest();
		} catch (Exception $e) {
			self::log('Could not open socket: ' . $e->getMessage());
			return false;
		}
		$success = null;
		while ($line = self::getSocketLine()):
			if (strcmp ($line, "VERIFIED") == 0) {
				$success = true;
				break;
			} else if (strcmp ($line, "INVALID") == 0) {
				$success = false;
				break;
				// log for manual investigation
				self::log('Invalid Socket Connection. Aborting.');
			}
		endwhile;

		if ($success) {
			self::log('IPN Connection verified');
		} else {
			self::log('IPN Connection could not be verified');
		}

		self::closeSocket();
		self::logClose();
		return $success;
	}

/**
 * Sends the socket request information
 *
 **/
	protected static function sendSocketRequest() {
		if (empty(self::$socketResource)) {
			self::openSocket();
		}
		self::log('Sending Socket Request');
		try {
			return fputs(self::$socketResource, self::getRequest());
		} catch (Exception $e) {
			throw new Exception('Error sending socket request: ' . $e->getMessage());
		}
	}

/**
 * Opens the socket connection to PayPal
 *
 * @return void;
 **/
	protected static function openSocket() {
		if (empty(self::$socketResource)) {
			self::closeSocket();
		}
		self::$socketResource = fsockopen (self::$socketUrl, self::$socketPort, $errorNumber, $errorString, self::$socketTimeout);
		if (empty(self::$socketResource)) {
			throw new Exception("Could not open PayPal Socket. Aborting. `$errorString` (#$errorNumber)");
		}
		self::log('Socket opened successfully');
	}

/**
 * Closes the socket connection to PayPal
 *
 * @return void;
 **/
	protected static function closeSocket() {
		if (!empty(self::$socketResource)) {
			fclose(self::$socketResource);
			self::log('Socket closed');
		}
	}

	public static function getData() {
		return $_POST;
	}

/**
 * Gets request variables being passed along and converts it to a query string
 *
 * @return string;
 **/
	protected static function getRequestVariables() {
		// read the post from PayPal system and add 'cmd'
		$requestVars = ['cmd' => '_notify-validate'];
		$data = self::getData();
		if (!empty($data)) {
			$requestVars += $data;
		}

		$requestVars = http_build_query($requestVars);

		self::log('Request Variables');
		self::log($requestVars);
		self::log('End Request');

		return $requestVars;
	}

/**
 * Returns the content of the request
 *
 * @return string;
 **/
	protected static function getRequest() {
		return self::getRequestHeader() . self::getRequestVariables();
	}

/**
 * Builds the socket header
 *
 * @return string;
 **/
	protected static function getRequestHeader() {
		$requestVars = self::getRequestVariables();
		$headerLines = [
			"POST /cgi-bin/webscr HTTP/1.1",
			"Content-Type: application/x-www-form-urlencoded",
			"Content-Length: " . strlen($requestVars),
			//"Host: www.paypal.com",
			//"Connection: close"
		];
		$header = implode(self::$eol, $headerLines) . self::$eol . self::$eol;

		foreach ($headerLines as $headerLine) {
			self::log('HEADER: ' . $headerLine);
		}

		return $header;

		/** Old outdated 1.0 Header
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		*/
		//$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	}

/**
 * Gets one line of the returned socket file. Returns false if it's at the end of the file
 *
 * @return string|bool
 **/
	protected static function getSocketLine() {
		if (feof(self::$socketResource)) {
			self::log("END OF REQUEST FILE");
			return false;
		}
		$line = trim(fgets(self::$socketResource, 1024));
		self::log("LINE: $line");
		return $line;
	}

/**
 * Writes one log line
 *
 * @param string $msg
 * @return bool;
 **/
	public static function log($msg) {
		if (empty(self::$logResource) && !self::$logFailed) {
			self::logOpen();
		}
		if (self::$logResource) {
			return fwrite(self::$logResource, date('c') . ' ' . str_replace(["\r","\t","\n"],' ',$msg)."\n");
		} else {
			return false;
		}
	}

	public static function getLogDir() {
		return WWW_ROOT . self::$logDir;
	}

	public static function getLogFiles() {
		$logDir = self::getLogDir();
		if (!($folder = opendir($logDir))) {
			throw new Exception('Could not open directory: ' . $logDir);
		}
		$logFiles = [];
		while(($file = readdir($folder)) !== false) {
			if ($file[0] == '.' || $file == 'empty')  {
				continue;
			}
			$logFiles[$file] = $file;
		}
		closedir($folder);
		krsort($logFiles);
		return $logFiles;
	}

	protected static function logOpen() {
		//$dir = '/home/souper/page_logs/ipn/';
		$logDir = self::getLogDir();
		if (!is_dir($logDir)) {
			mkdir($logDir, 0775, true);
		}
		self::$logFile = $logDir . date('Y-m-d').'.log';
		self::$logResource = fopen(self::$logFile, 'a');
		if (!self::$logResource) {
			self::$logFailed = true;
			return false;
		}
		self::logLine('Opening Log');
		return true;
	}
		
	protected static function logClose() {
		if (!empty(self::$logResource)) {
			self::logLine('Closing Log');
			fclose(self::$logResource);
		}
	}

	protected static function logLine($msg) {
		$wrap = ' **************** ';
		return self::log("$wrap$msg$wrap");
	}

	protected static function getPostVal($key) {
		$data = self::getData();
		return self::checkPost($key) ? $data[$key] : '';
	}

	protected static function checkPost($key) {
		$data = self::getData();
		return !empty($data) && array_key_exists($key, $data);
	}

	protected static function checkPostVal($key, $val) {
		$data = self::getData();
		return self::checkPost($key) && ($data[$key] === $val);
	}
}