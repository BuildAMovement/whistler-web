<?php
namespace controller;

class download extends base
{

    public function indexAction()
    {
        $this->redirect($this->url(['action' => 'android'], false));
    }
    
    public function androidAction() {
        $this->redirect('https://play.google.com/store/apps/details?id=org.buildamovement.whistler&ah=-MYg40c2HaECWAnCSvZQHylwE6o');
    }
}