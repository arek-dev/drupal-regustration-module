<?php

/**
 * @file
 * Creates a block displays Form created in SingleRegistrationForm.php
 */

namespace Drupal\singleregistration\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;

/**
 * Provides the Form registration block.
 * 
 * @Block(
 *   id = "module_form_block",
 *   admin_label = @Translation("Single Registration Form"),
 *   category = @Translation("Custom Block"),
 * )
 */
class SingleRegistrationBlock extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
      
    return \Drupal::formBuilder()->getForm('\Drupal\singleregistration\Form\SingleRegistrationForm');
  }
}
