<?php
namespace ufw\helper;

class escape extends base
{

    public function __invoke($msg)
    {
        return $this->escape($msg);
    }

    public function escape($msg)
    {
        if (is_array($msg)) {
            foreach ($msg as $key => $value) {
                $msg[$key] = $this->escape($value);
            }
            return $msg;
        } else {
            return htmlspecialchars($msg, ENT_QUOTES | ENT_HTML5);
        }
    }
}
