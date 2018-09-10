<?php 
/**
 * @var \model\record\mediaFile $item
 */

?>

<section class="evidence audio">
    <div class="item embed-responsive embed-responsive-16by9">
        <audio>
            <source src="<?php echo $item->url(); ?>" type="audio/mpeg">
        </audio>
        <a href="#" class="play-audio"><img src="/static/img/iconset/audio-transparent.svg" class="img-responsive"></a>
    </div>
    <div class="actions-overlay">
        <span class="placeholder"></span>
        <a href="<?php echo $item->downloadUrl(); ?>" class="download">Download <img src="/static/img/iconset/download.svg" height="24" width="24"></a>
    </div>
</section>