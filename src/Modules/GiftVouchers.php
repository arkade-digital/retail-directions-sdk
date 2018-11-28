<?php

namespace Arkade\RetailDirections\Modules;

use DomainException;
use Illuminate\Support\Collection;
use Arkade\RetailDirections\GiftVoucher;
use Arkade\RetailDirections\GiftVoucherRequest;
use Arkade\RetailDirections\GiftVoucherFinaliseRequest;
use Arkade\RetailDirections\GiftVoucherRedeemRequest;
use Arkade\RetailDirections\Exceptions;
use Arkade\RetailDirections\PaymentDetail;

class GiftVouchers extends AbstractModule
{

    /**
     * Get a gift voucher by reference number.
     *
     * @param string $referenceNumber
     * @param string $pin
     * @param string $locationCode
     * @param string $schema
     *
     * @return GiftVoucher
     *
     * @throws Exceptions\ServiceException
     * @throws Exceptions\NotFoundException
     */
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
     * Issues a gift voucher, can use either the one or two step process
     *
	 * @param  array $payload
     *
	 * @return GiftVoucherRequest
     *
	 * @throws Exceptions\ServiceException
     * @throws Exceptions\NotFoundException
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
     * Finalise pending giftcard request to include payment details.
     *
     * @param array $payload
     * @param PaymentDetail[]|Collection $payments
     *
     * @return GiftVoucherFinaliseRequest
     *
     * @throws Exceptions\ServiceException
     * @throws Exceptions\NotFoundException
     */
    public function finaliseGiftVoucher($payload, $payments)
    {
        /*
        * Multi Step Sample
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

    public function redeemGiftVoucher($payload)
    {
        $payload = [
            'reference_type_ind' => 'E',
            'doc_line_id' => '12345',
            'clienttype_ind' => 'E',
            'giftvoucherscheme_code' => '000487h1h',
            'giftvoucher_reference' => '2780055041482665455',
            'location_code' => '1112',
            'tran_type' => 'R',
            'tran_datetime' => '2018-11-20T13:50:00.0000000+10:00',
            'tran_currency' => 'AUD',
            'tran_amount' => 100000000000.00,
            'user_code' => '111200007079',
            'pin' => '6654',
            'min_bal_redeem_full_ind' => 'N'
        ];

        try {
            $response = $this->client->call('DoVoucherTransaction', [
                'VoucherTransaction' => $payload,
            ],
                'VoucherTransaction'
            );
        } catch (Exceptions\ServiceException $e) {

            if (60103 == $e->getCode()) {
                throw (new Exceptions\NotFoundException)
                    ->setHistoryContainer($e->getHistoryContainer());
            }
            throw $e;
        }

        return GiftVoucherRedeemRequest::fromXml($response->VoucherTransaction);
    }

}