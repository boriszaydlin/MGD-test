(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.myModal = {
    attach: function(context) {
      $(context).find('body')
        .once('modal')
        .each(function () {
          $(".modal-trigger").dialog({
          });
        });
    }
  };

})(jQuery, Drupal);