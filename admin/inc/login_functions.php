<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Login Functions
 *
 * @package GetSimple
 * @subpackage Login
 */

$MSG = null;
# if the login cookie is already set, redirect user to control panel
if(cookie_check()) {
	gotoDefaultPage();                                           
}

# was the form submitted?
if(isset($_POST['submitted'])) { 
	
	# initial variable setup
	$user_xml = GSUSERSPATH . _id($_POST['userid']).'.xml';
	$userid   = strtolower($_POST['userid']);
	$password = $_POST['pwd'];
	$status   = null;
	
	# check the username or password fields
	if ( !$userid || !$password ) {
		$status = "login-req";
	} 
	
	# check for any errors
	if ( !$status ) {
		
		exec_action('successful-login-start'); // @hook successful-login-start login process started
		
		# hash the given password
		$password  = passhash($password);

		# does this user exist?
		if (file_exists($user_xml)) {
			# pull the data from the user's data file
			$data   = getXML($user_xml);
			$PASSWD = (string)$data->PWD;
			$USR    = strtolower($data->USR);

			# do the username and password match?
			if ( ($userid === $USR) && ($password === $PASSWD) ) {
				$authenticated = true;
				# add login success to failed logins log
				$logFailed = new GS_Logging_Class('logins.log');
				$logFailed->add('Username',$userid);
			} else {
				$authenticated = false;
				# add login failure to failed logins log
				$logFailed = new GS_Logging_Class('failedlogins.log');
				$logFailed->add('Username',$userid);
				$logFailed->add('Reason',i18n_r('INVALID_PASSWORD'));
			}
		} else {
			# user doesnt exist in this system
			$authenticated = false;
			# add login failure to failed logins log
			$logFailed = new GS_Logging_Class('failedlogins.log');
			$logFailed->add('Username',$userid);
			$logFailed->add('Reason',i18n_r('INVALID_USER'));
		}
		
		# is this successful?
		if( $authenticated ) {
			# YES - set the login cookie, then redirect user to secure panel		
			create_cookie();
			exec_action('successful-login-end');  // @hook successful-login-end login process authentication success
			$logFailed->save();			
			gotoDefaultPage();
		} else {
			# NO - show error message
			exec_action('successful-login-failed');  // @hook successful-login-failed login process authentication failed
			$status = "login-fail";
			$logFailed->save();
		} 
		
	} # end error check
	
	$update = $status;
	
} # end submission check


/* ?> */
