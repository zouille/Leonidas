<?php

/**
 * Prevent to render all multivalue assets & use first instead.
 */
function _culturebox_get_first_diffusion_media_asset_view($vars, $default_view_mode = 'live_teaser', $field_name = 'field_fiche_emission_main_media') {
  $node = $vars['node'];
  $media = field_get_items('node', $node, $field_name);
  if ($media) {
    if (!empty($vars['content'][$field_name][0]['asset'])) {
      $raw_asset = array_shift($vars['content'][$field_name][0]['asset']);
      $view_mode = $raw_asset['#view_mode'];
    }
    $view_mode = empty($view_mode) ? $default_view_mode : $view_mode;

    $asset = !empty($media[0]['entity']) ? $media[0]['entity'] : asset_load($media[0]['target_id']);

    if ($asset) {
      $asset->alt_title = $node->title;
      if (!empty($asset) && is_object($asset)) {
        $asset_view = $asset->view($view_mode);
        return $asset_view;
      }
    }
  }
  return NULL;
}

/**
 * Get main category of node live.
 */
function _culturebox_get_node_diffusion_main_category($node, $first_deepest_category = FALSE, $blank = FALSE) {
  $field_main_category = field_get_items('node', $node, 'field_main_category');
  if ($field_main_category && !$first_deepest_category) {
    $thematic_id = end($field_main_category);
    $thematic = taxonomy_term_load($thematic_id['tid']);
    if ($thematic && $thematic->name != 'Généraliste') {
      $attributes = array();
      if ($blank) {
        $attributes['target'] = '_blank';
      }
      return culturebox_site_l($thematic->name, "taxonomy/term/{$thematic->tid}", array(
        'attributes' => $attributes,
      ));
    }
  }
  // Get deepest category if main category is empty.
  else {
    $tids = field_get_items('node', $node, 'field_categories');

    $sorted_categories = culturebox_site_get_tids_sorted_by_depth($tids);
    if (!empty($sorted_categories)) {
      foreach (array('depth_2', 'depth_1', 'depth_0') as $depth) {
        if (!empty($sorted_categories[$depth])) {
          $tid = array_shift($sorted_categories[$depth]);
          $term = taxonomy_term_load($tid);
          if ($term && $term->name != 'Généraliste') {
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
  }

  return '';
}

/**
 * Return live status.
 */
function _culturebox_diffusion_status(&$vars) {
  $status = field_get_items('node', $vars['node'], 'field_fe_status');
  $status = $status[0]['value'];
  $vars['label_class'] = '';
  $vars['live_status_label'] = '';

  if (!empty($status)) {
    switch ($status) {
      case CULTUREBOX_EMISSION_STATUS_DIFFUSION_PROCHAINEMENT:
      case CULTUREBOX_EMISSION_STATUS_DIFFUSION_AVANT_LE_DIRECT:
      case CULTUREBOX_EMISSION_STATUS_DIFFUSION_RETARD:
        $vars['live_status_label'] = 'Bientôt';
        break;

      case CULTUREBOX_EMISSION_STATUS_DIFFUSION_LAST_CHANCE:
        $vars['live_status_label'] = 'Dernière chance';
        break;

      case CULTUREBOX_EMISSION_STATUS_DIFFUSION_DIRECT:
        $vars['live_status_label'] = 'Direct';
        break;

      case CULTUREBOX_EMISSION_STATUS_DIFFUSION_REPLAY:
        $vars['live_status_label'] = 'A revoir';
        $vars['label_class'] = 'arevoir';
        break;
    }
  }
  $vars['status'] = $status;
}

/**
 * Get diffusion button play by status. Status types : bientôt, direct, replay.
 */
function _culturebox_preprocess_diffusion_button_play(&$vars, $status_type = NULL, $class = NULL, $is_home_emission = FALSE) {
  $live_status = theme(
    'diffusion_status', array(
      'node' => $vars['node'],
      'status_type' => $status_type,
      'class' => $class,
    )
  );
  if (!empty($live_status)) {
    if ($is_home_emission) {
      $vars['live_status'] = $live_status;
    }
    else {
      $vars['live_status'] = $live_status;
    }
  }
}

function culturebox_preprocess_node__fiche_emission_full(&$vars) {
  $node = $vars['node'];

  // Bloc oeuvres.
  _culturebox_get_bloc_livres($vars, $node, 'field_oeuvre');

  // Bloc écrivains.
  _culturebox_get_bloc_personnalites($vars, $node, 'field_personnalit_s');

  // Si la diffusion dispose d'un média principal, on l'affiche tout le temps.
  // On ne gère pas les données spécifiques aux diffusions.
  if (!empty($node->field_article_main_media)) {
    $media = _culturebox_get_article_main_media_asset_view($vars);

    if (!empty($media)) {
      $vars['media'] = drupal_render($media);
    }
  }

  if (empty($vars['media'])) {
    // Common preprocess for culturebox and culturebox_mobile themes.
    culturebox_emission_common_part_preprocess_diffusion_full($vars);

    // Status icon.
    _culturebox_preprocess_diffusion_button_play($vars);

    _culturebox_diffusion_status($vars);
  }

  // Share links.
  $vars['share_links'] = theme('share_links_ajax', array('node' => $vars['node']));
  $vars['inform_popup'] = theme('diffusion_inform_popup', $vars);

  $emission = culturebox_emission_get_term();

  if (!empty($emission->name)) {
    $vars['emission_title'] = $emission->name;
  }

  if (!empty($vars['content']['field_body'])) {
    $vars['text_long'] = _filter_htmlcorrector(render($vars['content']['field_body']));
    $vars['text_short'] = _filter_htmlcorrector(text_summary($vars['text_long'], NULL, 1000));
  }

  // On ajoute des microdatas schema.org.
  if ($vars['video_id'] && ($vars['status'] == CULTUREBOX_EMISSION_STATUS_DIFFUSION_REPLAY || $vars['status'] == CULTUREBOX_EMISSION_STATUS_DIFFUSION_DIRECT)) {
    $video_id = $vars['video_id'];

    // URL du flux vidéo embed (pour Twitter).
    $embed_video_url = _culturebox_site_get_embed_video_url($video_id, $node);

    // URL du flux vidéo source (pour Google).
    if ($vars['status'] == CULTUREBOX_EMISSION_STATUS_DIFFUSION_REPLAY) {
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
            'content' => 660
          ),
        );
        drupal_add_html_head($card, 'twitter_card_player_width');
        
        $card = array(
          '#tag' => 'meta',
          '#attributes' => array(
            'name' => 'twitter:player:height',
            'content' => 370
          ),
        );
        drupal_add_html_head($card, 'twitter_card_player_height');
    }

    if (!empty($video_url)) {
      $microdata_video_object[] = '<meta itemprop="name" content="' . $node->title . '" />';
      $microdata_video_object[] = '<meta itemprop="contentURL" content="' . $video_url . '" />';
      $microdata_video_object[] = '<meta itemprop="url" content="' . url("node/{$node->nid}", array('absolute' => TRUE, 'base_url' => _culturebox_pagedevie_get_urlfo())) . '" />';

      if (!empty($vars['replay_end_date_iso8601'])) {
        $microdata_video_object[] = '<meta itemprop="expires" content="' . $vars['replay_end_date_iso8601'] . '" />';
      }

      if (!empty($vars['field_fiche_emission_main_media'][0]['entity']->field_asset_image[LANGUAGE_NONE][0]['uri'])) {
        // On génère des thumbnails de bonne qualité, mais pas trop gros : https://support.google.com/webmasters/answer/156442?hl=en.
        $image_url = image_style_url('tvc_mea_1280_720', $vars['field_fiche_emission_main_media'][0]['entity']->field_asset_image[LANGUAGE_NONE][0]['uri']);

        $microdata_video_object[] = '<meta itemprop="thumbnailUrl" content="' . $image_url . '" />';

        if (file_exists($image_url)) {
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
      }

      $vars['microdata_video_object'] = implode(PHP_EOL, $microdata_video_object);
    }
  }

  $vars['date'] = culturebox_emission_date($vars);
  $vars['duree'] = culturebox_emission_duree($vars);
}

function culturebox_preprocess_node__oeuvre_livre_personnalite_emission(&$vars) {
  $node = $vars['node'];

  if (!empty($node->field_illustration[LANGUAGE_NONE][0]['entity']->field_asset_image[LANGUAGE_NONE][0]['uri'])) {
    $vars['microdata_image'] = '<meta itemprop="image" content="' . file_create_url($node->field_illustration[LANGUAGE_NONE][0]['entity']->field_asset_image[LANGUAGE_NONE][0]['uri']) . '" />';
  }

  if (!empty($node->field_livre_annee_edition[LANGUAGE_NONE][0]['value'])) {
    $vars['annee_edition'] = format_date(strtotime($node->field_livre_annee_edition[LANGUAGE_NONE][0]['value']), 'custom', 'Y');
  }

  if (!empty($node->field_editeur[LANGUAGE_NONE][0]['value'])) {
    $vars['editeur'] = trim($node->field_editeur[LANGUAGE_NONE][0]['value']);
  }

  // Auteurs.
  if (!empty($node->field_personnalit_s[LANGUAGE_NONE])) {
    $auteurs = array();
    $auteurs_nids = array();

    foreach ($node->field_personnalit_s[LANGUAGE_NONE] as $personnalite) {
      if (!empty($personnalite['target_id'])) {
        $auteurs_nids[] = $personnalite['target_id'];
      }
    }

    if (!empty($auteurs_nids)) {
      $auteurs_nodes = taxonomy_term_load_multiple($auteurs_nids);

      foreach ($auteurs_nodes as $personnalite) {
        if (!empty($personnalite->name)) {
          $auteurs[] = $personnalite->name;
        }
      }

      if (!empty($auteurs)) {
        $vars['auteurs'] = implode(', ', $auteurs);
      }
    }
  }

  // Catégories (on récupère uniquement la sous-thématique).
  if (!empty($node->field_main_category[LANGUAGE_NONE][1])) {
    if (empty($node->field_main_category[LANGUAGE_NONE][1]['taxonomy_term'])) {
      $node->field_main_category[LANGUAGE_NONE][1]['taxonomy_term'] = taxonomy_term_load($node->field_main_category[LANGUAGE_NONE][1]['tid']);
    }

    $vars['categories'] = culturebox_site_l($node->field_main_category[LANGUAGE_NONE][1]['taxonomy_term']->name, 'taxonomy/term/' . $node->field_main_category[LANGUAGE_NONE][1]['taxonomy_term']->tid);
  }

  if (!empty($node->field_body)) {
    $vars['description'] = trim(_filter_htmlcorrector(truncate_utf8($node->field_body[LANGUAGE_NONE][0]['value'], 170) . '...'));
  }
}

function culturebox_preprocess_node__oeuvre_livre_full(&$vars) {
  culturebox_preprocess_node__oeuvre_livre_personnalite_emission($vars);
}

function culturebox_preprocess_node__fiche_emission_home_from_editor_big(&$vars) {
  $node = $vars['node'];

  // Status.
  $status = field_get_items('node', $node, 'field_fe_status');
  $status = $status[0]['value'];

  $bientot_statuses = array(
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_PROCHAINEMENT,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_AVANT_LE_DIRECT,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_RETARD
  );

  // Image section.
  $live_media = '';
  $media_view = _culturebox_get_first_diffusion_media_asset_view($vars, 'live_home_top');

  if (!empty($media_view)) {
    $live_media = drupal_render($media_view);
  }
  $vars['media'] = culturebox_site_l(
    $live_media, 'node/' . $vars['nid'], array(
      'html' => TRUE,
    )
  );

  if (!empty($vars['field_fe_live_start_time'])) {
    $field_data = field_get_items('node', $vars['node'], 'field_fe_live_start_time');
    $field_live_start_time = array_shift($field_data);
    $live_start_time = $field_live_start_time['value'];
  }

  // Status icon.
  _culturebox_preprocess_diffusion_button_play($vars);

  // Line section.
  // 1.1 Thematic associations.
  $vars['main_thematic'] = _culturebox_get_node_diffusion_main_category($node);

  if (!empty($live_start_time)) {
    // Live start date.
    $live_start_timestamp = strtotime($live_start_time);
    $date = format_date($live_start_timestamp, 'custom', 'l j F Y');
    $time = format_date($live_start_timestamp, 'custom', 'H\hi');

    $vars['live_start_date'] = sprintf('<span>%s</span> %s', $date, $time);
  }

  // Button more. Can have several states: read more | view video.
  $before = in_array($status, array_merge($bientot_statuses, array(CULTUREBOX_EMISSION_STATUS_DIFFUSION_ENTRE_LE_DIRECT_ET_LE_REPLAY)));
  $button_text = $before ? 'En savoir plus' : 'Voir la vidéo';
  $button_img = $before ? theme('image', array('path' => CULTUREBOX_THEME_PATH . '/images/ico-more.png')) :
    theme('image', array(
      'path' => CULTUREBOX_THEME_PATH . '/images/ico-play.png',
      'attributes' => array('class' => array('ico-play')),
    ));

  culturebox_emission_preprocess_diffusion_status($vars);

  $vars['more_button'] = culturebox_site_l(
    '<span class="picto"></span>' . $button_text, "node/{$node->nid}", array(
      'html' => TRUE,
      'attributes' => array('class' => (!empty($vars['direct']) || !empty($vars['replay'])) ? array('view-link-mode') : array()),
    )
  );

  // Share links.
  $vars['share_links'] = theme('live_share_popup', array('node' => $node));
  $vars['title_link'] = culturebox_site_l($vars['node']->title, "node/${vars['nid']}");
  $vars['inform_popup'] = theme('diffusion_inform_popup', $vars);

  if (!empty($vars['direct']) || !empty($vars['replay'])) {
    $vars['reagir'] = culturebox_site_l('Réagir', "node/$node->nid", array(
      'attributes' => array('class' => 'imitation-link'),
    ));
  }
  $get_emission_tid = field_get_items('node', $vars['node'], 'field_fe_emission_tid');
  if (!empty($get_emission_tid)) {
    $tid = $get_emission_tid[0]['tid'];
  }
  $emission = taxonomy_term_load($tid);
  if (!empty($emission)) {
    $vars['name_emission'] = $emission->name;
  }
}

/**
 * Preprocess variables for node--fiche_emission--rss.tpl.php.
 *
 * @see templates/node/article/node--fiche_emission-rss.tpl.php
 */
function culturebox_preprocess_node__fiche_emission_rss(&$vars) {
  _culturebox_preprocess_node_rss_view_mode($vars);
}

/**
 * Preprocess variables for node--fiche_emission-home-from-editor-medium.tpl.php.
 *
 * @see templates/node/live/node--fiche_emission-home-from-editor-medium.tpl.php
 */
function culturebox_preprocess_node__fiche_emission_home_from_editor_medium(&$vars) {
  $node = $vars['node'];

  // Image section.
  $live_media = '';
  $media_view = _culturebox_get_first_diffusion_media_asset_view($vars, 'live_la_section');

  if (!empty($media_view)) {
    $live_media = drupal_render($media_view);
  }
  $vars['media'] = culturebox_site_l(
    $live_media, 'node/' . $vars['nid'], array(
      'html' => TRUE,
    )
  );

  _culturebox_preprocess_diffusion_button_play($vars);

  // Title.
  $vars['title'] = culturebox_site_l(
    $node->title, "node/{$node->nid}"
  );
    
  // lien voir plus
  if (empty($vars['fiche_person']) || !$vars['fiche_person']) {
    $vars['know_more'] = culturebox_site_l('En savoir plus', "node/{$node->nid}", array('attributes' => array('class' => 'know-more')));
  }
  
  $vars['date'] = culturebox_emission_date($vars);
  $vars['duree'] = culturebox_emission_duree($vars);
  $get_emission_tid = field_get_items('node', $node, 'field_fe_emission_tid');
  
  if (!empty($get_emission_tid[0]['tid'])) {
    $emission = taxonomy_term_load($get_emission_tid[0]['tid']);

    if (!empty($emission->name)) {
      if (!empty($vars['node']->home_from_editor_medium_custom_display)) {
        $vars['name_emission'] = l($emission->name, "taxonomy/term/{$emission->tid}");
      }
      else {
        $vars['name_emission'] = $emission->name;
      } 
    }

    $get_channel = field_get_items('taxonomy_term', $emission, 'field_emission_channel');
    $vars['channel'] = $get_channel[0]['value'];
  }
}

function culturebox_preprocess_node__live_home_from_editor_medium_emission(&$vars) {
  $node = $vars['node'];

  $vars['know_more'] = culturebox_site_l('En savoir plus', "node/{$node->nid}", array('attributes' => array('class' => 'know-more')));
  $vars['status'] = in_array($vars['field_live_status'][LANGUAGE_NONE][0]['value'], array(
    'live',
    'replay',
    'last_chance'
  ));
}

function culturebox_preprocess_node__fiche_emission_teaser(&$vars) {
  $node = $vars['node'];
  if ($thematic = _culturebox_get_node_diffusion_main_category($node)) {
    $vars['category'] = $thematic;
  }

  _culturebox_preprocess_diffusion_button_play($vars, NULL, 'play-medium');

  if (!empty($node->field_fiche_emission_main_media[LANGUAGE_NONE][0]['target_id'])) {
    $target_id = $node->field_fiche_emission_main_media[LANGUAGE_NONE][0]['target_id'];
    $image = asset_load($target_id);
    if (!empty($image->field_asset_image[LANGUAGE_NONE][0]['uri'])) {
      $media_asset_view = theme('image_style', array(
        'style_name' => (arg(0) == 'node' ? 'live_teaser' : 'article_view_full_redaction_article_image'),
        'path' => $image->field_asset_image[LANGUAGE_NONE][0]['uri'],
        'title' => $node->title,
        'alt' => $node->title,
        'attributes' => array('class' => array('visuel-sommaire')),
      ));
    }

    if (!empty($media_asset_view)) {
      $vars['image'] = culturebox_site_l(
        $media_asset_view, 'node/' . $vars['nid'], array(
          'html' => TRUE,
        )
      );
    }
  }

  $rord = field_get_items('node', $node, 'field_fe_direct_replay_switch');
  if ($rord) {
    $rord = $rord[0]['value'];
  }
  else {
    $rord = 0;
  }
  if ($rord == 2) {
    $start_time = field_get_items('node', $node, 'field_fe_replay_start_time');
  }
  else {
    $start_time = field_get_items('node', $node, 'field_fe_live_start_time');
  }

  if ($start_time) {
    $vars['year'] = format_date(strtotime($start_time[0]['value']), 'custom', 'Y');
  }
  elseif ($start_time = field_get_items('node', $node, 'field_fe_broadcast_date')) {
    $vars['year'] = format_date(strtotime($start_time[0]['value']), 'custom', 'Y');
  }
  else {
    $vars['year'] = format_date(time(), 'custom', 'Y');
  }
  $get_emission_tid = field_get_items('node', $vars['node'], 'field_fe_emission_tid');
  $tid = $get_emission_tid[0]['tid'];
  $emission = taxonomy_term_load($tid);
  if (!empty($emission->name)) {
    $vars['name_emission'] = $emission->name;
  }
  $vars['date'] = culturebox_emission_date($vars);
  $vars['duree'] = culturebox_emission_duree($vars);
}

function culturebox_preprocess_node__extrait_description(&$vars) {
  $node = $vars['node'];

  if (!empty($node->field_extrait_emission_tid[LANGUAGE_NONE][0]['tid'])) {
    $emission_name = db_query_range("SELECT name FROM {taxonomy_term_data} WHERE tid = :tid", 0, 1, array(':tid' => $node->field_extrait_emission_tid[LANGUAGE_NONE][0]['tid']))->fetchField();

    $vars['category'] = culturebox_site_l($emission_name, "taxonomy/term/{$node->field_extrait_emission_tid[LANGUAGE_NONE][0]['tid']}");
  }

  if (!empty($node->field_extrait_illustration[LANGUAGE_NONE][0]['target_id'])) {
    $target_id = $node->field_extrait_illustration[LANGUAGE_NONE][0]['target_id'];
    $image = asset_load($target_id);
    if (!empty($image->field_asset_image[LANGUAGE_NONE][0]['uri'])) {
      $media_asset_view = theme('image_style', array(
        'style_name' => 'article_view_full_redaction_article_image',
        'path' => $image->field_asset_image[LANGUAGE_NONE][0]['uri'],
        'title' => $node->title,
        'alt' => $node->title,
        'attributes' => array('class' => array('visuel-sommaire')),
      ));
    }

    if (!empty($media_asset_view)) {
      $vars['image'] = culturebox_site_l(
        $media_asset_view, 'node/' . $vars['nid'], array(
          'html' => TRUE,
        )
      );
    }
  }
}

function culturebox_preprocess_node__live_teaser_emission(&$vars) {
  $node = $vars['node'];

  $style_name = (arg(0) == 'node' ? 'live_teaser' : 'article_view_full_redaction_article_image');

  if (arg(0) == 'taxonomy' && arg(1) == 'term') {
    $term = taxonomy_term_load(arg(2));
    if (!empty($term) && substr($term->name, 0, strlen('Les lives')) == 'Les lives') {
      $style_name = 'live_teaser';
    }
    if (!empty($term) && $term->vocabulary_machine_name == 'event' || $term->vocabulary_machine_name == 'mini_site') {
      $style_name = 'live_teaser';
    }
  }

  $target_id = $node->field_live_media[LANGUAGE_NONE][0]['target_id'];
  $image = asset_load($target_id);
  $media_asset_view = theme('image_style', array(
    'style_name' => $style_name,
    'path' => $image->field_asset_image[LANGUAGE_NONE][0]['uri'],
    'title' => $node->title,
    'alt' => $node->title,
    'attributes' => array('class' => array('visuel-sommaire')),
  ));

  if (!empty($media_asset_view)) {
    $vars['image'] = culturebox_site_l(
      $media_asset_view, 'node/' . $vars['nid'], array(
        'html' => TRUE,
      )
    );
  }
}

function culturebox_preprocess_node__fiche_emission_description(&$vars) {
  $node = $vars['node'];
  _culturebox_preprocess_diffusion_button_play($vars, NULL, 'play-medium');
  $media_view = _culturebox_get_first_diffusion_media_asset_view($vars);

  if (!empty($media_view)) {
    $vars['image'] = culturebox_site_l(
      drupal_render($media_view), 'node/' . $vars['nid'], array(
        'html' => TRUE,
      )
    );
  }
}

function culturebox_preprocess_node__fiche_emission_home_from_editor_small_2(&$vars) {
  $node = $vars['node'];
  //$vars['category'] = _culturebox_get_node_diffusion_main_category($node);
  $status = array(
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_ENTRE_LE_DIRECT_ET_LE_REPLAY,
    //CULTUREBOX_EMISSION_STATUS_DIFFUSION_REPLAY,
    //CULTUREBOX_EMISSION_STATUS_DIFFUSION_LAST_CHANCE,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_APRES_LE_REPLAY,
  );
  if (!in_array($node->field_fe_status[LANGUAGE_NONE][0]['value'], $status)) {
    _culturebox_preprocess_diffusion_button_play($vars, NULL, 'play-medium');
  }

  $parent = taxonomy_term_load($node->field_fe_emission_tid[LANGUAGE_NONE][0]['tid']);
  $vars['emssion_parent'] = culturebox_site_l($parent->name, 'taxonomy/term/' . $parent->tid);

  $live_media = '';
  $media_view = _culturebox_get_first_diffusion_media_asset_view($vars);

  if (!empty($media_view)) {
    $live_media = drupal_render($media_view);
  }
  $vars['rendered_article_media'] = culturebox_site_l(
    $live_media, 'node/' . $vars['nid'], array(
      'html' => TRUE,
    )
  );

  $get_channel = field_get_items('taxonomy_term', $parent, 'field_emission_channel');
  $vars['channel'] = $get_channel[0]['value'];

  $vars['title_link'] = culturebox_site_l($vars['node']->title, 'node/' . $vars['nid']);
  $vars['date'] = culturebox_emission_date($vars);
  $vars['duree'] = culturebox_emission_duree($vars);
}

function culturebox_preprocess_node__fiche_emission_slider(&$vars) {
  $node = $vars['node'];
  if ($thematic = _culturebox_get_node_diffusion_main_category($node)) {
    $vars['category'] = $thematic;
  }

  _culturebox_preprocess_diffusion_button_play($vars, NULL, 'play-medium');
  $media_view = _culturebox_get_first_diffusion_media_asset_view($vars);

  if (!empty($media_view)) {
    $vars['image'] = culturebox_site_l(
      drupal_render($media_view), 'node/' . $vars['nid'], array(
        'html' => TRUE,
      )
    );
  }
}

function culturebox_preprocess_node__article_light_dans_actu(&$vars) {
  $node = $vars['node'];

  $emission = field_get_items('node', $node, 'field_fe_emission_tid');

  if (!empty($emission[0]['taxonomy_term'])) {
    $emission = $emission[0]['taxonomy_term'];
  }
  elseif (!empty($emission[0]['tid'])) {
    $emission = taxonomy_term_load($emission[0]['tid']);
  }

  if (!empty($emission->vocabulary_machine_name) && $emission->vocabulary_machine_name == CULTUREBOX_EMISSION_EMISSION_VOCABULARY_NAME) {
    $vars['category'] = $emission->name;
  }

  // Image section.
  $image = '';
  $media_view = _culturebox_get_article_main_media_asset_view($vars, 'live_most_viewed', TRUE);

  if (empty($media_view)) {
    $media_view = _culturebox_get_article_media_asset_view($vars, 'live_most_viewed');
  }

  if (!empty($media_view)) {
    $image = drupal_render($media_view);
  }
  else {
    // On récupère le type d'article light.
    $article_light_type = field_get_items('node', $node, 'field_content_type');

    if (!empty($article_light_type)) {
      $article_light_type = $article_light_type[0]['value'];
    }
    else {
      $article_light_type = 'article';
    }

    $default_image_path = file_default_scheme() . '://' . "mls_{$article_light_type}.png";

    if (!file_exists($default_image_path)) {
      // Si l'image est manquante dans public://, on la copie depuis le module culturebox_site.
      $orig_image_path = "sites/all/modules/custom/culturebox_site/img/mls_{$article_light_type}.png";

      if (file_exists($orig_image_path)) {
        file_unmanaged_copy($orig_image_path, NULL, FILE_EXISTS_REPLACE);
      }
    }

    if (file_exists($default_image_path)) {
      $image = theme('image_style', array('style_name' => 'live_most_viewed', 'path' => $default_image_path));
    }
  }

  if (!empty($image)) {
    $vars['image'] = culturebox_site_l(
      $image, 'node/' . $vars['nid'], array(
        'html' => TRUE,
      )
    );
  }
}

function culturebox_preprocess_node__article_light_full(&$vars) {
  global $user;

  // Signature.
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

      if ($author->uid != 1) {
        $uids[$author->uid] = $author->name;
      }
    }
  }
  elseif ($vars['node']->uid != 1) {
    $authors[$vars['node']->uid] = user_load($vars['node']->uid);
  }

  if (!empty($signature_free)) {
    if ($uid = array_search($signature_free[0]['value'], $uids)) {
      $vars['signature'] = user_view(user_load($uid), 'signature');
    }
    else {
      $vars['signature_free'] = $signature_free[0]['value'];
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
        $vars['signature'] = user_view($author, 'signature');
      }
    }
  }

  // Published date.
  $published_date = field_get_items('node', $vars['node'], 'field_published_date');
  if (!$published_date) {
    $published_date = $vars['created'];
  }
  else {
    $published_date = $published_date[0]['value'];
  }

  $vars['published'] = format_date($published_date, 'custom', 'd/m/Y', NULL, 'fr');

  culturebox_process_node_thematics($vars, 'field_categories');

  // Share links.
  $vars['share_links'] = theme('share_links_ajax', array('node' => $vars['node']));

  if (!($media = _culturebox_get_article_main_media_asset_view($vars))) {
    $media = _culturebox_get_article_media_asset_view($vars);
  }

  if (!empty($media)) {
    $tmp_media = reset($media['asset']);

    if ($tmp_media["#bundle"] == 'image') {
      $vars['img_full_seo'] = file_create_url($tmp_media['field_asset_image']['#items'][0]['uri']);

      $vars['microdata_image'] = '<meta itemprop="image" content="' . $vars['img_full_seo'] . '" />';
    }
    $vars['media'] = drupal_render($media);
  }
}

function culturebox_preprocess_node__article_light_home_from_editor_medium(&$vars) {
  $node = $vars['node'];
  // Category tag.
  $category = _culturebox_get_node_main_category($node);
  $vars['tags'] = $category;

  // Image section.
  $image = '';
  $media_view = _culturebox_get_article_main_media_asset_view($vars, 'live_la_section', TRUE);

  if (empty($media_view)) {
    $media_view = _culturebox_get_article_media_asset_view($vars, 'live_la_section');
  }

  if (!empty($media_view)) {
    $image = drupal_render($media_view);
  }
  else {
    // On récupère le type d'article light.
    $article_light_type = field_get_items('node', $node, 'field_content_type');

    if (!empty($article_light_type)) {
      $article_light_type = $article_light_type[0]['value'];
    }
    else {
      $article_light_type = 'article';
    }

    $default_image_path = file_default_scheme() . '://' . "mls_{$article_light_type}.png";

    if (!file_exists($default_image_path)) {
      // Si l'image est manquante dans public://, on la copie depuis le module culturebox_site.
      $orig_image_path = "sites/all/modules/custom/culturebox_site/img/mls_{$article_light_type}.png";

      if (file_exists($orig_image_path)) {
        file_unmanaged_copy($orig_image_path, NULL, FILE_EXISTS_REPLACE);
      }
    }

    if (file_exists($default_image_path)) {
      $image = theme('image_style', array('style_name' => 'live_la_section_320x160', 'path' => $default_image_path));
    }
  }

  if (!empty($image)) {
    $vars['media'] = culturebox_site_l(
      $image, 'node/' . $vars['nid'], array(
        'html' => TRUE,
      )
    );
  }

  // Title.
  $vars['title'] = culturebox_site_l(
    $node->title, "node/{$node->nid}"
  );
  // lien voir plus
  if (empty($vars['fiche_person']) || !$vars['fiche_person']) {
    $vars['know_more'] = culturebox_site_l('En savoir plus', "node/{$node->nid}", array('attributes' => array('class' => 'know-more')));
  }
}

function culturebox_preprocess_node__article_light_teaser(&$vars) {
  $node = $vars['node'];
  if ($thematic = _culturebox_get_node_main_category($node)) {
    $vars['category'] = $thematic;
  }

  // Image section.
  if (!empty($node->field_article_main_media[LANGUAGE_NONE][0]['target_id'])) {
    $img = asset_load($node->field_article_main_media[LANGUAGE_NONE][0]['target_id']);

    if ($img->type == 'image') {
      $image_asset = $img;
    }
  }

  if (empty($image_asset)) {
    if (!empty($node->field_article_media[LANGUAGE_NONE][0]['target_id'])) {
      $image_asset = asset_load($node->field_article_media[LANGUAGE_NONE][0]['target_id']);
    }
  }

  if (!empty($image_asset->field_asset_image[LANGUAGE_NONE][0]['uri'])) {
    $media_asset_view = theme('image_style', array(
      'style_name' => (arg(0) == 'node' ? 'live_teaser' : 'article_view_full_redaction_article_image'),
      'path' => $image_asset->field_asset_image[LANGUAGE_NONE][0]['uri'],
      'title' => $node->title,
      'alt' => $node->title,
      'attributes' => array('class' => array('visuel-sommaire')),
    ));
  }
  else {
    // On récupère le type d'article light.
    $article_light_type = field_get_items('node', $node, 'field_content_type');

    if (!empty($article_light_type)) {
      $article_light_type = $article_light_type[0]['value'];
    }
    else {
      $article_light_type = 'article';
    }

    $default_image_path = file_default_scheme() . '://' . "mls_{$article_light_type}.png";

    if (!file_exists($default_image_path)) {
      // Si l'image est manquante dans public://, on la copie depuis le module culturebox_site.
      $orig_image_path = "sites/all/modules/custom/culturebox_site/img/mls_{$article_light_type}.png";

      if (file_exists($orig_image_path)) {
        file_unmanaged_copy($orig_image_path, NULL, FILE_EXISTS_REPLACE);
      }
    }

    if (file_exists($default_image_path)) {
      $media_asset_view = theme('image_style', array(
        'style_name' => (arg(0) == 'node' ? 'live_teaser' : 'article_view_full_redaction_article_image'),
        'path' => $default_image_path,
        'title' => $node->title,
        'alt' => $node->title,
        'attributes' => array('class' => array('visuel-sommaire')),
      ));
    }
  }

  if (!empty($media_asset_view)) {
    if (!empty($media_asset_view)) {
      $vars['image'] = culturebox_site_l(
        $media_asset_view, 'node/' . $vars['nid'], array(
          'html' => TRUE,
        )
      );
    }
  }
}

function culturebox_preprocess_node__article_light_home_from_editor_big(&$vars) {
  $node = $vars['node'];

  // Image section.
  $image = '';
  $media_view = _culturebox_get_article_main_media_asset_view($vars, 'live_home_top', TRUE);

  if (empty($media_view)) {
    $media_view = _culturebox_get_article_media_asset_view($vars, 'live_home_top');
  }

  if (!empty($media_view)) {
    $image = drupal_render($media_view);
  }
  else {
    // On récupère le type d'article light.
    $article_light_type = field_get_items('node', $node, 'field_content_type');

    if (!empty($article_light_type)) {
      $article_light_type = $article_light_type[0]['value'];
    }
    else {
      $article_light_type = 'article';
    }

    $default_image_path = file_default_scheme() . '://' . "mls_{$article_light_type}.png";

    if (!file_exists($default_image_path)) {
      // Si l'image est manquante dans public://, on la copie depuis le module culturebox_site.
      $orig_image_path = "sites/all/modules/custom/culturebox_site/img/mls_{$article_light_type}.png";

      if (file_exists($orig_image_path)) {
        file_unmanaged_copy($orig_image_path, NULL, FILE_EXISTS_REPLACE);
      }
    }

    if (file_exists($default_image_path)) {
      $image = theme('image_style', array('style_name' => 'live_home_top_1000x500', 'path' => $default_image_path));
    }
  }

  if (!empty($image)) {
    $vars['media'] = culturebox_site_l(
      $image, 'node/' . $vars['nid'], array(
        'html' => TRUE,
      )
    );
  }

  if (!empty($vars['field_published_date'])) {
    $field_data = field_get_items('node', $vars['node'], 'field_published_date');
    $field_live_start_time = array_shift($field_data);
    $live_start_time = $field_live_start_time['value'];

    if (!empty($live_start_time)) {
      $date = format_date($live_start_time, 'custom', 'd/m/Y');
      $vars['live_start_date'] = sprintf('<span>%s</span>', $date);
    }
  }


  // Line section.
  // 1.1 Thematic associations.
  $vars['main_thematic'] = _culturebox_get_node_main_category($node);

  $button_img = theme('image', array('path' => CULTUREBOX_THEME_PATH . '/images/ico-more.png'));

  $vars['more_button'] = culturebox_site_l(
    '<span class="picto"></span>En savoir plus', "node/{$node->nid}", array(
      'html' => TRUE,
    )
  );

  // Share links.
  $vars['share_links'] = theme('live_share_popup', array('node' => $node));
  $vars['title_link'] = culturebox_site_l($vars['node']->title, "node/${vars['nid']}");

  if (!empty($vars['direct']) || !empty($vars['replay'])) {
    $vars['reagir'] = culturebox_site_l('Réagir', "node/$node->nid", array(
      'attributes' => array('class' => 'imitation-link'),
    ));
  }
}

function culturebox_preprocess_node__fiche_emission_dans_actu(&$vars) {
  $node = $vars['node'];

  $emission = field_get_items('node', $node, 'field_fe_emission_tid');

  if (!empty($emission[0]['taxonomy_term'])) {
    $emission = $emission[0]['taxonomy_term'];
  }
  elseif (!empty($emission[0]['tid'])) {
    $emission = taxonomy_term_load($emission[0]['tid']);
  }

  if (!empty($emission->vocabulary_machine_name) && $emission->vocabulary_machine_name == CULTUREBOX_EMISSION_EMISSION_VOCABULARY_NAME) {
    $vars['category'] = $emission->name;
    $vars['presentateur'] = $emission->field_fe_presentateur[LANGUAGE_NONE][0]['value'];
    $vars['section'] = $emission->field_emission_section[LANGUAGE_NONE][0]['value'];
  }

  $media = field_get_items('node', $node, 'field_fiche_emission_main_media');
  if ($media) {
    $asset = !empty($media[0]['entity']) ? $media[0]['entity'] : asset_load($media[0]['target_id']);
    $asset->alt_title = $node->title;
    if (!empty($asset) && is_object($asset)) {
      $media_view = theme('image_style', array(
        'style_name' => 'live_most_viewed',
        'path' => $asset->field_asset_image[LANGUAGE_NONE][0]['uri'],
        'title' => $node->title,
        'alt' => $node->title,
        'attributes' => array('class' => array('')),
      ));
    }
  }
  _culturebox_preprocess_diffusion_button_play($vars, NULL, 'play-medium');
//   $media_view = _culturebox_get_first_diffusion_media_asset_view($vars);

  if (!empty($media_view)) {
    $vars['image'] = culturebox_site_l(
      $media_view, 'node/' . $vars['nid'], array(
        'html' => TRUE,
      )
    );
  }
}

function culturebox_preprocess_views_view__diffusions_list__diffusion_nodequeue_list(&$vars) {
  // Format "Voir toutes les émissions" link.
  $term = menu_get_object('taxonomy_term', 2);
  $emission = FALSE;

  if (!empty($term->vocabulary_machine_name) && $term->vocabulary_machine_name == CULTUREBOX_EMISSION_EMISSION_VOCABULARY_NAME) {
    $emission = TRUE;
  }
  else {
    $node = menu_get_object();

    if (!empty($node->field_fe_emission_tid[LANGUAGE_NONE][0]['tid'])) {
      $term = taxonomy_term_load($node->field_fe_emission_tid[LANGUAGE_NONE][0]['tid']);
      $emission = TRUE;
    }
  }

  if ($emission) {
    $vars['header_text'] = 'Toutes les émissions';

    if (!empty($term->field_emission_texts_b1_header[LANGUAGE_NONE][0]['value'])) {
      $vars['header_text'] = $term->field_emission_texts_b1_header[LANGUAGE_NONE][0]['value'];
    }

    $footer_text = 'Voir toutes les émissions';

    if (!empty($term->field_emission_texts_b1_footer[LANGUAGE_NONE][0]['value'])) {
      $footer_text = $term->field_emission_texts_b1_footer[LANGUAGE_NONE][0]['value'];
    }

    $vars['footer'] = culturebox_site_l($footer_text, culturebox_emission_link_to_list($term));
  }
}

function culturebox_preprocess_views_view__diffusions_list__emission_la_une_bottom(&$vars) {
  // Format "Voir toutes les émissions" link.
  $term = menu_get_object('taxonomy_term', 2);
  $emission = FALSE;

  if (!empty($term->vocabulary_machine_name) && $term->vocabulary_machine_name == CULTUREBOX_EMISSION_EMISSION_VOCABULARY_NAME) {
    $emission = TRUE;
  }
  else {
    $node = menu_get_object();

    if (!empty($node->field_fe_emission_tid[LANGUAGE_NONE][0]['tid'])) {
      $term = taxonomy_term_load($node->field_fe_emission_tid[LANGUAGE_NONE][0]['tid']);
      $emission = TRUE;
    }
  }

  if ($emission) {
    $vars['header_text'] = 'Toutes les émissions';

    if (!empty($term->field_emission_texts_b1_header[LANGUAGE_NONE][0]['value'])) {
      $vars['header_text'] = $term->field_emission_texts_b1_header[LANGUAGE_NONE][0]['value'];
    }

    $footer_text = 'Voir toutes les émissions';

    if (!empty($term->field_emission_texts_b1_footer[LANGUAGE_NONE][0]['value'])) {
      $footer_text = $term->field_emission_texts_b1_footer[LANGUAGE_NONE][0]['value'];
    }

    $vars['footer'] = culturebox_site_l($footer_text, culturebox_emission_link_to_list($term));
  }
}

function culturebox_preprocess_node__extrait_evenement(&$vars) {
  $node = $vars['node'];

  $media_view = _culturebox_get_first_diffusion_media_asset_view($vars);

  if (!empty($media_view)) {
    $vars['image'] = culturebox_site_l(
      drupal_render($media_view), 'node/' . $vars['nid'], array(
        'html' => TRUE,
      )
    );
  }
}

function culturebox_preprocess_node__extrait_teaser(&$vars) {
  $node = $vars['node'];

  if ($vars['field_extrait_video_bonus'] && $vars['field_extrait_video_bonus'][LANGUAGE_NONE][0]['value'] == 1) {
    $vars['is_bonus'] = TRUE;
  }

  $media = field_get_items('node', $node, 'field_extrait_illustration');
  if ($media) {

    $asset = !empty($media[0]['entity']) ? $media[0]['entity'] : asset_load($media[0]['target_id']);
    $asset->alt_title = $node->title;
    if (!empty($asset) && is_object($asset)) {
      $media_asset_view = theme('image_style', array(
        'style_name' => 'sommaire_diff_220x110',
        'path' => $asset->field_asset_image[LANGUAGE_NONE][0]['uri'],
        'title' => $node->title,
        'alt' => $node->title,
        'attributes' => array('class' => array('visuel-sommaire')),
      ));
    }
  }

  if (!empty($media_asset_view)) {
    // si on a une vidéo au bout c'est a dire qu'on a vraiment un extrait et non un élément de sommaire simple
    // on ajoute un lien sur l'image pour aller à l'extrait
    if (!empty($node->field_extrait_video) || !empty($node->field_fe_id_pgep) || (!empty($node->field_extrait_diffusion[LANGUAGE_NONE][0]['target_id']) && current_path() != 'node/' . $node->field_extrait_diffusion[LANGUAGE_NONE][0]['target_id'])) {
      $vars['image'] = culturebox_site_l(
        $media_asset_view, 'node/' . $vars['nid'], array(
          'html' => TRUE,
          'attributes' => array('class' => array("lien-visuel-sommaire")),
        )
      );
      if (!empty($node->field_extrait_video) || !empty($node->field_fe_id_pgep)) {
        $vars['have_video'] = TRUE;
      }
      else {
        $vars['have_video'] = FALSE;
      }
      $vars['has_link'] = TRUE;
    }
    else {
      // sinon on charge une image simple et seule
      $vars['image'] = $media_asset_view;
      $vars['have_video'] = FALSE;
      $vars['has_link'] = FALSE;
    }
  }
}

function culturebox_preprocess_node__extrait_dans_actu(&$vars) {
  $node = $vars['node'];
  
  $custom_display = !empty($vars['view']) && $vars['view']->name == 'diffusions_list' && in_array($vars['view']->current_display, array('extraits_la_une'));

  $emission = field_get_items('node', $node, 'field_extrait_emission_tid');

  if (!empty($emission[0]['taxonomy_term'])) {
    $emission = $emission[0]['taxonomy_term'];
  }
  elseif (!empty($emission[0]['tid'])) {
    $emission = taxonomy_term_load($emission[0]['tid']);
  }

  if (!$custom_display && !empty($emission->vocabulary_machine_name) && $emission->vocabulary_machine_name == CULTUREBOX_EMISSION_EMISSION_VOCABULARY_NAME) {
    $vars['category'] = $emission->name;
  }

  if ($vars['field_extrait_video_bonus'] && $vars['field_extrait_video_bonus'][LANGUAGE_NONE][0]['value'] == 1) {
    $vars['is_bonus'] = TRUE;
  }

  if ($custom_display && isset($vars['view']->row_index) && $vars['view']->row_index == 0) {
    $image_style_name = 'live_view_small_first_la_une';
  }
  else {
    $image_style_name = 'live_most_viewed';
  }

  $media = field_get_items('node', $node, 'field_extrait_illustration');
  if ($media) {
    $asset = !empty($media[0]['entity']) ? $media[0]['entity'] : asset_load($media[0]['target_id']);
    $asset->alt_title = $node->title;
    if (!empty($asset) && is_object($asset) && !empty($asset->field_asset_image[LANGUAGE_NONE][0]['uri'])) {
      $media_asset_view = theme('image_style', array(
        'style_name' => $image_style_name,
        'path' => $asset->field_asset_image[LANGUAGE_NONE][0]['uri'],
        'title' => $node->title,
        'alt' => $node->title,
        'attributes' => array('class' => array('visuel-sommaire')),
      ));
    }
  }

  if (!empty($node->field_extrait_video) || !empty($node->field_fe_id_pgep)) {
    $vars['have_video'] = TRUE;
  }
  else {
    $vars['have_video'] = FALSE;
  }

  if (!empty($media_asset_view)) {
    $vars['image'] = culturebox_site_l(
      $media_asset_view, 'node/' . $vars['nid'], array(
        'html' => TRUE,
        'attributes' => array('class' => array("lien-visuel-sommaire")),
      )
    );
  }
}

function culturebox_preprocess_asset__extrait__small(&$vars) {
  $node = $vars['asset'];
  if ($vars['field_extrait_video_bonus'] && $vars['field_extrait_video_bonus'][0]['value'] == 1) {
    $vars['is_bonus'] = TRUE;
  }

  $media = field_get_items('asset', $node, 'field_extrait_illustration');
  if ($media) {

    $asset = !empty($media[0]['entity']) ? $media[0]['entity'] : asset_load($media[0]['target_id']);
    $asset->alt_title = $node->title;
    if (!empty($asset) && is_object($asset)) {
      $media_asset_view = theme('image_style', array(
        'style_name' => 'sommaire_diff_220x110',
        'path' => $asset->field_asset_image[LANGUAGE_NONE][0]['uri'],
        'title' => $node->title,
        'alt' => $node->title,
        'attributes' => array('class' => array('visuel-sommaire')),
      ));
    }
  }

  if (!empty($media_asset_view)) {
    // si on a une vidéo au bout c'est a dire qu'on a vraiment un extrait et non un élément de sommaire simple
    // on ajoute un lien sur l'image pour aller à l'extrait
    if (!empty($node->field_extrait_video) || !empty($node->field_fe_id_pgep) || (!empty($node->field_extrait_diffusion[0]['target_id']) && current_path() != 'node/' . $node->field_extrait_diffusion[0]['target_id'])) {
      $vars['image'] = culturebox_site_l(
        $media_asset_view, 'node/' . $vars['nid'], array(
          'html' => TRUE,
          'attributes' => array('class' => array("lien-visuel-sommaire")),
        )
      );
      if (!empty($node->field_extrait_video) || !empty($node->field_fe_id_pgep)) {
        $vars['have_video'] = TRUE;
      }
      else {
        $vars['have_video'] = FALSE;
      }
      $vars['has_link'] = TRUE;
    }
    else {
      // sinon on charge une image simple et seule
      $vars['image'] = $media_asset_view;
      $vars['have_video'] = FALSE;
      $vars['has_link'] = FALSE;
    }
  }
}

function culturebox_preprocess_node__fiche_emission_recommendate(&$vars) {
  $node = $vars['node'];
  $media = field_get_items('node', $node, 'field_fiche_emission_main_media');

  if ($media) {
    $asset = !empty($media[0]['entity']) ? $media[0]['entity'] : asset_load($media[0]['target_id']);
    $asset->alt_title = $node->title;
    if (!empty($asset) && is_object($asset)) {
      $media_asset_view = theme('image_style', array(
        'style_name' => 'sommaire_diff_220x110',
        'path' => $asset->field_asset_image[LANGUAGE_NONE][0]['uri'],
        'title' => $node->title,
        'alt' => $node->title,
        'attributes' => array('class' => array('visuel-sommaire')),
      ));
    }
  }

  if (!empty($media_asset_view)) {
    $vars['image'] = culturebox_site_l(
      $media_asset_view, 'node/' . $vars['nid'], array(
        'html' => TRUE,
        'attributes' => array('class' => array("lien-visuel-sommaire")),
      )
    );
  }

  _culturebox_preprocess_diffusion_button_play($vars, NULL, 'play-medium');
}

function culturebox_preprocess_node__extrait_full(&$vars) {
  $node = $vars['node'];

  _culturebox_get_bloc_livres($vars, $node, 'field_oeuvre');
  _culturebox_get_bloc_personnalites($vars, $node, 'field_linvite');

  $diffusion = field_get_items('node', $node, 'field_extrait_diffusion');

  if (!empty($diffusion[0]['entity'])) {
    $diffusion = $diffusion[0]['entity'];
  }
  elseif (!empty($diffusion[0]['target_id'])) {
    $diffusion = node_load($diffusion[0]['target_id']);
  }

  if (!empty($diffusion->type) && $diffusion->type == CULTUREBOX_EMISSION_FICHE_EMISSION_NODE_NAME) {
    $date = field_get_items('node', $diffusion, 'field_fe_broadcast_date');

    if (!empty($date[0]['value'])) {
      $field_date = strtotime($date[0]['value']);

      if ($field_date < strtotime('+21 days')) {
        // On n'affiche la date de la diffusion que si celle-ci est dans moins de 21 jours.
        $field_date = date('d/m/Y', $field_date);
        $vars['diff_date'] = $field_date;
      }
    }

    $vars['diff_title'] = $diffusion->title;
  }

  // Share links.
  $vars['share_links'] = theme('share_links_ajax', array('node' => $vars['node']));

  // S'il n'y a pas d'asset vidéo lié et qu'un ID PGEP est renseigné, on affiche le player FTV pour cet ID PGPEP.
  if (empty($node->field_extrait_video) && !empty($node->field_fe_id_pgep[LANGUAGE_NONE][0]['value'])) {
    $video_id = field_get_items('node', $node, 'field_fe_id_pgep');
    if (!empty($video_id[0]['value'])) {
      $video_id = $video_id[0]['value'];
      $video_url = "http://videos.francetv.fr/video/$video_id@" . variable_get('cb_pgep_catalog_name', 'Culture');

      if (!empty($video_url)) {
        $vars['player_link'] = '<a href="' . $video_url . '" class="player"></a>';
        
        $player_ftv_vars = array(
          'default_width' => 660,
          'default_height' => 370,
        );
        
        $vars['player_script'] = theme('culturebox_site_player_ftv_script', $player_ftv_vars);

        $flash_player_url = _culturebox_emission_get_flash_player_url();

        if (!empty($flash_player_url)) {
          drupal_add_js($flash_player_url, array('type' => 'external', 'weight' => 10));
        }
      }
    }
  }
}

function culturebox_preprocess_views_view_field__diffusions_list__icalendar(&$vars) {
  if (in_array($vars['field']->field, array('field_fe_live_start_time', 'field_fe_live_end_time'))) {
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

function culturebox_preprocess_taxonomy_term__personality_personnalite_emission(&$vars) {
  $term = $vars['term'];

  if (!empty($term->field_picture[LANGUAGE_NONE][0]['entity']->field_asset_image[LANGUAGE_NONE][0]['uri'])) {
    $vars['microdata_image'] = '<meta itemprop="image" content="' . file_create_url($term->field_picture[LANGUAGE_NONE][0]['entity']->field_asset_image[LANGUAGE_NONE][0]['uri']) . '" />';
  }

  if (!empty($term->description)) {
    $text_short = _filter_htmlcorrector(text_summary($term->description, NULL, 500));
    $text_long = $term->description;

    if ($text_short != $text_long) {
      $vars['description'] = '<div class="show">' . $text_short . '</div><div class="hide" itemprop="description">' . $text_long . '</div>';
      $vars['expander'] = '<div class="expander hidden"><div class="picto"><div class="fleche-expander"></div></div></div>';
    }
    else {
      $vars['description'] = '<p itemprop="description">' . $term->description . '</p>';
    }
  }

  // Link to twitter page.
  if (!empty($vars['content']['field_emission_twitter'][0]['#markup'])) {
    $user_name = $term->field_emission_twitter[LANGUAGE_NONE][0]['value'];
    $vars['content']['field_emission_twitter'][0]['#markup'] = culturebox_site_l("@$user_name", 'https://twitter.com/' . $user_name, array(
      'external' => TRUE,
      'attributes' => array('class' => 'twitter-link')
    ));
  }

  // Link to instagram page.
  if (!empty($vars['content']['field_emission_instagram'][0]['#markup'])) {
    // Get instagram user name.
    if (function_exists('culturebox_instagram_get_username_from_user_id')) {
      $user_name = culturebox_instagram_get_username_from_user_id($vars['content']['field_emission_instagram'][0]['#markup']);
      if ($user_name) {
        $vars['content']['field_emission_instagram'][0]['#markup'] = culturebox_site_l('Instagram', 'http://instagram.com/' . $user_name, array(
          'external' => TRUE,
          'attributes' => array('class' => 'instagram-link')
        ));
      }
    }
  }

  if (!empty($vars['content']['field_facebook'][0]['#element']['url'])) {
    $vars['content']['field_facebook'][0]['#element']['title'] = 'Facebook';
  }

  if (culturebox_site_show_personnality_page_if_nodes_count($term)) {
    $vars['personnality_link'] = culturebox_site_l('Voir la fiche artiste', 'taxonomy/term/' . $term->tid, array(
      'attributes' => array(
        'itemprop' => 'url',
        'class' => array('link-fiche')
      )
    ));
  }
}

function culturebox_preprocess_taxonomy_term__personality_festivals(&$vars) {
  $term = $vars['term'];

  if (!empty($term->description)) {
    $vars['description'] = trim(_filter_htmlcorrector(truncate_utf8($term->description, 170) . '...'));
  }

  $vars['is_linkable'] = FALSE;

  if (culturebox_site_show_personnality_page_if_nodes_count($term)) {
    $vars['is_linkable'] = TRUE;
  }
}

function culturebox_preprocess_field_collection_item__field_emission_team(&$vars) {
  $fields = $vars['content'];
  // On encapsule chaque articlé lié dans une div.
  if (!empty($vars['content']['field_emission_team_int_article']['#items'])) {
    foreach ($vars['content']['field_emission_team_int_article']['#items'] as $key => $item) {
      if (!empty($item['target_id'])) {
        if (!empty($vars['content']['field_emission_team_int_article'][$key]['node'][$item['target_id']])) {
          $vars['content']['field_emission_team_int_article'][$key]['node'][$item['target_id']]['#prefix'] = '<div class="item-lie">';
          $vars['content']['field_emission_team_int_article'][$key]['node'][$item['target_id']]['#suffix'] = '</div>';
        }
      }
    }
  }
  // On encapsule chaque live lié dans une div.
  if (!empty($vars['content']['field_emission_team_int_live']['#items'])) {
    foreach ($vars['content']['field_emission_team_int_live']['#items'] as $key => $item) {
      if (!empty($item['target_id'])) {
        if (!empty($vars['content']['field_emission_team_int_live'][$key]['node'][$item['target_id']])) {
          $vars['content']['field_emission_team_int_live'][$key]['node'][$item['target_id']]['#prefix'] = '<div class="item-lie live">';
          $vars['content']['field_emission_team_int_live'][$key]['node'][$item['target_id']]['#suffix'] = '</div>';
        }
      }
    }
  }
  $expander = '';

  $vars['content']['#prefix'] = '<div itemprop="actor" itemscope itemtype="http://schema.org/Person">';
  $vars['content']['#suffix'] = '</div>';

  if (!empty($vars['content']['field_emission_team_bio'][0]['#markup'])) {
    $text_short = _filter_htmlcorrector(text_summary($vars['content']['field_emission_team_bio'][0]['#markup'], NULL, 500));
    $text_long = $vars['content']['field_emission_team_bio'][0]['#markup'];

    if ($text_short != $text_long) {
      $vars['content']['field_emission_team_bio'][0]['#markup'] = '<div class="show">' . $text_short . '</div><div class="hide">' . $text_long . '</div>';
      $expander = '<div class="expander hidden"><div class="picto"><div class="fleche-expander"></div></div></div>';
    }
  }

  $vars['content']['field_emission_team_bio'][0]['#prefix'] = '<div class="field-bio">';
  $vars['content']['field_emission_team_bio'][0]['#suffix'] = '</div>' . $expander;
  $vars['content']['field_emission_team_title'][0]['#prefix'] = '<div class="field-nom" itemprop="name">';
  $vars['content']['field_emission_team_title'][0]['#suffix'] = '</div>';

  // Link to personnality taxonomy term page.
  if (!empty($vars['content']['field_people']['#items'][0]['target_id'])) {
    $vars['content']['field_people'][0]['#markup'] = culturebox_site_l('Voir la fiche artiste', 'taxonomy/term/' . $vars['content']['field_people']['#items'][0]['target_id'], array(
      'attributes' => array(
        'itemprop' => 'url',
        'class' => 'link-fiche'
      )
    ));
  }
  // Link to twitter page.
  if (!empty($vars['content']['field_emission_twitter']['#items'][0]['value'])) {
    $user_name = $vars['content']['field_emission_twitter']['#items'][0]['value'];
    $vars['content']['field_emission_twitter'][0]['#markup'] = culturebox_site_l("$user_name", 'https://twitter.com/' . $user_name, array(
      'external' => TRUE,
      'attributes' => array('class' => 'twitter-link')
    ));
  }
  // Link to instagram page.
  if (!empty($vars['content']['field_emission_instagram']['#items'][0]['value'])) {
    // Get instagram user name.
    if (function_exists('culturebox_instagram_get_username_from_user_id')) {
      $user_name = culturebox_instagram_get_username_from_user_id($vars['content']['field_emission_instagram']['#items'][0]['value']);
      if ($user_name) {
        $vars['content']['field_emission_instagram'][0]['#markup'] = culturebox_site_l('Instagram', 'http://instagram.com/' . $user_name, array(
          'external' => TRUE,
          'attributes' => array('class' => 'instagram-link')
        ));
      }
    }
  }
}

function culturebox_preprocess_taxonomy_term__emission_full(&$vars) {
  if (!empty($vars['content']['description']['#markup'])) {
    $vars['text_short'] = _filter_htmlcorrector(text_summary($vars['content']['description']['#markup'], NULL, 1000));
    $vars['text_long'] = $vars['content']['description']['#markup'];
  }
}

function culturebox_preprocess_taxonomy_term__emission_live_header(&$vars) {
  $term = $vars['term'];

  $vars['text_short'] = '';
  if (!empty($vars['content']['description']['#markup'])) {
    $vars['text_short'] = _filter_htmlcorrector(text_summary($vars['content']['description']['#markup'], NULL, 1000));
  }

  $main_image = field_get_items('taxonomy_term', $term, 'field_emission_image_generic');

  if (!empty($main_image)) {
    if (!empty($main_image[0]['entity'])) {
      $asset = $main_image[0]['entity'];
    }
    elseif (!empty($main_image[0]['target_id'])) {
      $asset = entity_load('asset', array($main_image[0]['target_id']));

      $asset = reset($asset);
    }

    if (!empty($asset)) {
      $entity_view = entity_view('asset', array($asset), 'live_home_event_term');
      //$vars['emission_image'] = render($entity_view);
    }
  }
}

function culturebox_preprocess_views_view_unformatted__diffusions_list__all_extracts_by_diffusion(&$vars) {
  // Add "active" class to <div> if current node is listed.
  $id = NULL;
  $node = menu_get_object();

  if (!empty($node) && $node->type == CULTUREBOX_EMISSION_EXTRAIT_NODE_NAME) {
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

function culturebox_preprocess_views_view_field__diffusions_list__all_extracts_by_diffusion__title(&$vars) {
  // Do not output link if we are on the same page.
  $node = menu_get_object();

  if (!empty($node) && $node->type == CULTUREBOX_EMISSION_EXTRAIT_NODE_NAME) {
    if ($vars['row']->nid == $node->nid) {
      $vars['output'] = strip_tags($vars['output']);
    }
  }
}

/**
 * Return first live, which haven't status after replay an between direct and replay.
 */
function _culturebox_get_first_diffusion($ids, $nb = 1) {
  if (empty($ids)) {
    return NULL;
  }

  $status = array(
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_APRES_LE_REPLAY,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_ENTRE_LE_DIRECT_ET_LE_REPLAY,
  );

  $nids = db_select('node', 'n');
  $nids->join('field_data_field_fe_status', 'fdfls', 'n.nid = fdfls.entity_id');
  $nids->fields('n', array('nid'))
    ->condition('fdfls.field_fe_status_value', $status, 'NOT IN')
    ->condition('n.nid', $ids, 'IN')
    ->condition('n.status', NODE_PUBLISHED)
    ->range(0, $nb);
  $res = $nids->execute()->fetchCol();

  if (!empty($res)) {
    return $nb == 1 ? array_shift($res) : $res;
  }

  return NULL;
}

function culturebox_preprocess_node__extrait_home_from_editor_big(&$vars) {
  $node = $vars['node'];

  // Image section.
  $media_view = field_get_items('node', $node, 'field_extrait_illustration');

  if (!empty($media_view)) {
    if (!empty($media_view[0]['entity'])) {
      $media = $media_view[0]['entity'];
    }
    elseif ($media_view[0]['target_id']) {
      $media = asset_load($media_view[0]['target_id']);
    }

    if (!empty($media)) {
      $media_view = entity_view('asset', array($media), 'live_home_top');
      $live_media = drupal_render($media_view);

      $vars['media'] = culturebox_site_l(
        $live_media, 'node/' . $vars['nid'], array(
          'html' => TRUE,
        )
      );
    }
  }

  $diff = field_get_items('node', $node, 'field_extrait_diffusion');

  if (!empty($diff[0]['entity'])) {
    $vars['main_thematic'] = _culturebox_get_node_diffusion_main_category($diff[0]['entity']);
  }
  elseif (!empty($diff[0]['target_id'])) {
    $node_diff = node_load($diff[0]['target_id']);

    if (!empty($node_diff)) {
      $vars['main_thematic'] = _culturebox_get_node_diffusion_main_category($node_diff);
    }
  }

  if (!empty($node->field_extrait_video)) {
    $button_text = 'Voir la vidéo';
    $vars['direct'] = TRUE;
  }
  else {
    $button_text = 'En savoir plus';
  }

  $vars['more_button'] = culturebox_site_l(
    '<span class="picto"></span>' . $button_text, "node/{$node->nid}", array(
      'html' => TRUE,
      'attributes' => array('class' => (!empty($vars['direct'])) ? array('view-link-mode') : array()),
    )
  );

  // Share links.
  $vars['share_links'] = theme('live_share_popup', array('node' => $node));
  $vars['title_link'] = culturebox_site_l($vars['node']->title, "node/{$vars['nid']}");

  $vars['reagir'] = culturebox_site_l('Réagir', "node/$node->nid", array(
    'attributes' => array('class' => 'imitation-link'),
  ));
}

function culturebox_preprocess_node__extrait_home_from_editor_medium(&$vars) {
  $node = $vars['node'];

  $diff = field_get_items('node', $node, 'field_extrait_diffusion');

  if (!empty($diff[0]['entity'])) {
    $category = _culturebox_get_node_diffusion_main_category($diff[0]['entity']);
    $vars['tags'] = $category;
  }
  elseif (!empty($diff[0]['target_id'])) {
    $node_diff = node_load($diff[0]['target_id']);

    if (!empty($node_diff)) {
      $category = _culturebox_get_node_diffusion_main_category($node_diff);
      $vars['tags'] = $category;
    }
  }

  // Image section.
  $media_view = field_get_items('node', $node, 'field_extrait_illustration');

  if (!empty($media_view)) {
    if (!empty($media_view[0]['entity'])) {
      $media = $media_view[0]['entity'];
    }
    elseif ($media_view[0]['target_id']) {
      $media = asset_load($media_view[0]['target_id']);
    }

    if (!empty($media)) {
      $media_view = entity_view('asset', array($media), 'live_la_section');
      $live_media = drupal_render($media_view);

      $vars['media'] = culturebox_site_l(
        $live_media, 'node/' . $vars['nid'], array(
          'html' => TRUE,
        )
      );
    }
  }

  // Title.
  $vars['title'] = culturebox_site_l(
    $node->title, "node/{$node->nid}"
  );
  // lien voir plus
  $vars['know_more'] = culturebox_site_l('En savoir plus', "node/{$node->nid}", array('attributes' => array('class' => 'know-more')));
}

function culturebox_preprocess_node__fiche_emission_most_viewed(&$vars) {
  // Categories.
  if ($thematic = _culturebox_get_node_main_category($vars['node'])) {
    $vars['category'] = $thematic;
  }

  $vars['position'] = $vars['view']->row_index + 1;

  if ($article_media = _culturebox_get_first_diffusion_media_asset_view($vars)) {
    $vars['rendered_article_media'] = culturebox_site_l(
      drupal_render($article_media), 'node/' . $vars['nid'], array(
        'html' => TRUE,
      )
    );
  }

  $vars['title_link'] = culturebox_site_l($vars['node']->title, "node/{$vars['nid']}");
}

function culturebox_preprocess_node__fiche_emission_most_viewed_sidebar(&$vars) {
  $node = $vars['node'];

  $get_emission_tid = field_get_items('node', $vars['node'], 'field_fe_emission_tid');
  $tid = $get_emission_tid[0]['tid'];
  $emission = taxonomy_term_load($tid);
  if (!empty($emission->name)) {
    $vars['name_emission'] = $emission->name;
  }
  if (isset($vars['view']->row_index)) {
    $vars['num'] = $vars['view']->row_index + 1;
  }

  if ($thematic = _culturebox_get_node_diffusion_main_category($node)) {
    $vars['category'] = $thematic;
  }

  _culturebox_preprocess_diffusion_button_play($vars, NULL, 'play-small');

  $media_view = _culturebox_get_first_diffusion_media_asset_view($vars);
  if (!empty($media_view)) {
    $vars['image'] = culturebox_site_l(
      drupal_render($media_view), 'node/' . $vars['nid'], array(
        'html' => TRUE,
      )
    );
  }
  $heure = $node->field_fe_broadcast_date['und'][0];
  _culturebox_site_preprocess_date_countdown($vars, $heure);

  $vars['channel'] = $emission->field_emission_channel['und'][0]['value'];
}

function culturebox_preprocess_node__fiche_emission_home_from_editor_medium_2(&$vars) {
  // Image.
  if (!empty($vars['content']['field_fiche_emission_main_media'])) {
    $illustration = drupal_render($vars['content']['field_fiche_emission_main_media']);
    $vars['media'] = culturebox_site_l(
      $illustration, 'node/' . $vars['nid'], array(
        'html' => TRUE,
      )
    );
  }

  if (!empty($vars['node']->field_fe_emission_tid[LANGUAGE_NONE][0]['tid'])) {
    $parent = taxonomy_term_load($vars['node']->field_fe_emission_tid[LANGUAGE_NONE][0]['tid']);

    if ($parent) {
      if (!empty($parent->field_emission_channel)) {
        $get_channel = field_get_items('taxonomy_term', $parent, 'field_emission_channel');
        $vars['channel'] = $get_channel[0]['value'];
      }

      $vars['emssion_parent'] = culturebox_site_l($parent->name, 'taxonomy/term/' . $parent->tid);
      $vars['tags'] = culturebox_site_l($parent->name, 'taxonomy/term/' . $parent->tid);
    }
  }

  $status = array(
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_ENTRE_LE_DIRECT_ET_LE_REPLAY,
    //CULTUREBOX_EMISSION_STATUS_DIFFUSION_REPLAY,
    //CULTUREBOX_EMISSION_STATUS_DIFFUSION_LAST_CHANCE,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_APRES_LE_REPLAY,
  );
  if (!in_array($vars['field_fe_status'][LANGUAGE_NONE][0]['value'], $status)) {
    _culturebox_preprocess_diffusion_button_play($vars, NULL, 'play-medium', TRUE);
  }

  $vars['title'] = culturebox_site_l(
    truncate_utf8($vars['node']->title, 45, TRUE, TRUE), "node/{$vars['node']->nid}"
  );

  $vars['date'] = culturebox_emission_date($vars);
}

function culturebox_preprocess_node__fiche_emission_live_small(&$vars) {
  $live = $vars['node'];

  if (!empty($vars['view']) &&
    $vars['view']->name == 'diffusions_list' &&
    $vars['view']->current_display == 'diffusions_list_tagged_in_minisite' &&
    isset($vars['view']->row_index) && $vars['view']->row_index == 0
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
  if ($thematic = _culturebox_get_node_diffusion_main_category($live)) {
    $vars['category'] = $thematic;
  }

  $live_params = array(
    'node' => $live,
  );

  _culturebox_preprocess_diffusion_button_play($live_params, NULL, $icon);
  if (!empty($live_params['live_status'])) {
    $vars['live_status'] = $live_params['live_status'];
  }

  $media = field_get_items('node', $live, 'field_fiche_emission_main_media');
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

function culturebox_preprocess_node__fiche_emission_widget_small(&$vars) {
  _culturebox_preprocess_node__fiche_emission_widget($vars);
}

function culturebox_preprocess_node__fiche_emission_widget_medium(&$vars) {
  _culturebox_preprocess_node__fiche_emission_widget($vars);
}

function culturebox_preprocess_node__fiche_emission_widget_large(&$vars) {
  _culturebox_preprocess_node__fiche_emission_widget($vars);
}

function _culturebox_preprocess_node__fiche_emission_widget(&$vars) {
  $node = $vars['node'];

  // Live status.
  $status = field_get_items('node', $vars['node'], 'field_fe_status');
  $status = $status[0]['value'];
  drupal_add_js(array('CultureboxLive' => array('statusLive' => $status)), 'setting');
  $vars['label_class'] = '';
  $vars['live_status'] = '';
  $vars['status'] = $status;
  $vars['message'] = '';

  switch ($status) {
    case CULTUREBOX_EMISSION_STATUS_DIFFUSION_RETARD:
      $vars['message'] = '';
      $message = field_get_items('node', $node, 'field_fe_delay_message');
      if ($message) {
        $vars['message'] = $message[0]['value'];
      }
      $vars['live_status'] = 'Bientôt';
      break;

    case CULTUREBOX_EMISSION_STATUS_DIFFUSION_PROCHAINEMENT:
    case CULTUREBOX_EMISSION_STATUS_DIFFUSION_AVANT_LE_DIRECT:
      $message = field_get_items('node', $node, 'field_fe_before_broadcast_mess');
      if ($message) {
        $vars['message'] = $message[0]['value'];
      }
      $vars['live_status'] = 'Bientôt';
      break;

    case CULTUREBOX_LIVE_STATUS_LIVE_ENTRE_LE_DIRECT_ET_LE_REPLAY:
      $message = field_get_items('node', $node, 'field_fe_bwn_bct_replay_msg');
      if ($message) {
        $vars['message'] = $message[0]['value'];
      }
      break;

    case CULTUREBOX_EMISSION_STATUS_DIFFUSION_APRES_LE_DIRECT:
    case CULTUREBOX_EMISSION_STATUS_DIFFUSION_ENTRE_LE_DIRECT_ET_LE_REPLAY:
      $message = field_get_items('node', $node, 'field_fe_after_replay_message');
      if ($message) {
        $vars['message'] = $message[0]['value'];
      }
      break;

    case CULTUREBOX_EMISSION_STATUS_DIFFUSION_LAST_CHANCE:
      $vars['live_status'] = 'Dernière chance';
      break;

    case CULTUREBOX_EMISSION_STATUS_DIFFUSION_DIRECT:
      $vars['live_status'] = 'Direct';
      break;

    case CULTUREBOX_EMISSION_STATUS_DIFFUSION_REPLAY:
      $vars['live_status'] = 'A revoir';
      $vars['label_class'] = 'arevoir';
      break;
  }

  // Dates.
  $vars['live_start_time'] = field_get_items('node', $node, 'field_fe_broadcast_date');

  if (!empty($vars['field_fe_delay']) && $vars['live_status'] == 'Bientôt') {
    // Get remain time before live start.
    $timezone = new DateTimeZone('Europe/Paris');
    $start_time_obj = new DateTime($vars['live_start_time'][0]['value'], $timezone);

    if (!empty($vars['field_fe_delay'][LANGUAGE_NONE][0]['value'])) {
      $start_time_obj->modify('+' . $vars['field_fe_delay'][LANGUAGE_NONE][0]['value'] . ' minutes');
    }

    $time = new DateTime('now', $timezone);
    $offset = $time->getOffset() / 60;

    drupal_add_js(array(
      'CultureboxLive' => array(
        'CountdownTime' => $start_time_obj->format('m/d/Y H:i:s'),
        'offset' => $offset
      )
    ), 'setting');
    $vars['countdown_time'] = $start_time_obj->format('m/d/Y H:i:s');
    $vars['offset'] = $offset;
    $vars['counter'] = TRUE;
  }

  // Player integration.
  if (!empty($status) && in_array($status, array(
      CULTUREBOX_EMISSION_STATUS_DIFFUSION_DIRECT,
      CULTUREBOX_EMISSION_STATUS_DIFFUSION_REPLAY,
      CULTUREBOX_EMISSION_STATUS_DIFFUSION_LAST_CHANCE
    ))
  ) {
    $video_url = '';

    if (!empty($status) && in_array($status, array(CULTUREBOX_EMISSION_STATUS_DIFFUSION_DIRECT))) {
      $emission = field_get_items('node', $node, 'field_fe_emission_tid');

      if (!empty($emission[0]['taxonomy_term'])) {
        $emission = $emission[0]['taxonomy_term'];
      }
      elseif (!empty($emission[0]['tid'])) {
        $emission = taxonomy_term_load($emission[0]['tid']);
      }

      if (!empty($emission->vocabulary_machine_name) && $emission->vocabulary_machine_name == CULTUREBOX_EMISSION_EMISSION_VOCABULARY_NAME) {
        $chaine = field_get_items('taxonomy_term', $emission, 'field_emission_channel');

        if (!empty($chaine[0]['value'])) {
          $video_id = 'SIM_' . $chaine[0]['value'];
          $video_url = "http://videos.francetv.fr/video/$video_id";
        }
      }
    }

    if (!empty($status) && in_array($status, array(
        CULTUREBOX_EMISSION_STATUS_DIFFUSION_REPLAY,
        CULTUREBOX_EMISSION_STATUS_DIFFUSION_LAST_CHANCE
      ))
    ) {
      $video_id = field_get_items('node', $node, 'field_fe_id_pgep');
      if (!empty($video_id[0]['value'])) {
        $video_id = $video_id[0]['value'];
        $video_url = "http://videos.francetv.fr/video/$video_id@" . variable_get('cb_pgep_catalog_name', 'Culture');
      }
    }

    // Player link.
    if (!empty($video_url)) {
      $vars['player_link'] = '<a href="' . $video_url . '" class="player"></a>';
      $vars['player_script'] = theme('culturebox_site_player_ftv_script');
    }
  }

  // Request to get flash player URL.
  $flash_player_url = _culturebox_emission_get_flash_player_url();

  if (!empty($flash_player_url)) {
    drupal_add_js($flash_player_url, array('weight' => 10));
  }

  // Image or trailer.
  $status_for_show_trailer = array(
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_PROCHAINEMENT,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_AVANT_LE_DIRECT,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_RETARD,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_ENTRE_LE_DIRECT_ET_LE_REPLAY,
  );

  $status_for_show_image = array(
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_PROCHAINEMENT,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_AVANT_LE_DIRECT,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_RETARD,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_APRES_LE_DIRECT,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_ENTRE_LE_DIRECT_ET_LE_REPLAY,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_APRES_LE_REPLAY,
  );

  // Get trailer if available.
  if ($trailer = field_get_items('node', $node, 'field_fe_video_trailer') &&
    in_array($status, $status_for_show_trailer)
  ) {
    $vars['trailer'] = render($vars['content']['field_fe_video_trailer']);
    $vars['trailer'] = trim($vars['trailer']);
  }

  // We not need image if we have status CULTUREBOX_LIVE_STATUS_LIVE_PROCHAINEMENT and we have trailer.
  if (in_array($status, $status_for_show_image)) {
    if ($main_image = field_get_items('node', $node, 'field_fiche_emission_main_media')) {
      $vars['image'] = render($vars['content']['field_fiche_emission_main_media'][0]);
    }
  }
}

function _culturebox_get_bloc_livres(&$vars, $entity, $field_name) {
  if (!empty($entity->{$field_name}[LANGUAGE_NONE])) {
    $oeuvres = array();
    $oeuvres_nids = array();

    foreach ($entity->{$field_name}[LANGUAGE_NONE] as $field_oeuvre) {
      if (!empty($field_oeuvre['target_id'])) {
        $oeuvres_nids[] = $field_oeuvre['target_id'];
      }
    }

    if (!empty($oeuvres_nids)) {
      $oeuvres_nodes = node_load_multiple($oeuvres_nids);

      foreach ($oeuvres_nodes as $oeuvre_node) {
        if ($oeuvre_node->status == NODE_PUBLISHED) {
          $oeuvres[] = $oeuvre_node;
        }
      }

      if (!empty($oeuvres)) {
        $rendered_nodes = array();

        foreach ($oeuvres as $oeuvre) {
          $node_view = node_view($oeuvre, 'personnalite_emission');
          $node_view_rendered = render($node_view);

          if (!empty($node_view_rendered)) {
            $rendered_nodes[] = $node_view_rendered;
          }
        }

        if (!empty($rendered_nodes)) {
          $vars['bloc_oeuvres'] = theme('culturebox_oeuvres_livres', array(
            'lists' => $rendered_nodes,
            'title_changed' => format_plural(count($rendered_nodes), 'Le livre', 'Les livres'),
          ));
        }
      }
    }
  }
}

function _culturebox_get_bloc_personnalites(&$vars, $entity, $field_name) {
  if (!empty($entity->{$field_name}[LANGUAGE_NONE])) {
    $personnalites_tids = array();

    foreach ($entity->{$field_name}[LANGUAGE_NONE] as $field_personnalite) {
      if (!empty($field_personnalite['target_id'])) {
        $personnalites_tids[] = $field_personnalite['target_id'];
      }
    }

    if (!empty($personnalites_tids)) {
      $personnalites_terms = taxonomy_term_load_multiple($personnalites_tids);
      $rendered_terms = array();

      foreach ($personnalites_terms as $personnalite) {
        $term_view = taxonomy_term_view($personnalite, 'festivals');
        $term_view_rendered = render($term_view);

        if (!empty($term_view_rendered)) {
          $rendered_terms[] = $term_view_rendered;
        }
      }

      if (!empty($rendered_terms)) {
        $vars['bloc_personnalites'] = theme('culturebox_oeuvres_livres', array(
          'lists' => $rendered_terms,
          'title_changed' => format_plural(count($rendered_terms), 'L\'auteur', 'Les auteurs'),
        ));
      }
    }
  }
}

function culturebox_emission_date($vars) {
  $get_date = field_get_items('node', $vars['node'], 'field_fe_broadcast_date');
  
  if (!empty($get_date[0]['value'])) {
    $time_date = strtotime($get_date[0]['value']);
    $date = format_date($time_date, 'custom', 'l j');
    
    $month = drupal_strtolower(format_date($time_date, 'custom', 'F'));
    
    if (drupal_strlen($month) > 3) {
      $month = drupal_substr($month, 0, 3) . '.';
    }
    
    $date .= ' ' . $month;
    $date .= ' ' . format_date($time_date, 'custom', 'Y');
  }
  
  return $date;
}

function culturebox_emission_duree($vars) {
  $get_duree = field_get_items('node', $vars['node'], 'field_fe_broadcast_duration');
  $duree = '';
  if (!empty($get_duree[0]['value'])) {
    $duree = ' | ' . $get_duree[0]['value'];
  }
  return $duree;
}

function culturebox_preprocess_node__fiche_emission_very_small(&$vars) {
  $emission_tid = field_get_items('node', $vars['node'], 'field_fe_emission_tid');

  if (!empty($emission_tid[0]['tid'])) {
    $vars['emission'] = taxonomy_term_load($emission_tid[0]['tid']);
    $channel = field_get_items('taxonomy_term', $vars['emission'], 'field_emission_channel');

    if (!empty($channel[0]['value'])) {
      $vars['channel'] = $channel[0]['value'];
    }
  }
}

function culturebox_preprocess_node__fiche_emission_search(&$vars) {
  $node = $vars['node'];

  if (!empty($node->field_fe_emission_tid[LANGUAGE_NONE][0]['tid'])) {
    $emission = taxonomy_term_load($node->field_fe_emission_tid[LANGUAGE_NONE][0]['tid']);
    $vars['category'] = culturebox_site_l($emission->name, 'taxonomy/term/' . $emission->tid);

    $get_channel = field_get_items('taxonomy_term', $emission, 'field_emission_channel');
    $vars['channel'] = $get_channel[0]['value'];
  }

  $vars['title'] = culturebox_site_l($vars['node']->title, "node/$node->nid");

  if ($media = _culturebox_get_first_diffusion_media_asset_view($vars, 'search')) {
    $vars['image'] = culturebox_site_l(render($media), 'node/' . $node->nid, array('html' => TRUE));
  }

  _culturebox_preprocess_diffusion_button_play($vars, NULL, 'play-medium', TRUE);
}

function culturebox_preprocess_node__extrait_search(&$vars) {
  $node = $vars['node'];

  if (!empty($node->field_extrait_emission_tid[LANGUAGE_NONE][0]['tid'])) {
    $emission = taxonomy_term_load($node->field_extrait_emission_tid[LANGUAGE_NONE][0]['tid']);
    $vars['category'] = culturebox_site_l($emission->name, 'taxonomy/term/' . $emission->tid);
    
    $get_channel = field_get_items('taxonomy_term', $emission, 'field_emission_channel');
    $vars['channel'] = $get_channel[0]['value'];
  }

  $vars['title'] = culturebox_site_l($vars['node']->title, "node/$node->nid");

  if ($media = _culturebox_get_first_diffusion_media_asset_view($vars, 'search', 'field_extrait_illustration')) {
    $vars['image'] = culturebox_site_l(render($media), 'node/' . $node->nid, array('html' => TRUE));
  }

  $vars['media_icon'] = culturebox_site_l(
      '&nbsp;', 'node/' . $node->nid, array(
    'html' => TRUE,
    'attributes' => array(
      'class' => array('imitation-links', 'play-medium'),
    ),
      )
  );
}

function culturebox_preprocess_node__fiche_emission_ticker_small(&$vars) {
  $node = $vars['node'];
  
  $status = field_get_items('node', $vars['node'], 'field_fe_status');
  $status = $status[0]['value'];

  $show_date_statuses = array(
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_PROCHAINEMENT,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_AVANT_LE_DIRECT,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_RETARD,
  );
  
  $vars['show_play'] = in_array($status, array(CULTUREBOX_EMISSION_STATUS_DIFFUSION_DIRECT, CULTUREBOX_EMISSION_STATUS_DIFFUSION_REPLAY, CULTUREBOX_EMISSION_STATUS_DIFFUSION_LAST_CHANCE));

  $diffusion_status_vars = array('node' => $vars['node'], 'hide_play' => TRUE);

  if (in_array($status, $show_date_statuses)) {
    $diffusion_start_time = field_get_items('node', $vars['node'], 'field_fe_live_start_time');

    if ($diffusion_start_time) {
      $diffusion_start_time = array_shift($diffusion_start_time);
      _culturebox_site_preprocess_date_countdown($vars, $diffusion_start_time);

      $diffusion_status_vars['custom'] = $vars['date'];
    }
  }

  $vars['status'] = theme('diffusion_status', $diffusion_status_vars);
  
  $vars['url'] = "node/{$node->nid}";
  
  if (!empty($node->ticker_custom_url)) {
    $vars['url'] = $node->ticker_custom_url;
  }
  
  // Image.
  $image = field_get_items('node', $vars['node'], 'field_fiche_emission_main_media');

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

function culturebox_preprocess_node__fiche_emission_ticker_big(&$vars) {
  $node = $vars['node'];

  $status = field_get_items('node', $vars['node'], 'field_fe_status');
  $status = $status[0]['value'];

  $show_date_statuses = array(
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_PROCHAINEMENT,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_AVANT_LE_DIRECT,
    CULTUREBOX_EMISSION_STATUS_DIFFUSION_RETARD,
  );

  $diffusion_status_vars = array('node' => $vars['node'], 'hide_play' => TRUE);

  if (in_array($status, $show_date_statuses)) {
    $diffusion_start_time = field_get_items('node', $vars['node'], 'field_fe_live_start_time');

    if ($diffusion_start_time) {
      $diffusion_start_time = array_shift($diffusion_start_time);
      _culturebox_site_preprocess_date_countdown($vars, $diffusion_start_time);

      $diffusion_status_vars['custom'] = $vars['date'];
    }
  }

  $vars['status'] = theme('diffusion_status', $diffusion_status_vars);

  $vars['url'] = "node/{$node->nid}";

  if (!empty($node->ticker_custom_url)) {
    $vars['url'] = $node->ticker_custom_url;
  }

  $video_url = '';

  if (!empty($status) && in_array($status, array(CULTUREBOX_EMISSION_STATUS_DIFFUSION_DIRECT))) {
    $emission = field_get_items('node', $node, 'field_fe_emission_tid');

    if (!empty($emission[0]['taxonomy_term'])) {
      $emission = $emission[0]['taxonomy_term'];
    }
    elseif (!empty($emission[0]['tid'])) {
      $emission = taxonomy_term_load($emission[0]['tid']);
    }

    if (!empty($emission->vocabulary_machine_name) && $emission->vocabulary_machine_name == CULTUREBOX_EMISSION_EMISSION_VOCABULARY_NAME) {
      $chaine = field_get_items('taxonomy_term', $emission, 'field_emission_channel');

      if (!empty($chaine[0]['value'])) {
        $video_id = 'SIM_' . $chaine[0]['value'];
        $video_url = "http://videos.francetv.fr/video/$video_id@" . variable_get('cb_pgep_catalog_name', 'Culture');
      }
    }
  }

  if (!empty($status) && in_array($status, array(CULTUREBOX_EMISSION_STATUS_DIFFUSION_REPLAY, CULTUREBOX_EMISSION_STATUS_DIFFUSION_LAST_CHANCE))) {
    $video_id = field_get_items('node', $node, 'field_fe_id_pgep');

    if (!empty($video_id[0]['value'])) {
      $video_id = $video_id[0]['value'];
      $video_url = "http://videos.francetv.fr/video/$video_id@" . variable_get('cb_pgep_catalog_name', 'Culture');
    }
  }

  if (!empty($video_url)) {
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

  if (empty($vars['image'])) {
    // Image.
    $image = field_get_items('node', $vars['node'], 'field_fiche_emission_main_media');

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
}
