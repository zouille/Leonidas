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
  'title'     => t('Live home page'),
  'category'  => 'Culturebox',
  'icon'      => 'onecol.png',
  'admin css' => 'layout.css',
  'theme'     => 'home_live',
  'theme arguments' => array('content'),
  'regions'    => array(
    'top_live' => t('top live'),
    'middle_live_left' => t('middle live left'),
    'middle_live_sidebar' => t('middle live sidebar'),
    'bottom_evenements' => t('evenements'),
    'derniers_list' => t('derniers list'),
    'derniers_agenda' => t('derniers agenda'),
    'derniers_search' => t('derniers search'),
    'three_column' => t('three column'),
  ),
);
