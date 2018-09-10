<?php
/**
 * 
 * @var \model\record\report $report
 * @var \helper\render $this
 */
    $status = $report->status;
    $form = new \form\adminReport(['report' => $report]);
?>
<div class="container">
    <div class="admins-only">
        <?php echo $this->html5tag()->open('form', $form->getAttribs()); ?>
        <?php echo $form->csrf_token; ?>
        <div class="row">
            <div class="col-xs-12">
                Current status of the report is <strong> 
                <?php 
                    if ($status == \model\report::STATUS_UNREVIEWED) echo "unreviewed";
                    elseif ($status == \model\report::STATUS_APPROVED) echo "approved";
                    elseif ($status == \model\report::STATUS_REJECTED) echo "rejected";
                ?></strong>.
                <br><br>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <?php echo $form->status; ?>
            </div>
            <div class="col-xs-8">
                <?php echo $form->emails; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <?php echo $form->submit; ?>
            </div>
        </div>
        <?php echo $this->html5tag()->close(); ?>
    </div>
</div>
