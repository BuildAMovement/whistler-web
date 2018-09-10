<?php 
namespace ufw\helper;

class bootstrap3 extends base {
    
    public function __invoke() {
        return $this;
    }
    
    public function form(\ufw\form\base $form) {
        return $this->partial('bootstrap3/form.php', ['form' => $form]);
    }
    
    public function field(\ufw\form\element\base $element) {
        $script = 'input.php';
        if ($element instanceof \ufw\form\element\input) {
            $script = 'input.php';
        } elseif ($element instanceof \ufw\form\element\multi) {
            $script = 'multi-' . $element->getType() . '.php';
        } else {
            $script = (new \ReflectionClass($element))->getShortName() . '.php';
        }
        
        return $this->partial('bootstrap3/' . $script, ['element' => $element]);
    }
}

