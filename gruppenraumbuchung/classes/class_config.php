<?php 
class Config
{
	public static function getValue($var)
	{
		$access = new DB_Access("gap");
		
		$sql="SELECT * FROM gap_config WHERE var='$var'";
		$access->executeSQL($sql);
		
		if($access->rows)
		{
			return $access->db_data[0]["value"];
		}
		else return $var;
	}
	
	public static function getValues()
	{
		$access = new DB_Access("gap");
		
		$sql="SELECT * FROM gap_config";
		$access->executeSQL($sql);
		
		$values=array();
		for($t=0; $t < $access->rows; $t++)
		{
			$var=$access->db_data[$t]["var"];
			$value=$access->db_data[$t]["value"];
			
			$values[$var]=$value;
		}
		
		return $values;
	}
	
}
?>