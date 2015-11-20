<?php

namespace Application\Model\Table;
use Application\Model\Entity\RRolePage;
/**
 * RRolePageModel
 *
 * Model methods below.
 */
class RRolePageModel extends RRolePage
{

    public function exchangeArray($data)
    {
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->roleId = (!empty($data['role_id'])) ? $data['role_id'] : null;
        $this->pageId = (!empty($data['page_id'])) ? $data['page_id'] : null;

    }

    // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
