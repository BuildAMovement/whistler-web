<?php
namespace controller;

class reports extends base
{

    const PER_PAGE = 24;

    public function init()
    {
        if ($this->getRequest()->getParam('askLogin') && !$_COOKIE['logged']) {
            $proceedTo = $this->url([], false);
            $this->redirect($this->url([
                'controller' => 'user',
                'action' => 'login'
            ]) . '?proceed_to=' . urlencode($proceedTo));
        }
    }

    public function changeStatusAction()
    {
        if (!$this->user->isAdmin()) {
            $this->redirect('/');
        }
        
        $id = $this->getRequest()->getParam('id', null);
        if (!$id || !preg_match('~^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$~i', $id)) {
            throw new \Exception('Not found', 404);
        }
        $item = (new \model\report())->one($id);
        if (!$item) {
            throw new \Exception("Not found", 404);
        }
        
        if ($this->getRequest()->isPost()) {
            $form = new \form\adminReport([
                'report' => $item
            ]);
            $post = $this->getRequest()->getPost();
            $form->populate($post);
            if ($form->isValid()) {
                $post = $form->getValues();
                (new \model\report())->changeStatus($id, $post['status']);
                $rowsAffected = \db\db::instance()->rows_affected;
                if ($rowsAffected) {
                    $this->flashMessenger()->addMessage('Report status changed.', 'info');
                }
                if (($post['status'] == \model\report::STATUS_APPROVED) && $rowsAffected) {
                    if ($post['emails']) {
                        try {
                            $failedEmailAddresses = $this->sendNotificationEmails($item, $post['emails']);
                            if ($failedEmailAddresses) {
                                $this->flashMessenger()->addMessage('Email delivery failed to following address(es):<br>' . join('<br>', $failedEmailAddresses), 'error');
                            } else {
                                $this->flashMessenger()->addMessage('Email(s) sent successfully.', 'info');
                            }
                        } catch (\Swift_SwiftException $e) {
                            $this->flashMessenger()->addMessage($e->getMessage(), 'error');
                        }
                    }
                }
            } else {
                $this->formErrorsToFlashMessenger($form);
            }
        }
        $this->redirect($this->url([
            'action' => 'item'
        ], false));
    }

    public function indexAction()
    {
        $items = (new \model\report())->recent($this->getRequest()
            ->getParam('status', null), $this->getRequest()
            ->getParam('page', 1), static::PER_PAGE);
        echo $this->render([
            'items' => $items,
            'page' => $this->getRequest()
                ->getParam('page', 1),
            'perPage' => static::PER_PAGE
        ]);
    }

    public function itemAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        if (!$id || !preg_match('~^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$~i', $id)) {
            throw new \Exception('Not found', 404);
        }
        $item = (new \model\report())->one($id);
        if (!$item) {
            throw new \Exception("Not found", 404);
        }
        echo $this->render([
            'item' => $item
        ]);
        
        // @todo: remove following block - temporary solution
        if (!$item->mailsent && in_array($item->status, [
            \model\report::STATUS_APPROVED,
            \model\report::STATUS_UNREVIEWED
        ])) {
            (new \model\report())->mailsent($id);
            $emails = [];
            foreach ($item->getEmailRecipients() as $reportRecipient) {
                $emails[] = trim($reportRecipient['email']);
            }
            try {
                $this->sendNotificationEmails($item, $emails);
            } catch (\Swift_SwiftException $e) {}
        }
    }

    public function adminNotificationAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        if (!$id || !preg_match('~^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$~i', $id)) {
            throw new \Exception('Not found', 404);
        }
        $item = (new \model\report())->one($id);
        if (!$item) {
            throw new \Exception("Not found", 404);
        }
        if (!$item->admin_notification_sent && in_array($item->status, [
            \model\report::STATUS_UNREVIEWED
        ])) {
            (new \model\report())->adminNotificationSent($id);
            $emails = [
                'office@buildamovement.org'
            ];
            try {
                $this->sendAdminNotificationEmails($item, $emails);
            } catch (\Swift_SwiftException $e) {}
        }
    }

    public function downloadAction()
    {
        if (!($evidence = $this->checkAccess())) {
            throw new \Exception('Not found', 404);
        }
        
        $ffn = $evidence->getFfn();
        header('Content-Disposition: attachment; filename="' . rawurlencode(basename($ffn) . $evidence->fileExt) . '"');
        header('Cache-Control: private');
        header('Pragma: \"\"');
        $this->send($evidence, 'application/octet-stream');
    }

    public function evidenceAction()
    {
        if (!($evidence = $this->checkAccess())) {
            throw new \Exception('Not found', 404);
        }
        $mimeType = null;
        if ($evidence->hasSupportedExt()) {
            $mimes = new \Mimey\MimeTypes();
            $mimeType = $mimes->getMimeType(preg_replace('~^\.~', '', $evidence->fileExt));
        }
        if (!$mimeType) {
            $mimeType = 'application/octet-stream';
        }
        $this->send($evidence, $mimeType);
    }

    /**
     *
     * @throws \Exception
     * @return NULL|\model\record\evidence
     */
    protected function checkAccess()
    {
        try {
            $id = $this->getRequest()->getParam('id', null);
            if (!$id || !preg_match('~^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$~i', $id)) {
                throw new \Exception('Not found', 404);
            }
            
            $evidence = (new \model\evidence())->one($id);
            if (!$evidence) {
                throw new \Exception("Not found", 404);
            }
            
            if (!$evidence->isAllowed()) {
                throw new \Exception("Not found", 404);
            }
        } catch (\Exception $e) {
            return null;
        }
        return $evidence;
    }

    protected function send($evidence, $mimeType)
    {
        header("Content-Type: $mimeType");
        if (strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') === false) {
            header("X-Accel-Redirect: /protected/" . $evidence->uid);
        } elseif (!strcmp(APPLICATION_ENV, 'development')) {
            // local dev env on apache only 
            header("X-Sendfile: " . $evidence->getFfn());
        }
        exit();
    }

    protected function sendNotificationEmails(\model\record\report $item, $emails)
    {
        $transport = \Swift_SendmailTransport::newInstance();
        $mailer = \Swift_Mailer::newInstance($transport);
        
        $subject = 'You\'ve received a Whistler report';
        $htmlContent = $this->render([
            'url' => $this->getHelper('url')
                ->full([
                'action' => 'item'
            ], false),
            'report' => $item
        ], 'email-notification.php', true);
        
        $failedEmailAddresses = array();
        $reportRecipients = $item->getEmailRecipients();
        foreach ($reportRecipients as $reportRecipient) {
            if (!in_array($reportRecipient['email'], $emails)) {
                continue;
            }
            $message = \Swift_Message::newInstance()->setSubject($subject)->setFrom('report@whistlerapp.org', "Whistler Report");
            
            $message->setTo($reportRecipient['email'], $reportRecipient['title'])
                ->setBcc('whistlerapp.org@gmail.com')
                ->addPart($htmlContent, 'text/html');
            
            $mailer->send($message, $failedEmailAddresses);
        }
        
        return $failedEmailAddresses;
    }

    protected function sendAdminNotificationEmails(\model\record\report $item, $emails)
    {
        $transport = \Swift_SendmailTransport::newInstance();
        $mailer = \Swift_Mailer::newInstance($transport);
        
        $subject = 'There\'s new Whistler report to approve';
        $htmlContent = $this->render([
            'url' => $this->getHelper('url')
                ->full([
                'action' => 'item'
            ], false) . '?askLogin=1',
            'report' => $item
        ], 'email-admin-notification.php', true);
        
        $failedEmailAddresses = array();
        foreach ($emails as $email) {
            $message = \Swift_Message::newInstance()->setSubject($subject)->setFrom('report@whistlerapp.org', "Whistler Report");
            
            $message->setTo($email)
                ->setBcc('whistlerapp.org@gmail.com')
                ->addPart($htmlContent, 'text/html');
            
            $mailer->send($message, $failedEmailAddresses);
        }
        
        return $failedEmailAddresses;
    }
}