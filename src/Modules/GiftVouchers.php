<?php

namespace Arkade\RetailDirections\Modules;

use DomainException;
use Illuminate\Support\Collection;
use Arkade\RetailDirections\GiftVoucher;
use Arkade\RetailDirections\GiftVoucherRequest;
use Arkade\RetailDirections\GiftVoucherFinaliseRequest;
use Arkade\RetailDirections\Exceptions;
use Arkade\RetailDirections\PaymentDetail;

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


	/**
	 * @param  array $payload
	 *
	 * @return  \SimpleXMLElement
	 * @throws Exceptions\ServiceException
	 */
    public function issueGiftVoucher($payload)
    {
        /*
         * Single Step Sample
         * $payload = [
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
        * ];
        * Multi Step Sample
        * $payload = [
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
                'status_ind' => 'P'
        * ];
        */
        try {
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

        return GiftVoucherRequest::fromXml($response->VoucherRequest);

    }

    /**
     * Finalise pending giftcard.
     *
     * @param  array $payload
     * @param PaymentDetail[]|Collection $payments
     *
     * @return  \SimpleXMLElement
     * @throws Exceptions\ServiceException
     */
    public function finaliseGiftVoucher($payload, $payments)
    {
        /*
        * Working example
        * $payload = [
               'giftvoucherrequest_code' => '000487h1h',
               'store_code' => '1112',
               'status_ind' => 'A'
               'finalise_user' => 'WEB',
               'finalise_date_time' => '2013-09-02T09:58:57.6167226+10:00',
               'generate_giftcard_ind' => 'Y'
               'fulfilment_method_ind' => 'V',//V for virtual P for physical
           ];
        */
        $payload = [
            'VoucherRequestFinalise' => $payload
        ];

        // Add payments to order is available, this is the last chance below the order is locked
        if ($payments->count()) {
            $payload['PaymentDetails'] = $payments->map(function(PaymentDetail $payment) {
                return $payment->getXmlArray();
            })->toArray();
        }

        try {
            $response = $this->client->call('DoGiftVoucherRequestFinalise', $payload, 'VoucherRequestFinalise');
        } catch (Exceptions\ServiceException $e) {

            if (60103 == $e->getCode()) {
                throw (new Exceptions\NotFoundException)
                    ->setHistoryContainer($e->getHistoryContainer());
            }
            throw $e;
        }

        return GiftVoucherFinaliseRequest::fromXml($response->VoucherRequestFinalise);
    }

}