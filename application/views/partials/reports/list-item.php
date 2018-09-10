<?php
/**
 * @var \model\record\report $item
 * @var \controller\base $this
 */

?>

<article class="report-item">
    <a href="<?php echo $item->url(); ?>" class="wrap">
        <div class="image"><img src="<?php echo $item->getCoverImage(); ?>" class="img-responsive"></div>
        <div class="content-icon"><img src="<?php echo $item->getContentIcon(); ?>" class="img-responsive"></div>
        <h2><?php echo $this->escape($item->getTitle()); ?></h2>
        <h3><?php echo $this->escape($item->getLocation()); ?></h3>
        <time datetime="<?php echo date('r', $item->getTs()); ?>"><?php echo date('n/j/Y', $item->getTs()); ?></time>
    </a>
</article>
<?php 
    if ($this->user->isAdmin()) {
?>
    <div class="admins-only text-left" style="top: -1rem;">
        Report status is
        <?php 
        if ($item->status == \model\report::STATUS_UNREVIEWED) echo "unreviewed";
            elseif ($item->status == \model\report::STATUS_APPROVED) echo "approved";
            elseif ($item->status == \model\report::STATUS_REJECTED) echo "rejected";
        ?>
    </div>
<?php 
    }
?>
