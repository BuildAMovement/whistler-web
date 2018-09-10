<?php 
/**
 * @var \ufw\form\base $form
 * @var \helper\render $this
 */
?>

<?php echo $this->html5tag()->open('form', $form->getAttribs()); ?>
    <?php foreach ($form->getElements() as $element): ?>
        <?php echo $element; ?>
    <?php endforeach; ?>
<?php echo $this->html5tag()->close(); ?>
