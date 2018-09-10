<?php 
/**
 * @var \model\record\report $item
 * @var \model\record\evidence $evidence
 */

?>

<section class="evidence photo in-progress">
    <div class="item embed-responsive embed-responsive-16by9">
        <img src="<?php echo $evidence->coverImageUrl(); ?>" class="embed-responsive-item cover">
    </div>
    <div class="actions-overlay">
        <a href="#" class="metadata">See metadata</a>
        <a href="#" class="metadata metadata-hide">Hide metadata</a>
    </div>
</section>