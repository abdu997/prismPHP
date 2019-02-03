<?php

namespace Utilities;

/**
 * Includes all mail Prism objects.
 *
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
  /**
   * Replaces links in string with a clickable button link. Developer
   * may input primary colour by filling out the mail prism config.
   *
   * @param  string $text message
   * @return string
   */
  private static function makeLinksClickable($text){
    return preg_replace(
      '!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;/=]+)!i', "<center><a target='_blank' style='text-decoration: underline; cursor: pointer;' href='$1'><button style='background: ".$GLOBALS['primary_colour']."; border: 0; padding: 5px; width: 75px; color: white; margin: 20px 0;'>Link</button></a></center>",
      $text
    );
  }

  /**
   * Defines mail headers and sends an email using the prism template. Developer
   * may input background colour, primary colour, secondary colour and logo url
   * by filling out the mail prism config.
   *
   * @param  string $to      email
   * @param  string $subject
   * @param  string $message
   */
  public static function enable($to, $subject, $message)
  {
    $message = "
    <html style='background: white'>
      <center>
        <table style='max-width: 600px; width: 100%; margin-top: 25px;'>
          <tr>
            <td style='background: ".$GLOBALS['primary_colour']."; height: 75px' align='center'>
              <img alt='logo' style='height: 75px' src='".$GLOBALS['logo_url']."' />
            </td>
          </tr>
          <tr style='height: 500px;'>
            <td valign='top' style=' background: ".$GLOBALS['secondary_colour']."; color: ".$GLOBALS['primary_colour']."; font-size: 18px; padding: 25px; border: 1px solid ".$GLOBALS['primary_colour'].";'>
              <br/>
              ".self::makeLinksClickable($message)."
            </td>
          </tr>
        </table>
      </center>
    </html>";
    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = $GLOBALS['host'];
      $mail->SMTPAuth = true;
      $mail->Username = $GLOBALS['email'];
      $mail->Password = $GLOBALS['password'];
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;
      $mail->setFrom($mail->Username, $GLOBALS['from_name']);
      $mail->addAddress($to, $to);
      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body = $message;
      $mail->send();
    } catch (Exception $e) {
      trigger_error('Message could not be sent. Mailer Error: '. $mail->ErrorInfo, E_USER_ERROR);
    }
  }
}
