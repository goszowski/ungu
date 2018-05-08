<?

/**
 * xmlElement class definition
 */
require_once ("base/xmlElement.php");

/**
 * I need to plase this variables here, becose php's xml parser don't understand variables inside object,
 * which is gived by xml_set_object( $this->parser, &$this );
 * You may think, that this is an inner variables from XmlReader class
 */
$_queue = array();
$_depth = 0;

/**
 * Reads XML documents into a xmlElment object structure.
 *
 * Structure is : a root element that contains all others elements
 * @package base
 */
class XmlReader extends PHPObject {
    /**
     * Xml parser resource
     * @var parser resource
     */
    var $parser = null;

    /**
     * Parses a given XML file and returns the data DOM.
     * 
     * @param xmlcode string xml code to be parsed
     * @access public
     */ 
    function parse($xmlcode) {
    	global $_queue, $_depth;
    	$_queue = array();
    	$_depth = 0;

    	if (!xml_parse($this->parser, $xmlcode, true)) {
    		echo "\n\n<br>Xml parser error :<br>";
    		echo  "Error string: ".xml_error_string( xml_get_error_code ($this->parser))."<br>";
    		echo "Line: ".xml_get_current_line_number($this->parser)."<br>";
    		echo "Column: ".xml_get_current_column_number($this->parser)."<br>";
    		echo "Byte index: ".xml_get_current_byte_index($this->parser)."<br>";
    		echo "\n\n\n".htmlspecialchars($xmlcode);

    		die();
    	}

    	return $_queue[0];
    }

    /**
     * Constructor.
     * 
     * Creates xml parser and prepares it to parse any text using startElement,endElement,characterData
     * functions of this object
     */
    function XmlReader() {
    	//$this->super();
    	$this->parser = xml_parser_create();
    	xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
    	xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE , 1);

    	xml_set_object ( $this->parser, &$this );
    	xml_set_element_handler ( $this->parser, "startElement", "endElement" );
    	xml_set_character_data_handler ( $this->parser, "characterData" );
    }

    /**
     * When parsing, calls at element start tag
     * @param parser resource
     * @param name string tag name
     * @param attribs array hash of tag attributes ( name=>value )
     * @access private
     * @return void
     */
    function startElement($parser, $name, $attribs) {
    	global $_queue, $_depth;
    	$_queue[$_depth] = new xmlElement;
    	$_queue[$_depth]->tagname = $name;
    	$_queue[$_depth]->attributes = $attribs;

    	$_depth++;
    }

    /**
     * When parsing, calls at element end tag
     * @param parser resource
     * @param name string tag name
     * @access private
     * @return void
     */
    function endElement($parser, $name) {
    	global $_queue, $_depth;

    	$_depth--;

    	if($_depth != 0) {
    		$_queue[$_depth-1]->content[] = $_queue[$_depth];
    	}
    }

    /**
     * When parsing, calls at char. data in tag starts
     * @param parser resource
     * @param data string char. data in tag
     * @access private
     * @return void
     */
    function characterData($parser, $data) {
    	global $_queue, $_depth;
    	$cdata = &$_queue[$_depth-1]->cdata;
    	$cdata .= $data;
    	$cdata = rtrim($cdata);
    }

    /**
     * Destructor
     * @access private
     * @return void
     */
    function _XmlReader () {
    	xml_parser_free($this->parser);
    }

    /**
     * Parses file with goven filename
     * @param string filename Name of the xml document
     * @access public
     * @see parse()
     */
    function parseFile ($filename) {
    	return $this->parse(join("",file($filename)));
    }

    /**
    * Replaces some basic entities with their character counterparts.
    * 
    * @param    string  String to decode
    * @return   string  Decoded string
    */
    function xmldecode($value) {
    	#return preg_replace( array("@&lt;@", "@&gt;@", "@&apos;@", "@&quot;@", "@&amp;@"), array("<", ">", "'", '"', "&"), $value);
    	return utf8_decode(preg_replace( array("@&lt;@", "@&gt;@", "@&apos;@", "@&quot;@", "@&amp;@"), array("<", ">", "'", '"', "&"), $value));
    } // end func xmldecode
}

?>