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
  'title'     => t('Default content page layout'),
  'category'  => 'Culturebox',
  'icon'      => 'icon.png',
  'theme'     => 'content_page_layout',
  'theme arguments' => array('id', 'content'),
  'admin css' => 'admin-layout.css',
  'regions'    => array(
    'above_section' => t('Above any section blocks'),
    'left_top' => t('Left top region'),
    'left_middle' => t('Left middle region'),
    'left_bottom' => t('Left bottom region'),
    'right' => t('Right region'),
  ),
);
