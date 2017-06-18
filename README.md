# OVERVIEW
Simple PHP Crawler created for fun (Its not that useful). This crawler is able to crawle any website including internal links and display different assets for those website in JSON format. You can extend the retrieved elements and display whatever data you want (see #EXTENDING section)



# INSTALLATION
To install this Project, you should have `composer` and `npm` installed in your PC. After that, go to main project directory and install `composer` and `npm` packages using :- 


1. <code>composer install</code>
2. <code>npm install</code>



# TECHNICAL OVERVIEW
This project is based on a custom created MVC structure. However, its not 100% custom code. Some other libraries used to make my life easier :- 

1. <a href="http://getbootstrap.com">Bootstrap</a> : Used for handling the front-end styling
2. <a href="https://github.com/noahbuscher/Macaw">Macaw</a> : As a base router for this project (See `include/Router.php` for implementation)
3. <a href="https://github.com/electrolinux/phpquery">phpQuery</a> : For parsing HTML for different crawled websites markups
4. 


