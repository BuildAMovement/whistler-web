<?php
/**
 * @var \ufw\info_hash $items
 * @var int $page
 * @var int $perPage 
 * @var \model\record\report[] $reports
 * @var \controller\reports $this 
 */
$reports = & $items->info;

if ($this->user->isAdmin()) {
    $status = $this->getRequest()->getParam('status', \model\report::STATUS_APPROVED);
?>
    <div class="container">
        <div class="admins-only">
            <?php 
                if ($status === \model\report::STATUS_UNREVIEWED) echo "Currenty showing unreviewed reports.";
                elseif ($status == \model\report::STATUS_APPROVED) echo "Currenty showing approved reports.";
                elseif ($status == \model\report::STATUS_REJECTED) echo "Currenty showing rejected reports.";
                elseif (is_array($status) && $status == [\model\report::STATUS_UNREVIEWED, \model\report::STATUS_APPROVED]) {
                    echo "Currenty showing both approved and unreviewed reports.";
                }

                $params = ['controller' => 'reports', 'action' => 'index'];
            ?>
                Show list of
                <a href="<?php echo $this->url($params + ['status' => \model\report::STATUS_APPROVED]); ?>" class="admin-action">approved</a>,
                <a href="<?php echo $this->url($params + ['status' => \model\report::STATUS_UNREVIEWED]); ?>" class="admin-action">unreviewed</a>,
                <a href="<?php echo $this->url($params + ['status' => [\model\report::STATUS_UNREVIEWED, \model\report::STATUS_APPROVED]]); ?>" class="admin-action">approved or unreviwed</a>
                or
                <a href="<?php echo $this->url($params + ['status' => \model\report::STATUS_REJECTED]); ?>" class="admin-action">rejected</a>
                reports.
        </div>
    </div>
<?php
    }
?>

<div class="row reports">
    <div class="col-xs-12 col-md-10 col-md-push-1 text-left">
        <div class="row">
<?php
foreach ($reports as $item) {
?>
            <div class="col-xxs-12 col-xs-6 col-md-4 report-item-wrapper">
                <?php echo $this->partial('reports/list-item.php', ['item' => $item]); ?>
            </div>
<?php
}
?>
        </div>
    </div>
</div>

<div class="row reports">
    <div class="col-xs-12 col-md-10 col-md-push-1 text-center">
        <br>
<?php 
    echo $this->partial('paginator.php', ['count' => $items->getTotalCount(), 'page' => $page, 'perPage' => $perPage]);
?>
    </div>
</div>    