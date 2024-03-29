<?php

namespace Arkade\RetailDirections\Modules;

use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Collection;
use Arkade\RetailDirections\ItemColour;
use Arkade\RetailDirections\ItemDetail;
use Arkade\RetailDirections\ItemColourDetail;
use Arkade\RetailDirections\WebItemDetail;
use Arkade\RetailDirections\StockAvailability;
use Arkade\RetailDirections\Exceptions;

class Items extends AbstractModule
{
    /**
     * Return the colour range for a store
     *
     * @param  string $storeCode
     * @param  Carbon|null $datetime
     * @return ItemColour|Collection
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
     * Return the colour items whose stock levels have changed for a store
     *
     * @param  string $storeCode
     * @param  Carbon|null $datetime
     * @return ItemColour|Collection
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\ServiceException
     */
    public function getStoreItemStockMovement($storeCode, Carbon $datetime)
    {
        try {
            $response = $this->client->call('ItemColourStockMovementFind', [
                'ItemColourStockMovementFind' => [
                    'storeCode' => $storeCode,
                    'fromDateTime' => $this->client->formatDateTime($datetime),
                ]
            ]);
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
     * Get web site feature items
     *
     * @param  string $sellcodeCode
     * @return StockAvailability|Collection
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\ServiceException
     */
    public function getStoreStockAvailability($sellcodeCode, $includeZeros = true)
    {
        $includeZeros = $includeZeros ? 'Y' : 'N';
        try {
            $response = $this->client->call('GetStockInStoreAvailability',[
                'ItemColourSizeList' => [
                    'SKU' => [
                        'sellcodeCode' => $sellcodeCode,
                    ],
                ],
                'includeZeroes' => $includeZeros,
            ]);
        } catch (Exceptions\ServiceException $e) {

            if (60103 == $e->getCode()) {
                throw (new Exceptions\NotFoundException)
                    ->setHistoryContainer($e->getHistoryContainer());
            }

            throw $e;
        }

        $items = collect([]);
        foreach ($response->ResultList->StockInStoreAvailability as $item) {
            $items->push(StockAvailability::fromXml($item));
        }

        return $items;
    }

    /**
     * Return the item details for a stores item
     *
     * @param  string $itemReference
     * @param  string $storeCode
     * @param  string $supplyChannelCode
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

        $items = collect([]);
        foreach ($response->ItemDetails->ItemDetail as $item) {
            $items->push(ItemDetail::fromXml($item));
        }

        return $items;
    }

    /**
     * Return the item color details for a stores item
     *
     * @param  string $itemReference
     * @param  string $storeCode
     * @param  string $storeGroupCode
     * @param  string $itemTypeCode
     * @return ItemColourDetail|Collection
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\ServiceException
     */
    public function getStoreColourDetails($itemReference, $storeCode, $storeGroupCode, $itemTypeCode)
    {
        try {
            $response = $this->client->call('ItemColourDetailsGet',[
                'ItemColourDetailsGet' => [
                    'storeCode' => $storeCode,
                    'storegroupCode' => $storeGroupCode,
                    'itemTypeCode' => $itemTypeCode,
                ],
                'ItemColourList' => [
                    'ItemColour' => [
                        'itemColourRef' => $itemReference
                    ]
                ]
            ]);
        } catch (Exceptions\ServiceException $e) {

            if (60103 == $e->getCode()) {
                throw (new Exceptions\NotFoundException)
                    ->setHistoryContainer($e->getHistoryContainer());
            }

            throw $e;
        }

        $items = collect([]);
        foreach ($response->ItemColourDetailsList->ItemColourDetails as $item) {
            $items->push(ItemColourDetail::fromXml($item));
        }

        return $items;
    }

    /**
     * Get web site feature items
     *
     * @param  string $itemReference
     * @param  string $storeCode
     * @param  string $supplyChannelCode
     * @return WebItemDetail
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

        return WebItemDetail::fromXml($response->WebItem);
    }

}
