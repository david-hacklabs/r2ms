<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0. If a copy of the MPL was not distributed with this file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/*
 * David Zarza Luna - HackLabs
 * Start Modyfing 2014/5/21
 */

// Include required configuration files
require_once ('config.php');
require_once ('functions.php');
// require_once ('/Applications/MAMP/bin/php/php5.6.2/lib/php/Auth/Yubico.php');
require_once ('Yubico.php');

/**
 * *****************************
 * FUNCTION: OLD GENERATE SALT *
 * *****************************
 */
function oldGenerateSalt($username) {
	$salt = '$2a$15$';
	$salt = $salt . md5 ( strtolower ( $username ) );
	return $salt;
}

/**
 * *****************************
 * FUNCTION: GENERATE SALT *
 * *****************************
 */
function generateSalt($username) {
	// Open the database connection
	$db = db_open ();
	
	// Get the users unique salt
	$stmt = $db->prepare ( "SELECT salt FROM user WHERE username = :username" );
	$stmt->bindParam ( ":username", $username, PDO::PARAM_STR, 20 );
	$stmt->execute ();
	$values = $stmt->fetchAll ();
	$salt = '$2a$15$' . md5 ( $values [0] ['salt'] );
	
	// Close the database connection
	db_close ( $db );
	
	return $salt;
}

/**
 * *************************
 * FUNCTION: GENERATE HASH *
 * *************************
 */
function generateHash($salt, $password) {
	$hash = crypt ( $password, $salt );
	return $hash;
}

/**
 * *************************
 * FUNCTION: GET USER TYPE *
 * *************************
 */
function get_user_type($user) {
	// Open the database connection
	$db = db_open ();
	/*SELECT typeuser.name FROM typeuser 
	INNER JOIN user ON user.type = typeuser.value 
	WHERE user.enabled = 1 AND user.name = :user;
	*/
	// Query the DB for a matching enabled user
	$stmt = $db->prepare ( "SELECT typeuser.value, typeuser.name FROM typeuser 
							INNER JOIN user ON user.type = typeuser.value 
							WHERE user.enabled = 1 AND user.username = :user" );
	$stmt->bindParam ( ":user", $user, PDO::PARAM_STR, 20 );
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
	
	$type = $array [0] ['value'];
	
	// If the type isn't simplerisk or ldap
	// If the type isn't a valid type user: HackLabs Staff and HackLabs Client
	if ($type != "1" && $type != "2") {
		// The user does not exist
		$type = 0;
	}
	
	// Close the database connection
	db_close ( $db );
	
	return $type;
}

/**
 * *************************
 * FUNCTION: IS VALID USER *
 * *************************
 */
function is_valid_user($user, $pass , $key = null) {
	// Default set valid_simplerisk and valid_ad to false
	$valid_simplerisk = new stdClass();
	$valid_ad = false;
	
	// Find the type of the user in the database
	$type = get_user_type ( $user );
	
	// If the user does not exist
	if ($type == 0) {
		// Return that the user is not valid
		return false;
	} 	
	else if ($type == 1 || $type == 2) {
		// Check for a valid 'HackLabs Staff' or 'HackLabs Client' user
		$valid_simplerisk = is_valid_simplerisk_user ( $user, $pass );
		
		if ($valid_simplerisk->valid && isset ($key)) {
			$valid_simplerisk = yubikeyAuthentication ( $pass, $key );
			if ( $valid_simplerisk->valid ) 
				$valid_simplerisk->valid = is_valid_yubico_user ($user, $valid_simplerisk->identity);
		}
		
	} 	// If the type is ldap
	else if ($type == "ldap") {
		// If custom authentication is enabled
		if (custom_authentication_extra ()) {
			// Include the custom authentication extra
			require_once (__DIR__ . "/../extras/authentication/index.php");
			
			// Check for a valid Active Directory user
			$valid_ad = is_valid_ad_user ( $user, $pass );
		}
	}
	
	// If either the AD or SimpleRisk user are valid
	if ($valid_ad || $valid_simplerisk->valid) {
		// Set the user permissions
		set_user_permissions ( $user );
		
		// If the encryption extra is enabled
		if (encryption_extra ()) {
			// Load the extra
			require_once (__DIR__ . "/../extras/encryption/index.php");
			
			// If the user has been activated
			if (activated_user ( $user )) {
				$encrypted_pass = get_enc_pass ( $user, $pass );
			} 			// The user has not yet been activated
			else {
				// Get the current password encrypted with the temp key
				$encrypted_pass = get_enc_pass ( $user, fetch_tmp_pass () );
				
				// Set the new encrypted password
				set_enc_pass ( $user, $pass, $encrypted_pass );
			}
			
			// Set the encrypted pass in the session
			$_SESSION ['encrypted_pass'] = $encrypted_pass;
		}
		
		return true;
	} else
		return false;
}

/**
 * ****************************************************
 * FUNCTION: yubikeyAuthentication WITH YUBIKEY 	  *
 * ****************************************************
 */

function yubikeyAuthentication ( $pass, $key ) {
	
	$valid_simplerisk = new stdClass();
	
	$passwordkey = $pass . ':' . $key;
	
	$yubi = new Auth_Yubico ( YUBICO_ID, YUBICO_KEY );
	$ret = $yubi->parsePasswordOTP($passwordkey);
	//$ret = Auth_Yubico::parsePasswordOTP($passwordkey);
	
	$passwordkey = htmlspecialchars($passwordkey);
	
	if ($ret) {
		
		$identity = htmlspecialchars($ret['prefix']);
		$key = htmlspecialchars($ret['otp']);
		
		//$yubi = new Auth_Yubico ( YUBICO_ID, YUBICO_KEY );
		$auth = $yubi->verify($key);
		
		if (@PEAR::isError($auth)) {
			$valid_simplerisk->valid = false;
								
			//print "<p>Authentication failed: " . $auth->getMessage();
			//print "<p>Debug output from server: " . $yubi->getLastResponse();
		}
		else {
			$valid_simplerisk->valid = true;
			$valid_simplerisk->identity = $identity;
			$valid_simplerisk->key = $key;
			
			//print "<p>You are authenticated!";
		}
		
	} else $valid_simplerisk->valid = false;
		
	return $valid_simplerisk;
}

/**
 * ********************************
 * FUNCTION: SET USER PERMISSIONS *
 * ********************************
 */
function set_user_permissions($user) {
	// Open the database connection
	$db = db_open ();
	
	// Query the DB for the users information
	$stmt = $db->prepare ( "SELECT value, type, company_id, name, lang, admin, review_critical, review_high, review_medium, review_low, submit_risks, modify_risks, plan_mitigations, close_risks FROM user WHERE username = :user" );
	$stmt->bindParam ( ":user", $user, PDO::PARAM_STR, 20 );
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
	
	// Set the session values
	$_SESSION ['uid'] = $array [0] ['value'];
	$_SESSION ['user'] = $user;
	$_SESSION ['type'] = $array[0] ['type'];
	$_SESSION ['company'] = $array[0] ['company_id'];
	$_SESSION ['name'] = htmlentities ( $array [0] ['name'], ENT_QUOTES, 'UTF-8' );
	$_SESSION ['admin'] = $array [0] ['admin'];
	$_SESSION ['review_critical'] = $array [0] ['review_critical'];
	$_SESSION ['review_high'] = $array [0] ['review_high'];
	$_SESSION ['review_medium'] = $array [0] ['review_medium'];
	$_SESSION ['review_low'] = $array [0] ['review_low'];
	$_SESSION ['submit_risks'] = $array [0] ['submit_risks'];
	$_SESSION ['modify_risks'] = $array [0] ['modify_risks'];
	$_SESSION ['close_risks'] = $array [0] ['close_risks'];
	$_SESSION ['plan_mitigations'] = $array [0] ['plan_mitigations'];
	$_SESSION ['user_type'] = $array [0] ['type'];
	
	// If the users language is not null
	if (! is_null ( $array [0] ['lang'] )) {
		// Set the session value
		$_SESSION ['lang'] = $array [0] ['lang'];
	} 	// Otherwise, the session should use the default language
	else
		$_SESSION ['lang'] = LANG_DEFAULT;
}

/**
 * ************************
 * FUNCTION: GRANT ACCESS *
 * ************************
 */
function grant_access() {
	$_SESSION ["access"] = "granted";
	
	// Update the last login
	if (update_last_login ( $_SESSION ['uid'] )){
		// Audit log
		$risk_id = 1000;
		$message = "Username \"" . $_SESSION ['user'] . "\" logged in successfully.";
		write_log ( $risk_id, $_SESSION ['uid'], $message );
	}
}

/**
 * ************************************
 * FUNCTION: IS VALID SIMPLERISK USER *
 * ************************************
 */
function is_valid_simplerisk_user($user, $pass) {
	// Old password hash format
	$salt = oldGenerateSalt ( $user );
	$oldProvidedPassword = generateHash ( $salt, $pass );
	
	// New password hash format
	$salt = generateSalt ( $user );
	$providedPassword = generateHash ( $salt, $pass );
	
	// Open the database connection
	$db = db_open ();
	
	// Query the DB for a matching user and hash
	$stmt = $db->prepare ( "SELECT password FROM user WHERE username = :user" );
	$stmt->bindParam ( ":user", $user, PDO::PARAM_STR, 20 );
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
	
	// Get the stored password
	$storedPassword = $array [0] ['password'];
	
	// Close the database connection
	db_close ( $db );
	
	$verify_simplerisk_user = new stdClass();
	
	// If the passwords are equal
	if (($providedPassword == $storedPassword) || ($oldProvidedPassword == $storedPassword)) {
		$verify_simplerisk_user->valid = true;
	} else $verify_simplerisk_user->valid = false;
	
	return $verify_simplerisk_user;
	
}

/**
 * ******************************
 * FUNCTION: IS SIMPLERISK USER *
 * ******************************
 */
function is_simplerisk_user($username) {
	// Open the database connection
	$db = db_open ();
	
	// Query the DB for a matching user and hash
	//$stmt = $db->prepare ( "SELECT value FROM user WHERE type = 'simplerisk' AND username = :username" );
	$stmt = $db->prepare ( "SELECT value FROM user WHERE username = :username" );
	$stmt->bindParam ( ":username", $username, PDO::PARAM_STR, 20 );
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
	
	// Close the database connection
	db_close ( $db );
	
	// If the user does not exist return 0
	if (empty ( $array )) {
		return 0;
	} 	// Otherwise, return the user id value
	else
		return $array [0] ['value'];
}

/**
 * ********************************************
 * FUNCTION: IS VALID YUBICO USER             *
 * ********************************************
 */
function is_valid_yubico_user($username, $identityyubico) {
	// Open the database connection
	$db = db_open ();

	// Query the DB for a matching user and hash
	$stmt = $db->prepare ( "SELECT value FROM user WHERE username = :username AND identityyubico = :identityyubico" );
	$stmt->bindParam ( ":username", $username, PDO::PARAM_STR, 20 );
	$stmt->bindParam ( ":identityyubico", $identityyubico, PDO::PARAM_STR, 60 );
	$stmt->execute ();

	// Store the list in the array
	$array = $stmt->fetchAll ();

	// Close the database connection
	db_close ( $db );

	// If the user does not exist return 0
	if (empty ( $array )) {
		return false;
	} 	// Otherwise, return the user id value
	else
		return true;

}

/**
 * **************************
 * FUNCTION: GENERATE TOKEN *
 * **************************
 */
function generate_token($size) {
	$token = "";
	$values = array_merge ( range ( 0, 9 ), range ( 'a', 'z' ), range ( 'A', 'Z' ) );
	
	for($i = 0; $i < $size; $i ++) {
		$token .= $values [array_rand ( $values )];
	}
	
	return $token;
}

/**
 * **************************************
 * FUNCTION: PASSWORD RESET BY USERNAME *
 * **************************************
 */
function password_reset_by_username($username) {
	$userid = is_simplerisk_user ( $username );
	
	// Check if the username exists
	if ($userid != 0) {
		password_reset_by_userid ( $userid );
		
		return true;
	} else
		return false;
}

/**
 * ************************************
 * FUNCTION: PASSWORD RESET BY USERID *
 * ************************************
 */
function password_reset_by_userid($userid) {
	
	// Generate a 20 character reset token
	$token = generate_token ( 20 );
	
	// Open the database connection
	$db = db_open ();
	
	// Get the users e-mail address
	$stmt = $db->prepare ( "SELECT username, name, email FROM user WHERE value=:userid" );
	
	$stmt->bindParam ( ":userid", $userid, PDO::PARAM_INT );
	
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
		
	$username = $array [0] ['username'];
	$name = $array [0] ['name'];
	$email = $array [0] ['email'];
	
	// Insert into the password reset table
	$stmt = $db->prepare ( "INSERT INTO password_reset (`username`, `token`) VALUES (:username, :token)" );
	
	$stmt->bindParam ( ":username", $username, PDO::PARAM_STR, 20 );
	$stmt->bindParam ( ":token", $token, PDO::PARAM_STR, 20 );
	
	$stmt->execute ();
	
	// Close the database connection
	db_close ( $db );
	
	// Send the reset e-mail
	send_reset_email ( $username, $name, $email, $token );
}

/**
 * ****************************************************
 * FUNCTION: UPDATE YUBIKEY FOR USER                  *
 * ****************************************************
 */

function updateYubikeyForUser ( $user , $identityyubico ) {

	// Open the database connection
	$db = db_open ();
	
	// Get the users e-mail address
	$stmt = $db->prepare ( "UPDATE user SET identityyubico=:identityyubico WHERE value=:userid" );
	
	$stmt->bindParam ( ":userid", $user, PDO::PARAM_INT );
	$stmt->bindParam ( ":identityyubico", $identityyubico, PDO::PARAM_STR, 60 );
		
	$stmt->execute ();
	
	// Close the database connection
	db_close ( $db );
}

/**
 * ****************************
 * FUNCTION: SEND RESET EMAIL *
 * ****************************
 */
function send_reset_email($username, $name, $email, $token) {
	$to = $email;
	$subject = "R2MS Password Reset";
	$body = $name . ",\n\nA request was submitted to reset your R2MS password.  Your username is \"" . $username . "\" and your password reset token is \"" . $token . "\".  You may now use the \"Forgot your password\" link on the R2MS log in page to reset your password.";
	mail ( $to, $subject, $body );
}

/**
 * ***********************************
 * FUNCTION: PASSWORD RESET BY TOKEN *
 * ***********************************
 */
function password_reset_by_token($username, $token, $password, $repeat_password, $key) {
	$reset_response = new stdClass();
	$userid = is_simplerisk_user ( $username );
	// Verify that the passwords match
	if ($password == $repeat_password) {
		if (is_strong_password($password)) {
			// If the username exists
			if ($userid != 0) {
				// If the reset token is valid
				if (is_valid_reset_token ( $username, $token )) {
					
					$reset_response = yubikeyAuthentication ( $password, $key );
					if ( $reset_response->valid ) {
						
						updateYubikeyForUser($userid, $reset_response->identity);
						
						// Open the database connection
						$db = db_open ();
							
						// Create the new password hash
						$salt = generateSalt ( $username );
						$hash = generateHash ( $salt, $password );
							
						// Update the password
						$stmt = $db->prepare ( "UPDATE user SET password=:hash WHERE username=:username" );
						$stmt->bindParam ( ":hash", $hash, PDO::PARAM_STR, 60 );
						$stmt->bindParam ( ":username", $username, PDO::PARAM_STR, 20 );
						$stmt->execute ();
							
						// Close the database connection
						db_close ( $db );
						
						$reset_response->message = 'The password has been successfully updated.';
						
						$reset_response->valid = true;
					
					} else { 
						$reset_response->valid = false;
						$reset_response->message = "There was a problem with your password reset request.<br/> The Yubikey is not valid.<br/>Please try again.";
					}
				} else {
					$reset_response->valid = false;
					$reset_response->message = "There was a problem with your password reset request. <br/> The token is not valid.<br/>Please try again.";
				}
				
			} else {
				$reset_response->valid = false;
				$reset_response->message = "There was a problem with your password reset request. UU.<br/> Please try again.";
		  }
		} else {
		$reset_response->valid = false;
		$reset_response->message = "There was a problem with your password reset request.<br/>Select a password with the following requirements:<br/><ul class=\"unstyled\"><li>Minimum 8 metacharacters length.</li><li>Combination of lower and uppercase characters, digits and special characters.</li></ul>Please try again.";
		}
	} else {
		$reset_response->valid = false;
		$reset_response->message = "There was a problem with your password reset request.<br/>The passwords do not match.  Please try again.";
	}
	
	return $reset_response;
		
}

/**
 * ********************************
 * FUNCTION: IS VALID RESET TOKEN *
 * ********************************
 */
function is_valid_reset_token($username, $token) {
	// Open the database connection
	$db = db_open ();
	
	// Delete tokens older than 15 minutes
	$stmt = $db->prepare ( "DELETE FROM password_reset WHERE timestamp < DATE_SUB(NOW(), INTERVAL 15 MINUTE)" );
	$stmt->execute ();
	
	// Increment the attempts for the username
	$stmt = $db->prepare ( "UPDATE password_reset SET attempts=attempts+1 WHERE username=:username" );
	$stmt->bindParam ( ":username", $username, PDO::PARAM_STR, 20 );
	$stmt->execute ();
	
	// Search for a valid token
	$stmt = $db->prepare ( "SELECT attempts FROM password_reset WHERE username=:username AND token=:token" );
	
	$stmt->bindParam ( ":username", $username, PDO::PARAM_STR, 20 );
	$stmt->bindParam ( ":token", $token, PDO::PARAM_STR, 20 );
	$stmt->execute ();
	
	// Store the list in the array
	$array = $stmt->fetchAll ();
			
	// Close the database connection
	db_close ( $db );
	
	// If there is not a match for the username and token
	if (empty ( $array )) {
		return false;
	} else {
		
		$attempts = $array [0] ['attempts'];
		
		// Remove the matching token
		$stmt = $db->prepare ( "DELETE FROM password_reset WHERE token=:token" );
		$stmt->bindParam ( ":token", $token, PDO::PARAM_STR, 20 );
		$stmt->execute ();
		
		// Matching token has been attempted <= 5 times
		if ($attempts < 5) {
			return true;
		} 		// Matching token has been attempted > 5 times
		else
			return false;
	}
}

/**
 * *************************
 * FUNCTION: SESSION CHECK *
 * *************************
 */
function session_check() {
	// Last request was more $last_activity
	if (isset ( $_SESSION ['LAST_ACTIVITY'] ) && (time () - $_SESSION ['LAST_ACTIVITY'] > LAST_ACTIVITY_TIMEOUT)) {
		// unset $_SESSION variable for the run-time
		session_unset ();
		
		// destroy session data in storage
		session_destroy ();
		
		// Return false
		return false;
	}
	// update last activity time stamp
	$_SESSION ['LAST_ACTIVITY'] = time ();
	
	// If the session created value has not been set
	if (! isset ( $_SESSION ['CREATED'] )) {
		// Set it with the current time
		$_SESSION ['CREATED'] = time ();
	} 	// Otherwise check if it was created more than $created
	else if (time () - $_SESSION ['CREATED'] > SESSION_RENEG_TIMEOUT) {
		// change session ID for the current session an invalidate old session ID
		session_regenerate_id ( true );
		
		// update creation time
		$_SESSION ['CREATED'] = time ();
	}
	
	// Return true
	return true;
}

/**
 * ************************
 * FUNCTION: SESSION OPEN *
 * ************************
 */
function sess_open($sess_path, $sess_name) {
	return true;
}

/**
 * *************************
 * FUNCTION: SESSION CLOSE *
 * *************************
 */
function sess_close() {
	return true;
}

/**
 * ************************
 * FUNCTION: SESSION READ *
 * ************************
 */
function sess_read($sess_id) {
	// Open the database connection
	$db = db_open ();
	
	// Get the session data
	$stmt = $db->prepare ( "SELECT data FROM sessions WHERE `id`=:sess_id" );
	$stmt->bindParam ( ":sess_id", $sess_id, PDO::PARAM_STR, 32 );
	$stmt->execute ();
	$array = $stmt->fetchAll ();
	
	/*
	 * // If the session does not exist if (empty($array)) { $current_time = time(); // Create the session $stmt = $db->prepare("INSERT INTO sessions (id, access) VALUES (:sess_id, :current_time)"); $stmt->bindParam(":sess_id", $sess_id, PDO::PARAM_STR, 32); $stmt->bindParam(":current_time", $current_time, PDO::PARAM_INT, 10); $stmt->execute(); $return = ""; } // The session does exist else { $stmt = $db->prepare("UPDATE sessions SET `access`=:current_time WHERE `id`=:sess_id"); $stmt->bindParam(":sess_id", $sess_id, PDO::PARAM_STR, 32); $stmt->bindParam(":current_time", $current_time, PDO::PARAM_INT, 10); $stmt->execute(); $return = $array[0]['data']; }
	 */
	
	// Close the database connection
	db_close ( $db );
	
	// If the array is empty
	if (empty ( $array )) {
		return false;
	} else
		return $array [0] ['data'];
}

/**
 * *************************
 * FUNCTION: SESSION WRITE *
 * *************************
 */
function sess_write($sess_id, $data) {
	$access = time ();
	
	// Open the database connection
	$db = db_open ();
	
	$stmt = $db->prepare ( "REPLACE INTO sessions VALUES (:sess_id, :access, :data)" );
	$stmt->bindParam ( ":sess_id", $sess_id, PDO::PARAM_STR, 32 );
	$stmt->bindParam ( ":access", $access, PDO::PARAM_INT );
	$stmt->bindParam ( ":data", $data, PDO::PARAM_STR );
	$stmt->execute ();
	
	// Close the database connection
	db_close ( $db );
	
	return true;
}

/**
 * ***************************
 * FUNCTION: SESSION DESTROY *
 * ***************************
 */
function sess_destroy($sess_id) {
	// Open the database connection
	$db = db_open ();
	
	$stmt = $db->prepare ( "DELETE FROM sessions WHERE `id`=:sess_id" );
	$stmt->bindParam ( ":sess_id", $sess_id, PDO::PARAM_STR, 32 );
	$stmt->execute ();
	
	// Close the database connection
	db_close ( $db );
	
	return true;
}

/**
 * **************************************
 * FUNCTION: SESSION GARBAGE COLLECTION *
 * **************************************
 */
function sess_gc($sess_maxlifetime) {
	$old = time () - $sess_maxlifetime;
	
	// Open the database connection
	$db = db_open ();
	
	$current_time = time ();
	$stmt = $db->prepare ( "DELETE FROM sessions WHERE `access` < :old" );
	$stmt->bindParam ( ":old", $old, PDO::PARAM_INT, 10 );
	$stmt->execute ();
	
	// Close the database connection
	db_close ( $db );
	
	return true;
}

?>
