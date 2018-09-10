<?php
namespace model;

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

    protected $recordClassName = '\model\record\evidence';

    /**
     *
     * @param int|string $uid            
     * @return \model\record\evidence
     */
    public function one($uid, $idOrUid = false)
    {
        $dbc = \db\db::instance();
        $items = $dbc->simple_select('\model\record\evidence', [
            'where' => [
                ($idOrUid ? 'id' : 'uid') => $uid
            ]
        ], '\ufw\info_hash', 'id', 'uid', 'default', __FILE__, __LINE__);
        return reset($items->info);
    }

}
