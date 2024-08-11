<?php

namespace Drupal\custom_url_prefix\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;


class CustomUrlPrefixSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Define the event to subscribe to.
    $events[KernelEvents::RESPONSE][] = ['onResponse', -100];
    return $events;
  }

  /**
   * Modifies the HTML response to add a prefix to URLs.
   *
   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
   *   The event object.
   */
  public function onResponse(ResponseEvent $event) {
    $response = $event->getResponse();
    $redirect_codes = [301, 302, 303, 307, 308];

    if (in_array($response->getStatusCode(), $redirect_codes)) {
      $location = $response->headers->get('Location');
      $port = $_ENV['CONTAINER_PORT'] ?? 5551;
      $prefix = $_ENV['APP_PREFIX'] ?? '/dashboard';
      $newval = str_replace('localhost/', 'localhost:' . $port . $prefix . '/', $location);
      $response->headers->set('Location', $newval);
    }

    // Check if the response content type is HTML.
    if (!str_contains($response->headers->get('Content-Type'), 'text/html')) {
      return;
    }

    // Get the response content and modify URLs.
    $content = $response->getContent();

    // replace href="/*" << if it's not already prefixed with /dashboard then add it
    // target only <a> tag do not target other tag
    // $content = preg_replace('/<a\s+[^>]*href="(?!\/dashboard)([^"]*)"/i', '<a href="/dashboard/$1"', $content);


    // Inject JavaScript to modify <a> tags.
    $script = <<<EOT
<script>
document.addEventListener('DOMContentLoaded', function() {
  var links = document.querySelectorAll('a[href^="/"]:not([href^="APP_PREFIX"])');
  links.forEach(function(link) {
    link.href = 'APP_PREFIX' + link.getAttribute('href');
  });
  var forms = document.querySelectorAll('form[action^="/"]:not([action^="APP_PREFIX"])');
  forms.forEach(function(formx) {
    formx.action = 'APP_PREFIX' + formx.getAttribute('action');
  });
});
</script>
EOT;

    $app_prefix = $_ENV['APP_PREFIX'];
    $script = str_replace('APP_PREFIX', $app_prefix, $script);

    // Insert the script before the closing </body> tag.
    $content = preg_replace('/<\/body>/i', $script . '</body>', $content);


    // Set the modified content back to the response.
    $response->setContent($content);
  }
}
