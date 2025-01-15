<?php

declare(strict_types=1);

namespace Drupal\bricc\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @todo Add description for this subscriber.
 */
final class BriccDecryptPasswordSubscriber implements EventSubscriberInterface {

  /**
   * Kernel request event handler.
   */
  public function onKernelRequest(RequestEvent $event): void {
    $request = $event->getRequest();

    // Check if the request is for the user login form.
    if ($request->attributes->get('_route') === 'user.login') {
      // Check if the 'encrypted_password' field exists in the POST data.
      if ($request->request->has('encrypted_password')) {
        $encryptedPassword = $request->request->get('encrypted_password');

        // Decrypt the password (replace this with your actual decryption logic).
        $decryptedPassword = $this->decryptPassword($encryptedPassword);

        // Replace the 'pass' field in the POST data with the decrypted password.
        $request->request->set('pass', $decryptedPassword);
      }
    }
  }

  /**
   * Kernel response event handler.
   */
  public function onKernelResponse(ResponseEvent $event): void {
    // @todo Place your code here.
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => ['onKernelRequest'],
      KernelEvents::RESPONSE => ['onKernelResponse'],
    ];
  }

  private function decryptPassword(float|bool|int|string|null $encryptedPassword
  ) {
    // Load private key from env
    $privateKeyString =  <<< STR

STR;

    // Load the private key from the string
    $keyResource = openssl_pkey_get_private($privateKeyString);

    if (!$keyResource) {
      die('Private key is invalid or cannot be loaded.');
    }

    // Base64 decode the encrypted message
    $encryptedMessage = base64_decode($encryptedPassword);

    // Decrypt the message using the private key
    $decryptedMessage = '';

    if (openssl_private_decrypt($encryptedMessage, $decryptedMessage, $keyResource)) {
      return $decryptedMessage;
    }

    return FALSE;
  }


}
