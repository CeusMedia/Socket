<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 'On' );

require_once '../../vendor/autoload.php';
require_once '../../src/Server.php';
require_once '../../src/Package.php';
require_once '../../src/Package/Builder.php';
require_once '../../src/Package/Server.php';
new UI_DevOutput();

$host		= '0.0.0.0';
$port		= 8001;

class CountingEchoServer extends \CeusMedia\SocketStream\Package\Server{

	protected function countRequest(){
		$lock	= new \FS_File_Lock( 'counter.lock' );
		$lock->setSleep( rand( 1, 10 ) / 1000 );
		$lock->lock();
		$counter	= (int) file_get_contents('counter.txt');
		file_put_contents( 'counter.txt', $counter + 1 );
		$lock->unlock();
	}

	protected function handleRequest( $connection, $request ){
		$this->countRequest();
		$request	= $this->decodeRequest( $request );
		return $request->getData();
	}
}

$server	= new CountingEchoServer( $host, $port );
