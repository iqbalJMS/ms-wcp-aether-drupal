(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.briccApplicant = {
    attach: function(context, settings) {
      once('briccApplicant', '[data-drupal-selector="views-exposed-form-applicant-page-1"]', context)
        .forEach(function (element) {

          let selectedFilterType = $(element).find('[data-drupal-selector="edit-filtering-type"]').val();
          Drupal.behaviors.briccApplicant.setFilterType(selectedFilterType, element);

          // Your DOM manipulation code here
          $(element).on('change', '[data-drupal-selector="edit-filtering-type"]', function (e) {
            Drupal.behaviors.briccApplicant.setFilterType(e.target.value, element);
          });

      });
    },
    init: function (e) {

    },
    setFilterType: function (filterType, form) {
      let filterByDate = [
        '.form-item--startdate',
        '.form-item--enddate',
        '.form-item--jeniskartu',
      ];
      let filterByName = [
        '.form-item--name',
        '.form-item--phone',
        '.form-item--tgllahir',
      ];

      if (filterType === 'date') {
        filterByName.forEach(function (selector) {
          $(form).find(selector).hide();
        })
        filterByDate.forEach(function (selector) {
          $(form).find(selector).show();
        })
      }
      else {
        filterByName.forEach(function (selector) {
          $(form).find(selector).show();
        })
        filterByDate.forEach(function (selector) {
          $(form).find(selector).hide();
        })
      }
    },
  }
})(jQuery, Drupal, drupalSettings);
