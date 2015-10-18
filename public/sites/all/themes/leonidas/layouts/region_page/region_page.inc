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
  'title'     => t('Region page'),
  'category'  => 'Culturebox',
  'icon'      => 'onecol.png',
  'theme'     => 'region_page',
  'theme arguments' => array('content'),
  'regions'    => array(
    'head' => t('head content'),
    'top_left'      => t('top left'),
    'top_right'     => t('top right'),
  ),
);

/**
 * Preprocess variables for theme('actu_home_page').
 * @see actu-home-page.tpl.php
 */
function template_preprocess_region_page(&$vars) {
  $context = $vars['display']->context;
  $vars['is_region'] = FALSE;

  if (!empty($context['argument_term_1'])) {
    $arg = $context['argument_term_1'];

    if (!empty($arg->restrictions['type']) && $arg->restrictions['type'][0] == CULTUREBOX_SITE_LOCALISATION_VOCABULARY_NAME) {
      $vars['is_region'] = TRUE;
    }
  }
}
