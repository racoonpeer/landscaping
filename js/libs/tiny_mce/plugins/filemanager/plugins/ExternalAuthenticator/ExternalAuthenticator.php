<?php
/**
 * DrupalAuthenticatorImpl.php
 *
 * @package MCImageManager.authenicators
 * @author Moxiecode
 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
 */

/**
 * This class is a External authenticator implementation.
 *
 * @package MCImageManager.Authenticators
 */
class Moxiecode_ExternalAuthenticator extends Moxiecode_ManagerPlugin {
    /**#@+
	 * @access public
	 */

	/**
	 * Main constructor.
	 */
	function Moxiecode_ExternalAuthenticator() {
	}

	function onAuthenticate(&$man) {
		$config =& $man->getConfig();

		session_start();

		$authURL = $config['ExternalAuthenticator.external_auth_url'];
		$secretKey = $config['ExternalAuthenticator.secret_key'];
		$prefix = isset($config['ExternalAuthenticator.session_prefix']) ? $config['ExternalAuthenticator.session_prefix'] : "mcmanager_";
		$useCookie = isset($config['ExternalAuthenticator.use_cookie']) ? $config['ExternalAuthenticator.use_cookie'] == true : true;
		$dir = basename(dirname($_SERVER["PHP_SELF"]));
                
                // Session verification from WLCMS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
                $logined  = false;
                $timeout  = $config['SessionAuthenticator.logged_in_timeout'];
                $sitezone = $config['ExternalAuthenticator.check_sitezone'] ? (empty($_SESSION['WLCMS_ZONE']) ? false : $_SESSION['WLCMS_ZONE']) : 'BACKEND'; // [ BACKEND | FRONTEND ]
                $ssprefix = ucfirst(strtolower(basename(realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR))));
                $arConfig = array(
                    'logged_in_key'         => $config['SessionAuthenticator.logged_in_key'],
                    'logged_in_timeout_key' => $config['SessionAuthenticator.logged_in_timeout_key'],
                    'logged_in_timeout'     => $config['SessionAuthenticator.logged_in_timeout'],
                    'groups_key'            => $config['SessionAuthenticator.groups_key'],
                    'user_key'              => $config['SessionAuthenticator.user_key'],
                    'config_prefix'         => $config['SessionAuthenticator.config_prefix']
                );
                $_SESSION['ar'.$ssprefix] = $arConfig;
                
                if(@$_SESSION[$arConfig['config_prefix'].$arConfig['logged_in_key']]){
                    if(($_SESSION[$arConfig['config_prefix'].$arConfig['logged_in_timeout_key']]-time())<=0 ){
                        $_SESSION[$arConfig['config_prefix'].$arConfig['logged_in_key']]=false;
                    } else {
                        $_SESSION[$arConfig['config_prefix'].$arConfig['logged_in_timeout_key']] = time()+$timeout;
                        $logined = true;
                    }
                }
                if(!$logined && $sitezone=='BACKEND' && isset($_SESSION['auser_obj']) && $_SESSION['auser_obj']->logined){
                    if((time()-$_SESSION['auser_timeout']) > $timeout){
                        $_SESSION['auser_obj']->logined = 0;
                    } else {
                        $_SESSION['auser_timeout'] = time();
                        $logined = true;
                    }
                } elseif(!$logined && $sitezone=='FRONTEND' && isset($_SESSION['suser_obj']) && $_SESSION['suser_obj']->logined){
                    if((time()-$_SESSION['suser_timeout']) > $timeout){
                        $_SESSION['suser_obj']->logined = 0;
                    } else {
                        $_SESSION['suser_timeout'] = time();
                        $logined = true;
                    }
                }
                if(!$logined) return false;
                // End Session verification from WLCMS /////////////////////////
                

		// Always allow language packs to be loaded
		if ($dir == "language") {
			// Override language key
			if (isset($_SESSION[$prefix . "ExternalAuthenticator_general__language"]))
				$config["general.language"] = $_SESSION[$prefix . "ExternalAuthenticator_general__language"];

			return true;
		}

		// Check local session if authenticated
		if ($dir == "rpc" || $dir  == "stream") {
			if (isset($_SESSION[$prefix . 'ExternalAuthenticator']) && $_SESSION[$prefix . 'ExternalAuthenticator'] == true) {
				if (!$useCookie || isset($_COOKIE[$prefix . 'enabled']) && $_COOKIE[$prefix . 'enabled'] == md5($secretKey . $_SERVER['REMOTE_ADDR'])) {
					foreach ($_SESSION as $key => $value) {
						if (strpos($key, $prefix . "ExternalAuthenticator_") === 0) {
							$key = str_replace("__", ".", $key);
							$key = substr($key, strlen($prefix . "ExternalAuthenticator_"));
							$config[$key] = $value;
						}
					}

					// Try create rootpath
					$rootPath = $man->toAbsPath($config['filesystem.rootpath']);
					$rootPathItems = explode(';', $rootPath);
					$rootPathItems = explode('=', $rootPathItems[0]);

					if (count($rootPathItems) > 1)
						$rootPath = $rootPathItems[1];
					else
						$rootPath = $rootPathItems[0];

					if (!file_exists($rootPath))
						@mkdir($rootPath);

					// Use rootpath as path
					if (!$config['filesystem.path'] || !$man->isChildPath($rootPath, $config['filesystem.path']))
						$config['filesystem.path'] = $rootPath;

					return true;
				}
			}
		}

		if (isset($_POST['key'])) {
			// Generate data chunk
			$data = "";
			$ignored = array("key");
			foreach ($_POST as $key => $value) {
				if (!in_array($key, $ignored))
					$data .= $value;
			}

			// Check input
			if ($_POST['key'] == md5($data . $secretKey)) {
				// Set authenticated session and cookie
				$_SESSION[$prefix . 'ExternalAuthenticator'] = true;

				if ($useCookie)
					setcookie($prefix . 'enabled', md5($secretKey . $_SERVER['REMOTE_ADDR']), 0, '/');

				// Set config parameters
				foreach ($_POST as $key => $value) {
					if (!in_array($key, $ignored)) {
						$_SESSION[$prefix . 'ExternalAuthenticator_' . $key] = $value;
						$config[$key] = $value;
					}
				}

				return true;
			} else {
				sleep(1); // Sleep for bots
				die("Invalid input make sure that the secret keys match.");
			}
		}

		// Force absolute
		if (strpos($authURL, "http") !== 0 && strpos($authURL, "/") !== 0)
			$authURL = "plugins/ExternalAuthenticator/" . $authURL;

		// Setup return URL
		$prot = "http";
		//$port = "";

		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")
			$prot = "https";

		// Non default port
		//if ($_SERVER['SERVER_PORT'] != "80" && $_SERVER['SERVER_PORT'] != "443")
		//	$port = ":" . $_SERVER['SERVER_PORT'];

		// If RPC or stream then return it using config
		if ($dir == "rpc" || $dir  == "stream") {
			// This part doesn't work yet but isn't really needed.

			// Make it absolute
			if (strpos($authURL, "/") === 0)
				$authURL = $prot . "://" . $_SERVER['HTTP_HOST'] . $authURL;

			$returnURL = $prot . "://" . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['PHP_SELF'])) . "/index.php?type=" . $man->getType();
			$config['authenticator.login_page'] = $authURL . "?return_url=" . urlencode($returnURL);
			return false;
		}

		// Not logged redirect to External backend
		$returnURL = $prot . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?type=" . $man->getType();
		header('location: ' . $authURL . "?return_url=" . urlencode($returnURL));
		die();
	}

	/**#@-*/
}

// Add plugin to MCManager
$man->registerPlugin("ExternalAuthenticator", new Moxiecode_ExternalAuthenticator());

?>