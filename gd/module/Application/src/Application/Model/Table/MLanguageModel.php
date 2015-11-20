<?php

namespace Application\Model\Table;
use Application\Model\Entity\MLanguage;
/**
 * MLanguageModel
 *
 * Model methods below.
 */
class MLanguageModel extends MLanguage
{

    public function exchangeArray($data)
    {
        $this->languageId     = (!empty($data['language_id'])) ? $data['language_id'] : null;
        $this->nameEn = (!empty($data['name_en'])) ? $data['name_en'] : null;
        $this->nameNative = (!empty($data['name_native'])) ? $data['name_native'] : null;
        $this->code = (!empty($data['code'])) ? $data['code'] : null;
        $this->resource = (!empty($data['resource'])) ? $data['resource'] : null;

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
