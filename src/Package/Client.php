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
class Client extends \CeusMedia\SocketStream\Client{


	protected function decodeResponse( $response ){
		$package	= new \CeusMedia\SocketStream\Package();
		$package->fromSerial( $response );
		return $package;
	}

	protected function encodeRequest( $request ){
		$request	= Builder::buildPackage( $request );
		return $request->toSerial();
	}
}
?>
