<?php

namespace Drupal\english_translation_status\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\node\NodeInterface;

/**
 * A custom Views field that shows the English translation's published status of a node.
 *
 * @ViewsField("english_translation_status_field")
 */
class EnglishTranslationStatusField extends FieldPluginBase {

    /**
     * Prevents modification of the SQL query.
     *
     * {@inheritdoc}
     */
    public function query(): void {
        // This field is not tied to the database query.
        // Do nothing here to prevent Views from treating this as a database-backed field.
    }

    /**
     * Renders the field value for display in a view.
     *
     * {@inheritdoc}
     */
    public function render(ResultRow $values): string {
        $entity = $values->_entity;

        // Check if the entity is a node.
        if ($entity instanceof NodeInterface) {
            // Check if the node has an English translation.
            if ($entity->hasTranslation('en')) {
                // Load the English translation.
                $english_translation = $entity->getTranslation('en');
                // Determine if the English translation is published or not.
                return $english_translation->isPublished()
                    ? $this->t('Yes (Published)')
                    : $this->t('Yes (Unpublished)');
            }
            // If no English translation exists.
            return $this->t('No');
        }

        // Return 'N/A' if the entity is not a node (edge case).
        return $this->t('N/A');
    }
}
