<?php

namespace Application\Model\Table;
use Application\Model\Entity\MRole;
/**
 * MRoleModel
 *
 * Model methods below.
 */
class MRoleModel extends MRole
{
    public function exchangeArray($data)
    {
        $this->roleId     = (!empty($data['role_id'])) ? $data['role_id'] : null;
        $this->roleName = (!empty($data['role_name'])) ? $data['role_name'] : null;

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
