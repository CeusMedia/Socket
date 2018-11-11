<?php
/**
 *	@category		Library
 *	@package		CeusMedia_Common_Net_Socket_Stream
 */
namespace CeusMedia\SocketStream;
/**
 *	@category		Library
 *	@package		CeusMedia_Common_Net_Socket_Stream
 */
abstract class Server{

	const FORMAT_PHP		= 0;
	const FORMAT_JSON		= 1;

	protected $socket		= NULL;
	protected $port;

	public function __construct( $host = '0.0.0.0', $port = 8000 ){
		set_time_limit( 0 );
		try{
			$this->socket = stream_socket_server( "tcp://".$host.":".$port, $errorNumber, $errorMessage );
			if( !$this->socket )
				throw new \RuntimeException( $errorMessage, $errorNumber );
			stream_set_blocking( $this->socket, 0 );
			stream_set_timeout( $this->socket, 0 );
			$this->__onServerConnected();
			$this->runService();
		}
		catch( \Exception $e ){
			$this->handleServerException( $e );
		}
	}

	public function __destruct(){
		if( $this->socket )
			@fclose( $this->socket );
	}

	protected function __onServerConnected(){}

	public function disconnect(){
		if( $this->socket )
			@fclose( $this->socket );
	}

	abstract protected function handleRequest( $connection, $request );

	protected function handleServerException( $e ){
		switch( get_class( $e ) ){
			default:
				$message	= "[".$e->getCode."] ".$e->getMessage()."\n";
				die( $message );
		}
	}

	protected function handleUncatchedAppException( $e ){
		echo "EXCEPTION: ".$e->getMessage()."\n";
		echo $e->getTraceAsString()."\n";
	}


	protected function runService(){
		while( $this->socket && $conn = stream_socket_accept( $this->socket ) ){
			$content	= fread( $conn, 64 * 1024 );
			try{
				$response	= $this->handleRequest( $conn, $content );
				$this->sendResponse( $conn, $response );
			}
			catch( \Exception $e ){
				$this->handleUncatchedAppException( $e );
				fclose( $conn );
			}
		}
	}

	protected function encodeResponse( $response ){
		return $response;
	}

	protected function sendResponse( $connection, $response ){
		$response	= $this->encodeResponse( $response );
		fwrite( $connection, $response );
		fclose( $connection );
	}
}
?>
