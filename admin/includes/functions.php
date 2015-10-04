<?php
include "database.php";

$title = "eShop";
$apath = "http://localhost/eshop/";
$path = $apath . "admin/";
$uploads["files"] = "uploads/";
$uploads["images"] = "uploads/images/";
@session_start();
$db = new Database("localhost", "root", "", "eshop");
$db->connect();

function currentURL()
{
	$pageURL = "http";
	if($_SERVER["HTTPS"] == "on") $pageURL .= "s";
	$pageURL .= "://" . $_SERVER["SERVER_NAME"];
	if($_SERVER["SERVER_PORT"] != "80") $pageURL .= ":" . $_SERVER["SERVER_PORT"];
	$pageURL .= $_SERVER["REQUEST_URI"];
	return $pageURL;
}

function goToUrl($url)
{
	echo '<script>location.href = "' . $url . '";</script>';
}

function getFiles(&$file_post, $name)
{
	$file_ary = array();
	$file_count = count($file_post['name']);
	$file_keys = array_keys($file_post);

	for ($i = 0; $i < $file_count; $i++) foreach ($file_keys as $key) $file_ary[$i][$name][$key] = $file_post[$key][$i];
	return $file_ary;
}

function removeParameter($url, $key)
{
	$url = preg_replace('/(?:&|(\?))' . $key . '=[^&]*(?(1)&|)?/i', "$1", $url);
	$params = explode("?", $url);
	if(sizeof($params) == 1 || $url[strlen($url) - 1] == '?') return $params[0]; else return $url;
}

function addParameter($url, $key, $value)
{
	$url = removeParameter($url, $key);
	$params = explode("?", $url);
	if(sizeof($params) == 1 || $url[strlen($url) - 1] == '?') return "{$params[0]}?{$key}={$value}";
	else return "{$url}&{$key}={$value}";
}

function addParameters($url, $array)
{
	foreach($array as $element) $url = addParameter($url, $element[0], $element[1]);
	return $url;
}

function my_date($a, $b)
{
	global $time_zone;
	return date($a, ($b?$b:time()) + $time_zone * 3600);
}
?>
