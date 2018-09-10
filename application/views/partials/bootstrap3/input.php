<?php 
/**
 * @var \form\element\text $element
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
    <?php echo $this->html5tag()->void('input', $element->getAttribs() + ['value' => $element->getValue()] + ($hasErrors ? ['aria-describedby' => $element->getId() . '-errors'] : [])); ?>
    <?php if ($hasSuccess): ?>
    <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
    <?php endif; ?>
    <?php if ($hasErrors): ?>
    <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
    <span id="<?php echo $element->getId(); ?>-errors" class="help-block"><?php echo join('<br>', $errors); ?></span>
    <?php endif; ?>
</div>
<?php
?>