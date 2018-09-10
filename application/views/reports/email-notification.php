<?php
/**
 * @var string $url
 * @var \model\record\report $report
 */
?>
<p>You’ve received a report from a Whistler user.</p>

<?php if ($report->isPublic()): ?>
    <p>This report is public, it is visible to anyone on <a href="https://whistlerapp.org/reports">whistlerapp.org/reports</a>.</p>
<?php else: ?>
    <p>This report is private, it is only visible to those who received the link below. Only share this link with people you trust.</p>
<?php endif; ?>

<p><strong>You can visualize your report at <?php echo $url; ?></strong></p>

<p>Whistler is a mobile app allowing activists, human rights defenders and citizen journalists to securely document and publicize human rights abuses. Each
    Whistler report includes data from the user’s device to make the evidence verifiable, including geolocation, elevation, luminosity, air pressure and the
    surrounding WiFi networks and cell towers. This set of data helps journalists and human rights organizations ensure the report was produced where and when
    the Whistler user claims it was and strengthen their advocacy efforts.</p>

<p>In order to better protect the privacy and security of our users, Whistler does not collect any other data than those included in the report. We do not know
    the identity of our users and cannot help you identify who shared this report.</p>

<p>
    If you have questions, comments or feedback on the report or Whistler, visit whistlerapp.org or contact us at <a href="mailto:contact@whistlerapp.org">contact@whistlerapp.org</a>.
</p>