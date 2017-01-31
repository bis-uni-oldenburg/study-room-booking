<?php 
class LOC
{
	public static function getLocale($key_term)
	{
		$access=new DB_Access("gap");
		
		$language=self::getLanguage();
		
		$table="gap_localization";
		
		$sql="SELECT * FROM $table WHERE key_term='$key_term' AND language='$language'";
		$access->executeSQL($sql);
		
		if($access->rows)
		{
			return $access->db_data[0]["term"];
		}
		else return $key_term;
	} 
	
	public static function getLocales()
	{
		$access=new DB_Access("gap");
		
		$language=self::getLanguage();
		
		$table="gap_localization";
		
		$sql="SELECT * FROM $table WHERE language='$language'";
		$access->executeSQL($sql);
		
		$locales=array();
		for($t=0; $t < $access->rows; $t++)
		{
			$key_term=$access->db_data[$t]["key_term"];
			$term=$access->db_data[$t]["term"];
			
			$locales[$key_term]=$term;
		}
		
		return $locales;
	} 
	
	public static function getLanguage()
	{
		//return Config::getValue("language");

		return $_COOKIE["gap_language"];
	}
	
	public static function getDefaultLanguage()
	{
		return Config::getValue("default_language");
	}
	
	public static function getLanguageLinks($query_string, $current_language, $separator="&nbsp;&nbsp;|&nbsp;&nbsp;")
	{		
		$access=new DB_Access();

		$sql="SELECT language FROM gap_localization GROUP BY language";
		$access->executeSQL($sql);
		
		$links=array();
		
		for($t=0; $t < $access->rows; $t++)
		{
			$language=$access->db_data[$t]["language"];
			$language_loc=self::getLocale($language);
			
			if($language==$current_language)
			{
				$link="<span>$language_loc</span>";
			}
			else 
			{
				$link="<a href=\"?$query_string&language=$language\">$language_loc</a>";
			}
			
			$links[]=$link;
		}
		
		$links=implode($separator, $links);
		return $links;
	}
}

?>