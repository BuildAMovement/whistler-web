<?php
namespace ufw\model\behaviour;

abstract class base
{

    public static $behaviourMethods = [
        'beforeSave',
        'afterSave',
        'beforeDataSet',
        'afterDataSet'
    ];

    /**
     *
     * @var \model\record\base
     */
    protected $owner = null;

    public function __construct(\model\record\base $owner)
    {
        $this->setOwner($owner);
    }

    /**
     *
     * @return \model\record\base
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     *
     * @param \model\record\base $owner            
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }
}