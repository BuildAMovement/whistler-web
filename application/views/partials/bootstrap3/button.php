<?php 
/**
 * @var \form\element\button $element
 * @var \helper\render $this
 */

?>
<div class="form-group">
    <?php echo $this->html5tag()->full('button', $element->getAttribs(), $element->getCaption()); ?>
</div>
