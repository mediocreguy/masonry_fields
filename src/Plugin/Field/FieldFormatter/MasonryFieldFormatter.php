<?php

namespace Drupal\masonry_fields\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\text\Plugin\Field\FieldFormatter\TextDefaultFormatter;

/**
 * Plugin implementation of the 'masonry_fields_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "masonry_fields_formatter",
 *   label = @Translation("Masonry Fields"),
 *   field_types = {
 *     "text_long",
 *     "text_long_with_summary",
 *   }
 * )
 */
class MasonryFieldFormatter extends TextDefaultFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
  
    $options = array();
    $options['masonry'] = FALSE;
    
    $options += parent::defaultSettings();

    // Get default options from Masonry.
    $default_options = \Drupal::service('masonry.service')->getMasonryDefaultOptions();

    // Set default values for Masonry
    foreach ($default_options as $option => $default_value) {
      $options[$option] = array(
        'default' => $default_value,
      );
      if (is_int($default_value)) {
        $options[$option]['bool'] = TRUE;
      }
    }

    return $options;  
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
  
    $element = array();
    
    $options = $this->getSettings(); 
    $options += $this->defaultSettings();
    
    // Add Masonry options to formatter settings form
    $element['masonry'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable Masonry'),
      '#description' => t("Displays items in a Masonry layout."),
      '#default_value' => $options['masonry'],
    );
    
    if (\Drupal::service('masonry.service')->isMasonryInstalled()) {
      if ($options['masonry']) {
        $element += \Drupal::service('masonry.service')->buildSettingsForm($options);
      }
    }
    else {
      // Disable Masonry as plugin is not installed
      $element['masonry']['#disabled'] = TRUE;
      $element['masonry']['#description'] = t('This option has been disabled as the jQuery Masonry plugin is not installed.');
    }
  
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {

    $options = $this->getSettings(); 
    
    $summary = array();
    
    if (\Drupal::service('masonry.service')->isMasonryInstalled()) {
      if ($options['masonry']) {
        $summary[] = t('Masonry Enabled');
      }
      else {
        $summary[] = t('Masonry Disabled');
      }
    }
    else {
      $summary[] = t('Masonry Not Installed');
    }
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
  
    $elements = parent::viewElements($items, $langcode);

    $options = $this->getSettings();
    
    if ($options['masonry']) {
    
      // get field name
      $fielddef = $items->getFieldDefinition();
      $fieldname = $fielddef->getName();
      // field selector for classy base theme
      $fieldnamecss = strtr($fieldname, '_', '-');
      $container = '.field--name-' . $fieldnamecss;
      $item_selector = '.field__item';
      
      // Apply the masonry display on this page.
      \Drupal::service('masonry.service')->applyMasonryDisplay($elements, $container, $item_selector, $options);
    }
    
    return $elements;
  }

}
