<?php

namespace Arkade\RetailDirections\Modules;

use DomainException;
use Illuminate\Support\Collection;
use Arkade\RetailDirections\GiftVoucher;
use Arkade\RetailDirections\Exceptions;

class GiftVouchers extends AbstractModule
{

    public function getGiftVouchers($referenceNumber, $pin, $locationCode)
    {

        try {
            $response = $this->client->call('GetVoucherEnquire', [
                'VoucherDetails' => [
                    'giftvoucher_reference' => $referenceNumber,
                    'giftvoucherscheme_code' => '04',
                    'location_code' => $locationCode,
                    'pin' => $pin
                ],

              ],
                'VoucherEnquiry'
            );
            
            //$response = $this->client->call('VoucherEnquiry',$request);
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