<?php 
namespace ufw\helper;

class html5attribs extends base {
    
    public function __invoke($attribs) {
        return $this->html5attribs($attribs);
    }
    
    public function html5attribs($attribs) {
        $out = [];
        foreach ($attribs as $key => $value) {
            if (is_bool($value)) {
                if ($value) $out[] = "$key";
            } else {
                $out[] = "$key=\"" . htmlspecialchars($value, ENT_COMPAT) . "\"";
            }
        }
        return join(' ', $out);
    }
}