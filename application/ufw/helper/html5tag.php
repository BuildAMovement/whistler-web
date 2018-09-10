<?php 
namespace ufw\helper;

class html5tag extends base {

    protected $heap = array();
    
    public function __invoke() {
        return $this;
    }
        
    public function open($tag, $attribs) {
        array_push($this->heap, $tag);
        return '<' . $tag . ($attribs ? ' ' . $this->html5attribs($attribs) : '') . '>';
    }
    
    public function selfClosing($tag, $attribs) {
        return '<' . $tag . ($attribs ? ' ' . $this->html5attribs($attribs) : '') . ' />';
    }
    
    public function void($tag, $attribs) {
        return '<' . $tag . ($attribs ? ' ' . $this->html5attribs($attribs) : '') . '>';
    }
    
    public function close() {
        $tag = array_pop($this->heap);
        return '</' . $tag . '>';
    }
    
    public function full($tag, $attribs, $content) {
        return $this->open($tag, $attribs) . $content . $this->close();
    }
}