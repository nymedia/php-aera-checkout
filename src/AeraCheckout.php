<?php

// @todo: REMOVE, load via autoloader

define('AERA_CHECKOUT_DIR', dirname(__FILE__) . '/Checkout');

require_once AERA_CHECKOUT_DIR . '/AeraCheckoutConnectInterface.php';
require_once AERA_CHECKOUT_DIR . '/AeraCheckoutConnect.php';
require_once AERA_CHECKOUT_DIR . '/AeraCheckoutHostedSession.php';
require_once AERA_CHECKOUT_DIR . '/AeraCheckoutTransaction.php';
require_once AERA_CHECKOUT_DIR . '/AeraCheckoutException.php';
