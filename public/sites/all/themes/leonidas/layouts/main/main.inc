<?php
/**
 * @file
 * Layout page
 */

/**
 * Implements hook_panels_layouts().
 */
// Plugin definition.
$plugin = array(
  'title'     => t('Main site template layout'),
  'category'  => 'Culturebox',
  'icon'      => 'icon.png',
  'theme'     => 'main',
  'theme arguments' => array('id', 'content'),
  'regions'    => array(
    'header_top_left'      => t('Header top left area'),
    'header_top_right'      => t('Header top right area'),
    'header_top_right_frame'  => t('Header top right frame area'),
    'header_top_right_logo'  => t('Header top right logo area'),
    'header_bottom'      => t('Header bottom area'),
    'header_submenu'      => t('Header submenu area'),
    'content'      => t('Content area'),
    'left_selection'         => t('Selection left area'),
    'right_selection'         => t('Selection right area'),
    'footer_before'          => t('Before Footer'),
    'footer_top'          => t('Top Footer'),
    'footer_bottom'      => t('Bottom Footer'),
  ),
);
