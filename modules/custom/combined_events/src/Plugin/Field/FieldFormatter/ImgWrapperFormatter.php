<?php

namespace Drupal\combined_events\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\file\FileInterface;
use Drupal\Core\Render\Element;

/**
 * Plugin implementation of the 'img_wrapper_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "img_wrapper_formatter",
 *   label = @Translation("Img Wrapper Formatter"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class ImgWrapperFormatter extends FormatterBase {

    /**
     * {@inheritdoc}
     */
    public function viewElements(FieldItemListInterface $items, $langcode): array {
        $elements = [];

        // Iterate over field items.
        foreach ($items as $delta => $item) {
            if (!empty($item->target_id)) {
                // Load the file entity.
                /** @var \Drupal\file\FileInterface|null $file */
                $file = \Drupal::entityTypeManager()->getStorage('file')->load($item->target_id);

                if ($file instanceof FileInterface) {
                    // Prepare image rendering array.
                    $uri = $file->getFileUri();
                    $image_render_array = [
                        '#theme' => 'image',
                        '#uri' => $uri,
                        '#attributes' => ['class' => ['img_custom']],
                        '#alt' => $item->alt ?? '',
                        '#title' => $item->title ?? '',
                    ];

                    // Wrap the image in a custom HTML structure.
                    $elements[$delta] = [
                        '#type' => 'html_tag',
                        '#tag' => 'span',
                        '#attributes' => [
                            'class' => ['custom-image-wrapper'],
                        ],
                        'content' => $image_render_array,
                    ];
                }
            }
        }

        return $elements;
    }
}