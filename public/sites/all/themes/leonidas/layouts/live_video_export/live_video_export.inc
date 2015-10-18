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
  'title'     => t('Live video export'),
  'category'  => 'Culturebox',
  'icon'      => 'onecol.png',
  'admin css' => 'layout.css',
  'theme'     => 'live_video_export',
  'theme arguments' => array('content'),
  'regions'    => array(
    'content' => t('Content'),
  ),
);
