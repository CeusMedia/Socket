<?php
require_once '../vendor/autoload.php';
require_once '../src/Client.php';
new UI_DevOutput;

$host		= '0.0.0.0';
$port		= 8001;

$request	= 'Hello World';
$client	= new \CeusMedia\SocketStream\Client( $host, $port );
print_m( $client->getResponse( $request ) );
