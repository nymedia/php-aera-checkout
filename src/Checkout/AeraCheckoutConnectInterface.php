<?php

namespace AeraCheckoutApi\Checkout;

/**
 * Interface ConnectInterface
 * @package AaraCheckoutApi\Checkout
 */
interface AeraCheckoutConnectInterface
{

  /**
   * @param string $service
   * @return string
   */
  public function getBaseUrl($service = '');

  /**
   * @param $url
   * @param $post_data
   * @return mixed
   *
   * @throws AeraCheckoutException
   */
  public function request($url, $post_data);

  /**
   * Execute the API request with an XML data.
   *
   * @param string $url
   *   Endpoint host URL.
   * @param string $parent
   *   XML parent tag.
   * @param array $data
   *   Array of parameters which should be convert to the XML.
   *
   * @return array
   *   Response array.
   *
   * @throws AeraCheckoutException
   */
  public function xmlRequest($url, $parent = '', array $data = array());

}
