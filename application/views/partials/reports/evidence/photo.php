<?php 
/**
 * @var \model\record\report $item
 * @var \model\record\evidence $evidence
 */

?>

<section class="evidence photo">
    <div class="item embed-responsive embed-responsive-16by9">
        <img src="<?php echo $evidence->url(); ?>" class="embed-responsive-item cover">
    </div>
    <div class="actions-overlay">
        <a href="#" class="fullscreen"><img src="/static/img/iconset/fullscreen.svg" width="24" height="24"></a>
        <?php if ($evidence->hasMetadata()): ?>
        <a href="#" class="metadata">See metadata</a>
        <a href="#" class="metadata metadata-hide">Hide metadata</a>
        <?php endif; ?>
        <a href="<?php echo $evidence->downloadUrl(); ?>" class="download">Download <img src="/static/img/iconset/download.svg" height="24" width="24"></a>
    </div>
</section>