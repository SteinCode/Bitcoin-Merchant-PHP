<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

include_once('components/FormattingUtil.php');
include_once('data/ApiError.php');
include_once('data/OrderStatusEnum.php');
include_once('data/OrderCallback.php');
include_once('messages/CreateOrderRequest.php');
include_once('messages/CreateOrderResponse.php');
include_once('./utilities.php');


require_once __DIR__ . '/../vendor/autoload.php';


class SCMerchantClient
{

	private $merchantApiUrl;
	private $projectId;

	private $clientId;
	private $clientSecret;
	private $authUrl;

	private $accessTokenData;

	protected $client;

	/**
	 * @param $merchantApiUrl
	 * @param $projectId
	 */
	function __construct($merchantApiUrl, $projectId, $clientId, $clientSecret, $authUrl, $accessTokenData = null)
	{
		$this->merchantApiUrl = $merchantApiUrl;
		$this->projectId = $projectId;
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
		$this->authUrl = $authUrl;
		$this->accessTokenData = $accessTokenData;

		$this->client = new Client();
	}

	/**
	 * @param CreateOrderRequest $request
	 * @return ApiError|CreateOrderResponse
	 */
	public function createOrder(CreateOrderRequest $request){
		$accessTokenArray = $this->getAccessTokenArray();

		if (!$accessTokenArray) {
			return new ApiError('AuthError', 'Failed to obtain access token');
		}

		$payload = array(
			"callbackUrl" => $request->getCallbackUrl(),
			"description" => $request->getDescription(),
			"failureUrl" => $request->getFailureUrl(),
			"lang" => $request->getLang(),
			"orderId" => $request->getOrderId(),
			"payAmount" => $request->getPayAmount(),
			"payCurrencyCode" => $request->getPayCurrencyCode(),
			"payNetworkName" => $request->getPayNetworkName(),
			"payerDateOfBirth" => $request->getPayerDateOfBirth(),
			"payerEmail" => $request->getPayerEmail(),
			"payerName" => $request->getPayerName(),
			"payerSurname" => $request->getPayerSurname(),
			"projectId" => $this->projectId,
			"receiveAmount" => $request->getReceiveAmount(),
			"receiveCurrencyCode" => $request->getReceiveCurrencyCode(),
			"successUrl" => $request->getSuccessUrl(),
		);

		$jsonPayload = json_encode($payload);

        try {
            $response = $this->client->request('POST', $this->merchantApiUrl . '/merchants/orders/create', [
                RequestOptions::HEADERS => [
					'Content-Type' => 'application/json',
					'Authorization' => 'Bearer ' . $accessTokenArray['access_token']
			],
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
        return new ApiError('Invalid Response', 'No valid response received.');
	}

		/**
	 * @param $merchantApiUrl
	 * @param $projectId
	 * @param bool $debug
	 * @return SCMerchantClient
	 */
	public function getAccessTokenArray() {
		$currentTime = time();

		if (isset($_SESSION['encryptedAccessTokenData'])) {
			$encryptedTokenData = $_SESSION['encryptedAccessTokenData'];
			$this->accessTokenData = json_decode(decrypt($encryptedTokenData, 'your_secret_encryption_key'), true);
		}
	
		if ($this->accessTokenData && isset($this->accessTokenData['expires_at']) && $currentTime < ($this->accessTokenData['expires_at'] - 60)) {
			return $this->accessTokenData;
		}
	
		try {
			$response = $this->client->post($this->authUrl, [
				'form_params' => [
					'grant_type' => 'client_credentials',
					'client_id' => $this->clientId,
					'client_secret' => $this->clientSecret,
				],
			]);
	
			$data = json_decode($response->getBody(), true);
			if (!isset($data['access_token'], $data['expires_in'])) {
				echo 'Invalid token response';
				return null;
			}
	
			$data['expires_at'] = $currentTime + $data['expires_in'];
			$this->accessTokenData = $data;

			$_SESSION['encryptedAccessTokenData'] = encrypt(json_encode($data), 'your_secret_encryption_key');

			return $this->accessTokenData;
		} catch (GuzzleException $e) {
			echo 'Failed to get access token: ' . $e->getMessage();
			return null;
		}
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
				'payCurrency' => $c->getPayCurrencyCode(),
				'payAmount' => $c->getPayAmount(),
				'receiveCurrency' => $c->getReceiveCurrencyCode(),
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
