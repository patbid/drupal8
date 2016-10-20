<?php

namespace Drupal\annonce\EventSubscriber;

use Drupal\Core\Database\Driver\mysql\Connection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Drupal\Core\Session\AccountProxy;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * Class AnnonceEventDispatcher.
 *
 * @package Drupal\annonce
 */
class AnnonceEventDispatcher implements EventSubscriberInterface {

  protected $current_user;
  protected $current_route_match;
    protected $ma_base;

  /**
   * Constructor.
   */
  public function __construct(AccountProxy $current_user, CurrentRouteMatch $current_route_match, Connection $connection ) {
    $this->current_user = $current_user;
    $this->current_route_match = $current_route_match;
      $this->ma_base = $connection;
  }

  public function DisplayUserName() {
      if( $this->current_route_match->getRouteName() =='entity.annonce.canonical') {
          $my_obj = $this->current_route_match->getRawParameter('annonce');
          $this->ma_base->insert('annonce_history')->fields(
              array(
                  'aid' => $my_obj,
                  'uid' =>  $this->current_user->id(),
                  'update_time' => time()
              )
          )->execute();
      }
  }


  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
      $events[KernelEvents::REQUEST][]=array('DisplayUserName');
    return $events;
  }


}
