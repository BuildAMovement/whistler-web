<?php
namespace model;

class mediaFile extends base
{

    protected $recordClassName = '\model\record\mediaFile';

    /**
     *
     * @param int|string $uid
     * @return \model\record\mediaFile
     */
    public function one($uid, $idOrUid = false)
    {
        $dbc = \db\db::instance();
        $query = $dbc->simple_select_query(\model\record\mediaFile::getTableName(), [
            $idOrUid ? 'id' : 'uid' => $uid
        ]);
        $out = $dbc->fetch_all($query, $this->getRecordClassName(), 'default', null, false, __FILE__, __LINE__);
        return @reset($out);
    }
}
