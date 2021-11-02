###### PHEXT > [Core](../README.md) > [API Reference](index.md) > global_functions
------
### global_functions
#### Methods
[println](#println)
[printstr](#printstr)
[ask](#ask)
[objectify](#objectify)
[named_objectify](#named_objectify)
[dump_stack](#dump_stack)
[sequence](#sequence)
[var_is_stringable](#var_is_stringable)
[starts_with](#starts_with)
[ends_with](#ends_with)
[contains](#contains)

------
##### println
```php
function println(...$values) 
```
Print a value to the output, adding a newline character at the end. If the value passed in is an array or an object then the text representation will be parsed and output.

This method can also take a variable number of arguments.

NOTE: This method can cause a performance hit in CPU intensive tasks due to its flexible intake of parameters and its automatic handling of data types. If you need to print in such situations you should instead use `printstr()`

Example:

``` php
println('This is an array:', [1,2,3]);
// prints:
// This is an array: array (
//   0 => 1,
//   1 => 2,
//   2 => 3,
// )
```


------
##### printstr
```php
function printstr(string $str = '') 
```
Convienience method for printing a string with a line ending.


------
##### ask
```php
function ask(string $prompt = '', bool $newLineAfterPrompt = false) 
```
Read the user input from the command prompt. Optionally pass a question/prompt to the user, to be printed before input is read.

NOTE: This method is intended for use with the CLI.

- **$prompt** The optional prompt to be displayed to the user prior to reading input.
- **$newLineAfterPrompt** If `TRUE`, add a new line in after the prompt.

**Returns:**  The response from the user in string format.

Example:

``` php
$name = ask('What is your name?');
// Input your name.. e.g. John
println('Hello', $name);
// prints 'Hello John' (or whatever you typed into the input).
```


------
##### objectify
```php
function objectify(array $data) 
```
Convert an associative array into an object.

This method works by creating an instance of a generic class and extracting the provided data array into its variable namespace.

Example Usage:

``` php
$var = objectify(['a' => 2, 'b' => 5]);
println($var);
// prints (a:2,b:5)
println($var->a);
// prints 2
```


------
##### named_objectify
```php
function named_objectify(...$prototype) 
```
Create a object template that can be instantiated multiple times. The given array takes a sequential list of variable names that will later represent the supplied data.

You can either pass in an array of keys or each key as a seperate parameter.

Example usage:

``` php
$Point = named_objectify('x', 'y');
$p = $Point(2, 4);
println($p);
// prints '(x:2,y:4)'
```


------
##### dump_stack
```php
function dump_stack(string $message = '') 
```
Print a stack trace (with an optional prefix message) at the current point in the code.


------
##### sequence
```php
function sequence(int $start, int $end = null, int $step = 1) 
```
A memory efficient alternative to range(). Loop through $start and $end and yield the result to your own foreach.

If $end is not supplied then a sequence is auto constructed either ranging from 0 (when $start is positive) or approaching 0 (when start is negative).


------
##### var_is_stringable
```php
function var_is_stringable($value) 
```
Is the supplied variable capable of being transformed into a string?


------
##### starts_with
```php
function starts_with($haystack, $needle) 
```
Does the haystack start with the needle? Accepts either an array or string as the haystack and routes to the equivalent method in `strings` or `arrays`.


------
##### ends_with
```php
function ends_with($haystack, $needle) 
```
Does the haystack end with the needle? Accepts either an array or string as the haystack and routes to the equivalent method in `strings` or `arrays`.


------
##### contains
```php
function contains($haystack, $needle) 
```
Does the needle occur within the haystack? Accepts either an array or string as the haystack and routes to the equivalent method in `strings` or `arrays`.


------
