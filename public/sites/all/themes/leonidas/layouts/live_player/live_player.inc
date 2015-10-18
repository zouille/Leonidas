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
  'title'     => t('Live Player page'),
  'category'  => 'Culturebox',
  'icon'      => 'onecol.png',
  'admin css' => 'layout.css',
  'theme'     => 'live_player',
  'theme arguments' => array('content'),
  'regions'    => array(
    'live_top' => t('Live top area'),
    'live_content' => t('Live Content'),
    'live_sidebar' => t('Live Sidebar'),
  ),
);
