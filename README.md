# Customer Contact Form
The objective of the task was to create a contact form to allow customers to make enquiries to a retailer.
## Usage Instructions
The application requires PHP and a MySQL database to store enquiry records. Before running the contact form you will need to create a MySQL database with full read and write permissions. Furthermore, the application will need to be configured. The configuration settings are stored in the ‘config.php’ file which can be found in the ‘mh-contact/app/config’ directory.

The configuration settings are as follows:-

* base_url: The URL to the public directory of the application (For example ‘http://localhost/mh-contact/public/’). Please ensure there is a trailing slash!
* captcha_site_key: Google reCAPTCHA site key (The provided default key should be fine for testing purposes)
* captcha_secret_key: Google reCAPTCHA secret key (The provided default key should be fine for testing purposes)
* db_host: Database host (Please use 127.0.0.1 instead of localhost)
* db_name: The name of the database
* db_user: Database username
* db_password: Database password
