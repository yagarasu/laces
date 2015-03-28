# laces
PHP Templating engine. Part of the Crinoline Framework

## Current version
The current version is 1.0.0

## Laces is...
Laces is a PHP templating engine built at first inside the [Crinoline Framework](https://github.com/yagarasu/crinoline) v0.1. The parser was tied to the framework so tight, it was difficult to use it as a library. That´s why I decided to extract and revamp the engine from scratch.

For this first version, I took just the basic idea and refactored the whole concept.

The most important difference about this version of Laces is the use of a custom expression parser, unlike the v0.1 which used the eval() function --exposing the server for a little bit of functionality--.

## How to use it
In your PHP client you must include or require the Laces.class.php (TO DO: CHECK):
```php
require 'laces/Laces.class.php';
```
Then you are ready to use Laces.

### Laces class
The Laces class ties everything up and provides a simple interface for you to use.

#### render($string)
This method takes the string, parses it, then prints the result. This is one of the most used methods.

```php
$laces = new Laces();
$laces->render(' ... laces template ... ');
```
Would print the template

#### parse($string)
This method takes the string and parses it, then returns the result. Useful if you want to save the result or you want to treat the result yourself without printing it.

```php
$laces = new Laces();
mySaveFile($laces->parse(' ... laces template ... '), 'filename.txt');
```
Would use a custom function to save the result inside `filename.txt`.

#### loadAndRender($url) / loadAndParse($url)
This method gets the URL, loads the template, parses it and prints or returns the result.
Useful when you have your views inside a folder.
The convention for Laces Template files is `filename.ltp` or `filename.ltp.php`.

```php
$laces = new Laces();
$laces->loadAndRender('templates/home.ltp');
```
Would load the `home.ltp` file and parse it on screen.

### Context class
The other important class is the Context, which allows us to save variables before parsing.

--- to do ---

## Versions
* 1.0.0
  * The very first release.
  * Refactored from the View class of Crinoline.
  * Custom expression parser to replace the eval() function.
  * Context can handle multiple levels of nesting, constants, evaluation results (IDs) and hook queues.
  * Available Laces: for, foreach, hook, if-else, include and replacer.
  * Expression tester (used while debugging the recursive descent parser for expressions).
* 0.1.0
  * Here for illustrative purposes. 
  * It was hard to use it as a library.
  * Never released on its own.

## License
GNU LGPL v3 Read LICENSE for details.