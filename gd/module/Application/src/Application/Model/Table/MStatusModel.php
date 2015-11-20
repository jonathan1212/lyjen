<?php

namespace Application\Model\Table;
use Application\Model\Entity\MStatus;
/**
 * MStatusModel
 *
 * Model methods below.
 */
class MStatusModel extends MStatus
{
    public function exchangeArray($data)
    {
        $this->statusId     = (!empty($data['status_id'])) ? $data['status_id'] : null;
        $this->statusName = (!empty($data['status_name'])) ? $data['status_name'] : null;

        $this->isDeleted  = (!empty($data['is_deleted'])) ? $data['is_deleted'] : null;
        $this->createUserId  = (!empty($data['create_user_id'])) ? $data['create_user_id'] : null;
        $this->createTime  = (!empty($data['create_time'])) ? $data['create_time'] : null;
        $this->updateUserId  = (!empty($data['update_user_id'])) ? $data['update_user_id'] : null;
        $this->updateTime  = (!empty($data['update_time'])) ? $data['update_time'] : null;

    }

    // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
