<?php
// Include files
require_once('../app/config/config.php');
require_once('../app/classes/Contact.php');
require_once('../app/classes/Token.php');

// Start session
session_start();

// Verify csrf token
Token::verify();

// Create new Contact instance
$contact = new Contact($config);

// Process POST data for contact form
$contact->processPostData();

?>
<!doctype html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Enquiry</title>
    <link rel="stylesheet" href="<?php echo $config['base_url']; ?>css/parsley.css">
    <link rel="stylesheet" href="<?php echo $config['base_url']; ?>css/style.css">
</head>
<body>
    <div class="container">
        <h3>Make an Enquiry</h3>
        <?php 
            // Check for successful form submission
            if($contact->getSubmitSuccess() == false)
            {
                // Render form
                require('../app/forms/contactForm.php');
            }
            else
            {
                // Form submitted OK
                echo '<h4>Thank you for your message</h4>';
                echo $contact->getEmailTextHTML();
            }
        ?>
    </div>
    <!-- JQuery -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- reCAPTCHA -->
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <!-- Parsley -->
    <script src="<?php echo $config['base_url']; ?>js/parsley.min.js"></script>
    <script>
        // ReCAPTCHA Callback on successful completion
        function onRecaptchaSuccess()
        {
            // Give recaptcha hidden text a value to allow parsley to validate
            $('#recaptcha_hidden').val('success');
        }

        // ReCAPTCHA Callback on expired
        function onRecaptchaExpired()
        {
            // Remove value from hidden text field
            $('#recaptcha_hidden').val('');
        }

        $(document).ready(function(){
            // Set inline validation for input and textarea fields
            $('#contact-form input').attr('data-parsley-trigger', 'focusout');
            $('#contact-form textarea').attr('data-parsley-trigger', 'focusout');

            // Set regex pattern for name field
            // This may not be good for people with international characters in their name
            // $('#contact_name').attr('data-parsley-pattern', "/^[-'a-zA-Z\s]*$/");

            // Set custome error messages
            $('#contact_name').attr('data-parsley-required-message', 'Please enter your name');
            $('#contact_email').attr('data-parsley-required-message', 'Please enter a valid email address');
            $('#contact_message').attr('data-parsley-required-message', 'Please enter your message');
            $('#recaptcha_hidden').attr('data-parsley-required-message', 'Please ensure you are human');

            // Initiate Parsley
            $('#contact-form').parsley();
        });
    </script>
</body>