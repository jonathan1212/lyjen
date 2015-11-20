<?php

namespace Application\Model\Table;
use Application\Model\Entity\HProduct;


/**
 * HProductModel
 *
 * Model methods below.
 */

class HProductModel extends HProduct
{

    public function exchangeArray($data)
    {
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->jurisdictionProductId = (!empty($data['jurisdiction_product_id'])) ? $data['jurisdiction_product_id'] : null;
        $this->history  = (!empty($data['history'])) ? $data['history'] : null;
    }

    // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

}
