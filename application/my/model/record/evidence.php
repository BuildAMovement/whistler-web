<?php
namespace model\record;

/**
 *
 * @property int $id
 * @property int $reportId
 * @property string $uid
 * @property string $fileExt
 * @property int state
 *          
 */
class evidence extends base
{

    const STATUS_UNKNOWN = 0;

    const STATUS_UPLOADING = 10;

    const STATUS_UPLOADED = 20;

    protected static $tableName = 'evidence';

    protected static $storageDir = '/opt/whistler/files/';

    protected $isAudio = null;

    protected $isPhoto = null;

    protected $isVideo = null;

    /**
     *
     * @var array
     */
    protected $metadata = null;

    /**
     *
     * @var \model\record\report
     */
    protected $report = null;

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
    }

    public function downloadUrl()
    {
        return $this->getHelper('url')->url([
            'controller' => 'reports',
            'action' => 'download',
            'id' => $this->uid
        ], true);
    }

    public function isAllowed()
    {
        return $this->getReport()->isAllowed();
    }
    
    public function hasSupportedExt() {
        return in_array(strtolower($this->fileExt), array_flatten(self::$exts));
    }

    public function url()
    {
        if ($this->getIsUploaded()) {
            return $this->getHelper('url')->url([
                'controller' => 'reports',
                'action' => 'evidence',
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
        return $this->id ? $this->fileExt : strrchr($this->getMetadata()['path'], '.');
    }

    public function getIsUploaded()
    {
        return $this->state == self::STATUS_UPLOADED;
    }

    public function getLocationMetadata()
    {
        $metadata = $this->getMetadata();
        return $metadata && isset($metadata['metadata']['location']) ? $metadata['metadata']['location'] : null;
    }

    public function getLocationLat()
    {
        $metadata = $this->getMetadata();
        return $metadata && isset($metadata['metadata']['location']) && isset($metadata['metadata']['location']['latitude']) ? $metadata['metadata']['location']['latitude'] : null;
    }

    public function getLocationLng()
    {
        $metadata = $this->getMetadata();
        return $metadata && isset($metadata['metadata']['location']) && isset($metadata['metadata']['location']['longitude']) ? $metadata['metadata']['location']['longitude'] : null;
    }

    public function getLocationElevation()
    {
        $metadata = $this->getMetadata();
        return $metadata && isset($metadata['metadata']['location']) && isset($metadata['metadata']['location']['altitude']) ? $metadata['metadata']['location']['altitude'] : null;
    }

    public function getLocationLuminosity()
    {
        $metadata = $this->getMetadata();
        return $metadata && isset($metadata['metadata']['light']) ? $metadata['metadata']['light'] : null;
    }

    public function getLocationAirPressure()
    {
        $metadata = $this->getMetadata();
        return $metadata && isset($metadata['metadata']['airpressure']) ? $metadata['metadata']['airpressure'] : null;
    }

    public function getWifiAPs()
    {
        $metadata = $this->getMetadata();
        return $metadata && isset($metadata['metadata']['wifis']) ? $metadata['metadata']['wifis'] : null;
    }

    public function getCellTowers()
    {
        $metadata = $this->getMetadata();
        return $metadata && isset($metadata['metadata']['cells']) ? $metadata['metadata']['cells'] : null;
    }

    public function hasMetadata()
    {
        return $this->getLocationMetadata() || $this->getWifiAPs() || $this->getCellTowers();
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

    /**
     *
     * @return string
     */
    public function getStorageDir()
    {
        return self::$storageDir;
    }

    /**
     *
     * @return \model\record\report
     */
    public function getReport()
    {
        if (!isset($this->report)) {
            $this->report = (new \model\report())->one($this->reportId, true);
        }
        return $this->report;
    }

    /**
     *
     * @param \model\record\report $report
     */
    public function setReport(\model\record\report $report = null)
    {
        $this->report = $report;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getMetadata()
    {
        if (!isset($this->metadata)) {
            foreach ($this->getReport()->json['evidences'] as $evidenceMeta) {
                if (!strcmp($evidenceMeta['name'], $this->uid)) {
                    $this->metadata = $evidenceMeta;
                    break;
                }
            }
            if (!$this->metadata) {
                $this->metadata = array();
            }
        }
        return $this->metadata;
    }

    public function setMetadata($metadata = null)
    {
        $this->metadata = $metadata;
        return $this;
    }
}
