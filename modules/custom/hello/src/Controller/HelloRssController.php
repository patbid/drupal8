<?php


namespace Drupal\hello\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

class HelloRssController extends ControllerBase {
  public function content() {
	$response = new Response();
	$response->setContent('<rss version="2.0"><channel><title>Hello</title><link>http://www.xul.fr</link><description>ceci est mon hello rss</description></channel></rss>');
  	return $response;
  }
}
