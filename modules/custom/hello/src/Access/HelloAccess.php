<?php

namespace Drupal\hello\Access;

use Drupal\Core\Access\AccessCheckInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

class HelloAccess implements AccessCheckInterface {

    public function applies(Route $route) {
        // TODO: Implement applies() method.
        return NULL;
    }

    public function access(Route $route, Request $request = NULL, AccountInterface $account) {

        return AccessResult::allowedIf((time()-$account->getAccount()->created) > $route->getRequirement('_access_hello'));
    }
}