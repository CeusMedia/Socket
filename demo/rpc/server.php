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

class Service{
	public function __construct(){

	}

	public function date( $format = 'c' ){
		return date( $format );
	}
}

class RpcServer extends \CeusMedia\SocketStream\Package\Server{

	protected function __onServerConnected(){
		remark( 'Connected.' );
	}

	protected function handleRequest( $connection, $request ){
		$request	= $this->decodeRequest( $request );
		$data		= $request->getData();
		if( !isset( $data['proc'] ) )
			throw new InvalidArgumentException( 'No procedure (proc) given' );
		$procedure	= $data['proc'];
		$arguments	= isset( $data['args'] ) ? $data['args'] : array();

		switch( $procedure ){
			case 'serverRestart':
				$this->sendResponse( $connection, array(
					'command'	=> 'restart',
					'status'	=> 'OK',
				) );
				$this->disconnect();
				exit;
		}
		if( !method_exists( 'Service', $procedure ) )
			throw new InvalidArgumentException( 'Invalid procedure: '.$procedure );
		$response	= Alg_Object_MethodFactory::callClassMethod(
			'Service',
			$procedure,
			array( $this ),
			$arguments
		);
		return $response;
	}
}

$server	= new RpcServer( $host, $port );
