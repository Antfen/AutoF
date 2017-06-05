<?php
return array(
	"title" => __("Icon Text", "theme_admin"),
	"shortcode" => 'icon',
	"type" => 'enclosing',
	"options" => array(
		array(
			"name" => __("Style",'theme_admin'),
			"id" => "style",
			"default" => '',
			"options" => array(
				"globe" => 'globe',
				"home" => 'home',
				"email" => 'email',
				"user" => 'user',
				"multiuser" => 'multiuser',
				"id" => 'id',
				"addressbook" => 'addressbook',
				"phone" => 'phone',
				"cellphone" => 'cellphone',
				"fax" => 'fax',
				"link" => 'link',
				"chain" => 'chain',
				"calendar" => 'calendar',
				"tag" => 'tag',
				"download" => 'download',
				"comments" => 'comments',
				"comment"  => 'comment',
				'comment-o' => 'comment-o',
				'comment-s' => 'comment-s',
				'heart' => 'heart',
				'heart-o' => 'heart-o',
				"thumbs-up" => "thumbs-up",
				"thumbs-down" => "thumbs-down",
				"key" => "key",
				"lightbulb" => "lightbulb",
				"eye" => "eye",
				"help" => "help",
				"marker" => "marker",
				"gift" => "gift",
				"star" => "star",
				"flag" => "flag",
				"medal" => "medal",
				"clock" => "clock",
				"cart" => "cart",
				"trash" => "trash",
				"cog" => "cog",
				"ban" => "ban",
				"times" => "times",
				"pencil" => "pencil",
				"note" => "note",
				"book" => "book",
				"gallery" => "gallery",
				"picture" => "picture",
				"movie" => "movie",
				"music" => "music",
				"play" => "play",
				"check" => "check",
				"check-b" => "check-b",
				"check-circle" => "check-circle",
				"check-circle-o" => "check-circle-o",
				"check-circle-d" => "check-circle-d",
				"check-square" => "check-square",
				"check-square-d" => "check-square-d",
				"arrow" => "arrow",
				"arrow-circle" => "arrow-circle",
				"arrow-circle-o" => "arrow-circle-o",
				"circle" => "circle",
				"info" => "info",
				"info-o" => "info-o",
				"question" => "question",
				"question-o" => "question-o",
				"exclamation" => "exclamation",
				"exclamation-triangle" => "exclamation-triangle",
				"exclamation-circle" => "exclamation-circle",
				"mobile" => "mobile",
				"tablet" => "tablet",
				"desktop" => "desktop",
			),
			"type" => "select",
		),
		array(
			"name" => __("Color (Optional)&#x200E;",'theme_admin'),
			"id" => "color",
			"default" => "",
			"prompt" => __("Choose one..",'theme_admin'),
			"options" => array(
				"black" => 'Black',
				"gray" => 'Gray',
				"red" => 'Red',
				"yellow" => 'Yellow',
				"blue" => 'Blue',
				"pink" => 'Pink',
				"green" => 'Green',
				"rosy" => 'Rosy',
				"orange" => 'Orange',
				"magenta" => 'Magenta',
			),
			"type" => "select",
		),
		array(
			"name" => __("Text",'theme_admin'),
			"id" => "content",
			"default" => "",
			"type" => "textarea"
		),
	),
	"custom" => '',
);