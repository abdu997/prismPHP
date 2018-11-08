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
  public static function sendSMS($phone_number, $message, $first_name, $last_name)
  {
    $client = new Client(Config::$sid, Config::$sid);
    $client->messages->create(
      "+1".$phone_number,
      [
        'from' => Config::$number,
        'body' => "Hello ".$first_name." ".$last_name.", ".$message." -Thawrih Intra",
      ]
    );
  }
}
