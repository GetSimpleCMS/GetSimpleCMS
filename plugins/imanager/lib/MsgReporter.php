<?php

class MsgReporter
{
	public static $plugin = 'imanager';
	public static $dir = 'imanager';
	private static $mergedLang=null;
	private static $_msgs=array();
	private static $error_code;
	private static $error=false;

	public static function setClause($name, array $var=array(), $error=false, $dir=false)
	{
		if(!self::$mergedLang) {
			self::$mergedLang = true;
			i18n_merge(self::$plugin) || i18n_merge(self::$plugin,'en_US');
		}
		$dir = !empty($dir) ? $dir.'/' : self::$dir.'/';
		$o = i18n_r($dir . $name);
		if(empty($var))
		{
			if($error) {
				self::setError();
				self::$_msgs[] = '<li class="im-error">'.$o.'<a class="close" href="javascript:void(0)"><i class="fa fa-times"></i></a></li>';
			} else {
				self::$_msgs[] = '<li class="im-notify">'.$o.'<a class="close" href="javascript:void(0)"><i class="fa fa-times"></i></a></li>';
			}
			return;
		}


		if($error) {
			foreach($var as $key => $value) {
				$o = preg_replace('%\[\[( *)'.$key.'( *)\]\]%', $value, $o);
			}
			self::setError();
			$o = '<li class="im-error">'.$o.'<a class="close" href="javascript:void(0)"><i class="fa fa-times"></i></a></li>';
		} else {
			foreach($var as $key => $value) {
				$o = preg_replace('%\[\[( *)'.$key.'( *)\]\]%', $value, $o);
			}
			$o = '<li class="im-notify">'.$o.'<a class="close" href="javascript:void(0)"><i class="fa fa-times"></i></a></li>';
		}

		self::$_msgs[] = $o;
	}

	public static function getClause($name, array $var=array(), $dir=false)
	{
		if(!self::$mergedLang) {
			self::$mergedLang = true;
			i18n_merge(self::$plugin) || i18n_merge(self::$plugin,'en_US');
		}
		$dir = !empty($dir) ? $dir.'/' : self::$dir.'/';
		$o = i18n_r($dir . $name);
		if(empty($var))
			return $o;
		foreach($var as $key => $value)
			$o = preg_replace('%\[\[( *)'.$key.'( *)\]\]%', $value, $o);
		return $o;
	}

	public static function removeClauseByValue($value)
	{
		$key = array_search($value, self::$_msgs);
		if($key !== false) unset(self::$_msgs[$key]);
	}

	public static function setError(){self::$error=true;}
	public static function setCode($val){self::$error_code = (int) $val; self::$error=true;}

	public static function msgs() {return self::$_msgs;}
	public static function isError(){return self::$error;}
	public static function errorCode(){return self::$error_code;}

	public static function buildMsg()
	{
		$o = '';
		$msg = self::msgs();
		if(!empty($msg))
			foreach($msg as $val) $o .= $val;
		return '<ul class="im-msgs msgs">'.$o.'</ul>';
	}
}
?>
