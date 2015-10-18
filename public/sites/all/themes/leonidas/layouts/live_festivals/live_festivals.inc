<?php
/**
 * @file
 * Layout declaration.
 */

// Plugin definition.
$plugin = array(
  'title'     => t('Live festivals'),
  'category'  => 'Culturebox',
  'icon'      => 'onecol.png',
  'admin css' => 'layout.css',
  'theme'     => 'live_festivals',
  'theme arguments' => array('content'),
  'regions'    => array(
    'top_live' => t('top live'),
    'live_left' => t('live left'),
    'live_sidebar' => t('live sidebar'),
    'live_bottom' => t('bottom live'),
  ),
);
