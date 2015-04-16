# Laces
PHP Templating engine. Part of the Crinoline Framework

## Current version
The current version is 1.0.2

## Laces is...
Laces is a PHP templating engine built at first inside the [Crinoline Framework](https://github.com/yagarasu/crinoline) v0.1. The parser was tied to the framework so tight, it was difficult to use it as a library. That´s why I decided to extract and revamp the engine from scratch.

For this first version, I took just the basic idea and refactored the whole concept.

The most important difference about this version of Laces is the use of a custom expression parser, unlike the v0.1 which used the eval() function --exposing the server for a little bit of functionality--.

## How to use it
In your PHP client you must include or require the autoloader and register it:
```php
// Define the correct directory. Optional. Default set to 'laces/'.
define('LACES_ROOT', '../../laces/');
// Require the autoloader
require LACES_ROOT . 'autoloader.inc.php';
// Register it
laces_register_autoloader();
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
The other important class is the Context, which allows us to save variables before parsing and to access this runtime data. It is bound to the laces parser in the Laces class constructor.

```php
$context = new Context();
$laces = new Laces($context);
```

#### set($query, $value)
Sets a new variable or constant to the context. Just use the correct format and the class makes it´s magic.

```php
$context = new Context();
// Set a variable
$context->set('$foo','Lorem ipsum');
// Set a nested variable
$context->set('$user:name','John Snow');
$context->set('$user:email','jsnow@winterfell.we');
// Set an iterable variable
$context->set('$fruits', array('apple','banana','pear'));
// Set PI
$context->set('PI',3.1416);
```

#### get($query)
Retrieves the variable from the context. It's not used as much as set().

```php
$context->get('$foo');
```
Returns $foo value, if set.

#### hooks
Hooks are functions executed when a part of a layout is parsed.

##### registerHook($hook, $callback)
Registers a new callback in the hook queue named $hook. You can trigger this hooks inside your template (check ~{ hook }~ for more detail). $callback must be a callable.

```php
// client.php
$context->registerHook('MYHOOK_NAME', 'handle_myhook_name');

function handle_myhook_name($attrs) {
	// Do something with the attribs (foo=>"bar")
}
```

```
// template.ltp

Content content blah ...
~{ hook name="MYHOOK_NAME" foo="bar" }~
```

##### unregisterHook($name, $callback)
Removes the callback from the hook queue so then the hook is triggered, the callback won´t be executed.

```php
$context->unregisterHook('MYHOOK_NAME', 'handle_myhook_name');
```

### The templates
All templates must start with a header. The header contains metadata that might be used in future versions, but for now, the only required data is the type.
Here can also be setted global variables for the whole template accesible in the $_HEADER variable.

```
{{{ LacesTemplate language="es_MX" author="Alexys Hegmann" }}}
```

Native header attributes:
* `version`
* `author`
* `language`

#### Laces
Laces are control strings that provide extra functionality to the templates.

There are three types by it´s construction:
* Replacer: `~{{ <replacement> }}~`
* Standalone: `~{ <lace> }~`
* Block: `~{ <lace> } <content> { <lace> }~`

The fist word after the opening ~{ is the lace. After the lace, we can find three kinds of constructions: attributes (`attrName="attr value"`), expressions (`[ 1 < 5 ]`) and filters (`| html | sql`).

With block laces, the content is between the } and the next {.

A lace can be assigned to an ID with the form `#id`, which after the parse can be accessed in the context. This serves as two purposes: allows multiple nesting and parse result storage.
`~{ lace#myLace }~`.

##### Replacer
Takes a variable, an ID or an expression to be printed out.

`~{{ $user:name }}~` prints `John Snow`
`~{{ PI }}~` prints `3.1416`
`~{{ [ 5*5+5 ] }}~` prints `30`

##### Include
Loads an external file and inserts the parsed template.

`~{ include src="template.ltp" parse="true" }~`

If parse is set to false, the loaded template is not parsed and the content is rendered as is.

##### If
Evaluates an expression and if the result is true, the if branch is parsed; if not, the else branch is parsed.

```
~{ if [$num > 10] }
	<p>The number is greater than 10</p>
{ else }
	<p>The number is not greater than 10</p>
{ if }~
```

##### Foreach
Iterates an array. Uses the `use` attribute to find the variable in the context and sets the current item in the variable set in `as`.

```
<ul>
	~{ foreach use="$news" as="$n" }
		<li>~{{ $n:title }}~</li>
	{ foreach }~
</ul>
```

##### For
Iterates a block until `while` is evaluated to false. `start` is evaluated before the iteration and is stored in the variable set in `use`. Every iteration, at the end, `step` is evaluated.

```
~{ for use="$i" start="10" while="$i>=1" step="$i--" }
    <span>Boo! ~{{ $i }}~</span>
{ for }~
```
Will print "Boo! 10" ... until "Boo! 1".

**Important!** Be careful on how you set this lace because it can cause an infinite loop if `while` is never evaluated to false.

##### Hook
This lace triggers the hook queue given by `name` and passes the current attribs to the callback functions.

```
~{ hook name="FOOTER" foo="bar" }~
```
Will trigger the "FOOTER" hook and pass array('foo'=>'bar') as an argument to the callback functions.

#### Filters
All laces can be passed through filters before the final output. This allows you to not worry about escaping your variables beforehand and leave it to Laces to do so.

```
~{{ $user:name }}~ said: ~{{ $user:message | html }}~
```
Will print `John Snow said: This is a <b>test</b>`; but without the `| html` would print `John Snow said: This is a test` (test being bold).

The current filters are:
* `| html`
* `| mysql`
* `| attr`

### Nesting
Unfortunately, the current parsing method doesn't support a normal nesting, but it can be done with the use of IDs. This can be done with any block lace.
Of course, nesting is a big feature that is planned to be supported in the next major release (if not sooner).

```
~{ if#foo1 [1==1] }
    ~{ if#foo2 [2!=2] }
        <p>IMPOSIBURU</p>
    { else#foo2 }
        <p>Nested ifs</p>
    { if#foo2 }~
{ if#foo1 }~
```

### Expressions
The current version of Laces includes a custom expression parser. The current supported operators are: 
* + (addition)
* - (substraction)
* * (multiplication)
* / (divition)
* % (module)
* ^ (power of)
* ! (boolean not)
* && (and)
* || (or)
* ^^ (exclusive or)
* == (equal)
* != (different)
* >= (greater than or equal)
* <= (lesser than or equal)
* > (greater than)
* < (lesser than)
* ++ (autoincrement)
* -- (autodecrement)
* = (assign)
* exists (boolean. Whether a variable exists or not)
* typeof (string. Returns the type of variable)
* variables (in the form $foo[:bar:baz...], ids #id, constants CONST)
* (, ) (parentheses to make nested expressions)

**The distribution includes an expression tester.**

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
