



# PHEXT Core functions and extensions library

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg)](https://php.net/)
[![License](https://sqonk.com/opensource/license.svg)](license.txt) [![Build Status](https://img.shields.io/travis/sqonk/phext-core/master.svg?style=flat-square)](https://travis-ci.org/sqonk/phext-core)

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
$ composer require sqonk/phext-core
```

Method Index
------------

- [Global Methods](#global-methods)
- [Arrays](#arrays)
- [Strings](#strings)
- [Numbers](#numbers)

### Global Methods

A collection of general purpose utility methods. 

These functions import across the global name space to keep usability to the maximum.

##### println

```php
function println(...$values)
```

Print a value to the output, adding a newline character at the end. If the value passed in is an array or an object then the text representation will be parsed and output.

This method can also take a variable number of arguments.

NOTE: This method can cause a performance hit in CPU intensive tasks due to its flexible intake of parameters and its automatic handling of data types. If you need to print in such situations you should instead use printstr().

Example:

```php
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



##### printstr

```php
function printstr(string $str = '')
```

Convienience method for printing a string with a line ending.



##### ask

```php
function ask($prompt = '', $newLineAfterPrompt = false)
```

Read the user input from the command prompt. Optionally pass a question/prompt to  the user, to be printed before input is read.

**NOTE**: This method is intended for use with the CLI.

Example:

```php
$name = ask('What is your name?');

// Input your name.. e.g. John

println('Hello', $name);
// prints 'Hello John' (or whatever you typed into the input).
```



##### objectify

```php
function objectify(array $data)
```

Convert an associative array into an object.

This method works by instanciating a new generic class and extracting the provided data array into its variable namespace.

Example:

```php
$var = objectify(['a' => 2, 'b' => 5]);

println($var);
// return (a:2,b:5)

println($var->a);
// return 2
```



##### named_objectify

```php
function named_objectify(...$prototype)
```

Create a object template that can be instantiated multiple times. The given array takes a sequential list of variable names that will later represent the supplied data.

You can either pass in an array of keys or each key as a seperate parameter.

Example:

```php
$Point = named_objectify('x', 'y');
$p = $Point(2, 4);

println($p);
// return '(x:2,y:4)'
```



##### dump_stack

```php
function dump_stack(string $message = '')
```

Print a stack trace (with an optional prefix message) at the current point in the code.



##### sequence

```php
function sequence(int $start, int $end = null, int $step = 1)
```

A memory efficient alternative to range(). Loop through `$start` and `$end` and yield the result to your own foreach.

If `$end` is not supplied then a sequence is auto constructed either ranging from 0 (when `$start` is positive) or approaching 0 (when start is negative). 



##### var_is_stringable

```php
function var_is_stringable($value)
```

Is the supplied variable capable of being transformed into a string?



##### starts_with

```php
function starts_with($haystack, $needle)
```

Does the haystack start with the needle? Accepts either an array or string as the haystack.



##### ends_with

```php
function ends_with($haystack, $needle)
```

Does the heystack end with the needle? Accepts either an array or string as the haystack.



##### contains

```php
function contains($haystack, $needle)
```

Does the needle occur within the haystack? Accepts either an array or string as the haystack.




### Arrays

A set of standard array functions designed to keep your code easier to read and remain obvious as to what is going on.

```php
use sqonk\phext\core\arrays;
```



##### is_populated

```php
static public function is_populated($value)
```
Is the given value both a valid array and does it contain at least one element?



##### safe_value / get

```php
static public function safe_value($anArray, $key, $defaultValue = null)	
```

```php
static public function get($anArray, $key, $defaultValue = null) // alias
```

Safely return the value from the given array under the given key. If the key does not exist in the array then the value specified by `$defaultValue` is returned instead.

This method allows you to avoid protential errors caused by trying to directly access non-existant keys by normalising the result regardless of whether the key is not set or if the value is empty.



##### shift

```php
static public function shift(array $array, int $amount, &$shiftedItems = [])
```

Shift elements off the start of the array to the number specified in the 'amount' parameter. Returns the modified array.



##### pop

```php
static public function pop(array $array, int $amount, &$poppedItems = [])
```

Pop elements off the end of the array to the number specified in the 'amount' parameter. Returns the modified array.



##### add_constrain

```php
static public function add_constrain(array &$array, $value, int $maxItems)
```

Add an item to end of an array. If the array count exceeds maxItems then shift first item off. This method both modifies the provided array by reference and returns it (to allow for method chaining).



##### sorted

```php
static public function sorted(array $array, int $mode = BY_VALUE, int $sort_flags = SORT_REGULAR)
```

Sort the given array using a standard sort method. This method is intended as a wrapper for the in-built native sorting methods, which typically modify the original array by reference instead of returning a modified copy.

`$mode` can have three possible values:

   - `BY_VALUE` (default): standard sort of the array values.
   - `BY_KEY`: Sort based on the array indexes.
   - `MAINTAIN_ASSOC`: Standard sort of the array values but maintaining index association.

Refer to the PHP documentation for all possible values on the `$sort_flags`.

Depending on the value of `$mode` this method will utilise either `sort`, `asort` or `ksort`  



##### rsorted

```php
static public function rsorted(array $array, int $mode = BY_VALUE, int $sort_flags = SORT_REGULAR)
```

Sort the given array in reverse order using a standard sort method. This method is intended as a wrapper for the in-built native sorting methods, which typically modify the original array by reference instead of returning a modified copy.

`$mode` can have three possible values:

   - `BY_VALUE` (default): standard sort of the array values.
   - `BY_KEY`: Sort based on the array indexes.
   - `MAINTAIN_ASSOC`: Standard sort of the array values but maintaining index association.

Refer to the PHP documentation for all possible values on the `$sort_flags`.

Depending on the value of `$mode` this method will utilise either `rsort`, `arsort` or `krsort`



##### key_sort

```php
static public function key_sort(array &$array, $key, bool $maintainKeyAssoc = false)
```

Sort an array of arrays or objects based on the value of a key inside of the sub-array/object.

If `$key` is an array then this method will perform a multi-sort, ordering by each key with sort priroity given in ascending order.

As per the native sorting methods, the array passed in will be modified directly. As an added convienience the array is also returned to allow method chaining.

Internally this function will use either usort or uasort depending on whether `$maintainKeyAssoc` is set to `TRUE` or `FALSE`. Setting it to `TRUE` will ensure the array indexes are maintained.



##### group_by / groupby

```php
static public function group_by(array $items, $keys, bool $keepEmptyKeys = false, int $pos = 0)
```

```php
static public function groupby(array $items, $keys, bool $keepEmptyKeys = false, int $pos = 0) // alias
```

Takes a flat array of elements and splits them into a tree of associative arrays based on  the keys passed in.

You need to ensure the array is sorted by the same order as the set of keys being used prior to calling this method. If only one key is required to split the array then a singular string may be provided, otherwise pass in an array.

Unless `$keepEmptyKeys` is set to `TRUE` then any key values that are empty will be omitted.

This method operates in a recursive fashion and the last parameter $pos is used internally when in operation. You should never need to pass in a custom value to $pos yourself.



##### transpose

```php
static public function transpose(array $array, string $groupKey, array $mergeMap)
```

Transform a set of rows and columns with vertical data into a horizontal configuration where the resulting array contains a column for each different value for the given fields in the merge map (associative array).

The group key is used to specifiy which field in the input array will be used to flatten multiple rows into one.



Example:

```php
use sqonk\phext\core\{strings,arrays};

$data = [
    ['character' => 'Actor A', 'decade' => 1970, 'appearances' => 1],
    ['character' => 'Actor A', 'decade' => 1980, 'appearances' => 2],
    ['character' => 'Actor A', 'decade' => 1990, 'appearances' => 2],
    ['character' => 'Actor A', 'decade' => 2000, 'appearances' => 1],
    ['character' => 'Actor A', 'decade' => 2010, 'appearances' => 1],
    
    ['character' => 'Actor B', 'decade' => 1980, 'appearances' => 1],
    ['character' => 'Actor B', 'decade' => 1990, 'appearances' => 1],
    ['character' => 'Actor B', 'decade' => 2000, 'appearances' => 1],
];
println(strings::columnize($data, ['decade', 'character', 'appearances']));
/*
     	decade	character	appearances
_____	______	_________	___________
0    	  1970	  Actor A	          1
1    	  1980	  Actor A	          2
2    	  1990	  Actor A	          2
3    	  2000	  Actor A	          1
4    	  2010	  Actor A	          1
5    	  1980	  Actor B	          1
6    	  1990	  Actor B	          1
7    	  2000	  Actor B	          1
*/



// TAKE NOTE: The $data array is pre-sorted by the group key prior to being transposed, this is critical for correct behaviour. 
$data = arrays::key_sort($data, 'decade');

// Transform the matrix using transpose() so that each character becomes a column
// with their resulting appearances listed alongside the decade.
$transformed = arrays::transpose($data, 'decade', ['character' => 'appearances']);
println(strings::columnize($transformed, ['decade', 'Actor A', 'Actor B']));

/*
     	decade	Actor A	Actor B
_____	______	_______	_______
0    	  1970	      1	       
1    	  1980	      2	      1
2    	  1990	      2	      1
3    	  2000	      1	      1
4    	  2010	      1	       
*/

```



##### first / start

```php
static public function first(iterable $array)
```

```php
static public function start(iterable $array) // alias
```

Return the first object in the array or null if array is empty.



##### end / last

```php
static public function end(iterable $array)
```

```php
static public function last(iterable $array) // alias
```

Return the last object in the array or null if array is empty.



##### middle

```php
static public function middle(iterable $array, bool $weightedToFront = true)
```

Return the object closest to the middle of the array. 
   - If the array is empty, returns null.
- If the array has less than 3 items, then return the first or last item depending 
    on the value of `$weightedToFront`.
- Otherwise return the object closest to the centre. When dealing with arrays containing
and even number of items then it will use the value of `$weightedToFront` to determine if it
picks the item closer to the start or closer to the end.



##### prune

```php
static public function prune(array $array, $empties = '')
```

 Creates a copy of the provided array where all values corresponding to 'empties' are omitted.



##### compact

```php
static public function compact(array $array)
```

Creates a copy of the provided array where all NULL values are omitted.



##### only_keys

```php
static public function only_keys(array $array, ...$keys)
```

Return a copy of an array containing only the values for the specified keys, with index association being maintained.

This method is primarily designed for associative arrays. It should be noted that if a key is not present in the provided array then it will not be present in the resulting array.



##### map

```php
static public function map(array $array, callable $callback)
```

Apply a callback function to the supplied array. This version will optionally supply the corresponding index/key of the value when needed (unlike the built-in `array_map()` method).

Callback format: `myFunc($value, $index) -> mixed`



##### choose

```php
static public function choose(iterable $array)
```

Randomly choose an item from the given array.

Example:

```php
$numbers = [1,2,3,4,5,6,7,8,9,10];
$choice = arrays::choose($numbers);
// return a random selection from provided array.
```



##### zip

```php
static public function zip(...$arrays)
```

Iterate through a series of arrays, yielding the value of the correpsonding index in each a sequential array to your own loop.

This method can handle both associative and non-associative arrays.

Example:

```php
$array1 = ['a', 'b', 'c'];
$array2 = [1, 2, 3, 4];
$array3 = ['#', '?'];

foreach (arrays::zip($array1, $array2, $array3) as [$v1, $v2, $v3])
	println($v1, $v2, $v3);

/*
Prints:
a 1 #
b 2 ?
c 3 
 4 
*/
```



##### zipall

```php
static public function zipall(...$arrays)
```

Iterate through a series of arrays, yielding the values for every possible combination of values.

This method can handle both associative and non-associative arrays.

Example:

```php
$array1 = ['a', 'b', 'c'];
$array2 = [1, 2, 3, 4];
$array3 = ['#', '?'];

foreach (arrays::zipall($array1, $array2, $array3) as [$v1, $v2, $v3])
	println($v1, $v2, $v3);

/*
a 1 #
a 1 ?
a 2 #
a 2 ?
a 3 #
a 3 ?
a 4 #
a 4 ?
b 1 #
b 1 ?
b 2 #
b 2 ?
b 3 #
b 3 ?
b 4 #
b 4 ?
c 1 #
c 1 ?
c 2 #
c 2 ?
c 3 #
c 3 ?
c 4 #
c 4 ?
*/
```



##### is_assoc

```php
static public function is_assoc(array $array)
```

Attempt to determine if the given array is either sequential or hashed.

This method works by extracting the keys of the array and performing a comparison of the keys of the given array and the indexes of the extracted key array to see if they match. If they do not then the provided array is likely associative.



##### encapsulate

```php
static public function encapsulate(array $array, string $startToken, string $endToken = null)
```

Return a copy of an array with every item wrapped in the provided tokens. If no end token is provided then the `$startToken` is used on both ends.

*NOTE*: This function expects all items in the array to convertable to a string.



##### implode_assoc

```php
static public function implode_assoc(string $delim, array $array, string $keyValueDelim)
```

Implode an associate array into a string where each element of the array is imploded with a given delimiter and each key/value pair is imploding using a different delimiter.



##### values

```php
static public function values(array $array, ...$keys)
```

Return the values in the provided array belonging to the specified keys. This method is primarily designed for associative arrays.

Example:

```php
$info = ['name' => 'Doug', 'age' => 30, 'job' => 'Policeman'];
println(arrays::values($info, 'name', 'age'));
/*
Prints: array (
  0 => 'Doug',
  1 => 30,
)
*/
```



##### implode

```php
static public function implode(string $delimiter, array $array, string $subDelimiter = null)
```

This method acts in a similar fashion to the native `implode`, however in addition it will recursively implode any sub-arrays found within the parent.

You may optionally provide a `$subDelimiter` to be applied to any inner arrays. If nothing is supplied then it will default to the primary delimiter.



##### implode_only

```php
static public function implode_only(string $delimiter, array $array, ...$keys)
```

Implode the given array using the desired delimiter. This method differs from the built-in implode in that it will only implode the values associated with the specified keys/indexes.

Empty values are automatically removed prior to implosion.



##### contains / any

```php
static public function contains(array $haystack, $needle, bool $strict = false)
```

```php
static public function any(array $haystack, $needle, bool $strict = false) // alias
```

Search an array for the given needle (subject). Returns `TRUE` if the needle was found, `FALSE` otherwise.

A callback may be provided as the `$needle` to perform more complex testing.

Callback format: `myFunc($value) -> bool`

For basic (non-callback) matches, setting `$strict` to `TRUE` will enforce type-safe comparisons.



##### all

```php
static public function contains(array $haystack, $needle, bool $strict = false)
```

Search an array for the given needle (subject). Returns `TRUE` if every value in the array matches the `$needle`, `FALSE` otherwise.

A callback may be provided as the `$needle` to perform more complex testing.

Callback format: `myFunc($value) -> bool`

For basic (non-callback) matches, setting `$strict` to `TRUE` will enforce type-safe comparisons.



##### ends_with

```php
static public function ends_with(array $haystack, $needle)
```

Determines if the given haystack ends with the needle.



##### starts_with

```php
static public function starts_with(array $haystack, $needle)
```

Determines if the given haystack starts with the needle.





### Strings

A set of standard string functions designed to keep your code easier to read and remain obvious as to what is going on.

```php
use \sqonk\phext\core\strings;
```



##### matches

```php
static public function matches(string $pattern, string $subject)
```

Wrapper for preg_match to gather the match array. Works more elegantly for inline operations.



##### contains

```php
static public function contains(string $haystack, string $needle)
```

Does the given needle occur within the given haystack?

Example:

```php
$str = 'The lazy fox jumped over the sleeping dog.';
if (strings::contains($str, 'lazy fox'))
    println('lazy fox found.');
// will print 'lazy fox found.'
```



##### ends_with

```php
static public function ends_with(string $haystack, string $needle)
```

Determines if the given haystack ends with the needle.

Example:

```php
if (strings::ends_with('What a nice day', 'day')) 
    println('There string ends with "day"');
// will print 'There string ends with "day"'.
```



##### starts_with

```php
static public function starts_with(string $haystack, string $needle)
```

Determines if the given haystack starts with the needle.



##### pop

```php
static public function pop(string $string, string $delimiter, int $amount)
```

Modify a string by splitting it by the given delimiter and popping `$amount` of elements off of the end.



##### shift

```php
static public function shift(string $string, string $delimiter, int $amount)
```

Modify a string by splitting it by the given delimiter and shifting `$amount` of elements off of the start.



##### popex

```php
static public function popex(string $string, string $delimiter, string &$poppedItem = null)
```

Split the string by the delimiter and return the shortened input string, providing the peopped item as output via the third parameter.

If the delimiter was not found and no item was shifted then this method returns the original string.

Example:

```php
$modified = strings::popex("doug,30,manager", ',', $item);
// return 'doug,30' with 'manager' stored in $item
```



##### shiftex

```php
static public function shiftex(string $string, string $delimiter, string &$shiftedItem = null)
```

Split the string by the delimiter and return the shortened input string, providing the shifted item as output via the third parameter.

If the delimiter was not found and no item was shifted then this method returns the original string.

Example:

```php
$modified = strings::shiftex("doug,30,manager", ',', $item);
// return '30,manager' with 'doug' stored in $item
```



##### contains_word

```php
static public function contains_word(string $haystack, string $word)
```

Perform a search for a word in a string.



##### replace_word

```php
static public function replace_word(string $haystack, string $word, string $replacement)
```

Perform a find & replace on a word in a string.



##### replace_words

```php
static public function replace_words(string $haystack, array $wordMap)
```

Replace a series of words with their counterpart provided in an associative array.



##### clean

```php
static public function clean(string $text)
```

Translate the given text to a clean representation by removing all control or UTF characters that can produce unreadable artifacts on various mediums of output such as HTML or PDF. 

It also assumes the desired output is a UTF-8 string. If you are working with a different character set you will need to employ an alternative cleaning system.

This method requires both mbstring and inconv extensions to be installed.



##### one_space

```php
static public function one_space(string $str)
```

To replace all types of whitespace with a single space.



##### truncate

```php
static public function truncate(string $value, int $maxLength, string $position = 'l')
```

Truncate a string if it's length exceeds the specified maximum value.  Strings can be truncated from the left, middle or right.

Position options:

- `l`: truncate left
- `c`: truncate middle
- `r`: truncate right



##### strip_non_alpha_numeric

```php
static public function strip_non_alpha_numeric(string $string, ?int $min = null, ?int $max = null)
```

Filter out all non alpha-numeric characters. Optionally pass in a minimum  and maximum string length to invalidate any resulting string that does not meet the given boundaries.



##### columnize

```php
static public function columnize(array $array, array $headers, bool $printHeaders = true, bool $printNumericIndexes = true)
```

Format and print out a series of rows and columns using the provided array of headers as the table header.

The data array provided should be in an array of rows, each row being an associative array of the column names (corresponding to those passed in as the header) and the related value.



### Numbers

Utility methods for dealing with numerical values.

```php
use \sqonk\phext\core\numbers;
```



##### constrain

```php
static public function constrain($value, $min, $max)
```

Clip a numeric value, if necessary, to the given min and max boundaries.

Example:

```php
$value = 4.9;
println("value:", numbers::constrain($value, 5.0, 5.5));
// will print out '5'.
```



##### is_within

```php
static public function is_within($value, $min, $max)
```

Check if the given numeric value is in range.

Example:

```php
$value = 20;
if (numbers::is_within($value, 10, 30))
    println('The number is within range');
// will print out 'The number is within range'.
```




## Credits

Theo Howell



## License

The MIT License (MIT). Please see [License File](license.txt) for more information.