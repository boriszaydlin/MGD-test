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
      // If not a member of the group show the message
      $element = [];

      // url language version prefix
      $languagecode = \Drupal::languageManager()->getCurrentLanguage()->getId();
      $default_languagecode = \Drupal::languageManager()->getDefaultLanguage()->getId();
      if ($languagecode == $default_languagecode) {
        $languagecode = '';
      } else {
        $languagecode = '/' . $languagecode;
      }

      // group submission url
      $here = $languagecode . '/group/node/' . $entity->id() . '/subscribe';
      
      $message = t(
        'Hi @name, click <a href="@here" class="font-bold">here</a> if you would like to subscribe to this group called @label.',
        [
          '@name' => ucfirst($current_user->getDisplayName()),
          '@label' => ucfirst($entity->title->value),
          '@here' => $here,
        ]
      );
      
      $element = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $message,
      ];
    } else {
      // otherwise will display membership status with the label hidden
      $element = $entity->get($field)->view($options);
    }
    
    return $element;
  }

}
