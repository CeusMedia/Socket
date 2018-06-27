<?php
/**
 *	@category		Library
 *	@package		CeusMedia_Common_Net_Socket_Stream
 */
namespace CeusMedia\SocketStream\Package;
/**
 *	@category		Library
 *	@package		CeusMedia_Common_Net_Socket_Stream
 */
abstract class Server extends \CeusMedia\SocketStream\Server{

	protected function decodeRequest( $request ){
		$package	= new \CeusMedia\SocketStream\Package();
		$package->fromSerial( $request );
		return $package;
	}

	protected function encodeResponse( $response ){
		$response	= Builder::buildPackage( $response );
		return $response->toSerial();
	}

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
}
?>
