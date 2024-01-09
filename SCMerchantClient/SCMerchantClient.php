<?php

/**
 * Created by UAB Spectro Fincance.
 * This is a sample SpectroCoin Merchant v1.1 API PHP client
 */

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

include_once('components/FormattingUtil.php');
include_once('data/ApiError.php');
include_once('data/OrderStatusEnum.php');
include_once('data/OrderCallback.php');
include_once('messages/CreateOrderRequest.php');
include_once('messages/CreateOrderResponse.php');
include_once('./debug_helpers.php');


require_once __DIR__ . '/../vendor/autoload.php';


class SCMerchantClient
{

	private $merchantApiUrl;
	private $privateMerchantCertLocation;
	private $publicSpectroCoinCertLocation;

	private $merchantId;
	private $apiId;
	private $debug;

	private $privateMerchantKey;

	protected $client;
	/**
	 * @param $merchantApiUrl
	 * @param $merchantId
	 * @param $apiId
	 * @param bool $debug
	 */
	function __construct($merchantApiUrl, $merchantId, $apiId, $debug = false)
	{
		$this->privateMerchantCertLocation = dirname(__FILE__) . '/../cert/mprivate.pem';
		$this->publicSpectroCoinCertLocation = dirname(__FILE__) . '/../cert/mpublic.pem';
		$this->merchantApiUrl = $merchantApiUrl;
		$this->merchantId = $merchantId;
		$this->apiId = $apiId;
		$this->debug = $debug;
		$this->client = new Client();
	}

	/**
	 * @param $privateKey
	 */
	public function setPrivateMerchantKey($privateKey) {
		$this->privateMerchantKey = $privateKey;
	}
	/**
	 * @param CreateOrderRequest $request
	 * @return ApiError|CreateOrderResponse
	 */
	public function createOrder(CreateOrderRequest $request){
		$payload = array(
			"callbackUrl" => $request->getCallbackUrl(),
			"description" => $request->getDescription(),
			"failureUrl" => $request->getFailureUrl(),
			"lang" => $request->getLang(),
			"orderId" => $request->getOrderId(),
			"payAmount" => $request->getPayAmount(),
			"payCurrency" => $request->getReceiveCurrency(),
			"payNetworkName" => $request->getPayNetworkName(),
			"payerDateOfBirth" => $request->getPayerDateOfBirth(),
			"payerEmail" => $request->getPayerEmail(),
			"payerName" => $request->getPayerName(),
			"payerSurname" => $request->getPayerSurname(),
			"projectId" => $this->apiId,
			"receiveAmount" => $request->getReceiveAmount(),
			"receiveCurrency" => $request->getReceiveCurrency(),
			"successUrl" => $request->getSuccessUrl(),
		);

		$jsonPayload = json_encode($payload);

		printToBrowserConsole($payload);

        try {
            $response = $this->client->request('POST', $this->merchantApiUrl . '/createOrder', [
                RequestOptions::HEADERS => ['Content-Type' => 'application/json'],
                RequestOptions::BODY => $jsonPayload
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody()->getContents());

            if ($statusCode == 200 && $body != null) {
                if (is_array($body) && count($body) > 0 && isset($body[0]->code)) {
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
        } catch (GuzzleException $e) {
            return new ApiError($e->getCode(), $e->getMessage());
        }
		echo "Invalid Response: " . "No valid response received.". "\n";
        return new ApiError('Invalid Response', 'No valid response received.');
	}




	private function generateSignature($data)
	{
		$privateKey = $this->privateMerchantKey != null ? $this->privateMerchantKey : file_get_contents($this->privateMerchantCertLocation);
		$pkeyid = openssl_pkey_get_private($privateKey);

		$s = openssl_sign($data, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
		$encodedSignature = base64_encode($signature);

		return $encodedSignature;
	}

	/**
	 * @param $r $_REQUEST
	 * @return OrderCallback|null
	 */
	public function parseCreateOrderCallback($r)
	{
		$result = null;

		if ($r != null && isset($r['userId'], $r['merchantApiId'], $r['merchantId'], $r['apiId'], $r['orderId'], $r['payCurrency'], $r['payAmount'], $r['receiveCurrency'], $r['receiveAmount'], $r['receivedAmount'], $r['description'], $r['orderRequestId'], $r['status'], $r['sign'])) {
			$result = new OrderCallback($r['userId'], $r['merchantApiId'], $r['merchantId'], $r['apiId'], $r['orderId'], $r['payCurrency'], $r['payAmount'], $r['receiveCurrency'], $r['receiveAmount'], $r['receivedAmount'], $r['description'], $r['orderRequestId'], $r['status'], $r['sign']);
		}

		return $result;
	}

	/**
	 * @param OrderCallback $c
	 * @return bool
	 */
	public function validateCreateOrderCallback(OrderCallback $c)
	{
		$valid = false;

		if ($c != null) {

			if ($this->userId != $c->getUserId() || $this->merchantApiId != $c->getMerchantApiId())
				return $valid;

			if (!$c->validate())
				return $valid;

			$payload = array(
				'merchantId' => $c->getMerchantId(),
				'apiId' => $c->getApiId(),
				'orderId' => $c->getOrderId(),
				'payCurrency' => $c->getPayCurrency(),
				'payAmount' => $c->getPayAmount(),
				'receiveCurrency' => $c->getReceiveCurrency(),
				'receiveAmount' => $c->getReceiveAmount(),
				'receivedAmount' => $c->getReceivedAmount(),
				'description' => $c->getDescription(),
				'orderRequestId' => $c->getOrderRequestId(),
				'status' => $c->getStatus(),
			);

			$formHandler = new \Httpful\Handlers\FormHandler();
			$data = $formHandler->serialize($payload);
			$valid = $this->validateSignature($data, $c->getSign());
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

		return $r;
	}

}
