<?php

// Class Template

class Template
{
	// Members
	
	protected $frame_domain;
	protected $frame_regex;
	protected $content_regex;
	protected $localization;

	
	// Constructor
	
	function __construct()
	{
		$this->localization=false;
	}
	
	// Methods
	
	function getTemplate($template, $path="")
	{
		$file=$path . "templates/$template.tpl";
		if(file_exists($file) and filesize($file))
		{
			$content=file_get_contents($file);
			
			if(preg_match("!^@(.+)$!", $content, $match))
			{
				$alt_tpl=$match[1];
				$content=$this->getTemplate($alt_tpl);
			}
			
			if($this->localization) $content=$this->localize($content);
		}
		else $content="";
		
		return $content;
	}
	
	function enableLocalization()
	{
		$this->localization=true;
	}
	
	function disableLocalization()
	{
		$this->localization=false;
	}
	
	function localize($html)
	{
		$regex="!%~([^%]+)%!";
		if(preg_match_all($regex, $html, $matches))
		{
			foreach($matches[1] as $loc_term)
			{
				$html=str_replace("%~" . $loc_term . "%", LOC::getLocale($loc_term), $html);
			}
		}
		
		return $html;
	}
	
	
	function getLoop($html, $loop_name="loop")
	{
		$regex="!%" . $loop_name . "%(.*)%\/" . $loop_name . "%!s";

		if(preg_match($regex, $html, $treffer)) $loop=$treffer[1];
		else $loop=$html;
		
		return $loop;
	}
	
	
	function getBlock($html, $block_name)
	{
		$regex="!%" . $block_name . "%(.*)%\/" . $block_name . "%!s";

		preg_match($regex, $html, $treffer);
		$block=$treffer[1];
		
		return $block;
	}
	
	
	function getTop($html, $loop_name="loop")
	{
		$regex="!(.*)%" . $loop_name . "%!s";
		
		if(preg_match($regex, $html, $treffer)) $top=$treffer[1];
		else $top=$html;
		
		return $top;
	}
	
	
	function getBottom($html, $loop_name="loop")
	{
		$regex="!%\/" . $loop_name . "%(.*)!s";
		
		if(preg_match($regex, $html, $treffer)) $bottom=$treffer[1];
		else $bottom=$html;
		
		return $bottom;
	}
	
	
	function setExtension($tpl_extension)
	{
		$this->tpl_extension=$tpl_extension;
	}
	
	
	
	function tplReplace($html, $placeholders, $replace=false)
	{
		if(!is_array($placeholders) and $replace)
		{
			$placeholders=array($placeholders=>$replace);
		}
		
		foreach($placeholders as $placeholder => $value)
		{
			$regex="!%if " . $placeholder . "%(.*)%\/if " . $placeholder . "%!s";
			if($value)
			{
				$html=preg_replace($regex, "$1", $html);
			}
			else $html=preg_replace($regex, "", $html);	
			$html=preg_replace("!(%" . $placeholder . "%)!", $value, $html);
		}
		
		return $html;
	}
	
	
	function tplLoop($tpl_data, $html, $loop_name="loop")
	{
		$rows=count($tpl_data);
		$output=$this->getTop($html, $loop_name);
		$loop=$this->getLoop($html, $loop_name);
		
		for($t=0;$t<$rows;$t++)
		{
			$output.=$this->tplReplace($loop,$tpl_data[$t]);
		}
		
		$output.=$this->getBottom($html, $loop_name);
		
		return $output;
	}
	
	
	function tplReplaceLoop($html, $replacement, $loop_name="loop")
	{
		$output=$this->getTop($html, $loop_name);
		$output.=$replacement;
		$output.=$this->getBottom($html, $loop_name);
		
		return $output;
	}
	
	
	function removePlaceholders($html)
	{
		$regex="!(%[^%]+%)!";
		$html=preg_replace($regex, "", $html);
		
		return $html;
	}
	
	
	function displayOptionalArea($html, $area, $bool)
	{
		$regex="!%optional " . $area . "%(.*?)%\/optional " . $area . "%!s";

		if($bool) $output=preg_replace($regex,"$1",$html);
		else $output=preg_replace($regex,"",$html);
		
		return $output;
	}

}
