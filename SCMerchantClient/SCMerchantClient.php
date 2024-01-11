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
	private $projectId;
	private $debug;

	protected $client;

	/**
	 * @param $merchantApiUrl
	 * @param $projectId
	 * @param bool $debug
	 */
	function __construct($merchantApiUrl, $projectId, $debug = false)
	{
		$this->merchantApiUrl = $merchantApiUrl;
		$this->projectId = $projectId;
		$this->debug = $debug;
		$this->client = new Client();
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
			"projectId" => $this->projectId,
			"receiveAmount" => $request->getReceiveAmount(),
			"receiveCurrency" => $request->getReceiveCurrency(),
			"successUrl" => $request->getSuccessUrl(),
		);

		$jsonPayload = json_encode($payload);

        try {
            $response = $this->client->request('POST', $this->merchantApiUrl . '/createOrder', [
                RequestOptions::HEADERS => ['Content-Type' => 'application/json'],
                RequestOptions::BODY => $jsonPayload
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody()->getContents(), true); //

            if ($statusCode == 200 && $body != null) {
                if (is_array($body) && count($body) > 0 && isset($body[0]->code)) {
                    return new ApiError($body[0]->code, $body[0]->message);
                } else {
					return new CreateOrderResponse(
						$body['depositAddress'],
						$body['memo'],
						$body['orderId'],
						$body['payAmount'],
						$body['payCurrency'],
						$body['payNetworkName'],
						$body['preOrderId'],
						$body['receiveAmount'],
						$body['receiveCurrency'],
						$body['redirectUrl'],
						$body['validUntil']
					);
                }
            }
        } catch (GuzzleException $e) {
            return new ApiError($e->getCode(), $e->getMessage());
        }
		echo "Invalid Response: " . "No valid response received.". "\n";
        return new ApiError('Invalid Response', 'No valid response received.');
	}


	/**
	 * @param $r $_REQUEST
	 * @return OrderCallback|null
	 */
	public function parseCreateOrderCallback($r){
		$callback = new OrderCallback($r['userId'], $r['merchantApiId'], $r['merchantId'], $r['apiId'], $r['orderId'], $r['payCurrency'], $r['payAmount'], $r['receiveCurrency'], $r['receiveAmount'], $r['receivedAmount'], $r['description'], $r['orderRequestId'], $r['status'], $r['sign']);
		return $callback;
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

}
