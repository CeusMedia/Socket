<?php
require_once '../../vendor/autoload.php';
require_once '../../src/Client.php';
require_once '../../src/Package.php';
require_once '../../src/Package/Builder.php';
require_once '../../src/Package/Client.php';
new UI_DevOutput;

$host		= '0.0.0.0';
$port		= 8001;

$request	= new \CeusMedia\SocketStream\Package( 'wddx' );
$request->setData( array( 'date' => time(), 'message' => 'Hello World!' ) );
$client	= new \CeusMedia\SocketStream\Package\Client( $host, $port );
print_m( $client->getResponse( $request ) );
