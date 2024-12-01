<?PHP

/**
 * Project:		Absynthe sql4array
 * File:		sql4array.class.php5
 * Author:		Absynthe <sylvain@abstraction.fr>
 * Webste:		http://absynthe.is.free.fr/sql4array/
 * Version:		alpha 1
 * Date:		30/04/2007
 * License:		LGPL
 */

/**
 * Parameters available :
 * SELECT, DISTINCT, FROM, WHERE, ORDER BY, LIMIT, OFFSET
 *
 * Operators available :
 * =, <, >, <=, >=, <>, !=, IS, IS IN, IS NOT, IS NOT IN, LIKE, ILIKE, NOT LIKE, NOT ILIKE
 *
 * Functions available in WHERE parameters :
 * LOWER(var), UPPER(var), TRIM(var)
 */

class sql4array
{
	/**
	 * Init
	 */
	protected $query = FALSE;
	protected $parse_query = FALSE;
	protected $parse_query_lower = FALSE;
	protected $parse_select = FALSE;
	protected $parse_select_as = FALSE;
	protected $parse_from = FALSE;
	protected $parse_from_as = FALSE;
	protected $parse_where = FALSE;
	protected $distinct_query = FALSE;
	protected $tables = array();
	protected $response = array();

	protected static $cache_patterns = array();
	protected static $cache_replacements = array();

	/**
	 * sql4array setting
	 */
	protected $attr = array();

	/**
	 * sql4array tables map
	 */
	protected $globals = array();

	protected $cache_query = array();

	public function __construct()
	{
		$this
			->destroy()
			->createFromGlobals(true)
			->cacheQuery(true)
		;
	}

	/**
	 * set tables get from where
	 * and this func will reset $this->globals
	 *
	 * @return sql4array
	 */
	public function createFromGlobals($enabled = true)
	{
		$this->attr['createFromGlobals'] = $enabled;

		// reset $this->globals
		$this->globals = array();

		return $this;
	}

	/**
	 * @return array
	 */
	function table($table)
	{
		if ($this->attr['createFromGlobals'])
		{
			return $GLOBALS[$table];
		}
		else
		{
			return $this->globals[$table];
		}
	}

	/**
	 * set tables map
	 *
	 * @return sql4array
	 */
	function asset($key, $value) {
		$this->globals[$key] = $value;

		return $this;
	}

	/**
	 * Query function
	 *
	 * @return array - return $this->return_response();
	 */
	public function query($query)
	{
		$this->destroy();
		$this->query = $query;

		if (!$this->cacheQueryGet($this->query))
		{

			$this
				->parse_query()
				->parse_select()
				->parse_select_as()
				->parse_from()
				->parse_from_as()
				->parse_where()
			;

			$this->cacheQuerySet($this->query);

		}

		$this
			->exec_query()
			->parse_order()
		;

		return $this->return_response();
	}

	public function cacheQuery($val = true, $clear = false)
	{
		$this->attr['cacheQuery'] = $val;

		if ($clear) $this->cache_query = array();

		return $this;
	}

	/**
	 * @return bool
	 */
	protected function cacheQueryGet($query)
	{
		$key = md5($query);

		if (
			$this->attr['cacheQuery']
			&&
			array_key_exists($key, $this->cache_query)
			&& $data = &$this->cache_query[$key]
		)
		{
   			$this->query = $data['query'];
   			$this->parse_query = $data['parse_query'];
   			$this->parse_query_lower = $data['parse_query_lower'];
   			$this->parse_select = $data['parse_select'];
   			$this->parse_select_as = $data['parse_select_as'];
   			$this->parse_from = $data['parse_from'];
   			$this->parse_from_as = $data['parse_from_as'];
   			$this->parse_where = $data['parse_where'];
   			$this->distinct_query = $data['distinct_query'];

   			$data['count_used'] += 1;

   			return true;
		}

		return false;
	}

	protected function cacheQuerySet($query)
	{
		$key = md5($query);

		$data = array();

		$data['query'] = $this->query;
		$data['parse_query'] = $this->parse_query;
		$data['parse_query_lower'] = $this->parse_query_lower;
		$data['parse_select'] = $this->parse_select;
		$data['parse_select_as'] = $this->parse_select_as;
		$data['parse_from'] = $this->parse_from;
		$data['parse_from_as'] = $this->parse_from_as;
		$data['parse_where'] = $this->parse_where;
		$data['distinct_query'] = $this->distinct_query;

		$this->cache_query[$key] = $data;

		return $this;
	}

	/**
	 * Destroy current values
	 */
	protected function destroy()
	{
		$this->query = FALSE;
		$this->parse_query = FALSE;
		$this->parse_query_lower = FALSE;
		$this->parse_select = FALSE;
		$this->parse_select_as = FALSE;
		$this->parse_from = FALSE;
		$this->parse_from_as = FALSE;
		$this->parse_where = FALSE;
		$this->distinct_query = FALSE;
		$this->tables = array();
		$this->response = array();

		return $this;
	}

	/**
	 * Parse SQL query
	 */
	protected function parse_query()
	{
		$this->parse_query = preg_replace('#ORDER(\s){2,}BY(\s+)(.*)(\s+)(ASC|DESC)#i', 'ORDER BY \\3 \\5', $this->query);
		$this->parse_query = preg_split('#(SELECT|DISTINCT|FROM|JOIN|WHERE|ORDER(\s+)BY|LIMIT|OFFSET)+#i', $this->parse_query, -1, PREG_SPLIT_DELIM_CAPTURE);
		$this->parse_query = array_map('trim', $this->parse_query);
		$this->parse_query_lower = array_map('strtolower', $this->parse_query);

		return $this;
	}

	/**
	 * Parse SQL select parameters
	 */
	protected function parse_select()
	{
		$key = array_search('distinct', $this->parse_query_lower);

		if ($key === FALSE) $key = array_search("select", $this->parse_query_lower);
		else  $this->distinct_query = TRUE;

		$string = $this->parse_query[$key + 1];
		$arrays = preg_split('#((\s)*,(\s)*)#i', $string, -1, PREG_SPLIT_NO_EMPTY);

		foreach ($arrays as $array) $this->parse_select[] = $array;

		return $this;
	}

	/**
	 * Parse again SQL select parameters with as keyword
	 */
	protected function parse_select_as()
	{
		foreach ($this->parse_select as $select)
		{
			if (preg_match('#as#i', $select))
			{
				$arrays = preg_split('#((\s)+AS(\s)+)#i', $select, -1, PREG_SPLIT_NO_EMPTY);
				$this->parse_select_as[$arrays[1]] = $arrays[0];
			}
			else
			{
				$this->parse_select_as[$select] = $select;
			}
		}

		return $this;
	}

	/**
	 * Parse SQL from parameters
	 */
	protected function parse_from()
	{
		$key = array_search('from', $this->parse_query_lower);
		$string = $this->parse_query[$key + 1];
		$arrays = preg_split('#((\s)*,(\s)*)#i', $string, -1, PREG_SPLIT_NO_EMPTY);

		foreach ($arrays as $array) $this->parse_from[] = $array;

		return $this;
	}

	/**
	 * Parse again SQL from parameters with as keyword
	 */
	protected function parse_from_as()
	{
		foreach ($this->parse_from as $from)
		{
			if (preg_match('#as#i', $from))
			{
				$arrays = preg_split('#((\s)+AS(\s)+)#i', $from, -1, PREG_SPLIT_NO_EMPTY);

				$table = $arrays[0];
				$from = $arrays[1];
			}
			else
			{
				$table = $from;
			}

			$this->parse_from_as[$from] = $table;
			/*
			$this->tables[$from] = $this->table($table);
			*/
		}

		return $this;
	}

	/**
	 * Parse SQL where parameters
	 */
	protected function parse_where()
	{
		$key = array_search('where', $this->parse_query_lower);

		if ($key == FALSE)
		{
			$this->parse_where = 'return TRUE;';

			return $this;
		}

		$string = $this->parse_query[$key + 1];

		if (trim($string) == '') return $this->parse_where = 'return TRUE;';

		if (self::$cache_patterns && self::$cache_replacements)
		{
			$patterns = self::$cache_patterns;
			$replacements = self::$cache_replacements;
		}
		else
		{

			/**
			 * SQL Functions
			 */
			$patterns[] = '/LOWER\((.*)\)/ie';
			$patterns[] = '/UPPER\((.*)\)/ie';
			$patterns[] = '/TRIM\((.*)\)/ie';

			$replacements[] = "'strtolower(\\1)'";
			$replacements[] = "'strtoupper(\\1)'";
			$replacements[] = "'trim(\\1)'";

			/**
			 * Basics SQL operators
			 */
			$patterns[] = '/(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\.]+)(\))?(\s)+(=|IS)(\s)+([[:digit:]]+)(\s)*/ie';
			$patterns[] = '/(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\.]+)(\))?(\s)+(=|IS)(\s)+(\'|\")(.*)(\'|\")(\s)*/ie';
			$patterns[] = '/(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\.]+)(\))?(\s)+(>|<)(\s)+([[:digit:]]+)(\s)*/ie';
			$patterns[] = '/(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\.]+)(\))?(\s)+(<=|>=)(\s)+([[:digit:]]+)(\s)*/ie';
			$patterns[] = '/(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\.]+)(\))?(\s)+(<>|IS NOT|!=)(\s)+([[:digit:]]+)(\s)*/ie';
			$patterns[] = '/(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\.]+)(\))?(\s)+(<>|IS NOT|!=)(\s)+(\'|\")(.*)(\'|\")(\s)*/ie';
			$patterns[] = '/(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\.]+)(\))?(\s)+(IS)?(NOT IN)(\s)+\((.*)\)/ie';
			$patterns[] = '/(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\.]+)(\))?(\s)+(IS)?(IN)(\s)+\((.*)\)/ie';

			$replacements[] = "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 == \\9 '";
			$replacements[] = "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 == \"\\10\" '";
			$replacements[] = "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 \\7 \\9 '";
			$replacements[] = "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 \\7 \\9 '";
			$replacements[] = "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 != \\9 '";
			$replacements[] = "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 != \"\\10\" '";
			$replacements[] = "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 != ('.\$this->parse_in(\"\\10\").') '";
			$replacements[] = "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 == ('.\$this->parse_in(\"\\10\").') '";

			self::$cache_patterns = $patterns;
			self::$cache_replacements = $replacements;

		}

		/**
		 * match SQL operators
		 */
		$ereg = array('%' => '(.*)', '_' => '(.)');

		$patterns[] = '/([a-zA-Z0-9\.]+)(\s)+LIKE(\s)*(\'|\")(.*)(\'|\")/ie';
		$patterns[] = '/([a-zA-Z0-9\.]+)(\s)+ILIKE(\s)*(\'|\")(.*)(\'|\")/ie';
		$patterns[] = '/([a-zA-Z0-9\.]+)(\s)+NOT LIKE(\s)*(\'|\")(.*)(\'|\")/ie';
		$patterns[] = '/([a-zA-Z0-9\.]+)(\s)+NOT ILIKE(\s)*(\'|\")(.*)(\'|\")/ie';

		// TODO: use preg to replace ereg

		$replacements[] = "'preg_match(\"'.strtr(\"\\5\", \$ereg).'\", '.\$this->parse_where_key(\"\\1\").')'";
		$replacements[] = "'eregi(\"'.strtr(\"\\5\", \$ereg).'\", '.\$this->parse_where_key(\"\\1\").')'";
		$replacements[] = "'!preg_match(\"'.strtr(\"\\5\", \$ereg).'\", '.\$this->parse_where_key(\"\\1\").')'";
		$replacements[] = "'!eregi(\"'.strtr(\"\\5\", \$ereg).'\", '.\$this->parse_where_key(\"\\1\").')'";

		$this->parse_where = "return " . stripslashes(trim(preg_replace($patterns, $replacements, $string))) . ";";

		return $this;
	}

	/**
	 * return '$row[$this->parse_select_as[' . $key . ']]';
	 *
	 * @return string
	 */
	protected function parse_where_key($key)
	{
		if (preg_match('#\.#', $key))
		{
			list($table, $col) = explode('.', $key);

			$key = $col;
		}

    $this->parse_select_as[$key] = $key;    
		return '$row[$this->parse_select_as[\'' . $key . '\']]';
	}

	/**
	 * Format IN parameters for PHP
	 *
	 * @return string
	 */
	protected function parse_in($string)
	{
		$array = explode(',', $string);
		$array = array_map('trim', $array);

		return implode(' || ', $array);
	}

	/**
	 * Execute query
	 */
	protected function exec_query()
	{
		$klimit = array_search('limit', $this->parse_query_lower);
		$koffset = array_search('offset', $this->parse_query_lower);

		if ($klimit !== FALSE) $limit = (int)$this->parse_query[$klimit + 1];

		if ($koffset !== FALSE) $offset = (int)$this->parse_query[$koffset + 1];

		$irow = 0;
		$distinct = array();

		foreach ($this->parse_from_as as $from_name => $table_name)
		{

			$this->tables[$from_name] = $this->table($table_name);

			foreach ($this->tables[$from_name] as $row)
			{
				// Offset
				if ($koffset !== FALSE && $irow < $offset)
				{
					$irow++;
					continue;
				}
                
				if (eval($this->parse_where))
				{
					if (isset($this->parse_select_as['*']) and $this->parse_select_as['*'] == '*')
					{
						foreach (array_keys($row) as $key) $temp[$key] = $row[$key];

						if ($this->distinct_query && in_array($temp, $distinct)) continue;
						else  $this->response[] = $temp;

						# $distinct[] = $response; // what is this
					}
					else
					{
						foreach ($this->parse_select_as as $key => $value) $temp[$key] = $row[$value];

						if ($this->distinct_query && in_array($temp, $distinct)) continue;
						else  $this->response[] = $temp;

						$distinct[] = $temp;
					}

					// Limit
					if ($klimit !== FALSE && count($this->response) == $limit) break;
				}

				$irow++;
			}
		}

		return $this;
	}

	/**
	 * Parse SQL order by parameters
	 */
	protected function parse_order()
	{
		$key = array_search('order by', $this->parse_query_lower);

		if ($key === FALSE) return;

		$string = $this->parse_query[$key + 2];
		$arrays = explode(',', $string);

		if (!is_array($arrays)) $arrays[] = $string;

		$arrays = array_map('trim', $arrays);

		$multisort = 'array_multisort(';

		foreach ($arrays as $array)
		{
			list($col, $sort) = preg_split('#((\s)+)#', $array, -1, PREG_SPLIT_NO_EMPTY);
			$multisort .= "\$this->split_array(\$this->response, '$col'), SORT_" . strtoupper($sort) . ', SORT_STRING, ';
		}

		$multisort .= '$this->response);';

		eval($multisort);

		return $this;
	}

	/**
	 * Return response
	 */
	protected function return_response()
	{
		return $this->response;
	}

	/**
	 * Return a column of an array
	 */
	protected function split_array($input_array, $column)
	{
		$output_array = array();

		foreach ($input_array as $key => $value) $output_array[] = $value[$column];

		return $output_array;
	}

	/**
	 * Entire array search
	 */
	protected function entire_array_search($needle, $array)
	{
		foreach ($array as $key => $value)
			if ($value === $needle) $return[] = $key;

		if (!is_array($return)) $return = FALSE;

		return $return;
	}
	
	public function get_tablenames($query){
			$this->destroy();
			$this->query = $query;

			# if (!$this->cacheQueryGet($this->query))
			# {

				$this
					->parse_query()
					->parse_from()
				;
			# }
			
			return $this->parse_from;
	}		
}

?>