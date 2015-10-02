<?php
$page =
[
	"name" => "products.php",
	"title" => "Products",
	"table" => "products",
	"primary" => "id",
	"fields" =>
	[
		[
			"title" => "Name",
			"name" => "name",
			"type" => "text",
			"required" => true
		],
		[
		  "title" => "Image",
		  "name" => "image",
		  "type" => "image",
		  "destination" => "products",
		  "required" => true
		],
		[
			"title" => "Price",
			"name" => "price",
			"type" => "text",
			"required" => true
		],
		[
			"title" => "Stock",
			"name" => "stock",
			"type" => "text",
			"value" => 0
		]
	]
];
include "header.php";
$engine = new Engine($page);
include "footer.php";
?>
