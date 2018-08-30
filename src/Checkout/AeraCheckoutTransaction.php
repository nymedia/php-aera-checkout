<?php

namespace AeraCheckoutApi\Checkout;

/**
 * Class AeraCheckoutTransaction
 * @package AeraCheckoutApi\Checkout
 */
class AeraCheckoutTransaction
{

  /**
   * @var AeraCheckoutConnectInterface
   */
  protected $connect;

  /**
   * @var string
   */
  protected $url;

  /**
   * AeraCheckoutTransaction constructor.
   * @param AeraCheckoutConnectInterface $connect
   */
  public function __construct(AeraCheckoutConnectInterface $connect)
  {
    $this->connect = $connect;
    $this->url = $connect->getBaseUrl('trx');
  }

  /**
   * Capture transaction.
   *
   * @param string $transaction_id
   *   Transaction reference.
   * @param array $data
   *   Data of the transaction to refund.
   *
   * @return array
   *   Response array.
   *
   * @throws AeraCheckoutException
   *
   * @see: https://docs.ips.ihost.com/api/?json#capture
   */
  public function capture($transaction_id, array $data)
  {
    $data['SVC'] = 'Capture';
    $data['XRF'] = $transaction_id;
    return $this->connect->xmlRequest($this->url, '<TRX/>', $data);
  }

  /**
   * Refund transaction.
   *
   * @param string $transaction_id
   *   Transaction reference.
   * @param array $data
   *   Data of the transaction to refund.
   *
   * @return array
   *   Response array.
   *
   * @throws AeraCheckoutException
   *
   * @see: https://docs.ips.ihost.com/api/?json#refund
   */
  public function refund($transaction_id, array $data)
  {
    $data['SVC'] = 'Refund';
    $data['XRF'] = $transaction_id;
    return $this->connect->xmlRequest($this->url, '<TRX/>', $data);
  }

  /**
   * Void transaction.
   *
   * @param string $transaction_id
   *   Transaction reference.
   * @param array $data
   *   Data of the transaction to refund.
   *
   * @return array
   *   Response array.
   *
   * @throws AeraCheckoutException
   *
   * @see: https://docs.ips.ihost.com/api/?json#void62
   */
  public function void($transaction_id, array $data = array())
  {
    $data['SVC'] = 'Void';
    $data['XRF'] = $transaction_id;
    return $this->connect->xmlRequest($this->url, '<TRX/>', $data);
  }

}
