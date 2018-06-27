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
class Builder{

	static public function buildPackage( $data, $format = 'json' ){
		if( $data instanceof \CeusMedia\SocketStream\Package )
			return $data;
		if( is_resource( $data ) )
			throw new InvalidArgumentException( 'Resources are not supported' );
		$package	= new \CeusMedia\SocketStream\Package( $format );
		if( is_object( $data ) ){
			if( $data instanceof \Renderable ){
				if( method_exists( $data, 'render' ) )
					$package->setData( $data->render() );
				else
					$package->setData( (string) $data );
			}
			else
				$package->setData( $data );
		}
		else
			$package->setData( $data );
		return $package;
	}
}
