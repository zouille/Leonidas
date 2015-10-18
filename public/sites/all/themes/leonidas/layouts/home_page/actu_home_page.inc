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
  'title'     => t('Actu home page'),
  'category'  => 'Culturebox',
  'icon'      => 'onecol.png',
  'theme'     => 'actu_home_page',
  'theme arguments' => array('content'),
  'regions'    => array(
    'head' => t('head content'),
    'top_left'      => t('top left'),
    'top_right'     => t('top right'),
    'above_middle_top'     => t('above middle top'),
    'middle_top_left'   => t('middle top left'),
    'middle_top_right'  => t('middle top right'),
    'middle_middle' => t('middle tabs'),
    'middle_middle_bottom_one' => 'mea block 1',
    'middle_middle_bottom_two' => 'mea block 2',
    'middle_middle_bottom_three' => 'mea block 3',
    'middle_bottom' => t('Thematic blocks'),
  ),
);

/**
 * Preprocess variables for theme('actu_home_page').
 * @see actu-home-page.tpl.php
 */
function template_preprocess_actu_home_page(&$vars) {
  $context = $vars['display']->context;
  $vars['is_category_page'] = FALSE;

  if (!empty($context['argument_term_1'])) {
    $arg = $context['argument_term_1'];

    if (!empty($arg->restrictions['type']) && $arg->restrictions['type'][0] == CULTUREBOX_SITE_CATEGORIES_VOCABULARY_NAME) {
      $vars['is_category_page'] = TRUE;
    }
  }
}
