# PHP-authit
Simple PHP with MySQL account authentication to protect pages, includes an automatic creation via a form (`index.html`) and multiple methods of login protection.

![Login page no captcha](https://raw.githubusercontent.com/cp6/PHP-authit/master/screens/login%20no%20captcha.png)

## Features

1. Password hashing.

2. Self generating captcha (optional).

3. 5 wrong login attempts sees 10 minute cool down.

4. Styled with Bootstrap 4.1.3

## Who is this for?

Anyone that needs an easy to install/setup login system to protect web pages such as dashboards, information pages, hubs or control panels from prying eyes.

## Installation

1. Unzip this project into your directory or sub directory of choice

2. Have a MySQL account with database/table create ability

3. Navigate to where you placed these files

4. `index.html` should load, fill in the form and submit

5. If done right you will be at the default login page now 

6. Use the username and password entered in the form to login

To protect pages simply put at the start of the page:

```php
ob_start();
session_start();
if (!isset($_SESSION['user'])) {
    //User not logged in
    //do a redirect to login page (index.php)
} else {
    //show page stuff
}
```


### Screenshots

![Login page](https://raw.githubusercontent.com/cp6/PHP-authit/master/screens/login.png)
![No captcha submitted](https://raw.githubusercontent.com/cp6/PHP-authit/master/screens/login%20please%20enter%20captcha.png)
![Captcha wrong](https://raw.githubusercontent.com/cp6/PHP-authit/master/screens/login%20captcha%20was%20incorrect.png)
![Username or Password wrong](https://raw.githubusercontent.com/cp6/PHP-authit/master/screens/login%20user%20or%20pass%20incorrect.png)