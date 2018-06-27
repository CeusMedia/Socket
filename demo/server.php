<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 'On' );

require_once '../vendor/autoload.php';
require_once '../src/Server.php';

$host		= '0.0.0.0';
$port		= 8001;

new UI_DevOutput();
class CountingEchoServer extends \CeusMedia\SocketStream\Server{

	protected function handleRequest( $connection, $request ){
		$this->countRequest();
		return $request;
	}

	protected function countRequest(){
		$lock	= new \FS_File_Lock( 'counter.lock' );
		$lock->setSleep( rand( 1, 10 ) / 1000 );
		$lock->lock();
		$counter	= (int) file_get_contents('counter.txt');
		file_put_contents( 'counter.txt', $counter + 1 );
		$lock->unlock();
	}
}
$server	= new CountingEchoServer( $host, $port );
