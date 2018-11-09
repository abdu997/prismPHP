<?php

namespace Prism;

// Twilio library

use Twilio\Rest\Client;

class Twilio
{
  /**
   * Sends sms mesage using a twilio number to a number passed as an arg.
   *
   * @param  string $phone_number
   * @param  string $message
   */
  public static function sendSMS($phone_number, $message)
  {
    $client = new Client($GLOBALS['sid'], $GLOBALS['token']);
    $client->messages->create(
      $phone_number,
      [
        'from' => $GLOBALS['number'],
        'body' => $message,
      ]
    );
  }
}
