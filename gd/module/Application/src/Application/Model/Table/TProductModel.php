<?php

namespace Application\Model\Table;
use Application\Model\Entity\TProduct;
/**
 * TProductModel
 *
 * Model methods below.
 */
class TProductModel extends TProduct
{

    public function exchangeArray($data)
    {
        $this->productId     = (!empty($data['product_id'])) ? $data['product_id'] : null;
        $this->controlId = (!empty($data['control_id'])) ? $data['control_id'] : null;
        $this->eArtworkSpDate = (!empty($data['e_artwork_sp_date'])) ? $data['e_artwork_sp_date'] : null;
        $this->rArtworkSpDate = (!empty($data['e_artwork_sp_date'])) ? $data['e_artwork_sp_date'] : null;
        $this->eGslickDate = (!empty($data['e_gslick_date'])) ? $data['e_gslick_date'] : null;
        $this->rGslickDate = (!empty($data['r_gslick_date'])) ? $data['r_gslick_date'] : null;
        $this->eDemoDate = (!empty($data['e_demo_date'])) ? $data['e_demo_date'] : null;
        $this->rDemoDate = (!empty($data['r_demo_date'])) ? $data['r_demo_date'] : null;
        $this->eMovieDate = (!empty($data['e_movie_date'])) ? $data['e_movie_date'] : null;
        $this->rMovieDate = (!empty($data['r_movie_date'])) ? $data['r_movie_date'] : null;
        $this->eArtworkTrDate = (!empty($data['e_artwork_tr_date'])) ? $data['e_artwork_tr_date'] : null;
        $this->rArtworkTrDate = (!empty($data['r_artwork_tr_date'])) ? $data['r_artwork_tr_date'] : null;
        $this->eWebsiteDate = (!empty($data['e_website_date'])) ? $data['e_website_date'] : null;
        $this->rWebsiteDate = (!empty($data['r_website_date'])) ? $data['r_website_date'] : null;
        $this->isReturnDemo = (!empty($data['is_return_demo'])) ? $data['is_return_demo'] : null;
        $this->gameImagePass = (!empty($data['game_image_pass'])) ? $data['game_image_pass'] : null;

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
