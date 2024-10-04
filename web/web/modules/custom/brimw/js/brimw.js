(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.brimwLocationType = {
    attach: function (context, settings) {
      once('brimwIdLocationType', '.form-item--select-idlocationtype', context)
        .forEach(function (element) {
          $(element).on('change', '[data-drupal-selector="edit-select-idlocationtype"]', function (e) {
            let selectedIdLocationType = e.target.value;
            $(element).closest('form').find('[data-drupal-selector="edit-field-location-type-0-value"]').val(selectedIdLocationType);
          })
        });
    }
  }
})(jQuery, Drupal, drupalSettings);
