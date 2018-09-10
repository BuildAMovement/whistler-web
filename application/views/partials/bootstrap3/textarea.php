<?php 
/**
 * @var \form\element\textarea $element
 * @var \helper\render $this
 */

    $errors = $element->getErrors();
    $hasErrors = !!$errors;
    $hasSuccess = strlen($element->getValue()) && !$hasErrors && $element->getForm()->getValidated(); 
?>
<div class="form-group <?php echo $element->isRequired() ? 'required ' : '', $hasSuccess ? 'has-success ' : '', $hasErrors ? 'has-error ' : '', $hasErrors || $hasSuccess ? 'has-feedback ' : ''; ?>">
    <?php if ($element->getLabel()): ?>
        <label for="<?php echo $element->getId(); ?>" class="control-label"><?php echo $element->getLabel(); ?></label>
    <?php endif; ?>
    <?php echo $this->html5tag()->full('textarea', $element->getAttribs(), $element->getValue()); ?>
</div>
