<?php

namespace Arkade\RetailDirections\Modules;

use DomainException;
use Illuminate\Support\Collection;
use Arkade\RetailDirections\GiftVoucher;
use Arkade\RetailDirections\Exceptions;

class GiftVouchers extends AbstractModule
{

    public function getGiftVoucher($referenceNumber, $pin, $locationCode)
    {
        try {
            $request = [
                'VoucherDetails' => [
                    'giftvoucher_reference' => $referenceNumber,
                    'location_code' => $storeCode,
                    'pin' => $pin
                ]
            ];

            $response = $this->client->call('VoucherEnquiry',$request);
        } catch (Exceptions\ServiceException $e) {

            if (60103 == $e->getCode()) {
                throw (new Exceptions\NotFoundException)
                    ->setHistoryContainer($e->getHistoryContainer());
            }

            throw $e;
        }

        $giftVoucher = $reponse->VoucherDetails;

        return $giftVoucher;
    }



}