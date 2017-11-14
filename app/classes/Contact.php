<?php
/*
* Contact.php
* Provides main logic for the contact form
*/

// Include base class
require_once('Database.php');

class Contact extends Database{

    // Configuration - associative array
    private $config;

    // Form successfully submitted by user
    private $submitSuccess = false;

    // Array of validation errors
    private $validationErrors = [];

    // Contact name
    private $contactName;

    // Contact email
    private $contactEmail;

    // Contact message
    private $contactMessage;

    // Email body text
    private $emailTextHTML = '';

    /**
    * Constructor
    * @param array
    */
    public function __construct(&$config)
    {
        // Call parent constructor
        parent::__construct($config);

        // Set configuration
        $this->config = $config;
    }


    /**
    * Process POST data from contact form
    */
    public function processPostData()
    {
        // Check we have POST data from contact form
        if(isset($_POST['contact_submit']) && !empty($_POST['contact_submit']))
        {
            // Set values from POST global
            $this->contactName = isset($_POST['contact_name']) ? trim($_POST['contact_name']) : '';
            $this->contactEmail = isset($_POST['contact_email']) ? trim($_POST['contact_email']) : '';
            $this->contactMessage = isset($_POST['contact_message']) ? trim($_POST['contact_message']) : '';

            // Validate form fields
            $this->validateForm();

            // Check successful form submission
            if($this->hasValidationErrors() === false)
            {
                // Successful submission
                $this->submitSuccess = true;

                // Build email message text
                $this->buildEmailTextHTML();

                // Insert message details into database
                $this->insert();

                // Send email
                $this->sendMail();
            }
        }
    }


    /**
    * Determine if validation errors have occured after form submission
    * @return bool
    */
    public function hasValidationErrors()
    {
        return count($this->validationErrors) > 0 ? true : false;
    }


    /**
    * Returns HTML unordered list of validation errors
    * @return string
    */
    public function getValidationErrorsHTML()
    {
        // Build HTML unordered list
        $strHTML = '<ul class="error">';

        // Loop through balidation errors
        foreach($this->validationErrors as $error)
        {
            // Add list item
            $strHTML .= '<li>' . $error . '</li>';
        }

        // End unordered list
        $strHTML .= '</ul>';

        // Return HTML
        return $strHTML;
    }


    /**
    * Get successful submission status
    * @return bool
    */
    public function getSubmitSuccess()
    {
        return $this->submitSuccess;
    }


    /**
    * Gets the HTML text to output enquiry
    */
    public function getEmailTextHTML()
    {
        return $this->emailTextHTML;
    }


    /**
    * Validates the contact form
    */
    private function validateForm()
    {
        // Validate name field
        $length = strlen($this->contactName);
        if($length == 0) $this->validationErrors[] = 'Name is a required field';
        if($length > 64) $this->validationErrors[] = 'Name must be less than 65 characters long';

        /*
        // This may not be good for people with international characters in their name
        if(preg_match("/^[-'a-zA-Z\s]*$/", $this->contactName) == false)
        {
            $this->validationErrors[] = 'Name contains illegal characters';
        }
        */

        // Validate email field
        if(filter_var($this->contactEmail, FILTER_VALIDATE_EMAIL) == false)
        {
            $this->validationErrors[] = 'A valid email address is required';
        }

        // Validate message
        $length = strlen($this->contactMessage);
        if($length == 0) $this->validationErrors[] = 'Message is a required field';
        if($length > 999) $this->validationErrors[] = 'Name must be less than 1000 characters long';

        // Verify recaptcha
        if($this->verifyRecaptcha() == false)
        {
            // Captcha failed - add validation error
            $this->validationErrors[] = 'Please ensure you are human';
        }
    }


    /**
    * Verify recaptcha
    * @return bool
    */
    private function verifyRecaptcha()
    {
        if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response']))
        {
            // Get user IP
            $ip = '';

            if(isset($_SERVER['REMOTE_ADDR']))
            {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            // Build URL string
            $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $this->config['captcha_secret_key'];
            $url .= '&response=' . $_POST['g-recaptcha-response'];
            $url .= '&remoteip=' . $ip;

            // Get response from Google API
            $response = json_decode(file_get_contents($url), true);
            
            if($response["success"] === true)
            {
                // Successful verifiction
                return true;
            }

        }

        // Unable to verify captcha
        return false;
    }


    /**
    * Builds and escapes the text to send in email and output to user
    */
    private function buildEmailTextHTML()
    {
        $this->emailTextHTML = '<p>The following enquiry has been made:-</p>';

        // Add name
        $this->emailTextHTML .= '<p><strong>Name: ' . htmlspecialchars($this->contactName, ENT_QUOTES, 'utf-8');

        // Add email address
        $this->emailTextHTML .= ', Email: ' . htmlspecialchars($this->contactEmail, ENT_QUOTES, 'utf-8') . '</strong></p>';

        // Add message
        $this->emailTextHTML .= '<p>' . nl2br(htmlspecialchars($this->contactMessage, ENT_QUOTES, 'utf-8')) . '</p>';
    }


    /**
    * Inserts a new record into database
    */
    private function insert()
    {
        // Connect to database
        $this->connect();

        // Build enquiries table
        $this->migrate_up();

        // Prepare SQL statement
        $this->prepare('INSERT INTO enquiries(enquiry_name, enquiry_email, enquiry_message, enquiry_timestamp) VALUES(?, ?, ?, ?);');

        // Execute
        $this->execute(array($this->contactName, $this->contactEmail, $this->contactMessage, time()));
    }

    /**
    * Creates the database enquiries table
    */
    private function migrate_up()
    {
        // Build SQL
        $sql = "CREATE TABLE IF NOT EXISTS enquiries (";
        $sql .= "enquiry_id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,";
        $sql .= "enquiry_name varchar(255) NOT NULL,";
        $sql .= "enquiry_email varchar(255) NOT NULL,";
        $sql .= "enquiry_message text NOT NULL,";
        $sql .= "enquiry_timestamp bigint(20) NOT NULL)";

        // Prepare statement
        $this->prepare($sql);

        // Execute
        $this->execute();
    }


    /**
    * Send an email to customer and webmaster
    */
    private function sendMail()
    {
        // Additional headers
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: <no-reply@example.com>\r\n";
        $headers .= "Bcc: <enquiries@example.com>\r\n";

        // Send mail
        mail($this->contactEmail, 'Customer Enquiry', $this->emailTextHTML, $headers);
    }
}