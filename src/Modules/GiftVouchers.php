<?php

namespace Arkade\RetailDirections\Modules;

use DomainException;
use Illuminate\Support\Collection;
use Arkade\RetailDirections\GiftVoucher;
use Arkade\RetailDirections\Exceptions;

class GiftVouchers extends AbstractModule
{

    public function getGiftVouchers($referenceNumber, $pin, $locationCode, $schema)
    {
        try {
            $response = $this->client->call('GetVoucherEnquire', [
                'VoucherDetails' => [
                    'giftvoucher_reference' => $referenceNumber,
                    'giftvoucherscheme_code' => $schema,
                    'location_code' => $locationCode,
                    'pin' => $pin
                ],
              ],
                'VoucherEnquiry'
            );
        } catch (Exceptions\ServiceException $e) {

            if (60103 == $e->getCode()) {
                throw (new Exceptions\NotFoundException)
                    ->setHistoryContainer($e->getHistoryContainer());
            }
            throw $e;
        }

        return GiftVoucher::fromXml($response->VoucherDetails);
    }

    public function createGiftVoucher(GiftVoucher $gift_voucher)
    {
    	$payload = array_filter([
    		'giftvoucherscheme_code' => $gift_voucher->getGiftVoucherSchemaCode(),
    		'giftvoucherscheme_code' => $gift_voucher->getStoreCode(),
	    ]);

        try {
            $response = $this->client->call('VoucherRequest', [
                'VoucherRequest' => $payload
              ],
                'VoucherRequest'
            );

            //$response = $this->client->call('VoucherEnquiry',$request);
        } catch (Exceptions\ServiceException $e) {

            if (60103 == $e->getCode()) {
                throw (new Exceptions\NotFoundException)
                    ->setHistoryContainer($e->getHistoryContainer());
            }

            throw $e;
        }

        return GiftVoucher::fromXml($response->VoucherDetails);

    }


	/**
	 * @param  array $payload
	 *
	 * @return  \SimpleXMLElement
	 * @throws Exceptions\ServiceException
	 */
    public function issueGiftVoucher($payload)
    {
        try {
            /*
             * Working example
             * $response = $this->client->call('DoGiftVoucherRequest', [
                'VoucherRequest' => [
                    'giftvoucherscheme_code' => '000487h1h',
                    'store_code' => '1112',
                    'issued_currency_code' => 'AUD',
                    'amount' => 50,
                    'reference_code' => 'abcdefghijklinn',//this is payment reference number
                    'purchaser_first_name' => 'tejas',
                    'recipient_first_name' => 'tejas',
                    'recipient_email_address' => 'tejas@arkade.com.au',
                    'message' => 'hellow how are you',
                    'fulfilment_method_ind' => 'V',//V for virtual P for physical
                    'status_ind' => 'A'
                ],
            ],
                'VoucherRequest'
            );*/
            $response = $this->client->call('DoGiftVoucherRequest', [
                'VoucherRequest' => $payload,
            ],
                'VoucherRequest'
            );
        } catch (Exceptions\ServiceException $e) {

            if (60103 == $e->getCode()) {
                throw (new Exceptions\NotFoundException)
                    ->setHistoryContainer($e->getHistoryContainer());
            }
            throw $e;
        }

        return $response->VoucherRequest;
    }

}