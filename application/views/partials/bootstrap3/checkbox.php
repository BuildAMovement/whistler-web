<?php 
/**
 * @var \form\element\checkbox $element
 * @var \helper\render $this
 */

    $errors = $element->getErrors();
    $hasErrors = !!$errors;
    $hasSuccess = strlen($element->getValue()) && !$hasErrors && $element->getForm()->getValidated();
?>
<div class="form-group <?php echo $element->isRequired() ? 'required ' : '', $hasSuccess ? 'has-success ' : '', $hasErrors ? 'has-error ' : '', $hasErrors || $hasSuccess ? 'has-feedback ' : ''; ?>">
    <div class="checkbox">
        <?php echo $this->html5tag()->void('input', ['name' => $element->getName(), 'id' => $element->getId() . '-pseudo', 'value' => $element->getUncheckedValue(), 'type' => 'hidden']); ?>
        <label>
            <?php echo $this->html5tag()->void('input', $element->getAttribs() + ['value' => $element->getCheckedValue(), 'checked' => $element->getChecked()]); ?>
            <?php echo $element->getLabel(); ?>
        </label>
    </div>
</div>