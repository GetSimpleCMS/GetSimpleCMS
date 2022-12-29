<?php

class MsgReporter
{
	private static $_msgs=array();
	private static $error_code;
	private static $error=false;

	public static function setClause($name, array $var=array(), $error=false, $dir=false)
	{
		i18n_merge('imanager') || i18n_merge('imanager','en_US');
		$dir = !empty($dir) ? $dir.'/' : 'imanager/';
		$o = i18n_r($dir . $name);
		if(empty($var))
		{
			if($error) {
				self::setError();
				self::$_msgs[] = '<li class="error">'.$o.'</li>';
			} else {
				self::$_msgs[] = '<li class="notify">'.$o.'</li>';
			}
			return;
		}
		foreach($var as $key => $value)
		{
			if($error) {
				self::setError();
				$o = '<li class="error">'.preg_replace('%\[\[( *)'.$key.'( *)\]\]%', $value, $o).'</li>';
			} else {
				$o = '<li class="notify">'.preg_replace('%\[\[( *)'.$key.'( *)\]\]%', $value, $o).'</li>';
			}
		}
		self::$_msgs[] = $o;
	}

	public static function getClause($name, array $var=array(), $dir=false)
	{
		i18n_merge('imanager') || i18n_merge('imanager','en_US');
		$dir = !empty($dir) ? $dir.'/' : 'imanager/';
		$o = i18n_r($dir . $name);
		if(empty($var))
			return $o;
		foreach($var as $key => $value)
			$o = preg_replace('%\[\[( *)'.$key.'( *)\]\]%', $value, $o);
		return $o;
	}

	public static function setError(){self::$error=true;}
	public static function setCode($val){self::$error_code = (int) $val; self::$error=true;}

	public static function msgs(){return (self::$_msgs);}
	public static function isError(){return (self::$error);}
	public static function errorCode(){return (self::$error_code);}

	public static function buildMsg()
	{
		$o = '';
		$msg = self::msgs();
		if(!empty($msg))
			foreach($msg as $val) $o .= $val;
		return '<ul class="msgs">'.$o.'</ul>';
	}
}
?>
