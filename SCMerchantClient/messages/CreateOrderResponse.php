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
	function __construct($depositAddress, $memo, $orderId, $payAmount, $payCurrency, $payNetworkName, $preOrderId, $receiveAmount, $receiveCurrency, $redirectUrl, $validUntil)
	{
		$this->depositAddress = $depositAddress;
		$this->memo = $memo;
		$this->orderId = $orderId;
		$this->payAmount = $payAmount;
		$this->payCurrency = $payCurrency;
		$this->payNetworkName = $payNetworkName;
		$this->preOrderId = $preOrderId;
		$this->receiveAmount = $receiveAmount;
		$this->receiveCurrency = $receiveCurrency;
		$this->redirectUrl = $redirectUrl;
		$this->validUntil = $validUntil;
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