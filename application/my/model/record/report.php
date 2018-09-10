<?php
namespace model\record;

/**
 *
 * @property int $id
 * @property string $uid
 * @property int $created timestamp when report is created
 * @property bool $public
 * @property int $status
 * @property bool $mailsent
 * @property bool $admin_notification_sent
 * @property array $json
 *
 */
class report extends base
{

    protected static $tableName = 'report';

    public $hasAudio = false;

    public $hasPhoto = false;

    public $hasVideo = false;

    /**
     *
     * @var \model\record\evidence[]
     */
    public $evidences = null;

    public function behaviours()
    {
        return array(
            'json' => array(
                'class' => '\ufw\model\behaviour\json',
                'serialAttributes' => array(
                    'json'
                ) // name of attribute(s)
            )
        );
    }

    public function url()
    {
        return $this->getHelper('url')->url([
            'controller' => 'reports',
            'action' => 'item',
            'id' => $this->getId()
        ], true);
    }

    public function getContentIcon()
    {
        if ($this->hasVideo()) {
            $out = '/static/img/iconset/video-gray.svg';
        } elseif ($this->hasPhoto()) {
            $out = '/static/img/iconset/photo-gray.svg';
        } elseif ($this->hasAudio()) {
            $out = '/static/img/iconset/audio-gray.svg';
        } else {
            $out = '/static/img/iconset/photo-gray.svg';
        }
        return $out;
    }

    public function getCoverImage()
    {
        if ($this->hasPhoto()) {
            $out = null;
            foreach ($this->getEvidences() as $evidence) {
                if ($evidence->getIsPhoto() && $evidence->getIsUploaded()) {
                    $out = $evidence->coverImageUrl();
                }
            }
            if (!$out) {
                $out = '/static/img/cover-placeholder-upload-in-progress.jpg';
            }
        } elseif ($this->hasVideo()) {
            $out = $this->getEvidences()[$this->hasVideo]->coverImageUrl();
        } elseif ($this->hasAudio()) {
            $out = $this->getEvidences()[$this->hasAudio]->coverImageUrl();
        } else {
            $out = '/static/img/cover-placeholder.jpg';
        }
        return $out;
    }

    protected function readEvidences()
    {
        $dbc = \db\db::instance();
        $query = $dbc->simple_select_query(\model\record\evidence::getTableName(), [
            'reportId' => $this->id,
//             'state' => \model\record\evidence::STATUS_UPLOADED
        ]);
        $this->evidences = $dbc->fetch_all($query, '\model\record\evidence', 'default', 'id', false, __FILE__, __LINE__);
        return $this;
    }

    public function isAllowed()
    {
        return in_array($this->status, $this->getHelper('user')->isAdmin() ? [
            \model\report::STATUS_APPROVED,
            \model\report::STATUS_REJECTED,
            \model\report::STATUS_UNREVIEWED
        ] : [
            \model\report::STATUS_UNREVIEWED,
            \model\report::STATUS_APPROVED
        ]);
    }
    
    public function isPrivate() {
        return !$this->public;
    }
    
    public function isPublic() {
        return $this->public;
    }

    public function getContactInfo() {
        return @$this->json['contactInformation'];
    }
    
    public function getContent() {
        return @$this->json['content'];
    }
    
    public function getId()
    {
        return $this->uid;
    }

    public function getEmailRecipients() {
        return isset($this->json['recipients']) ? $this->json['recipients'] : array();
    }
    
    public function getEvidences()
    {
        if (!isset($this->evidences)) {
            $this->readEvidences();
        }
        return $this->evidences;
    }

    public function getLocation($default = 'Unknown location')
    {
        return isset($this->json['location']) && $this->json['location'] ? $this->json['location'] : $default;
    }

    public function getMapZoom()
    {
        return 12;
    }

    public function getTs()
    {
        return is_numeric($this->json['date']) ? $this->json['date'] : strtotime($this->json['date']);
    }

    public function getTitle()
    {
        return $this->json['title'] ? $this->json['title'] : '(title missing)';
    }

    public function hasAudio()
    {
        return $this->hasAudio;
    }

    public function hasPhoto()
    {
        return $this->hasPhoto;
    }

    public function hasVideo()
    {
        return $this->hasVideo;
    }
}
