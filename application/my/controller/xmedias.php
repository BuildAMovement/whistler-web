<?php
namespace controller;

class xmedias extends base
{

    public function indexAction()
    {
        $this->redirect('/');
    }

    public function itemAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        if (!$id || !preg_match('~^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$~i', $id)) {
            throw new \Exception('Not found', 404);
        }
        $item = (new \model\mediaFile())->one($id);
        if (!$item) {
            throw new \Exception("Not found", 404);
        }
        echo $this->render([
            'item' => $item
        ]);
    }

    public function downloadAction()
    {
        if (!($mediaFile = $this->checkAccess())) {
            throw new \Exception('Not found', 404);
        }
        
        $ffn = $mediaFile->getFfn();
        header('Content-Disposition: attachment; filename="' . rawurlencode(basename($ffn) . $mediaFile->fileExt) . '"');
        header('Cache-Control: private');
        header('Pragma: \"\"');
        $this->send($mediaFile, 'application/octet-stream');
    }

    public function fileAction()
    {
        if (!($mediaFile = $this->checkAccess())) {
            throw new \Exception('Not found', 404);
        }
        $mimeType = null;
        if ($mediaFile->hasSupportedExt()) {
            $mimes = new \Mimey\MimeTypes();
            $mimeType = $mimes->getMimeType(preg_replace('~^\.~', '', $mediaFile->fileExt));
        }
        if (!$mimeType) {
            $mimeType = 'application/octet-stream';
        }
        $this->send($mediaFile, $mimeType);
    }

    /**
     *
     * @throws \Exception
     * @return NULL|\model\record\mediaFile
     */
    protected function checkAccess()
    {
        try {
            $id = $this->getRequest()->getParam('id', null);
            if (!$id || !preg_match('~^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$~i', $id)) {
                throw new \Exception('Not found', 404);
            }
            
            $mediaFile = (new \model\mediaFile())->one($id);
            if (!$mediaFile) {
                throw new \Exception("Not found", 404);
            }
            
            if (!$mediaFile->isAllowed()) {
                throw new \Exception("Not found", 404);
            }
        } catch (\Exception $e) {
            return null;
        }
        return $mediaFile;
    }

    protected function send($mediaFile, $mimeType)
    {
        header("Content-Type: $mimeType");
        if (strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') === false) {
            header("X-Accel-Redirect: /protected/" . $mediaFile->uid);
        } elseif (!strcmp(APPLICATION_ENV, 'development')) {
            // local dev env on apache only
            header("X-Sendfile: " . $mediaFile->getFfn());
        }
        exit();
    }
}