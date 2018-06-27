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
class Client{

	protected $host;
	protected $port;
	protected $socket;

	public function __construct( $host, $port = 8000 ){
		$this->host		= $host;
		$this->port		= $port;
		$this->address	= 'tcp://'.$host.':'.$port;
	}

	protected function decodeResponse( $response ){
		return $response;
	}

	protected function encodeRequest( $request ){
		return $request;
	}

	public function getHost(){
		return $this->host;
	}

	public function getPort(){
		return $this->port;
	}

	public function getResponse( $request ){
		$socket		= stream_socket_client( $this->address, $errno, $errstr, 30 );
#		stream_set_blocking( $socket, 0 );
		if( !$socket )
			throw new \RuntimeException( $errstr.' ('.$errno.')' );

		$buffer		= "";
		fwrite( $socket, $this->encodeRequest( $request ) );
		while( !feof( $socket ) )
			$buffer	.= fgets( $socket, 1024 );
		fclose( $socket );
		return $this->decodeResponse( $buffer );
	}
}
?>
