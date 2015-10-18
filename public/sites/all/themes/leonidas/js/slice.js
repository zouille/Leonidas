/**
 * This file should be added only in slice.
 */
Drupal = {};
Drupal.behaviors = {};

jQuery(function(){
  Drupal.behaviors.culturebox.attach(this);
  if (Drupal.behaviors.cultureboxDiaporama != undefined) {
    Drupal.behaviors.cultureboxDiaporama.attach(this);
  }
});


