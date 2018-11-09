<?php

namespace Prism;

// Twilio library

use Twilio\Rest\Client;

class Twilio
{
  /**
   * Sends sms mesage using thawrih's twilio number to a number concatenated with the area code. Message is also concatenated with a signature.
   *
   * @param  string $phone_number
   * @param  string $message
   */
  public static function sendSMS($phone_number, $message)
  {
    $client = new Client($GLOBALS['sid'], $GLOBALS['token']);
    $client->messages->create(
      "+1".$phone_number,
      [
        'from' => $GLOBALS['number'],
        'body' => $message,
      ]
    );
  }
}
