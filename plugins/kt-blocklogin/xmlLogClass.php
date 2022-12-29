<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

class xmlLogClassError extends Exception {
}

class xmlLogClass{
 private $oldflag = false;
 private $createdflag = false;
 private $domhandle = false;
 private $ip2check = false;
 private $ip2checktime = false;

 /**
  * Create a lock file
  *
  * @param string $ktfilelockname name of lock file
  */
private function kt_create_file_lock($ktfilelockname = KTLOCKFILE) {
	$starttime = time();
	while (! ($handle = @fopen($ktfilelockname, 'x'))) {
 		if (time() - $starttime > KTMAXSLEEP)
 			return false;
 		usleep(KTSLEEPINTERVAL);
 	}
 	fclose($handle);
 	return true;
}

 /**
  * delet a lock file and save the xml failed log
  *
  * @param string $ktfilelockname name of lock file
  */
private function kt_delete_file_lock ($ktfilelockname = KTLOCKFILE){
	$this->domhandle->save(KTFAILEDPATH,LIBXML_NOBLANKS);
	unlink($ktfilelockname);
} 
 
/** The contructor of the class try to get a file lock
 *  if it fails it will throw an error
 * 
 * @param string $ktip ip adress
 * @param Timestamp $ktiptime
 * @throws xmlLogClassError
 */
function __construct($ktip , $ktiptime){
	if(!$this->kt_create_file_lock())
		throw new xmlLogClassError();
	else{
	$this->ip2check = 'ip' . $ktip;
	$this->ip2checktime = $ktiptime;
	if(!file_exists(KTFAILEDPATH)){
		$this->domhandle = new DOMDocument();
		$this->domhandle->loadXML('<root></root>');
		$this->createdflag = true;
	}
	elseif (filesize(KTFAILEDPATH)>KTMAXFILESIZE){
		rename(KTFAILEDPATH, KTFAILEDPATHBU);
		$this->domhandle = new DOMDocument();
		$this->domhandle->loadXML('<root></root>');
		$this->oldflag = new DOMDocument();
		$this->oldflag->load(KTFAILEDPATHBU,LIBXML_NOBLANKS);
	}
	else {
	$this->domhandle = new DOMDocument();
	$this->domhandle->load(KTFAILEDPATH,LIBXML_NOBLANKS);
	}
	}
}


/** Add a static constructor to the class which will handle the error 
 * thrown by the constructorand return false on failed
 * 
 * @param string $ktip ip adress
 * @param Timestamp $ktiptime
 * @return multi The created Object or false on failure
 */
static function kt_create_class($ktip , $ktiptime) {
	try{
		return new xmlLogClass($ktip , $ktiptime);
	}
	catch (xmlLogClassError){
		return false;
	}
}

/**
 * keep and update the log file if we have made it this 
 * far this means that we have incorrect usr / passwd values 
 * redirect the client to the login page
 * 
 * @return boolean true 
 */
private function kt_log_maintain(){
	$root = $this->domhandle->documentElement;
	$tmpel = $root->getElementsByTagName($this->ip2check);
	$tmpel = $tmpel->item(0);
	if(!$tmpel){
		$tmpel = $this->domhandle->createElement($this->ip2check);
		if(!$root->hasChildNodes())
			$root->appendChild($tmpel);
		else
			$root->insertBefore($tmpel,$root->firstChild);
		$tmpel->setAttribute('start',$this->ip2checktime);
		$tmpel->setAttribute('middle','');
		$tmpel->setAttribute('end','');
		$tmpel->setAttribute('counter','1');
	}
	else{
		$counter = ((int) $tmpel->getAttribute('counter')) + 1;	
		$middle = $tmpel->getAttribute('middle');
		$start = $tmpel->getAttribute('start');		
		$tmpel->setAttribute('counter',$counter);
		$tmpel->setAttribute('middle',$start);
		$tmpel->setAttribute('end',$middle);
		$tmpel->setAttribute('start',$this->ip2checktime);
	}
	$this->kt_delete_file_lock();
	return true;
}

/**
 * Verify if the user correctly entered the user name password
 * if so redirect him to the admin panel if not call kt_log_maintain
 * to capture the failed login attempt
 */
private function kt_verify_login() {
	global $user_xml , $userid , $password,$cookie_redirect,$USR ;
	if (file_exists($user_xml)){
		$password = passhash($password);
		$data = getXML($user_xml);
		$PASSWD = $data->PWD;
		$USR = strtolower($data->USR);
		if ( ($userid == $USR) && ($password == $PASSWD) ) {
			$this->kt_delete_file_lock();
			create_cookie();
			setcookie('GS_ADMIN_USERNAME', $USR, time() + 3600,'/');
			exec_action('successful-login-end');
			redirect($cookie_redirect);
		}
	}
	return $this->kt_log_maintain();
}

/**
 * check if the ip adress is located inside the failed login log , if not
 * call kt_verify_login
 * 
 * @return boolean true if must block the client
 */
function kt_check_deny() {
	if(!$this->createdflag){
		$handle = $this->domhandle;
		if($this->oldflag)
			$handle = $this->oldflag;
		$elem = $handle->getElementsByTagName($this->ip2check);
		$elem = $elem->item(0);
		if($elem){
			$counter = (int) $elem->getAttribute('counter');
			$lasttime = (int) $elem->getAttribute ('start');
			if(($counter % 3 ==0) && ($this->ip2checktime - $lasttime <= KTBANTIME)){
				if($this->oldflag){
					$tmpnode = $this->domhandle->importNode($elem);
					$this->domhandle->documentElement->appendChild($tmpnode);
				}
			$this->kt_delete_file_lock();
			return true;
			}
		}
	}

	return $this->kt_verify_login();
}

	
}


?>
