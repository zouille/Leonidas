<?php

/**
 * @file
 * template.php for culturbox theme.
 */
include_once 'inc/pager.inc';
include_once 'inc/emission.inc';

/**
 * Prevent to render all multivalue assets & use first instead.
 */
function _culturebox_get_first_live_media_asset_view($vars, $default_view_mode = 'live_teaser', $force = FALSE, $field_name = 'field_live_media') {
  $node = $vars['node'];
  $media = field_get_items('node', $node, $field_name);
  if ($media) {
    if (!empty($vars['content'][$field_name][0]['asset'])) {
      $raw_asset = array_shift($vars['content'][$field_name][0]['asset']);
      $view_mode = $raw_asset['#view_mode'];
    }
    $view_mode = empty($view_mode) ? $default_view_mode : $view_mode;

    if ($force) {
      $view_mode = $default_view_mode;
    }

    $asset = !empty($media[0]['entity']) ? $media[0]['entity'] : asset_load($media[0]['target_id']);
    $asset->alt_title = $node->title;
    if (!empty($asset) && is_object($asset)) {
      $asset_view = $asset->view($view_mode);
      return $asset_view;
    }
  }
  return NULL;
}

/**
 * Prevent to render all multivalue assets & use first instead.
 */
function _culturebox_get_article_main_media_asset_view($vars, $default_view_mode = 'live_teaser', $image_only = FALSE) {
  $media = field_get_items('node', $vars['node'], 'field_article_main_media');
  if (!empty($media)) {
    $asset = !empty($media[0]['entity']) ? $media[0]['entity'] : asset_load($media[0]['target_id']);

    if ($image_only && $asset->type != 'image') {
      return NULL;
    }

    if (!empty($vars['content']['field_article_main_media'][0]['asset'])) {
      $raw_asset = array_shift($vars['content']['field_article_main_media'][0]['asset']);
      $view_mode = $raw_asset['#view_mode'];
    }
    $view_mode = empty($view_mode) ? $default_view_mode : $view_mode;

    if (!empty($media[0]['entity']->type) && $media[0]['entity']->type == 'image') {
      if (!empty($asset->field_asset_description[LANGUAGE_NONE][0]['safe_value'])) {
        $asset->alt_title = strip_tags($asset->field_asset_description[LANGUAGE_NONE][0]['safe_value']);
      }
      else {
        $asset->alt_title = $asset->title;
      }
    }
    if (!empty($asset) && is_object($asset)) {
      $asset_view = $asset->view($view_mode);
      return $asset_view;
    }
  }
  return NULL;
}

/**
 * Prevent to render all multivalue assets & use first instead.
 */
function _culturebox_get_article_media_asset_view($vars, $default_view_mode = 'live_teaser') {
  if (!empty($vars['node'])) {
    $media = field_get_items('node', $vars['node'], 'field_article_media');
  }
  elseif (empty($media) && !empty($vars['field_collection_item'])) {
    $media = field_get_items('field_collection_item', $vars['field_collection_item'], 'field_article_media');
  }

  if (!empty($media)) {
    if (!empty($vars['content']['field_article_media'][0]['asset'])) {
      $raw_asset = array_shift($vars['content']['field_article_media'][0]['asset']);
      $view_mode = $raw_asset['#view_mode'];
    }
    $view_mode = empty($view_mode) ? $default_view_mode : $view_mode;

    $asset = !empty($media[0]['entity']) ? $media[0]['entity'] : asset_load($media[0]['target_id']);
    if (!empty($asset->field_asset_description[LANGUAGE_NONE][0]['safe_value'])) {
      $asset->alt_title = strip_tags($asset->field_asset_description[LANGUAGE_NONE][0]['safe_value']);
    }
    else {
      $asset->alt_title = $asset->title;
    }
    if (!empty($asset) && is_object($asset)) {
      $asset_view = $asset->view($view_mode);
      return $asset_view;
    }
  }
  return NULL;
}

/**
 * Get "Department" term by city.
 */
function _culturebox_get_department_by_place($place) {
  $parents_tree = taxonomy_get_parents_all($place->tid);

  foreach ($parents_tree as $parent) {
    $temp_parrents = taxonomy_get_parents_all($parent->tid);
    if (count($temp_parrents) == 2) {
      return $parent;
    }
  }

  return $place;
}

/**
 * Get main category of node.
 */
function _culturebox_get_node_main_category($node, $field_name = 'field_main_category') {
  $field_main_category = field_get_items('node', $node, $field_name);

  if ($field_main_category) {
    $thematic_id = end($field_main_category);
    $thematic = taxonomy_term_load($thematic_id['tid']);
    if ($thematic) {
      return culturebox_site_l($thematic->name, "taxonomy/term/{$thematic->tid}");
    }
  }

  return FALSE;
}

/**
 * Get node live status.
 */
function _culturebox_get_node_live_status($vars) {
  if (!empty($vars['field_live_status'])) {
    $field_data = field_get_items('node', $vars['node'], 'field_live_status');
    $field_status = array_shift($field_data);
    if (empty($field_status['value'])) {
      switch (drupal_strtolower($field_status['value'])) {
        case 'avant le direct':
          return 'before';

        case 'pendant le direct':
          return 'during';

        case 'après le direct':
          return 'after';
      }
    }
  }

  return FALSE;
}

/**
 * Get main category of node live.
 */
function _culturebox_get_node_live_main_category($node, $first_deepest_category = FALSE, $blank = FALSE) {
  $field_live_main_category = field_get_items('node', $node, 'field_live_main_category');
  if ($field_live_main_category && !$first_deepest_category) {
    $thematic_id = end($field_live_main_category);
    $thematic = NULL;

    if (!empty($thematic_id['taxonomy_term'])) {
      $thematic = $thematic_id['taxonomy_term'];
    }
    elseif (!empty($thematic_id['tid'])) {
      $thematic = taxonomy_term_load($thematic_id['tid']);
    }

    if (!empty($thematic)) {
      $attributes = array();
      if ($blank) {
        $attributes['target'] = '_blank';
      }
      return culturebox_site_l($thematic->name, "taxonomy/term/{$thematic->tid}", array(
        'attributes' => $attributes,
      ));
    }
  } // Get deepest category if main category is empty.
  else {
    $tids = field_get_items('node', $node, 'field_live_categories');

    $sorted_categories = culturebox_site_get_tids_sorted_by_depth($tids);
    if (!empty($sorted_categories)) {
      foreach (array('depth_2', 'depth_1', 'depth_0') as $depth) {
        if (!empty($sorted_categories[$depth])) {
          $tid = array_shift($sorted_categories[$depth]);
          $term = taxonomy_term_load($tid);
          $attributes = array();
          if ($blank) {
            $attributes['target'] = '_blank';
          }
          return culturebox_site_l(
              $term->name, "taxonomy/term/$tid", array(
            'attributes' => $attributes,
              )
          );
        }
      }
    }
  }

  return '';
}

/**
 * Get live button play by status. Status types : beintot, direct, replay.
 */
function _culturebox_preprocess_live_button_play(&$vars, $status_type = NULL, $class = NULL) {
  $live_status = theme(
      'live_status', array(
    'node' => $vars['node'],
    'status_type' => $status_type,
    'class' => $class,
      )
  );
  if (!empty($live_status)) {
    $vars['live_status'] = $live_status;
  }
}

/**
 * Get live geolocalization.
 */
function _culturebox_get_node_live_geolocalization($node) {
  if (!empty($node->field_live_tag_place)) {
    $field_live_tag_place = field_get_items('node', $node, 'field_live_tag_place');
    if ($field_live_tag_place) {
      $first = array_shift($field_live_tag_place);
      $term = NULL;

      if (!empty($first['taxonomy_term'])) {
        $term = $first['taxonomy_term'];
      }
      elseif (!empty($first['tid'])) {
        $term = taxonomy_term_load($first['tid']);
      }

      if (!empty($term)) {
        return culturebox_site_l(drupal_strtoupper($term->name), 'taxonomy/term/' . $term->tid);
      }
    }
  }
  return '';
}

/**
 * Get article geolocalization.
 */
function _culturebox_get_node_article_geolocalization($node) {
  if ($field_geolocalization = field_get_items('node', $node, 'field_geolocalization')) {
    $geolocalization_tid = end($field_geolocalization);
    $city = NULL;

    if (!empty($geolocalization_tid['taxonomy_term'])) {
      $city = $geolocalization_tid['taxonomy_term'];
    }
    elseif (!empty($geolocalization_tid['tid'])) {
      $city = taxonomy_term_load($geolocalization_tid['tid']);
    }

    if (!empty($city)) {
      $self = end($field_geolocalization);
      if (culturebox_site_show_city_page_if_nodes_count($city) || !(count($field_geolocalization) > 2 && $self['tid'] == $city->tid)) {
        return culturebox_site_l(
            $city->name, 'taxonomy/term/' . $city->tid, array(
          'attributes' => array(
            'class' => array(
              'city-link',
            ),
          ),
            )
        );
      }
      else {
        return check_plain($city->name);
      }
    }
  }

  return FALSE;
}

/**
 * Get event geolocalization.
 */
function _culturebox_get_taxonomy_term_festival_geolocalization($term) {
  $field_geolocalization = field_get_items('taxonomy_term', $term, 'field_geolocalization');
  while (!empty($field_geolocalization)) {
    $geolocalization_tid = array_pop($field_geolocalization);
    $parents = taxonomy_get_parents_all($geolocalization_tid['tid']);
    if (!empty($parents)) {
      array_shift($parents);
      while (!empty($parents)) {
        $parent = array_shift($parents);
        $last_geolocalization = end($field_geolocalization);
        if ($last_geolocalization['tid'] == $parent->tid) {
          array_pop($field_geolocalization);
        }
      }
    }
    $city = taxonomy_term_load($geolocalization_tid['tid']);
    if ($city) {
      $cities[] = culturebox_site_l($city->name, "taxonomy/term/$city->tid");
    }
  }
  return !empty($cities) ? $cities : FALSE;

  return FALSE;
}

/**
 * Get taxonomy event geolocalization.
 */
function _culturebox_get_taxonomy_event_geolocalization($term, $full_tree = TRUE) {
  $cities = array();
  $field_geolocalization = field_get_items('taxonomy_term', $term, 'field_geolocalization');
  if (!$full_tree) {
    if (!empty($field_geolocalization)) {
      $last_geolocalization = end($field_geolocalization);
      if (empty($last_geolocalization['taxonomy_term']) && !empty($last_geolocalization['tid'])) {
        $last_geolocalization['taxonomy_term'] = taxonomy_term_load($last_geolocalization['tid']);
      }
      if (!empty($last_geolocalization['taxonomy_term'])) {
        $cities[] = check_plain($last_geolocalization['taxonomy_term']->name);
      }
    }
    return !empty($cities) ? $cities : FALSE;
  }

  while (!empty($field_geolocalization)) {
    $geolocalization_tid = array_pop($field_geolocalization);
    $parents = taxonomy_get_parents_all($geolocalization_tid['tid']);
    if (!empty($parents)) {
      array_shift($parents);
      while (!empty($parents)) {
        $parent = array_shift($parents);
        $last_geolocalization = end($field_geolocalization);
        if ($last_geolocalization['tid'] == $parent->tid) {
          array_pop($field_geolocalization);
        }
      }
    }
    $city = taxonomy_term_load($geolocalization_tid['tid']);
    if ($city) {
      $cities[] = check_plain($city->name);
    }
  }
  return !empty($cities) ? $cities : FALSE;
}

/**
 * Get "Region" term by city or "department".
 * @todo why this func in template.php while it's a pure logic?
 */
function _culturebox_get_region_by_place($place) {
  $parents_tree = taxonomy_get_parents_all($place->tid);

  foreach ($parents_tree as $parent) {
    $temp_parrents = taxonomy_get_parents_all($parent->tid);
    if (count($temp_parrents) == 1) {
      return $parent;
    }
  }

  return $place;
}

/**
 * Override theme_menu_tree__menu_name. Returns HTML for a wrapper for a menu sub-tree.
 */
function culturebox_menu_tree__main_menu($variables) {
  return '<ul class="top-nav">' . $variables['tree'] . '</ul>';
}

/**
 * Override theme_menu_tree__menu_name. Returns HTML for a wrapper for a menu sub-tree.
 */
function culturebox_menu_tree__menu_footer_menu($variables) {
  return '<ul>' . $variables['tree'] . '</ul>';
}

/**
 * Override theme_menu_link__menu_name. Returns HTML for a menu link and submenu.
 */
function culturebox_menu_link__main_menu($variables) {


  global $language_url;
  $element = $variables['element'];
  $sub_menu = '';

  // Unset leaf class.
  $key = array_search('leaf', $element['#attributes']['class']);
  if ($key === FALSE) {
    $key = array_search('collapsed', $element['#attributes']['class']);
  }
  if ($key === FALSE) {
    $key = array_search('expanded', $element['#attributes']['class']);
  }
  if ($key !== FALSE) {
    unset($element['#attributes']['class'][$key]);
  }

  if (isset($element['#below'])) {
    $sub_menu = drupal_render($element['#below']);
  }
  if($element['#attributes']) {
    $element['#attributes']['data-mlid'][] = $element['#original_link']['mlid'];
  }
  static $node;
  static $main_category_term;

  if (empty($node)) {

    $node = menu_get_object();
    // If node was loaded correctly.
    if (!empty($node) && $node != ' ') {
      switch ($node->type) {
        case 'live':
          $field = 'field_live_main_category';
          break;

        case CULTUREBOX_EMISSION_FICHE_EMISSION_NODE_NAME:
          $field = 'field_fe_emission_tid';
          break;

        case CULTUREBOX_EMISSION_EXTRAIT_NODE_NAME:
          $field = 'field_extrait_emission_tid';
          break;

        default:
          $field = 'field_main_category';
      }
      if (empty($main_category_term)) {
        $main_categories = field_get_items('node', $node, $field);

        if ($main_categories) {
          $main_categories_terms_tids = array();

          foreach ($main_categories as $main_category) {
            $main_categories_terms_tids[] = $main_category['tid'];
          }

          $main_categories_terms = taxonomy_term_load_multiple($main_categories_terms_tids);
          foreach ($main_categories_terms as $term) {
            $parents = taxonomy_get_parents($term->tid);

            if (empty($parents)) {
              $main_category_term = $term;
            }
          }
        }
      }
      else {
        $category = field_get_items('node', $node, $field);

        // Don't let highlight parent menu item if node has over 1 continent tag.
        if (!empty($category)) {
          foreach ($category as $item) {
            $terms[] = 'taxonomy/term/' . $item['tid'];
          }
        }
      }
    }
  }
  else {
    $node = ' ';
  }

  if (!empty($main_category_term)) {
    if (drupal_match_path($element['#href'], "taxonomy/term/$main_category_term->tid")) {
      $element['#attributes']['class'][] = 'active-trail';


    }
  }

  // Highlight parent element menu at taxonomy term page.
  if (empty($node) || is_string($node)) {
    $term = menu_get_object('taxonomy_term', 2);


  }


  if (!empty($term->tid) && !culturebox_emission_is_emission_section()) {

    $parents = taxonomy_get_parents_all($term->tid);
    foreach ($parents as $parent) {
      if (drupal_match_path($element['#href'], "taxonomy/term/$parent->tid")) {
        $element['#attributes']['class'][] = 'active-trail';
        $element['#attributes']['class'][] = 'parent';

        break;
      }
    }
  }


if($element['#original_link']['mlid'] == '12115' &&  current_path() !== $element['#href']) {
 $index = array_search('active-trail', $element['#attributes']['class']);
  unset($element['#attributes']['class'][$index]);
}

  // Highlight "emissions" on "emissions" section.
  if (culturebox_emission_is_emission_section() && $element['#original_link']['plid'] == '0' && $element['#original_link']['link_title'] == 'Emissions') {
    $element['#attributes']['class'][] = 'active-trail';

    $element['#attributes']['class'][] = 'active';

  }

  // Highlight parent term menu minisite item.
  if ($minisite_term = culturebox_minisite_get_term()) {
    if (drupal_match_path($element['#href'], "taxonomy/term/$minisite_term->tid")) {
      $element['#attributes']['class'][] = 'active-trail';

    }
  }
  // Change minisite level1 link style.
  if (!empty($element['#original_link']['depth']) && $element['#original_link']['depth'] == 1) {
    // If we have absolute url alias.
    global $base_url;
    $source = $base_url . '/';
    $path = str_replace($source, '', $element['#original_link']['link_path']);
    require_once variable_get('path_inc', 'includes/path.inc');
    $path = drupal_lookup_path('source', $path);
    // if source path not found, we get original path link.
    if (!$path) {
      $path = $element['#original_link']['link_path'];
    }
    if (!empty($element['#original_link']['link_path']) && preg_match('/taxonomy\/term\/([0-9]+)/', $path, $matches)) {
      $term = taxonomy_term_load($matches[1]);
      if (!empty($term) && $term->vocabulary_machine_name == 'mini_site') {
        $element['#attributes']['class'][] = 'minisite-item';
      }
    }
  }
  if (($element['#href'] == $_GET['q'] || ($element['#href'] == '<front>' && drupal_is_front_page())) &&
      (empty($options['language']) || $options['language']->language == $language_url->language)
  ) {
    // For main menu query params important to select active.
    if (!empty($element['#localized_options']['query'])) {
      $active = TRUE;
      foreach ($element['#localized_options']['query'] as $key => $param) {
        if (!isset($_GET[$key]) || $_GET[$key] != $param) {
          $active = FALSE;
          break;
        }
      }
      if ($active) {
        $element['#attributes']['class'][] = 'active';
      }
    }
    else {
      $element['#attributes']['class'][] = 'active';
    }
  }
  elseif (in_array('active-trail', $element['#attributes']['class'])) {
    $element['#attributes']['class'][] = 'active';
  }

  foreach ($element['#attributes'] as $key => $value) {
    if (empty($value)) {
      unset($element['#attributes'][$key]);
    }
  }

  if($element['#href'] == 'emissions'){


  }


  $output = culturebox_site_l($element['#title'], $element['#href'], $element['#localized_options']);

  return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
}

/**
 * Override theme_menu_link__menu_name. Returns HTML for a menu link and submenu.
 */
function culturebox_menu_link__menu_footer_menu($variables) {
  $element = $variables['element'];
  $sub_menu = '';

  // Unset leaf class.
  $key = array_search('leaf', $element['#attributes']['class']);
  if ($key === FALSE) {
    $key = array_search('collapsed', $element['#attributes']['class']);
  }
  if ($key === FALSE) {
    $key = array_search('expanded', $element['#attributes']['class']);
  }
  if ($key !== FALSE) {
    unset($element['#attributes']['class'][$key]);
  }

  if ($element['#below']) {
    $sub_menu = drupal_render($element['#below']);
  }

  $output = culturebox_site_l($element['#title'], $element['#href'], $element['#localized_options']);
  return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
}

/**
 * Override theme_menu_link__menu_name.Returns HTML for a menu link and submenu.
 */
function culturebox_menu_link__menu_social_links($variables) {
  $element = $variables['element'];
  $sub_menu = '';
  unset($element['#attributes']['class']);

  $output = culturebox_site_l($element['#title'], $element['#href'], $element['#localized_options']);
  return '<li>' . $output . $sub_menu . "</li>\n";
}

/**
 * Override theme_menu_link__menu_name. Returns HTML for a menu link and submenu.
 */
function culturebox_menu_link__menu_footer_social_links($variables) {
  $element = $variables['element'];
  $sub_menu = '';
  $output = culturebox_site_l($element['#title'], $element['#href'], $element['#localized_options']);
  return '<li' . drupal_attributes($element['#localized_options']['attributes']) . '>' . $output . $sub_menu . "</li>\n";
}

/**
 * Override theme_menu_tree__menu_name. Returns HTML for a wrapper for a menu sub-tree.
 */
function culturebox_menu_tree__menu_social_links($variables) {
  return '<ul class="social">' . $variables['tree'] . '</ul>';
}

/**
 * Override theme_menu_tree__menu_name. Returns HTML for a wrapper for a menu sub-tree.
 */
function culturebox_menu_tree__menu_footer_social_links($variables) {
  return '<ul class="social-list">' . $variables['tree'] . '</ul>';
}

function _culturebox_preprocess_node_twitter_card(&$vars) {
  $view_mode = $vars['view_mode'];
  $node = $vars['node'];

  // Ajout des twitter cards pour les fiche_emission les articles et les lives
  $tab = array('article', 'live', 'fiche_emission');
  if (in_array($node->type, $tab) && $view_mode == 'full') {
    // En premier lieu les balises static
    $card = array(
      '#tag' => 'meta',
      '#attributes' => array(
        'name' => 'twitter:app:name:ipad',
        'content' => 'Culturebox'
      ),
    );
    drupal_add_html_head($card, 'twitter_card_name_ipad');

    $card = array(
      '#tag' => 'meta',
      '#attributes' => array(
        'name' => 'twitter:app:id:ipad',
        'content' => '648273701'
      ),
    );
    drupal_add_html_head($card, 'twitter_card_app_id_ipad');

    $card = array(
      '#tag' => 'meta',
      '#attributes' => array(
        'name' => 'twitter:app:url:ipad',
        'content' => 'https://itunes.apple.com/fr/app/culturebox/id648273701?mt=8'
      ),
    );
    drupal_add_html_head($card, 'twitter_card_app_url_ipad');

    $card = array(
      '#tag' => 'meta',
      '#attributes' => array(
        'name' => 'twitter:app:country',
        'content' => 'FR'
      ),
    );
    drupal_add_html_head($card, 'twitter_app_country');

    $card = array(
      '#tag' => 'meta',
      '#attributes' => array(
        'name' => 'twitter:card',
        'content' => 'summary_large_image'
      ),
    );
    drupal_add_html_head($card, 'twitter_card_card');

    $card = array(
      '#tag' => 'meta',
      '#attributes' => array(
        'name' => 'twitter:site',
        'content' => '@Culturebox'
      ),
    );
    drupal_add_html_head($card, 'twitter_card_site');

    // twitter cards dépendante du
    $card = array(
      '#tag' => 'meta',
      '#attributes' => array(
        'name' => 'twitter:title',
        'content' => $node->title
      ),
    );
    drupal_add_html_head($card, 'twitter_card_title');

    $twit_desc = '';
    $twit_img = '';
    switch ($node->type) {
      case 'article':
        $twit_desc = !empty($node->field_article_catchline[LANGUAGE_NONE][0]['value']) ? $node->field_article_catchline[LANGUAGE_NONE][0]['value'] : NULL;
        $twit_img = !empty($node->field_article_media[LANGUAGE_NONE][0]['entity']) ? $node->field_article_media[LANGUAGE_NONE][0]['entity'] : NULL;
        break;
      case 'live':
        $twit_desc = !empty($node->field_live_catchline) ? $node->field_live_catchline[LANGUAGE_NONE][0]['value'] : NULL;
        $twit_desc = ($twit_desc == NULL && !empty($node->field_live_description[LANGUAGE_NONE][0]['value'])) ? $node->field_live_description[LANGUAGE_NONE][0]['value'] : $twit_desc;
        $twit_img = !empty($node->field_live_media) ? $node->field_live_media[LANGUAGE_NONE][0]['entity'] : NULL;
        break;
      case 'fiche_emission':
        $twit_desc = !empty($node->field_fe_body_short[LANGUAGE_NONE][0]['value']) ? $node->field_fe_body_short[LANGUAGE_NONE][0]['value'] : NULL;
        $twit_desc = ($twit_desc == NULL && !empty($node->field_body[LANGUAGE_NONE][0]['value'])) ? $node->field_body[LANGUAGE_NONE][0]['value'] : $twit_desc;
        $twit_img = !empty($node->field_fiche_emission_main_media) ? $node->field_fiche_emission_main_media[LANGUAGE_NONE][0]['entity'] : NULL;
        break;
    }

    if ($twit_img) {
      $twit_img = image_style_url('article_view_full_main_image', $twit_img->field_asset_image[LANGUAGE_NONE][0]['uri']);
    }

    if (!empty($twit_desc)) {
      $twit_desc = truncate_utf8($twit_desc, 200, FALSE, TRUE);
    }

    $card = array(
      '#tag' => 'meta',
      '#attributes' => array(
        'name' => 'twitter:description',
        'content' => strip_tags($twit_desc),
      ),
    );
    drupal_add_html_head($card, 'twitter_card_description');

    $card = array(
      '#tag' => 'meta',
      '#attributes' => array(
        'name' => 'twitter:image',
        'content' => $twit_img,
      ),
    );
    drupal_add_html_head($card, 'twitter_card_image');
  }
}

/**
 * Preprocess variables for node.tpl.php.
 *
 * @see templates/node/article/node.tpl.php
 */
function culturebox_preprocess_node(&$vars, $hook) {
  $view_mode = $vars['view_mode'];
  $node = $vars['node'];

  if (!empty($vars['content']['field_live_media'][0]['asset'])) {
    $keys = array_keys($vars['content']['field_live_media'][0]['asset']);
    foreach ($keys as $key) {
      if (!empty($vars['content']['field_live_media'][0]['asset'][$key]['#entity'])) {
        $vars['content']['field_live_media'][0]['asset'][$key]['#entity']->alt_title = $node->title;
      }
    }
  }

  _culturebox_preprocess_node_twitter_card($vars);

  $preprocess = 'culturebox_preprocess_node__' . $node->type . '_' . str_replace('-', '_', $view_mode);

  if (function_exists($preprocess)) {
    $preprocess($vars, $hook);
  }

  $vars['theme_hook_suggestions'][] = 'node__' . $node->type . '_' . str_replace('-', '_', $view_mode);

  if ($node->type == 'live' && $view_mode == 'home_from_editor_medium' && !empty($vars['view']->name) && $vars['view']->name == 'diffusions_list' && !empty($vars['view']->current_display) && ($vars['view']->current_display == 'emission_la_une_bottom' || $vars['view']->current_display == 'diffusion_nodequeue_list')) {
    $preprocess = 'culturebox_preprocess_node__' . $node->type . '_' . str_replace('-', '_', $view_mode) . '_emission';

    if (function_exists($preprocess)) {
      $preprocess($vars, $hook);
    }

    $vars['theme_hook_suggestions'][] = 'node__' . $node->type . '_' . str_replace('-', '_', $view_mode) . '_emission';
  }

  if ($node->type == 'live' && $view_mode == 'teaser' && !empty($vars['view']->name) && !empty($vars['view']->current_display) && (($vars['view']->name == 'diffusions_list' && ($vars['view']->current_display == 'diffusions_list_toutes_les_diffusions')) || ($vars['view']->name == 'lives_list' && $vars['view']->current_display == 'festival_lives_list'))) {
    $preprocess = 'culturebox_preprocess_node__' . $node->type . '_' . str_replace('-', '_', $view_mode) . '_emission';

    if (function_exists($preprocess)) {
      $preprocess($vars, $hook);
    }

    $vars['theme_hook_suggestions'][] = 'node__' . $node->type . '_' . str_replace('-', '_', $view_mode) . '_emission';
  }
}

/**
 * Preprocess variables for node--article-en-direct.tpl.php.
 *
 * @see templates/node/article/node--article-en-direct.tpl.php
 */
function culturebox_preprocess_node__article_en_direct(&$vars) {
  $vars['link'] = culturebox_site_l(
      $vars['title'], "node/{$vars['node']->nid}", array(
    'html' => TRUE,
      )
  );
  // @todo we should use publish date instead of changed.
  if (date('Y.m.d', $vars['changed']) != date('Y.m.d', time())) {
    $vars['time'] = date('d/m', $vars['changed']);
  }
  else {
    $vars['time'] = date('H:i', $vars['changed']);
  }
}

/**
 * Preprocess variables for node--live-live-small.tpl.php.
 *
 * @see templates/node/live/node--live-live-small.tpl.php
 */
function culturebox_preprocess_node__live_live_small(&$vars) {
  $live = $vars['node'];

  if (!empty($vars['view']) &&
      $vars['view']->name == 'lives_list' &&
      ((in_array($vars['view']->current_display, array('actu_live_la_une', 'minisite_live_la_une')) &&
      isset($vars['view']->row_index) && $vars['view']->row_index == 0) || ($vars['view']->current_display == 'articles_live_lives_la_une'))
  ) {

    $vars['class'] = ' main';
    $asset_mode = 'live_small_first_la_une';
    $icon = NULL;
  }
  else {
    $asset_mode = 'live_small';
    $icon = 'play-small';
    $vars['class'] = '';
  }
  if ($thematic = _culturebox_get_node_live_main_category($live)) {
    $vars['category'] = $thematic;
  }

  $live_params = array(
    'node' => $live,
  );

  _culturebox_preprocess_live_button_play($live_params, NULL, $icon);
  if (!empty($live_params['live_status'])) {
    $vars['live_status'] = $live_params['live_status'];
  }

  $media = field_get_items('node', $live, 'field_live_media');
  if ($media) {
    if (!empty($media[0]['entity'])) {
      $asset = $media[0]['entity'];
    }
    else {
      $asset = asset_load($media[0]['target_id']);
    }
    $asset->alt_title = $live->title;
    $media_view = $asset->view($asset_mode);
    if ($media_view) {
      $vars['live_image'] = culturebox_site_l(
          drupal_render($media_view), 'node/' . $live->nid, array(
        'html' => TRUE,
          )
      );
    }
  }
  $vars['live_title'] = culturebox_site_l(
      $live->title, 'node/' . $live->nid
  );
}

function culturebox_preprocess_node__extrait_live_live_small(&$vars) {
  $media = field_get_items('node', $vars['node'], 'field_extrait_illustration');
  if ($media) {
    if (!empty($media[0]['entity'])) {
      $asset = $media[0]['entity'];
    }
    else {
      $asset = asset_load($media[0]['target_id']);
    }
    $asset->alt_title = $vars['node']->title;
    $media_view = $asset->view('live_small');
    if ($media_view) {
      $vars['live_image'] = culturebox_site_l(
          drupal_render($media_view), 'node/' . $vars['node']->nid, array(
        'html' => TRUE,
          )
      );
    }
  }
  $vars['live_title'] = culturebox_site_l(
      $vars['node']->title, 'node/' . $vars['node']->nid
  );
}

/**
 * Preprocess variables for node--live-en-direct.tpl.php.
 *
 * @see templates/node/live/node--live-en-direct.tpl.php
 */
function culturebox_preprocess_node__live_en_direct(&$vars) {
  $vars['link'] = culturebox_site_l(
      $vars['title'], "node/{$vars['node']->nid}", array(
    'html' => TRUE,
      )
  );
  // @todo we should use publish date instead of changed.
  if (date('Y.m.d', $vars['changed']) != date('Y.m.d', time())) {
    $vars['time'] = date('d/M', $vars['changed']);
  }
  else {
    $vars['time'] = date('H:i', $vars['changed']);
  }
}

function _culturebox_preprocess_article_published_date(&$vars) {
  $published_date = field_get_items('node', $vars['node'], 'field_published_date');
  if (!$published_date) {
    $published_date = $vars['created'];
  }
  else {
    $published_date = $published_date[0]['value'];
  }
  $params = array();
  $params['@publish_date'] = format_date($published_date, 'custom', 'd/m/Y \à H\Hi', NULL, 'fr');
  $vars['published_schema_org'] = format_date($published_date, 'custom', DATE_ISO8601, NULL, 'fr');
  if (format_date($published_date, 'custom', 'd/m/Y H\Hi') != format_date($vars['changed'], 'custom', 'd/m/Y H\Hi')) {
    $params['@updated_date'] = format_date($vars['changed'], 'custom', 'd/m/Y \à H\Hi', NULL, 'fr');
    $updated = strtr('Mis à jour le @updated_date, ', $params);
    $vars['updated_schema_org'] = format_date($vars['changed'], 'custom', DATE_ISO8601, NULL, 'fr');
    $vars['published'] = $updated . strtr('publié le @publish_date', $params);
  }
  else {
    $vars['published'] = strtr('Publié le @publish_date', $params);
  }
}

function _culturebox_preprocess_article_signature(&$vars, $user_view_mode = 'signature_article') {
  $signature_free = field_get_items('node', $vars['node'], 'field_signature_free');
  $authors = field_get_items('node', $vars['node'], 'field_signature');
  $uids[] = array();
  if ($authors) {
    foreach ($authors as &$author) {
      if (empty($author['entity'])) {
        $author = user_load($author['target_id']);
      }
      else {
        $author = $author['entity'];
      }

      if (!empty($author->uid) && !empty($author->name)) {
        $uids[$author->uid] = $author->name;
      }
    }
  }
  else {
    $authors[$vars['node']->uid] = user_load($vars['node']->uid);
  }

  if (!empty($signature_free)) {
    if ($uid = array_search($signature_free[0]['value'], $uids)) {
      $vars['signature'] = user_view(user_load($uid), $user_view_mode);
    }
    else {
      $vars['signature_free'] = $signature_free[0]['value'];

      if (!empty($vars['node']->field_signature_free_sub_title)) {
        $signature_free_sub_title = field_get_items('node', $vars['node'], 'field_signature_free_sub_title');

        if (!empty($signature_free_sub_title[0]['value'])) {
          $vars['signature_free_sub_title'] = $signature_free_sub_title[0]['value'];
        }
      }
    }
  }
  elseif (!empty($authors)) {
    if (count($authors) > 1) {
      $last_author = array_pop($authors);
      $vars['signature'] = array();
      foreach ($authors as $value) {
        // Name.
        $user_name = array();
        $first_name = field_get_items('user', $value, 'field_user_firstname');
        $last_name = field_get_items('user', $value, 'field_user_lastname');
        if ($first_name) {
          $user_name[] = $first_name[0]['value'];
        }
        if ($last_name) {
          $user_name[] = $last_name[0]['value'];
        }
        if (!empty($user_name)) {
          $user_name = implode(' ', $user_name);
          $vars['signature'][] = $user_name;
        }
        else {
          $vars['signature'][] = $value->name;
        }
      }
      $vars['signature'] = implode(', ', $vars['signature']);

      // Last Author Name.
      $first_name = field_get_items('user', $last_author, 'field_user_firstname');
      $last_name = field_get_items('user', $last_author, 'field_user_lastname');
      $user_name = array();
      if ($first_name) {
        $user_name[] = $first_name[0]['value'];
      }
      if ($last_name) {
        $user_name[] = $last_name[0]['value'];
      }
      if (!empty($user_name)) {
        $user_name = implode(' ', $user_name);
        $vars['signature'] .= " et $user_name";
      }
      else {
        $vars['signature'] = "Par {$vars['signature']} et $last_author->name";
      }
    }
    else {
      $author = reset($authors);
      if ($author) {
        $vars['signature'] = user_view($author, $user_view_mode);
      }
    }
  }
}

function culturebox_preprocess_node__article_full(&$vars) {
  // Title.
  $title = field_get_items('node', $vars['node'], 'field_title_long');
  if ($title) {
    $vars['title'] = $title[0]['safe_value'];
  }

  // Published date.
  _culturebox_preprocess_article_published_date($vars);

  // Signature.
  if (!(!empty($vars['node']->field_pilier_associe[LANGUAGE_NONE][0]['value']) && $vars['node']->field_pilier_associe[LANGUAGE_NONE][0]['value'] == 'live')) {
    // On affiche la signature de l'article uniquement si celui-ci n'est pas un article live.
    _culturebox_preprocess_article_signature($vars);
  }
  else {
    $vars['article_live'] = TRUE;
  }

  // Description.
  $description = field_get_items('node', $vars['node'], 'field_article_catchline');
  if ($description) {
    $vars['description'] = $description[0]['safe_value'];
  }

  // Share links.
  $vars['share_links'] = theme('share_links_ajax', array('node' => $vars['node']));

  // Média principal.
  if (!($media = _culturebox_get_article_main_media_asset_view($vars))) {
    $media = _culturebox_get_article_media_asset_view($vars);
  }

  $vars['media'] = drupal_render($media);

  if (!empty($media) && !empty($vars['field_article_media'][0]['target_id'])) {
    $aid = $vars['field_article_media'][0]['target_id'];

    if (!empty($media['asset'][$aid])) {
      $tmp_media = $media['asset'][$aid];

      if ($tmp_media["#bundle"] == 'image') {
        $vars['img_full_seo'] = file_create_url($tmp_media['field_asset_image']['#items'][0]['uri']);

        $vars['microdata_image'] = '<meta itemprop="image" content="' . $vars['img_full_seo'] . '" />';
      }
    }
  }

  // Pour simplifier la lecture du code, on gère tout le contenu "aside" du noeud dans une autre fonction de thème.
  $vars['aside'] = theme('culturebox_node__article_full__aside', array('node' => $vars['node']));

  // Article minute.
  $min_article = field_get_items('node', $vars['node'], 'field_type');
  if (!empty($min_article[0]['value']) && $min_article[0]['value'] == 'article_minute_par_minute' ) {
    $vars['min_article'] = TRUE;
  }
  // Newsletter bloc.
  $node = $vars['node'];

  $context = new ctools_context('node');
  $context->plugin = 'node';
  $context->data = $node;
  $context->title = $node->title;
  $context->argument = $node->nid;
  $contexts = array('node' => $context);
  $conf = array('context' => $contexts, 'conf' => 'bloc_newsletter');
  $pane = ctools_content_render('culturebox_bloc_newsletter', 'culturebox_bloc_newsletter', $conf, array(), array(), $contexts);
  $vars['newsletter_bloc'] = render($pane->content);
}

function culturebox_theme() {
  return array(
    'culturebox_node__article_full__aside' => array(
      'variables' => array(
        'node' => FALSE,
      ),
      'template' => 'node--article-full--aside',
      'path' => CULTUREBOX_THEME_PATH . '/templates/node/article',
    ),
  );
}

function culturebox_preprocess_culturebox_node__article_full__aside(&$vars) {
  // Contenu recommandé.
  $context = new ctools_context('node');
  $context->plugin = 'node';
  $context->data = $vars['node'];
  $context->title = $vars['node']->title;
  $context->argument = $vars['node']->nid;
  $contexts = array('node' => $context);
  $conf = array('context' => array('node'));
  $pane = ctools_content_render('culturebox_recommendations', 'culturebox_recommendations', $conf, array(), array(), $contexts);
  $vars['recommend'] = render($pane->content);

  $exclude_nids = array($vars['node']->nid);

  // Articles liés.
  $related_internal = field_get_items('node', $vars['node'], 'field_related_internal');
  if ($related_internal) {
    $related_internal_nids = array();

    foreach ($related_internal as $value) {
      if (!empty($value['target_id'])) {
        $related_internal_nids[] = $value['target_id'];
      }
    }

    if (!empty($related_internal_nids)) {
      $related_internal_nodes = node_load_multiple($related_internal_nids);

      foreach ($related_internal_nodes as $related_internal_node) {
        if ($related_internal_node->status == NODE_PUBLISHED) {
          $vars['related_articles'][] = culturebox_site_l($related_internal_node->title, "node/{$related_internal_node->nid}");
          $exclude_nids[] = $related_internal_node->nid;
        }
      }
    }
  }

  $related_external = field_get_items('node', $vars['node'], 'field_related_external');
  if ($related_external) {
    foreach ($related_external as $value) {
      if (isset($value['fragment'])) {
        $path = url($value['url'], array('fragment' => $value['fragment']));
      }
      else {
        $path = $value['url'];
      }
      $vars['related_articles'][] = culturebox_site_l($value['title'], $path);
    }
  }

  // Evénements liés.
  $events = array();

  if (!empty($vars['node']->field_events)) {
    $events = field_get_items('node', $vars['node'], 'field_events');
  }

  if (!empty($vars['node']->field_mini_site)) {
    $mini_sites = field_get_items('node', $vars['node'], 'field_mini_site');

    if (!empty($mini_sites)) {
      foreach ($mini_sites as $mini_site) {
        $mini_site_parents = taxonomy_get_parents($mini_site['tid']);

        if (empty($mini_site_parents)) {
          // On n'ajoute que les mini-sites de premier niveau.
          $events = array_merge($events, array($mini_site));
        }
      }
    }
  }

  if (!empty($events)) {
    $events_tids = array();

    foreach ($events as $event) {
      $events_tids[] = $event['tid'];
    }

    $events = taxonomy_term_load_multiple($events_tids);

    foreach ($events as $event) {
      $e = array('event' => $event);

      // On récupère les 4 derniers articles taggés dans l'événement.
      $q = db_select('node', 'n');
      $q->addField('n', 'nid');
      $q->leftJoin('field_data_field_published_date', 'fpd', 'fpd.entity_id = n.nid');
      $q->leftJoin('field_data_field_pilier_associe', 'fpa', 'fpa.entity_id = n.nid');
      $q->condition('n.type', 'article');
      $q->condition('n.status', NODE_PUBLISHED);
      $q->condition('n.nid', $exclude_nids, 'NOT IN');
      $q->where("fpa.field_pilier_associe_value <> 'live' OR fpa.field_pilier_associe_value IS NULL");
      $q->orderBy('fpd.field_published_date_value', 'DESC');
      $q->range(0, 4);

      if ($event->vocabulary_machine_name == 'event') {
        $q->join('field_data_field_events', 'fe', 'fe.entity_id = n.nid');
        $q->condition('fe.field_events_tid', $event->tid);
      }
      elseif ($event->vocabulary_machine_name == 'mini_site') {
        $q->join('field_data_field_mini_site', 'fe', 'fe.entity_id = n.nid');
        $q->condition('fe.field_mini_site_tid', $event->tid);
      }

      $results = $q->execute()->fetchCol();

      if (!empty($results)) {
        $e['nodes'] = node_load_multiple($results);
        $exclude_nids = array_merge($exclude_nids, $results);
      }

      if (!empty($e['nodes'])) {
        $mini_site_dmdm = _culturebox_minisite_get_dmdm_minisite();

        if ($event->tid == $mini_site_dmdm->tid) {
          $e['title'] = 'Autour de ' . check_plain($event->name);
        }
        else {
          $e['title'] = 'Autour de l\'événement ' . check_plain($event->name);
        }

        $vars['events'][] = $e;
      }
    }
  }

  // Live lié.
  $nodes = field_get_items('node', $vars['node'], 'field_related_live');
  if ($nodes) {
    $lives_nids = array();

    foreach ($nodes as $item) {
      if (!empty($item['target_id'])) {
        $lives_nids[] = $item['target_id'];
      }
    }

    if (!empty($lives_nids)) {
      $first_live_nids = _culturebox_get_first_live($lives_nids, 4);

      if ($first_live_nids) {
        if (count($first_live_nids) == 1) {
          $first_live = node_load(array_shift($first_live_nids));

          // On place un marqueur pour afficher des informations sous conditions dans le preprocess du view_mode "home_from_editor_medium_2".
          $first_live->home_from_editor_medium_2_custom_display = TRUE;

          $vars['live_title'] = 'Voir l\'événement';
          if (!empty($first_live->field_live_main_category)) {
            $field_live_main_category = field_get_items('node', $first_live, 'field_live_main_category');

            $vars['live_title'] = !empty($field_live_main_category[0]['tid']) && $field_live_main_category[0]['tid'] == 45629 ? "Voir le concert" : $vars['live_title'];
            $vars['live_title'] = !empty($field_live_main_category[0]['tid']) && $field_live_main_category[0]['tid'] == 45689 ? "Voir la pièce de théâtre" : $vars['live_title'];
            $vars['live_title'] = !empty($field_live_main_category[0]['tid']) && $field_live_main_category[0]['tid'] == 45699 ? "Voir le spectacle de danse" : $vars['live_title'];
            $vars['live_title'] = !empty($field_live_main_category[0]['tid']) && $field_live_main_category[0]['tid'] == 45709 ? "Voir le spectacle" : $vars['live_title'];
            $vars['live_title'] = !empty($field_live_main_category[1]['tid']) && $field_live_main_category[1]['tid'] == 45633 ? "Voir l'opéra" : $vars['live_title'];
          }
          $vars['live'] = node_view($first_live, 'home_from_editor_medium_2');
        }
        else {
          $first_lives = node_load_multiple($first_live_nids);
          $vars['lives'] = array();

          foreach ($first_lives as $first_live) {
            $vars['lives'][] = node_view($first_live, 'live_home_derniers');
          }
        }
      }
    }
  }

  // Diffusion ou extrait lié.
  $diffusions = field_get_items('node', $vars['node'], 'field_article_related_diffusions');
  if ($diffusions) {
    foreach ($diffusions as $item) {
      $nids_diffs[] = $item['target_id'];
    }
    $nid_diff = _culturebox_get_first_diffusion($nids_diffs, 4);
  }

  if (!empty($nid_diff)) {
    if (count($nid_diff) == 1) {
      $diff_or_extrait = node_load(array_shift($nid_diff));
    }
    else {
      $diffs = node_load_multiple($nid_diff);
      $vars['emissions'] = array();

      foreach ($diffs as $diff) {
        $vars['emissions'][] = node_view($diff, 'dans_actu');
      }
    }
  }
  else {
    $extraits = field_get_items('node', $vars['node'], 'field_article_related_extraits');

    if (!empty($extraits)) {
      foreach ($extraits as $ext) {
        if (!empty($ext['entity'])) {
          $diff_or_extrait = $ext['entity'];
        }
        elseif (!empty($ext['target_id'])) {
          $diff_or_extrait = node_load($ext['target_id']);
        }

        if ($diff_or_extrait->status == NODE_PUBLISHED) {
          break;
        }
      }
    }
  }

  if (!empty($diff_or_extrait) && $diff_or_extrait->status == NODE_PUBLISHED && ($diff_or_extrait->type == CULTUREBOX_EMISSION_FICHE_EMISSION_NODE_NAME || $diff_or_extrait->type == CULTUREBOX_EMISSION_EXTRAIT_NODE_NAME)) {
    if ($diff_or_extrait->type == CULTUREBOX_EMISSION_FICHE_EMISSION_NODE_NAME) {
      $diff_or_extrait->home_from_editor_medium_custom_display = TRUE;
      $vars['emission_title'] = 'Voir l\'émission';
      $vars['emission'] = node_view($diff_or_extrait, 'home_from_editor_medium');
    }
    elseif ($diff_or_extrait->type == CULTUREBOX_EMISSION_EXTRAIT_NODE_NAME) {
      $vars['emission_title'] = 'Voir l\'extrait';
      $vars['emission'] = node_view($diff_or_extrait, 'home_from_editor_medium');
    }
  }

  // Thématiques liées.
  culturebox_process_node_thematics($vars, 'field_categories', TRUE);

  // Lieux liés.
  culturebox_process_node_localisation($vars, 'field_geolocalization', TRUE);

  // Personnalités liées.
  $peoples = field_get_items('node', $vars['node'], 'field_people');
  if ($peoples) {
    $peoples_tids = array();

    foreach ($peoples as $people) {
      $peoples_tids[] = $people['target_id'];
    }

    $peoples = taxonomy_term_load_multiple($peoples_tids);

    foreach ($peoples as $people) {
      $vars['peoples'][] = culturebox_site_l($people->name, "taxonomy/term/{$people->tid}");
    }
  }
}

/**
 * Return first live, which haven't status after replay an between direct and replay.
 */
function _culturebox_get_first_live($ids, $nb = 1) {
  if (empty($ids)) {
    return NULL;
  }

  $status = array(
    CULTUREBOX_LIVE_STATUS_LIVE_APRES_LE_REPLAY,
    CULTUREBOX_LIVE_STATUS_LIVE_ENTRE_LE_DIRECT_ET_LE_REPLAY,
  );

  $nids = db_select('node', 'n');
  $nids->join('field_data_field_live_status', 'fdfls', 'n.nid = fdfls.entity_id');
  $nids->fields('n', array('nid'))
    ->condition('fdfls.field_live_status_value', $status, 'NOT IN')
    ->condition('n.nid', $ids, 'IN')
    ->condition('n.status', NODE_PUBLISHED)
    ->range(0, $nb);
  $res = $nids->execute()->fetchCol();

  if (!empty($res)) {
    return $res;
  }

  return NULL;
}

function _culturebox_preprocess_culturebox_vote_model__culturebox_vote_model_45879_title(&$vars) {
  $vote = $vars['culturebox_vote_model'];

  $fullname = trim("{$vote->field_vote_prenom[LANGUAGE_NONE][0]['value']} {$vote->field_vote_nom[LANGUAGE_NONE][0]['value']}");
  $age = format_plural(date_diff(date_create($vote->field_birth_date[LANGUAGE_NONE][0]['value']), date_create('now'))->y, '1 an', '@count ans');
  $ville = trim($vote->field_vote_ville[LANGUAGE_NONE][0]['value']);
  $evenement = $vars['term_name'];

  if (empty($vote->field_asset_image_copyright[LANGUAGE_NONE][0]['value'])) {
    $vars['share_title'] = 'Partager ce témoignage';
    $vars['title'] = "{$fullname}, {$age}, de {$ville}, nous livre son témoignage";
  }
  else {
    $vars['noquote'] = TRUE;
    $evenement = trim($vote->field_asset_image_copyright[LANGUAGE_NONE][0]['value']);

    $vars['share_title'] = "Partager cet avis sur la pièce «{$evenement}»";
    $vars['title'] = "{$fullname}, {$age}, de {$ville}, partage son avis sur la pièce «{$evenement}»";
  }
}

function _culturebox_preprocess_culturebox_vote_model__culturebox_vote_model_45879_share(&$vars) {
  $vote = $vars['culturebox_vote_model'];

  $bundle = _culturebox_vote_model_get_bundle_name($vars['term_tid']);
  $config = _culturebox_vote_model_get_bundle_config($bundle, FALSE);

  if (!empty($config[$bundle])) {
    $config = $config[$bundle];
  }

  $share_text_operation = !empty($config->config['name']) ? $config->config['name'] : $vars['term_name'];
  $share_text = "{$share_text_operation} - Découvrez la participation de {$vote->field_vote_prenom[LANGUAGE_NONE][0]['value']}";

  if (!empty($config->config['hashtag'])) {
    $share_text .= " {$config->config['hashtag']}";
  }

  $vars['share_links'] = theme('share_links_ajax', array('ajax' => FALSE, 'show_email' => FALSE, 'custom_share_text' => urlencode($share_text), 'custom_share_url' => urlencode(url("taxonomy/term/{$vars['term_tid']}/culturebox-vote-model/view/{$vote->id}", array('absolute' => TRUE, 'base_url' => 'http://culturebox.francetvinfo.fr')))));
}

function _culturebox_preprocess_culturebox_vote_model__culturebox_vote_model_45879_share_image(&$vars) {
  $vote = $vars['culturebox_vote_model'];

  if (!empty($vote->field_image[LANGUAGE_NONE][0]['uri'])) {
    $image_url = image_style_url('article_view_full_main_image', $vote->field_image[LANGUAGE_NONE][0]['uri']);
  }
  else {
    $term = taxonomy_term_load($vars['term_tid']);

    if (!empty($term->field_mini_site_main_image[LANGUAGE_NONE][0]['target_id'])) {
      $asset = asset_load($term->field_mini_site_main_image[LANGUAGE_NONE][0]['target_id']);

      if (!empty($asset->field_asset_image[LANGUAGE_NONE][0]['uri'])) {
        $image_url = file_create_url($asset->field_asset_image[LANGUAGE_NONE][0]['uri']);
      }
    }
  }

  if (!empty($image_url)) {
    $twitter_image = array(
      '#tag' => 'meta',
      '#attributes' => array(
        'property' => 'twitter:image:src',
        'content' => $image_url,
      ),
    );
    drupal_add_html_head($twitter_image, 'twitter_image');

    $facebook_image = array(
      '#tag' => 'meta',
      '#attributes' => array(
        'property' => 'og:image',
        'content' => $image_url,
      ),
    );
    drupal_add_html_head($facebook_image, 'facebook_image');
  }
}

function culturebox_preprocess_culturebox_vote_model__culturebox_vote_model_45879__full(&$vars) {
  $vote = $vars['culturebox_vote_model'];
  $vars['term_tid'] = _culturebox_vote_model_get_tid_from_bundle($vote->type);
  $vars['term_name'] = db_query_range("SELECT name FROM {taxonomy_term_data} WHERE tid = :tid", 0, 1, array(':tid' => $vars['term_tid']))->fetchField();

  if (!empty($vars['term_name'])) {
    _culturebox_preprocess_culturebox_vote_model__culturebox_vote_model_45879_title($vars);
    _culturebox_preprocess_culturebox_vote_model__culturebox_vote_model_45879_share($vars);
  }

  _culturebox_preprocess_culturebox_vote_model__culturebox_vote_model_45879_share_image($vars);
}

function culturebox_preprocess_culturebox_vote_model__culturebox_vote_model_45879__small(&$vars) {
  $vote = $vars['culturebox_vote_model'];
  $vars['term_tid'] = _culturebox_vote_model_get_tid_from_bundle($vote->type);
  $vars['term_name'] = db_query_range("SELECT name FROM {taxonomy_term_data} WHERE tid = :tid", 0, 1, array(':tid' => $vars['term_tid']))->fetchField();

  if (!empty($vars['term_name'])) {
    _culturebox_preprocess_culturebox_vote_model__culturebox_vote_model_45879_share($vars);
  }

  _culturebox_preprocess_culturebox_vote_model__culturebox_vote_model_45879_share_image($vars);

  if (empty($vote->field_asset_image_copyright[LANGUAGE_NONE][0]['value'])) {
    $vars['share_title'] = 'Partagez votre témoignage';
    $vars['title'] = 'Merci d\'avoir participé en nous livrant votre témoignage';
  }
  else {
    $vars['share_title'] = 'Partagez votre avis';
    $vars['title'] = 'Merci d\'avoir participé en nous livrant votre contribution. Celle-ci sera peut-être reprise dans un article de la rédaction de Culturebox.<br />Rendez-vous sur <a href="http://culturebox.fr/avignon">culturebox.fr/avignon</a> !<br />En attendant, voici votre critique :';
  }
}

function culturebox_preprocess_culturebox_vote_model__culturebox_vote_model_45879__teaser(&$vars) {
  $vote = $vars['culturebox_vote_model'];
  $vars['term_tid'] = _culturebox_vote_model_get_tid_from_bundle($vote->type);
  $vars['term_name'] = db_query_range("SELECT name FROM {taxonomy_term_data} WHERE tid = :tid", 0, 1, array(':tid' => $vars['term_tid']))->fetchField();

  if (!empty($vars['term_name'])) {
    _culturebox_preprocess_culturebox_vote_model__culturebox_vote_model_45879_title($vars);
  }

  $vars['vote_uri'] = url("taxonomy/term/{$vars['term_tid']}/culturebox-vote-model/view/{$vote->id}");

  if (!empty($vars['content']['field_image'][0]['#item']['uri'])) {
    $image_size = getimagesize($vars['content']['field_image'][0]['#item']['uri']);

    if (!empty($image_size[0]) && !empty($image_size[1])) {
      $width = $image_size[0];
      $height = $image_size[1];

      if ($width > $height) {
        $vars['image_orientation'] = 'paysage';
      }
      else {
        $vars['image_orientation'] = 'portrait';
      }
    }
  }
}

/**
 * Preprocess variables for node--article--rss.tpl.php.
 *
 * @see templates/node/article/node--article-rss.tpl.php
 */
function culturebox_preprocess_node__article_rss(&$vars) {
  _culturebox_preprocess_node_rss_view_mode($vars);
}

/**
 * Preprocess variables for node--live--rss.tpl.php.
 *
 * @see templates/node/live/node--live-rss.tpl.php
 */
function culturebox_preprocess_node__live_rss(&$vars) {
  _culturebox_preprocess_node_rss_view_mode($vars);
}

/**
 * Common preprocess for article and live.
 */
function _culturebox_preprocess_node_rss_view_mode(&$vars) {
  $node = $vars['node'];

  switch ($node->type) {
    case 'live':
      $field_name = 'field_live_media';
      break;

    case 'article':
      $field_name = 'field_article_media';
      break;

    case CULTUREBOX_EMISSION_FICHE_EMISSION_NODE_NAME:
      $field_name = 'field_fiche_emission_main_media';
      break;
  }

  $illustration = field_get_items('node', $node, $field_name);
  if ($illustration) {
    if (isset($illustration[0]['entity'])) {
      $asset = $illustration[0]['entity'];
    }
    else {
      $asset = asset_load($illustration[0]['target_id']);
    }
    if ($asset) {
      $image = field_get_items('asset', $asset, 'field_asset_image');
      $image_uri = isset($image[0]['uri']) ? $image[0]['uri'] : FALSE;
      if ($image && $image_uri) {
        $url = image_style_url('asset_small', $image_uri);
        if (isset($url)) {
          $image[0]['external_url'] = $url;
          $vars['enclosure'] = $image[0];
        }
      }
    }
  }

  $vars['pubdate'] = format_date($node->created, 'custom', 'r');
}

/**
 * Preprocess variables for node--article--rss.tpl.php.
 *
 * @see templates/node/article/node--article-rss.tpl.php
 */
function culturebox_preprocess_node__feed_item_rss(&$vars) {
  $node = $vars['node'];
  $image = field_get_items('node', $node, 'field_image');
  $image_uri = isset($image[0]['uri']) ? $image[0]['uri'] : FALSE;
  if ($image && $image_uri) {
    $url = image_style_url('asset_small', $image_uri);
    if (isset($url)) {
      $image[0]['external_url'] = $url;
      $vars['enclosure'] = $image[0];
    }
  }

  $url = field_get_items('node', $node, 'field_url');
  if ($url) {
    $vars['url'] = $url[0]['display_url'];
  }

  $vars['pubdate'] = format_date($node->created, 'custom', 'r');
}

/**
 * Preprocess variables for node--article-search.tpl.php.
 *
 * @see templates/node/article/node--article-search.tpl.php
 */
function culturebox_preprocess_node__article_search(&$vars) {
  $node = $vars['node'];
  if ($thematic = _culturebox_get_node_main_category($node)) {
    $vars['category'] = $thematic;
  }

  $vars['title'] = culturebox_site_l($vars['node']->title, "node/$node->nid");

  // Article image.
  if ($article_media = _culturebox_get_article_media_asset_view($vars)) {
    $vars['image'] = culturebox_site_l(
        drupal_render($article_media), 'node/' . $node->nid, array(
      'html' => TRUE,
        )
    );
  }

  culturebox_preprocess_article_media_icon($vars, 'big');
}

function culturebox_preprocess_node__page_search(&$vars) {
  $vars['title'] = culturebox_site_l($vars['node']->title, 'node/' . $vars['node']->nid);
}

function culturebox_preprocess_node__article_diaporama(&$vars) {
  // Title.
  $title = field_get_items('node', $vars['node'], 'field_title_long');
  if ($title) {
    $vars['title'] = $title[0]['safe_value'];
  }

  // Published date.
  _culturebox_preprocess_article_published_date($vars);

  // Description.
  $description = field_get_items('node', $vars['node'], 'field_article_catchline');
  if ($description) {
    $vars['description'] = $description[0]['safe_value'];
  }

  // Signature.
  _culturebox_preprocess_article_signature($vars);

  // Diaporama.
  if (!empty($vars['field_article_main_media'])) {
    $media = field_get_items('node', $vars['node'], 'field_article_main_media');
    if (!empty($media[0]['target_id'])) {
      $asset = asset_load($media[0]['target_id']);
      $field_asset_diaporama = field_get_items('asset', $asset, 'field_asset_diaporama');
      if (!empty($field_asset_diaporama)) {
        $count_image = 0;
        $count_video = 0;
        $mids = array();
        foreach ($field_asset_diaporama as $media_id) {
          $mids[] = $media_id['target_id'];
        }
        $diaporama_medias = asset_load_multiple($mids);
        foreach ($diaporama_medias as $diaporama_media) {
          if ($diaporama_media->type == 'image') {
            $count_image++;
          }
          else {
            $count_video++;
          }
        }

        $icon = theme('image', array(
          'path' => CULTUREBOX_THEME_PATH . '/images/ico-diaporamma.gif',
          'width' => 40,
          'height' => 40,
        ));

        $count_image = $count_image ? $count_image . ' photos' : '';
        $count_video = $count_video ? $count_video . ' videos' : '';
        if (!empty($count_image)) {
          $count = $count_image;
          if (!empty($count_video)) {
            $count .= ', ' . $count_video;
          }
        }
        else {
          $count = $count_video;
        }

        $vars['diaporama'] = array(
          'image' => $icon,
          'count' => $count,
        );
      }
    }
  }

  // Share links.
  $vars['share_links'] = theme('share_links_ajax', array('node' => $vars['node']));
  
  $node = $vars['node'];
  
  // On charge chaque bloc de l'interface d'admin de Panels.
  ctools_include('content');
  ctools_include('context-task-handler');
  
  // Contextes CTools globaux.
  $context = new ctools_context('node');
  $context->plugin = 'node';
  $context->data = $node;
  $context->title = $node->title;
  $context->argument = $node->nid;
  $contexts = array('node' => $context);
  
  // HEAD.
  $conf = array('context' => 'node');
  $output = ctools_content_render('culturebox_diaporamma_images', 'culturebox_diaporamma_images', $conf, array(), array(), $contexts);
  $vars['photo'] = $output->content;
}

function culturebox_preprocess_node__article_timeline(&$vars) {
  global $user;
  // Title.
  $title = field_get_items('node', $vars['node'], 'field_title_long');
  if ($title) {
    $vars['title'] = $title[0]['safe_value'];
  }

  // Published date.
  _culturebox_preprocess_article_published_date($vars);

  // Description.
  $description = field_get_items('node', $vars['node'], 'field_article_catchline');
  if ($description) {
    $vars['description'] = $description[0]['safe_value'];
  }

  // Signature.
  _culturebox_preprocess_article_signature($vars, 'signature');

  // Share links.
  $vars['share_links_bottom'] = theme('share_links_ajax', array('node' => $vars['node']));
}

/**
 * Preprocess variables for node--article-home-close-to-user-medium.tpl.php.
 *
 * @see templates/node/article/node--article-home-close-to-user-medium.tpl.php
 */
function culturebox_preprocess_node__article_home_close_to_user_medium(&$vars) {
  if ($article_media = _culturebox_get_article_media_asset_view($vars)) {
    $vars['image'] = culturebox_site_l(
        drupal_render($article_media), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }

  culturebox_preprocess_article_media_icon($vars);
}

/**
 * Preprocess variables for node--article-teaser.tpl.php.
 *
 * @see templates/node/article/node--article-teaser.tpl.php
 */
function culturebox_preprocess_node__article_teaser(&$vars) {
  // Affichage spécial pour les pages /tous-les-articles des émissions.
  $custom_display = FALSE;
  $vars['custom_display'] = FALSE;

  if (!empty($vars['view']->name) && !empty($vars['view']->current_display) && $vars['view']->name == 'diffusions_list' && $vars['view']->current_display == 'diffusions_list_all_news') {
    $custom_display = TRUE;
    $vars['custom_display'] = TRUE;
  }

  // Categories.
  if ($thematic = _culturebox_get_node_main_category($vars['node'])) {
    $vars['category'] = $thematic;
  }

  // Description.
  if (!$custom_display) {
    $description = field_get_items('node', $vars['node'], 'field_article_catchline');
    if ($description) {
      $vars['description'] = truncate_utf8($description[0]['safe_value'], 150, TRUE, TRUE);
    }
  }

  if ($article_media = _culturebox_get_article_media_asset_view($vars)) {
    $vars['image'] = culturebox_site_l(
        drupal_render($article_media), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }

  culturebox_preprocess_article_media_icon($vars, 'big');
}

/**
 * Preprocess variables for node--article-small.tpl.php.
 *
 * @see templates/node/article/node--article-small.tpl.php
 */
function culturebox_preprocess_node__article_small(&$vars) {
  if ($article_media = _culturebox_get_article_media_asset_view($vars)) {
    $vars['rendered_article_media'] = culturebox_site_l(
        drupal_render($article_media), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }

  culturebox_preprocess_article_media_icon($vars);

  if ($thematic = _culturebox_get_node_main_category($vars['node'])) {
    $vars['category'] = $thematic;
  }
}

/**
 * Check if main media of node is video.
 */
function _culturebox_check_main_media_is_video($vars) {
  $field_main_media = field_get_items('node', $vars['node'], 'field_article_main_media');
  if (!empty($field_main_media)) {
    $asset = asset_load($field_main_media[0]['target_id']);
    if (!empty($asset) && $asset->type == 'video') {
      return TRUE;
    }
  }

  return FALSE;
}

/**
 * Check if main media of node is premuim gallery.
 */
function _culturebox_check_main_media_is_diaporama($vars, $return_asset = FALSE, $only_premium = TRUE) {
  $field_main_media = field_get_items('node', $vars['node'], 'field_article_main_media');
  if (!empty($field_main_media)) {
    $asset = asset_load($field_main_media[0]['target_id']);
    if ($asset) {
      if (!empty($asset) && $asset->type == 'diaporama') {
        $items = field_get_items('asset', $asset, 'field_asset_premium');
        if (!$only_premium || !empty($items[0]['value'])) {
          return $return_asset ? $asset : TRUE;
        }
      }
    }
  }

  return FALSE;
}

/**
 * Return live status.
 */
function _culturebox_live_status(&$vars) {
  $status = field_get_items('node', $vars['node'], 'field_live_status');
  $status = $status[0]['value'];
  drupal_add_js(array('CultureboxLive' => array('statusLive' => $status)), 'setting');
  $vars['label_class'] = '';
  $vars['live_status'] = '';

  if (!empty($status)) {
    switch ($status) {
      case CULTUREBOX_LIVE_STATUS_LIVE_PROCHAINEMENT:
      case CULTUREBOX_LIVE_STATUS_LIVE_AVANT_LE_DIRECT:
      case CULTUREBOX_LIVE_STATUS_LIVE_RETARD:
        $vars['live_status'] = 'Bientôt';
        break;

      case CULTUREBOX_LIVE_STATUS_LIVE_LAST_CHANCE:
        $vars['live_status'] = 'Dernière chance';
        break;

      case CULTUREBOX_LIVE_STATUS_LIVE_DIRECT:
        $vars['live_status'] = 'Direct';
        break;

      case CULTUREBOX_LIVE_STATUS_LIVE_REPLAY:
        $vars['live_status'] = 'A revoir';
        $vars['label_class'] = 'arevoir';
        break;
    }
  }
  $vars['status'] = $status;
}

/**
 * Process localisations links block per node.
 */
function culturebox_process_node_localisation(&$vars, $field = 'field_geolocalization', $flatten = FALSE) {
  if ($vars['node']) {
    // Find department or region in geolocalization field.
    $items = field_get_items('node', $vars['node'], $field);
    if (!empty($items)) {
      $tids = array();
      foreach ($items as $item) {
        if (!empty($item['tid'])) {
          $tids[] = $item['tid'];
        }
      }
      // Special case when we have only city and other tids is missed.
      if (count($tids) == 1) {
        $terms = taxonomy_get_parents_all(array_shift($tids));
        $tids = array();
        foreach ($terms as $term) {
          array_unshift($tids, $term->tid);
        }
      }

      if (!isset($terms)) {
        $terms = taxonomy_term_load_multiple($tids);
      }

      for ($i = 0; $i < 3; $i++) {
        $term = array_shift($terms);
        if (!empty($term)) {
          // Region.
          if ($i == 0 && !$flatten) {
            $vars['department_links'][] = culturebox_site_l(drupal_strtoupper($term->name), "taxonomy/term/$term->tid");
          }
          else {
            $vars['department_links'][] = culturebox_site_l($term->name, "taxonomy/term/$term->tid");
          }
        }
      }
    }
  }
}

/**
 * Process thematics links block per node.
 */
function culturebox_process_node_thematics(&$vars, $field = 'field_category', $flatten = FALSE) {
  $vars['thematics'] = array();
  // Build category tree.
  if ($vars['node']) {
    $items = field_get_items('node', $vars['node'], $field);
    if ($items) {
      $tids = array();
      $terms = NULL;
      foreach ($items as $item) {
        if (!empty($item['tid'])) {
          $tids[] = $item['tid'];
        }
      }
      // Special case when we have only city and other tids is missed.
      if (count($tids) == 1) {
        $terms = taxonomy_get_parents_all(array_shift($tids));
        $tids = array();
        foreach ($terms as $term) {
          array_unshift($tids, $term->tid);
        }
      }

      if (!isset($terms)) {
        $terms = taxonomy_term_load_multiple($tids);
      }

      $depth = array();
      $terms_count = count($terms);
      if ($terms_count > 1) {
        $depth = culturebox_site_get_tids_sorted_by_depth($items);
      }

      for ($i = 0; $i < $terms_count; $i++) {
        $term = array_shift($terms);
        if (!empty($term)) {
          $options['attributes']['itemprop'] = 'genre';

          if (isset($depth['depth_0']) && in_array($term->tid, $depth['depth_0']) && !$flatten) {
            $vars['thematics'][] = culturebox_site_l(drupal_strtoupper($term->name), "taxonomy/term/{$term->tid}", $options);
          }
          else {
            $vars['thematics'][] = culturebox_site_l($term->name, "taxonomy/term/{$term->tid}", $options);
          }
        }
      }
    }
  }
}

/**
 * Custom preprocess for all icons on articles.
 */
function culturebox_preprocess_article_media_icon(&$vars, $type = 'small') {
  $vars['media_icon'] = '';

  if (_culturebox_check_main_media_is_video($vars)) {
    $class = ($type == 'small') ? 'btn-video' : 'btn-video-big';
    $vars['media_icon'] = culturebox_site_l(
        '&nbsp;', 'node/' . $vars['nid'], array(
      'html' => TRUE,
      'attributes' => array('class' => array('imitation-links', $class)),
        )
    );
  }
  elseif (_culturebox_check_main_media_is_diaporama($vars)) {
    $class = ($type == 'small') ? 'btn-show' : 'btn-show-big';
    $vars['media_icon'] = culturebox_site_l(
        '&nbsp;', 'node/' . $vars['nid'], array(
      'html' => TRUE,
      'attributes' => array('class' => array('imitation-links', $class)),
        )
    );
  }
}

/**
 * Preprocess variables for any entity.
 */
function culturebox_preprocess_entity(&$vars, $hook) {
  $view_mode = $vars['view_mode'];

  if ($vars['entity_type'] == 'culturebox_vote_model') {
    $preprocess = 'culturebox_preprocess_culturebox_vote_model__' . $vars['culturebox_vote_model']->type . '__' . str_replace('-', '_', $view_mode);

    if (function_exists($preprocess)) {
      $preprocess($vars, $hook);
    }
  }

  if ($vars['entity_type'] == 'asset') {
    $asset = $vars['asset'];
    switch ($asset->type) {
      case 'image':

        if ($view_mode == 'in_body' && ($node = menu_get_object())) {
          if ($node->type == 'article') {
            if (!empty($asset->field_asset_description[LANGUAGE_NONE][0]['safe_value'])) {
              $asset->alt_title = strip_tags($asset->field_asset_description[LANGUAGE_NONE][0]['safe_value']);
            }
          }
        }
        if (empty($vars['elements']['#entity']->in_editor) && !empty($vars['content']['field_asset_image'])) {
          // Note that we set raw value, as they will be plained as attributes.
          $alt = array();
          if (!empty($asset->alt_title)) {
            $alt[] = trim($asset->alt_title);
          }

          // If fields was empty, use title as alt attribute.
          if (empty($alt)) {
            $alt[] = $vars['elements']['#entity']->title;
          }
          $vars['content']['field_asset_image'][0]['#item']['title'] = $vars['content']['field_asset_image'][0]['#item']['alt'] = implode(' ', $alt);
        }

        if (!empty($asset->alt_title)) {
          $asset->alt_title = html_entity_decode($asset->alt_title, ENT_QUOTES, 'UTF-8');
        }

        break;

      case 'video':
        if (in_array($vars['view_mode'], array('in_body', 'in_body_emission', 'article_full_size'))) {
          $description = strip_tags(render($vars['content']['field_asset_description']), '<a><span>');
          if (!empty($description)) {
            $vars['description'] = $description;
          }
        }
        break;

      case 'timeline':
        if ($vars['view_mode'] == 'tooltip') {
          $vars['preview'] = theme(
              'image', array('path' => drupal_get_path('theme', 'culturebox_admin') . '/images/media_gallery/timeline.jpg')
          );
        }
        break;

      case 'diaporama':
        if (in_array($vars['view_mode'], array('in_body', 'in_body_emission', 'article_full_size')) && !empty($asset->in_editor) && !empty($vars['content']['field_asset_diaporama']['#items'])) {
          static $counter = 0;
          $vars['counter'] = $counter++;
          $style_name = ($vars['view_mode'] == 'article_full_size') ? 'article_view_full_main_image' : 'in_body_asset_diaporama_thumbnail';
          foreach ($vars['content']['field_asset_diaporama']['#items'] as $item) {
            if ($item['entity']->type == 'image') {
              $img_asset = $item['entity'];
              $image_items = field_get_items('asset', $img_asset, 'field_asset_image');
              $image = reset($image_items);
              $image_array = array(
                'path' => $image['uri'],
                'alt' => $item['entity']->title,
                'attributes' => array(
                  'longdesc' => $item['entity']->field_asset_image_copyright[LANGUAGE_NONE][0]['safe_value'],
                ),
                'style_name' => $style_name,
              );
              $thumbnail = theme('image_style', $image_array);
              $image_array['style_name'] = 'non_premium_asset_diaporama_viewport';
              theme('image_style', $image_array);
              $a = culturebox_site_l(
                  $thumbnail, image_style_url('non_premium_asset_diaporama_viewport', $image['uri']), array(
                'html' => TRUE,
                  )
              );
              $vars['diaporama_items_list'][] = array(
                'content' => $a,
                'type' => 'image',
              );
            }
            elseif ($item['entity']->type == 'video') {
              $video_asset = entity_load('asset', array($item['target_id']));
              $renderable_video_field = entity_view('asset', array(reset($video_asset)), 'diaporama_teaser');
              $description = check_plain($renderable_video_field['asset'][$item['target_id']]['#entity']->title);
              $image_array = array(
                'alt' => $description,
                'style_name' => $style_name,
              );

              if (!empty($item['entity']->field_asset_video[LANGUAGE_NONE][0]['snapshot'])) {
                $file_path = $item['entity']->field_asset_video[LANGUAGE_NONE][0]['snapshot'];
                $image_array['path'] = $file_path;
                $thumbnail = theme('imagecache_external', $image_array);
                if (empty($thumbnail)) {
                  $thumbnail = theme('image_style', $image_array);
                }
              }
              else {
                $file_path = CULTUREBOX_THEME_PATH . '/images/no_video.jpg';
                $thumbnail = theme('image', array(
                  'path' => $file_path,
                ));
              }

              $a = culturebox_site_l(
                  $thumbnail, '', array(
                'html' => TRUE,
                  )
              );
              $vars['diaporama_items_list'][] = array(
                'content' => $a,
                'type' => 'video',
                'video' => render($renderable_video_field),
                'description' => $video_asset['asset']->aid,
              );
            }
          }
        }
        elseif ((in_array($vars['view_mode'], array('in_body', 'in_body_emission', 'article_full_size')) || ($vars['view_mode'] == 'tooltip') || ($vars['view_mode'] == 'full')) && !empty($vars['content']['field_asset_diaporama']['#items'])) {
          foreach ($vars['content']['field_asset_diaporama']['#items'] as $item) {
            if ($item['entity']->type == 'image') {
              $style_name = ($vars['view_mode'] == 'article_full_size') ? 'article_view_full_main_image' : 'asset_in_body';
              $img_asset = $item['entity'];
              $image_items = field_get_items('asset', $img_asset, 'field_asset_image');
              $field_copyright = field_get_items('asset', $img_asset, 'field_asset_image_copyright');
              $field_copyright = (!empty($field_copyright[0]['safe_value'])) ? $field_copyright[0]['safe_value'] : '';
              $field_description = field_get_items('asset', $img_asset, 'field_asset_description');
              $field_description = (!empty($field_description[0]['safe_value'])) ? $field_description[0]['safe_value'] : '';
              if (!empty($image_items)) {
                $image = reset($image_items);
                $thumbnail_style_name = ($vars['view_mode'] == 'article_full_size') ? 'asset_slider_in_body_thumbnail' : 'asset_diaporama_in_body_thumbnail';
                $image_array = array(
                  'path' => $image['uri'],
                  'alt' => $item['entity']->title,
                  'attributes' => array(
                    'longdesc' => (!empty($field_copyright)) ? $field_copyright : '',
                  ),
                  'style_name' => $thumbnail_style_name,
                );
                $thumbnail = theme('image_style', $image_array);
                $image_array['style_name'] = $style_name;
                $image_content = theme('image_style', $image_array);
                $vars['diaporama_items_list'][] = array(
                  'thumbnail' => $thumbnail,
                  'content' => $image_content,
                  'txt' => ($vars['view_mode'] == 'article_full_size') ? '' : (!empty($field_description)) ? $field_description : '',
                  'copyright' => $field_copyright,
                  'title' => ($vars['view_mode'] == 'article_full_size') ? $field_description : check_plain($img_asset->title),
                );
              }
            }
            elseif ($item['entity']->type == 'video') {
              $video_asset = entity_load('asset', array($item['target_id']));
              $style_name = ($vars['view_mode'] == 'article_full_size') ? 'article_full_size' : 'in_body';
              $renderable_video_field = entity_view('asset', array(reset($video_asset)), $style_name);
              $title = check_plain($renderable_video_field['asset'][$item['target_id']]['#entity']->title);
              $description = render($renderable_video_field['asset'][$item['target_id']]['field_asset_description']);
              $content = render($renderable_video_field['asset'][$item['target_id']]['field_asset_video']);
              $thumbnail_style_name = ($vars['view_mode'] == 'article_full_size') ? 'asset_slider_in_body_thumbnail' : 'asset_diaporama_in_body_thumbnail';
              $image_array = array(
                'alt' => $description,
                'style_name' => $thumbnail_style_name,
              );

              if (!empty($item['entity']->field_asset_video[LANGUAGE_NONE][0]['snapshot'])) {
                $file_path = $item['entity']->field_asset_video[LANGUAGE_NONE][0]['snapshot'];
                $image_array['path'] = $file_path;

                $thumbnail = theme('imagecache_external', $image_array);
                if (empty($thumbnail)) {
                  $thumbnail = theme('image_style', $image_array);
                }
              }
              else {
                $file_path = CULTUREBOX_THEME_PATH . '/images/no_video.jpg';
                $thumbnail = theme('image', array(
                  'path' => $file_path,
                ));
              }
              $vars['diaporama_items_list'][] = array(
                'thumbnail' => $thumbnail,
                'content' => $content,
                'txt' => (!empty($description)) ? $description : '',
                'title' => $title,
              );
            }
          }
        }
    }

    $preprocess = 'culturebox_preprocess_asset__' . $asset->type . '__' . str_replace('-', '_', $view_mode);

    if (function_exists($preprocess)) {
      $preprocess($vars, $hook);
    }
  }

  if ('field_collection_item' == $vars['elements']['#entity_type']) {
    $field_collection_item = $vars['field_collection_item'];
    $preprocess = 'culturebox_preprocess_field_collection_item__' . $field_collection_item->field_name;

    if (function_exists($preprocess)) {
      $preprocess($vars, $hook);
    }

    if (!empty($vars['view_mode'])) {
      $preprocess = 'culturebox_preprocess_field_collection_item__' . $field_collection_item->field_name . '__' . $vars['view_mode'];
      if (function_exists($preprocess)) {
        $preprocess($vars, $hook);
      }
    }
  }

  if ('bean' == $vars['elements']['#entity_type']) {
    $bean = $vars['bean'];
    $preprocess = 'culturebox_preprocess_bean__' . $bean->type;

    if (function_exists($preprocess)) {
      $preprocess($vars, $hook);
    }

    if (!empty($vars['view_mode'])) {
      $preprocess = 'culturebox_preprocess_bean__' . $bean->type . '__' . $vars['view_mode'];
      if (function_exists($preprocess)) {
        $preprocess($vars, $hook);
      }
    }

    if ($bean->type == 'mise_en_avant') {
      if (!empty($vars['content']['field_link']) && !empty($vars['content']['field_illustration'])) {
        if (isset($vars['content']['field_link']['#items'][0]['fragment'])) {
          $path = url($vars['content']['field_link']['#items'][0]['url'], array('fragment' => $vars['content']['field_link']['#items'][0]['fragment']));
        }
        else {
          $path = $vars['content']['field_link']['#items'][0]['url'];
        }
        if (!empty($vars['content']['field_illustration']['#items'][0]['target_id'])) {
          $img = culturebox_site_get_original_image($vars['content']['field_illustration']['#items'][0]['target_id']);
          if (!empty($img)) {
            $vars['image_link'] = culturebox_site_l(
                $img, $path, array(
              'html' => 'TRUE',
              'attributes' => array_merge($vars['content']['field_link']['#items'][0]['attributes'], array('target' => '_blank')),
                )
            );
          }
        }
      }
    }
    elseif ($bean->type == 'free_html') {

    }
  }
}

/**
 * Preprocess variables for asset--video--slider-item.tpl.php.
 *
 * @see templates/asset/video/asset--video--slider-item.tpl.php
 */
function culturebox_preprocess_asset__video__slider_item(&$vars) {
  $field_asset_description = field_get_items('asset', $vars['asset'], 'field_asset_description');
  $description = !empty($field_asset_description[0]['safe_value']) ? $field_asset_description[0]['safe_value'] : '';
  $image_array = array(
    'alt' => $description,
    'title' => $vars['asset']->title,
    'style_name' => 'slider_item',
  );
  $asset_video_image = NULL;

  $field_asset_video = field_get_items('asset', $vars['asset'], 'field_asset_video');
  if (!empty($field_asset_video[0]['snapshot'])) {
    $image_array['path'] = $field_asset_video[0]['snapshot'];
    $asset_video_image = theme('imagecache_external', $image_array);
    if (empty($asset_video_image)) {
      $asset_video_image = theme('image_style', $image_array);
    }
  }
  else {
    $asset_video_image = theme('image', array(
      'path' => CULTUREBOX_THEME_PATH . '/images/no_slider_video.jpg',
    ));
  }
  $vars['asset_video_image'] = $asset_video_image;
}

/**
 * Preprocess variables for asset--video--related-video.tpl.php.
 *
 * @see templates/asset/video/asset--video--related-video.tpl.php
 */
function culturebox_preprocess_asset__video__related_video(&$vars) {
  $asset = $vars['asset'];
  if ($video = field_get_items('asset', $asset, 'field_asset_video')) {
    $url = 'related_video/' . $asset->aid . '/related_video_tooltip';
    $vars['title'] = culturebox_site_l($asset->title, $url, array(
      'attributes' => array(
        'class' => array('video-bonus', 'video-' . $asset->aid),
      ),
    ));

    $image = theme('image', array(
      'path' => CULTUREBOX_THEME_PATH . '/images/bonus.png',
      'width' => 178,
    ));
    $vars['image'] = culturebox_site_l($image, $url, array(
      'attributes' => array(
        'class' => array('video-bonus', 'video-' . $asset->aid),
      ),
      'html' => TRUE,
    ));
  }
}

/**
 * Preprocess variables for asset--video--widget-related-video.tpl.php.
 *
 * @see templates/asset/video/asset--video--widget-related-video.tpl.php
 */
function culturebox_preprocess_asset__video__widget_related_video(&$vars) {
  $asset = $vars['asset'];
  if ($video = field_get_items('asset', $asset, 'field_asset_video')) {
    $vars['title'] = culturebox_site_l($asset->title, '', array(
      'attributes' => array(
        'class' => array('asset-player-link'),
      ),
    ));

    $image = theme('image', array(
      'path' => CULTUREBOX_THEME_PATH . '/images/bonus.png',
      'width' => 130,
      'height' => 66,
    ));
    $vars['image'] = culturebox_site_l($image, '', array(
      'attributes' => array(
        'class' => array('asset-player-link'),
      ),
      'html' => TRUE,
    ));
  }
}

/**
 * Preprocess variables for asset--diaporama--slider.tpl.php.
 *
 * @see templates/node/article/asset--diaporama--slider.tpl.php
 */
function culturebox_preprocess_asset__diaporama__slider(&$vars) {
  $field_asset_diaporama = field_get_items('asset', $vars['asset'], 'field_asset_diaporama');
  $vars['diaporama_rendered_items'] = array();
  $aids = array();
  foreach ($field_asset_diaporama as $item) {
    if ($item['target_id']) {
      $aids[] = $item['target_id'];
    }
  }
  $assets = asset_load_multiple($aids);
  foreach ($assets as $asset) {
    $asset_view = $asset->view('slider_item');
    $vars['diaporama_rendered_items'][] = render($asset_view);
  }
  // @see culturebox_preprocess_node__article_slider().
  if (!empty($vars['asset']->nid)) {
    $vars['nid'] = $vars['asset']->nid;
  }
}

/**
 * Preprocess variables.
 */
function culturebox_preprocess_node__article_slider(&$vars) {
  $vars['is_slider'] = _culturebox_check_main_media_is_diaporama($vars, FALSE, FALSE);
  $vars['is_video'] = _culturebox_check_main_media_is_video($vars);
  if ($vars['is_video']) {
    $vars['icon_link'] = culturebox_site_l(
        '&nbsp;', 'node/' . $vars['nid'], array(
      'html' => TRUE,
      'attributes' => array('class' => array('imitation-links', 'btn-video-big')),
        )
    );
  }
  elseif ($vars['is_slider']) {
    $vars['icon_link'] = culturebox_site_l(
        '&nbsp;', 'node/' . $vars['nid'], array(
      'html' => TRUE,
      'attributes' => array('class' => array('imitation-links', 'btn-show-big')),
        )
    );
  }

  if ($thematic = _culturebox_get_node_main_category($vars['node'])) {
    $vars['category'] = $thematic;
  }

  if ($article_media = _culturebox_get_article_media_asset_view($vars)) {
    $vars['rendered_article_media'] = culturebox_site_l(
        drupal_render($article_media), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }
  $vars['title_link'] = culturebox_site_l($vars['node']->title, 'node/' . $vars['nid']);
}

/**
 * Preprocess variables.
 * @see sites/all/themes/culturebox/templates/node/article/node--article-topcategory-first.tpl.php
 */
function culturebox_preprocess_node__article_topcategory_first(&$vars) {
  $node = $vars['node'];

  if ($article_media = _culturebox_get_article_media_asset_view($vars)) {
    $vars['media'] = culturebox_site_l(
        drupal_render($article_media), "node/$node->nid", array(
      'html' => TRUE,
        )
    );
    culturebox_preprocess_article_media_icon($vars, 'big');
  }
}

/**
 * Preprocess variables.
 * @see sites/all/themes/culturebox/templates/node/article/node--article-subcategory-first.tpl.php
 */
function culturebox_preprocess_node__article_subcategory_first(&$vars) {
  culturebox_preprocess_node__article_topcategory_first($vars);
  $node = $vars['node'];
  $vars['category'] = _culturebox_get_node_main_category($node);
  $field_main_category = field_get_items('node', $node, 'field_main_category');
  if ($field_main_category && !empty($field_main_category[1])) {
    $subcategory_path = "taxonomy/term/{$field_main_category[1]['tid']}";
    $vars['link_all'] = culturebox_site_l(
        'Toute l\'actu', $subcategory_path, array(
      'attributes' => array(
        'class' => array(
          'btn-01',
        ),
      ),
        )
    );
  }
}

function culturebox_preprocess_asset__image__in_body(&$vars) {
  if (!empty($vars['field_asset_image'][0]['cropbox_height']) && !empty($vars['field_asset_image'][0]['cropbox_width']) && $vars['field_asset_image'][0]['cropbox_height'] > $vars['field_asset_image'][0]['cropbox_width']) {
    $vars['content']['field_asset_image'][0]['#image_style'] = 'asset_in_body';
    $vars['custom_classes'] = ' align-center';
  }

  if (!empty($vars['content']['field_asset_image_copyright'])) {
    $copyright = trim(strip_tags(render($vars['content']['field_asset_image_copyright']), '<a><span>'));

    if (!empty($copyright)) {
      $vars['copyright'] = $copyright;
    }
  }
}

function culturebox_preprocess_asset__image__full(&$vars) {
  $image = field_get_items('asset', $vars['asset'], 'field_asset_image');
  if ($image) {
    $vars['image'] = theme('image_style', array(
      'style_name' => 'article_view_full_main_image',
      'path' => $image[0]['uri'],
      'alt' => !empty($vars['asset']->alt_title) ? $vars['asset']->alt_title : $vars['asset']->title,
      'title' => !empty($vars['asset']->alt_title) ? $vars['asset']->alt_title : $vars['asset']->title,
    ));
  }

  $legend = field_get_items('asset', $vars['asset'], 'field_asset_description');
  if (!empty($legend[0]['safe_value'])) {
    $vars['legend'] = $legend[0]['safe_value'];
  }

  $copyright = field_get_items('asset', $vars['asset'], 'field_asset_image_copyright');
  if ($copyright) {
    $vars['copyright'] = $copyright[0]['safe_value'];
  }
}

/**
 * Preprocess variables for asset--image--article-full-size.tpl.php.
 *
 * @see templates/node/article/asset--image--article-full-size.tpl.php
 */
function culturebox_preprocess_asset__image__article_full_size(&$vars) {
  $image = field_get_items('asset', $vars['asset'], 'field_asset_image');
  if ($image) {
    $vars['image'] = theme('image_style', array(
      'style_name' => 'article_view_full_main_image',
      'path' => $image[0]['uri'],
      'alt' => !empty($vars['asset']->alt_title) ? $vars['asset']->alt_title : $vars['asset']->title,
      'title' => !empty($vars['asset']->alt_title) ? $vars['asset']->alt_title : $vars['asset']->title,
    ));
  }

  $legend = field_get_items('asset', $vars['asset'], 'field_asset_description');
  if ($legend) {
    if (!empty($legend[0]['safe_value'])) {
      $vars['legend'] = $legend[0]['safe_value'];
    }
    else {
      $vars['legend'] = $legend[0]['value'];
    }
  }

  $copyright = field_get_items('asset', $vars['asset'], 'field_asset_image_copyright');
  if ($copyright) {
    if (!empty($copyright[0]['safe_value'])) {
      $vars['copyright'] = $copyright[0]['safe_value'];
    }
    else {
      $vars['copyright'] = $copyright[0]['value'];
    }
  }
}

/**
 * Override or insert variables into the user profile template.
 */
function culturebox_preprocess_user_profile(&$vars, $hook) {
  if (!empty($vars['elements']['#view_mode'])) {
    $vars['theme_hook_suggestions'][] = 'user_profile__' . $vars['elements']['#view_mode'];

    $preprocess = 'culturebox_preprocess_user_profile__' . $vars['elements']['#view_mode'];

    if (function_exists($preprocess)) {
      $preprocess($vars, $hook);
    }
  }
}

/**
 * Override or insert variables into the user profile template.
 */
function culturebox_preprocess_user_profile__signature(&$vars) {
  if (!empty($vars['elements']['#account'])) {
    $account = $vars['elements']['#account'];
    // Name.
    $vars['user_name'] = array();
    if (!empty($vars['user_profile']['field_user_firstname'])) {
      $vars['user_name'][] = trim(render($vars['user_profile']['field_user_firstname']));
    }
    if (!empty($vars['user_profile']['field_user_lastname'])) {
      $vars['user_name'][] = trim(render($vars['user_profile']['field_user_lastname']));
    }
    if (!empty($vars['user_name'])) {
      $vars['user_name'] = implode(' ', $vars['user_name']);
    }

    // Image.
    if (!empty($account->picture)) {
      $vars['user_image'] = theme('image_style', array(
        'style_name' => 'user_view_signature',
        'path' => $account->picture->uri,
        'alt' => isset($vars['user_name']) ? $vars['user_name'] : '',
        'title' => isset($vars['user_name']) ? $vars['user_name'] : '',
      ));
    }
  }
}

function culturebox_preprocess_user_profile__signature_article(&$vars) {
  culturebox_preprocess_user_profile__signature($vars);
  if (!empty($vars['user_profile']['field_twitter'][0]['#element']['attributes'])) {
    $vars['user_profile']['field_twitter'][0]['#element']['attributes']['class'] = 'signature-twitter';
  }
}

/**
 * Override or insert variables into the taxonomy term template.
 */
function culturebox_preprocess_taxonomy_term(&$vars, $hook) {
  $view_mode = $vars['view_mode'];
  $term = $vars['term'];

  // Alt and title.
  if (!empty($vars['content']['field_illustration'][0]['asset'])) {
    $keys = array_keys($vars['content']['field_illustration'][0]['asset']);
    foreach ($keys as $key) {
      if (!empty($vars['content']['field_illustration'][0]['asset'][$key]['#entity'])) {
        $vars['content']['field_illustration'][0]['asset'][$key]['#entity']->alt_title = $term->name;
      }
    }
  }

  $preprocess[] = 'culturebox_preprocess_taxonomy_term__' . $term->vocabulary_machine_name . '_' . str_replace('-', '_', $view_mode);
  $preprocess[] = 'culturebox_preprocess_taxonomy_term__' . str_replace('-', '_', $view_mode);

  foreach ($preprocess as $function) {
    if (function_exists($function)) {
      $function($vars, $hook);
    }
  }

  $vars['theme_hook_suggestions'][] = 'taxonomy_term__' . $term->vocabulary_machine_name . '_' . str_replace('-', '_', $view_mode);
  $vars['theme_hook_suggestions'][] = 'taxonomy_term__' . str_replace('-', '_', $view_mode);
}

/**
 * Preprocess variables for bean--tous-les-films.tpl.php.
 *
 * @see templates/bean/bean--tous-les-films.tpl.php
 */
function culturebox_preprocess_bean__tous_les_films(&$vars) {
  $wrapper = entity_metadata_wrapper('bean', $vars['bean']);
  $items = $wrapper->field_mise_en_avant_text_item->value();

  switch ($wrapper->field_mise_en_avant_liste_format->value()) {
    case '0':
      $entity_view = entity_view('field_collection_item', $items);
      $vars['view'] = array_shift($entity_view);
      $format = 'compact';
      break;

    case '1':
      $entity_view = entity_view('field_collection_item', $items);
      $vars['view'] = array_shift($entity_view);
      if (!empty($vars['content']['field_mise_en_avant_image'])) {
        $vars['header_image'] = render($vars['content']['field_mise_en_avant_image']);
      }
      $header_title = $wrapper->field_liste_header_title->value();
      if (!empty($vars['field_mise_en_avant_url'][0]['url'])) {
        $url = $vars['field_mise_en_avant_url'][0]['url'];
      }
      $vars['header_text'] = culturebox_site_l(
          $header_title, !empty($url) ? $url : '', array(
        'html' => TRUE,
          )
      );
      $bottom_images = $wrapper->field_liste_link_images->value();
      foreach ($bottom_images as $item) {
        $item->{'#disable_text_links'} = TRUE;
      }
      $entity_view = entity_view('field_collection_item', $bottom_images);
      $vars['bottom_images'] = array_shift($entity_view);
      $format = 'extended';
      break;
  };
  $column_format = $wrapper->field_mise_en_avant_liste_column->value();
  $vars['double_column'] = !empty($column_format) ? TRUE : FALSE;
  $vars['format'] = $format;
}

/**
 * Preprocess variables for bean--bean__en_direct.tpl.php.
 *
 * @see templates/bean/bean--bean__en_direct.tpl.php
 */
function culturebox_preprocess_bean__en_direct(&$vars) {
  $term = culturebox_minisite_get_term();
  if (!empty($term)) {
    $vars['view'] = views_embed_view('minisite_content', 'en_direct', $term->tid);
  }
}

/**
 * Preprocess variables for bean--mise-en-avant-le-jury.tpl.php.
 *
 * @see templates/bean/bean--mise-en-avant-le-jury.tpl.php
 */
function culturebox_preprocess_bean__mise_en_avant_le_jury(&$vars) {
  $wrapper = entity_metadata_wrapper('bean', $vars['bean']);
  $items = $wrapper->field_mise_en_avant_image_item->value();

  switch ($wrapper->field_mise_en_avant_format->value()) {
    case '0':
      $entity_view = entity_view('field_collection_item', $items, 'multiportrait');
      $vars['view'] = array_shift($entity_view);
      $format = 'multiportrait';
      break;

    case '1':
      $entity_view = entity_view('field_collection_item', $items, 'multipaysage');
      $vars['view'] = array_shift($entity_view);
      $format = 'multipaysage';
      break;

    case '2':
      $entity_view = entity_view('field_collection_item', $items, 'paysage_unitaire');
      $vars['view'] = array_shift($entity_view);
      $format = 'paysage_unitaire';
      break;
  };
  $vars['format'] = $format;
}

/**
 * Preprocess variables for field_collection_item__field_classment_item.tpl.php.
 *
 * @see templates/field_collection_item__field_classment_item.tpl.php
 */
function culturebox_preprocess_field_collection_item__field_classment_item(&$vars) {
  if (!empty($vars['field_mise_en_avant_url'][0]['url'])) {
    $url = $vars['field_mise_en_avant_url'][0]['url'];
  }
  if (!empty($vars['content']['field_mise_en_avant_name'])) {
    $title = render($vars['content']['field_mise_en_avant_name']);
  }
  if (!empty($url) && !empty($title)) {
    $vars['item'] = culturebox_site_l(
        $title, !empty($url) ? $url : '', array('html' => TRUE)
    );
  }
  elseif (!empty($title)) {
    $vars['item'] = $title;
  }
}

function culturebox_preprocess_field_collection_item__field_live_featured_external__widget_related_video(&$vars) {
  culturebox_preprocess_field_collection_item__field_live_featured_external__dans_actu($vars);
}

function culturebox_preprocess_field_collection_item__field_live_featured_external__dans_actu(&$vars) {
  $link = field_get_items('field_collection_item', $vars['field_collection_item'], 'field_featured_external_link');

  if (!empty($link)) {
    if ($article_media = _culturebox_get_article_media_asset_view($vars)) {
      $vars['rendered_article_media'] = culturebox_site_l(
          drupal_render($article_media), $link[0]['url'], array(
        'html' => TRUE,
        'attributes' => array(
          'class' => 'imitation-links',
          'target' => '_blank',
        ),
          )
      );
    }

    $vars['title_link'] = culturebox_site_l($link[0]['title'], $link[0]['url'], array(
      'attributes' => array('external' => TRUE, 'class' => 'imitation-links', 'target' => '_blank'),
    ));

    $desc = field_get_items('field_collection_item', $vars['field_collection_item'], 'field_featured_external_desc');

    if (!empty($desc)) {
      $vars['description'] = culturebox_site_l(truncate_utf8($desc[0]['safe_value'], 70, TRUE, TRUE), $link[0]['url'], array(
        'attributes' => array('external' => TRUE, 'class' => 'imitation-links', 'target' => '_blank'),
      ));
    }
  }
}

/**
 * Preprocess variables for field_collection_item__field_mise_en_avant_text_item.tpl.php.
 *
 * @see templates/field_collection_item__field_mise_en_avant_text_item.tpl.php
 */
function culturebox_preprocess_field_collection_item__field_mise_en_avant_text_item(&$vars) {
  if (!empty($vars['field_mise_en_avant_url'][0]['url'])) {
    $url = $vars['field_mise_en_avant_url'][0]['url'];
  }
  if (!empty($vars['content']['field_mise_en_avant_film'])) {
    $film = render($vars['content']['field_mise_en_avant_film']);
  }
  $vars['film_link'] = culturebox_site_l(
      $film, !empty($url) ? $url : ''
  );
}

/**
 * Preprocess variables for field_collection_item__field_liste_link_images.tpl.php.
 *
 * @see templates/field_collection_item__field_liste_link_images.tpl.php
 */
function culturebox_preprocess_field_collection_item__field_liste_link_images(&$vars) {
  if (!empty($vars['field_mise_en_avant_url'][0]['url'])) {
    $url = $vars['field_mise_en_avant_url'][0]['url'];
  }
  if (!empty($vars['content']['field_liste_bottom_images'])) {
    $image = render($vars['content']['field_liste_bottom_images']);
    $vars['item_link'] = culturebox_site_l(
        $image, !empty($url) ? $url : '', array(
      'html' => TRUE,
        )
    );
  }
  elseif (!empty($url) && empty($vars['field_collection_item']->{'#disable_text_links'})) {
    $vars['item_link'] = culturebox_site_l(
        $url, !empty($url) ? $url : ''
    );
  }
}

/**
 * Preprocess variables for field_collection_item__field_mise_en_avant_image_item_paysage_unitaire.tpl.php.
 *
 * @see templates/field_collection_item__field_mise_en_avant_image_item_paysage_unitaire.tpl.php
 */
function culturebox_preprocess_field_collection_item__field_mise_en_avant_image_item__paysage_unitaire(&$vars) {
  if (!empty($vars['content']['field_mise_en_avant_image'])) {
    if (!empty($vars['field_mise_en_avant_url'][0]['url'])) {
      $url = $vars['field_mise_en_avant_url'][0]['url'];
    }
    $vars['image'] = culturebox_site_l(
        render($vars['content']['field_mise_en_avant_image']), !empty($url) ? $url : '', array(
      'html' => TRUE,
      'attributes' => array('class' => array('imitation-links')),
        )
    );
  }
  if (!empty($vars['content']['field_mise_en_avant_name'])) {
    $name = render($vars['content']['field_mise_en_avant_name']);
  }
  if (!empty($vars['content']['field_mise_en_avant_family'])) {
    $family = render($vars['content']['field_mise_en_avant_family']);
  }

  $title = $name . (!empty($family) ? ' ' . $family : '');
  $vars['person'] = culturebox_site_l(
      "<span class=\"slide-description\">$title</span>", !empty($url) ? $url : '', array(
    'html' => TRUE,
      )
  );

  if (!empty($vars['content']['field_mise_en_avant_description'])) {
    $vars['description'] = render($vars['content']['field_mise_en_avant_description']);
  }
}

/**
 * Preprocess variables for field_collection_item__field_mise_en_avant_image_item_multipaysage.tpl.php.
 *
 * @see templates/field_collection_item__field_mise_en_avant_image_item_multipaysage.tpl.php
 */
function culturebox_preprocess_field_collection_item__field_mise_en_avant_image_item__multipaysage(&$vars) {
  if (!empty($vars['content']['field_mise_en_avant_image'])) {
    $image = render($vars['content']['field_mise_en_avant_image']);
    if (!empty($vars['field_mise_en_avant_url'][0]['url'])) {
      $url = $vars['field_mise_en_avant_url'][0]['url'];
    }
    if (!empty($vars['content']['field_mise_en_avant_name'])) {
      $name = render($vars['content']['field_mise_en_avant_name']);
    }
    if (!empty($vars['content']['field_mise_en_avant_family'])) {
      $family = render($vars['content']['field_mise_en_avant_family']);
    }

    $title = $name . (!empty($family) ? '<br/>' . $family : '');
    $vars['multipaysage_item'] = culturebox_site_l(
        $image . "<span class=\"text\"><strong>$title</strong></span>", !empty($url) ? $url : '', array(
      'html' => TRUE,
        )
    );
  }
}

/**
 * Preprocess variables for field_collection_item__field_mise_en_avant_image_item_multiportrait.tpl.php.
 *
 * @see templates/field_collection_item__field_mise_en_avant_image_item_multiportrait.tpl.php
 */
function culturebox_preprocess_field_collection_item__field_mise_en_avant_image_item__multiportrait(&$vars) {
  if (!empty($vars['content']['field_mise_en_avant_image'])) {
    $image = render($vars['content']['field_mise_en_avant_image']);
    if (!empty($vars['field_mise_en_avant_url'][0]['url'])) {
      $url = $vars['field_mise_en_avant_url'][0]['url'];
    }
    if (!empty($vars['content']['field_mise_en_avant_name'])) {
      $name = render($vars['content']['field_mise_en_avant_name']);
    }
    if (!empty($vars['content']['field_mise_en_avant_family'])) {
      $family = render($vars['content']['field_mise_en_avant_family']);
    }

    $title = $name . (!empty($family) ? '<br/>' . $family : '');
    $vars['multiportrait_item'] = culturebox_site_l(
        $image . "<span class=\"slide-description\">$title</span>", !empty($url) ? $url : '', array(
      'html' => TRUE,
        )
    );
  }
}

/**
 * Preprocess variables for bean--bean__classement.tpl.php.
 *
 * @see templates/bean/bean--bean__classement.tpl.php
 */
function culturebox_preprocess_bean__classement(&$vars) {
  if (!empty($vars['field_mise_en_avant_url'][0]['url'])) {
    $url = $vars['field_mise_en_avant_url'][0]['url'];
  }
  if (!empty($vars['content']['field_mise_en_avant_name'])) {
    $title = render($vars['content']['field_mise_en_avant_name']);
  }
  if (!empty($title) && !empty($url)) {
    $vars['button'] = culturebox_site_l(
        $title, $url, array(
      'attributes' => array('class' => array('btn-01', 'btn-r')),
      'html' => TRUE,
        )
    );
  }
  $wrapper = entity_metadata_wrapper('bean', $vars['bean']);
  $items = $wrapper->field_classment_item->value();
  $entity_view = entity_view('field_collection_item', $items);
  $vars['items'] = array_shift($entity_view);
}

/**
 * Preprocess variables for bean--mise-en-avant--emissions-culture.tpl.php.
 *
 * @see templates/bean/bean--mise-en-avant--emissions-culture.tpl.php
 */
function culturebox_preprocess_bean__mise_en_avant__emissions_culture(&$vars) {

  $second_title = field_get_items('bean', $vars['bean'], 'field_second_title');
  $second_title = (!empty($second_title)) ? '<br />' . check_plain($second_title[0]['value']) : '';

  $date_time = field_get_items('bean', $vars['bean'], 'field_date_time');
  $date_time = (!empty($date_time)) ? '<span>' . check_plain($date_time[0]['value']) . '</span>' : '';

  if (!empty($vars['content']['field_link']) && !empty($vars['content']['field_illustration']['#items'][0]['target_id'])) {
    $link = culturebox_site_get_original_image($vars['content']['field_illustration']['#items'][0]['target_id']);
    $link .= '<span class="text">';
    $link .= '<strong>' . check_plain($vars['bean']->title) . $second_title . '</strong>' . $date_time . '</span>';
    if (isset($vars['content']['field_link']['#items'][0]['fragment'])) {
      $path = url($vars['content']['field_link']['#items'][0]['url'], array('fragment' => $vars['content']['field_link']['#items'][0]['fragment']));
    }
    else {
      $path = $vars['content']['field_link']['#items'][0]['url'];
    }
    $vars['link'] = culturebox_site_l(
        $link, $path, array(
      'html' => 'TRUE',
      'attributes' => $vars['content']['field_link']['#items'][0]['attributes'],
        )
    );
  }
}

function culturebox_preprocess_bean__mise_en_avant_blog__emissions_culture(&$vars) {
  $second_title = field_get_items('bean', $vars['bean'], 'field_second_title');
  $second_title = (!empty($second_title)) ? '<br />' . check_plain($second_title[0]['value']) : '';

  if (!empty($vars['content']['field_link']) && !empty($vars['content']['field_illustration']['#items'][0]['target_id'])) {
    $link = culturebox_site_get_original_image($vars['content']['field_illustration']['#items'][0]['target_id']);
    $link .= '<span class="text">';
    $link .= '<strong>' . check_plain($vars['bean']->title) . '</strong><span>' . $second_title . '</span></span>';
    if (isset($vars['content']['field_link']['#items'][0]['fragment'])) {
      $path = url($vars['content']['field_link']['#items'][0]['url'], array('fragment' => $vars['content']['field_link']['#items'][0]['fragment']));
    }
    else {
      $path = $vars['content']['field_link']['#items'][0]['url'];
    }
    $vars['link'] = culturebox_site_l(
        $link, $path, array(
      'html' => 'TRUE',
      'attributes' => $vars['content']['field_link']['#items'][0]['attributes'],
        )
    );
  }
}

/**
 * Preprocess variables for node--article-very-small.tpl.php.
 *
 * @see templates/node/article/node--article-very-small.tpl.php
 */
function culturebox_preprocess_node__article_very_small(&$vars) {
  if ($article_media = _culturebox_get_article_media_asset_view($vars)) {
    $vars['rendered_article_media'] = culturebox_site_l(
        drupal_render($article_media), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }

  culturebox_preprocess_article_media_icon($vars);
  $vars['title_link'] = culturebox_site_l($vars['node']->title, 'node/' . $vars['nid']);
}

/**
 * Preprocess variables for node--article-very-small.tpl.php.
 *
 * @see templates/node/article/node--article-very-small.tpl.php
 */
function culturebox_preprocess_node__article_dans_actu(&$vars) {
  // Categories.
  if ($thematic = _culturebox_get_node_main_category($vars['node'])) {
    $vars['category'] = $thematic;
  }

  if ($article_media = _culturebox_get_article_media_asset_view($vars)) {
    $vars['rendered_article_media'] = culturebox_site_l(
        drupal_render($article_media), 'node/' . $vars['nid'], array(
      'html' => TRUE,
      'attributes' => array(
        'class' => 'imitation-links',
        'target' => '_blank',
      ),
        )
    );
  }

  culturebox_preprocess_article_media_icon($vars);
  $vars['title_link'] = culturebox_site_l($vars['node']->title, 'node/' . $vars['nid'], array(
    'attributes' => array('target' => '_blank'),
  ));
}

/**
 * Preprocess variables for node--article-read-also.tpl.php.
 *
 * @see templates/node/article/node--article-read-also.tpl.php
 */
function culturebox_preprocess_node__article_read_also(&$vars) {
  if (!empty($vars['node']->field_geolocalization)) {
    $vars['localization'] = '';
  }

  if ($article_media = _culturebox_get_article_media_asset_view($vars)) {
    $vars['rendered_article_media'] = culturebox_site_l(
        drupal_render($article_media), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }

  $vars['title_link'] = culturebox_site_l($vars['node']->title, 'node/' . $vars['nid']);
  culturebox_preprocess_article_media_icon($vars);
}

/**
 * Preprocess variables for node--article-widget-related-video.tpl.php.
 *
 * @see templates/node/article/node--article-widget-related-video.tpl.php
 */
function culturebox_preprocess_node__article_widget_related_video(&$vars) {
  // Categories.
  $field_main_category = field_get_items('node', $vars['node'], 'field_main_category');

  if ($field_main_category) {
    $thematic_id = end($field_main_category);
    $thematic = taxonomy_term_load($thematic_id['tid']);
    if ($thematic) {
      $vars['category'] = culturebox_site_l($thematic->name, "taxonomy/term/{$thematic->tid}", array(
        'attributes' => array('class' => 'imitation-links', 'target' => '_blank'),
      ));
    }
  }

  if ($article_media = _culturebox_get_article_media_asset_view($vars)) {
    $vars['rendered_article_media'] = culturebox_site_l(
        drupal_render($article_media), 'node/' . $vars['nid'], array(
      'html' => TRUE,
      'attributes' => array('class' => 'imitation-links', 'target' => '_blank'),
        )
    );
  }

  $vars['title_link'] = culturebox_site_l(
      $vars['node']->title, 'node/' . $vars['nid'], array('attributes' => array('class' => 'imitation-links', 'target' => '_blank'))
  );
  culturebox_preprocess_article_media_icon($vars);
}

/**
 * Preprocess variables for node--feed_item-widget-related-video.tpl.php.
 *
 * @see templates/node/article/node--feed_item-widget-related-video.tpl.php
 */
function culturebox_preprocess_node__feed_item_widget_related_video(&$vars) {
  _culturebox_preprocess_node__feed_item($vars);
}

/**
 * Preprocess variables for node--article-read-also-festivals.tpl.php.
 * е
 * @see templates/node/article/node--article-read-also-festivals.tpl.php
 */
function culturebox_preprocess_node__article_read_also_festivals(&$vars) {
  if (!empty($vars['node']->field_geolocalization)) {
    $vars['localization'] = '';
  }

  if ($article_media = _culturebox_get_article_media_asset_view($vars)) {
    $vars['rendered_article_media'] = culturebox_site_l(
        drupal_render($article_media), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }

  $vars['title_link'] = culturebox_site_l($vars['node']->title, 'node/' . $vars['nid']);
}

/**
 * Preprocess variables for node--article-read-also-small.tpl.php.
 *
 * @see templates/node/article/node--article-read-also-small.tpl.php
 */
function culturebox_preprocess_node__article_read_also_small(&$vars) {
  $city = _culturebox_get_node_article_geolocalization($vars['node']);
  $title = $vars['node']->title;

  $vars['title_link'] = culturebox_site_l(
      !empty($city) ? "{$title} - {$city}" : $title, 'node/' . $vars['nid'], array(
    'html' => TRUE,
    'attributes' => array(
      'class' => array('origin-link'),
    ),
      )
  );
}

/**
 * Preprocess variables for node--live-home-from-editor-big-2.tpl.php.
 *
 * @see templates/node/live/node--live-home-from-editor-big-2.tpl.php
 */
function culturebox_preprocess_node__live_home_from_editor_big_2(&$vars) {
  $node = $vars['node'];

  // Status.
  $status = field_get_items('node', $node, 'field_live_status');
  $status = $status[0]['value'];

  // Player integration.
  $vars['vid_switch'] = FALSE;

  if (variable_get('cb_enable_player_on_minisite_hp', TRUE) && !empty($status) && in_array($status, array(CULTUREBOX_LIVE_STATUS_LIVE_DIRECT, CULTUREBOX_LIVE_STATUS_LIVE_REPLAY, CULTUREBOX_LIVE_STATUS_LIVE_LAST_CHANCE))) {
    $vars['vid_switch'] = TRUE;
    $video_url = _culturebox_live_get_video_url($node);

    if ($video_url) {
      $vars['player_link'] = '<a href="' . $video_url . '" class="player"></a>';

      // Request to get flash player URL.
      $flash_player_url = _culturebox_emission_get_flash_player_url();
      if (!empty($flash_player_url)) {
        $player_script_vars['default_width'] = 653;
        $player_script_vars['default_height'] = 360;

        $vars['player_script'] = theme('culturebox_site_player_ftv_script', $player_script_vars);
        $vars['tags'] = _culturebox_get_node_live_main_category($vars['node']);
        $vars['title'] = culturebox_site_l($vars['node']->title, "node/{$vars['node']->nid}");

        drupal_add_js($flash_player_url, array('weight' => 10));
      }
    }
  }
  else {
    culturebox_preprocess_node__live_home_from_editor_medium_2($vars);
  }
}

/**
 * Preprocess variables for node--live-home-from-editor-medium-2.tpl.php.
 *
 * @see templates/node/live/node--live-home-from-editor-medium-2.tpl.php
 */
function culturebox_preprocess_node__live_home_from_editor_medium_2(&$vars) {
  // On affiche le picto play et l'encart du statut sur les pages des mini-sites.
  if ((!empty($vars['node']->home_from_editor_medium_2_custom_display)) || (!empty($vars['view']->name) && !empty($vars['view']->current_display) && $vars['view']->name == 'minisite_content' && $vars['view']->current_display == 'panel_pane_minisite_hp')) {
    $vars['show_picto_play_status'] = TRUE;
  }
  else {
    $vars['show_picto_play_status'] = FALSE;
  }

  // Image.
  if (!empty($vars['content']['field_live_media'])) {
    $illustration = drupal_render($vars['content']['field_live_media']);
    $vars['media'] = culturebox_site_l(
        $illustration, 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }
  $vars['tags'] = _culturebox_get_node_live_main_category($vars['node']);

  // Status icaon.
  _culturebox_preprocess_live_button_play($vars, NULL, 'play-medium');

  // Title.
  $vars['title'] = culturebox_site_l(
      $vars['node']->title, "node/{$vars['node']->nid}"
  );

  // On n'affiche la description que sous certaines conditions.
  if (!empty($vars['node']->home_from_editor_medium_2_custom_display)) {
    $description = field_get_items('node', $vars['node'], 'field_live_chapo');
    if ($description) {
      $vars['description'] = strip_tags(truncate_utf8($description[0]['value'], 150, TRUE, TRUE));
    }
  }

  _culturebox_preprocess_live_channel($vars);
}

/**
 * Preprocess variables for node--live-home-from-editor-medium.tpl.php.
 *
 * @see templates/node/live/node--live-home-from-editor-medium.tpl.php
 */
function culturebox_preprocess_node__live_home_from_editor_medium(&$vars) {
  $node = $vars['node'];

  // Category & place tags.
  $category = _culturebox_get_node_live_main_category($node);
  $place = _culturebox_get_node_live_geolocalization($node);
  $vars['tags'] = sprintf('%s <span>%s</span>', $category, $place);

  // Image section.
  $live_media = '';
  $media_view = _culturebox_get_first_live_media_asset_view($vars);

  if (!empty($media_view)) {
    $live_media = drupal_render($media_view);
  }
  $vars['media'] = culturebox_site_l(
      $live_media, 'node/' . $vars['nid'], array(
    'html' => TRUE,
      )
  );

  _culturebox_preprocess_live_button_play($vars);

  // Title.
  $vars['title'] = culturebox_site_l(
      $node->title, "node/{$node->nid}"
  );

  _culturebox_preprocess_live_channel($vars);
}

/**
 * Preprocess variables for node--live-full.tpl.php.
 *
 * @see templates/node/live/node--live-full.tpl.php
 */
function culturebox_preprocess_node__live_full(&$vars) {
  $node = $vars['node'];
  global $base_url;

  // Common preprocess for culturebox and culturebox_mobile themes.
  culturebox_live_common_part_preprocess_live_full($vars);

  // Status icon.
  _culturebox_preprocess_live_button_play($vars);

  // Counter section.
  $bientot_statuses = array(
    CULTUREBOX_LIVE_STATUS_LIVE_PROCHAINEMENT,
    CULTUREBOX_LIVE_STATUS_LIVE_AVANT_LE_DIRECT,
    CULTUREBOX_LIVE_STATUS_LIVE_RETARD
  );
  if (!empty($vars['live_start_time']) && !empty($vars['field_live_countdown'][0]['value']) && in_array($vars['status'], $bientot_statuses)) {
    // Get remain time before live start.
    $timezone = new DateTimeZone('Europe/Paris');
    $start_time_obj = new DateTime($vars['live_start_time'][0]['value'], $timezone);

    if (!empty($vars['field_live_delay'][0]['value'])) {
      $start_time_obj->modify('+' . $vars['field_live_delay'][0]['value'] . ' minutes');
    }

    $time = new DateTime('now', $timezone);
    $offset = $time->getOffset() / 60;

    drupal_add_js(array('CultureboxLive' => array('CountdownTime' => $start_time_obj->format('m/d/Y H:i:s'), 'offset' => $offset)), 'setting');
    $vars['counter'] = TRUE;
  }

  // Line section.
  // 1.1 Thematic associations.
  // 1.1 Thematic associations.
  $vars['main_thematic'] = _culturebox_get_node_live_main_category($vars['node']);

  // Share links.
  $links = array('export' => array('class' => 'share-export'));
  $vars['share_links'] = theme('share_links_ajax', array('node' => $node)) . theme('live_share_links', array('links' => $links, 'node' => $node));
  $vars['inform_popup_share'] = theme('live_inform_popup', $vars);

  if ($vars['status'] == CULTUREBOX_LIVE_STATUS_LIVE_REPLAY || $vars['status'] == CULTUREBOX_LIVE_STATUS_LIVE_LAST_CHANCE || $vars['status'] == CULTUREBOX_LIVE_STATUS_LIVE_DIRECT) {
    $video_id = _culturebox_live_get_video_url($node, 'id');

    if ($video_id) {
      // URL du flux vidéo embed (pour Twitter).
      $embed_video_url = _culturebox_site_get_embed_video_url($video_id, $node);

      // URL du flux vidéo source (pour Google).
      if ($vars['status'] == CULTUREBOX_LIVE_STATUS_LIVE_REPLAY) {
        $video_url = _culturebox_site_get_video_url($video_id, $node);
      }

      if (!empty($embed_video_url)) {
        $card = array(
          '#tag' => 'meta',
          '#attributes' => array(
            'name' => 'twitter:card',
            'content' => 'player'
          ),
        );
        drupal_add_html_head($card, 'twitter_card_card');

        // Et on ajoute les balises meta pour Twitter concernant la vidéo.
        $card = array(
          '#tag' => 'meta',
          '#attributes' => array(
            'name' => 'twitter:player',
            'content' => $embed_video_url
          ),
        );
        drupal_add_html_head($card, 'twitter_card_player');

        $card = array(
          '#tag' => 'meta',
          '#attributes' => array(
            'name' => 'twitter:player:width',
            'content' => 1000
          ),
        );
        drupal_add_html_head($card, 'twitter_card_player_width');

        $card = array(
          '#tag' => 'meta',
          '#attributes' => array(
            'name' => 'twitter:player:height',
            'content' => 505
          ),
        );
        drupal_add_html_head($card, 'twitter_card_player_height');
      }

      if (!empty($video_url)) {
        $microdata_video_object[] = '<div id="live-seo-container" itemscope itemtype="http://schema.org/VideoObject">';
        $microdata_video_object[] = '	<meta itemprop="name" content="' . check_plain($node->title) . '" />';
        $microdata_video_object[] = '	<meta itemprop="contentUrl" content="' . $video_url . '" />';
        $microdata_video_object[] = '	<meta itemprop="url" content="' . url("node/{$node->nid}", array('absolute' => TRUE, 'base_url' => _culturebox_pagedevie_get_urlfo())) . '" />';
        if (!empty($vars['replay_end_date_iso8601'])) {
          $microdata_video_object[] = '	<meta itemprop="expires" content="' . $vars['replay_end_date_iso8601'] . '" />';
        }
        $microdata_video_object[] = '	<meta itemprop="isFamilyFriendly" content="True" />';
        $microdata_video_object[] = '	<meta itemprop="playerType" content="flash" />';
        if ($node->field_live_catchline) {
          $microdata_video_object[] = '	<meta itemprop="description" content="' . strip_tags($node->field_live_catchline[LANGUAGE_NONE][0]['value']) . '" />';
        }
        if ($node->field_live_duration) {
          $duration = $node->field_live_duration[LANGUAGE_NONE][0]['value'];
          $heure = floor($duration / 60);
          $minute = $duration - (60 * $heure);
          $duration_iso8601 = 'PT' . ($heure > 0 ? $heure . 'H' : '') . ($minute > 0 ? $minute . 'M' : '');
          $microdata_video_object[] = '	<meta itemprop="duration" content="' . $duration_iso8601 . '">';
        }

        if (!empty($vars['field_live_media'][0]['entity']->field_asset_image[LANGUAGE_NONE][0]['uri'])) {
          // On génère des thumbnails de bonne qualité, mais pas trop gros : https://support.google.com/webmasters/answer/156442?hl=en.
          $image_url = image_style_url('tvc_mea_1280_720', $vars['field_live_media'][0]['entity']->field_asset_image[LANGUAGE_NONE][0]['uri']);

          $microdata_video_object[] = '<meta itemprop="thumbnailUrl" content="' . $image_url . '" />';

          $image_size = getimagesize($image_url);

          if ($image_size) {
            $microdata_video_object[] = '
          <span itemprop="thumbnail" itemscope itemtype="http://schema.org/ImageObject">
          <link itemprop="url" href="' . $image_url . '">
          <meta itemprop="width" content="' . $image_size[0] . '">
          <meta itemprop="height" content="' . $image_size[1] . '">
          </span>';
          }
        }

        $microdata_video_object[] = '</div>';
        $vars['microdata_video_object'] = implode(PHP_EOL, $microdata_video_object);
      }
    }

    $vars['status'] = '';
  }

  $nde_binaural = field_get_items('node', $vars['node'], 'field_extrait_lie_binaural');

  if (!empty($nde_binaural)) {
    if (!empty($nde_binaural[0]['entity'])) {
      $extrait_binaural = $nde_binaural[0]['entity'];
    }
    elseif (!empty($nde_binaural[0]['target_id'])) {
      $extrait_binaural = node_load($nde_binaural[0]['target_id']);
    }
  }
  else {
    $default = '216863';
    $cb_extrait_binaural = 'cb_extrait_binaural';
    $nd_extrait_binaural = variable_get($cb_extrait_binaural, $default);
    $extrait_binaural = node_load($nd_extrait_binaural);
  }
  if (!empty($extrait_binaural)) {
    $ext_lie_binaural = node_view($extrait_binaural, 'most_viewed_sidebar');
    $vars['extrait_binaural'] = render($ext_lie_binaural);
  }

  // Image bandeau.
  if (!empty($node->field_mini_site_bandeau_image)) {
    $bandeau_image = !empty($node->field_mini_site_bandeau_image[LANGUAGE_NONE][0]['entity']) ? $node->field_mini_site_bandeau_image[LANGUAGE_NONE][0]['entity'] : asset_load($node->field_mini_site_bandeau_image[LANGUAGE_NONE][0]['target_id']);

    if (!empty($bandeau_image->field_asset_image[LANGUAGE_NONE][0]['uri'])) {
      $bandeau_image = theme('image_style', array('style_name' => 'mini_site_header', 'path' => $bandeau_image->field_asset_image[LANGUAGE_NONE][0]['uri']));

      if (!empty($node->field_link[LANGUAGE_NONE][0]['url'])) {
        $bandeau_image = l($bandeau_image, $node->field_link[LANGUAGE_NONE][0]['url'], array('html' => TRUE, 'attributes' => array('target' => '_blank')));
      }

      $vars['bandeau_image'] = $bandeau_image;
    }
  }
  // Multilingue live
  if (!empty($node->field_btn_live_multi[LANGUAGE_NONE][0]['value']) && $node->field_btn_live_multi[LANGUAGE_NONE][0]['value'] == TRUE) {
    $vars['multilingue'] = $node->field_btn_live_multi[LANGUAGE_NONE][0]['value'];
    $language = field_get_items('node', $node, 'field_langue_choice');
    $language = $language[0]['value'];

    $id_related = array(
     'en' => $id_related_en = field_get_items('node', $node, 'field_page_liee'),
      'fr' => $id_related_fr = field_get_items('node', $node, 'field_page_liee_2'),
      'man' => $id_related_man = field_get_items('node', $node, 'field_page_liee_3'),
    );

    if (!empty($id_related)) {
      foreach($id_related as $key=>$node_related){
        if($language == $key){
          $node_related = FALSE;
        }
        if($node_related){
          if(!empty($node_related[0]['target_id'])) {
            $url_related['flag-' . $key] = url('node/' . $node_related[0]['target_id']);
          }
        }
      }

      if (!empty($url_related)) {
        $vars['url_related'] = theme('culturebox_live_flag',array('url_related' => $url_related));
      }
    }
  }

  $vars['replay_en_attente'] = !empty($node->field_replay_en_attente[LANGUAGE_NONE][0]['value']) && $node->field_live_status[LANGUAGE_NONE][0]['value'] == CULTUREBOX_LIVE_STATUS_LIVE_ENTRE_LE_DIRECT_ET_LE_REPLAY;
}

/**
 * Preprocess variables for node--live-description.tpl.php.
 *
 * @see templates/node/live/node--live-description.tpl.php
 */
function culturebox_preprocess_node__live_description(&$vars) {
  $accroche_title = field_get_items('node', $vars['node'], 'field_live_catchline_title');
  if ($accroche_title) {
    $vars['accroche_title'] = $accroche_title[0]['value'];
  }
  if (!empty($vars['content']['field_live_catchline'])) {
    $vars['accroche'] = drupal_render($vars['content']['field_live_catchline']);
  }
  $signature_accroche = field_get_items('node', $vars['node'], 'field_live_catchline_signature');
  if ($signature_accroche) {
    $vars['signature_accroche'] = $signature_accroche[0]['value'];
  }

  $chapo = field_get_items('node', $vars['node'], 'field_live_chapo');
  if ($chapo) {
    $vars['chapo'] = $chapo[0]['value'];
  }

  if (!empty($vars['content']['field_live_description'])) {
    $vars['description'] = drupal_render($vars['content']['field_live_description']);
  }
  $start_time = field_get_items('node', $vars['node'], 'field_live_start_time');
  if ($start_time) {
    $start_time = new DateObject($start_time[0]['value']);
    $vars['start_time'] = date_format_date($start_time, 'custom', 'd F Y', 'fr');
  }

  // Duration.
  $duration = field_get_items('node', $vars['node'], 'field_live_duration');
  if ($duration) {
    $h = floor($duration[0]['value'] / 60);
    if ($h) {
      $text[] = '@hh';
    }
    $m = $duration[0]['value'] - $h * 60;
    $text[] = '@mmin';
    $text = implode(' ', $text);
    $vars['duration'] = strtr($text, array('@h' => $h, '@m' => $m));
  }

  // Droits.
  $replay_start = field_get_items('node', $vars['node'], 'field_live_replay_start_time');
  $replay_end = field_get_items('node', $vars['node'], 'field_live_replay_end_time');
  if ($replay_start && $replay_end) {
    $replay_start = new DateTime($replay_start[0]['value']);
    $replay_end = new DateTime($replay_end[0]['value']);
    $diff = $replay_end->diff($replay_start);
    if ($diff->days) {
      $vars['droits'] = format_plural($diff->days, '1 jour', '@count jours');
    }
  }

  // Lieu.
  $place = field_get_items('node', $vars['node'], 'field_live_tag_place');
  if ($place) {
    $place = taxonomy_term_load($place[0]['tid']);
    $vars['place'] = $place->name;
  }

  // Categories.
  $category = field_get_items('node', $vars['node'], 'field_live_main_category');
  if ($category) {
    if ($category = taxonomy_term_load($category[0]['tid'])) {
      $vars['category'] = $category->name;
    }
  }

  // Festival.
  $festival = field_get_items('node', $vars['node'], 'field_live_tag_event');
  if ($festival) {
    if ($festival = taxonomy_term_load($festival[0]['tid'])) {
      $event_pilier = field_get_items('taxonomy_term', $festival, 'field_event_access');

      if (!(!empty($event_pilier) && count($event_pilier) == 1 && $event_pilier[0]['value'] == 'emission')) {
        $vars['festival'] = $festival->name;
      }
    }
  }

  // Free text.
  $vars['free_text'] = array();
  $free_text_group1 = field_get_items('node', $vars['node'], 'field_live_free_field1_group');
  if ($free_text_group1) {
    foreach ($free_text_group1 as $group_item1) {
      $collection = field_collection_item_load($group_item1['value']);
      $label = field_get_items('field_collection_item', $collection, 'field_live_free_field1_label');
      if ($label) {
        $label = $label[0]['value'];
      }
      $value = field_get_items('field_collection_item', $collection, 'field_live_free_field1_value');
      if ($value) {
        $value = $value[0]['value'];
      }
      $vars['free_text'][] = array('label' => $label, 'value' => $value);
    }
  }
  $free_text_group2 = field_get_items('node', $vars['node'], 'field_live_free_field2_group');
  if ($free_text_group2) {
    foreach ($free_text_group2 as $group_item2) {
      $collection = field_collection_item_load($group_item2['value']);
      $label = field_get_items('field_collection_item', $collection, 'field_live_free_field2_label');
      if ($label) {
        $label = $label[0]['value'];
      }
      $value = field_get_items('field_collection_item', $collection, 'field_live_free_field2_value');
      if ($value) {
        $value = $value[0]['value'];
      }
      $vars['free_text'][] = array('label' => $label, 'value' => $value);
    }
  }

  $vars['distribution_fields'] = array(
    'field_live_artist' => 'Artiste',
    'field_live_group' => 'Groupe',
    'field_live_author' => 'Auteur',
    'field_live_composer' => 'Compositeur',
    'field_live_director' => 'Metteur en scène',
    'field_live_choreographer' => 'Chorégraphe',
    'field_live_conductor' => 'Chef d\'orchestre',
    'field_live_orchestra' => 'Orchestre',
    'field_live_company' => 'Compagnie',
    'field_live_costumes' => 'Costumes',
    'field_live_lights' => 'Lumière',
    'field_live_decors' => 'Décors',
    'field_live_choeur' => 'Choeur',
  );

  $vars['actors_fields'] = array(
    'field_live_actors' => 'Acteurs (+rôles)',
    'field_live_dancers' => 'Danseurs',
    'field_live_soloists' => 'Solistes',
  );

  if (!empty($vars['node']->field_hide_block[LANGUAGE_NONE][0]['value']) && $vars['node']->field_hide_block[LANGUAGE_NONE][0]['value'] == '1') {
    _culturebox_live_get_automatic_description($vars);
  }
}

/**
 * Preprocess variables for node--live-home-from-editor-big.tpl.php.
 *
 * @see templates/node/live/node--live-home-from-editor-big.tpl.php
 */
function culturebox_preprocess_node__live_home_from_editor_big(&$vars) {
  $node = $vars['node'];

  // Status.
  $status = field_get_items('node', $node, 'field_live_status');
  $status = $status[0]['value'];

  $bientot_statuses = array(
    CULTUREBOX_LIVE_STATUS_LIVE_PROCHAINEMENT,
    CULTUREBOX_LIVE_STATUS_LIVE_AVANT_LE_DIRECT,
    CULTUREBOX_LIVE_STATUS_LIVE_RETARD
  );

  // Image section.
  $live_media = '';
  $media_view = _culturebox_get_first_live_media_asset_view($vars);

  if (!empty($media_view)) {
    $live_media = drupal_render($media_view);
  }
  $vars['media'] = culturebox_site_l(
    $live_media, 'node/' . $vars['nid'], array(
      'html' => TRUE,
    )
  );

  if (!empty($vars['field_live_start_time2'])) {
    $field_data = field_get_items('node', $vars['node'], 'field_live_start_time2');
    $field_live_start_time = array_shift($field_data);
    $live_start_time = $field_live_start_time['value'];
  }

  // Status depended logic.
  $vars['status'] = _culturebox_get_node_live_status($vars);

  // Status icon.
  // Counter section.
  _culturebox_preprocess_live_button_play($vars);
  if (!empty($live_start_time) &&
    !empty($vars['field_live_countdown'][LANGUAGE_NONE][0]['value']) && in_array($status, $bientot_statuses)
  ) {
    // Get remain time before live start.
    $timezone = new DateTimeZone('Europe/Paris');
    $start_time_obj = new DateTime($live_start_time, $timezone);
    $time = new DateTime('now', $timezone);
    $offset = $time->getOffset() / 60;

    drupal_add_js(array(
        'CultureboxLive' => array(
          'CountdownTime' => $start_time_obj->format('m/d/Y H:i:s'),
          'offset' => $offset
        )
      ), 'setting');
    $vars['counter'] = TRUE;
  }

  // Line section.
  // 1.1 Thematic associations.
  $vars['main_thematic'] = _culturebox_get_node_live_main_category($vars['node']);

  // 1.2 Geolocalization associations.
  if (!empty($vars['field_live_tag_place'])) {
    $field_live_tag_place = field_get_items('node', $node, 'field_live_tag_place');
    foreach ($field_live_tag_place as $item) {
      $tids[] = $item['tid'];
    }
    $terms = taxonomy_term_load_multiple($tids);

    // Get field terms sorted by it's depth. So we can now use it to build tags in write order.
    $sorted_places = culturebox_site_get_tids_sorted_by_depth($tids);
    if (!empty($sorted_places)) {
      foreach (array('depth_2', 'depth_1', 'depth_0') as $depth) {
        if (!empty($sorted_places[$depth])) {
          $tid = array_shift($sorted_places[$depth]);
          $places[] = culturebox_site_l($terms[$tid]->name, "taxonomy/term/$tid");
        }
      }
    }
  }

  if (!empty($live_start_time)) {
    // Live start date.
    $live_start_timestamp = strtotime($live_start_time);
    $date = date('d/m/Y', $live_start_timestamp);
    $time = date('H\hi', $live_start_timestamp);

    $vars['live_start_date'] = sprintf('<span>%s</span> %s', $date, $time);
  }

  // Button more. Can have several states: read more | view video.
  $before = $vars['status'] == 'before';
  $button_text = $before ? 'En savoir plus' : 'Voir la vidéo';
  $button_img = $before ? theme('image', array('path' => CULTUREBOX_THEME_PATH . '/images/ico-more.png')) :
    theme('image', array(
      'path' => CULTUREBOX_THEME_PATH . '/images/ico-play.png',
      'attributes' => array('class' => array('ico-play')),
    ));

  $vars['more_button'] = culturebox_site_l(
    $button_img . $button_text, "node/{$node->nid}", array(
      'html' => TRUE,
    )
  );

  // Share links.
  $vars['share_links'] = theme('live_share_popup', array('node' => $node));
  $vars['title_link'] = culturebox_site_l($vars['node']->title, "node/${vars['nid']}");
  $vars['inform_popup'] = theme('live_inform_popup', $vars);

  culturebox_live_preprocess_live_status($vars);
  if (!empty($vars['direct']) || !empty($vars['replay'])) {
    $vars['reagir'] = culturebox_site_l('Réagir', "node/$node->nid", array(
      'attributes' => array('class' => 'imitation-link'),
    ));
  }

  $vars['emission_section'] = culturebox_emission_is_emission_section();

  _culturebox_preprocess_live_channel($vars);

  // Player.
  $vars['vid_switch'] = FALSE;

  if (!empty($status) && current_path() == 'live') {
    $vars['vid_switch'] = in_array($status, array(CULTUREBOX_LIVE_STATUS_LIVE_DIRECT, CULTUREBOX_LIVE_STATUS_LIVE_REPLAY, CULTUREBOX_LIVE_STATUS_LIVE_LAST_CHANCE));

    if ($vars['vid_switch']) {
      $url_options = array('absolute' => TRUE);

      // On force l'URL de prod uniquement en prod pour éviter les soucis sur le compteur de partage.
      if (variable_get('ftven_env') == 'prod') {
        $url_options['base_url'] = 'http://culturebox.francetvinfo.fr';
      }

      $vars['custom_share_url'] = url("node/{$node->nid}", $url_options);
      $vars['custom_share_text'] = urlencode($node->title);

      $video_url = _culturebox_live_get_video_url($node);

      // Player link.
      if ($video_url) {
        $player_script_vars = array();
        $vars['player_link'] = '<a href="' . $video_url . '" class="player"></a>';

        // Request to get flash player URL.
        $flash_player_url = _culturebox_emission_get_flash_player_url();
        if (!empty($flash_player_url)) {
          $player_script_vars['default_width'] = 960;
          $player_script_vars['default_height'] = 485;
          if (current_path() === 'live') {
            $player_script_vars['default_showad'] = FALSE;
          }

          $mini_site = field_get_items('node', $node, 'field_mini_site');
          if (!empty($mini_site[0]['tid']) && $mini_site[0]['tid'] == '56657') {
            $player_script_vars['default_showad'] = FALSE;
          }

          $vars['player_script'] = theme('culturebox_site_player_ftv_script', $player_script_vars);

          drupal_add_js($flash_player_url, array('weight' => 10));
        }
      }
    }
  }
}

/**
 * Provide special label for nodes from Minisite on minisite pages.
 */
function _culturebox_preprocess_prepare_article_rubrique_link(&$vars) {
  if (culturebox_minisite_is_minisite_page() || culturebox_minisite_get_hub_term()) {
    $items = field_get_items('node', $vars['node'], 'field_mini_site');
    if (!empty($items)) {
      $minisite_page = array_pop($items);
      if (!empty($minisite_page['tid']) && ($term = taxonomy_term_load($minisite_page['tid']))) {
        $vars['category'] = culturebox_site_l($term->name, 'taxonomy/term/' . $minisite_page['tid']);
      }
    }

    $vars['inside_minisite'] = TRUE;
  }
}

/**
 * Preprocess variables for node--article-home-from-editor-big-2.tpl.php.
 *
 * @see templates/node/article/node--article-home-from-editor-big-2.tpl.php
 */
function culturebox_preprocess_node__article_home_from_editor_big_2(&$vars) {
  culturebox_preprocess_node__article_home_from_editor_big($vars);
}

/**
 * Preprocess variables for node--article-home-from-editor-big.tpl.php.
 *
 * @see templates/node/article/node--article-home-from-editor-big.tpl.php
 */
function culturebox_preprocess_node__article_home_from_editor_big(&$vars) {
  _culturebox_preprocess_prepare_article_rubrique_link($vars);

  if (empty($vars['inside_minisite']) || culturebox_minisite_get_hub_term()) {
    if ($thematic = _culturebox_get_node_main_category($vars['node'])) {
      $vars['category'] = $thematic;
    }

    if ($city = _culturebox_get_node_article_geolocalization($vars['node'])) {
      $vars['city'] = $city;
    }
  }

  if ($article_media = _culturebox_get_article_media_asset_view($vars)) {
    $vars['rendered_article_media'] = culturebox_site_l(
        drupal_render($article_media), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }

  $vars['title_link'] = culturebox_site_l($vars['node']->title, 'node/' . $vars['nid']);
  culturebox_preprocess_article_media_icon($vars, 'big');

  if (!empty($vars['node']->field_article_catchline[LANGUAGE_NONE][0]['value']) && culturebox_minisite_get_hub_term()) {
    $vars['accroche'] = truncate_utf8($vars['node']->field_article_catchline[LANGUAGE_NONE][0]['value'], 200, TRUE, TRUE);
  }
}

function culturebox_preprocess_node__live_home_from_editor_small_2(&$vars) {
  $node = $vars['node'];

  $vars['category'] = _culturebox_get_node_live_main_category($node);

  $live_media = '';
  $media_view = _culturebox_get_first_live_media_asset_view($vars);

  if (!empty($media_view)) {
    $live_media = drupal_render($media_view);
  }
  $vars['rendered_article_media'] = culturebox_site_l(
      $live_media, 'node/' . $vars['nid'], array(
    'html' => TRUE,
      )
  );
  $parent = taxonomy_term_load($vars['field_live_emission_internal'][LANGUAGE_NONE][0]['tid']);
  $vars['tags'] = culturebox_site_l($parent->name, 'taxonomy/term/' . $parent->tid);
  $get_channel = field_get_items('node', $vars['node'], 'field_live_type_channel');
  $vars['channel'] = $get_channel[0]['value'];
  $vars['title_link'] = culturebox_site_l($vars['node']->title, 'node/' . $vars['nid']);
  $vars['date'] = culturebox_live_date($vars);
  $vars['duree'] = culturebox_live_duree($vars);
}

function culturebox_preprocess_node__extrait_live_home_from_editor_small_2(&$vars) {
  $live_media = '';
  $media_view = _culturebox_get_first_live_media_asset_view($vars, NULL, FALSE, 'field_extrait_illustration');

  if (!empty($media_view)) {
    $live_media = drupal_render($media_view);
  }
  $vars['rendered_article_media'] = culturebox_site_l(
      $live_media . '<div class="fleche-play play-medium"></div>', 'node/' . $vars['nid'], array(
    'html' => TRUE,
    'attributes' => array('class' => 'img')
      )
  );

  $vars['title_link'] = culturebox_site_l($vars['node']->title, 'node/' . $vars['nid']);

  if (!empty($vars['node']->field_body[LANGUAGE_NONE][0]['value']) && culturebox_minisite_get_hub_term()) {
    $vars['accroche'] = truncate_utf8($vars['node']->field_body[LANGUAGE_NONE][0]['value'], 200, TRUE, TRUE);
  }
}

/**
 * Preprocess variables for node--feed_item-home-from-editor-main.tpl.php.
 */
function culturebox_preprocess_node__feed_item_home_from_editor_big(&$vars) {
  _culturebox_preprocess_node__feed_item($vars);
}

/**
 * Preprocess variables for node--feed_item-home-from-editor-medium-2.tpl.php.
 *
 * @see templates/node/article/node--feed_item-home-from-editor-medium-2.tpl.php
 */
function culturebox_preprocess_node__feed_item_home_from_editor_medium_2(&$vars) {
  _culturebox_preprocess_node__feed_item($vars);

  $description = field_get_items('node', $vars['node'], 'field_description');
  if ($description) {
    $vars['description'] = truncate_utf8($description[0]['value'], 150, TRUE, TRUE);
  }
}

/**
 * Preprocess variables for node--article-home-from-editor-medium-2.tpl.php.
 *
 * @see templates/node/article/node--article-home-from-editor-medium-2.tpl.php
 */
function culturebox_preprocess_node__article_home_from_editor_medium_2(&$vars) {
  _culturebox_preprocess_prepare_article_rubrique_link($vars);

  culturebox_preprocess_node__article_home_from_editor_medium($vars);

  if (!culturebox_minisite_get_hub_term()) {
    $description = field_get_items('node', $vars['node'], 'field_article_catchline');
    if ($description) {
      $vars['description'] = truncate_utf8($description[0]['safe_value'], 150, TRUE, TRUE);
    }
  }
}

/**
 * Preprocess variables for node--article-home-from-editor-medium.tpl.php.
 *
 * @see templates/node/article/node--article-home-from-editor-medium.tpl.php
 */
function culturebox_preprocess_node__article_home_from_editor_medium(&$vars) {
  if (empty($vars['inside_minisite'])) {
    if ($thematic = _culturebox_get_node_main_category($vars['node'])) {
      $vars['category'] = $thematic;
    }

    if ($city = _culturebox_get_node_article_geolocalization($vars['node'])) {
      $vars['city'] = $city;
    }
  }

  if ($article_media = _culturebox_get_article_media_asset_view($vars)) {
    $vars['rendered_article_media'] = culturebox_site_l(
        drupal_render($article_media), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }

  $vars['title_link'] = culturebox_site_l($vars['node']->title, 'node/' . $vars['nid']);
  culturebox_preprocess_article_media_icon($vars, 'big');
}

/**
 * Preprocess variables for node--article-home-from-editor-small.tpl.php.
 *
 * @see sites/all/themes/culturebox/templates/node/article/node--article-home-from-editor-small.tpl.php
 */
function culturebox_preprocess_node__article_home_from_editor_small(&$vars) {
  // Categories.
  if ($thematic = _culturebox_get_node_main_category($vars['node'])) {
    $vars['category'] = $thematic;
  }

  // City.
  if ($city = _culturebox_get_node_article_geolocalization($vars['node'])) {
    $vars['city'] = $city;
  }

  $vars['title_link'] = culturebox_site_l($vars['node']->title, "node/{$vars['nid']}");
}

/**
 * Preprocess variables for node--article-most-viewed.tpl.php.
 *
 * @see templates/node/article/node--article-most-viewed.tpl.php
 */
function culturebox_preprocess_node__article_most_viewed(&$vars) {
  // Categories.
  if ($thematic = _culturebox_get_node_main_category($vars['node'])) {
    $vars['category'] = $thematic;
  }
  if (!empty($vars['view']->row_index)) {
    $vars['position'] = $vars['view']->row_index + 1;
  }

  if ($article_media = _culturebox_get_article_media_asset_view($vars)) {
    $vars['rendered_article_media'] = culturebox_site_l(
        drupal_render($article_media), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }

  culturebox_preprocess_article_media_icon($vars);
  $vars['title_link'] = culturebox_site_l($vars['node']->title, "node/{$vars['nid']}");
}

/**
 * Preprocess variables for node--article-most-viewed-home.tpl.php.
 *
 * @see templates/node/article/node--article-most-viewed-home.tpl.php
 */
function culturebox_preprocess_node__article_most_viewed_home(&$vars) {
  // Categories.
  if ($thematic = _culturebox_get_node_main_category($vars['node'])) {
    $vars['category'] = $thematic;
  }

  if ($article_media = _culturebox_get_article_media_asset_view($vars)) {
    $vars['rendered_article_media'] = culturebox_site_l(
        drupal_render($article_media), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }

  culturebox_preprocess_article_media_icon($vars);
  $vars['title_link'] = culturebox_site_l($vars['node']->title, "node/{$vars['nid']}");
}

function culturebox_preprocess_taxonomy_term__event_articles_summary(&$vars) {
  $query = db_select('node', 'n');
  $query->innerJoin('field_data_field_events', 'fdfe', 'n.nid = fdfe.entity_id');
  $query->addField('n', 'nid');
  $query->condition('fdfe.field_events_tid', $vars['tid']);
  $query->condition('n.status', NODE_PUBLISHED);
  $query->condition('n.type', 'article');

  if (arg(0) == 'node') {
    $node = menu_get_object();
    
    if ($node && $node->type == 'article') {
      $query->condition('n.nid', $node->nid, '<>');
    }
  }

  $query->orderBy('n.nid', 'DESC');
  $query->range(0, 2);
  $nids = $query->execute()->fetchCol();

  if (!empty($nids)) {
    $nodes = node_load_multiple($nids);

    foreach ($nodes as $article) {
      $vars['articles'][] = culturebox_site_l($article->title, "node/{$article->nid}");
    }
  }

  if (!empty($vars['content']['field_illustration_secondaire']['#items'][0]['target_id'])) {
    $img = culturebox_site_get_original_image($vars['content']['field_illustration_secondaire']['#items'][0]['target_id']);
    if ($img) {
      $vars['term_link'] = culturebox_site_l(
        $img, "taxonomy/term/{$vars['tid']}", array(
        'html' => TRUE,
        )
      );
    }
    else {
      $vars['term_link'] = '';
    }
  }
  elseif (!empty($vars['content']['field_illustration']['#items'][0]['target_id'])) {
    $img = culturebox_site_get_original_image($vars['content']['field_illustration']['#items'][0]['target_id']);
    if ($img) {
      $vars['term_link'] = culturebox_site_l(
        $img, "taxonomy/term/{$vars['tid']}", array(
        'html' => TRUE,
        )
      );
    }
    else {
      $vars['term_link'] = '';
    }
  }
  else {
    $vars['term_link'] = '';
  }
}

/**
 * Override or insert variables into the taxonomy term template for event vocabulary teaser view mode.
 *
 * @see templates/taxonomy/event/taxonomy-term--event-teaser.tpl.php
 */
function culturebox_preprocess_taxonomy_term__event_teaser(&$vars) {
  // Image.
  $illustration = field_get_items('taxonomy_term', $vars['term'], 'field_illustration_secondaire');
  if (!$illustration) {
    $illustration = field_get_items('taxonomy_term', $vars['term'], 'field_illustration');
  }
  if ($illustration) {
    $event_image_asset = asset_load($illustration[0]['target_id']);
    $image = field_get_items('asset', $event_image_asset, 'field_asset_image');

    if ($image) {
      $vars['event_image'] = theme(
          'image_style', array(
        'path' => $image[0]['uri'],
        'style_name' => 'article_view_full_event_image',
        'alt' => !empty($image[0]->title) ? $image[0]->title : $vars['term_name'],
        'title' => !empty($image[0]->title) ? $image[0]->title : $vars['term_name'],
          )
      );

      $vars['event_image'] = culturebox_site_l($vars['event_image'], "taxonomy/term/{$vars['tid']}", array('html' => TRUE));
    }
  }

  // Date - Year.
  if ($date_start = field_get_items('taxonomy_term', $vars['term'], 'field_date_start')) {
    $event_timezone = new DateTimeZone($date_start[0]['timezone']);
    $event_date = new DateTime($date_start[0]['value'], $event_timezone);

    $vars['event_date'] = date_format($event_date, 'Y');
  }
}

/**
 * Override or insert variables into the taxonomy term template for event vocabulary search view mode.
 */
function culturebox_preprocess_taxonomy_term__event_search(&$vars) {
  // Image.
  $illustration = field_get_items('taxonomy_term', $vars['term'], 'field_illustration');
  if ($illustration) {
    $event_image_asset = asset_load($illustration[0]['target_id']);
    if ($event_image_asset) {
      $image = field_get_items('asset', $event_image_asset, 'field_asset_image');
      if ($image) {
        $vars['event_image'] = theme('image_style', array(
          'path' => $image[0]['uri'],
          'style_name' => 'article_view_full_event_image',
          'alt' => !empty($image[0]->title) ? $image[0]->title : $vars['term_name'],
          'title' => !empty($image[0]->title) ? $image[0]->title : $vars['term_name'],
        ));
        $vars['event_image'] = culturebox_site_l(
            $vars['event_image'], "taxonomy/term/{$vars['tid']}", array(
          'html' => TRUE,
            )
        );
      }
    }
  }
  // Date - Year.
  $date_start = field_get_items('taxonomy_term', $vars['term'], 'field_date_start');
  if ($date_start) {
    $event_timezone = new DateTimeZone($date_start[0]['timezone']);
    $event_date = new DateTime(
        $date_start[0]['value'], $event_timezone
    );
    $vars['event_date'] = date_format($event_date, 'Y');
  }
  // City.
  if ($cities = _culturebox_get_taxonomy_event_geolocalization($vars['term'])) {
    $vars['event_cities'] = $cities;
  }

  $vars['name'] = culturebox_site_l(
      $vars['term']->name, 'taxonomy/term/' . $vars['term']->tid
  );
}

/**
 * Override or insert variables into the taxonomy term template for event vocabulary geolocalization view mode.
 */
function culturebox_preprocess_taxonomy_term__event_live_evenemets(&$vars) {
  $term = $vars['term'];

  // Show live & article for actu home & only live nodes at live home pages.
  if (!culturebox_live_is_live_section()) {
    $type = 'live,article';
    $vars['actu'] = TRUE;
  }
  else {
    $type = 'live';
  }
  // Image section.
  if (!empty($vars['content']['field_illustration'])) {
    $media = render($vars['content']['field_illustration']);
    $vars['media'] = culturebox_site_l(
        $media, "taxonomy/term/{$term->tid}", array(
      'html' => TRUE,
        )
    );
  }

  // Terms live/article nodes.
  $view = views_get_view('lives_list');
  if ($view && $view->access('live_home_evenements')) {
    $view_result = $view->preview('live_home_evenements', array($term->tid, $type));
    if (count($view->result) > 4) {
      $vars['show_arrows'] = TRUE;
    }
    $vars['live_nodes'] = $view_result;
  }
}

/**
 * Preprocess variables.
 */
function culturebox_preprocess_taxonomy_term__mini_site_live_evenemets(&$vars) {
  $term = $vars['term'];

  // Image section.
  if (!empty($vars['content']['field_mini_site_main_image'])) {
    $media = render($vars['content']['field_mini_site_main_image']);
    $vars['media'] = l($media, "taxonomy/term/{$term->tid}", array('html' => TRUE));
  }

  if (drupal_is_front_page() || arg(0) == 'live') {
    $type = array('live', 'article');
    $vars['actu'] = TRUE;
  }
  else {
    $type = array('live');
  }

    $query = db_select('node', 'n');
    $query->addField('n', 'nid');
    $query->leftJoin('field_data_field_live_status', 'fls', 'n.nid = fls.entity_id');
    $query->leftJoin('field_data_field_live_published_date', 'flpd', 'n.nid = flpd.entity_id');

    if (in_array('article', $type)) {
      $query->leftJoin('field_data_field_published_date', 'fpd', 'n.nid = fpd.entity_id');
      $query->addExpression("IFNULL(fpd.field_published_date_value, UNIX_TIMESTAMP(flpd.field_live_published_date_value))", 'published_date');

      $query->addExpression("IFNULL(fls.field_live_status_value, 'not_after_replay')", 'live_status');
    }
    else {
      $query->addField('flpd', 'field_live_published_date_value', 'published_date');

      $query->addField('fls', 'field_live_status_value', 'live_status');
    }

    $query->join('field_data_field_mini_site', 'fms', 'n.nid = fms.entity_id');
    $query->condition('fms.field_mini_site_tid', $term->tid);
    $query->condition('n.status', NODE_PUBLISHED);
    $query->condition('n.type', $type, 'IN');
    $query->having("live_status <> 'after_replay'");
    $query->orderBy('published_date', 'DESC');
    $query->range(0, 32);
    $nids = $query->execute()->fetchCol();

  if (!empty($nids)) {
    $content = '';
    $nodes = node_load_multiple($nids);

    $content .= '<ul class="event-slider-list">';
    foreach ($nodes as $node) {
      $node_view = node_view($node, 'evenement');
      $content .= '<li' . ($node->type == 'live' ? ' class="black"' : '') . '>' . render($node_view) . '</li>';
    }
    $content .= '</ul>';

    if (count($nodes) > 4) {
      $vars['show_arrows'] = TRUE;
    }
    $vars['live_nodes'] = $content;
  }
}

/**
 * Override or insert variables into the taxonomy term template for event vocabulary geolocalization view mode.
 */
function culturebox_preprocess_taxonomy_term__event_festivals(&$vars) {
  $term = $vars['term'];

  // Image section.
  if (!empty($vars['content']['field_illustration'])) {
    $media = render($vars['content']['field_illustration']);
    $vars['media'] = culturebox_site_l(
        "{$media}<span>{$term->name}</span>", "taxonomy/term/{$term->tid}", array(
      'html' => TRUE,
        )
    );
  }
}

/**
 * Override or insert variables into the taxonomy term template for mini-site vocabulary geolocalization view mode.
 */
function culturebox_preprocess_taxonomy_term__mini_site_festivals(&$vars) {
  $term = $vars['term'];

  // Image section.
  if (!empty($vars['content']['field_mini_site_main_image'])) {
    $media = render($vars['content']['field_mini_site_main_image']);
    $vars['media'] = culturebox_site_l(
        "{$media}<span>{$term->name}</span>", "taxonomy/term/{$term->tid}", array(
      'html' => TRUE,
        )
    );
  }
}

/**
 * Override or insert variables into the taxonomy term template for event vocabulary geolocalization view mode.
 */
function culturebox_preprocess_taxonomy_term__geolocalization_search(&$vars) {
  // Image.
  $illustration = field_get_items('taxonomy_term', $vars['term'], 'field_illustration');
  if ($illustration) {
    $event_image_asset = asset_load($illustration[0]['target_id']);
    $image = field_get_items('asset', $event_image_asset, 'field_asset_image');
    if ($image) {
      $vars['event_image'] = theme('image_style', array(
        'path' => $image[0]['uri'],
        'style_name' => 'article_view_full_event_image',
        'alt' => !empty($image[0]->title) ? $image[0]->title : $vars['term_name'],
        'title' => !empty($image[0]->title) ? $image[0]->title : $vars['term_name'],
      ));
      $vars['event_image'] = culturebox_site_l(
          $vars['event_image'], "taxonomy/term/{$vars['tid']}", array(
        'html' => TRUE,
          )
      );
    }
  }
  // Date - Year.
  $date_start = field_get_items('taxonomy_term', $vars['term'], 'field_date_start');
  if ($date_start) {
    $event_timezone = new DateTimeZone($date_start[0]['timezone']);
    $event_date = new DateTime(
        $date_start[0]['value'], $event_timezone
    );
    $vars['event_date'] = date_format($event_date, 'Y');
  }
  // City.
  if ($cities = _culturebox_get_taxonomy_event_geolocalization($vars['term'])) {
    $vars['event_cities'] = $cities;
  }
  if (!empty($vars['description'])) {
    $alter = array(
      'strip_tags' => FALSE,
      'text_length' => 200,
      'word_boundary' => TRUE,
      'ellipsis' => TRUE,
    );
    $vars['description'] = better_trimmed_trim_text($alter, $vars['description']);
  }

  $vars['name'] = culturebox_site_l(
      $vars['term']->name, 'taxonomy/term/' . $vars['term']->tid
  );
}

/**
 * Override or insert variables into the taxonomy term template for event vocabulary full view mode.
 */
function culturebox_preprocess_taxonomy_term__event_full(&$vars, $hook) {
  $is_emission_related = FALSE;

  $piliers = field_get_items('taxonomy_term', $vars['term'], 'field_event_access');

  if (!empty($piliers)) {
    foreach ($piliers as $pilier) {
      if ($pilier['value'] == 'emission') {
        menu_tree_set_path('main-menu', 'live');

        $is_emission_related = TRUE;
        break;
      }
    }
  }

  if (!$is_emission_related) {
    // Date.
    $date_start = field_get_items('taxonomy_term', $vars['term'], 'field_date_start');
    if ($date_start && !empty($date_start[0]['value'])) {
      $date_start = new DateObject($date_start[0]['value'], $date_start[0]['timezone']);
      $start_month_number = date_format_date($date_start, 'custom', 'n', 'fr');
      $start_year_number = date_format_date($date_start, 'custom', 'Y', 'fr');
      $date_start_format = '\D\U d';
      $date_start_in_format = date_format_date($date_start, 'custom', 'd.m.Y', 'fr');
    }
    $date_end = field_get_items('taxonomy_term', $vars['term'], 'field_date_end');
    if ($date_end && !empty($date_end[0]['value'])) {
      $date_end = new DateObject($date_end[0]['value'], $date_end[0]['timezone']);
      $end_month_number = date_format_date($date_end, 'custom', 'n', 'fr');
      $end_year_number = date_format_date($date_end, 'custom', 'Y', 'fr');
      $date_end_format = '\A\U d F';
      $date_end_in_format = date_format_date($date_end, 'custom', 'd.m.Y', 'fr');
    }
    if (!empty($date_start_in_format) && !empty($date_end_in_format)) {
      if ($date_end_in_format == $date_start_in_format) {
        $vars['date'] = date_format_date($date_start, 'custom', 'd F', 'fr');
      }
      else {
        if ($start_month_number != $end_month_number) {
          $date_start_format .= ' F';
        }
        if ($start_year_number != $end_year_number) {
          $date_end_format .= ' Y';
        }

        $vars['date'] = date_format_date($date_start, 'custom', $date_start_format, 'fr') .
            " " .
            date_format_date($date_end, 'custom', $date_end_format, 'fr');
      }
    }

    // Localisation.
    if ($cities = _culturebox_get_taxonomy_event_geolocalization($vars['term'], FALSE)) {
      $vars['localizations'] = $cities;
    }

    $vars['category'] = '';
    $categories = field_get_items('taxonomy_term', $vars['term'], 'field_categories');
    if ($categories) {
      $tids_to_load = array();
      foreach ($categories as $category) {
        $tids_to_load[] = $category['tid'];
      }

      if (!empty($tids_to_load)) {
        $terms = taxonomy_term_load_multiple($tids_to_load);
        $vars['categories'] = array();
        foreach ($categories as $category) {
          if (isset($terms[$category['tid']])) {
            $vars['categories'][] = culturebox_site_l($terms[$category['tid']]->name, "taxonomy/term/{$category['tid']}");
          }
        }

        $vars['categories'] = implode(' ', $vars['categories']);
      }
    }
  }

  $vars['persons'] = array();
  $items = field_get_items('taxonomy_term', $vars['term'], 'field_personalities');
  if (!empty($items)) {
    foreach ($items as $item) {
      if (!empty($item['tid']) && !empty($item['taxonomy_term'])) {
        $vars['persons'][] = culturebox_site_l($item['taxonomy_term']->name, 'taxonomy/term/' . $item['taxonomy_term']->tid);
      }
    }
  }

  // Main image.
  $image_size = field_get_items('taxonomy_term', $vars['term'], 'field_image_size');
  if (!$image_size) {
    $image_size = 'landscape';
  }
  else {
    $image_size = $image_size[0]['value'];
  }

  if (!empty($vars['content']['field_illustration']['#items'][0]['target_id'])) {
    $asset_id = $vars['content']['field_illustration']['#items'][0]['target_id'];
    if ($image_size == 'landscape') {
      if (!empty($vars['content']['field_illustration'][0]['asset'][$asset_id]['field_asset_image'][0])) {
        $vars['content']['field_illustration'][0]['asset'][$asset_id]['field_asset_image'][0]['#image_style'] = 'taxonomy_event_view_full_main_image_landscape';
      }
    }
    else {
      if (!empty($vars['content']['field_illustration'][0]['asset'][$asset_id]['field_asset_image'][0])) {
        $vars['content']['field_illustration'][0]['asset'][$asset_id]['field_asset_image'][0]['#image_style'] = 'taxonomy_event_view_full_main_image';
      }
    }
  }

  if (!empty($vars['term']->field_illustration[LANGUAGE_NONE][0]['entity']->field_asset_image[LANGUAGE_NONE][0]['uri'])) {
    $vars['microdata_image'] = '<meta itemprop="image" content="' . file_create_url($vars['term']->field_illustration[LANGUAGE_NONE][0]['entity']->field_asset_image[LANGUAGE_NONE][0]['uri']) . '" />';
  }

  // Site links.
  $fields = array('field_website', 'field_secondary_website', 'field_facebook', 'field_twitter');
  $vars['links'] = array();
  foreach ($fields as $field) {
    if (!empty($vars['content'][$field])) {
      $link = trim(drupal_render($vars['content'][$field]));
      if (!empty($link)) {
        $vars['links'][] = $link;
      }
    }
  }
  $vars['links'] = implode('<br />', $vars['links']);
}

/**
 * Override or insert variables into the taxonomy term template for festival vocabulary full view mode.
 */
function culturebox_preprocess_taxonomy_term__event_full_festival(&$vars, $hook) {
  $is_emission_related = FALSE;

  $piliers = field_get_items('taxonomy_term', $vars['term'], 'field_event_access');

  if (!empty($piliers)) {
    foreach ($piliers as $pilier) {
      if ($pilier['value'] == 'emission') {
        $is_emission_related = TRUE;
        break;
      }
    }
  }

  if (!$is_emission_related) {
    menu_tree_set_path('main-menu', "live/festivals");
    // Date.
    $date_start = field_get_items('taxonomy_term', $vars['term'], 'field_date_start');
    if ($date_start && !empty($date_start[0]['value'])) {
      $date_start = new DateObject($date_start[0]['value'], $date_start[0]['timezone']);
      $start_month_number = date_format_date($date_start, 'custom', 'n', 'fr');
      $date_start_format = '\D\U d';
      $date_start_in_format = date_format_date($date_start, 'custom', 'd.m.Y', 'fr');
    }
    $date_end = field_get_items('taxonomy_term', $vars['term'], 'field_date_end');
    if ($date_end && !empty($date_end[0]['value'])) {
      $date_end = new DateObject($date_end[0]['value'], $date_end[0]['timezone']);
      $end_month_number = date_format_date($date_end, 'custom', 'n', 'fr');
      $date_end_format = '\A\U d F';
      $date_end_in_format = date_format_date($date_end, 'custom', 'd.m.Y', 'fr');
    }
    if (!empty($date_start_in_format) && !empty($date_end_in_format)) {
      if ($date_end_in_format == $date_start_in_format) {
        $vars['date'] = date_format_date($date_start, 'custom', 'd F Y', 'fr');
      }
      else {
        if ($start_month_number != $end_month_number) {
          $date_start_format .= ' F';
        }

        $date_end_format .= ' Y';

        $vars['date'] = date_format_date($date_start, 'custom', $date_start_format, 'fr') .
            " " .
            date_format_date($date_end, 'custom', $date_end_format, 'fr');
      }
    }

    // Localisation.
    if ($cities = _culturebox_get_taxonomy_term_festival_geolocalization($vars['term'], FALSE)) {
      $vars['localizations'] = $cities;
    }

    $vars['category'] = '';
    $categories = field_get_items('taxonomy_term', $vars['term'], 'field_categories');
    if ($categories) {
      $tids_to_load = array();
      foreach ($categories as $category) {
        $tids_to_load[] = $category['tid'];
      }

      if (!empty($tids_to_load)) {
        $terms = taxonomy_term_load_multiple($tids_to_load);
        $vars['categories'] = array();
        foreach ($categories as $category) {
          if (isset($terms[$category['tid']])) {
            if (!taxonomy_get_parents($terms[$category['tid']]->tid)) {
              $vars['categories'][] = culturebox_site_l($terms[$category['tid']]->name, "taxonomy/term/{$category['tid']}");
            }
          }
        }

        $vars['categories'] = implode(' ', $vars['categories']);
      }
    }
  }

  // Main image.
  $image_size = field_get_items('taxonomy_term', $vars['term'], 'field_image_size');
  if (!$image_size) {
    $image_size = 'landscape';
  }
  else {
    $image_size = $image_size[0]['value'];
  }

  if (!empty($vars['content']['field_illustration']['#items'][0]['target_id'])) {
    $asset_id = $vars['content']['field_illustration']['#items'][0]['target_id'];
    if ($image_size == 'landscape') {
      if (!empty($vars['content']['field_illustration'][0]['asset'][$asset_id]['field_asset_image'][0])) {
        $vars['content']['field_illustration'][0]['asset'][$asset_id]['field_asset_image'][0]['#image_style'] = 'taxonomy_event_view_full_main_image_landscape';
      }
    }
    else {
      if (!empty($vars['content']['field_illustration'][0]['asset'][$asset_id]['field_asset_image'][0])) {
        $vars['content']['field_illustration'][0]['asset'][$asset_id]['field_asset_image'][0]['#image_style'] = 'taxonomy_event_view_full_main_image';
      }
    }
  }

  // Site links.
  $fields = array('field_website', 'field_secondary_website', 'field_facebook', 'field_twitter');
  $vars['links'] = array();
  foreach ($fields as $field) {
    if (!empty($vars['content'][$field])) {
      $link = trim(drupal_render($vars['content'][$field]));
      if (!empty($link)) {
        $vars['links'][] = $link;
      }
    }
  }
  $vars['links'] = implode('<br />', $vars['links']);
  $image = theme('image', array('path' => CULTUREBOX_THEME_PATH . '/images/ico-01.gif'));
  $image .= ' Exporter les vidéos du festival';
  $vars['export_link'] = l($image, 'live/export', array(
    'html' => TRUE,
    'attributes' => array('class' => array('btn-01')),
    'query' => array(
      'source_type' => 'festival',
      'id' => $vars['term']->tid,
    ),
      )
  );
}

/**
 * Override or insert variables into the taxonomy term template for event vocabulary full view mode.
 */
function culturebox_preprocess_taxonomy_term__event_festivals_list(&$vars, $hook) {
  $is_emission_related = FALSE;

  $piliers = field_get_items('taxonomy_term', $vars['term'], 'field_event_access');

  if (!empty($piliers)) {
    foreach ($piliers as $pilier) {
      if ($pilier['value'] == 'emission') {
        $is_emission_related = TRUE;
        break;
      }
    }
  }

  if (!$is_emission_related) {
    // Date.
    $date_start = field_get_items('taxonomy_term', $vars['term'], 'field_date_start');
    if ($date_start && !empty($date_start[0]['value'])) {
      $date_start = new DateObject($date_start[0]['value'], $date_start[0]['timezone']);
      $start_month_number = date_format_date($date_start, 'custom', 'n', 'fr');
      $start_year_number = date_format_date($date_start, 'custom', 'Y', 'fr');
      $date_start_format = '\D\U d';
      $date_start_in_format = date_format_date($date_start, 'custom', 'd.m.Y', 'fr');
    }
    $date_end = field_get_items('taxonomy_term', $vars['term'], 'field_date_end');
    if ($date_end && !empty($date_end[0]['value'])) {
      $date_end = new DateObject($date_end[0]['value'], $date_end[0]['timezone']);
      $end_month_number = date_format_date($date_end, 'custom', 'n', 'fr');
      $end_year_number = date_format_date($date_end, 'custom', 'Y', 'fr');
      $date_end_format = '\A\U d F';
      $date_end_in_format = date_format_date($date_end, 'custom', 'd.m.Y', 'fr');
    }
    if (!empty($date_start_in_format) && !empty($date_end_in_format)) {
      if ($date_end_in_format == $date_start_in_format) {
        $vars['date'] = date_format_date($date_start, 'custom', 'd F Y', 'fr');
      }
      else {
        if ($start_month_number != $end_month_number) {
          $date_start_format .= ' F';
        }

        $date_end_format .= ' Y';

        $vars['date'] = date_format_date($date_start, 'custom', $date_start_format, 'fr') .
            " " .
            date_format_date($date_end, 'custom', $date_end_format, 'fr');
      }
    }

    // Localisation.
    if ($cities = _culturebox_get_taxonomy_event_geolocalization($vars['term'], FALSE)) {
      $vars['localizations'] = $cities;
    }

    $vars['category'] = '';
    $categories = field_get_items('taxonomy_term', $vars['term'], 'field_categories');
    if ($categories) {
      $tids_to_load = array();
      foreach ($categories as $category) {
        $tids_to_load[] = $category['tid'];
      }

      if (!empty($tids_to_load)) {
        $terms = taxonomy_term_load_multiple($tids_to_load);
        $vars['categories'] = array();
        foreach ($categories as $category) {
          if (isset($terms[$category['tid']]) && !taxonomy_get_parents($category['tid'])) {
            $vars['categories'][] = $terms[$category['tid']]->name;
          }
        }

        $vars['categories'] = implode(' ', $vars['categories']);
      }
    }

    $vars['type_title'] = 'événement';
  }
  else {
    $vars['type_title'] = 'les lives';
  }

  // Image section.
  if (!empty($vars['content']['field_illustration'])) {
    $media = render($vars['content']['field_illustration']);
    $vars['media'] = culturebox_site_l(
        $media, "taxonomy/term/{$vars['term']->tid}", array(
      'html' => TRUE,
        )
    );
  }

  $nid = 0;
  if ($node = menu_get_object('node')) {
    $nid = $node->nid;
  }

  $lives = views_get_view_result('lives_list', 'live_by_festival', $vars['term']->tid, $nid);
  $vars['show_arrows'] = (boolean) (count($lives) > 2);

  $vars['lives'] = views_embed_view('lives_list', 'live_by_festival', $vars['term']->tid, $nid);
}

/**
 * Override or insert variables into the taxonomy term template for mini-site vocabulary full view mode.
 */
function culturebox_preprocess_taxonomy_term__mini_site_festivals_list(&$vars, $hook) {
  $is_emission_related = FALSE;

  $piliers = field_get_items('taxonomy_term', $vars['term'], 'field_mini_site_section');

  if (!empty($piliers)) {
    foreach ($piliers as $pilier) {
      if ($pilier['value'] == 'emission') {
        $is_emission_related = TRUE;
        break;
      }
    }
  }

  if (!$is_emission_related) {
    // Date.
    $date_start = field_get_items('taxonomy_term', $vars['term'], 'field_mini_site_start_date');
    if ($date_start && !empty($date_start[0]['value'])) {
      $date_start = new DateObject($date_start[0]['value'], $date_start[0]['timezone']);
      $start_month_number = date_format_date($date_start, 'custom', 'n', 'fr');
      $date_start_format = '\D\U d';
      $date_start_in_format = date_format_date($date_start, 'custom', 'd.m.Y', 'fr');
    }
    $date_end = field_get_items('taxonomy_term', $vars['term'], 'field_mini_site_end_date');
    if ($date_end && !empty($date_end[0]['value'])) {
      $date_end = new DateObject($date_end[0]['value'], $date_end[0]['timezone']);
      $end_month_number = date_format_date($date_end, 'custom', 'n', 'fr');
      $end_year_number = date_format_date($date_end, 'custom', 'Y', 'fr');
      $date_end_format = '\A\U d F';
      $date_end_in_format = date_format_date($date_end, 'custom', 'd.m.Y', 'fr');
    }
    if (!empty($date_start_in_format) && !empty($date_end_in_format)) {
      if ($date_end_in_format == $date_start_in_format) {
        $vars['date'] = date_format_date($date_start, 'custom', 'd F Y', 'fr');
      }
      else {
        if ($start_month_number != $end_month_number) {
          $date_start_format .= ' F';
        }

        $date_end_format .= ' Y';

        $vars['date'] = date_format_date($date_start, 'custom', $date_start_format, 'fr') .
            " " .
            date_format_date($date_end, 'custom', $date_end_format, 'fr');
      }
    }

    // Localisation.
    if ($cities = _culturebox_get_taxonomy_event_geolocalization($vars['term'], FALSE)) {
      $vars['localizations'] = $cities;
    }

    $vars['category'] = '';
    $categories = field_get_items('taxonomy_term', $vars['term'], 'field_mini_site_category');
    if ($categories) {
      $tids_to_load = array();
      foreach ($categories as $category) {
        $tids_to_load[] = $category['tid'];
      }

      if (!empty($tids_to_load)) {
        $terms = taxonomy_term_load_multiple($tids_to_load);
        $vars['categories'] = array();
        foreach ($categories as $category) {
          if (isset($terms[$category['tid']]) && !taxonomy_get_parents($category['tid'])) {
            $vars['categories'][] = $terms[$category['tid']]->name;
          }
        }

        $vars['categories'] = implode(' ', $vars['categories']);
      }
    }

    $vars['type_title'] = 'événement';
  }
  else {
    $vars['type_title'] = 'les lives';
  }

  // Image section.
  if (!empty($vars['content']['field_mini_site_main_image'])) {
    $media = render($vars['content']['field_mini_site_main_image']);
    $vars['media'] = culturebox_site_l(
        $media, "taxonomy/term/{$vars['term']->tid}", array(
      'html' => TRUE,
        )
    );
  }

  $nid = 0;
  if ($node = menu_get_object('node')) {
    $nid = $node->nid;
  }

  $lives = views_get_view_result('lives_list', 'live_by_minisite', $vars['term']->tid, $nid);
  $vars['show_arrows'] = (boolean) (count($lives) > 2);

  $vars['lives'] = views_embed_view('lives_list', 'live_by_minisite', $vars['term']->tid, $nid);
}

/**
 * Override or insert variables into the taxonomy term template.
 */
function culturebox_preprocess_taxonomy_term__mini_site_minisites_list(&$vars, $hook) {
  $nid = 0;
  if ($node = menu_get_object('node')) {
    $nid = $node->nid;
  }

  // Get referenced nodes list.
  $view = views_get_view('lives_list');
  if ($view && $view->access('minisite_lives')) {
    $vars['nodes'] = $view->preview('minisite_lives', array($vars['term']->tid, $nid));
    if (count($view->result) > 2) {
      $vars['show_arrows'] = TRUE;
    }
  }
  if (!empty($view->result)) {
    // Date.
    $date_start = field_get_items('taxonomy_term', $vars['term'], 'field_mini_site_start_date');
    if ($date_start && !empty($date_start[0]['value'])) {
      $date_start = new DateObject($date_start[0]['value'], $date_start[0]['timezone']);
      $start_month_number = date_format_date($date_start, 'custom', 'n', 'fr');
      $start_year_number = date_format_date($date_start, 'custom', 'Y', 'fr');
      $date_start_format = '\D\U d';
      $date_start_in_format = date_format_date($date_start, 'custom', 'd.m.Y', 'fr');
    }
    $date_end = field_get_items('taxonomy_term', $vars['term'], 'field_mini_site_end_date');
    if ($date_end && !empty($date_end[0]['value'])) {
      $date_end = new DateObject($date_end[0]['value'], $date_end[0]['timezone']);
      $end_month_number = date_format_date($date_end, 'custom', 'n', 'fr');
      $end_year_number = date_format_date($date_end, 'custom', 'Y', 'fr');
      $date_end_format = '\A\U d F';
      $date_end_in_format = date_format_date($date_end, 'custom', 'd.m.Y', 'fr');
    }
    if (!empty($date_start_in_format) && !empty($date_end_in_format)) {
      if ($date_end_in_format == $date_start_in_format) {
        $vars['date'] = date_format_date($date_start, 'custom', 'd F Y', 'fr');
      }
      else {
        if ($start_month_number != $end_month_number) {
          $date_start_format .= ' F';
        }

        $date_end_format .= ' Y';

        $vars['date'] = date_format_date($date_start, 'custom', $date_start_format, 'fr') .
            " " .
            date_format_date($date_end, 'custom', $date_end_format, 'fr');
      }
    }

    // Localisation.
    if ($cities = _culturebox_get_taxonomy_event_geolocalization($vars['term'], FALSE)) {
      $vars['localizations'] = $cities;
    }

    $vars['category'] = '';
    $categories = field_get_items('taxonomy_term', $vars['term'], 'field_mini_site_category');
    if ($categories) {
      $tids_to_load = array();
      foreach ($categories as $category) {
        $tids_to_load[] = $category['tid'];
      }

      if (!empty($tids_to_load)) {
        $terms = taxonomy_term_load_multiple($tids_to_load);
        $vars['categories'] = array();
        foreach ($categories as $category) {
          if (isset($terms[$category['tid']]) && !taxonomy_get_parents($category['tid'])) {
            $vars['categories'][] = $terms[$category['tid']]->name;
          }
        }

        $vars['categories'] = implode(' ', $vars['categories']);
      }
    }

    // Image section.
    if (!empty($vars['content']['field_mini_site_main_image'])) {
      $media = render($vars['content']['field_mini_site_main_image']);
      $vars['media'] = culturebox_site_l(
          $media, "taxonomy/term/{$vars['term']->tid}", array(
        'html' => TRUE,
          )
      );
    }
    $vars['term_link'] = culturebox_site_l(
        'Toutes les vidéos', "taxonomy/term/{$vars['term']->tid}/lives", array(
      'attributes' => array('class' => array('btn-01')),
        )
    );
  }
}

/**
 * Override or insert variables into the taxonomy term template for personality vocabulary full view mode.
 */
function culturebox_preprocess_taxonomy_term__personality_full(&$vars, $hook) {
  if (!empty($vars['term']->field_picture[LANGUAGE_NONE][0]['entity']->field_asset_image[LANGUAGE_NONE][0]['uri'])) {
    $vars['microdata_image'] = '<meta itemprop="image" content="' . file_create_url($vars['term']->field_picture[LANGUAGE_NONE][0]['entity']->field_asset_image[LANGUAGE_NONE][0]['uri']) . '" />';
  }

  $category = field_get_items('taxonomy_term', $vars['term'], 'field_categories');
  if ($category) {
    if (empty($category[0]['taxonomy_term'])) {
      $category[0]['taxonomy_term'] = taxonomy_term_load($category[0]['tid']);
    }
    $vars['category'] = culturebox_site_l($category[0]['taxonomy_term']->name, "taxonomy/term/{$category[0]['taxonomy_term']->tid}");
  }

  // Link to twitter page.
  if (!empty($vars['content']['field_emission_twitter'][0]['#markup'])) {
    $user_name = $vars['term']->field_emission_twitter[LANGUAGE_NONE][0]['value'];
    $vars['content']['field_emission_twitter'][0]['#markup'] = culturebox_site_l("@$user_name", 'https://twitter.com/' . $user_name, array('external' => TRUE, 'attributes' => array('class' => 'twitter-link')));
  }

  // Link to instagram page.
  if (!empty($vars['content']['field_emission_instagram'][0]['#markup'])) {
    // Get instagram user name.
    if (function_exists('culturebox_instagram_get_username_from_user_id')) {
      $user_name = culturebox_instagram_get_username_from_user_id($vars['content']['field_emission_instagram'][0]['#markup']);

      if ($user_name) {
        $vars['content']['field_emission_instagram'][0]['#markup'] = culturebox_site_l('Instagram', 'http://instagram.com/' . $user_name, array('external' => TRUE, 'attributes' => array('class' => 'instagram-link')));
      }
    }
  }

  if (!empty($vars['content']['field_facebook'][0]['#element']['url'])) {
    $vars['content']['field_facebook'][0]['#element']['title'] = 'Facebook';
  }
}

/**
 * Override or insert variables into the taxonomy term template for personality vocabulary search view mode.
 */
function culturebox_preprocess_taxonomy_term__personality_search(&$vars, $hook) {
  $category = field_get_items('taxonomy_term', $vars['term'], 'field_category');
  if ($category) {
    if (empty($category[0]['taxonomy_term'])) {
      $category[0]['taxonomy_term'] = taxonomy_term_load($category[0]['tid']);
    }
    $vars['category'] = culturebox_site_l($category[0]['taxonomy_term']->name, "taxonomy/term/{$category[0]['taxonomy_term']->tid}");
  }
  if (!empty($vars['description'])) {
    $alter = array(
      'strip_tags' => FALSE,
      'text_length' => 200,
      'word_boundary' => TRUE,
      'ellipsis' => TRUE,
    );
    $vars['description'] = better_trimmed_trim_text($alter, $vars['description']);
  }

  $vars['name'] = culturebox_site_l(
      $vars['term']->name, 'taxonomy/term/' . $vars['term']->tid
  );

  if (!empty($vars['content']['field_picture'])) {
    $vars['image'] = culturebox_site_l(
        drupal_render($vars['content']['field_picture']), 'taxonomy/term/' . $vars['term']->tid, array(
      'html' => TRUE,
        )
    );
  }
}

/**
 * Override theme_form_element to prevent adding "form-item" class.
 */
function culturebox_form_element($variables) {
  $element = & $variables['element'];

  if (!empty($_GET['email_popup_form'])) {
    $class = !empty($element['#type']) && $element['#type'] == 'textarea' ? 'area' : 'text-form';
    return theme('form_element_label', $variables)
        . '<div class="form-item ' . $class . '">' . $element['#children'] . "</div>\n";
  }

  // This is also used in the installer, pre-database setup.
  $t = get_t();

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }
  // Add element's #type and #name as class to aid with JS/CSS selectors.
  $attributes['class'] = array('');
  if (!empty($element['#type'])) {
    $attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
  }
  if (!empty($element['#name'])) {
    $attributes['class'][] = 'form-item-' . strtr(
            $element['#name'], array(' ' => '-', '_' => '-', '[' => '-', ']' => '')
    );
  }
  // Add a class for disabled elements to facilitate cross-browser styling.
  if (!empty($element['#attributes']['disabled'])) {
    $attributes['class'][] = 'form-disabled';
  }
  if ($element['#type'] == 'radio' && !empty($element['#parents'][0]) && $element['#parents'][0] == 'sort_by') {
    $output = '<li' . drupal_attributes($attributes) . '>' . "\n";
  }
  else {
    $output = '<div' . drupal_attributes($attributes) . '>' . "\n";
  }

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }
  $prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . $element['#field_prefix'] . '</span> ' : '';
  $suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . $element['#field_suffix'] . '</span>' : '';

  switch ($element['#title_display']) {
    case 'before':
    case 'invisible':
      $output .= ' ' . theme('form_element_label', $variables);
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;

    case 'after':
      $output .= ' ' . $prefix . $element['#children'] . $suffix;
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  if (!empty($element['#description'])) {
    $output .= '<div class="description">' . $element['#description'] . "</div>\n";
  }

  if ($element['#type'] == 'radio' && !empty($element['#parents'][0]) && $element['#parents'][0] == 'sort_by') {
    $output .= "</li>\n";
  }
  else {
    $output .= "</div>\n";
  }

  return $output;
}

/**
 * Process variables for views-view.tpl.php.
 *
 * @see views-view.tpl.php
 */
function culturebox_preprocess_views_view(&$vars) {
  if (isset($vars['theme_hook_suggestion'])) {
    $function = 'culturebox_preprocess_' . $vars['theme_hook_suggestion'];
    if (function_exists($function)) {
      $function($vars);
    }
  }
}

/**
 * Process variables for views-view-unformatted.tpl.php.
 *
 * @see views-view-unformatted.tpl.php
 */
function culturebox_preprocess_views_view_unformatted(&$vars) {
  if (isset($vars['theme_hook_suggestion'])) {
    $function = 'culturebox_preprocess_' . $vars['theme_hook_suggestion'];
    if (function_exists($function)) {
      $function($vars);
    }
  }
}

/**
 * Process variables for views-view--lives-list--live-home-derniers.tpl.php.
 *
 * @see templates/views/views-view--lives-list--live-home-derniers.tpl.php
 */
function culturebox_preprocess_views_view__lives_list__live_home_derniers(&$vars) {
  $vars['tous_les_lives'] = culturebox_site_l(
      'Tous les lives', 'live/tous-les-lives', array(
    'attributes' => array(
      'class' => array(
        'btn-01',
      ),
    ),
      )
  );
}

/**
 * Process variables for views-view--live-search--panel-pane-live-full.tpl.php.
 *
 * @see templates/views/live_search/live_full_search/views-view--live-search--panel-pane-live-full.tpl.php
 */
function culturebox_preprocess_views_view__live_search__panel_pane_live_full(&$vars) {
  // Prepare search info string.
  $keywords = !empty($vars['view']->filter['search_api_views_fulltext']->value) ? '“' . $vars['view']->filter['search_api_views_fulltext']->value . '” ' : '';
  $results = intval($vars['view']->total_rows);
  $vars['results_info'] = format_plural(
      $results, '@keywords!span_start(@count résultat)!span_end', '@keywords!span_start(@count résultats)!span_end', array('@count' => $results, '@keywords' => $keywords, '!span_start' => '<span>', '!span_end' => '</span>')
  );
}

/**
 * Default preprocess for views-view-fields.
 *
 * Call preprocesses for all suggestions.
 */
function culturebox_preprocess_views_view_fields(&$vars) {
  if (isset($vars['theme_hook_suggestion'])) {
    $function = 'culturebox_preprocess_' . $vars['theme_hook_suggestion'];
    if (function_exists($function)) {
      $function($vars);
    }
  }
}

function culturebox_preprocess_views_view_fields__site_search__panel_pane_site_search(&$vars) {
  $id = $vars['id'] - 1;
  if (isset($vars['fields']['nodes_index:nid']) && !empty($vars['fields']['nodes_index:nid']->raw)) {
    $node = $vars['view']->result[$id]->entity;
    $vars['entity'] = node_view($node, 'search');
  }
  elseif (isset($vars['fields']['terms_index:tid']) && !empty($vars['fields']['terms_index:tid']->raw)) {
    $term = $vars['view']->result[$id]->entity;
    $vars['entity'] = taxonomy_term_view($term, 'playlist');
  }
}

/**
 * Preprocess variables for views-view--article-list--read-also.tpl.php.
 *
 * @see views-view--article-list--read-also.tpl.php
 */
function culturebox_preprocess_views_view__article_list__read_also(&$vars) {
  $term = menu_get_object('taxonomy_term', 2);
  if ($term) {
    if (!(!empty($vars['view']->args[1]) && $vars['view']->args[1] == 'all')) {
      $parents = taxonomy_get_parents_all($term->tid);
      if ($parents) {
        $term = end($parents);
      }
    }

    $vars['thematic_title'] = $term->name;
  }
}

function culturebox_preprocess_views_view__site_search__panel_pane_site_search(&$vars) {
  $culturebox_site_search = &drupal_static('culturebox_site_search', array());
  $page_num = 0;
  if (!empty($GLOBALS['pager_page_array'])) {
    $page_num = !empty($GLOBALS['pager_page_array'][0]) ? $GLOBALS['pager_page_array'][0] + 1 : 1;
  }
  $culturebox_site_search = array('arg' => check_plain($vars['view']->filter['search_api_multi_fulltext']->value), 'page_num' => $page_num);

  if (!empty($vars['view']->filter['search_api_multi_fulltext']->value)) {
    $vars['keyword'] = '“ ' . check_plain($vars['view']->filter['search_api_multi_fulltext']->value) . ' ”';
  }
  $vars['results_info'] = format_plural(
      $vars['view']->total_rows, 'Résultats de votre recherche (@count)', 'Résultats de votre recherche (@count)', array('@count' => $vars['view']->total_rows)
  );

  $vars['related_pages'] = '';

  if (!empty($vars['view']->result)) {
    $term_suggestions = $term_occurences = array(
      'geolocalization' => array(),
      'thematic' => array(),
      'people' => array(),
    );

    $term_field_names = array(
      'geolocalization' => array(
        'field_geolocalization',
      ),
      'thematic' => array(
        //'field_events',
        'field_categories',
        //'field_minisites',
      ),
      'people' => array(
        'field_people',
        'field_personnalit_s',
        'field_linvite',
      )
    );

    foreach ($vars['view']->result as $result) {
      if (!empty($result->entity) && $result->search_api_multi_index == 'nodes_index' && is_object($result->entity)) {
        foreach ($term_field_names as $category => $category_field_names) {
          foreach ($category_field_names as $category_field_name) {
            if (!empty($result->entity->{$category_field_name})) {
              $items = field_get_items('node', $result->entity, $category_field_name);

              foreach ($items as $item) {
                $tid = FALSE;

                if (!empty($item['tid'])) {
                  $tid = $item['tid'];
                }
                elseif (!empty($item['target_id'])) {
                  $tid = $item['target_id'];
                }

                if ($tid) {
                  if (array_key_exists($tid, $term_occurences[$category])) {
                    $term_occurences[$category][$tid] ++;
                  }
                  else {
                    $term_occurences[$category][$tid] = 1;
                  }
                }
              }
            }
          }
        }
      }
    }

    // On exclue les termes de "geolocalization" avec moins de 5 contenus associés.
    if (!empty($term_occurences['geolocalization'])) {
      $geo_term_tids = array_keys($term_occurences['geolocalization']);

      // Recognize city term level.
      $tids_by_depth = culturebox_site_get_tids_sorted_by_depth($geo_term_tids);

      if (!empty($tids_by_depth['depth_2'])) {
        $city_terms = $tids_by_depth['depth_2'];
        $city_terms = taxonomy_term_load_multiple($city_terms);
        foreach ($city_terms as $tid => $term) {
          $valid_related_articles_count = culturebox_site_show_city_page_if_nodes_count($term);

          // Exclude term from common output.
          if (!$valid_related_articles_count) {
            unset($term_occurences['geolocalization'][$tid]);
          }
        }
      }
    }

    // On exclue les termes de "personality" avec moins de 5 contenus associés.
    if (!empty($term_occurences['people'])) {
      $ppl_term_tids = array_keys($term_occurences['people']);

      $ppl_terms = taxonomy_term_load_multiple($ppl_term_tids);
      foreach ($ppl_terms as $tid => $term) {
        $valid_related_articles_count = culturebox_site_show_personnality_page_if_nodes_count($term);

        // Exclude term from common output.
        if (!$valid_related_articles_count) {
          unset($term_occurences['people'][$tid]);
        }
      }
    }
    // Sort association terms by frequency of occurrence.
    foreach (array('geolocalization', 'thematic', 'people') as $association) {
      if (!empty($term_occurences[$association])) {
        arsort($term_occurences[$association]);
        $term_occurences[$association] = array_slice($term_occurences[$association], 0, 3, TRUE);

        $tids_to_show = array_keys($term_occurences[$association]);
        $terms_to_show = taxonomy_term_load_multiple($tids_to_show);
        $i = 0;
        foreach ($terms_to_show as $key => $term_to_show) {
          $classes = array('imitation-links', $association);

          if ($i != 0) {
            $classes[] = 'additional';
          }
          $i++;

          $term_suggestions[$association][] = culturebox_site_l(truncate_utf8($term_to_show->name, 30, TRUE, TRUE), "taxonomy/term/{$term_to_show->tid}", array('attributes' => array('class' => $classes)));
        }
      }
    }

    if (!empty($term_suggestions)) {
      $vars['related_pages'] = $term_suggestions;
    }
  }
  else {
    // Pour éviter d'avoir une page blanche, on met du faux contenu.
    $vars['view']->result = ' ';

    // On regarde si la recherche nous propose une suggestion
    // Voir : culturebox_search_search_api_solr_multi_search_results_alter().
    if (!empty($GLOBALS['culturebox_search_suggestion']->suggestion)) {
      $vars['rows'] = '<p>Nous n\'avons trouvé aucun résultat pour votre recherche.<br /><br /></p>'
          . l('<p class="search-result-suggest">Essayez avec cette orthographe : “ <span>' . $GLOBALS['culturebox_search_suggestion']->suggestion . '</span> ”.</p>', 'recherche/' . $GLOBALS['culturebox_search_suggestion']->suggestion, array('html' => TRUE))
          . '<br /><br />';
    }
  }
}

/**
 * Format a date popup element.
 *
 * Use a class that will float date and time next to each other.
 */
function culturebox_date_popup($vars) {
  $element = $vars['element'];
  $attributes = !empty($element['#wrapper_attributes']) ? $element['#wrapper_attributes'] : array('class' => array());
  $attributes['class'][] = 'container-inline-date';
  // If there is no description, the floating date elements need some extra padding below them.
  $wrapper_attributes = array('class' => array('date-padding'));
  if (empty($element['date']['#description'])) {
    $wrapper_attributes['class'][] = 'clearfix';
  }
  // Add an wrapper to mimic the way a single value field works, for ease in using #states.
  if (isset($element['#children'])) {
    $element['#children'] = $element['#children'];
  }
  return theme('form_element', $element);
}

/**
 * Preprocess variables for view template.
 */
function culturebox_preprocess_views_view_unformatted__lives_list__live_home_evenements(&$vars) {
  $vars['actu'] = (drupal_is_front_page() || arg(0) == 'live') ? TRUE : NULL;

  if ($vars['actu']) {
    $vars['live_nids'] = $nids = array();

    foreach ($vars['view']->result as $key => $item) {
      if (!empty($item->nid)) {
        $nids[] = $item->nid;
      }
    }

    if (!empty($nids)) {
      $nodes = node_load_multiple($nids);
      foreach ($nodes as $node) {
        if ($node->type == 'live') {
          $vars['live_nids'][] = $node->nid;
        }
      }
    }
  }
}

/**
 * Preprocess variables for view template.
 */
function culturebox_preprocess_views_view_unformatted__lives_list__live_home_thematic_blocks(&$vars) {
  if (!empty($vars['view']->args)) {
    $term = taxonomy_term_load(array_shift($vars['view']->args));
    $vars['thematic_name'] = culturebox_site_l($term->name, "taxonomy/term/{$term->tid}");
  }
}

/**
 * Process variables for views-view-unformatted--article-list--home-thematic-block.tpl.php.
 *
 * @see views-view-unformatted--article-list--home-thematic-block.tpl.php
 */
function culturebox_preprocess_views_view_unformatted__article_list__home_thematic_block(&$vars) {
  if (!empty($vars['view']->args[0])) {
    $tid = $vars['view']->args[0];
    $term = taxonomy_term_load($tid);
    $vars['category_name'] = check_plain($term->name);
    $titiere = field_get_items('taxonomy_term', $term, 'field_tetiere');
    if ($titiere) {
      $vars['titiere'] = $titiere[0]['safe_value'];
    }
    $vars['category_link'] = culturebox_site_l(
        'Toute l\'actu', "taxonomy/term/$tid", array(
      'attributes' => array(
        'class' => array(
          'btn-01',
        ),
      ),
        )
    );
  }
}

/**
 * Process variables for views-view-unformatted--article-list--home-thematic-block-simple.tpl.php.
 *
 * @see views-view-unformatted--article-list--home-thematic-block-simple.tpl.php
 */
function culturebox_preprocess_views_view_unformatted__article_list__home_thematic_block_simple(&$vars) {
  if (!empty($vars['view']->args[0])) {
    $tid = $vars['view']->args[0];
    $term = taxonomy_term_load($tid);
    $vars['category_name'] = check_plain($term->name);
    $titiere = field_get_items('taxonomy_term', $term, 'field_tetiere');
    if ($titiere) {
      $vars['titiere'] = $titiere[0]['safe_value'];
    }
    $vars['category_link'] = culturebox_site_l(
        'Toute l\'actu', "taxonomy/term/$tid", array(
      'attributes' => array(
        'class' => array(
          'btn-01',
        ),
      ),
        )
    );
  }
}

/**
 * Process variables for views-view-unformatted--taxonomy-list--events.tpl.php.
 *
 * @see views-view-unformatted--taxonomy-list--events.tpl.php
 */
function culturebox_preprocess_views_view_unformatted__taxonomy_list__events(&$vars) {
  // Enable slider only when more then one slides.
  $vars['slider'] = (!empty($vars['rows']) && (count(array_filter($vars['rows'])) > 1));
}

/**
 * Process variables for facebook_pull-feed.tpl.php.
 *
 * @see facebook_pull-feed.tpl.php
 */
function culturebox_preprocess_facebook_pull_feed(&$vars) {
  if (!empty($vars['items'])) {
    foreach ($vars['items'] as &$item) {
      if (!empty($item->message_tags)) {
        $tags = array();
        foreach ($item->message_tags as $key => $value) {
          $tags[$key]['id'] = $value[0]->id;
          $tags[$key]['name'] = $value[0]->name;
          $tags[$key]['offset'] = $value[0]->offset;
          $tags[$key]['length'] = $value[0]->length;
        }
        krsort($tags);
        foreach ($tags as $tag) {
          $first = drupal_substr($item->message, 0, $tag['offset']);
          $second = drupal_substr($item->message, $tag['offset'] + $tag['length']);
          $link = culturebox_site_l($tag['name'], "http://facebook.com/{$tag['id']}");
          $item->message = "{$first}{$link}{$second}";
        }
      }
      if (function_exists('_filter_url') && function_exists('_filter_url_settings')) {
        $filter = new stdClass();
        $filter->settings = array('filter_url_length' => 72);
        ;
        $item->message = _filter_url($item->message, $filter);
      }
    }
  }
}

/**
 * Override theme_panels_default_style_render_region.
 */
function culturebox_panels_default_style_render_region($vars) {
  if (variable_get('culturebox_debug_panels_panes', FALSE)) {
    $output = '';
    foreach ($vars['panes'] as $pid => $pane_output) {
      $pane = $vars['display']->content[$pid];
      $output .= $pane_output;
      $output .= '<!-- Region:' . $vars['region_id'] . '-Pane:' . ($pane->type == $pane->subtype ? $pane->type : $pane->type . ':' . $pane->subtype) . ' -->';
    }
    return $output . '<!-- Region:' . $vars['region_id'] . ' -->';
  }
  else {
    // Wipe panel separator.
    return implode('', $vars['panes']);
  }
}

/**
 * Implements hook_css_alter().
 */
function culturebox_css_alter(&$css) {
  $current_path = current_path();

  // For some reason exclude via info file doesn't work.
  $exclude = array(
    'misc/ui/jquery.ui.datepicker.css',
    'misc/ui/jquery.ui.theme.css',
    'misc/ui/jquery.ui.slider.css',
    'modules/system/system.menus.css',
    'modules/user/user.css',
    'modules/node/node.css',
    'modules/field/theme/field.css',
    'modules/taxonomy/taxonomy.css',
    'sites/all/modules/contrib/hide_submit/hide_submit.css',
    'sites/all/modules/contrib/asset/css/assets.css',
    'sites/all/modules/contrib/panels/css/panels.css',
    'sites/all/modules/contrib/ctools/css/ctools.css',
    'sites/all/modules/contrib/ckeditor/ckeditor.css',
    'sites/all/modules/contrib/date/date_popup/themes/datepicker.1.7.css',
    'sites/all/modules/contrib/date/date_api/date.css',
    'sites/all/modules/custom/ftv_evolutive_cache_control/css/ftv_evolutive_cache_control.css',
    'sites/all/modules/contrib/views/css/views.css',
    'sites/all/modules/custom/culturebox_site/css/map.css',
    'sites/all/modules/contrib/panels/plugins/layouts/onecol/onecol.css',
    'sites/all/modules/contrib/field_group/field_group.css',
  );

  if (!drupal_match_path($current_path, 'resultats/widgets/external.html') && !drupal_match_path($current_path, 'live/export')) {
    $exclude[] = 'sites/all/themes/culturebox/css/external_widget.css';
  }
  elseif (drupal_match_path($current_path, 'resultats/widgets/external.html')) {
    $exclude[] = 'modules/system/system.messages.css';
    $exclude[] = 'sites/all/themes/culturebox/css/jquery.fancybox-1.3.4.css';
    $exclude[] = 'sites/all/modules/contrib/ckeditor/css/ckeditor.css';
    $exclude[] = 'sites/all/modules/custom/culturebox_social/css/culturebox_social.css';
    $exclude[] = 'modules/system/system.admin.css';
    $exclude[] = 'modules/system/system.theme.css';
    $exclude[] = 'sites/all/themes/culturebox/css/form.css';
    $exclude[] = 'sites/all/themes/culturebox/css/ui-popup.css';
    $exclude[] = 'sites/all/themes/culturebox/css/jquery.jscrollpane.css';
  }

  foreach ($exclude as $path) {
    if (isset($css[$path])) {
      unset($css[$path]);
    }
  }

  // On regroupe le plus possible les fichiers CSS.
  foreach ($css as $key => $path) {
    if (strpos($key, 'modules/system') !== FALSE) {
      $css[$key]['weight'] = CSS_SYSTEM;
    }
    elseif (!empty($path['type']) && $path['type'] == 'external') {
      $js[$key]['weight'] = CSS_THEME;
    }
    else {
      $js[$key]['weight'] = CSS_DEFAULT;
    }

    if (!empty($path['every_page']) && $path['every_page'] === TRUE) {
      $css[$key]['group'] = CSS_DEFAULT;
    }
    else {
      $css[$key]['group'] = CSS_THEME;
    }
  }
}



/**
 * Implements hook_css_alter().
 */
function culturebox_js_alter(&$js) {
  $current_path = current_path();

  if (culturebox_site_is_ajax()) {
    if ($current_path == 'culturebox-vote/get-vote-form') {
      $allow = array(
        'sites/all/modules/contrib/jquery_update/replace/jquery/1.5/jquery.min.js',
        'misc/jquery.once.js',
        'misc/form.js',
        'misc/ajax.js',
        'misc/autocomplete.js',
        'misc/collapse.js',
        'misc/ui/jquery.ui.core.min.js',
        'misc/ui/jquery.ui.widget.min.js',
        'misc/ui/jquery.ui.button.min.js',
        'misc/ui/jquery.ui.mouse.min.js',
        'misc/ui/jquery.ui.draggable.min.js',
        'misc/ui/jquery.ui.position.min.js',
        'misc/ui/jquery.ui.resizable.min.js',
        'misc/ui/jquery.ui.dialog.min.js',
        'sites/all/modules/contrib/jquery_update/replace/misc/jquery.form.min.js',
        'sites/all/modules/contrib/references_dialog/js/references-dialog.js',
      );

      foreach ($js as $path => $item) {
        if (!in_array($path, $allow)) {
          unset($js[$path]);
        }
      }
    }

    $exclude = array();
    $exclude[] = 'sites/all/themes/culturebox/js/placeholder.min.js';
    $exclude[] = 'misc/collapse.js';
    $exclude[] = 'misc/ui/jquery.ui.core.min.js';
    $exclude[] = 'misc/ui/jquery.ui.widget.min.js';
    $exclude[] = 'misc/ui/jquery.ui.button.min.js';
    $exclude[] = 'misc/ui/jquery.ui.mouse.min.js';
    $exclude[] = 'misc/ui/jquery.ui.draggable.min.js';
    $exclude[] = 'misc/ui/jquery.ui.position.min.js';
    $exclude[] = 'misc/ui/jquery.ui.resizable.min.js';
    $exclude[] = 'misc/ui/jquery.ui.dialog.min.js';
    $exclude[] = 'sites/all/modules/contrib/views/js/jquery.ui.dialog.patch.js';

    foreach ($exclude as $path) {
      if (isset($js[$path])) {
        unset($js[$path]);
      }
    }
  }
  else {
    $exclude = array(
      'misc/progress.js',
      'sites/all/modules/contrib/hide_submit/hide_submit.js',
      'sites/all/modules/contrib/field_group/field_group.js',
      'sites/all/modules/contrib/panels/js/panels.js',
    );

    if ($current_path == 'resultats/widgets/external.html') {
      $exclude[] = 'sites/all/themes/culturebox/js/lazyload.min.js';
      $exclude[] = 'sites/all/modules/contrib/panels/js/panels.js';
      $exclude[] = 'sites/all/themes/culturebox/js/image-map.min.js';
      $exclude[] = 'sites/all/themes/culturebox/js/jquery.countdown.min.js';
      $exclude[] = 'sites/all/themes/culturebox/js/jquery.tooltip.min.js';
      $exclude[] = 'sites/all/themes/culturebox/js/culturebox_email_popup.js';
      $exclude[] = 'sites/all/themes/culturebox/js/jquery.sameheight.min.js';
      $exclude[] = 'sites/all/themes/culturebox/js/scripts-diapograma.js';
      $exclude[] = 'sites/all/themes/culturebox/js/jquery.stickyBar.min.js';
      $exclude[] = 'sites/all/modules/contrib/views_load_more/views_load_more.js';
      $exclude[] = 'sites/all/themes/culturebox/js/jquery.slideto.v1.1.min.js';
      $exclude[] = 'sites/all/themes/culturebox/js/culturebox_submain_menu.js';
      $exclude[] = 'sites/all/modules/custom/culturebox_social/js/culturebox_social.js';
      $exclude[] = 'sites/all/themes/culturebox/js/fancybox.min.js';
      $exclude[] = 'sites/all/themes/culturebox/js/jquery.form.min.js';
      $exclude[] = 'sites/all/themes/culturebox/js/jquery.jscrollpane.min.js';
      $exclude[] = 'sites/all/themes/culturebox/js/jquery.mousewheel.min.js';
      $exclude[] = 'sites/all/modules/contrib/jquery_update/replace/misc/jquery.form.min.js';
      $exclude[] = 'sites/all/modules/contrib/views/js/base.js';
      $exclude[] = 'sites/all/modules/contrib/views/js/ajax_view.js';
    }
    else {
      $node = menu_get_object();
      $term = menu_get_object('taxonomy_term', 2);

      if ($current_path != 'live' && !(!empty($node->type) && in_array($node->type, array('live', 'playlist'))) && !(!empty($term->vocabulary_machine_name) && in_array($term->vocabulary_machine_name, array(CULTUREBOX_EMISSION_EMISSION_VOCABULARY_NAME, 'hub_minisites_evenements')))) {
        $exclude[] = 'sites/all/themes/culturebox/js/jquery.countdown.min.js';
      }

      if ($current_path != CULTUREBOX_VOTE_CUSTOM_URL_BO && $current_path != 'culturebox-vote/get-vote-form' && strpos($current_path, 'culturebox-vote-model') === FALSE) {
        $exclude[] = 'misc/collapse.js';
        $exclude[] = 'misc/form.js';
      }
      else {
        $exclude[] = 'sites/all/themes/culturebox/js/placeholder.min.js';
      }

      if (culturebox_live_is_live_section() || culturebox_emission_is_emission_section()) {
        $exclude[] = 'sites/all/themes/culturebox/js/jquery.openclose.min.js';
      }

      if (empty($node->type) || (!empty($node->type) && $node->type != 'article')) {
        $exclude[] = 'sites/all/themes/culturebox/js/jquery.stickyBar.min.js';
        $exclude[] = 'sites/all/themes/culturebox/js/jquery.slideto.v1.1.min.js';
      }
    }

    foreach ($exclude as $path) {
      if (isset($js[$path])) {
        unset($js[$path]);
      }
    }

    // Fichiers JS à déplacer dans le footer.
    $js_footer = array(
      'sites/all/themes/culturebox/js/scripts.js',
      'sites/all/themes/culturebox/js/scripts-diapograma.js',
      'sites/all/modules/contrib/views_load_more/views_load_more.js',
      'sites/all/modules/contrib/views/js/base.js',
      'sites/all/modules/contrib/views/js/ajax_view.js',
      'modules/contextual/contextual.js',
      'sites/all/modules/custom/culturebox_site/plugins/cache/geolocation/geolocation.js',
      'misc/autocomplete.js',
      'sites/all/modules/contrib/google_analytics/googleanalytics.js',
      'sites/all/modules/contrib/disqus/disqus.js',
      'misc/textarea.js',
      'sites/all/modules/contrib/references_dialog/js/references-dialog.js',
      'sites/all/modules/custom/culturebox_vote/js/culturebox_vote_admin.js',
      'sites/all/modules/custom/culturebox_social/js/culturebox_social.js',
    );

    foreach ($js_footer as $path) {
      if (isset($js[$path])) {
        $js[$path]['scope'] = 'footer';
      }
    }

    // On regroupe le plus possible les fichiers JS.
    foreach ($js as $key => $path) {
      if ($key == 'sites/all/themes/culturebox/js/jquery-1.8.3.min.js' || $key == 'sites/all/modules/contrib/jquery_update/replace/jquery/1.5/jquery.min.js') {
        $js[$key]['weight'] = -500;
      }
      elseif ($key == 'misc/jquery.once.js') {
        $js[$key]['weight'] = -450;
      }
      elseif ($key == 'misc/drupal.js') {
        $js[$key]['weight'] = -400;
      }
      elseif ($key == 'misc/ajax.js') {
        $js[$key]['weight'] = -350;
      }
      elseif ($key == 'misc/ui/jquery.ui.core.min.js') {
        $js[$key]['weight'] = -345;
      }
      elseif ($key == 'misc/ui/jquery.ui.widget.min.js') {
        $js[$key]['weight'] = -340;
      }
      elseif ($key == 'misc/ui/jquery.ui.button.min.js') {
        $js[$key]['weight'] = -335;
      }
      elseif ($key == 'misc/ui/jquery.ui.mouse.min.js') {
        $js[$key]['weight'] = -330;
      }
      elseif ($key == 'misc/ui/jquery.ui.draggable.min.js') {
        $js[$key]['weight'] = -325;
      }
      elseif ($key == 'misc/ui/jquery.ui.position.min.js') {
        $js[$key]['weight'] = -320;
      }
      elseif ($key == 'misc/ui/jquery.ui.resizable.min.js') {
        $js[$key]['weight'] = -315;
      }
      elseif ($key == 'misc/ui/jquery.ui.dialog.min.js') {
        $js[$key]['weight'] = -310;
      }
      elseif ($key == 'misc/autocomplete.js') {
        $js[$key]['weight'] = -305;
      }
      elseif ($key == 'sites/all/modules/contrib/references_dialog/js/references-dialog.js') {
        $js[$key]['weight'] = -300;
      }
      elseif (!empty($path['type']) && $path['type'] == 'inline') {
        $js[$key]['weight'] = JS_LIBRARY;
      }
      elseif (!empty($path['type']) && $path['type'] == 'external') {
        $js[$key]['weight'] = JS_THEME;
      }
      else {
        $js[$key]['weight'] = JS_DEFAULT;
      }

      if (!empty($path['every_page']) && $path['every_page'] === TRUE) {
        $js[$key]['group'] = JS_DEFAULT;
      }
      else {
        $js[$key]['group'] = JS_THEME;
      }
    }
  }
}

/**
 * Preprocess variables for content-page-layout.tpl.php.
 *
 * @see content-page-layout.tpl.php
 */
function culturebox_preprocess_content_page_layout(&$vars) {
  if (!empty($vars['display']->context)) {
    $node = reset($vars['display']->context);
    if (!empty($node->data) && is_array($node->type) && $node->type[0] == 'entity:node') {
      $node = $node->data;
      switch ($node->type) {
        case 'page':
          $vars['classes_array'][] = 'legal';
          break;
      }
    }
  }
}

/**
 * Override default formatter theming for seo optimization.
 */
function culturebox_link_formatter_link_default($vars) {
  $link_options = $vars['element'];
  unset($link_options['element']['title']);
  unset($link_options['element']['url']);

  // Issue #1199806 by ss81: Fixes fatal error when the link URl is equal to page URL.
  if (isset($link_options['attributes']['class'])) {
    $link_options['attributes']['class'] = array($link_options['attributes']['class']);
  }

  // Display a normal link if both title and URL are available.
  if (!empty($vars['element']['title']) && !empty($vars['element']['url'])) {
    return culturebox_site_l($vars['element']['title'], $vars['element']['url'], $link_options);
  } // If only a title, display the title.
  elseif (!empty($vars['element']['title'])) {
    return check_plain($vars['element']['title']);
  }
  elseif (!empty($vars['element']['url'])) {
    return culturebox_site_l($vars['element']['title'], $vars['element']['url'], $link_options);
  }
}

/**
 * Override default formatter theming for seo optimization.
 */
function culturebox_link_formatter_link_url($vars) {
  $link_options = $vars['element'];
  unset($link_options['element']['title']);
  unset($link_options['element']['url']);
  return $vars['element']['url'] ? culturebox_site_l($vars['element']['display_url'], $vars['element']['url'], $link_options) : '';
}

/**
 * Override default formatter theming for seo optimization.
 */
function culturebox_link_formatter_link_short($vars) {
  $link_options = $vars['element'];
  unset($link_options['element']['title']);
  unset($link_options['element']['url']);
  return $vars['element']['url'] ? culturebox_site_l(t('Link'), $vars['element']['url'], $link_options) : '';
}

/**
 * Override default formatter theming for seo optimization.
 */
function culturebox_link_formatter_link_label($vars) {
  $link_options = $vars['element'];
  unset($link_options['element']['title']);
  unset($link_options['element']['url']);
  return $vars['element']['url'] ? culturebox_site_l($vars['field']['label'], $vars['element']['url'], $link_options) : '';
}

/**
 * Override default formatter theming for seo optimization.
 */
function culturebox_link_formatter_link_separate($vars) {
  $class = empty($vars['element']['attributes']['class']) ? '' : ' ' . $vars['element']['attributes']['class'];
  unset($vars['element']['attributes']['class']);
  $link_options = $vars['element'];
  unset($link_options['element']['title']);
  unset($link_options['element']['url']);
  $title = empty($vars['element']['title']) ? '' : check_plain($vars['element']['title']);

  // @TODO static html markup looks not very elegant to me (who takes it off?).
  // Needs smarter output solution and an optional title/url seperator (digidog).
  $output = '';
  $output .= '<div class="link-item ' . $class . '">';
  if (!empty($title)) {
    $output .= '<div class="link-title">' . $title . '</div>';
  }
  $output .= '<div class="link-url">' . culturebox_site_l($vars['element']['url'], $vars['element']['url'], $link_options) . '</div>';
  $output .= '</div>';
  return $output;
}

/**
 * Overrides default menu theming for seo.
 */
function culturebox_menu_link(array $variables) {
  $element = $variables['element'];
  $sub_menu = '';

  if ($element['#below']) {
    $sub_menu = drupal_render($element['#below']);
  }
  $output = culturebox_site_l($element['#title'], $element['#href'], $element['#localized_options']);
  return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
}

/**
 * Overrides default menu theming for seo.
 */
function culturebox_menu_local_task($variables) {
  $link = $variables['element']['#link'];
  $link_text = $link['title'];

  if (!empty($variables['element']['#active'])) {
    // If the link does not contain HTML already, check_plain() it now.
    // After we set 'html'=TRUE the link will not be sanitized by culturebox_site_l().
    if (empty($link['localized_options']['html'])) {
      $link['title'] = check_plain($link['title']);
    }
    $link['localized_options']['html'] = TRUE;
    $link_text = t('!local-task-title', array('!local-task-title' => $link['title']));
  }

  $class = !empty($variables['element']['#active']) ? 'active' : '';
  $class .= (arg(2) == 'revisions' && arg(4) == 'view') ? !empty($class) ? ' revision-page' : 'revision-page' : '';

  $class = !empty($class) ? 'class="' . $class . '"' : '';

  return "<li {$class}>" . culturebox_site_l($link_text, $link['href'], $link['localized_options']) . "</li>\n";
}

/**
 * Overrides default menu theming for seo.
 */
function culturebox_menu_local_action($variables) {
  $link = $variables['element']['#link'];

  $output = '<li>';
  if (isset($link['href'])) {
    $output .= culturebox_site_l($link['title'], $link['href'], isset($link['localized_options']) ? $link['localized_options'] : array());
  }
  elseif (!empty($link['localized_options']['html'])) {
    $output .= $link['title'];
  }
  else {
    $output .= check_plain($link['title']);
  }
  $output .= "</li>\n";

  return $output;
}

/**
 * Process variables for panels-everywhere-page.tpl.php.
 */
function culturebox_preprocess_panels_everywhere_page(&$vars) {
  // FTVEN channels header.
  // Resided here, to fix asset preview in media library.
  if (!culturebox_site_is_ajax() && current_path() != 'resultats/widgets/external.html') {
    $env = variable_get('ws_env_switcher', variable_get('ftven_env', ''));
    switch ($env) {
      case 'prod':
        $url = 'http://static.francetv.fr/js/jquery.metanav-min.js';
        break;
      default:
        $url = 'http://static.ftv-preprod.fr/js/jquery.metanav-min.js';
        break;
    }
    // on ne construit pas la metanav avec le js sur la home, on le fait en dur pour qu'il soit référencé.
    if (!drupal_is_front_page()) {
      drupal_add_js($url, array('type' => 'external', 'scope' => 'footer', 'weight' => 1000));
    }
    drupal_add_js('http://www.francetvinfo.fr/skin/1.1.357/www/js/externe/header.js', array('type' => 'external', 'scope' => 'footer'));
    drupal_add_js('var headerFTVInfo = {headerPresent : 1};', array('type' => 'inline'));
    drupal_add_js("jQuery('html').addClass('html-no-cnil');", array('type' => 'inline'));
    drupal_add_js('var AudienceScience = true;', array('type' => 'inline', 'scope' => 'header'));
  }

  // Add minisites specific CSS.
  if (culturebox_minisite_is_minisite_page() || culturebox_minisite_get_hub_term()) {
    drupal_add_css(CULTUREBOX_THEME_PATH . '/css/mini-style.css', array('group' => CSS_THEME, 'weight' => 100));
  }

  // Add emissions specific CSS.
  if (culturebox_emission_is_emission_section() || culturebox_emission_get_term()) {
    drupal_add_css(CULTUREBOX_THEME_PATH . '/css/emission-style.css', array('group' => CSS_THEME, 'weight' => 100));
  }
}

/**
 * Process variables for html page.
 */
function culturebox_preprocess_html(&$vars) {
  if (!culturebox_emission_is_emission_section()) {
    $vars['classes_array'][] = 'site-habillage';
  }

  $env = variable_get('ftven_env', '');
  $server_marketing = ($env == 'prod') ? 'francetv' : 'ftv-preprod';
  $vars['ftven_formabo'] = array(
    'css' => array(
      '<link type="text/css" rel="stylesheet" href="http://newsletters.' . $server_marketing . '.fr/ftven_formabo/css/style-min.css" media="all"/>'
    ),
    'js' => array(
      '<script src="http://newsletters.' . $server_marketing . '.fr/ftven_formabo/js/jquery.easyModal.min.js" defer></script>',
      '<script src="http://newsletters.' . $server_marketing . '.fr/ftven_formabo/js/ftven_formabo-min.js" defer></script>'
    )
  );

  $vars['player_widget'] = FALSE;
  $current_path = current_path();
  $node = menu_get_object();

  // Balise archimade.
  $vars['archimade_token_content'] = 'culturebox';

  if (drupal_is_front_page()) {
    $vars['archimade_token_content'] = 'culturebox_accueil';
  }
  elseif ($current_path == 'live') {
    $vars['archimade_token_content'] = 'culturebox_live_accueil';
  }
  elseif (!empty($node->type) && $node->type == 'live') {
    $vars['archimade_token_content'] = 'culturebox_live';
  }

  // On ajoute les balises <link> rel="next" rel="prev".
  if (!empty($GLOBALS['pager_page_array'])) {
    $query_parameters_orig = array();
    $url_current = request_uri();
    $url_current_parsed = parse_url($url_current);

    if (!empty($url_current_parsed['query'])) {
      $query_parameters_orig = explode('&', $url_current_parsed['query']);
    }

    if ($GLOBALS['pager_page_array'][0] > 0) {
      $query_parameters = $query_parameters_orig;
      $key = array_search('page=' . $GLOBALS['pager_page_array'][0], $query_parameters);

      if ($key !== FALSE) {
        if ($GLOBALS['pager_page_array'][0] == 1) {
          unset($query_parameters[$key]);
        }
        else {
          $query_parameters[$key] = 'page=' . ($GLOBALS['pager_page_array'][0] - 1);
        }
      }
      else {
        $query_parameters[] = 'page=' . ($GLOBALS['pager_page_array'][0] - 1);
      }

      $url_prev = $GLOBALS['base_url'] . $url_current_parsed['path'];

      if (!empty($query_parameters)) {
        $url_prev .= '?' . implode('&', $query_parameters);
      }

      $element = array(
        '#tag' => 'link',
        '#attributes' => array(
          'rel' => 'prev',
          'href' => $url_prev
        ),
      );

      drupal_add_html_head($element, 'link-rel-prev');
    }

    if ($GLOBALS['pager_page_array'][0] < ($GLOBALS['pager_total'][0] - 1)) {
      $query_parameters = $query_parameters_orig;
      $key = array_search('page=' . $GLOBALS['pager_page_array'][0], $query_parameters);

      if ($key !== FALSE) {
        $query_parameters[$key] = 'page=' . ($GLOBALS['pager_page_array'][0] + 1);
      }
      else {
        $query_parameters[] = 'page=' . ($GLOBALS['pager_page_array'][0] + 1);
      }

      $url_next = $GLOBALS['base_url'] . $url_current_parsed['path'];

      if (!empty($query_parameters)) {
        $url_next .= '?' . implode('&', $query_parameters);
      }

      $element = array(
        '#tag' => 'link',
        '#attributes' => array(
          'rel' => 'next',
          'href' => $url_next
        ),
      );
      drupal_add_html_head($element, 'link-rel-next');
    }
  }

  if (drupal_is_front_page()) {
    // On ajoute la metanav dans le html juste sous le body dans le cas ou on est sur la home.
    $vars['metanav'] = theme('metanav_home', array());

    $element = array(
      '#tag' => 'meta',
      '#attributes' => array(
        'content' => 'fJwPWA_m-bcbRdomBMI3i2Zhxi2MfgVv9oqyc03gg3E',
        'name' => 'google-site-verification',
      ),
    );
    drupal_add_html_head($element, 'google-site-verification');

    $element = array(
      '#tag' => 'link',
      '#attributes' => array(
        'href' => 'https://plus.google.com/107368086649921030514',
        'rel' => 'publisher',
      ),
    );
    drupal_add_html_head($element, 'google-plus-publisher');

    $element = array(
      '#tag' => 'meta',
      '#attributes' => array(
        'http-equiv' => 'refresh',
        'content' => '900',
      ),
    );
    drupal_add_html_head($element, 'refresh');
  }
  else {
    if ($node && $node->status == NODE_NOT_PUBLISHED) {
      // On ajoute une classe sur la balise <body> pour savoir si un noeud n'est pas publié.
      $vars['classes_array'][] = 'page-node-not-published';
    }
    // If article or page.
    if ($node && in_array($node->type, array('article', 'page')) && arg(1) && $node->nid == arg(1)) {
      if(!empty($node->field_type[LANGUAGE_NONE][0]['value']) && $node->field_type[LANGUAGE_NONE][0]['value'] == 'article_minute_par_minute') {
        $element = array(
          '#tag' => 'meta',
          '#attributes' => array(
            'http-equiv' => 'refresh',
            'content' => '120',
          ),
        );
      } else {
        $element = array(
          '#tag' => 'meta',
          '#attributes' => array(
            'http-equiv' => 'refresh',
            'content' => '1799',
          ),
        );
      }
      drupal_add_html_head($element, 'refresh');
    }
    else {
      // If not live player page.
      global $theme;
      if ((!$node || ($node && !in_array($node->type, array('live', 'fiche_emission', 'playlist')))) && $theme != 'culturebox_admin' && arg(0) != 'ajax') {
        $taxonomy_term = menu_get_object('taxonomy_term', 2);

        if (!(!empty($taxonomy_term->vocabulary_machine_name) && $taxonomy_term->vocabulary_machine_name == CULTUREBOX_MINISITE_HUB_VOCABULARY_NAME)) {
          $element = array(
            '#tag' => 'meta',
            '#attributes' => array(
              'http-equiv' => 'refresh',
              'content' => '1799',
            ),
          );
          drupal_add_html_head($element, 'refresh');
        }

        if (!empty($taxonomy_term->vocabulary_machine_name) && $taxonomy_term->vocabulary_machine_name == 'mini_site') {
          // Si on est sur un mini-site avec un affichage de la HP en mode "V2".
          if (!empty($taxonomy_term->field_selection_variant_panels[LANGUAGE_NONE][0]['value']) && $taxonomy_term->field_selection_variant_panels[LANGUAGE_NONE][0]['value'] == 'term_view_panel_context_25') {
            $vars['classes_array'][] = 'minisite-page-v2';
          }
        }
      }
    }

    if ($node && $node->type == 'article') {
      $vars['classes_array'][] = 'articles';
      if (!empty($node->field_article_main_media[LANGUAGE_NONE][0]['target_id'])) {
        $asset = asset_load($node->field_article_main_media[LANGUAGE_NONE][0]['target_id']);
        if ($asset) {
          $field_asset_premium = field_get_items('asset', $asset, 'field_asset_premium');
          if ($asset->type == 'diaporama' && !empty($field_asset_premium[0]['value']) && $field_asset_premium[0]['value']) {
            $vars['classes_array'][] = 'article-diaporama';
          }
        }
      }
    }

    if (drupal_match_path($current_path, 'references-dialog/search/votes_list/livres_suggeres_dialog/*') || drupal_match_path($current_path, 'related_video/*')) {
      $vars['player_widget'] = TRUE;
    }

    // Assign body css class.
    if (drupal_match_path($current_path, 'resultats/widgets/external.html')) {
      if (!empty($_GET['width']) && $_GET['width'] >= 900) {
        $vars['classes_array'][] = 'w900';
      }

      $vars['player_widget'] = TRUE;
    }

    if (culturebox_emission_is_emission_section()) {
      $vars['classes_array'][] = 'live-page';
      $vars['classes_array'][] = 'emission-page';
    }

    if (culturebox_emission_get_term() && $node && $node->type == 'page') {
      $vars['classes_array'][] = 'live-page';
      $vars['classes_array'][] = 'emission-page';
      $vars['classes_array'][] = 'node-type-page-emission';
    }

    if (culturebox_live_is_live_section()) {
      $vars['classes_array'][] = 'live-page';

      if (arg(0) == 'node' && is_numeric(arg(1))) {
        $vars['classes_array'][] = 'live-player';
      }
    }

    if ($term = _culturebox_emission_get_term_from_listing_page()) {
      $vars['classes_array'][] = 'page-emissions-custom-listing';
    }

    if (culturebox_minisite_get_hub_term()) {
      $vars['classes_array'][] = 'hub-page';
      $vars['classes_array'][] = 'live-page';
    }

    if (!empty($node->type) && in_array($node->type, array('live'))) {
      $vars['head_title'] = _culturebox_site_improve_titles_seo($node, $node->title, TRUE);
    }

    if (!empty($node->type) && $node->type == 'article' && !empty($node->field_pilier_associe[LANGUAGE_NONE][0]['value']) && $node->field_pilier_associe[LANGUAGE_NONE][0]['value'] == 'live') {
      // On rajoute une classe spéciale pour les articles live.
      $vars['classes_array'][] = 'node-type-article-live';
    }
  }
}

/**
 * Preprocess variables of feed_item.
 */
function _culturebox_preprocess_node__feed_item(&$vars) {
 $node = $vars['node'];

    // Process image.
  $image = '';
  if (!empty($vars['content']['field_image'])) {
    $image = drupal_render($vars['content']['field_image']);
  }

  $path = field_get_items('node', $node, 'field_url');

  if (!empty($image)) {
    $vars['feed_item_image'] = culturebox_site_l(
        $image, $path[0]['display_url'], array(
      'attributes' => array('title' => $node->title),
      'html' => TRUE,
        )
    );
  }

  // Process title.
  $vars['title_link'] = culturebox_site_l(
      $node->title, $path[0]['display_url'], array(
    'attributes' => array('title' => $node->title),
      )
  );

  // Process categories.
  $field_category = field_get_items('node', $node, 'field_categories');
  if ($field_category) {
    $thematic_id = end($field_category);
    $thematic = taxonomy_term_load($thematic_id['tid']);
    if ($thematic) {
      $vars['category'] = culturebox_site_l($thematic->name, "taxonomy/term/{$thematic->tid}");
    }
  }
}

/**
 * Preprocess variables of feed_item teaser.
 */
function culturebox_preprocess_node__feed_item_teaser(&$vars) {
  _culturebox_preprocess_node__feed_item($vars);
}

/**
 * Preprocess variables of live teaser.
 */
function culturebox_preprocess_node__live_teaser(&$vars) {
  $node = $vars['node'];
  if ($thematic = _culturebox_get_node_live_main_category($node)) {
    $vars['category'] = $thematic;
  }

  _culturebox_preprocess_live_button_play($vars, NULL, 'play-medium');
  $media_view = _culturebox_get_first_live_media_asset_view($vars);

  if (!empty($media_view)) {
    $vars['image'] = culturebox_site_l(
        drupal_render($media_view), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }

  if ($start_time = field_get_items('node', $node, 'field_live_start_time')) {
    $vars['year'] = format_date(strtotime($start_time[0]['value']), 'custom', 'Y');
  }
  else {
    $vars['year'] = format_date(time(), 'custom', 'Y');
  }
  _culturebox_preprocess_live_channel($vars);
}

/**
 * Preprocess variables of live search.
 */
function culturebox_preprocess_node__live_search(&$vars) {
  $node = $vars['node'];
  if ($thematic = _culturebox_get_node_live_main_category($node)) {
    $vars['category'] = $thematic;
  }

  _culturebox_preprocess_live_button_play($vars, NULL, 'play-medium');
  $media_view = _culturebox_get_first_live_media_asset_view($vars);

  if (!empty($media_view)) {
    $vars['image'] = culturebox_site_l(
        drupal_render($media_view), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }

  _culturebox_preprocess_live_channel($vars);
}

/**
 * Preprocess variables of live related video.
 */
function culturebox_preprocess_node__live_related_video(&$vars) {
  $node = $vars['node'];
  if ($thematic = _culturebox_get_node_live_main_category($node)) {
    $vars['category'] = $thematic;
  }

  _culturebox_preprocess_live_button_play($vars, NULL, 'play-small');
  $media_view = _culturebox_get_first_live_media_asset_view($vars);

  if (!empty($media_view)) {
    $vars['image'] = culturebox_site_l(
        drupal_render($media_view), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }
}

/**
 * Preprocess variables of live widget related video.
 */
function culturebox_preprocess_node__live_widget_related_video(&$vars) {
  $node = $vars['node'];
  if ($thematic = _culturebox_get_node_live_main_category($node, FALSE, TRUE)) {
    $vars['category'] = $thematic;
  }

  _culturebox_preprocess_live_button_play($vars, NULL, 'play-small widget-player-link');
  $media_view = _culturebox_get_first_live_media_asset_view($vars);

  if (!empty($media_view)) {
    $vars['image'] = culturebox_site_l(
        drupal_render($media_view), 'node/' . $vars['nid'], array(
      'html' => TRUE,
      'attributes' => array('class' => array('widget-player-link')),
        )
    );
  }

  global $language;
  if ($language->language == 'en') {
    // Titre anglais.
    $english_title = field_get_items('node', $node, 'field_timeline_title');

    if (!empty($english_title[0]['value'])) {
      $vars['node']->title = $english_title[0]['value'];
    }
  }
}

/**
 * Preprocess variables of live widget.
 */
function _culturebox_preprocess_node__live_widget(&$vars) {
  global $language;

  $node = $vars['node'];

  $title = field_get_items('node', $vars['node'], 'field_title_long');
  if ($title) {
    $vars['title'] = $title[0]['safe_value'];
  }

  // Live status.
  $status = field_get_items('node', $vars['node'], 'field_live_status');
  $status = $status[0]['value'];
  drupal_add_js(array('CultureboxLive' => array('statusLive' => $status)), 'setting');
  $vars['label_class'] = '';
  $vars['live_status'] = '';
  $vars['status'] = $status;
  $vars['message'] = '';

  switch ($status) {
    case CULTUREBOX_LIVE_STATUS_LIVE_RETARD:
      $vars['message'] = '';
      $message = field_get_items('node', $node, 'field_live_delay_message');
      if ($message) {
        $vars['message'] = $message[0]['value'];
      }
      $vars['live_status'] = t('Coming soon');
      break;

    case CULTUREBOX_LIVE_STATUS_LIVE_PROCHAINEMENT:
    case CULTUREBOX_LIVE_STATUS_LIVE_AVANT_LE_DIRECT:
      $message = field_get_items('node', $node, 'field_live_before_live_message');
      if ($message) {
        $vars['message'] = $message[0]['value'];
      }
      $vars['live_status'] = t('Coming soon');
      break;
      
    case CULTUREBOX_LIVE_STATUS_LIVE_ENTRE_LE_DIRECT_ET_LE_REPLAY:
      $message = field_get_items('node', $node, 'field_live_bwn_dt_and_replay_msg');
      if ($message) {
        $vars['message'] = $message[0]['value'];
      }
      break;
      
    case CULTUREBOX_LIVE_STATUS_LIVE_APRES_LE_REPLAY:
      $message = field_get_items('node', $node, 'field_live_after_replay_msg');
      if ($message) {
        $vars['message'] = $message[0]['value'];
      }
      break;

    case CULTUREBOX_LIVE_STATUS_LIVE_LAST_CHANCE:
      $vars['live_status'] = t('Last chance');
      break;

    case CULTUREBOX_LIVE_STATUS_LIVE_DIRECT:
      $vars['live_status'] = t('Live');
      break;

    case CULTUREBOX_LIVE_STATUS_LIVE_REPLAY:
      $vars['live_status'] = t('Replay');
      $vars['label_class'] = 'arevoir';
      break;
  }

  // Player integration.
  if (!empty($status) && in_array($status, array(CULTUREBOX_LIVE_STATUS_LIVE_DIRECT, CULTUREBOX_LIVE_STATUS_LIVE_REPLAY, CULTUREBOX_LIVE_STATUS_LIVE_LAST_CHANCE))) {
    $video_url = _culturebox_live_get_video_url($node);

    // Player link.
    if ($video_url) {
      $player_script_vars = array();
      $vars['player_link'] = '<a href="' . $video_url . '" class="player"></a>';

      if (!empty($node->field_second_title[LANGUAGE_NONE][0]['value'])) {
        $vars['ftv_player_double_widget'] = TRUE;

        $vars['player_link'] .= '<a href="' . 'http://videos.francetv.fr/video/' . $node->field_second_title[LANGUAGE_NONE][0]['value'] . '@' . variable_get('cb_pgep_catalog_name', 'Culture') . '" class="player"></a>';

        if ($vars['view_mode'] == 'widget_large') {
          $player_script_vars['player_size_half'] = TRUE;
        }

        // Si on affiche deux players, on désactive la pub sur les players.
        $player_script_vars['default_showad'] = FALSE;
      }

      $mini_site = field_get_items('node', $node, 'field_mini_site');
      if (!empty($mini_site[0]['tid']) && $mini_site[0]['tid'] == '56657') {
        $player_script_vars['default_showad'] = FALSE;
      }

      // Couche d'enrichissement Adways.
      $player_script_vars['adways'] = !empty($node->field_adways[LANGUAGE_NONE][0]['value']) ? $node->field_adways[LANGUAGE_NONE][0]['value'] : FALSE;

      $vars['player_script'] = theme('culturebox_site_player_ftv_script', $player_script_vars);

      // Request to get flash player URL.
      $flash_player_url = _culturebox_emission_get_flash_player_url();

      if (!empty($flash_player_url)) {
        drupal_add_js($flash_player_url, array('weight' => 10));
      }
    }
  }

  // Image or trailer.
  $status_for_show_trailer = array(
    CULTUREBOX_LIVE_STATUS_LIVE_PROCHAINEMENT,
    CULTUREBOX_LIVE_STATUS_LIVE_AVANT_LE_DIRECT,
    CULTUREBOX_LIVE_STATUS_LIVE_RETARD,
    CULTUREBOX_LIVE_STATUS_LIVE_ENTRE_LE_DIRECT_ET_LE_REPLAY,
  );

  $status_for_show_image = array(
    CULTUREBOX_LIVE_STATUS_LIVE_PROCHAINEMENT,
    CULTUREBOX_LIVE_STATUS_LIVE_AVANT_LE_DIRECT,
    CULTUREBOX_LIVE_STATUS_LIVE_RETARD,
    CULTUREBOX_LIVE_STATUS_LIVE_ENTRE_LE_DIRECT_ET_LE_REPLAY,
    CULTUREBOX_LIVE_STATUS_LIVE_APRES_LE_REPLAY,
  );

  // Get trailer if available.
  if ($trailer = field_get_items('node', $node, 'field_live_video_trailer') && in_array($status, $status_for_show_trailer)) {
    $vars['trailer'] = render($vars['content']['field_live_video_trailer']);
    $vars['trailer'] = trim($vars['trailer']);
  }

  // We not need image if we have status CULTUREBOX_LIVE_STATUS_LIVE_PROCHAINEMENT and we have trailer.
  if (in_array($status, $status_for_show_image)) {
    if ($main_image = field_get_items('node', $node, 'field_live_media')) {
      // Quand on passe size=auto, on utilise le view_mode "widget_large" pour avoir la taille d'image la plus grande possible.
      if (!empty($_GET['size']) && $_GET['size'] == 'auto') {
        if (!empty($vars['content']['field_live_media'][0]['asset'])) {
          foreach ($vars['content']['field_live_media'][0]['asset'] as $asset_image) {
            $asset_image['#view_mode'] = 'widget_large';
            $asset_image['field_asset_image']['#view_mode'] = 'widget_large';
            $asset_image['field_asset_image'][0]['#image_style'] = 'widget-large';
          }
        }
      }

      $vars['image'] = render($vars['content']['field_live_media'][0]);

      // Quand on passe size=auto, on adapte automatiquement la taille de l'image.
      if (!empty($_GET['size']) && $_GET['size'] == 'auto') {
        $width = check_plain($_GET['width']);
        $height = check_plain($_GET['height']);

        $vars['image'] = preg_replace('~width="\d+"~', "width=\"{$width}\"", $vars['image']);
        $vars['image'] = preg_replace('~height="\d+"~', "height=\"{$height}\"", $vars['image']);
      }
    }
  }

  if ($language->language == 'en') {
    _culturebox_preprocess_node__live_widget_en($vars);
  }
}

/**
 * Traductions anglaises pour le titre et les messages d'attente des lives en mode widget.
 */
function _culturebox_preprocess_node__live_widget_en(&$vars) {
  // Titre anglais.
  $english_title = field_get_items('node', $vars['node'], 'field_timeline_title');

  if (!empty($english_title[0]['value'])) {
    $vars['node']->title = $english_title[0]['value'];
  }

  // Messages d'erreur standards.
  if (!empty($vars['status'])) {
    switch ($vars['status']) {
      case CULTUREBOX_LIVE_STATUS_LIVE_RETARD:
        $vars['message'] = 'The show id being delayed. Stay tuned!';
        break;

      case CULTUREBOX_LIVE_STATUS_LIVE_PROCHAINEMENT:
      case CULTUREBOX_LIVE_STATUS_LIVE_AVANT_LE_DIRECT:
        $vars['message'] = 'The show will begin shortly.';
        break;

      case CULTUREBOX_LIVE_STATUS_LIVE_ENTRE_LE_DIRECT_ET_LE_REPLAY:
        $vars['message'] = 'The video is not available yet.';
        break;

      case CULTUREBOX_LIVE_STATUS_LIVE_APRES_LE_REPLAY:
        $vars['message'] = 'The video is no longer available.';
        break;
    }
  }
}

/**
 * Preprocess variables of live widget small.
 */
function culturebox_preprocess_node__live_widget_small(&$vars) {
  _culturebox_preprocess_node__live_widget($vars);
}

/**
 * Preprocess variables of live widget medium.
 */
function culturebox_preprocess_node__live_widget_medium(&$vars) {
  _culturebox_preprocess_node__live_widget($vars);

  if (!empty($vars['node']->cb_festival_player)) {
    if (in_array($vars['node']->field_live_status[LANGUAGE_NONE][0]['value'], array('live', 'replay', 'last_chance'))) {
      $vars['label_sup'] = 'Vous regardez';
    }
  }
}

/**
 * Preprocess variables of live widget large.
 */
function culturebox_preprocess_node__live_widget_large(&$vars) {
  _culturebox_preprocess_node__live_widget($vars);
}

/**
 * Preprocess variables of live slider.
 */
function culturebox_preprocess_node__live_slider(&$vars) {
  $node = $vars['node'];
  if ($thematic = _culturebox_get_node_live_main_category($node)) {
    $vars['category'] = $thematic;
  }

  _culturebox_preprocess_live_button_play($vars, NULL, 'play-medium');
  $media_view = _culturebox_get_first_live_media_asset_view($vars);

  if (!empty($media_view)) {
    $vars['image'] = culturebox_site_l(
        drupal_render($media_view), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }

  _culturebox_preprocess_live_channel($vars);
}

/**
 * Preprocess variables of live evenement.
 */
function culturebox_preprocess_node__live_evenement(&$vars) {
  $node = $vars['node'];
  if ($thematic = _culturebox_get_node_live_main_category($node)) {
    $vars['category'] = $thematic;
  }

  _culturebox_preprocess_live_button_play($vars, NULL, 'play-small');
  $media_view = _culturebox_get_first_live_media_asset_view($vars);

  if (!empty($media_view)) {
    $vars['image'] = culturebox_site_l(
        drupal_render($media_view), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }
  _culturebox_preprocess_live_channel($vars);
}

/**
 * Preprocess variables of article evenement.
 */
function culturebox_preprocess_node__article_evenement(&$vars) {
  $vars['category'] = _culturebox_get_node_main_category($vars['node']);
  culturebox_preprocess_article_media_icon($vars);
  if ($article_media = _culturebox_get_article_media_asset_view($vars)) {
    $vars['image'] = culturebox_site_l(
        drupal_render($article_media), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }
}

/**
 * Preprocess variables of live most viewed.
 */
function culturebox_preprocess_node__live_most_viewed(&$vars) {
  $node = $vars['node'];

  if (isset($vars['view']->row_index)) {
    $vars['num'] = $vars['view']->row_index + 1;
  }

  if ($thematic = _culturebox_get_node_live_main_category($node)) {
    $vars['category'] = $thematic;
  }

  _culturebox_preprocess_live_button_play($vars, NULL, 'play-small');
  // On force une image de taille différente pour l'affichage des plus lus en homepage.
  $media_view = _culturebox_get_first_live_media_asset_view($vars);
  if (!empty($vars['view']) && $vars['view']->name == 'article_list' && $vars['view']->current_display == 'most_viewed') {
    $media_view = _culturebox_get_first_live_media_asset_view($vars, 'small_image', TRUE);
  }
  else {
    $media_view = _culturebox_get_first_live_media_asset_view($vars);
  }

  if (!empty($media_view)) {
    $vars['image'] = culturebox_site_l(
        drupal_render($media_view), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }
  _culturebox_preprocess_live_channel($vars);
}

/**
 * Preprocess variables of live most viewed.
 */
function culturebox_preprocess_node__live_most_viewed_home(&$vars) {
  $node = $vars['node'];

  if (isset($vars['view']->row_index)) {
    $vars['num'] = $vars['view']->row_index + 1;
  }

  if ($thematic = _culturebox_get_node_live_main_category($node)) {
    $vars['category'] = $thematic;
  }

  _culturebox_preprocess_live_button_play($vars, NULL, 'play-small');
  $media_view = _culturebox_get_first_live_media_asset_view($vars);

  if (!empty($media_view)) {
    $vars['image'] = culturebox_site_l(
        drupal_render($media_view), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }
}

/**
 * Preprocess variables.
 * @see sites/all/themes/culturebox/templates/node/live/node--live-topcategory-first.tpl.php
 */
function culturebox_preprocess_node__live_topcategory_first(&$vars) {
  $node = $vars['node'];
  $media_view = _culturebox_get_first_live_media_asset_view($vars);

  if (!empty($media_view)) {
    $vars['media'] = culturebox_site_l(
        drupal_render($media_view), "node/{$node->nid}", array(
      'html' => TRUE,
        )
    );

    $vars['thematic'] = _culturebox_get_node_live_main_category($node, TRUE);
    _culturebox_preprocess_live_button_play($vars);
  }
}

/**
 * Preprocess variables of live home derniers.
 */
function culturebox_preprocess_node__live_live_home_derniers(&$vars) {
  $node = $vars['node'];

  if ($thematic = _culturebox_get_node_live_main_category($node)) {
    $vars['category'] = $thematic;
  }

  _culturebox_preprocess_live_button_play($vars, NULL, 'play-small');

  if (!empty($vars['content']['field_live_media'])) {
    $vars['image'] = culturebox_site_l(
        drupal_render($vars['content']['field_live_media']), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }
  _culturebox_preprocess_live_channel($vars);
}

/**
 * Preprocess variables of live home most viewed sidebar.
 */
function culturebox_preprocess_node__live_most_viewed_sidebar(&$vars) {
  $node = $vars['node'];

  _culturebox_preprocess_live_button_play($vars, NULL, 'play-small');

  if (!empty($vars['content']['field_live_media'])) {
    $vars['image'] = culturebox_site_l(
      drupal_render($vars['content']['field_live_media']), 'node/' . $vars['nid'], array(
        'html' => TRUE,
      )
    );
  }
}

/**
 * Preprocess variables of feed_item blogs_list.
 */
function culturebox_preprocess_node__feed_item_blogs_list(&$vars) {
  $url = field_get_items('node', $vars['node'], 'field_url');
  if (!empty($url[0]['display_url'])) {
    $vars['title_link'] = culturebox_site_l(
        $vars['title'], $url[0]['display_url'], array('html' => TRUE, 'attributes' => array('target' => '_blank'))
    );

    $vars['content']['field_image'] = culturebox_site_l(
        render($vars['content']['field_image']), $url[0]['display_url'], array('html' => TRUE, 'attributes' => array('target' => '_blank'))
    );
  }
  else {
    $vars['title_link'] = $vars['title'];
    $vars['content']['field_image'] = render($vars['content']['field_image']);
  }
}

/**
 * Preprocess variables of feed_item teaser.
 */
function culturebox_preprocess_node__feed_item_small(&$vars) {
  _culturebox_preprocess_node__feed_item($vars);
}

/**
 * Preprocess variables of feed_item home from editor medium.
 */
function culturebox_preprocess_node__feed_item_home_from_editor_medium(&$vars) {
  _culturebox_preprocess_node__feed_item($vars);
}

/**
 * Preprocess variables of feed_item home from editor small.
 */
function culturebox_preprocess_node__feed_item_home_from_editor_small(&$vars) {
  $node = $vars['node'];
  $path = field_get_items('node', $node, 'field_url');
  // Process title.
  $vars['title_link'] = culturebox_site_l(
      $node->title, $path[0]['display_url'], array(
    'attributes' => array('title' => $node->title),
      )
  );

  // Process categories.
  $field_category = field_get_items('node', $node, 'field_categories');
  if ($field_category) {
    $thematic_id = end($field_category);
    $thematic = taxonomy_term_load($thematic_id['tid']);
    if ($thematic) {
      $vars['category'] = culturebox_site_l($thematic->name, "taxonomy/term/{$thematic->tid}");
    }
  }
}

/**
 * Preprocess variables of feed_item title.
 */
function culturebox_preprocess_node__feed_item_title(&$vars) {
  $path = field_get_items('node', $vars['node'], 'field_url');
  $vars['title_link'] = culturebox_site_l(
      $vars['node']->title, $path[0]['display_url'], array(
    'attributes' => array('title' => $vars['node']->title),
      )
  );
}

/**
 * Preprocess variables of feed_item title.
 */
function culturebox_preprocess_node__feed_item_topcategory_first(&$vars) {
  _culturebox_preprocess_node__feed_item($vars);
}

/**
 * Cut player rss error.
 */
function culturebox_feed_description_filter($text, $title) {
  $text = trim(preg_replace('/kitd\.html5loader\("flash_kplayer.*"\);/', '', $text));
  if (strlen($text) < 4) {
    if (!empty($title)) {
      $text = $title;
    }
    else {
      $text = '';
    }
  }
  return $text;
}

/**
 * Override theme_radios().
 */
function culturebox_radios($variables) {
  if ($variables['element']['#name'] == 'sort_by') {
    return '<ul class="form-trier-list">' . $variables['element']['#children'] . '</ul>';
  }
}

function culturebox_preprocess_asset__video__videomaton(&$vars) {
  $video = $vars['elements']['#entity'];
  $vars['video'] = $video;
  $vars['aclasses'] = '';

  // Construct array of filters.
  $asset_filters = field_get_items('asset', $video, 'field_asset_filters');
  if (!empty($asset_filters)) {
    $classes = array();
    $tids = array();
    $filters = array(
      'genre' => array(
        'theatre' => 'Théatre',
        'concert' => 'Concert',
        'danse' => 'Danse',
        'cirque' => 'Cirque',
        'lecture' => 'Lecture',
        'mime' => 'Mime',
      ),
      'public' => array(
        'jeune' => 'Jeune',
        'tout' => 'Tout',
        'adulte' => 'Adulte',
      ),
    );

    $tids = & drupal_static('culturebox_videomaton_filters_tids', array());

    if (empty($tids)) {
      foreach ($filters as $group) {
        foreach ($group as $name => $filter) {
          $tid = db_query_range("SELECT td.tid FROM {taxonomy_term_data} td INNER JOIN {taxonomy_vocabulary} tv ON td.vid = tv.vid WHERE tv.machine_name = :vocab AND td.name = :name", 0, 1, array(':vocab' => 'filtres_videomatons', 'name' => $filter))->fetchField();

          if ($tid) {
            $tids[$tid] = array('group' => $group, 'filter' => $name);
          }
        }
      }
    }

    foreach ($asset_filters as $term) {
      if (array_key_exists($term['tid'], $tids)) {
        $classes[] = $tids[$term['tid']]['filter'];
      }
    }

    $vars['aclasses'] = implode(' ', $classes);
  }

  // Video URL.
  $asset_video = field_get_items('asset', $video, 'field_asset_video');
  $custom_image = field_get_items('asset', $video, 'field_asset_image_videomaton');

  if (!empty($custom_image[0]['uri'])) {
    $vars['asset_snapshot_url'] = image_style_url('videomaton_216x257', $custom_image[0]['uri']);
  }
  else {
    $vars['asset_snapshot_url'] = $asset_video[0]['snapshot'];
  }

  if (substr($asset_video[0]['url'], 0, 8) == CULTUREBOX_SITE_VIDEO_DM_CLOUD_URL_PREFIX) {
    module_load_include('inc', 'dmcloud', 'dmcloud');
    $num = substr($asset_video[0]['url'], 8);
    $vars['video_url'] = dmcloudlib_get_path_video($num);

    if (empty($custom_image)) {
      $dmc_rfu = culturebox_site_dmcloud_sgv_get_url('dmcloud_sgv_readfullurl');
      if (!empty($dmc_rfu)) {
        $request = drupal_http_request($dmc_rfu . '/' . $num);

        if ($request->code < 300 && !empty($request->data)) {
          $json = json_decode($request->data);

          if (!empty($json->video->assets->jpeg_thumbnail_large->url)) {
            $vars['asset_snapshot_url'] = $json->video->assets->jpeg_thumbnail_large->url;
          }
        }
      }
    }
  }
  else {
    $vars['video_url'] = $asset_video[0]['url'];

    if (function_exists('emvideo_dailymotion_emvideo_parse') && $info = emvideo_dailymotion_emvideo_parse($asset_video[0]['url'])) {
      if (!empty($info['source'])) {
        $vars['video_url'] = $info['source'];
      }
    }
    elseif (strpos($asset_video[0]['url'], 'www.ina.fr') !== FALSE) {
      // Taken from emvideo_ina_emvideo_parse().
      if (preg_match('@/video/ticket/([a-zA-Z0-9\/]*)@', $asset_video[0]['url'], $matches)) {
        $embed = explode('/', $matches[1]);
        $video_id = $embed[0];
      }
      elseif (preg_match('@http://www.ina.fr(?:.*?)/video/([^/]*)@', $asset_video[0]['url'], $matches)) {
        $video_id = $matches[1];
      }

      if (!empty($video_id)) {
        $vars['video_url'] = 'http://player.ina.fr/player/ticket/' . $video_id . '/997653/fc4a6fe071a278f22c7f7d136b2ee8d2';
      }
    }
  }

  $asset_url = field_get_items('asset', $video, 'field_asset_url');
  $vars['asset_url'] = $asset_url[0]['url'];
  $vars['asset_title'] = check_plain($asset_url[0]['title']);

  if (!empty($vars['asset_snapshot_url']) && empty($custom_image)) {
    $vars['asset_snapshot_url'] = str_replace('http://default/', $GLOBALS['base_url'] . base_path(), $vars['asset_snapshot_url']);
    module_load_include('inc', 'culturebox_site', 'culturebox_site.seo');

    if (!culturebox_site_url_is_external($vars['asset_snapshot_url']) && strpos($vars['asset_snapshot_url'], 'sites/all/modules/custom/culturebox_site/img/video-thumbnail.jpg') === FALSE) {
      if (preg_match('/.*\/sites\/default\/files\/styles\/(.*)\/public\/.*//*', $vars['asset_snapshot_url'], $matches)) {
        $vars['asset_snapshot_url'] = str_replace($matches[1], 'videomaton_216x257', $vars['asset_snapshot_url']);
      }
      else {
        $vars['asset_snapshot_url'] = image_style_url('videomaton_216x257', $vars['asset_snapshot_url']);
      }
    }
    else {
      $vars['asset_snapshot_img'] = theme('imagecache_external', array('style_name' => 'videomaton_216x257', 'path' => $vars['asset_snapshot_url'], 'alt' => $vars['asset_title']));
    }
  }
}

function culturebox_preprocess_node__article_videomaton(&$vars) {
  // Title.
  $title = field_get_items('node', $vars['node'], 'field_title_long');
  if ($title) {
    $vars['title'] = $title[0]['safe_value'];
  }

  // Published date.
  _culturebox_preprocess_article_published_date($vars);

  // Signature.
  _culturebox_preprocess_article_signature($vars);

  // Description.
  $description = field_get_items('node', $vars['node'], 'field_article_catchline');
  if ($description) {
    $vars['description'] = $description[0]['safe_value'];
  }

  // Share links.
  $vars['share_links_bottom'] = theme('share_links_ajax', array('node' => $vars['node']));

  if (!($media = _culturebox_get_article_main_media_asset_view($vars))) {
    $media = _culturebox_get_article_media_asset_view($vars);
  }
  if (!empty($media) && !empty($vars['field_article_media'][0]['target_id'])) {
    $aid = $vars['field_article_media'][0]['target_id'];

    if (!empty($media['asset'][$aid])) {
      $tmp_media = $media['asset'][$aid];

      if ($tmp_media["#bundle"] == 'image') {
        $vars['img_full_seo'] = file_create_url($tmp_media['field_asset_image']['#items'][0]['uri']);

        $vars['microdata_image'] = '<meta itemprop="image" content="' . $vars['img_full_seo'] . '" />';
      }
    }

    $vars['media'] = drupal_render($media);
  }
}

/**
 * Process variables for views-view-field.tpl.php.
 *
 * @see views-view-fields.tpl.php
 */
function culturebox_preprocess_views_view_field(&$variables) {
  if (isset($variables['theme_hook_suggestion'])) {
    $function = 'culturebox_preprocess_' . $variables['theme_hook_suggestion'];
    if (function_exists($function)) {
      $function($variables);
    }
  }
}

/**
 * Preprocess variables for views-view-field--lives-list--icalendar.tpl.php.
 *
 * @see views-view-field--lives-list--icalendar.tpl.php
 */
function culturebox_preprocess_views_view_field__lives_list__icalendar(&$vars) {
  if (in_array($vars['field']->field, array('field_live_start_time2', 'field_live_end_time'))) {
    $field_name = 'field_' . $vars['field']->field;
    if (!empty($vars['row']->{$field_name}[0]['raw']['value'])) {
      $date = strtotime($vars['row']->{$field_name}[0]['raw']['value']);
      $date = new DateObject($date);
      $date->setTimezone(timezone_open('UTC'));
      $date = format_date(strtotime($date->format(DATE_FORMAT_DATETIME)), 'custom', 'Ymd\THi\0\0\Z');
      $vars['output'] = preg_replace('/>.*</', '>' . $date . '<', $vars['output']);
    }
  }
}

function culturebox_image($variables) {
  $current_path = current_path();
  $arg = arg();

  // Liste des styles d'images qui seront toujours lazyloadées (images de petite taille).
  $image_style_names = array(
    'live_related_video_178x90',
    'live_most_viewed',
    'live_slider',
    'article_view_small_image',
    'article_view_very_small',
    'live_la_section_320x160',
  );

  if (
      $current_path != 'resultats/widgets/external.html' && $current_path != 'live/export' && (
      $current_path == '<front>' ||
      ($arg[0] == 'taxonomy' && $arg[1] == 'term' && is_numeric($arg[2])) ||
      $current_path == 'live/tous-les-lives' ||
      $current_path == 'live/festivals' ||
      $current_path == 'live' ||
      ($arg[0] == 'emissions' && !empty($arg[1]) && is_numeric($arg[1]) && !empty($arg[2]) && $arg[2] == CULTUREBOX_EMISSION_LISTING_URL_PART) ||
      ($arg[0] == 'emissions' && count($arg) == 1) ||
      (!empty($variables['style_name']) && in_array($variables['style_name'], $image_style_names)))
  ) {
    $attributes = $variables['attributes'];
    $attributes['src'] = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
    $attributes['onload'] = $attributes['onerror'] = 'lzld(this)';
    $attributes['data-frz-src'] = file_create_url($variables['path']);
    foreach (array('width', 'height', 'alt', 'title') as $key) {
      if (isset($variables[$key])) {
        $attributes[$key] = $variables[$key];
      }
    }
    return '<img' . drupal_attributes($attributes) . ' />';
  }
  else {
    return theme_image($variables);
  }
}

function culturebox_preprocess_views_view_rss(&$vars) {
  if (isset($vars['theme_hook_suggestion'])) {
    $function = 'culturebox_preprocess_' . $vars['theme_hook_suggestion'];
    if (function_exists($function)) {
      $function($vars);
    }
  }

  if (!empty($vars['view']->current_display) && $vars['view']->current_display == 'emission_rss' && !empty($vars['view']->args[0])) {
    $vars['link'] = url('taxonomy/term/' . $vars['view']->args[0], array('absolute' => TRUE)) . '/rss';
  }
}

function culturebox_preprocess_views_view_rss__rss__last_modified(&$vars) {
  drupal_add_http_header('Cache-Control', 'public, max-age=0, s-maxage=60');
  drupal_add_http_header('Expires', gmdate('D, d M Y H:i:s', time() + 60) . ' GMT');

  if (!empty($vars['view']->style_plugin->row_plugin->nodes)) {
    $nodes = $vars['view']->style_plugin->row_plugin->nodes;

    foreach ($vars['view']->style_plugin->row_plugin->nodes as $nid => $node) {
      // On liste les champs dont on a besoin.
      switch ($node->type) {
        case 'live':
          $field_image = 'field_live_media';
          $field_categories = 'field_live_categories';
          break;
        case 'fiche_emission':
          $field_image = 'field_fiche_emission_main_media';
          $field_categories = 'field_categories';
          break;
        default:
          $field_image = 'field_article_media';
          $field_categories = 'field_categories';
      }
      // Image description.
      if (!empty($node->{$field_image})) {
        $media = field_get_items('node', $node, $field_image);
        $legende_image = '';

        if (!empty($media[0]['entity']) && $media[0]['entity']->type == 'image') {
          $image = field_get_items('asset', $media[0]['entity'], 'field_asset_image');
          if (!empty($image)) {
            $url = file_create_url($image[0]['uri']);
            $vars['view']->style_plugin->row_plugin->nodes[$nid]->final_attachment = '<enclosure url="' . $url . '" type="' . $image[0]['filemime'] . '" length="' . @filesize(drupal_realpath($image[0]['uri'])) . '" />' . "\n";
          }

          $description = field_get_items('asset', $media[0]['entity'], 'field_asset_description');
          $legende_image = $description[0]['safe_value'];
        }

        if (!empty($legende_image)) {
          $vars['view']->style_plugin->row_plugin->nodes[$nid]->legende_image = '<legendeImage>' . $legende_image . '</legendeImage>';
        }
      }

      // Tags.
      if (!empty($node->{$field_categories})) {
        $tags = field_get_items('node', $node, $field_categories);
        $thematics = '';

        if (!empty($tags)) {
          $t = array();

          foreach ($tags as $tag) {
            $t[] = $tag['tid'];
          }
          $ts = taxonomy_term_load_multiple($t);
          foreach ($ts as $cat) {
            $thematics[] = $cat->name;
          }

          if (!empty($thematics)) {
            $thematics = implode(', ', $thematics);
          }
        }

        $vars['view']->style_plugin->row_plugin->nodes[$nid]->thematics = '<thematiques>' . $thematics . '</thematiques>';
      }

      if ($node->type == 'article' || $node->type == 'fiche_emission') {
        $content = '';
        if (!empty($node->field_body)) {
          $body = field_get_items('node', $node, 'field_body');

          if (isset($body)) {
            if (!empty($content)) {
              $content .= ' ';
            }

            if (!empty($body[0]['value'])) {
              $content .= $body[0]['value'];
            }
          }
        }

        $content = trim(strip_tags($content));

        if (empty($content) || (!empty($content) && str_word_count($content) < 80)) {
          // On vérifie que le corps contient au moins 80 mots.
          unset($nodes[$nid]);
        }
      }

      $vars['view']->style_plugin->row_plugin->nodes[$nid]->final_link = !empty($node->link) ? $node->link : $GLOBALS['base_url'] . $GLOBALS['base_path'] . drupal_lookup_path('alias', 'node/' . $node->nid);

      if (!empty($node->changed)) {
        $node->show_update_date = TRUE;
      }
    }

    $vars['view']->style_plugin->row_plugin->nodes = array_intersect_key($vars['view']->style_plugin->row_plugin->nodes, $nodes);
  }
}

function culturebox_preprocess_twitter_pull_listing(&$vars) {
  if (!empty($vars['custom_template'])) {
    // Template spécial du bloc Twitter pour les émissions.
    $vars['theme_hook_suggestions'][] = "twitter_pull_listing__{$vars['custom_template']}";

    if (!empty($vars['tweets']) && is_array($vars['tweets'])) {
      foreach ($vars['tweets'] as $key => &$tweet) {
        $tweet->time_ago = format_interval(time() - $tweet->timestamp, 1);
      }
    }
  }
}

function culturebox_preprocess_node__extrait_live_full(&$vars) {
  $node = $vars['node'];

  if (!empty($node->field_extrait_live)) {
    $extrait_live = field_get_items('node', $node, 'field_extrait_live');

    if (!empty($extrait_live[0]['entity'])) {
      $node_extrait_live = $extrait_live[0]['entity'];
    }
    else {
      $node_extrait_live = node_load($extrait_live[0]['target_id']);
    }

    // Bloc intégral.
    $node_view = node_view($node_extrait_live, 'live_home_derniers');
    $vars['node_extrait_live'] = render($node_view);

    // Bloc sommaire.
    $vars['node_extrait_sommaire'] = views_embed_view('lives_list', 'all_extracts_by_live', $node_extrait_live->nid);
  }

  if (!empty($node->field_extrait_video_bonus) && $node->field_extrait_video_bonus[LANGUAGE_NONE][0]['value'] == 1) {
    $vars['is_bonus'] = TRUE;
  }

  $vars['share_links'] = theme('share_links_ajax', array('node' => $vars['node']));
}

function culturebox_preprocess_views_view_field__lives_list__all_extracts_by_live__title(&$vars) {
  // Do not output link if we are on the same page.
  $node = menu_get_object();

  if (!empty($node) && $node->type == 'extrait_live') {
    if ($vars['row']->nid == $node->nid) {
      $vars['output'] = strip_tags($vars['output']);
    }
  }
}

function culturebox_preprocess_views_view_unformatted__lives_list__all_extracts_by_live(&$vars) {
  // Add "active" class to <div> if current node is listed.
  $id = NULL;
  $node = menu_get_object();

  if (!empty($node) && $node->type == 'extrait_live') {
    if (!empty($vars['view']->result)) {
      foreach ($vars['view']->result as $key => $result) {
        if ($result->nid == $node->nid) {
          $id = $key;
          break;
        }
      }

      if ($id !== NULL) {
        $vars['classes'][$id][] = 'active';
        $vars['classes_array'][$id] = implode(' ', $vars['classes'][$id]);
      }
    }
  }
}

function culturebox_preprocess_taxonomy_term__event_agenda(&$vars) {
  $start_date = !empty($vars['term']->field_date_start[LANGUAGE_NONE][0]['value']) ? strtotime($vars['term']->field_date_start[LANGUAGE_NONE][0]['value']) : '';
  $end_date = !empty($vars['term']->field_date_end[LANGUAGE_NONE][0]['value']) ? strtotime($vars['term']->field_date_end[LANGUAGE_NONE][0]['value']) : '';
  $subtitle = '';

  if ($start_date && $end_date) {
    if (format_date($start_date, 'custom', 'Y') == format_date($end_date, 'custom', 'Y')) {
      if (format_date($start_date, 'custom', 'm') == format_date($end_date, 'custom', 'm')) {
        if (format_date($start_date, 'custom', 'j') == format_date($end_date, 'custom', 'j')) {
          $start_date = format_date($start_date, 'custom', 'j F');
          $end_date = FALSE;
        }
        else {
          $start_date = format_date($start_date, 'custom', 'j');
          $end_date = format_date($end_date, 'custom', 'j F');
        }
      }
      else {
        $start_date = format_date($start_date, 'custom', 'j F');
        $end_date = format_date($end_date, 'custom', 'j F');
      }
    }
    else {
      $start_date = format_date($start_date, 'custom', 'j F Y');
      $end_date = format_date($end_date, 'custom', 'j F');
    }

    if ($start_date && $end_date) {
      $subtitle = sprintf('Du %s au %s', drupal_strtolower($start_date), drupal_strtolower($end_date));
    }
    else {
      $subtitle = sprintf('Du %s', drupal_strtolower($start_date));
    }
  }
  elseif ($start_date) {
    $subtitle = sprintf('Du %s', drupal_strtolower(format_date($start_date, 'custom', 'j F')));
  }
  elseif ($end_date) {
    $subtitle = sprintf('Au %s', drupal_strtolower(format_date($start_date, 'custom', 'j F')));
  }

  $vars['subtitle'] = $subtitle;
}

function culturebox_preprocess_taxonomy_term__mini_site_agenda(&$vars) {
  $start_date = !empty($vars['term']->field_mini_site_start_date[LANGUAGE_NONE][0]['value']) ? strtotime($vars['term']->field_mini_site_start_date[LANGUAGE_NONE][0]['value']) : '';
  $end_date = !empty($vars['term']->field_mini_site_end_date[LANGUAGE_NONE][0]['value']) ? strtotime($vars['term']->field_mini_site_end_date[LANGUAGE_NONE][0]['value']) : '';
  $subtitle = '';

  if ($start_date && $end_date) {
    if (format_date($start_date, 'custom', 'Y') == format_date($end_date, 'custom', 'Y')) {
      if (format_date($start_date, 'custom', 'm') == format_date($end_date, 'custom', 'm')) {
        if (format_date($start_date, 'custom', 'j') == format_date($end_date, 'custom', 'j')) {
          $start_date = format_date($start_date, 'custom', 'j F');
          $end_date = FALSE;
        }
        else {
          $start_date = format_date($start_date, 'custom', 'j');
          $end_date = format_date($end_date, 'custom', 'j F');
        }
      }
      else {
        $start_date = format_date($start_date, 'custom', 'j F');
        $end_date = format_date($end_date, 'custom', 'j F');
      }
    }
    else {
      $start_date = format_date($start_date, 'custom', 'j F');
      $end_date = format_date($end_date, 'custom', 'j F');
    }

    if ($start_date && $end_date) {
      $subtitle = sprintf('Du %s au %s', drupal_strtolower($start_date), drupal_strtolower($end_date));
    }
    else {
      $subtitle = sprintf('Du %s', drupal_strtolower($start_date));
    }
  }
  elseif ($start_date) {
    $subtitle = sprintf('Du %s', drupal_strtolower(format_date($start_date, 'custom', 'j F')));
  }
  elseif ($end_date) {
    $subtitle = sprintf('Au %s', drupal_strtolower(format_date($start_date, 'custom', 'j F')));
  }

  $vars['subtitle'] = $subtitle;
}

function culturebox_preprocess_taxonomy_term__mini_site_home_teaser(&$vars) {
  $start_date = !empty($vars['term']->field_mini_site_start_date[LANGUAGE_NONE][0]['value']) ? strtotime($vars['term']->field_mini_site_start_date[LANGUAGE_NONE][0]['value']) : '';
  $end_date = !empty($vars['term']->field_mini_site_end_date[LANGUAGE_NONE][0]['value']) ? strtotime($vars['term']->field_mini_site_end_date[LANGUAGE_NONE][0]['value']) : '';
  $subtitle = '';

  if ($start_date && $end_date) {
    if (format_date($start_date, 'custom', 'Y') == format_date($end_date, 'custom', 'Y')) {
      if (format_date($start_date, 'custom', 'm') == format_date($end_date, 'custom', 'm')) {
        if (format_date($start_date, 'custom', 'j') == format_date($end_date, 'custom', 'j')) {
          $start_date = format_date($start_date, 'custom', 'j F Y');
          $end_date = FALSE;
        }
        else {
          $start_date = format_date($start_date, 'custom', 'j');
          $end_date = format_date($end_date, 'custom', 'j F Y');
        }
      }
      else {
        $start_date = format_date($start_date, 'custom', 'j F');
        $end_date = format_date($end_date, 'custom', 'j F Y');
      }
    }
    else {
      $start_date = format_date($start_date, 'custom', 'j F Y');
      $end_date = format_date($end_date, 'custom', 'j F Y');
    }

    if ($start_date && $end_date) {
      $subtitle = sprintf('Du %s au %s', drupal_strtolower($start_date), drupal_strtolower($end_date));
    }
    else {
      $subtitle = sprintf('Du %s', drupal_strtolower($start_date));
    }
  }
  elseif ($start_date) {
    $subtitle = sprintf('Du %s', drupal_strtolower(format_date($start_date, 'custom', 'j F Y')));
  }
  elseif ($end_date) {
    $subtitle = sprintf('Au %s', drupal_strtolower(format_date($start_date, 'custom', 'j F Y')));
  }

  $vars['subtitle'] = $subtitle;
}

function culturebox_preprocess_taxonomy_term__event_home_teaser(&$vars) {
  $start_date = !empty($vars['term']->field_date_start[LANGUAGE_NONE][0]['value']) ? strtotime($vars['term']->field_date_start[LANGUAGE_NONE][0]['value']) : '';
  $end_date = !empty($vars['term']->field_date_end[LANGUAGE_NONE][0]['value']) ? strtotime($vars['term']->field_date_end[LANGUAGE_NONE][0]['value']) : '';
  $subtitle = '';

  if ($start_date && $end_date) {
    if (format_date($start_date, 'custom', 'Y') == format_date($end_date, 'custom', 'Y')) {
      if (format_date($start_date, 'custom', 'm') == format_date($end_date, 'custom', 'm')) {
        if (format_date($start_date, 'custom', 'j') == format_date($end_date, 'custom', 'j')) {
          $start_date = format_date($start_date, 'custom', 'j F Y');
          $end_date = FALSE;
        }
        else {
          $start_date = format_date($start_date, 'custom', 'j');
          $end_date = format_date($end_date, 'custom', 'j F Y');
        }
      }
      else {
        $start_date = format_date($start_date, 'custom', 'j F');
        $end_date = format_date($end_date, 'custom', 'j F Y');
      }
    }
    else {
      $start_date = format_date($start_date, 'custom', 'j F Y');
      $end_date = format_date($end_date, 'custom', 'j F Y');
    }

    if ($start_date && $end_date) {
      $subtitle = sprintf('Du %s au %s', drupal_strtolower($start_date), drupal_strtolower($end_date));
    }
    else {
      $subtitle = sprintf('Du %s', drupal_strtolower($start_date));
    }
  }
  elseif ($start_date) {
    $subtitle = sprintf('Du %s', drupal_strtolower(format_date($start_date, 'custom', 'j F Y')));
  }
  elseif ($end_date) {
    $subtitle = sprintf('Au %s', drupal_strtolower(format_date($start_date, 'custom', 'j F Y')));
  }

  $vars['subtitle'] = $subtitle;
}

function culturebox_preprocess_node__live_home_from_editor_big_3(&$vars) {
  $node = $vars['node'];

  // Status.
  $status = field_get_items('node', $node, 'field_live_status');
  $status = $status[0]['value'];

  $bientot_statuses = array(
    CULTUREBOX_LIVE_STATUS_LIVE_PROCHAINEMENT,
    CULTUREBOX_LIVE_STATUS_LIVE_AVANT_LE_DIRECT,
    CULTUREBOX_LIVE_STATUS_LIVE_RETARD
  );

  // Image section.
  $live_media = '';
  $media_view = _culturebox_get_first_live_media_asset_view($vars);

  if (!empty($media_view)) {
    $live_media = drupal_render($media_view);
  }
  $vars['media'] = culturebox_site_l(
      $live_media, 'node/' . $vars['nid'], array(
    'html' => TRUE,
      )
  );

  if (!empty($vars['field_live_start_time2'])) {
    $field_data = field_get_items('node', $vars['node'], 'field_live_start_time2');
    $field_live_start_time = array_shift($field_data);
    $live_start_time = $field_live_start_time['value'];
  }

  // Status depended logic.
  $vars['status'] = _culturebox_get_node_live_status($vars);

  // Status icon.
  // Counter section.
  _culturebox_preprocess_live_button_play($vars);
  if (!empty($live_start_time) &&
      !empty($vars['field_live_countdown'][LANGUAGE_NONE][0]['value']) && in_array($status, $bientot_statuses)
  ) {
    // Get remain time before live start.
    $timezone = new DateTimeZone('Europe/Paris');
    $start_time_obj = new DateTime($live_start_time, $timezone);
    $time = new DateTime('now', $timezone);
    $offset = $time->getOffset() / 60;

    drupal_add_js(array('CultureboxLive' => array('CountdownTime' => $start_time_obj->format('m/d/Y H:i:s'), 'offset' => $offset)), 'setting');
    $vars['counter'] = TRUE;
  }

  // Line section.
  // 1.1 Thematic associations.
  $vars['main_thematic'] = _culturebox_get_node_live_main_category($vars['node']);

  $vars['title_link'] = culturebox_site_l($vars['node']->title, "node/${vars['nid']}");
}

function culturebox_preprocess_node__live_small(&$vars) {
  $node = $vars['node'];

  if (!empty($node->cb_festival_player)) {
    if ($node->field_live_direct_replay_switch[LANGUAGE_NONE][0]['value'] == 0 && strtotime($node->field_live_end_time[LANGUAGE_NONE][0]['value']) < time()) {
      $date = $node->field_live_replay_start_time[LANGUAGE_NONE][0];
    }
    else {
      // On essaye de récupérer la date de mise en avant.
      if (!empty($node->field_date_start[LANGUAGE_NONE][0]['value'])) {
        $date = $node->field_date_start[LANGUAGE_NONE][0];
      }
      else {
        // Sinon, si c'est un live de type "direct + replay", on prend la date de début du live si elle n'est pas encore passée, sinon la date de début du replay.
        if ($node->field_live_direct_replay_switch[LANGUAGE_NONE][0]['value'] == 0) {
          if (!empty($node->field_live_start_time2[LANGUAGE_NONE][0]['value']) && strtotime($node->field_live_start_time2[LANGUAGE_NONE][0]['value']) > time()) {
            $date = $node->field_live_start_time2[LANGUAGE_NONE][0];
          }
          elseif (!empty($node->field_live_replay_start_time[LANGUAGE_NONE][0]['value'])) {
            $date = $node->field_live_replay_start_time[LANGUAGE_NONE][0];
          }
        }
        else {
          // Pour les autres cas, on prend la date de début du live pour les lives de type "direct" et la date de début du replay pour les autres types de live.
          if ($node->field_live_direct_replay_switch[LANGUAGE_NONE][0]['value'] == 1 && !empty($node->field_live_start_time2[LANGUAGE_NONE][0]['value'])) {
            $date = $node->field_live_start_time2[LANGUAGE_NONE][0];
          }
          elseif (!empty($node->field_live_replay_start_time[LANGUAGE_NONE][0]['value'])) {
            $date = $node->field_live_replay_start_time[LANGUAGE_NONE][0];
          }
        }
      }
    }

    if (!empty($date)) {
      _culturebox_site_preprocess_date_countdown($vars, $date);
    }

    if (!empty($node->field_live_artist_name[LANGUAGE_NONE][0]['value'])) {
      $vars['title'] = $node->field_live_artist_name[LANGUAGE_NONE][0]['value'];
    }
  }
  else {
    // Status.
    $status = field_get_items('node', $node, 'field_live_status');
    $status = $status[0]['value'];

    $bientot_statuses = array(
      CULTUREBOX_LIVE_STATUS_LIVE_PROCHAINEMENT,
      CULTUREBOX_LIVE_STATUS_LIVE_AVANT_LE_DIRECT,
      CULTUREBOX_LIVE_STATUS_LIVE_RETARD,
    );

    if (!empty($vars['field_live_start_time2'])) {
      $field_data = field_get_items('node', $vars['node'], 'field_live_start_time2');
      $field_live_start_time = array_shift($field_data);
    }

    if (!empty($field_live_start_time) && in_array($status, $bientot_statuses)) {
      // Date.
      _culturebox_site_preprocess_date_countdown($vars, $field_live_start_time);
    }
  }
}

function culturebox_preprocess_node__playlist_full(&$vars) {
  // Par défaut, on n'affiche que la première vidéo.
  if (!empty($vars['field_playlist_videos'])) {
    $vars['first_video'] = entity_view('node', array($vars['field_playlist_videos'][0]['entity']), 'very_small');
  }

  // Share links.
  $vars['share_links'] = theme('share_links_ajax', array('node' => $vars['node']));

  // On ajoute le JS player car on en aura certainement besoin pour les lives.
  $flash_player_url = _culturebox_emission_get_flash_player_url();

  if (!empty($flash_player_url)) {
    if ($GLOBALS['theme'] != 'culturebox_mobile') {
      drupal_add_js($flash_player_url, array('type' => 'external', 'weight' => 10));
      drupal_add_js(array(
        'CultureboxLive' => array(
          'playerWidth' => 660,
          'playerHeight' => 370,
        ),
          ), 'setting');
    }
  }

  // On ajoute la librairie SWFObject car on en aura certainement besoin pour les extraits lives DMCloud.
  drupal_add_js('http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js', array('type' => 'external', 'weight' => 10));
}

function culturebox_preprocess_node__live_very_small(&$vars) {
  _culturebox_preprocess_node__live_widget($vars);

  if ($GLOBALS['theme'] != 'culturebox_mobile') {
    drupal_add_js(array(
      'CultureboxLive' => array(
        'playerWidth' => 660,
        'playerHeight' => 370,
      ),
        ), 'setting');
  }
}

/**
 * Preprocess variables for node--live-dans-actu.tpl.php.
 *
 */
function culturebox_preprocess_node__live_dans_actu(&$vars) {
  $live = $vars['node'];

  if ($thematic = _culturebox_get_node_live_main_category($live)) {
    $vars['category'] = $thematic;
  }
  _culturebox_preprocess_live_button_play($vars, NULL, 'play-small');

  if (!empty($vars['content']['field_live_media'])) {
    $vars['image'] = culturebox_site_l(
        drupal_render($vars['content']['field_live_media']), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }
}

function culturebox_preprocess_views_view__lives_list__live_list_tous_les_lives(&$vars) {
  $term = menu_get_object('taxonomy_term', 2);

  if (!empty($term) && $term->vocabulary_machine_name == 'festivals') {
    $vars['exposed'] = '<h2>Tous les lives</h2>';
    $vars['classes_array'][] = 'main-block';
  }
}

function culturebox_preprocess_taxonomy_term__festivals_home_teaser(&$vars) {
  culturebox_preprocess_taxonomy_term__event_home_teaser($vars);
}

function culturebox_live_date($vars) {
  $get_date = field_get_items('node', $vars['node'], 'field_live_start_time');
  if (!empty($get_date[0]['value'])) {
    $time_date = strtotime($get_date[0]['value']);
    $date = format_date($time_date, 'custom', 'l j');
    $date .= strtolower(format_date($time_date, 'custom', ' F '));
    $date .= format_date($time_date, 'custom', ' Y');
  }
  return $date;
}

function culturebox_live_duree($vars) {
  $get_duree = field_get_items('node', $vars['node'], 'field_live_duration');
  if (!empty($get_duree[0]['value'])) {
    $duree = ' | ' . $get_duree[0]['value'];
  }
  return $duree;
}

function culturebox_preprocess_node__vote_full(&$vars) {
  $node = $vars['node'];

  // On contrôle l'affichage du témoignage.
  $vars['display_comment'] = FALSE;

  if (isset($node->field_vote_display_comment[LANGUAGE_NONE][0]['value']) && $node->field_vote_display_comment[LANGUAGE_NONE][0]['value'] == '1') {
    $vars['display_comment'] = TRUE;
  }

  // Description.
  if (!$vars['display_comment']) {
    $vars['description'] = 'Votre participation a bien été prise en compte. <br /><span>Merci d\'avoir participé en choisissant : </span>';
  }
  else {
    if (!empty($node->field_vote_prenom)) {
      if (!empty($node->field_recherche_livre)) {
        // Livre par entity reference.
        $livre = !empty($node->field_recherche_livre[LANGUAGE_NONE][0]['entity']) ? $node->field_recherche_livre[LANGUAGE_NONE][0]['entity'] : node_load($node->field_recherche_livre[LANGUAGE_NONE][0]['target_id']);

        $vars['description'] = sprintf('%s nous dit pourquoi "%s"', $node->field_vote_prenom[LANGUAGE_NONE][0]['value'], $livre->title);

        if (!empty($livre->field_personnalit_s[LANGUAGE_NONE][0]['target_id'])) {
          $auteur = taxonomy_term_load($livre->field_personnalit_s[LANGUAGE_NONE][0]['target_id']);

          $vars['description'] .= sprintf(' de %s', $auteur->name);
        }

        $vars['description'] .= ' a changé sa vie.';
      }
      elseif (!empty($node->field_votre_livre)) {
        // Livre custom.
        $vars['description'] = sprintf('%s nous raconte %s', $node->field_vote_prenom[LANGUAGE_NONE][0]['value'], $node->field_votre_livre[LANGUAGE_NONE][0]['value']);

        if (!empty($node->field_user_firstname[LANGUAGE_NONE][0]['value'])) {
          $vars['description'] .= sprintf(' de %s', $node->field_user_firstname[LANGUAGE_NONE][0]['value'] . (!empty($node->field_user_lastname[LANGUAGE_NONE][0]['value']) ? ' ' . $node->field_user_lastname[LANGUAGE_NONE][0]['value'] : ''));
        }
      }
    }
  }

  // Bloc oeuvres.
  _culturebox_get_bloc_livres($vars, $node, 'field_recherche_livre');

  // S'il n'y a pas d'entity reference vers un livre de la BDD, mais qu'il a été saisi manuellement.
  if (empty($vars['bloc_oeuvres']) && !empty($vars['field_votre_livre'])) {
    $image = theme('image', array('path' => CULTUREBOX_THEME_PATH . '/images/livre-generique.png', 'width' => 95, 'height' => 148));

    $auteur = '';

    if (!empty($vars['field_user_firstname'][0]['value'])) {
      $auteur = $vars['field_user_firstname'][0]['value'];
    }

    if (!empty($vars['field_user_lastname'][0]['value'])) {
      $auteur .= ' ' . $vars['field_user_lastname'][0]['value'];
    }

    $html_livre = theme('culturebox_oeuvre_livre_custom', array('image' => $image, 'auteur' => $auteur, 'titre' => !empty($vars['field_votre_livre'][0]['value']) ? $vars['field_votre_livre'][0]['value'] : ''));

    $vars['bloc_oeuvres'] = theme('culturebox_oeuvres_livres', array('lists' => array($html_livre), 'title_changed' => 'Le livre'));
  }

  // Share links.
  $facebook = array('class' => 'facebook', 'type' => 'recommend');
  $twitter = array('class' => 'twitter');
  $links = array(
    'facebook' => $facebook,
    'twitter' => $twitter,
  );

  $vars['share_links'] = theme('share_links', array('node' => $vars['node'], 'links' => $links));
}

function culturebox_preprocess_node__vote_teaser(&$vars) {
  $node = $vars['node'];

  // Description.
  if (!empty($node->field_vote_prenom)) {
    if (!empty($node->field_recherche_livre)) {
      // Livre par entity reference.
      $livre = !empty($node->field_recherche_livre[LANGUAGE_NONE][0]['entity']) ? $node->field_recherche_livre[LANGUAGE_NONE][0]['entity'] : node_load($node->field_recherche_livre[LANGUAGE_NONE][0]['target_id']);

      $vars['description'] = sprintf('%s nous dit pourquoi "%s"', $node->field_vote_prenom[LANGUAGE_NONE][0]['value'], $livre->title);

      if (!empty($livre->field_personnalit_s[LANGUAGE_NONE][0]['target_id'])) {
        $auteur = taxonomy_term_load($livre->field_personnalit_s[LANGUAGE_NONE][0]['target_id']);

        if (!empty($auteur)) {
          $vars['description'] .= sprintf(' de %s', $auteur->name);
        }
      }

      $vars['description'] .= ' a changé sa vie.';
    }
    elseif (!empty($node->field_votre_livre)) {
      // Livre custom.
      $vars['description'] = sprintf('%s nous dit pourquoi "%s"', $node->field_vote_prenom[LANGUAGE_NONE][0]['value'], $node->field_votre_livre[LANGUAGE_NONE][0]['value']);

      if (!empty($node->field_user_firstname[LANGUAGE_NONE][0]['value'])) {
        $vars['description'] .= sprintf(' de %s', $node->field_user_firstname[LANGUAGE_NONE][0]['value'] . (!empty($node->field_user_lastname[LANGUAGE_NONE][0]['value']) ? ' ' . $node->field_user_lastname[LANGUAGE_NONE][0]['value'] : ''));
      }

      $vars['description'] .= ' a changé sa vie.';
    }
  }

  // On contrôle l'affichage du témoignage.
  $vars['display_comment'] = FALSE;

  if (isset($node->field_vote_display_comment[LANGUAGE_NONE][0]['value']) && $node->field_vote_display_comment[LANGUAGE_NONE][0]['value'] == '1') {
    $vars['display_comment'] = TRUE;
  }
}

function culturebox_preprocess_node__vote_very_small(&$vars) {
  $node = $vars['node'];

  if (!empty($node->field_recherche_livre[LANGUAGE_NONE][0]['target_id'])) {
    $livre = node_load($node->field_recherche_livre[LANGUAGE_NONE][0]['target_id']);

    if (!empty($livre->field_personnalit_s[LANGUAGE_NONE][0]['target_id'])) {
      $auteur = taxonomy_term_load($livre->field_personnalit_s[LANGUAGE_NONE][0]['target_id']);

      $vars['auteur'] = $auteur->name;
    }

    $vars['titre_livre'] = db_query_range("SELECT title FROM {node} WHERE nid = :nid", 0, 1, array(':nid' => $node->field_recherche_livre[LANGUAGE_NONE][0]['target_id']))->fetchField();
  }
  elseif (!empty($node->field_votre_livre[LANGUAGE_NONE][0]['value'])) {
    if (!empty($node->field_user_firstname[LANGUAGE_NONE][0]['value'])) {
      $vars['auteur'] .= $node->field_user_firstname[LANGUAGE_NONE][0]['value'] . (!empty($node->field_user_lastname[LANGUAGE_NONE][0]['value']) ? ' ' . $node->field_user_lastname[LANGUAGE_NONE][0]['value'] : '');
    }

    $vars['titre_livre'] = $node->field_votre_livre[LANGUAGE_NONE][0]['value'];
  }
}

function culturebox_preprocess_node__extrait_live_search(&$vars) {
  $node = $vars['node'];

  if (!empty($node->field_extrait_live[LANGUAGE_NONE][0]['target_id'])) {
    $live = node_load($node->field_extrait_live[LANGUAGE_NONE][0]['target_id']);
    if ($thematic = _culturebox_get_node_live_main_category($live)) {
      $vars['category'] = $thematic;
    }
  }

  $vars['live_status'] = culturebox_site_l(
      '&nbsp;', 'node/' . $node->nid, array(
    'html' => TRUE,
    'attributes' => array(
      'class' => array('imitation-links', 'play-medium'),
    ),
      )
  );

  if ($media = _culturebox_get_first_diffusion_media_asset_view($vars, 'search', 'field_extrait_illustration')) {
    $vars['image'] = culturebox_site_l(render($media), 'node/' . $node->nid, array('html' => TRUE));
  }

  if (!empty($media_view)) {
    $vars['image'] = culturebox_site_l(
        drupal_render($media_view), 'node/' . $vars['nid'], array(
      'html' => TRUE,
        )
    );
  }
}

function culturebox_preprocess_node__feed_item_search(&$vars) {
  $node = $vars['node'];

  if ($thematic = _culturebox_get_node_main_category($node, 'field_categories')) {
    $vars['category'] = $thematic;
  }

  $vars['title'] = culturebox_site_l($vars['node']->title, $node->field_url[LANGUAGE_NONE][0]['display_url']);
}

function _culturebox_preprocess_live_channel(&$vars) {
  $node = $vars['node'];

  if (!empty($node->field_live_type) && !empty($node->field_live_type_channel)) {
    $get_type = field_get_items('node', $node, 'field_live_type');

    if (!empty($get_type[0]['value']) && in_array($get_type[0]['value'], array('direct_antenne', 'direct_antenne_manuel'))) {
      $get_channel = field_get_items('node', $node, 'field_live_type_channel');

      if (!empty($get_channel[0]['value'])) {
        $vars['channel'] = $get_channel[0]['value'];
      }
    }
  }
}

function culturebox_preprocess_taxonomy_term__event_playlist(&$vars) {
  // Image.
  if (!empty($vars['term']->field_illustration[LANGUAGE_NONE][0]['entity'])) {
    $asset_image = $vars['term']->field_illustration[LANGUAGE_NONE][0]['entity'];
  }
  elseif (!empty($vars['term']->field_illustration[LANGUAGE_NONE][0]['target_id'])) {
    $asset_image = asset_load($vars['term']->field_illustration[LANGUAGE_NONE][0]['target_id']);
  }

  if (isset($asset_image) && !empty($asset_image->field_asset_image[LANGUAGE_NONE][0]['uri'])) {
    $vars['event_image'] = l(theme('image_style', array('width' => 140, 'path' => $asset_image->field_asset_image[LANGUAGE_NONE][0]['uri'], 'style_name' => 'taxonomy_event_view_full_main_image')), "taxonomy/term/{$vars['tid']}", array('html' => TRUE));
  }

  // Date.
  $date_start = field_get_items('taxonomy_term', $vars['term'], 'field_date_start');
  if ($date_start && !empty($date_start[0]['value'])) {
    $date_start = new DateObject($date_start[0]['value'], $date_start[0]['timezone']);
    $start_month_number = date_format_date($date_start, 'custom', 'n', 'fr');
    $start_year_number = date_format_date($date_start, 'custom', 'Y', 'fr');
    $date_start_format = '\D\U d';
    $date_start_in_format = date_format_date($date_start, 'custom', 'd.m.Y', 'fr');
  }
  $date_end = field_get_items('taxonomy_term', $vars['term'], 'field_date_end');
  if ($date_end && !empty($date_end[0]['value'])) {
    $date_end = new DateObject($date_end[0]['value'], $date_end[0]['timezone']);
    $end_month_number = date_format_date($date_end, 'custom', 'n', 'fr');
    $end_year_number = date_format_date($date_end, 'custom', 'Y', 'fr');
    $date_end_format = '\A\U d F';
    $date_end_in_format = date_format_date($date_end, 'custom', 'd.m.Y', 'fr');
  }
  if (!empty($date_start_in_format) && !empty($date_end_in_format)) {
    if ($date_end_in_format == $date_start_in_format) {
      $vars['event_date'] = date_format_date($date_start, 'custom', 'd F', 'fr');
    }
    else {
      if ($start_month_number != $end_month_number) {
        $date_start_format .= ' F';
      }
      if ($start_year_number != $end_year_number) {
        $date_end_format .= ' Y';
      }

      $vars['event_date'] = date_format_date($date_start, 'custom', $date_start_format, 'fr') . " " . date_format_date($date_end, 'custom', $date_end_format, 'fr');
    }
  }
  $vars['term_name'] = l($vars['term']->name, 'taxonomy/term/' . $vars['term']->tid);
}

function culturebox_preprocess_taxonomy_term__mini_site_playlist(&$vars) {
  // Image.
  if (!empty($vars['term']->field_mini_site_main_image[LANGUAGE_NONE][0]['entity'])) {
    $asset_image = $vars['term']->field_mini_site_main_image[LANGUAGE_NONE][0]['entity'];
  }
  elseif ($vars['term']->field_mini_site_main_image[LANGUAGE_NONE][0]['target_id']) {
    $asset_image = asset_load($vars['term']->field_mini_site_main_image[LANGUAGE_NONE][0]['target_id']);
  }

  if (isset($asset_image) && !empty($asset_image->field_asset_image[LANGUAGE_NONE][0]['uri'])) {
    $vars['event_image'] = l(theme('image_style', array('width' => 140, 'path' => $asset_image->field_asset_image[LANGUAGE_NONE][0]['uri'], 'style_name' => 'taxonomy_event_view_full_main_image')), "taxonomy/term/{$vars['tid']}", array('html' => TRUE));
  }

  // Date.
  $date_start = field_get_items('taxonomy_term', $vars['term'], 'field_mini_site_start_date');
  if ($date_start && !empty($date_start[0]['value'])) {
    $date_start = new DateObject($date_start[0]['value'], $date_start[0]['timezone']);
    $start_month_number = date_format_date($date_start, 'custom', 'n', 'fr');
    $start_year_number = date_format_date($date_start, 'custom', 'Y', 'fr');
    $date_start_format = '\D\U d';
    $date_start_in_format = date_format_date($date_start, 'custom', 'd.m.Y', 'fr');
  }
  $date_end = field_get_items('taxonomy_term', $vars['term'], 'field_mini_site_end_date');
  if ($date_end && !empty($date_end[0]['value'])) {
    $date_end = new DateObject($date_end[0]['value'], $date_end[0]['timezone']);
    $end_month_number = date_format_date($date_end, 'custom', 'n', 'fr');
    $end_year_number = date_format_date($date_end, 'custom', 'Y', 'fr');
    $date_end_format = '\A\U d F';
    $date_end_in_format = date_format_date($date_end, 'custom', 'd.m.Y', 'fr');
  }
  if (!empty($date_start_in_format) && !empty($date_end_in_format)) {
    if ($date_end_in_format == $date_start_in_format) {
      $vars['event_date'] = date_format_date($date_start, 'custom', 'd F', 'fr');
    }
    else {
      if ($start_month_number != $end_month_number) {
        $date_start_format .= ' F';
      }
      if ($start_year_number != $end_year_number) {
        $date_end_format .= ' Y';
      }

      $vars['event_date'] = date_format_date($date_start, 'custom', $date_start_format, 'fr') . " " . date_format_date($date_end, 'custom', $date_end_format, 'fr');
    }
  }

  $vars['term_name'] = l($vars['term']->name, 'taxonomy/term/' . $vars['term']->tid);
}

function culturebox_preprocess_views_view_unformatted__article_list__must_read_list(&$vars) {
  $vars['block_title'] = 'Les plus lus';

  if (!empty($vars['view']->args[0])) {
    $thematique = taxonomy_term_load($vars['view']->args[0]);

    if (!empty($thematique->name)) {
      $vars['block_title'] = "{$thematique->name} <span>les plus lus</span>";
    }
  }
}

function culturebox_preprocess_views_view__block_read_also__all_actu(&$vars) {
  // On récupère la thématique passée en argument du display pour l'afficher dans le titre du bloc.
  if (!empty($vars['view']->args[0])) {
    $term = taxonomy_term_load($vars['view']->args[0]);
    $vars['thematic'] = check_plain($term->name);
  }
}

function culturebox_preprocess_views_view_unformatted__lives_list__articles_live_lives_la_une(&$vars) {
  $vars['live_title'] = 'Live';
  $vars['live_sub_title'] = 'La Une';

  if (!empty($vars['view']->result[0]->nid)) {
    $first_live = node_load($vars['view']->result[0]->nid);

    if (!empty($first_live->field_live_main_category)) {
      $field_live_main_category = field_get_items('node', $first_live, 'field_live_main_category');

      $vars['live_title'] = !empty($field_live_main_category[0]['tid']) && $field_live_main_category[0]['tid'] == 45629 ? "Concerts" : $vars['live_title'];
      $vars['live_title'] = !empty($field_live_main_category[0]['tid']) && $field_live_main_category[0]['tid'] == 45689 ? "Théâtre" : $vars['live_title'];
      $vars['live_title'] = !empty($field_live_main_category[0]['tid']) && $field_live_main_category[0]['tid'] == 45699 ? "Danse" : $vars['live_title'];
      $vars['live_title'] = !empty($field_live_main_category[0]['tid']) && $field_live_main_category[0]['tid'] == 45709 ? "Spectacles" : $vars['live_title'];
      $vars['live_title'] = !empty($field_live_main_category[1]['tid']) && $field_live_main_category[1]['tid'] == 45633 ? "Opéra" : $vars['live_title'];
    }
  }

  // Le sous-titre du bloc est défini par l'argument reçu par la vue.
  // Si on reçoit en argument une communauté, on l'affiche.
  // Sinon, si on reçoit une thématique, on l'affiche.
  if (!empty($vars['view']->args[1]) && $vars['view']->args[1] != 'all') {
    // Communauté.
    $field_info = field_info_field('field_live_communauté');

    if (!empty($field_info['settings']['allowed_values'][$vars['view']->args[1]])) {
      $vars['live_sub_title'] = check_plain($field_info['settings']['allowed_values'][$vars['view']->args[1]]);
    }
  }
  elseif (!empty($vars['view']->args[2]) && $vars['view']->args[2] != 'all') {
    // Thématique.
    $thematic = taxonomy_term_load($vars['view']->args[2]);

    if ($thematic) {
      $vars['live_sub_title'] = check_plain($thematic->name);
    }
  }
}

function culturebox_preprocess_node__live_ticker_small(&$vars) {
  $node = $vars['node'];

  $status = field_get_items('node', $vars['node'], 'field_live_status');
  $status = $status[0]['value'];

  $show_date_statuses = array(
    CULTUREBOX_LIVE_STATUS_LIVE_PROCHAINEMENT,
    CULTUREBOX_LIVE_STATUS_LIVE_AVANT_LE_DIRECT,
    CULTUREBOX_LIVE_STATUS_LIVE_RETARD,
  );

  $vars['show_play'] = in_array($status, array(CULTUREBOX_LIVE_STATUS_LIVE_DIRECT, CULTUREBOX_LIVE_STATUS_LIVE_REPLAY, CULTUREBOX_LIVE_STATUS_LIVE_LAST_CHANCE)) || _culturebox_live_is_extrait_live($node);

  $live_status_vars = array('node' => $vars['node'], 'hide_play' => TRUE);

  if (in_array($status, $show_date_statuses)) {
    $live_start_time = field_get_items('node', $vars['node'], 'field_live_start_time2');

    if ($live_start_time) {
      $live_start_time = array_shift($live_start_time);
      _culturebox_site_preprocess_date_countdown($vars, $live_start_time);

      $live_status_vars['custom'] = $vars['date'];
    }
  }

  $vars['status'] = theme('live_status', $live_status_vars);

  $vars['url'] = "node/{$node->nid}";

  if (!empty($node->ticker_custom_url)) {
    $vars['url'] = $node->ticker_custom_url;
  }

  // Image.
  $image = field_get_items('node', $vars['node'], 'field_live_media');

  if ($image) {
    if (!empty($image[0]['entity'])) {
      $image = $image[0]['entity'];
    }
    else {
      $image = asset_load($image[0]['target_id']);
    }

    $image->alt_title = $node->title;
    $image = $image->view('ticker_reduced');

    if ($image) {
      $vars['image'] = l(render($image), $vars['url'], array('html' => TRUE));
    }
  }
}

function culturebox_preprocess_node__live_ticker_big(&$vars) {
  $node = $vars['node'];

  $status = field_get_items('node', $vars['node'], 'field_live_status');
  $status = $status[0]['value'];

  $show_date_statuses = array(
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_PROCHAINEMENT,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_AVANT_LE_DIRECT,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_RETARD,
  );

  $diffusion_status_vars = array('node' => $vars['node'], 'hide_play' => TRUE);

  if (in_array($status, $show_date_statuses)) {
    $diffusion_start_time = field_get_items('node', $vars['node'], 'field_live_start_time2');

    if ($diffusion_start_time) {
      $diffusion_start_time = array_shift($diffusion_start_time);
      _culturebox_site_preprocess_date_countdown($vars, $diffusion_start_time);

      $diffusion_status_vars['custom'] = $vars['date'];
    }
  }

  $vars['status'] = theme('live_status', $diffusion_status_vars);

  $vars['url'] = "node/{$node->nid}";

  if (!empty($node->ticker_custom_url)) {
    $vars['url'] = $node->ticker_custom_url;
  }

  $show_player_statuses = array(
    CULTUREBOX_LIVE_STATUS_LIVE_DIRECT,
    CULTUREBOX_LIVE_STATUS_LIVE_REPLAY,
    CULTUREBOX_LIVE_STATUS_LIVE_LAST_CHANCE,
  );

  if (in_array($status, $show_player_statuses)) {
    $video_url = _culturebox_live_get_video_url($node);

    // Player link.
    if ($video_url) {
      $vars['image'] = '<a href="' . $video_url . '" class="player"></a>';

      $player_script_vars = array();
      $player_script_vars['default_width'] = 250;
      $player_script_vars['default_height'] = 125;
      $player_script_vars['default_showad'] = FALSE;
      $player_script_vars['default_mute'] = TRUE;
      $player_script_vars['default_recommendations'] = FALSE;
      $player_script_vars['skin_blacklist'] = array('shareButton', 'logo');

      $vars['image'] .= theme('culturebox_site_player_ftv_script', $player_script_vars);
    }
  }

  if (empty($vars['image'])) {
    // Image.
    $image = field_get_items('node', $vars['node'], 'field_live_media');

    if ($image) {
      if (!empty($image[0]['entity'])) {
        $image = $image[0]['entity'];
      }
      else {
        $image = asset_load($image[0]['target_id']);
      }

      $image->alt_title = $node->title;
      $image = $image->view('ticker_extended');

      if ($image) {
        $vars['image'] = l(render($image), $vars['url'], array('html' => TRUE));
      }
    }
  }

  $vars['more_link_text'] = 'Plus d\'infos';

  if (_culturebox_live_is_extrait_live($node)) {
    $vars['more_link_text'] .= " sur l'extrait";
  }
  elseif (!empty($node->field_live_main_category)) {
    $field_live_main_category = field_get_items('node', $node, 'field_live_main_category');

    $vars['more_link_text'] .=!empty($field_live_main_category[0]['tid']) && $field_live_main_category[0]['tid'] == 45629 ? " sur le concert" : '';
    $vars['more_link_text'] .=!empty($field_live_main_category[0]['tid']) && $field_live_main_category[0]['tid'] == 45689 ? " sur la pièce" : '';
    $vars['more_link_text'] .=!empty($field_live_main_category[0]['tid']) && $field_live_main_category[0]['tid'] == 45699 ? " sur le spectacle" : '';
    $vars['more_link_text'] .=!empty($field_live_main_category[0]['tid']) && $field_live_main_category[0]['tid'] == 45709 ? " sur le spectacle" : '';
    $vars['more_link_text'] .=!empty($field_live_main_category[1]['tid']) && $field_live_main_category[1]['tid'] == 45633 ? " sur l'opéra" : '';
  }
}

function culturebox_preprocess_views_view_unformatted__lives_list__actu_live_thematic(&$vars) {
  $view = $vars['view'];
  $vars['term'] = FALSE;

  if (!empty($view->args[0])) {
    $result = db_query_range("SELECT td.tid, td.name "
        . "FROM {taxonomy_term_data} td "
        . "INNER JOIN {field_data_field_live_thematic_ref} fltr ON td.tid = fltr.entity_id "
        . "WHERE fltr.field_live_thematic_ref_tid IN(:tid_ref)", 0, 1, array(':tid_ref' => explode('+', $view->args[0])))->fetch();

    if ($result) {
      $vars['term'] = $result;
    }
  }
}

function culturebox_preprocess_views_view_unformatted__culturebox_vote_model_views__list_block_small(&$vars) {
  if (!empty($vars['view']->args[0])) {
    $vars['view_more_link'] = l('Tous les témoignages', "taxonomy/term/{$vars['view']->args[0]}/culturebox-vote-model/list");
  }
}

function culturebox_date_combo($variables) {
  $element = $variables['element'];
  $field = field_info_field($element['#field_name']);
  $instance = field_info_instance($element['#entity_type'], $element['#field_name'], $element['#bundle']);

  // Group start/end items together in fieldset.
  $fieldset = array(
    '#title' => field_filter_xss(t($element['#title'])) . ' ' . ($element['#delta'] > 0 ? intval($element['#delta'] + 1) : ''),
    '#value' => '',
    '#description' => !empty($element['#fieldset_description']) ? $element['#fieldset_description'] : '',
    '#attributes' => array(),
    '#children' => $element['#children'],
  );
  // Add marker to required date fields.
  if ($element['#required'] && $element['#field_name'] != 'field_birth_date') {
    $fieldset['#title'] .= " " . theme('form_required_marker');
  }
  return theme('fieldset', array('element' => $fieldset));
}

function culturebox_textfield($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'text';

  if (!empty($element['#parents']) && $element['#parents'][0] == 'field_birth_date') {
    $element['#attributes']['placeholder'] = 'Date de naissance *';
  }

  element_set_attributes($element, array('id', 'name', 'value', 'size', 'maxlength'));
  _form_set_class($element, array('form-text'));

  $extra = '';
  if ($element['#autocomplete_path'] && drupal_valid_path($element['#autocomplete_path'])) {
    drupal_add_library('system', 'drupal.autocomplete');
    $element['#attributes']['class'][] = 'form-autocomplete';

    $attributes = array();
    $attributes['type'] = 'hidden';
    $attributes['id'] = $element['#attributes']['id'] . '-autocomplete';
    $attributes['value'] = url($element['#autocomplete_path'], array('absolute' => TRUE));
    $attributes['disabled'] = 'disabled';
    $attributes['class'][] = 'autocomplete';
    $extra = '<input' . drupal_attributes($attributes) . ' />';
  }

  $output = '<input' . drupal_attributes($element['#attributes']) . ' />';

  return $output . $extra;
}

function culturebox_preprocess_views_view__lives_list__minisite_live_all(&$vars) {
  if (!empty($vars['view']->args[0]) && ctype_digit($vars['view']->args[0])) {
    $vars['term_link'] = l(
      'Voir toutes les vidéos', "taxonomy/term/{$vars['view']->args[0]}/lives", array(
      'attributes' => array('class' => array('btn-01')),
      )
    );
  }
}
