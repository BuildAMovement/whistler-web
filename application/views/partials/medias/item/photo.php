<?php 
/**
 * @var \model\record\mediaFile $item
 */
?>

<section class="evidence photo">
    <div class="item embed-responsive embed-responsive-16by9">
        <img src="<?php echo $item->url(); ?>" class="embed-responsive-item cover">
    </div>
    <div class="actions-overlay">
        <a href="#" class="fullscreen"><img src="/static/img/iconset/fullscreen.svg" width="24" height="24"></a>
        <span class="placeholder"></span>
        <a href="<?php echo $item->downloadUrl(); ?>" class="download">Download <img src="/static/img/iconset/download.svg" height="24" width="24"></a>
    </div>
</section>