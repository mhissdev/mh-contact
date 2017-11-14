        <p>Please complete the form below:-</p>
        <form action="<?php echo $config['base_url']; ?>" method="post" accept-charset="utf-8" class="contact-form" id="contact-form">
            <input type="hidden" name="csrf_token" value="<?php echo(Token::generate()); ?>"> 
            <!-- Contact Name -->
            <label for="contact_name">Your Name:</label>
            <input type="text" id="contact_name" name="contact_name" placeholder="Enter your name" maxlength="64" required>
            <!-- Contact Email -->
            <label for="contact_email">Your Email:</label>
            <input type="email" id="contact_email" name="contact_email" placeholder="Enter your email"  maxlength="64" required>
            <!-- Contact Message -->
            <label for="contact_message">Your Message:</label>
            <textarea id="contact_message" name="contact_message" rows="4" placeholder="Enter your message"  maxlength="1000" required></textarea>
            <!-- Google recaptcha-->
            <div class="g-recaptcha" data-callback="onRecaptchaSuccess" data-expired-callback="onRecaptchaExpired" data-sitekey="<?php echo $config['captcha_site_key']; ?>"></div>
            <!-- Recaptcha hidden text input -->
            <input type="text" id="recaptcha_hidden" required>
            <!-- Submit button -->
            <input type="submit" name="contact_submit" value="Send Message" class="btn">
        </form>
        <?php
            // Output validation errors
            if($contact->hasValidationErrors() == true)
            {
                echo '<p>Opps did you miss something?</p>';
                echo $contact->getValidationErrorsHTML();
            }
        ?>