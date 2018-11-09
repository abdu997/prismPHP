<?php

namespace Prism;

/**
 * Includes all mail Prism.
 *
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
  /**
   * Replaces links in string with a clickable button link.
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
   * Defines mail headers and sends an email using the corporate template. Asset address are relative based on the current origin. If the env is localhost, email will not be sent.
   *
   * @param  string $to      email
   * @param  string $subject
   * @param  string $message
   */
  public static function enable($to, $subject, $message)
  {
    $message = "
    <html style='background: ".$GLOBALS['bg_colour']."'>
      <center>
        <table style='max-width: 600px; width: 100%; margin-top: 25px;'>
          <tr>
            <td style='background: ".$GLOBALS['primary_colour']."; height: 75px' align='center'>
              <img alt='logo' style='height: 75px' src='".$GLOBALS['logo_url']."' />
            </td>
          </tr>
          <tr style='height: 500px;'>
            <td valign='top' style=' background: ".$GLOBALS['secondary_colour']."; color: ".$GLOBALS['primary_colour']."; font-size: 18px; padding: 25px; border: 1px solid ".$GLOBALS['primary_colour'].";'>
              Hello ".$first_name." ".$last_name."!
              <br/>
              <br/>
              ".self::makeLinksClickable($message)."
              <center>
                <a target='_blank' style='text-decoration: underline; cursor: pointer;' href='".$GLOBALS['website_url']."'>
                  <button style='background: ".$GLOBALS['primary_colour']."; border: 0; padding: 5px; width: 75px; color: white; margin: 20px 0;'>
                  Got to Intra
                  </button>
                </a>
              </center>
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
      trigger_error('Message could not be sent. Mailer Error: '. $mail->ErrorInfo);
    }
  }
}
