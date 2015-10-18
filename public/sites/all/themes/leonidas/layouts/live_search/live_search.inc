<?php
/**
 * @file
 * Layout declaration.
 */

/**
 * Implements hook_panels_layouts().
 */
// Plugin definition.
$plugin = array(
  'title'     => t('Live search page'),
  'category'  => 'Culturebox',
  'icon'      => 'onecol.png',
  'admin css' => 'layout.css',
  'theme'     => 'live_search',
  'theme arguments' => array('content'),
  'regions'    => array(
    'live_left' => t('live left'),
    'live_sidebar' => t('live sidebar'),
  ),
);
