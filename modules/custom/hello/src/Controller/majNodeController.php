<?php


namespace Drupal\hello\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;

class majNodeController extends ControllerBase {
    public function content(NodeInterface $node) {
        $db = \Drupal::database();

        $res = $db->select('hello_node_history','s')
                ->fields('s', array('uid', 'update_time'))
                ->condition('s.nid', $node->id())
                ->execute()
                ->fetchAll();
        $storage = \Drupal::entityTypeManager()->getStorage('user');
        $content = array();
        foreach ($res as $row) {
            $content[] = array($storage->load($row->uid)->getAccountName(),date('m/d/Y - H:i:s', $row->update_time));
        }
        $output = array(
            '#theme' => 'hello-node-history', '#node' => $node->getTitle(), '#count' => count($res)
        );
        $headers = array("User","Date");
        return array($output, array('#theme' => 'table', '#header' => $headers, '#rows' => $content));
    }
}
