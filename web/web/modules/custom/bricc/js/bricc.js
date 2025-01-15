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

          // Validate date field
          const startDateInput = element.querySelector('[data-drupal-selector="edit-startdate"]');
          const endDateInput = element.querySelector('[data-drupal-selector="edit-enddate"]');

          if (startDateInput && endDateInput) {
            // Event listener for start date changes
            startDateInput.addEventListener('change', function () {
              const startDate = new Date(this.value);

              if (endDateInput.value && new Date(endDateInput.value) < startDate) {
                endDateInput.value = ''; // Reset end date if it's before the start date
              }

              endDateInput.min = this.value; // Set minimum for end date
            });

            // Event listener for end date changes
            endDateInput.addEventListener('change', function () {
              const endDate = new Date(this.value);

              if (startDateInput.value && new Date(startDateInput.value) > endDate) {
                startDateInput.value = ''; // Reset start date if it's after the end date
              }

              startDateInput.max = this.value; // Set maximum for start date
            });
          }

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
          else if (hasRoleClass) {
            // $('.menu-item__system-admin_structure').hide();
          }
        });
    }
  }

  Drupal.behaviors.briCardGroupNav = {
    attach(context, settings) {
      const nodeForm = once('cardGroupNav', '.node-form', context)
      nodeForm.forEach(function (element) {
        const paragraphLabels = element.querySelectorAll('.paragraph-type-label');
        paragraphLabels.forEach(function (elem) {
          const label = elem.textContent;
          if (label === 'Card group nav') {
            const labelWrap= elem.closest('.paragraph-top');
            const editButton = labelWrap.querySelector('.paragraphs-icon-button-edit')
            editButton.classList.add('bri-hidden');
          }
        });
      });
    }
  }
})(jQuery, Drupal, drupalSettings);
