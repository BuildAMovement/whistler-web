<?php
namespace ufw\model\behaviour;

class json extends base
{

    public $serialAttributes = array();

    public function beforeDataSet($data)
    {
        if (count($this->serialAttributes)) {
            foreach ($this->serialAttributes as $attribute) {
                $_att = $data[$attribute];
                if (!empty($_att) && is_scalar($_att)) {
                    $a = @json_decode($_att, true);
                    if ($a !== false) {
                        $data[$attribute] = $a;
                    } else {
                        $data[$attribute] = array();
                    }
                }
            }
        }
        return $data;
    }
}