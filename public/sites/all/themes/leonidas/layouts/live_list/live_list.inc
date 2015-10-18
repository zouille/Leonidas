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
  'title'     => t('Live list page'),
  'category'  => 'Culturebox',
  'icon'      => 'onecol.png',
  'admin css' => 'layout.css',
  'theme'     => 'live_list',
  'theme arguments' => array('content'),
  'regions'    => array(
    'top_live' => t('top live'),
    'live_left' => t('live left'),
    'live_sidebar' => t('live sidebar'),
    'live_bottom' => t('bottom live'),
  ),
);
