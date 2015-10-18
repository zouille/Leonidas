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
  'title'     => t('Article diaporama page layout'),
  'category'  => t('Culturebox'),
  'icon'      => 'icon.png',
  'theme'     => 'article_diaporama',
  'theme arguments' => array('id', 'content'),
  'regions'    => array(
    'above_section' => t('Above any section blocks'),
    'top' => t('Top region'),
    'middle' => t('Middle region'),
    'bottom' => t('Bottom region'),
    'footer_left' => t('Footer left region'),
    'footer_right' => t('Footer right region'),
  ),
);
