<?php 
namespace form;

class contact extends \ufw\form\base {
    
    public function init() {
        $element = new \ufw\form\element\text('name', [
            'label' => 'Name',
            'placeholder' => 'Jane Doe',
            'required' => true,
            'maxlength' => 160,
        ]);
        $element->addValidator('stringLength', array('options' => ['160']));
        $this->addElement($element);
        
        $element = new \ufw\form\element\email('email', [
            'label' => 'Email address',
            'placeholder' => 'jane.doe@example.com',
            'required' => true,
            'maxlength' => 160,
        ]);
        $element->addValidator('stringLength', array('options' => ['160']));
        $this->addElement($element);
        
        $element = new \ufw\form\element\text('subject', [
            'label' => 'Subject',
            'placeholder' => ' ',
            'required' => true,
            'maxlength' => 255,
        ]);
        $element->addValidator('stringLength', array('options' => ['255']));
        $this->addElement($element);
        
        $element = new \ufw\form\element\textarea('message', [
            'label' => 'Message',
            'placeholder' => '',
            'required' => true,
            'rows' => 5,
        ]);
        $this->addElement($element);
        
        $element = new \ufw\form\element\button('submit', [
            'caption' => 'Send message',
            'type' => 'submit',
            'class' => 'btn btn-primary',
        ]);
        $this->addElement($element);
    }
}