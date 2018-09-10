<?php
/**
 * @var string $url
 * @var \model\record\report $report
 */
?>
<p>There is new report from a Whistler user that need to be approved.</p>

<p><strong>You can visualize your report at <?php echo $url; ?></strong></p>

<?php if ($report->isPublic()): ?>
    <p>This report is public, it would be visible to anyone on <a href="https://whistlerapp.org/reports">whistlerapp.org/reports</a>.</p>
<?php else: ?>
    <p>This report is private, it is only visible to those who received the link below. Only share this link with people you trust.</p>
<?php endif; ?>

