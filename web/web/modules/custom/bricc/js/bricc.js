(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.briccApplicant = {
    attach: function(context, settings) {
      once('briccApplicant', '.applicant-ui', context)
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

  Drupal.behaviors.briccIdCardType = {
    attach: function (context, settings) {
      once('briccIdCardType', '.form-item--select-idcardtype', context)
        .forEach(function (element) {
          $(element).on('change', '[data-drupal-selector="edit-select-idcardtype"]', function (e) {
            let selectedIdCardType = e.target.value;
            $(element).closest('form').find('[data-drupal-selector="edit-field-idcardtype-0-value"]').val(selectedIdCardType);
          })
        });
    }
  }

  Drupal.behaviors.briccIndex = {
    attach: function (context, settings) {
      const paths = [
        '/admin/bricc',
        '/admin/bri/location',
      ];
      const roles = [
        'admin_main_website',
        'admin_credit_card',
      ];
      once('briccIndex', 'body.authenticated', context)
        .forEach(function (element) {
          const currentPath = window.location.pathname;
          const hasRoleClass = roles.some(role => $('body').hasClass(role));
          if (paths.includes(currentPath) && hasRoleClass) {
            $('.block-local-tasks-block').addClass('hidden');
            $('.menu-item__system-admin_structure').hide();
          }
        });
    }
  }
})(jQuery, Drupal, drupalSettings);
