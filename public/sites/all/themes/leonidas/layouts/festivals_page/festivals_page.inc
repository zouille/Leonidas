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
  'title'     => t('Home festivals page'),
  'category'  => 'Culturebox',
  'icon'      => 'onecol.png',
  'admin css' => 'layout.css',
  'theme'     => 'festivals_page',
  'theme arguments' => array('content'),
  'regions'    => array(
    'top_live' => t('top live'),
    'middle_agenda' => t('middle live'),
    'middle_festivals_left' => t('middle festivals left'),
    'middle_festivals_sidebar' => t('middle festivals sidebar'),
    'middle_festivals' => t('middle festivals'),
    'bottom_festivals' => t('festivals'),
    'bottom_left' => t('bottom left'),
    'bottom_sidebar' => t('bottom sidebar'),
  ),
);
