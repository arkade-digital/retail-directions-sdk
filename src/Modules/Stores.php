<?php

namespace Arkade\RetailDirections\Modules;

use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Collection;
use Arkade\RetailDirections\Store;
use Arkade\RetailDirections\Exceptions;

class Stores extends AbstractModule
{
    /**
     * Return a list of stores
     *
     * @param  string $storeCode
     * @return Store|Collection
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\ServiceException
     */
    public function getStoreSummaryList($storeCode)
    {
        try {
            $response = $this->client->call('GetStoreSummaryList', [
                'GetStoreSummaryList ' => [
                    'storeCode' => $storeCode,
                ]
            ]);
        } catch (Exceptions\ServiceException $e) {

            if (60103 == $e->getCode()) {
                throw (new Exceptions\NotFoundException)
                    ->setHistoryContainer($e->getHistoryContainer());
            }

            throw $e;

        }

        $stores = collect([]);
        foreach ($response->StoreSummaryList->StoreSummary as $store) {
            $stores->push(Store::fromXml($store));
        }

        return $stores;
    }
}