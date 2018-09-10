<?php 
namespace form;

class adminReport extends \ufw\form\base {

    /**
     * 
     * @var \model\record\report $report
     */
    protected $report = null;
    
    public function init() {
        
        $element = new \ufw\form\element\radio('status', [
            'multiOptions' => [
                \model\report::STATUS_APPROVED => 'approved',
                \model\report::STATUS_REJECTED => 'rejected',
                \model\report::STATUS_UNREVIEWED => 'unreviewed',
            ],
            'value' => $this->getReport()->status,
            'required' => true,
        ]);
        $this->addElement($element);
        
        $multiOptions = array();
        foreach ($this->getReport()->getEmailRecipients() as $recipient) {
            $multiOptions[$recipient['email']] = ($recipient['title'] ? $recipient['title'] . ' ' : '') . '<' . $recipient['email'] . '>';
        }
        
        if ($multiOptions) {
            $element = new \ufw\form\element\checkboxset('emails', [
                'label' => 'Choose email recipients (emails would be sent only if you are approving the report)',
                'multiOptions' => $multiOptions,
                'value' => array_keys($multiOptions),
            ]);
            $this->addElement($element);
        }
        
        $element = new \ufw\form\element\button('submit', [
            'type' => 'submit',
            'class' => 'btn btn-primary',
            'caption' => 'Change status',
            'nameWithinAttribs' => false,
        ]);
        $this->addElement($element);
        
        $this->setAction($this->getHelper('url')->url(['controller' => 'reports', 'action' => 'change-status', 'id' => $this->getReport()->getId()]));
    }

    /**
     *
     * @return \model\record\report
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     *
     * @param $report
     */
    public function setReport(\model\record\report $report = null)
    {
        $this->report = $report;
        return $this;
    }
 

 
 
    
    
}