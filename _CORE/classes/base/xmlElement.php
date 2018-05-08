<?

/**
 * XML element abstraction
 */
class xmlElement extends PHPObject {
    /**
     * The name of tag
     * @access public
     * @var tagname string
     */
    var $tagname = "";
    /**
     * List of attributes ( hash $name=>value )
     * @access public
     * @var attributes array
     */
    var $attributes = array();
    /**
     * List of included elements
     * @access public
     * @var content array
     */
    var $content = array();
    /**
     * Character data in element
     * @access public
     * @var cdata string
     */
    var $cdata = "";

    function xmlElement($tagname="") {
    	$this->tagname = $tagname;
    }

    function getXML() {
    	return "<?xml version='1.0' encoding='windows-1251'?>\n".$this->getCode();
    }

    function getCode() {
    	$ret = "<$this->tagname";

    	foreach($this->attributes as $name=>$val){
    		$ret .= " $name=\"$val\"";
    	}

    	$ret .= ">";

    	if ( sizeof ( $this->content ) != 0 ) {
    		$ret .= "\n";
    	}

    	foreach($this->content as $el){
    		$ret .= $el->getCode();
    	}

    	$ret .= $this->cdata."</$this->tagname>\n";

    	return $ret;
    }

    function addNode($xmlEl) {
    	assert('!( !is_object($xmlEl) || !(is_subclass_of ($xmlEl, "PHPObject")) || !($xmlEl->className() != "xmlElement"))');
    	$this->content[] = $xmlEl;
    }

    function setAttribute($attname, $val) {
    	$this->attributes[$attname] = htmlspecialchars($val);
    }
}

?>