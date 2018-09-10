<?php
namespace controller;

class page extends base
{

    public function init()
    {
        $this->setLayout('page');
    }

    public function indexAction()
    {
        $this->setLayout('home');
        \ufw\registry::getInstance()->set('bodyCssClass', 'home whistler-bg');
        echo $this->render();
    }

    public function aboutAction()
    {
        echo $this->render();
    }

    public function contactAction()
    {
        $form = new \form\contact();
        $mailSent = false;
        
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $form->populate($post);

            if ($form->isValid()) {
                $post = $form->getValues();
                try {
                    $transport = \Swift_SendmailTransport::newInstance();
                    $mailer = \Swift_Mailer::newInstance($transport);
                    
                    $htmlContent = 
                        '<p>From: ' . $this->escape($form->getElement('name')->getValue()) . ' &lt;' . $this->escape($form->getElement('email')->getValue()) . '&gt;</p>' . "\n" . 
                        '<p>Subject: ' . $this->escape($form->getElement('subject')->getValue()) . '</p>' . "\n" . 
                        '<hr>' . "\n" . 
                        '<p>' . nl2br($this->escape($form->getElement('message')->getValue())) . '</p>';
                    $message = \Swift_Message::newInstance()->setSubject("Whistlerapp.org Contact Form")
                        ->setFrom('noreply@whistlerapp.org')
                        ->setTo('contact@whistlerapp.org')
                        ->setBcc('whistlerapp.org@gmail.com')
                        ->setBody(strip_tags($htmlContent))
                        ->addPart($htmlContent, 'text/html');
                    $mailSent = $mailer->send($message);
                } catch (\Swift_SwiftException $e) {
                    $mailSent = false;
                }
                
                if ($mailSent) {
                    $this->flashMessenger()->addMessage('Email sent successfully.', 'info');
                    $this->redirect($this->url([], false));
                } else {
                    $this->flashMessenger()->addMessage('Our server got in troubles trying to send the email. Please try again later.', 'error');
                }
            } else {}
        }
        
        echo $this->render([
            'form' => $form,
            'mailSent' => $mailSent
        ]);
    }

    public function downloadAction()
    {
        echo $this->render();
    }

    public function faqAction()
    {
        \ufw\registry::getInstance()->set('bodyCssClass', 'whistler-bg');
        echo $this->render();
    }
    
    public function supportUsAction() {
        echo $this->render();
    }
}