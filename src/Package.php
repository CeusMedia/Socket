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
class Package{

	public $data;
	public $format		= NULL;
	public $serial		= NULL;

	public function __construct( $format = NULL ){
		if( !is_null( $format ) )
			$this->setFormat( $format );
	}

	/**
	 *	Converts a Data Array to a XML Structure and appends it to the given SimpleXMLElement.
	 *	@access		protected
	 *	@param		XML_Element		$xmlNode		XML Node to append to
	 *	@param		array			$dataArray		Array to append
	 *	@param		string			$lastParent		Recursion: Outer Node Name for Integer Values
	 *	@return		void
	 */
	protected function addArrayToXmlNode( &$xmlNode, $dataArray, $lastParent = "" ){
		if( !( is_string( $lastParent ) && $lastParent ) )
			$lastParent	= "item";
		foreach( $dataArray as $key => $value ){
			if( is_array( $value ) ){
				if( is_int( $key ) ){
					$child	=& $xmlNode->addChild( "set" );
					$this->addArrayToXmlNode( $child, $value, "items" );
					continue;
				}
				$child	=& $xmlNode->addChild( $key );
				$this->addArrayToXmlNode( $child, $value, $key );
				continue;
			}
			else if( is_int( $key ) ){
				if( $lastParent )
					$key	= $this->getSingular( $lastParent );
				else
					$key	= "item";
			}
			$xmlNode->addChild( $key, str_replace( "&", "&amp;", $value ) );
		}
	}

	public function getData(){
		return $this->data;
	}

	/**
	 *	Returns Singular of a Word.
	 *	@access		public
	 *	@param		string			$words			Word in Plural
	 *	@return		string
	 */
	protected function getSingular( $word ){
		$word	= preg_replace( '@ies$@', "y", $word );
		$word	= preg_replace( '@(([s|x|h])e)?s$@', "\\2", $word );
		return $word;
	}

	public function fromSerial( $serial ){
		$parts	= preg_split( '/:/', $serial, 2 );
		switch( $parts[0] ){
			case 'php':
				$this->format	= $parts[0];
				$this->data	= unserialize( $parts[1] );
				$this->serial	= $serial;
				break;
			case 'json':
				$this->format	= $parts[0];
				$this->data	= json_decode( $parts[1] );
				$this->serial	= $serial;
				break;
			case 'wddx':
				if( !function_exists( 'wddx_packet_start' ) )
					throw new \RuntimeException( 'WDDX is not supported' );
				$this->format	= $parts[0];
				$this->data		= wddx_deserialize( $parts[1] );
				$this->serial	= $serial;
				break;
			default:
				$this->data	= $serial;
		}
	}

	public function setData( $data ){
		$type	= gettype( $data );
		switch( $type ){
			case 'integer':
			case 'float':
			case 'real':
			case 'double':
			case 'string':
			case 'array':
//			case 'object':
			case NULL:
				$this->data	= $data;
				break;
/**/		case 'object':
				$this->data	= (array) $data;
				break;
			case 'object':
				throw new \Exception( 'StreamSocketResponse does not support Objects as content, yet' );
			default:
				throw new \Exception( 'Unsupported data type: '.$type );
		}
	}

	public function setFormat( $format ){
		$this->format = $format;
	}

	public function toSerial( $format = NULL ){
		$format	= $format ? $format : $this->format;
		switch( $format ){
			case 'json':
				return $format.':'.json_encode( $this->data, JSON_FORCE_OBJECT );
			case 'php':
				return $format.':'.serialize( $this->data );
			case 'wddx':
				if( !function_exists( 'wddx_packet_start' ) )
					throw new \RuntimeException( 'WDDX is not supported' );
				return $format.':'.wddx_serialize_value( $this->data );
			case 'xml':
				$root	= new \XML_Element( "<response/>" );
				$this->addArrayToXmlNode( $root, $this->data, "item" );
				$xml	= $root->asXml();
				$xml	= \XML_DOM_Formater::format( $xml );
				return $format.':'.$xml;
			default:
				return $format.':'.implode( "\n", $this->data );
		}
	}
}
?>
