<?php

namespace AeraCheckoutApi\Checkout;

/**
 * Class AeraCheckoutHostedSession
 * @package AeraCheckoutApi\Checkout
 */
class AeraCheckoutHostedSession
{

  /**
   * @var AeraCheckoutConnectInterface
   */
  protected $connect;

  /**
   * AeraCheckoutHostedSession constructor.
   * @param AeraCheckoutConnectInterface $connect
   */
  public function __construct(AeraCheckoutConnectInterface $connect)
  {
    $this->connect = $connect;
  }

  /**
   * Retrieving hosted payment sessions
   *
   * @param string $session_id
   *   The ID of the session to retrieve.
   *
   * @return array
   *   Response array.
   *
   * @throws AeraCheckoutException
   *
   * @see: https://docs.ips.ihost.com/api/?json#retrieving-hosted-payment-sessions
   */
  public function getSession($session_id)
  {
    return $this->connect->xmlRequest($this->connect->getBaseUrl('ses/' . $session_id . '?view=status'));
  }

  /**
   * Create hosted payment session
   *
   * @param array $data
   *   Hosted session parameters.
   *
   * @return array
   *   Response array.
   *
   * @throws AeraCheckoutException
   *
   * @see: https://docs.ips.ihost.com/api/?json#create-hosted-payment-session
   */
  public function startSession(array $data)
  {
    $data['SVC'] = 'CheckoutStartSession';
    return $this->connect->xmlRequest($this->connect->getBaseUrl('trx'), '<TRX/>', $data);
  }

}
