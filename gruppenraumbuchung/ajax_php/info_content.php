<?php 
// Info Content
if(isset($_GET['page'])) $page=$_GET['page'];
else 
{
	echo 0;
	exit;
}

if(isset($_COOKIE["gap_language"])) $language=$_COOKIE["gap_language"];
else $language="de";

$file_path="../info/" . $page . "_" . $language . ".html";
if(!file_exists($file_path))
{
	echo 0;
	exit;
}

$content=file_get_contents($file_path);

header("content-type:text/html; charset=ISO-8859-1");
echo $content;


?>