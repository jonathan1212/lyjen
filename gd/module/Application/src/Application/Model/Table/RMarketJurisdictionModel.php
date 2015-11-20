<?php

namespace Application\Model\Table;
use Application\Model\Entity\RMarketJurisdiction;

/**
 * RMarketJurisdictionModel
 *
 * Model methods below.
 */
class RMarketJurisdictionModel extends RMarketJurisdiction
{

    public function exchangeArray($data)
    {
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->marketId = (!empty($data['market_id'])) ? $data['market_id'] : null;
        $this->jurisdictionId = (!empty($data['jurisdiction_id'])) ? $data['jurisdiction_id'] : null;

    }

    // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
