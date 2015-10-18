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
  'title'     => t('Emission page'),
  'category'  => 'Culturebox',
  'icon'      => 'icon.png',
  'theme'     => 'emission_page',
  'theme arguments' => array('id', 'content'),
  'admin css' => 'admin-layout.css',
  'regions'    => array(
    'above_section' => t('Above any section blocks'),
    'header' => t('Header'),
  	'header_bottom' => t('Header bottom'),
    'left_top' => t('Left top region'),
    'left' => t('Left region'),
    'right' => t('Right region'),
    'footer' => t('Footer'),
  ),
);
