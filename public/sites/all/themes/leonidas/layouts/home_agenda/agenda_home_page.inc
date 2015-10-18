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
  'title'     => t('Agenda home page'),
  'category'  => 'Culturebox',
  'icon'      => 'onecol.png',
  'theme'     => 'agenda_home_page',
  'theme arguments' => array('content'),
  'admin css' => 'layout.css',
  'regions'    => array(
    'top'      => t('Top area'),
    'middle_left'   => t('Middle left'),
    'middle_right'  => t('Middle right'),
    'bottom' => t('Bottom area'),
  ),
);
