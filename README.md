# php-ez-template
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Structure template with Laravel like Router and some stuff. Meant for personal usage and prototypes since it lacks important features.

**[Explore the repository »](https://github.com/egl136/php-ez-template)**

· [Report Bug](https://github.com/egl136/php-ez-template/issues)
· [Request Feature](https://github.com/egl136/php-ez-template/issues)


## Table of Contents

- [About](#about)
  - [Views Disclaimer](#views-disclaimer)
- [Features](#features)
- [Installation](#installation)
  - [Prerequisites](#prerequisites)
  - [Steps](#steps)
- [Configuration](#configuration)
  - [Edit httpd.conf](#edit-httpdconf)
    - [VirtualHost](#virtualhost)
    - [hosts](#hosts)
  - [.htaccess](#htaccess)
  - [Verify](#verify)
- [Structure](#structure)
- [Usage](#usage)
  - [The sample model](#the-sample-model)
  - [The sample controller](#the-sample-controller)
  - [Adding routes](#adding-routes)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

---

## About
First important project uploaded to my github.
This template is meant to be easy to implement.
Literally for my personal projects and for anyone who gets comfortable with this.
Build everything on the Models and Controllers, and manage the routes with the Router(just add the routes).
Inspired on Laravel.

### Views Disclaimer
This template is not meant for views rendering, so support is limited to send params with `View::render()`

---

## Features
- Router: Laravel like easy to follow router.
- Database Handler: Quick access to Database Handler.
- MVC: Based on MVC.

---

## Installation

To get a local copy up and running, follow these simple steps.



### Prerequisites

Ensure you have Git installed on your system. If not, you can download it from [git-scm.com](https://git-scm.com/downloads).
You will also need:
- **PHP (version 8.1 or higher recommended)**
- **Apache Web Server** with `mod_rewrite` enabled.
### Steps

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/egl136/php-ez-template.git
    ```
    or
    ```bash
    gh repo clone egl136/php-ez-tamplate
    ```
2.  **Navigate into the project directory:**
    ```bash
    cd php-ez-template
    ```
---

## Configuration
This was only tested on Linux (arch). However, I think there are some ways to make this work on Windows if needed.  
### Edit ```httpd.conf```
```bash
sudo nano /etc/httpd/conf/httpd.conf
```
Uncomment ```LoadModule rewrite_module modules/mod_rewrite.so```, so the line must pass from this:  

```bash
#LoadModule rewrite_module modules/mod_rewrite.so
```
to this:
```bash
LoadModule rewrite_module modules/mod_rewrite.so
```
#### VirtualHost
For this step, you must have a public access directory. This template already has one, but if not,  
you should create it and place the ```index.php``` and ```.htaccess``` on it. You must change the ```require_once``` directory if  
that happens.  
Once you have your public directory(where your ```index.php``` and ```.htaccess``` are), add the next on the ```httpd.conf```:
```bash
<VirtualHost *:80>
  DocumentRoot "/your/app/public"
  ServerName yourdomain.sub

  <Directory "/your/app/public">
    AllowOverride All
    Require all granted
  </Directory>
</VirtualHost>
```
Name the ServerName as you like.  
If the public directory is the default, then change ```your/app/public``` for your http path, followed by ```/template/public```.  
For arch, the root should be: ```/srv/http/template/public```.
#### ```hosts```
Once ```httpd.conf``` is edited, ```hosts``` file must be edited too:
```bash
sudo nano /etc/hosts
```
Add this line:
```bash
127.1.1.1  yourdomain.sub
```
Once saved, restart httpd:
```bash
sudo systemctl restart httpd # For systemd-based systems (Arch, CentOS 7+, Ubuntu 16.04+)
```
or
```bash
sudo service apache2 restart # For older Debian/Ubuntu
```
Change the IP if desired, as well as the domain name. Just keep in mind that the domain must be the same as the one on the ```httpd.conf``` VirtualHost.
### ```.htaccess```
Make sure that ```.htaccess``` is exactly like this:  
```bash
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

RewriteRule ^ index.php [QSA,L]
```
This makes server to always redirect index.php if url path is not found. So it's used for this template Router
### Verify
Check ```http://yourdomain.sub/sample```. This should print a json string on screen. If not, verify all the configuration steps.

---

## Structure
```
├── template/
│   ├── App/
│   │   ├── Core/
│   │   │   ├── Classes/
│   │   │   │   ├── Controller.php
│   │   │   │   ├── Model.php
│   │   │   │   └── View.php
│   │   │   ├── Controllers/
│   │   │   ├── Models/
│   │   │   └── Router/
│   │   │       ├── api.php
│   │   │       ├── Router.php
│   │   │       └── web.php
│   ├── Config/
│   │   └── Services
│   │       └── Database
│   ├── public/
│   │   ├── index.php
│   │   └── .htaccess
│   ├── storage/
└── └── README.md
```

---

## Usage
The template includes 1 Model and 1 Controller samples for the understanding.  
This template doesn't use composer, so autoload is not included(but can be added).
### The Sample Model
Located at ```App/Core/Models/SampleModel.php```
```php
<?php 
namespace App\Core\Models;
use App\Core\Classes\Model;

require_once __DIR__ . '/../Classes/Model.php';

class SampleModel extends Model
{
	function __construct(protected String $model_name = "sample")
	{

	}
}
```

### The Sample Controller
Located at ```App/Core/Controllers/SampleController.php```
```php
<?php
namespace App\Core\Controllers;

use App\Core\Classes\Controller;
use App\Core\Models\SampleModel;
require_once __DIR__ . '/../Classes/Controller.php';
require_once __DIR__ . '/../Models/SampleModel.php';


class SampleController extends Controller
{
	function __construct(protected String $model_name = "sample")
	{
		$sampleModel = new SampleModel();
		$this->set_model($sampleModel);
	}
	public function getAll()
	{
		$data = [
			"Sample1"=>"sup",
			"Sample2"=>"samples",
			"Sample3"=>"are",
			"Sample4"=>"working"
		];
		echo json_encode($data);
		return json_encode($data);
	}

	public function findId($id)
	{
		echo json_encode([$id]);
		return json_encode([$id]);
	}
}
```
### Adding routes
To add a new route, just add it to the ```App/Core/Router/api.php``` or ```App/Core/Router/web.php```.  
```get()``` expects the first parameter to be a custom route. The second one expects a single string,  
wich contains the controller name and the function name, separated by @.  
Then, using the sample
```php
<?php
use App\Core\Router\Router;

$router = new Router();
$router->get('/sample', 'SampleController@getAll');
$router->get('/sample/{id}', 'SampleController@findId');

$router->dispatch($_SERVER['REQUEST_URI']);
```
the expected routes are ```your-domain.com/sample```, wich calls to the ```getAll()``` from the ```SampleController```; and  
```your-domain.com/sample/{id}```, wich calls to the ```findId()``` from the ```SampleController```. This last one is a  
dynamic route.

---
## Contributing

We welcome contributions of all kinds! Whether you're fixing a bug, adding a new feature, improving documentation, or just providing feedback, your help is appreciated.

Please take a moment to review this document to make the contribution process as smooth as possible for everyone.

### How to Contribute

1.  **Report Bugs:**
    * If you find a bug, please open an [issue](https://github.com/egl136/php-ez-tamplate/issues) on GitHub.
    * Provide a clear and concise description of the bug, including steps to reproduce it.
    * Mention your operating system, browser, and any relevant environment details.

2.  **Suggest Enhancements/Features:**
    * Have an idea for a new feature or an improvement? Open an [issue](https://github.com/egl136/php-ez-tamplate/issues) to discuss it first.
    * Clearly describe the proposed feature and its potential benefits.

---

## License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

---

## Contact

Eduardo Guevara Lozano - eduardoguevaralozano1@gmail.com

Project Link: [https://github.com/egl136/php-ez-tamplate/](https://github.com/egl136/php-ez-tamplate/)
