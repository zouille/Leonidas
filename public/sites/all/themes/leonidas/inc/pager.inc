<?php
/**
 * @file
 * Pager.
 */

/**
 * Article list pager
 */
function culturebox_pager($variables) {
  $tags = $variables['tags'];
  $element = $variables['element'];
  // Filter empty GET params from query.
  $parameters = array_filter($variables['parameters']);
  $quantity = $variables['quantity'];

  global $pager_page_array, $pager_total;

  // Calculate various markers within this pager piece:
  // max is the maximum page number
  $pager_max = $pager_total[$element];
  // current is the page we are currently paged to
  $pager_current = $pager_page_array[$element] + 1;
  if ($pager_current < 3 || $pager_current > ($pager_max - 3)) {
    $quantity = 6;
  }
  elseif ($pager_current == 3 || $pager_current == ($pager_max - 3)) {
    $quantity = 5;
  }
  else {
    $quantity = 4;
  }

  // Middle is used to "center" pages around the current page.
  $pager_middle = ceil($quantity / 2);
  // first is the first page listed by this pager piece (re quantity)
  $pager_first = $pager_current - $pager_middle + 1;
  // last is the last page listed by this pager piece (re quantity)
  $pager_last = $pager_current + $quantity - $pager_middle;
  // End of marker calculations.

  // Prepare for generation loop.
  $i = $pager_first;
  if ($pager_last > $pager_max) {
    // Adjust "center" if at end of query.
    $i = $i + ($pager_max - $pager_last);
    $pager_last = $pager_max;
  }
  if ($i <= 0) {
    // Adjust "center" if at start of query.
    $pager_last = $pager_last + (1 - $i);
    $i = 1;
  }
  // End of generation loop preparation.

  $li_previous = theme('pager_previous', array('text' => (isset($tags[1]) ? $tags[1] : t('‹ previous')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));
  $li_next = theme('pager_next', array('text' => (isset($tags[3]) ? $tags[3] : t('next ›')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));

  if ($pager_total[$element] > 1) {
    if ($li_previous) {
      $items[] = array(
        'class' => array('pager-previous'),
        'data' => $li_previous,
      );
    }

    // When there is more than one page, create the pager list.
    if ($i != $pager_max) {
      if ($i > 3) {
        $items[] = array(
          'data' => theme('pager_previous', array('text' => 1, 'element' => $element, 'interval' => ($pager_current - 1), 'parameters' => $parameters)),
        );
        $items[] = array(
          'class' => array('separator'),
          'data' => '|',
        );
      }
      elseif ($i > 1 && $i < 4) {
        for ($x = 1; $x < $i; $x++) {
          $items[] = array(
            'data' => theme('pager_previous', array('text' => $x, 'element' => $element, 'interval' => ($pager_current - $x), 'parameters' => $parameters)),
          );
        }
      }
      // Now generate the actual pager piece.
      for (; $i <= $pager_last && $i <= $pager_max; $i++) {
        if ($i < $pager_current) {
          $items[] = array(
            'data' => theme('pager_previous', array('text' => $i, 'element' => $element, 'interval' => ($pager_current - $i), 'parameters' => $parameters)),
          );
        }
        if ($i == $pager_current) {
          $items[] = array(
            'data' => "<span>$i</span>",
          );
        }
        if ($i > $pager_current) {
          $items[] = array(
            'data' => theme('pager_next', array('text' => $i, 'element' => $element, 'interval' => ($i - $pager_current), 'parameters' => $parameters)),
          );
        }
      }
      if ($i < $pager_max-1) {
        $items[] = array(
          'class' => array('separator'),
          'data' => '|',
        );
        $items[] = array(
          'data' => theme('pager_next', array('text' => $pager_max, 'element' => $element, 'interval' => ($pager_max-$pager_current), 'parameters' => $parameters)),
        );
      }
      elseif ($i >= ($pager_max - 1) && $i < ($pager_max + 1)) {

        for ($y = $i; $y < ($pager_max + 1); $y++) {
          $items[] = array(
            'data' => theme('pager_next', array('text' => $y, 'element' => $element, 'interval' => ($y - $pager_current), 'parameters' => $parameters)),
          );
        }
      }
    }
    // End generation.
    if ($li_next) {
      $items[] = array(
        'data' => $li_next,
      );
    }
    $class[] = 'pagination';
    $class = implode(' ', $class);
    return "<div class='$class'>" .theme('item_list', array('items' => $items, 'pager' => TRUE,)) . '</div>';
  }
}

/**
 * Article list pager
 */
function culturebox_ten_first_pager($variables) {
  $tags = $variables['tags'];
  $element = $variables['element'];
  // Filter empty GET params from query.
  $parameters = array_filter($variables['parameters']);
  $quantity = $variables['quantity'];

  global $pager_page_array, $pager_total;

  // Calculate various markers within this pager piece:
  // max is the maximum page number
  $pager_max = $pager_total[$element];
  // current is the page we are currently paged to
  $pager_current = $pager_page_array[$element] + 1;
  if ($pager_current < 10 || $pager_current > ($pager_max - 5)) {
    $quantity = 10;
  }
  elseif ($pager_current == 5 || $pager_current == ($pager_max - 5)) {
    $quantity = 10;
  }
  else {
    $quantity = 9;
  }

  // Middle is used to "center" pages around the current page.
  $pager_middle = ceil($quantity / 2);
  // first is the first page listed by this pager piece (re quantity)
  $pager_first = $pager_current - $pager_middle + 1;
  // last is the last page listed by this pager piece (re quantity)
  $pager_last = $pager_current + $quantity - $pager_middle;
  // End of marker calculations.

  // Prepare for generation loop.
  $i = $pager_first;
  if ($pager_last > $pager_max) {
    // Adjust "center" if at end of query.
    $i = $i + ($pager_max - $pager_last);
    $pager_last = $pager_max;
  }
  if ($i <= 0) {
    // Adjust "center" if at start of query.
    $pager_last = $pager_last + (1 - $i);
    $i = 1;
  }
  // End of generation loop preparation.

  if ($pager_total[$element] > 1) {
    // When there is more than one page, create the pager list.
    if ($i != $pager_max) {
      // Now generate the actual pager piece.
      for (; $i <= $pager_last && $i <= $pager_max; $i++) {
        if ($i < $pager_current) {
          $items[] = array(
            'data' => theme('pager_previous', array('text' => $i, 'element' => $element, 'interval' => ($pager_current - $i), 'parameters' => $parameters)),
          );
        }
        if ($i == $pager_current) {
          $items[] = array(
            'data' => "<span>$i</span>",
          );
        }
        if ($i > $pager_current) {
          $items[] = array(
            'data' => theme('pager_next', array('text' => $i, 'element' => $element, 'interval' => ($i - $pager_current), 'parameters' => $parameters)),
          );
        }
      }
    }
    $class[] = 'pagination ten-first-pager';
    $class = implode(' ', $class);
    return "<div class='$class'>" .theme('item_list', array('items' => $items, 'pager' => TRUE,)) . '</div>';
  }
}


/**
 * Article list pager
 */
function culturebox_composite_pager($variables) {

  $tags = $variables['tags'];
  $element = $variables['element'];
  // Filter empty GET params from query.
  $parameters = array_filter($variables['parameters']);
  $quantity = $variables['quantity'];

  global $pager_page_array, $pager_total;

  // Calculate various markers within this pager piece:
  // max is the maximum page number
  $pager_max = $pager_total[$element];
  // current is the page we are currently paged to
  $pager_current = $pager_page_array[$element] + 1;
  $quantity = 9;

  // Middle is used to "center" pages around the current page.
  $pager_middle = ceil($quantity / 2);
  // first is the first page listed by this pager piece (re quantity)
  $pager_first = $pager_current - $pager_middle + 1;
  // last is the last page listed by this pager piece (re quantity)
  $pager_last = $pager_current + $quantity - $pager_middle;
  // End of marker calculations.

  // Prepare for generation loop.
  $i = $pager_first;
  if ($pager_last > $pager_max) {
    // Adjust "center" if at end of query.
    $i = $i + ($pager_max - $pager_last);
    $pager_last = $pager_max;
  }
  if ($i <= 0) {
    // Adjust "center" if at start of query.
    $pager_last = $pager_last + (1 - $i);
    $i = 1;
  }
  // End of generation loop preparation.

  $li_previous = theme('pager_previous', array('text' => (isset($tags[1]) ? $tags[1] : t('‹ previous')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));
  $li_next = theme('pager_next', array('text' => (isset($tags[3]) ? $tags[3] : t('next ›')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));

  $current_hundreds = floor($pager_current / 100) * 100;

  if ($pager_total[$element] > 1) {
    if ($li_previous) {
      $items[] = array(
        'class' => array('pager-previous'),
        'data' => $li_previous,
      );
    }

    // When there is more than one page, create the pager list.
    if ($i != $pager_max) {
      if ($i > 3) {
        $items[] = array(
          'data' => theme('pager_previous', array('text' => 1, 'element' => $element, 'interval' => ($pager_current - 1), 'parameters' => $parameters)),
        );
        $items[] = array(
          'class' => array('separator'),
          'data' => '|',
        );
      }
      elseif ($i > 1 && $i < 4) {
        for ($x = 1; $x < $i; $x++) {
          $items[] = array(
            'data' => theme('pager_previous', array('text' => $x, 'element' => $element, 'interval' => ($pager_current - $x), 'parameters' => $parameters)),
          );
        }
      }
      // Now generate the actual pager piece.
      for (; $i <= $pager_max; $i++) {
        if ($i <= $pager_last) {
          if ($i < $pager_current) {
            $items[$i] = array(
              'data' => theme('pager_previous', array('text' => $i, 'element' => $element, 'interval' => ($pager_current - $i), 'parameters' => $parameters)),
            );
          }
          if ($i == $pager_current) {
            $items[$i] = array(
              'data' => "<span>$i</span>",
            );
          }
          if ($i > $pager_current) {
            $items[$i] = array(
              'data' => theme('pager_next', array('text' => $i, 'element' => $element, 'interval' => ($i - $pager_current), 'parameters' => $parameters)),
            );
          }
        }
        else {
          if (empty($items[$i]) && $i%10 == 0 && $i <= $variables['other']->total_items && $i >= ($current_hundreds + 10) && $i <= ($current_hundreds + 90)) {
            $items[$i] = array(
              'data' => theme('pager_next', array('text' => $i, 'element' => $element, 'interval' => ($i - $pager_current), 'parameters' => $parameters)),
            );
          }
          if (empty($items[$i]) && $i%100 == 0 && $i <= $variables['other']->total_items && $i >= 100 && $i <= 900) {
            $items[$i] = array(
              'data' => theme('pager_next', array('text' => $i, 'element' => $element, 'interval' => ($i - $pager_current), 'parameters' => $parameters)),
            );
          }
          if (empty($items[$i]) && $i == $pager_max) {
            $items[$i] = array(
              'data' => theme('pager_next', array('text' => $i, 'element' => $element, 'interval' => ($i - $pager_current), 'parameters' => $parameters)),
            );
          }
        }
      }
    }
    // End generation.
    if ($li_next) {
      $items[] = array(
        'data' => $li_next,
      );
    }
    // Reset items.
    $items_copy = array();
    foreach ($items as $item) {
      $items_copy[] = $item;
    }
    $class[] = 'pagination';
    $class = implode(' ', $class);
    return "<div class='$class'>" .theme('item_list', array('items' => $items_copy, 'pager' => TRUE, 'attributes' => array('class' => array('pager')))) . '</div>';
  }
}

  /**
   * Returns HTML for the "previous page" link in a query pager.
   *
   * @param $variables
   *   An associative array containing:
   *   - text: The name (or image) of the link.
   *   - element: An optional integer to distinguish between multiple pagers on
   *     one page.
   *   - interval: The number of pages to move backward when the link is clicked.
   *   - parameters: An associative array of query string parameters to append to
   *     the pager links.
   *
   * @ingroup themeable
   */
function culturebox_pager_previous($variables) {
  $text = $variables['text'];
  $element = $variables['element'];
  $interval = $variables['interval'];
  $parameters = $variables['parameters'];
  global $pager_page_array;
  $output = '';

  // If we are anywhere but the first page
  if ($pager_page_array[$element] > 0) {
    $page_new = pager_load_array($pager_page_array[$element] - $interval, $element, $pager_page_array);

    // If the previous page is the first page, mark the link as such.
    if ($page_new[$element] == 0) {
      $output = theme('pager_first', array('text' => $text, 'element' => $element, 'parameters' => $parameters));
    }
    // The previous page is not the first page.
    else {
      $output = theme('pager_link', array('text' => $text, 'page_new' => $page_new, 'element' => $element, 'parameters' => $parameters));
    }
  }
  else {
    $output = "<span>$text</span>";
  }

  return $output;
}

/**
 * Returns HTML for the "next page" link in a query pager.
 *
 * @param $variables
 *   An associative array containing:
 *   - text: The name (or image) of the link.
 *   - element: An optional integer to distinguish between multiple pagers on
 *     one page.
 *   - interval: The number of pages to move forward when the link is clicked.
 *   - parameters: An associative array of query string parameters to append to
 *     the pager links.
 *
 * @ingroup themeable
 */
function culturebox_pager_next($variables) {
  $text = $variables['text'];
  $element = $variables['element'];
  $interval = $variables['interval'];
  $parameters = $variables['parameters'];
  global $pager_page_array, $pager_total;
  $output = '';

  // If we are anywhere but the last page
  if ($pager_page_array[$element] < ($pager_total[$element] - 1)) {
    $page_new = pager_load_array($pager_page_array[$element] + $interval, $element, $pager_page_array);
    // If the next page is the last page, mark the link as such.
    if ($page_new[$element] == ($pager_total[$element] - 1)) {
      $output = theme('pager_last', array('text' => $text, 'element' => $element, 'parameters' => $parameters));
    }
    // The next page is not the last page.
    else {
      $output = theme('pager_link', array('text' => $text, 'page_new' => $page_new, 'element' => $element, 'parameters' => $parameters));
    }
  }
  else {
    $output = "<span>$text</span>";
  }

  return $output;
}

/**
 * Returns HTML for a list or nested list of items.
 *
 * @param $variables
 *   An associative array containing:
 *   - items: An array of items to be displayed in the list. If an item is a
 *     string, then it is used as is. If an item is an array, then the "data"
 *     element of the array is used as the contents of the list item. If an item
 *     is an array with a "children" element, those children are displayed in a
 *     nested list. All other elements are treated as attributes of the list
 *     item element.
 *   - title: The title of the list.
 *   - type: The type of list to return (e.g. "ul", "ol").
 *   - attributes: The attributes applied to the list element.
 */
function culturebox_item_list($variables) {
  $items = $variables['items'];
  $title = $variables['title'];
  $type = $variables['type'];
  $attributes = $variables['attributes'];

  // Only output the list container and title, if there are any list items.
  // Check to see whether the block title exists before adding a header.
  // Empty headers are not semantic and present accessibility challenges.
  if (!empty($variables['pager']) && $variables['pager']) {
    $output = '<div>';
  }
  else {
    $output = '<div class="item-list">';
  }
  if (isset($title) && $title !== '') {
    $output .= '<h3>' . $title . '</h3>';
  }

  if (!empty($items)) {
    $output .= "<$type" . drupal_attributes($attributes) . '>';

    $num_items = count($items);
    foreach ($items as $i => $item) {
      $attributes = array();
      $children = array();
      $data = '';
      if (is_array($item)) {
        foreach ($item as $key => $value) {
          if ($key == 'data') {
            $data = $value;
          }
          elseif ($key == 'children') {
            $children = $value;
          }
          else {
            $attributes[$key] = $value;
          }
        }
      }
      else {
        $data = $item;
      }
      if (count($children) > 0) {
        // Render nested list.
        $data .= theme_item_list(array('items' => $children, 'title' => NULL, 'type' => $type, 'attributes' => $attributes));
      }
      if ($i == 0) {
        $attributes['class'][] = 'first';
      }
      if ($i == $num_items - 1) {
        $attributes['class'][] = 'last';
      }
      $output .= '<li' . drupal_attributes($attributes) . '>' . $data . "</li>\n";
    }
    $output .= "</$type>";
  }
  $output .= '</div>';
  return $output;
}

/**
 * Filter array of params
 */
function _culturebox_pager_params_filter() {

}

/**
 * Overrides default pager theme.
 */
function culturebox_pager_link($variables) {
  $text = $variables['text'];
  $page_new = $variables['page_new'];
  $element = $variables['element'];
  $parameters = $variables['parameters'];
  $attributes = $variables['attributes'];

  $page = isset($_GET['page']) ? $_GET['page'] : '';
  if ($new_page = implode(',', pager_load_array($page_new[$element], $element, explode(',', $page)))) {
    $parameters['page'] = $new_page;
  }

  $query = array();
  if (count($parameters)) {
    $query = drupal_get_query_parameters($parameters, array());
  }
  if ($query_pager = pager_get_query_parameters()) {
    $query = array_merge($query, $query_pager);
  }

  // Set each pager link title
  if (!isset($attributes['title'])) {
    static $titles = NULL;
    if (!isset($titles)) {
      $titles = array(
        t('« first') => t('Go to first page'),
        t('‹ previous') => t('Go to previous page'),
        t('next ›') => t('Go to next page'),
        t('last »') => t('Go to last page'),
      );
    }
    if (isset($titles[$text])) {
      $attributes['title'] = $titles[$text];
    }
    elseif (is_numeric($text)) {
      $attributes['title'] = t('Go to page @number', array('@number' => $text));
    }
  }

  $path = $_GET['q'];

  // Rebuild $path for Solr search paginator.
  if ($path == 'recherche') {
    if (!empty($query['keywords'])) {
      // @todo Remove accents.
      $path = 'recherche/' . $query['keywords'];
      // Remove param from GET string.
      unset($query['keywords']);
    }
    
    if (!empty($query['page']) && is_numeric($query['page'])) {
      $path .= '/' . $query['page'];
      unset($query['page']);
    }
  }
  else {
    if (empty($query['page'])) {
      $query['page'] = '0';
    }
  }

  $attributes['href'] = url($path, array('query' => $query));

  return '<a' . drupal_attributes($attributes) . '>' . check_plain($text) . '</a>';
}
