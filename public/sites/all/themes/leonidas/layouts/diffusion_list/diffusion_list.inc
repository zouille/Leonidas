<?php

$plugin = array(
  'title' => t('Diffusion list page'),
  'category' => 'Culturebox',
  'icon' => 'onecol.png',
  'admin css' => 'layout.css',
  'theme' => 'diffusion_list',
  'theme arguments' => array('content'),
  'regions' => array(
    'top_live' => t('top live'),
    'live_left' => t('live left'),
    'live_sidebar' => t('live sidebar'),
    'live_bottom' => t('bottom live'),
  ),
);
