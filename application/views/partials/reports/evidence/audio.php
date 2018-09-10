<?php 
/**
 * @var \model\record\report $item
 * @var \model\record\evidence $evidence
 */

?>

<section class="evidence audio">
    <div class="item embed-responsive embed-responsive-16by9">
        <audio>
            <source src="<?php echo $evidence->url(); ?>" type="audio/mpeg">
        </audio>
        <a href="#" class="play-audio"><img src="/static/img/iconset/audio-transparent.svg" class="img-responsive"></a>
    </div>
    <div class="actions-overlay">
        <?php if ($evidence->hasMetadata()): ?>
        <a href="#" class="metadata">See metadata</a>
        <a href="#" class="metadata metadata-hide">Hide metadata</a>
        <?php endif; ?>
        <a href="<?php echo $evidence->downloadUrl(); ?>" class="download">Download <img src="/static/img/iconset/download.svg" height="24" width="24"></a>
    </div>
</section>