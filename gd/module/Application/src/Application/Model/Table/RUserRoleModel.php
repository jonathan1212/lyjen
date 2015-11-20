<?php

namespace Application\Model\Table;
use Application\Model\Entity\RUserRole;

/**
 * RUserRoleModel
 *
 * Model methods below.
 */
class RUserRoleModel extends RUserRole
{
    public function exchangeArray($data)
    {
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->userId = (!empty($data['user_id'])) ? $data['user_id'] : null;
        $this->roleId = (!empty($data['role_id'])) ? $data['role_id'] : null;

    }

    // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
