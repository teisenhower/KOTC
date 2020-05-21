<?php

namespace App\Services;

use Psr\Container\ContainerInterface;

class SendEmail
{
    private $container;
    private $apiKey;

    public function __construct(ContainerInterface $container, $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->container = $container;
    }
    public function sendRegistrationEmail($userEmail, $firstName, $lastName)
    {
        $email = new \SendGrid\Mail\Mail();
        $fullName = $firstName . ' ' . $lastName;
        $email->setFrom('terrence@teisenhower.dev', 'Welcome');
        $email->setSubject('Welcome to KOTC ' . $firstName . '!');
        $email->addTo($userEmail, $fullName);
        $email->addContent('text/plain', 'We\'re glad you\'re here ' . $firstName . '!');
        $email->addContent(
            'text/html',
            '<strong> We\'re glad you\'re here ' . $firstName . '!</strong>'
        );
        $this->sendMail($email);
    }
    public function sendRecoveryEmail($userEmail, $firstName, $token)
    {
        $message = '  <html>
        <head>
          <title></title>
        </head>
        <body>
          <div data-role="module-unsubscribe" class="module" role="module" data-type="unsubscribe" style="color:#444444; font-size:12px; line-height:20px; padding:16px 16px 16px 16px; text-align:Center;" data-muid="4e838cf3-9892-4a6d-94d6-170e474d21e5">
            <div class="Unsubscribe--addressLine">
              <p class="Unsubscribe--senderName"
                style="font-size:12px;line-height:20px"
              >
              ' . $firstName . ' 
              </p>
              <a href="http://kotc.local/reset_password/' . $token . '">Reset Password</a>
              <p style="font-size:12px;line-height:20px">
                <span class="Unsubscribe--senderAddress">{{Sender_Address}}</span>, <span class="Unsubscribe--senderCity">{{Sender_City}}</span>, <span class="Unsubscribe--senderState">{{Sender_State}}</span> <span class="Unsubscribe--senderZip">{{Sender_Zip}}</span>
              </p>
            </div>
            <p style="font-size:12px; line-height:20px;">
              <a class="Unsubscribe--unsubscribeLink" href="{{{unsubscribe}}}" target="_blank" style="font-family:sans-serif;text-decoration:none;">
                Unsubscribe
              </a>
              -
              <a href="{{{unsubscribe_preferences}}}" target="_blank" class="Unsubscribe--unsubscribePreferences" style="font-family:sans-serif;text-decoration:none;">
                Unsubscribe Preferences
              </a>
            </p>
          </div>
        </body>
      </html>';
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom('terrence@teisenhower.dev', 'Welcome');
        $email->setSubject('Reset Password Request!');
        $email->addTo($userEmail, $firstName);
        $email->addContent(
            'text/html',
            $message
        );
        $this->sendMail($email);
    }
    public function sendMail($email)
    {
        $sendgrid = new \SendGrid($this->apiKey);
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (\Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }
}
