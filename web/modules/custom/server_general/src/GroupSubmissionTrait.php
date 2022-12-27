<?php

namespace Drupal\server_general;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\og\Og;
use Drupal\og\MembershipManagerInterface;

/**
 * Trait GroupSubmissionTrait.
 *
 * Helper method for building a Processed text (e.g. a body field).
 */
trait GroupSubmissionTrait {

  /**
   * Build a (processed) text of the content.
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   The entity.
   *
   * @return array
   *   Render array.
   */
  protected function buildGroupSubmission(FieldableEntityInterface $entity, string $field = 'og_group') : array {
    $current_user = \Drupal::currentUser();
    if (!$entity->bundle() == 'group' || $current_user->isAnonymous()) {
      // If not a group or not logged-in user
      return [];
    }
    // Hide the label
    $options = ['label' => 'hidden'];
    
    if (!Og::getMembership($entity, $current_user)) {
      // If not a member of the group show the modal
      $element = [];
      $tmp = $entity
        ->get($field)->value;

      $button = t(
        'Hi @name, click here if you would like to subscribe to this group called @label.',
        [
          '@name' => $current_user->getDisplayName(),
          '@label' => $entity->title->value,
        ]);

      $button .= \Drupal\Core\Render\Markup::create(
        \Drupal::service('renderer')
        ->render($entity
          ->get($field)
          ->view($options)
        )
      )->__toString();
      $element = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $button,
        '#attributes' => [
          'class' => ['hidden', 'modal-trigger'],
        ],
      ];
    } else {
      // otherwise will display membership status with the label hidden
      $element = $entity->get($field)->view($options);
    }
    
    return $element;
  }

}
