<?php
class PARSER{
  var $_xmlParser;
  var $_fies = [];
  var $_col;
  var $_data;

  function _start($parser, $tag, $v){
    switch($tag){
      case 'error':
        if($v[code] != 0) exit('Web publishing Error: Receive a script error from the server.');
      break;
      case 'field':
        $this->_col = $v[name];
        $this->_fies[$this->_col] = null;
      break;
      case 'data':
        $this->_data = '';
      break;
    }
  }
  function _end($parser, $tag){
    switch($tag){
      case 'data':
        $this->_fies[$this->_col] = $this->_data;
      break;
    }
  }
  function _cdata($parser, $v){
    $this->_data .= $v;
  }

  function parse($XML){
    if(empty($XML)) exit('Error: Did not receive an XML document from the server.');
    $this->_xmlParser = xml_parser_create('UTF-8');
    xml_set_object($this->_xmlParser, $this);
    xml_parser_set_option($this->_xmlParser, XML_OPTION_CASE_FOLDING, FALSE);
    xml_parser_set_option($this->_xmlParser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
    xml_set_element_handler($this->_xmlParser, '_start', '_end');
    xml_set_character_data_handler($this->_xmlParser, '_cdata');
    if(!@xml_parse($this->_xmlParser, $XML)){
      printf(
        'XML Parse Error: %s at line %d',
        xml_error_string(xml_get_error_code($this->_xmlParser)),
        xml_get_current_line_number($this->_xmlParser)
      );
      exit();
    }
    xml_parser_free($this->_xmlParser);
    return TRUE;
  }
}

class API{
  function __construct($host = NULL, $file = NULL, $acc = NULL, $pas = NULL){
    $this->_props['host'] = $host;
    $this->_props['file'] = $file;
    $this->_props['acc'] = $acc;
    $this->_props['pas'] = $pas;
  }

  function _execute($_QUERYS){
    if(!function_exists('curl_init')) exit('Error: cURL is required to use this API.');
    $arr = [];
    foreach($_QUERYS as $key => $v){
      $arr[] = urlencode($key).($v === TRUE ? '' : '='.urlencode($v));
    }
    $_HOST = $this->_props['host'];
    $_HOST .= (substr($_HOST, -1, 1) === '/' ? '' : '/') . 'fmi/xml/fmresultset.xml';
    $_CH = curl_init($_HOST);
    curl_setopt($_CH, CURLOPT_POST, TRUE);
    curl_setopt($_CH, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($_CH, CURLOPT_FAILONERROR, TRUE);
    $_bAbL = FALSE;
    if(!headers_sent()){
      $_bAbL = TRUE;
      curl_setopt($_CH, CURLOPT_HEADER, TRUE);
    }
    $optHead = [];
    $optHead[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
    $optHead[] = 'X-FMI-PE-ExtendedPrivilege: IrG6U+Rx0F5bLIQCUb9gOw==';//for ".fmp12"
            // = 'X-FMI-PE-ExtendedPrivilege: tU+xR2RSsdk=';//for ".fp7"
    if($this->_props['acc'])
      $optHead[] = 'Authorization: Basic '. base64_encode(utf8_decode($this->_props['acc']).':'.$this->_props['pas']);
    curl_setopt($_CH, CURLOPT_HTTPHEADER, $optHead);
    curl_setopt($_CH, CURLOPT_POSTFIELDS, implode('&', $arr));
    
    $_OUTPUT = curl_exec($_CH);
    if($_bAbL){
      $_pos = strpos($_OUTPUT, '<?xml');
      $_OUTPUT = ($_pos !== false ? substr($_OUTPUT, $_pos) : $_OUTPUT);
    }
    if($_CERR = curl_errno($_CH)){
      echo 'Communication Error: ('.$_CERR.') '.curl_error($_CH);
      if($_CERR == 52){
        echo ' - The Web Publishing Core and/or FileMaker Server services are not running.';
      }else 
      if($_CERR == 22){
        if(stristr('50', curl_error($_CH))){
          echo ' - The Web Publishing Core and/or FileMaker Server services are not running.';
        }else{
          echo ' - This can be due to an invalid username or password, or if the FMPHP privilege is not enabled for that user.';
        }
      }
      curl_close($_CH);
      exit();
    }
    curl_close($_CH);
    return $_OUTPUT;
  }

  function Perform($script, $param = null){
    $_QUERYS = [
      '-db' => $this->_props['file'],
      '-lay' => 'Sys',
      '-script.prefind' => $script,
      '-script.prefind.param' => $param,
      '-findany' => TRUE
    ];
    $XML = $this->_execute($_QUERYS);
    $parser = new PARSER($this);
    $parser->parse($XML);
    return $parser->_fies;
  }
}