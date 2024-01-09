<?php

class CreateOrderResponse
{

	private $depositAddress;
	private $memo;
	private $orderId;
	private $payAmount;
	private $payCurrency;
	private $payNetworkName;
	private $preOrderId;
	private $receiveAmount;
	private $receiveCurrency;
	private $redirectUrl;
	private $validUntil;

	/**
	 * @param $data
	 */
	function __construct($data)
	{
		$this->depositAddress = $data->depositAddress;
		$this->memo = $data->memo;
		$this->orderId = $data->orderId;
		$this->payAmount = $data->payAmount;
		$this->payCurrency = $data->payCurrency;
		$this->payNetworkName = $data->payNetworkName;
		$this->preOrderId = $data->preOrderId;
		$this->receiveAmount = $data->receiveAmount;
		$this->receiveCurrency = $data->receiveCurrency;
		$this->redirectUrl = $data->redirectUrl;
		$this->validUntil = $data->validUntil;
	}
	

	/**
	 * @return String
	 */
	public function getDepositAddress()
	{
		return $this->depositAddress;
	}

	/**
	 * @return String
	 */
	public function getMemo()
	{
		return $this->memo;
	}

	/**
	 * @return String
	 */
	public function getOrderId()
	{
		return $this->orderId;
	}

	/**
	 * @return Integer
	 */
	public function getPayAmount()
	{
		return $this->payAmount;
	}

	/**
	 * @return String
	 */
	public function getPayCurrency()
	{
		return $this->payCurrency;
	}

	/**
	 * @return String
	 */
	public function getPayNetworkName()
	{
		return $this->payNetworkName;
	}

	/**
	 * @return String
	 */
	public function getPreOrderId()
	{
		return $this->preOrderId;
	}

	/**
	 * @return Float
	 */
	public function getReceiveAmount()
	{
		return $this->receiveAmount;
	}

	/**
	 * @return String
	 */
	public function getReceiveCurrency()
	{
		return $this->receiveCurrency;
	}

	/**
	 * @return String
	 */
	public function getRedirectUrl()
	{
		return $this->redirectUrl;
	}

	/**
	 * @return String
	 */
	public function getValidUntil()
	{
		return $this->validUntil;
	}
}