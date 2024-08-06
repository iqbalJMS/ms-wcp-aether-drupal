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

    // Check if the response content type is HTML.
    if (strpos($response->headers->get('Content-Type'), 'text/html') === false) {
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
  var links = document.querySelectorAll('a[href^="/"]:not([href^="/dashboard"])');
  links.forEach(function(link) {
    link.href = '/dashboard' + link.getAttribute('href');
  });
  var forms = document.querySelectorAll('form[action^="/"]:not([action^="/dashboard"])');
  forms.forEach(function(formx) {
    formx.action = '/dashboard' + formx.getAttribute('action');
  });
});
</script>
EOT;

    // Insert the script before the closing </body> tag.
    $content = preg_replace('/<\/body>/i', $script . '</body>', $content);


    // Set the modified content back to the response.
    $response->setContent($content);
  }
}
