(function ($, Drupal, drupalSettings) {
  /**
   * Encrypt data using a public key.
   *
   * @param {string} publicKeyPem - Public key in PEM format.
   * @param {string} data - The data to encrypt.
   * @returns {Promise<string>} Encrypted data in Base64 format.
   */
  async function encryptWithPublicKey(publicKeyPem, data) {
    // Your public key (make sure it's a valid PEM public key)
    var publicKey = publicKeyPem;

    var encrypt = new JSEncrypt();
    encrypt.setPublicKey(publicKey);

    var encrypted = encrypt.encrypt(data);
    console.log("Encrypted:", encrypted);

    // Send encrypted string to your PHP server via AJAX or form submission
    return encrypted;
  }

  Drupal.behaviors.encryptPasswordOnSubmit = {
    attach: function (context, settings) {
      const loginForm = context.querySelector('[data-drupal-selector="user-login-form"]');

      if (loginForm) {
        once('encrypt-password', loginForm).forEach(function (form) {
          form.addEventListener('submit', async function (event) {
            const passwordField = form.querySelector('[data-drupal-selector="edit-pass"]');
            const encryptedPasswordField = form.querySelector('[data-drupal-selector="edit-encrypted-password"]');

            if (passwordField) {
              const publicKeyPem = loginForm.querySelector('[data-drupal-selector="edit-pubkey"]').value;

              try {
                event.preventDefault();

                // Encrypt the password
                const encryptedPassword = await encryptWithPublicKey(publicKeyPem, passwordField.value);

                // Replace the password field random string
                passwordField.value = (Math.random() + 1).toString(36).substring(2);

                // Provide encrypted password value
                encryptedPasswordField.value = encryptedPassword;

                form.submit();
              }
              catch (error) {
                console.error("Password encryption failed:", error);
                // Prevent form submission if encryption fails
                event.preventDefault();
              }
            }
          });
        });
      }
    },
  };
})(jQuery, Drupal, drupalSettings);
