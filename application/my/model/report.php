<?php
namespace model;

class report extends base
{

    const STATUS_UNREVIEWED = '0';

    const STATUS_APPROVED = '1';

    const STATUS_REJECTED = '2';

    protected $recordClassName = '\model\record\report';

    /**
     *
     * @param int|string $uid            
     * @return \model\record\report
     */
    public function one($uid, $idOrUid = false)
    {
        $dbc = \db\db::instance();
        $query = "
            SELECT *
            FROM " . \model\record\report::getTableName() . "
            WHERE 1 ";
        $query .= "AND " . ($idOrUid ? 'id' : 'uid') . ' = ' . $dbc->quote($uid) . " ";
        if (!$this->user->isAdmin()) {
            $query .= 'AND status IN (' . join(', ', $dbc->quote([
                self::STATUS_APPROVED,
                self::STATUS_UNREVIEWED
            ])) . ') ';
        }
        $out = $dbc->fetch_all($query, $this->getRecordClassName(), 'default', null, false, __FILE__, __LINE__);
        return @reset($out);
    }

    public function recent($status = \model\report::STATUS_APPROVED, $page = 1, $perPage = 24)
    {
        $dbc = \db\db::instance();
        
        $whereClause = [];
        if ($this->user->isAdmin()) {
            $whereClause['status'] = ($status || strlen($status)) ? $status : self::STATUS_APPROVED;
        } else {
            $whereClause['status'] = self::STATUS_APPROVED;
            $whereClause['public'] = 1;
        }
        $items = $dbc->simple_select($this->getRecordClassName(), [
            'where' => $whereClause,
            'orderby' => 'created DESC',
            'limit' => ($page - 1) * $perPage . ", $perPage"
        ], '\ufw\info_hash', 'id', 'uid', 'default', __FILE__, __LINE__);
        $this->readEvidences($items);
        return $items;
    }

    public function changeStatus($id, $status)
    {
        $dbc = \db\db::instance();
        return $dbc->simple_update_pk($this->getRecordClassName(), [
            'status' => $status
        ], [
            'uid' => $id
        ], 'default', __FILE__, __LINE__);
    }

    public function mailsent($id)
    {
        $dbc = \db\db::instance();
        return $dbc->simple_update_pk($this->getRecordClassName(), [
            'mailsent' => 1
        ], [
            'uid' => $id
        ], 'default', __FILE__, __LINE__);
    }

    public function adminNotificationSent($id)
    {
        $dbc = \db\db::instance();
        return $dbc->simple_update_pk($this->getRecordClassName(), [
            'admin_notification_sent' => 1
        ], [
            'uid' => $id
        ], 'default', __FILE__, __LINE__);
    }

    /**
     *
     * @param \ufw\info_hash $items            
     */
    protected function readEvidences($items)
    {
        if (!$items->id_arr)
            return $this;
        $dbc = \db\db::instance();
        $query = $dbc->simple_select_query(\model\record\evidence::getTableName(), [
            'reportId' => $items->id_arr,
            'state' => \model\record\evidence::STATUS_UPLOADED
        ]);
        $evidences = $dbc->fetch_all($query, '\model\record\evidence', 'default', 'uid', false, __FILE__, __LINE__);
        foreach ($items->info as $item) {
            foreach ($item->json['evidences'] as $evidenceMeta) {
                $uid = $evidenceMeta['name'];
                $evidence = isset($evidences[$uid]) ? $evidences[$uid] : new \model\record\evidence();
                $evidence->setReport($item)->setMetadata($evidenceMeta);
                $item->evidences[$uid] = $evidence;
                $item->hasVideo = $item->hasVideo ? $item->hasVideo : ($evidence->getIsVideo() ? $uid : false);
                $item->hasPhoto = $item->hasPhoto ? $item->hasPhoto : ($evidence->getIsPhoto() ? $uid : false);
                $item->hasAudio = $item->hasAudio ? $item->hasAudio : ($evidence->getIsAudio() ? $uid : false);
            }
        }
        
        return $this;
    }
}
