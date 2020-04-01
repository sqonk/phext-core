# PHEXT Core functions and extensions library

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg)](https://php.net/)
[![Software License][ico-license]](license.txt)

This is the core package to the PHEXT set of libraries for PHP. It includes general utility methods for strings, arrays and numbers, each of which exist as a grouped class with the methods statically accessible.

It also contains a small set of stand-alone functions that import across the global namespace.

Most of the other PHEXT modules rely on the core library.

Some of the string methods require mbstring and iconv to be installed and active.

## About PHEXT

The PHEXT package is a set of libraries for PHP that aim to solve common problems with a syntax that helps to keep your code both concise and readable.

PHEXT aims to not only be useful on the web SAPI but to also provide a productivity boost to command line scripts, whether they be for automation, data analysis or general research.

## Install

Via Composer

``` bash
$ composer require sqonk\phext-core
```

## Documentation

Forthcoming, refer to class comments and functions for the interim.


## Key Primative Methods


### println

Designed for CLI SAPI, prints a variable set of arguements to the terminal ending with a new line, converting to a string if required.

``` php
println('This is an array:', [1,2,3]);
/* 
return 

This is an array: array (
  0 => 1,
  1 => 2,
  2 => 3,
)
*/
```

### ask

Designed for CLI SAPI, prompt the user for information and return the result.

``` php
$name = ask('What is your name?');
```


### objectify & named_objectify

Convert an associative array into a standard object.

objectify:

``` php
$var = objectify(['a' => 2, 'b' => 5]);

println($var);
// return (a:2,b:5)

println($var->a);
// return 2
```
 
named_objectify creates a template for repeat usage:

``` php
$Point = named_objectify('x', 'y');
$p = $Point(2, 4);

println($p);
// return '(x:2,y:4)'
```
 
## Standard methods

Most of the utility methods however are accessed by importing the appropriate namespaces.

``` php
use sqonk\phext\core\{strings,arrays,numbers};

$modified = strings::shiftex("doug,30,manager", ',', $item);
// return '30,manager' with 'doug' stored in $item

if (strings::ends_with('What a nice day', 'day')) 
	println('There is a day in this string');
// will print out 'There is a day in this string'.

$numbers = [1,2,3,4,5,6,7,8,9,10];
$choice = arrays::choose($numbers);
// return a random selection from provided array.

$value = 20;
if (numbers::is_within($value, 10, 30))
	println('The number is within range');
// will print out 'There number is within range'.
```
 
## Credits

- [Theo Howell][link-author]
 
## License

 The MIT License (MIT). Please see [License File](license.txt) for more information.