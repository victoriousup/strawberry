<?php

return [

	'alias' => env('MAXCDN_ALIAS'),
	'key' => env('MAXCDN_KEY'),
	'secret' => env('MAXCDN_SECRET'),

	'cdn' => [

		'zone' => env('MAXCDN.CDN.ZONE'),

		'url' => env('MAXCDN.CDN.URL'),
	],


	'static' => [

		'zone' => env('MAXCDN.STATIC.ZONE'),

		'url' => env('MAXCDN.STATIC.URL'),
	],

];
