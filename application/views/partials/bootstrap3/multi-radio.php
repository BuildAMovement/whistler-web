<?php 
/**
 * @var \form\element\radio $element
 * @var \helper\render $this
 */

    $errors = $element->getErrors();
    $hasErrors = !!$errors;
    $hasSuccess = strlen($element->getValue()) && !$hasErrors && $element->getForm()->getValidated();
    $ord = 1;
?>
<div class="form-group <?php echo $element->isRequired() ? 'required ' : '', $hasSuccess ? 'has-success ' : '', $hasErrors ? 'has-error ' : '', $hasErrors || $hasSuccess ? 'has-feedback ' : ''; ?>">
    <?php if ($element->getLabel()): ?>
    <label class="control-label"><?php echo $element->getLabel(); ?></label>
    <?php endif;?>
    
    <?php foreach ($element->getMultiOptions() as $optKey => $optValue): ?>
    <div class="radio">
        <label>
            <?php echo $this->html5tag()->void('input', ['value' => $optKey, 'id' => $element->getId() . '-' . $ord++, 'checked' => !strcmp($element->getValue(), $optKey)] + $element->getAttribs()); ?>
            <?php echo $this->escape($optValue); ?>
        </label>
    </div>
    <?php endforeach; ?>
    
    <?php if ($hasErrors): ?>
    <span id="<?php echo $element->getId(); ?>-errors" class="help-block"><?php echo join('<br>', $errors); ?></span>
    <?php endif; ?>
</div>
