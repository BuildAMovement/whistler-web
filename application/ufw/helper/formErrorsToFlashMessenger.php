<?php
namespace ufw\helper;

class formErrorsToFlashMessenger extends base
{

    public function __invoke(\ufw\form\base $form = null)
    {
        if (!$form || !$form->getErrors()) {
            return;
        }
        foreach ($form->getErrors() as $key => $value) {
            $element = $form->getElement($key);
            $label = $element->getLabel() ? $element->getLabel() : $element->getName();
            $this->flashMessenger()->addMessage(($label ? $label . ': ' : '') . join('<br>', $this->escape($value)), 'error');
        }
    }
}
