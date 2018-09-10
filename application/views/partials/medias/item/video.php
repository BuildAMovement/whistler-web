<?php 
/**
 * @var \model\record\mediaFile $item
 */

?>

<section class="evidence video">
    <div class="item embed-responsive embed-responsive-16by9">
        <video class="embed-responsive-item" preload="none">
            <source src="<?php echo $item->url(); ?>" type="video/mp4">
        </video>
        <a href="#" class="play-video"><img src="/static/img/iconset/play.svg" class="img-responsive"></a>
    </div>
    <div class="actions-overlay">
        <span class="placeholder"></span>
        <a href="<?php echo $item->downloadUrl(); ?>" class="download">Download <img src="/static/img/iconset/download.svg" height="24" width="24"></a>
    </div>
</section>