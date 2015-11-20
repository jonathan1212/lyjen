<?php

namespace Application\Model\Table;
use Application\Model\Entity\TMarketProduct;
/**
 * TMarketProductModel
 *
 * Model methods below.
 */
class TMarketProductModel extends TMarketProduct
{

    public function exchangeArray($data)
    {
        $this->marketProductId     = (!empty($data['market_product_id'])) ? $data['market_product_id'] : null;
        $this->productId = (!empty($data['product_id'])) ? $data['product_id'] : null;
        $this->gvn = (!empty($data['gvn'])) ? $data['gvn'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->platformId = (!empty($data['platform_id'])) ? $data['platform_id'] : null;
        $this->branchId = (!empty($data['branch_id'])) ? $data['branch_id'] : null;
        $this->marketId = (!empty($data['market_id'])) ? $data['market_id'] : null;
        $this->gameCategoryId = (!empty($data['game_category_id'])) ? $data['game_category_id'] : null;
        $this->gameGroupId = (!empty($data['game_group_id'])) ? $data['game_group_id'] : null;
        $this->type = (!empty($data['type'])) ? $data['type'] : null;
        $this->target = (!empty($data['target'])) ? $data['target'] : null;
        $this->overview = (!empty($data['overview'])) ? $data['overview'] : null;
        $this->character = (!empty($data['character'])) ? $data['character'] : null;
        $this->remarks = (!empty($data['remarks'])) ? $data['remarks'] : null;
        $this->eDevStartDate = (!empty($data['e_dev_start_date'])) ? $data['e_dev_start_date'] : null;
        $this->rDevStartDate = (!empty($data['r_dev_start_date'])) ? $data['r_dev_start_date'] : null;
        $this->eDevFinishDate= (!empty($data['e_dev_finish_date'])) ? $data['e_dev_finish_date'] : null;
        $this->rDevFinishDate = (!empty($data['r_dev_finish_date'])) ? $data['r_dev_finish_date'] : null;
        $this->isActive = (!empty($data['is_active'])) ? $data['is_active'] : null;

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
