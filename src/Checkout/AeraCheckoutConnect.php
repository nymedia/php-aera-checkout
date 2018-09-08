<?php

namespace AeraCheckoutApi\Checkout;

class AeraCheckoutConnect implements AeraCheckoutConnectInterface {
  const BASE_URL = 'https://ips-preprod.ihost.com';
  const BASE_PORT = '50443';

  protected $certPath;
  protected $certPass;
  protected $settings;

  public function __construct($cert_path, $cert_pass = '', array $settings = array())
  {
    $this->certPath = $cert_path;
    $this->certPass = $cert_pass;
    $this->settings = $settings;
  }

  public function getBaseUrl($service = '')
  {
    $base_url = self::BASE_URL;
    $base_url .= !empty(self::BASE_PORT) ? ':' . self::BASE_PORT : '';
    $base_url .= !empty($service) ? '/' . $service : '';
    return $base_url;
  }

  /**
   * @param $url
   * @param null $post_data
   * @param array $header
   * @return mixed
   * @throws AeraCheckoutException
   */
  public function request($url, $post_data = NULL, array $header = array())
  {
    // Initialise cURL
    $ch = curl_init($url);

    // Set the URL.
    curl_setopt($ch, CURLOPT_URL, $url);

    // Return transfer.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // The --cacert option.
    curl_setopt($ch, CURLOPT_CAINFO, $this->certPath);

    // The --cert option.
    curl_setopt($ch, CURLOPT_SSLCERT, $this->certPath);

    // Set certificate password if necessary.
    if (!empty($this->certPass)) {
      curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->certPass);
    }

    // Set the header.
    if (!empty($header)) {
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }

    // POST request.
    if (!empty($post_data)) {
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    }

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === FALSE) {
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      throw new AeraCheckoutException('Request Error ' . $http_code. ': ' . curl_errno($ch), $http_code);
    }
    return $response;
  }

  /**
   * Execute the API request with an XML data.
   *
   * @inheritdoc
   *
   * @throws AeraCheckoutException
   */
  public function xmlRequest($url, $parent = '', array $data = array())
  {
    if (!empty($data)) {
      $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>' . $parent);
      $this->arrayToXML($data, $xml);

      if (!($data = $xml->asXML())) {
        throw new AeraCheckoutException("Failed to create XML from specified data", 400);
      }
    }

    $response = $this->request($url, $data, array('Content-Type: text/xml'));
    $response_xml = simplexml_load_string($response);
    $response_array = json_decode(json_encode($response_xml), true);

    // Check if response contain errors.
    if ($response_xml->getName() == 'ERR') {
      $message = $this->getErrorMessage($response_array);
      throw new AeraCheckoutException($message, 400);
    } elseif (isset($response_array['RES']['RCD']) && $response_array['RES']['RCD'] != 0) {
      $message = $this->getErrorMessage($response_array['RES']);
      throw new AeraCheckoutException($message, 400);
    }
    return $response_array;
  }

  /**
   * Adds array to the XML recursively.
   *
   * @param array $data
   *   Data.
   * @param \SimpleXMLElement $xml_data
   *   Simple XML element.
   */
  private function arrayToXML(array $data, &$xml_data)
  {
    foreach ($data as $key => $value) {
      if (is_array($value)) {
        $child = TRUE;
        foreach ($value as $k => $v) {
          if (!is_numeric($k) || !is_array($v)) {
            $child = FALSE;
            break;
          }
        }

        if ($child) {
          foreach ($value as $k => $v) {
            $sub = $xml_data->addChild($key);
            $this->arrayToXML($v, $sub);
          }
        }
        else {
          $sub = $xml_data->addChild($key);
          $this->arrayToXML($value, $sub);
        }
      } else {
        $xml_data->addChild("$key", htmlspecialchars("$value"));
      }
    }
  }

  /**
   * Get error message string from the response.
   *
   * @param array $response
   *   Array with the response values.
   *
   * @return string
   *   Error message.
   */
  private function getErrorMessage($response)
  {
    $message = array();
    if (isset($response['MSG'])) {
      $message[] = $response['MSG'] . (isset($response['RCD']) ? ' (' . $response['RCD'] . ')' : '');
    } elseif (isset($response['RCD'])) {
      $message[] = 'Return Code: ' . $response['RCD'];
    }
    if (isset($response['XID'])) {
      $message[] = 'Error ID: ' . $response['XID'];
    }
    if (isset($response['XIE'])) {
      $message[] = 'External ID: ' . $response['XIE'];
    }
    return implode(", ", $message);
  }

}
