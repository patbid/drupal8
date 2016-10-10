<?php

namespace Drupal\Tests\flag\Kernel;

use Drupal\node\Entity\Node;
use Drupal\flag\Entity\Flag;

/**
 * Tests default hook_flag_action_access() can be overriden by other modules.
 *
 * @group flag
 */
class AccessTest extends FlagKernelTestBase {

  /**
   * Alice has all permission.
   *
   * @var \Drupal\user\Entity\User|false
   */
  protected $userAlice;

  /**
   * Bob has no permission.
   *
   * @var \Drupal\user\Entity\User|false
   */
  protected $userBob;

  /**
   *
   * @var \Drupal\user\Entity\User|false
   */
  protected $userJill;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installSchema('user', 'users_data');

    // The first user is uid 1, create that to avoid that our test users
    // implicitly have all permissions even those that don't exist.
    $this->createUser();
  }

  /**
   * Tests default hook_flag_action_access() mechanism.
   */
  public function testDefault() {
    // Create a flag.
    $flag = Flag::create([
      'id' => 'example',
      'label' => $this->randomString(),
      'entity_type' => 'node',
      'bundles' => ['article'],
      'flag_type' => 'entity:node',
      'link_type' => 'reload',
      'flagTypeConfig' => [],
      'linkTypeConfig' => [],
    ]);
    $flag->save();

    // Create a user who may flag and unflag.
    $user_alice = $this->createUser([
      'administer flags',
      'flag example',
      'unflag example',
    ]);

    // Create a user who may only flag.
    $user_jill = $this->createUser([
      'administer flags',
      'flag example',
    ]);

    // Create a user who may not flag.
    $user_bob = $this->createUser();

    $article = Node::create([
      'type' => 'article',
      'title' => 'Article node',
    ]);
    $article->save();

    // Test with both permissions.
    $this->assertTrue($flag->actionAccess('flag', $user_alice, $article)->isAllowed(), 'Alice can flag.');
    $this->assertTrue($flag->actionAccess('unflag', $user_alice, $article)->isAllowed(), 'Alice can unflag.');

    // Test with only flag permission.
    $this->assertTrue($flag->actionAccess('flag', $user_jill, $article)->isAllowed(), 'Alice can flag.');
    $this->assertTrue($flag->actionAccess('unflag', $user_jill, $article)->isNeutral(), 'Alice can unflag.');

    // Test without permissions.
    $this->assertTrue($flag->actionAccess('flag', $user_bob, $article)->isNeutral(), 'Bob cannot flag.');
    $this->assertTrue($flag->actionAccess('unflag', $user_bob, $article)->isNeutral(), 'Bob cannot unflag..');
  }

  /**
   * User permissions.
   */
  public function testUserFlag() {
    // Cannot flag yourself.
    $selfiesFlag = Flag::create([
      'id' => 'selfies',
      'label' => $this->randomString(),
      'entity_type' => 'user',
      'flag_type' => 'entity:user',
      'link_type' => 'reload',
      'flagTypeConfig' => [
        // Sefies permitted.
        'access_uid' => TRUE,
      ],
      'linkTypeConfig' => [],
    ]);
    $selfiesFlag->save();

    // Self flagging permitted.
    $noSelfiesFlag = Flag::create([
      'id' => 'no_selfies',
      'label' => $this->randomString(),
      'entity_type' => 'user',
      'flag_type' => 'entity:user',
      'link_type' => 'reload',
      'flagTypeConfig' => [
        // Deny selfies.
        'access_uid' => FALSE,
      ],
      'linkTypeConfig' => [],
    ]);
    $noSelfiesFlag->save();

    // Create a user who may flag.
    $user_alice = $this->createUser([
      'administer flags',
      'flag selfies',
      'flag no_selfies',
    ]);

    // Create a user who may not flag.
    $user_bob = $this->createUser();

    // What happens when selfies are permitted.
    $this->assertTrue($selfiesFlag->actionAccess('flag', $user_alice, $user_alice)->isAllowed());
    $this->assertTrue($selfiesFlag->actionAccess('flag', $user_alice, $user_bob)->isAllowed());

    // What happens when selfies are banned.
    $this->assertTrue($noSelfiesFlag->actionAccess('flag', $user_alice, $user_alice)->isNeutral());
    $this->assertTrue($noSelfiesFlag->actionAccess('flag', $user_alice, $user_bob)->isAllowed());

  }

}
