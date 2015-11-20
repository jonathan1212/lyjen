<?php

namespace Application\Model\Table;
use Application\Model\Entity\MPage;
/**
 * MPageModel
 *
 * Model methods below.
 */
class MPageModel extends MPage
{

    public function exchangeArray($data)
    {
        $this->pageId     = (!empty($data['page_id'])) ? $data['page_id'] : null;
        $this->pageName = (!empty($data['page_name'])) ? $data['page_name'] : null;
        
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
