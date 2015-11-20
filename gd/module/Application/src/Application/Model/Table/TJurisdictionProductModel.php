<?php

namespace Application\Model\Table;
use Application\Model\Entity\TJurisdictionProduct;

/**
 * TJurisdictionProductModel
 *
 * Model methods below.
 */
class TJurisdictionProductModel extends TJurisdictionProduct
{

    public function exchangeArray($data)
    {
        $this->jurisdictionProductId     = (!empty($data['jurisdiction_product_id'])) ? $data['jurisdiction_product_id'] : null;
        $this->marketProductId = (!empty($data['market_product_id'])) ? $data['market_product_id'] : null;
        $this->jurisdictionId = (!empty($data['jurisdiction_id'])) ? $data['jurisdiction_id'] : null;
        $this->statusId = (!empty($data['status_id'])) ? $data['status_id'] : null;
        $this->eSubmissionDate = (!empty($data['e_submission_date'])) ? $data['e_submission_date'] : null;
        $this->rSubmissionDate = (!empty($data['r_submission_date'])) ? $data['r_submission_date'] : null;
        $this->eRegulatorDate = (!empty($data['e_regulator_date'])) ? $data['e_regulator_date'] : null;
        $this->rRegulatorDate = (!empty($data['r_regulator_date'])) ? $data['r_regulator_date'] : null;
        $this->eApprovalDate = (!empty($data['e_approval_date'])) ? $data['e_approval_date'] : null;
        $this->rApprovalDate = (!empty($data['r_approval_date'])) ? $data['r_approval_date'] : null;
        $this->eReleaseDate = (!empty($data['e_release_date'])) ? $data['e_release_date'] : null;
        $this->rReleaseDate = (!empty($data['r_release_date'])) ? $data['r_release_date'] : null;
        $this->eLaunchDate = (!empty($data['e_launch_date'])) ? $data['e_launch_date'] : null;
        $this->rLaunchDate = (!empty($data['r_launch_date'])) ? $data['r_launch_date'] : null;

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
