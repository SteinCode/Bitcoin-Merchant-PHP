<?php

/**
 * Created by UAB Spectro Fincance.
 * This is a sample SpectroCoin Merchant v1.1 API PHP client
 */

include_once "components/FormattingUtil.php";
include_once "data/ApiError.php";
include_once "data/OrderStatusEnum.php";
include_once "data/OrderCallback.php";
include_once "messages/CreateOrderRequest.php";
include_once "messages/CreateOrderResponse.php";

require "vendor/autoload.php";

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class SCMerchantClient
{
    private $merchantApiUrl;
    private $privateMerchantCertLocation;
    private $publicSpectroCoinCertLocation;

    private $merchantId;
    private $apiId;
    private $debug;

    private $privateMerchantKey;
    /**
     * @param $merchantApiUrl
     * @param $merchantId
     * @param $apiId
     * @param bool $debug
     */
    function __construct($merchantApiUrl, $merchantId, $apiId, $debug = false)
    {
        $this->privateMerchantCertLocation =
            dirname(__FILE__) . "/../cert/mprivate.pem";
        $this->publicSpectroCoinCertLocation =
            "https://spectrocoin.com/files/merchant.public.pem";
        $this->merchantApiUrl = $merchantApiUrl;
        $this->merchantId = $merchantId;
        $this->apiId = $apiId;
        $this->debug = $debug;

        $this->httpClient = HttpClient::create();
    }

    /**
     * @param $privateKey
     */
    public function setPrivateMerchantKey($privateKey)
    {
        $this->privateMerchantKey = $privateKey;
    }
    /**
     * @param CreateOrderRequest $request
     * @return ApiError|CreateOrderResponse
     */
    public function createOrder(CreateOrderRequest $request)
    {
        $payload = [
            "userId" => $this->merchantId,
            "merchantApiId" => $this->apiId,
            "orderId" => $request->getOrderId(),
            "payCurrency" => $request->getPayCurrency(),
            "payAmount" => $request->getPayAmount(),
            "receiveCurrency" => $request->getReceiveCurrency(),
            "receiveAmount" => $request->getReceiveAmount(),
            "description" => $request->getDescription(),
            "payerEmail" => $request->getPayerEmail(),
            "payerName" => $request->getPayerName(),
            "payerSurname" => $request->getPayerSurname(),
            "culture" => $request->getCulture(),
            "callbackUrl" => "http://localhost.com",
            "successUrl" => "http://localhost.com",
            "failureUrl" => "http://localhost.com",
        ];

        $payload["sign"] = $this->generateSignature(http_build_query($payload));

        //Initialize Symphony HTTP Client
        $httpClient = HttpClient::create();

        try {
            $response = $httpClient->request( "POST" , $this->merchantApiUrl . "/createOrder", 
			["headers" => ["Content-Type" => "application/x-www-form-urlencoded",],"body" => http_build_query($payload),]
            );

            $statusCode = $response->getStatusCode();
            $content = $response->getContent();

			echo json_encode(json_decode($content), JSON_PRETTY_PRINT); 

            if ($statusCode === 200) {
                $body = json_decode($content);

                if (
                    is_array($body) &&
                    count($body) > 0 &&
                    isset($body[0]->code)
                ) {
                    return new ApiError($body[0]->code, $body[0]->message);
                } else {
                    return new CreateOrderResponse(
                        $body->orderRequestId,
                        $body->orderId,
                        $body->depositAddress,
                        $body->payAmount,
                        $body->payCurrency,
                        $body->receiveAmount,
                        $body->receiveCurrency,
                        $body->validUntil,
                        $body->redirectUrl
                    );
                }
            }
        } catch (TransportExceptionInterface $exception) {
            // Log transport errors to the console or error log
            error_log("Transport Exception: " . $exception->getMessage());
        } catch (\Exception $exception) {
            // Log other exceptions to the console or error log
            error_log("Exception: " . $exception->getMessage());
        }

        if (!$this->debug) {
            // Handle non-200 status codes here
            // You may want to throw an exception or handle the error as needed
            error_log("Exception: non-200");
        } else {
            // Handle the debug case here
            error_log("Debug Response: " . $content);
        }
    }

    private function generateSignature($data)
    {
        $privateKey =
            $this->privateMerchantKey != null
                ? $this->privateMerchantKey
                : file_get_contents($this->privateMerchantCertLocation);
        $pkeyid = openssl_pkey_get_private($privateKey);

        $s = openssl_sign($data, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
        $encodedSignature = base64_encode($signature);
        // openssl_free_key($pkeyid);

        return $encodedSignature;
    }

    /**
     * @param $r $_REQUEST
     * @return OrderCallback|null
     */
    public function parseCreateOrderCallback($r)
    {
        $result = null;

        if (
            $r != null &&
            isset(
                $r["userId"],
                $r["merchantApiId"],
                $r["merchantId"],
                $r["apiId"],
                $r["orderId"],
                $r["payCurrency"],
                $r["payAmount"],
                $r["receiveCurrency"],
                $r["receiveAmount"],
                $r["receivedAmount"],
                $r["description"],
                $r["orderRequestId"],
                $r["status"],
                $r["sign"],
                $r["payerName"],
                $r["payerSurname"],
                $r["payerEmail"]
            )
        ) {
            $result = new OrderCallback(
                $r["userId"],
                $r["merchantApiId"],
                $r["merchantId"],
                $r["apiId"],
                $r["orderId"],
                $r["payCurrency"],
                $r["payAmount"],
                $r["receiveCurrency"],
                $r["receiveAmount"],
                $r["receivedAmount"],
                $r["description"],
                $r["orderRequestId"],
                $r["status"],
                $r["sign"],
                $r["payerName"],
                $r["payerSurname"],
                $r["payerEmail"]
            );
        }

        return $result;
    }

    /**
     * @param OrderCallback $c
     * @return bool
     */
    public function validateCreateOrderCallback(OrderCallback $orderCallback)
    {
        $valid = false;

        if ($orderCallback != null) {
            if (
                $this->userId != $orderCallback->getUserId() ||
                $this->merchantApiId != $orderCallback->getMerchantApiId()
            ) {
                return $valid;
            }

            if (!$orderCallback->validate()) {
                return $valid;
            }

            $payload = [
                "merchantId" => $orderCallback->getMerchantId(),
                "apiId" => $orderCallback->getApiId(),
                "orderId" => $orderCallback->getOrderId(),
                "payCurrency" => $orderCallback->getPayCurrency(),
                "payAmount" => $orderCallback->getPayAmount(),
                "receiveCurrency" => $orderCallback->getReceiveCurrency(),
                "receiveAmount" => $orderCallback->getReceiveAmount(),
                "receivedAmount" => $orderCallback->getReceivedAmount(),
                "description" => $orderCallback->getDescription(),
                "orderRequestId" => $orderCallback->getOrderRequestId(),
                "status" => $orderCallback->getStatus(),
            ];

            $data = http_build_query($payload);

            $valid = $this->validateSignature($data, $orderCallback->getSign());
        }

        return $valid;
    }

    /**
     * @param $data
     * @param $signature
     * @return int
     */
    private function validateSignature($data, $signature)
    {
        $sig = base64_decode($signature);
        $publicKey = file_get_contents($this->publicSpectroCoinCertLocation);
        $public_key_pem = openssl_pkey_get_public($publicKey);
        $r = openssl_verify($data, $sig, $public_key_pem, OPENSSL_ALGO_SHA1);
        // openssl_free_key($public_key_pem);

        return $r;
    }
}
