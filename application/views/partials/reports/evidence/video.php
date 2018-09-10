<?php 
/**
 * @var \model\record\report $item
 * @var \model\record\evidence $evidence
 */

?>

<section class="evidence video">
    <div class="item embed-responsive embed-responsive-16by9">
        <video class="embed-responsive-item" preload="none">
            <source src="<?php echo $evidence->url(); ?>" type="video/mp4">
        </video>
        <a href="#" class="play-video"><img src="/static/img/iconset/play.svg" class="img-responsive"></a>
    </div>
    <div class="actions-overlay">
        <?php if ($evidence->hasMetadata()): ?>
        <a href="#" class="metadata">See metadata</a>
        <a href="#" class="metadata metadata-hide">Hide metadata</a>
        <?php endif; ?>
        <a href="<?php echo $evidence->downloadUrl(); ?>" class="download">Download <img src="/static/img/iconset/download.svg" height="24" width="24"></a>
    </div>
</section>