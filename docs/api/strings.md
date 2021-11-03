###### PHEXT > [Core](../README.md) > [API Reference](index.md) > strings
------
### strings
A set of standard string functions designed to keep your code easier to read and remain obvious as to what is going on.
#### Methods
[matches](#matches)
[contains](#contains)
[ends_with](#ends_with)
[starts_with](#starts_with)
[pop](#pop)
[shift](#shift)
[popex](#popex)
[shiftex](#shiftex)
[contains_word](#contains_word)
[replace_word](#replace_word)
[replace_words](#replace_words)
[clean](#clean)
[one_space](#one_space)
[truncate](#truncate)
[strip_non_alpha_numeric](#strip_non_alpha_numeric)
[columnize](#columnize)

------
##### matches
```php
static public function matches(string $pattern, string $subject) : array
```
Wrapper for preg_match to gather the match array. Works more elegantly for inline operations.


------
##### contains
```php
static public function contains(string $haystack, string $needle) : bool
```
Search either an array or a string for the given needle (subject).

Example:

``` php
$str = 'The lazy fox jumped over the sleeping dog.';
if (strings::contains($str, 'lazy fox'))
println('lazy fox found.');
// will print 'lazy fox found.'
```


------
##### ends_with
```php
static public function ends_with(string $haystack, string $needle) : bool
```
Determines if the given haystack ends with the needle. When running on PHP >= 8.0 this function simply calls str_ends_with().

Example:

``` php
if (strings::ends_with('What a nice day', 'day'))
println('The string ends with "day"');
// will print 'The string ends with "day"'.
```


------
##### starts_with
```php
static public function starts_with(string $haystack, string $needle) : bool
```
Determines if the given haystack starts with the needle. When running on PHP >= 8.0 this function simply calls str_starts_with().


------
##### pop
```php
static public function pop(string $string, string $delimiter, int $amount) : string
```
Modify a string by splitting it by the given delimiter and popping $amount of elements off of the end.


------
##### shift
```php
static public function shift(string $string, string $delimiter, int $amount) : string
```
Modify a string by splitting it by the given delimiter and shifting $amount of elements off of the start.


------
##### popex
```php
static public function popex(string $string, string $delimiter, string &$poppedItem = null) : string
```
Split the string by the delimiter and return the shortened input string, providing the popped item as output via the third parameter.

If the delimiter was not found and no item was shifted then this method returns the original string.

Example:

``` php
$modified = strings::popex("doug,30,manager", ',', $item);
// return 'doug,30' with 'manager' stored in $item
```


------
##### shiftex
```php
static public function shiftex(string $string, string $delimiter, string &$shiftedItem = null) : string
```
Split the string by the delimiter and return the shortened input string, providing the shifted item as output via the third parameter.

If the delimiter was not found and no item was shifted then this method returns the original string.

Example:

``` php
$modified = strings::shiftex("doug,30,manager", ',', $item);
// return '30,manager' with 'doug' stored in $item
```


------
##### contains_word
```php
static public function contains_word(string $haystack, string $word) 
```
Perform a search for a word in a string.


------
##### replace_word
```php
static public function replace_word(string $haystack, string $word, string $replacement) 
```
Perform a find & replace on a word in a string.


------
##### replace_words
```php
static public function replace_words(string $haystack, array $wordMap) 
```
Replace a series of words with their counterpart provided in an associative array.


------
##### clean
```php
static public function clean(array|string $text) 
```
Translate the given text to a clean representation by removing all control or UTF characters that can produce unreadable artefacts on various mediums of output such as HTML or PDF.

It also assumes the desired output is a UTF-8 string. If you are working with a different character set you will need to employ an alternative cleaning system.

Passing in an array will cycle through and return a copy with all elements cleaned.

This method requires both mbstring and inconv extensions to be installed.


------
##### one_space
```php
static public function one_space(string $str) 
```
To replace all types of whitespace with a single space.


------
##### truncate
```php
static public function truncate(string $value, int $maxLength, string $position = 'r') 
```
Truncate a string if it's length exceeds the specified maximum value. Strings can be truncated from the left, middle or right.


Position options:
- `l`: truncate left
- `c`: truncate middle
- `r`: truncate right


------
##### strip_non_alpha_numeric
```php
static public function strip_non_alpha_numeric(string $string, int $min = null, int $max = null) 
```
Filter out all non alpha-numeric characters. Optionally pass in a minimum and maximum string length to invalidate any resulting string that does not meet the given boundaries.


------
##### columnize
```php
static public function columnize(array $array, array $headers, bool $printHeaders = true, bool $printNumericIndexes = true) 
```
Format and print out a series of rows and columns using the provided array of headers as the table header.

The data array provided should be in an array of rows, each row being an associative array of the column names (corresponding to those passed in as the header) and the related value.


------
