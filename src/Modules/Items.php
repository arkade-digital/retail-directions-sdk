<?php

namespace Arkade\RetailDirections\Modules;

use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Collection;
use Arkade\RetailDirections\Address;
use Arkade\RetailDirections\ItemColour;
use Arkade\RetailDirections\Exceptions;
use Arkade\RetailDirections\Identification;

class Items extends AbstractModule
{
    /**
     * Return the colour range for a store
     *
     * @param  string $storeCode
     * @param  Carbon|null $datetime
     * @return ColourItem|Collection
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\ServiceException
     */
    public function getStoreItemRange($storeCode, Carbon $datetime = null)
    {
        try {
            $request = [
                'BulkItemRangeGet' => [
                    'storeCode' => $storeCode,
                ]
            ];
            if(!is_null($datetime)){
                $request['BulkItemRangeGet']['fromDate'] = $this->client->formatDateTime($datetime);
            }
            $response = $this->client->call('BulkItemRangeGet',$request);
        } catch (Exceptions\ServiceException $e) {

            if (60103 == $e->getCode()) {
                throw (new Exceptions\NotFoundException)
                    ->setHistoryContainer($e->getHistoryContainer());
            }

            throw $e;
        }

        $items = collect([]);
        foreach ($response->ItemColourList->ItemColour as $item) {
            $items->push(ItemColour::fromXml($item));
        }

        return $items;
    }

    /**
     * Return the item details for a stores item
     *
     * @param  string $itemReference
     * @param  string $storeCode
     * @param  string $supplyChannelCode
     * @param  Carbon|null $datetime
     * @return Item|Collection
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\ServiceException
     */
    public function getStoreItemDetail($itemReference, $storeCode, $supplyChannelCode)
    {
        try {
            $response = $this->client->call('ItemDetailsGet', [
                'ItemDetailsGet' => [
                    'itemReference' => $itemReference,
                    'storeCode' => $storeCode,
                    'supplychannelCode' => $supplyChannelCode,
                ]
            ]);
        } catch (Exceptions\ServiceException $e) {

            if (60103 == $e->getCode()) {
                throw (new Exceptions\NotFoundException)
                    ->setHistoryContainer($e->getHistoryContainer());
            }

            throw $e;
        }
    }

    /**
     * Return the item color details for a stores item
     *
     * @param  string $itemReference
     * @param  string $storeCode
     * @param  string $storeGroupCode
     * @return ColourItem|Collection
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\ServiceException
     */
    public function getStoreColourDetails($itemReference, $storeCode, $storeGroupCode)
    {
        try {
            $response = $this->client->call('ItemColourDetailsGet',[
                'ItemColourDetailsGet' => [
                    'storeCode' => $storeCode,
                    'storeGroupCode' => $storeGroupCode,
                    'itemcolourRef' => $itemReference,
                ]
            ]);
        } catch (Exceptions\ServiceException $e) {

            if (60103 == $e->getCode()) {
                throw (new Exceptions\NotFoundException)
                    ->setHistoryContainer($e->getHistoryContainer());
            }

            throw $e;
        }
    }

    /**
     * Get web site feature items
     *
     * @param  string $storeCode
     * @return ColourItem|Collection
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\ServiceException
     */
    public function getWebItemDetails($itemReference, $storeCode, $supplyChannelCode)
    {
        try {
            $response = $this->client->call('GetWebItemDetails',[
                'GetWebItemDetails' => [
                    'itemCode ' => $itemReference,
                    'storeCode' => $storeCode,
                    'supplyChannelCode' => $supplyChannelCode,
                ]
            ]);
        } catch (Exceptions\ServiceException $e) {

            if (60103 == $e->getCode()) {
                throw (new Exceptions\NotFoundException)
                    ->setHistoryContainer($e->getHistoryContainer());
            }

            throw $e;
        }
    }

}