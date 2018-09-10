<?php
namespace model\record;

/**
 *
 * @property int $id
 * @property string $uid
 * @property string $fileName
 * @property string $fileExt
 * @property array $metadata
 * @property int $state
 * @property int $created
 * @property int $updated
 */
class mediaFile extends base
{

    const STATUS_UNKNOWN = 0;

    const STATUS_REGISTERED = 10;

    const STATUS_UPLOADING = 20;

    const STATUS_UPLOADED = 30;

    protected static $tableName = 'media_file';

    protected static $storageDir = '/opt/whistler/files/';

    protected $isAudio = null;

    protected $isPhoto = null;

    protected $isVideo = null;

    protected static $exts = [
        'audio' => [
            '.mp3',
            '.ogg',
            '.3gp',
            '.m4a',
            '.aac'
        ],
        'photo' => [
            '.jpg',
            '.jpeg',
            '.png'
        ],
        'video' => [
            '.mp4',
            '.avi'
        ]
    ];

    public function behaviours()
    {
        return array(
            'json' => array(
                'class' => '\ufw\model\behaviour\json',
                'serialAttributes' => array(
                    'metadata'
                ) // name of attribute(s)
            )
        );
    }

    public function coverImageUrl()
    {
        if ($this->getIsPhoto()) {
            return $this->getIsUploaded() ? $this->url() : '/static/img/cover-placeholder-upload-in-progress.jpg';
        }
        if ($this->getIsVideo()) {
            return $this->getIsUploaded() ? '/static/img/cover-placeholder-video.png' : '/static/img/cover-placeholder-upload-in-progress.jpg';
        }
        if ($this->getIsAudio()) {
            return $this->getIsUploaded() ? '/static/img/cover-placeholder-audio.png' : '/static/img/cover-placeholder-upload-in-progress.jpg';
        }
        return '#';
    }

    public function downloadUrl()
    {
        return $this->getHelper('url')->url([
            'controller' => 'xmedias',
            'action' => 'download',
            'id' => $this->uid
        ], true);
    }

    public function url()
    {
        if ($this->getIsUploaded()) {
            return $this->getHelper('url')->url([
                'controller' => 'xmedias',
                'action' => 'file',
                'id' => $this->uid
            ], true);
        } else {
            return $this->coverImageUrl();
        }
    }

    public function getFfn()
    {
        return $this->getStorageDir() . $this->uid;
    }

    public function isAllowed()
    {
        return true;
    }
    
    public function hasSupportedExt() {
        return in_array(strtolower($this->fileExt), array_flatten(self::$exts));
    }

    public function getIsUploaded()
    {
        return $this->state == self::STATUS_UPLOADED;
    }

    public function getId()
    {
        return $this->uid;
    }

    public function getIsAudio()
    {
        if (!isset($this->isAudio)) {
            $this->isAudio = in_array($this->getFileExt(), self::$exts['audio']);
        }
        return $this->isAudio;
    }

    public function getIsPhoto()
    {
        if (!isset($this->isPhoto)) {
            $this->isPhoto = in_array($this->getFileExt(), self::$exts['photo']);
        }
        return $this->isPhoto;
    }

    public function getIsVideo()
    {
        if (!isset($this->isVideo)) {
            $this->isVideo = in_array($this->getFileExt(), self::$exts['video']);
        }
        return $this->isVideo;
    }

    public function getFileExt()
    {
        return $this->fileExt;
    }

    public function getLocationMetadata()
    {
        $metadata = $this->getMetadata();
        return $metadata && isset($metadata['location']) ? $metadata['location'] : null;
    }

    /**
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    public function getTs()
    {
        $metadata = $this->getMetadata();
        if (isset($metadata['date'])) {
            $out = is_numeric($metadata['date']) ? $metadata['date'] : strtotime($metadata['date']);
        } elseif (strcmp('0000-00-00 00:00:00', $this->created)) {
            $out = strtotime($this->created);
        } else {
            $out = strtotime($this->updated);
        }
        return $out;
    }

    public function getContactInfo()
    {
        return @$this->metadata['contactInformation'];
    }

    public function getContent()
    {
        return @$this->metadata['content'];
    }

    public function getLocationLat()
    {
        $metadata = $this->getMetadata();
        return $metadata && isset($metadata['location']) && isset($metadata['location']['latitude']) ? $metadata['location']['latitude'] : null;
    }

    public function getLocationLng()
    {
        $metadata = $this->getMetadata();
        return $metadata && isset($metadata['location']) && isset($metadata['location']['longitude']) ? $metadata['location']['longitude'] : null;
    }

    public function getLocationElevation()
    {
        $metadata = $this->getMetadata();
        return $metadata && isset($metadata['location']) && isset($metadata['location']['altitude']) ? $metadata['location']['altitude'] : null;
    }

    public function getLocationLuminosity()
    {
        $metadata = $this->getMetadata();
        return $metadata && isset($metadata['light']) ? $metadata['light'] : null;
    }

    public function getLocationAirPressure()
    {
        $metadata = $this->getMetadata();
        return $metadata && isset($metadata['airpressure']) ? $metadata['airpressure'] : null;
    }

    public function getWifiAPs()
    {
        $metadata = $this->getMetadata();
        return $metadata && isset($metadata['wifis']) ? $metadata['wifis'] : null;
    }

    public function getCellTowers()
    {
        $metadata = $this->getMetadata();
        return $metadata && isset($metadata['cells']) ? $metadata['cells'] : null;
    }

    public function getType()
    {
        if ($this->getIsPhoto()) {
            return 'photo';
        }
        if ($this->getIsVideo()) {
            return 'video';
        }
        if ($this->getIsAudio()) {
            return 'audio';
        }
    }

    public function getMapZoom()
    {
        return 12;
    }

    public function getTitle()
    {
        return $this->uid;
    }

    /**
     *
     * @return string
     */
    public function getStorageDir()
    {
        return self::$storageDir;
    }
}
