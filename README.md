# OVERVIEW
Simple PHP Crawler created for fun (Its not that useful). This crawler is able to crawle any website including internal links and display different assets for those website in JSON format. You can extend the retrieved elements and display whatever data you want (see #EXTENDING section)



# INSTALLATION
To install this Project, you should have `composer` and `npm` installed in your PC. After that, go to main project directory and install `composer` and `npm` packages using :- 



1. <code>composer install</code>
2. <code>npm install</code>
3. You can view the website through `/web` directory (http://localhost/awesome-crawler-path/web)



# TECHNICAL OVERVIEW
This project is based on a custom created MVC structure. However, its not 100% custom code. Some other libraries used to make my life easier :- 

1. <a href="http://getbootstrap.com">Bootstrap</a> : Used for handling the front-end styling
2. <a href="https://github.com/twigphp/Twig">Twig</a> : As a template engine
3. <a href="https://github.com/noahbuscher/Macaw">Macaw</a> : As a base router for this project (See `include/Router.php` for implementation)
4. <a href="https://github.com/electrolinux/phpquery">phpQuery</a> : For parsing HTML for different crawled websites markups
5. <a href="https://github.com/php-curl-class/php-curl-class">PHP Curl</a> : HTTP requests Ninja



# FOLDERS STRUCTURE
This project is more inspired by Symfony folders structure with some customizations. Lets see how it look :- 

1. `/app` : Most of application login including any custom functions should be done through this file
  1.1 `/app/controllers` : Contain all controller functions that handle different routes. You can define a new route from `/config/routes.php` and then create related Controller class in this directory. 
  1.2 `/app/elements` : Here you can define your own crawling elements that will be displayed from the parsed website. You can define a new element form `config/elements.php` and then create related class in this directory. However, this will be explained [below](#EXTENDING-ELEMENTS)
  1.3 `/app/helpers` : Custom helpers functions that used in your controller to achieve a specific task (E.g JsonHelper class that help me parsing different JSON output)
  1.4 `/app/styles` : Any custom styles should be goes here (You should be familiar with sass). After doing ur awesome styling changes, you should compile your changes by going to the root directory of the app and implement `gulp sass` command
  1.5 `/app/views` : Any views associated with your controller (You should be familiar with twig)

2. `/config` : Most of configuration required to define routes, elements, database connection (For Future use)..etc
  2.1 `/config/elements.php` : You can define your custom elements here that display a specific information from the crawled website.
  2.2 `/config/routes.php` : Here you should define your route, then you have to implement the controller inside `/app/controllers`
  
3. `/includes` : Here I define all the handlers used across my application. It should be for core handlers.

4. `/vendors` : any external libraries (You should see this directory after installing composer packages)

5. `/web` : Add any front-end assets and libraries
  5.1 `/web/scripts` : Here I add the custom JS files 
  5.2 `/web/images` : No need to explain
  5.3 `/web/lib` : External JS Libraries
  5.4 `/web/styles` : Compiled styling (This shouln't not be touched. any custom styling should be done through `/app/styles`)


# EXTENDING ELEMENTS
I built this project to be more dynamic and easily to extend. you can even parse your custom output from the crawled pages. Lets say for example that you want to display all the `Titles` for the page by reading the text inside <h1> and <h2> tages. We can achieve this by doing the following :- 

1. Define a new element in `/config/elements.php`. Assuming that the new element name will be `titles` then the definition should be like this 
```
  'titles' => array(
    'group' => 'text', // The group of the final JSON output
    'class' => 'TitlesElement',
    'description' => 'Display Titles Files',
  ),
```

So the final output of the page should be like this 
```
<?php

/**
 * @file
 * Define elements as an array.
 */

$elements = array(
  'images' => array(
    'group' => 'assets',
    'class' => 'ImagesElement',
    'description' => 'Display Images Files',
  ),
  'js' => array(
    'group' => 'assets',
    'class' => 'JsElement',
    'description' => 'Display JS Files',
  ),
  'css' => array(
    'group' => 'assets',
    'class' => 'CssElement',
    'description' => 'Display CSS Files',
  ),
  'titles' => array(
    'group' => 'text',
    'class' => 'TitlesElement',
    'description' => 'Display Titles Files',
  ),
);
```


2. Create `/app/elements/TitlesElement.php` file that define `TitlesElement` class. This class should define the static funciton `output`. Here is how the final class should looks like :

```
<?php

/**
 * @file
 * Define new element of type "titles".
 */

use \phpQuery;

/**
 * Get titles for current markup.
 */
class TitlesElement {

  /**
   * Get titles  for current markup.
   *
   * @return array
   *   Array of titles files attached to current website marup.
   */
  public static function output($markup) {
    $titles_array = [];
    $page_markup = phpQuery::newDocumentHTML($markup, $charset = 'utf-8');
    $titles = pq("h1, h2", $page_markup); // Custom Selector

    foreach ($titles as $title_item) {
      if (!empty(pq($title_item)->text())) {
        $titles_array[] = pq($title_item)->text(); // Display text inside titles
      }
    }

    return array_merge(array_unique($titles_array), array());
  }

}

```

You are done now from defining your custom element. You should see the new defined element in search filter and output now. 
![New Elements](http://i.imgur.com/5eoDolk.png)


Heppy crawling 




