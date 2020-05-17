# PHEXT Core functions and extensions library

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg)](https://php.net/)
[![License](https://sqonk.com/opensource/license.svg)](license.txt)

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

``` php
/*
    Print a value to the output, adding a newline character at the end. If the value
    passed in is an array or an object then the text representation will be 
    parsed and output.

    This method can also take a variable number of arguments.

    NOTE: This method can cause a performance hit in CPU intensive tasks due to its
    flexible intake of parameters and its automatic handling of data types. If you 
    need to print in such situations you should instead use printstr()
*/
function println(...$values);


// Convienience method for printing a string with a line ending.
function printstr(string $str = '');


/*
    Read the user input from the command prompt. Optionally pass a question/prompt to 
    the user, to be printed before input is read.

    NOTE: This method is intended for use with the CLI.
*/
function ask($prompt = '', $newLineAfterPrompt = false);


/*
    Convert an associative array into an object.

    This method works by instanciating a new generic class and extracting
    the provided data array into its variable namespace.

    Example Usage:
        $p = objectify(['x' => 10, 'y' => 3]);
        println($p->x, $p->y);
*/
function objectify(array $data);


/*
    Create a object template that can be instantiated multiple times. The given
    array takes a sequential list of variable names that will later represent
    the supplied data.

	You can either pass in an array of keys or each key as a seperate parameter.

    Example usage:
        $Point = named_objectify(['x', 'y']);
        $p = $Point(2, 4);

        println($p->x, $p->y);
*/
function named_objectify(...$prototype);


// Print a stack trace (with an optional prefix message) at the current point in the code.
function dump_stack(string $message = '');


/* 
    A memory efficient alternative to range(). Loop through $start and
    $end and yield the result to your own foreach.

    If $end is not supplied then a sequence is auto constructed either
    ranging from 0 (when $start is positive) or approaching 0 (when
    start is negative). 
*/
function sequence(int $start, int $end = null, int $step = 1);


/* 
    Is the supplied variable capable of being transformed into a string?
*/
function var_is_stringable($value);


// Does the haystack start with the needle? Accepts either an array or string as the haystack.
function starts_with($haystack, $needle);


// Does the heystack end with the needle? Accepts either an array or string as the haystack.
function ends_with($haystack, $needle);


// Does the needle occur within the haystack? Accepts either an array or string as the haystack.
function contains($haystack, $needle);
```

### Arrays

A set of standard array functions designed to keep your code easier to read and remain obvious as to what is going on.

``` php 
// Is the given value both a valid array and does it contain at least one element?
arrays::is_populated($value);
    
/*
    Safely return the value from the given array under the given key. If the key does not
    exist in the array then the value specified by $defaultValue is returned instead.

    This method allows you to avoid protential errors caused by trying to directly access
    non-existant keys by normalising the result regardless of whether the key is not set
    or if the value is empty.

    As of PHP 7.4 $anArray[$key] ??= $defaultValue does the same thing.
*/
arrays::safe_value($anArray, $key, $defaultValue = null);

// Alias for safe_value().
arrays::get($anArray, $key, $defaultValue = null);

// Pop elements off the end of the array to the number specified in the 'amount' parameter.
arrays::pop(array $array, int $amount, &$poppedItems = []);

// Shift elements off the start of the array to the number specified in the 'amount' parameter.
arrays::shift(array $array, int $amount, &$shiftedItems = []);

// Add an item to end of an array. If the array count exceeds maxItems then shift first item off.
arrays::add_constrain(array &$array, $value, int $maxItems);

/*
    Sort an array of arrays or objects based on the value of a key inside of the sub-array/object.

    If $key is an array then this method will perform a multi-sort, ordering by each key with 	
    sort priroity given in ascending order.

    As per the native sorting methods, the array passed in will be modified directly. As an added
    convienience the array is also returned to allow method chaining.

    Internally this function will use either usort or uasort depending on whether $maintainKeyAssoc
    is set to TRUE or FALSE. Setting it to TRUE will ensure the array indexes are maintained.
*/
arrays::key_sort(array &$array, $key, bool $maintainKeyAssoc = false);

/*
    Takes a flat array of elements and splits them into a tree of associative arrays based on  
    the keys passed in.

    You need to ensure the array is sorted by the same order as the set of keys being used
    prior to calling this method. If only one key is required to split the array then a singular
    string may be provided, otherwise pass in an array.

    Unless $keepEmptyKeys is set to TRUE then any key values that are empty will be omitted.

    This method operates in a recursive fashion and the last parameter $pos is used internally
    when in operation. You should never need to pass in a custom value to $pos yourself.
*/
arrays::group_by(array $items, $keys, bool $keepEmptyKeys = false, int $pos = 0);

/*
    Transform a set of rows and columns with vertical data into a horizontal configuration
    where the resulting array contains a column for each different value for the given
    fields in the merge map (associative array).

    The group key is used to specifiy which field in the input array will be used to flatten
    multiple rows into one.

    For example, if you had a result set that contained a 'type' field, a corresponding
    'reading' field and a 'time' field (used as the group key) then this method would 
    merge all rows containing the same time value into a matrix containing as
    many columns as there are differing values for the type field, with each column
    containing the corresponding value from the 'reading' field.
*/
arrays::transpose(array $array, string $groupKey, array $mergeMap);

// Alias for self::first.
arrays::start(iterable $array);

// Return the first object in the array or null if array is empty.
arrays::first(iterable $array);

// Return the last object in the array or null if array is empty.
arrays::end(iterable $array);

// Alias for self::end.
arrays::last(iterable $array);

/*
    Return the object closest to the middle of the array. 
    - If the array is empty, returns null.
    - If the array has less than 3 items, then return the first or last item depending 
    on the value of $weightedToFront.
    - Otherwise return the object closest to the centre. When dealing with arrays containing
    and even number of items then it will use the value of $weightedToFront to determine if it
    picks the item closer to the start or closer to the end.
*/
arrays::middle(iterable $array, bool $weightedToFront = true);

/*
    Creates a copy of the provided array where all values corresponding to 'empties' are omitted.
*/
arrays::prune(array $array, $empties = '');

/*
    Creates a copy of the provided array where all NULL values are omitted.
*/
arrays::compact(array $array);

/*
    Return a copy of an array containing only the values for the specified keys,
    with index association being maintained.

    This method is primarily designed for associative arrays. It should be
    noted that if a key is not present in the provided array then it will not
    be present in the resulting array.
*/
arrays::only_keys(array $array, ...$keys);

/*
    Apply a callback function to the supplied array. This version will optionally
    supply the corresponding index/key of the value when needed (unlike the built-in
    array_map() method).

    Callback format: myFunc($value, $index) -> mixed
*/
arrays::map(array $array, callable $callback);

/*
    Randomly choose an item from the given array.
*/
arrays::choose(iterable $array);

/*
    Iterate through a series of arrays, yielding the value of the correpsonding index
    in each a sequential array to your own loop.

    This method can handle both associative and non-associative arrays.

    Example usage:
        foreach (arrays::zip($array1, $array2, $array3) as list($v1, $v2, $v3));
*/
arrays::zip(...$arrays);

/*
    Iterate through a series of arrays, yielding the values for every possible
    combination of values.

    For example, with 2 arrays this function will yield for every element in array 2 with 
    the value in the first index of array 1. It will then yield for every element in 
    array 2 with the value in the second index of array 1, etc.

    This method can handle both associative and non-associative arrays.

    Example usage:
        foreach (arrays::zipall($array1, $array2, $array3) as list($v1, $v2, $v3));
*/
arrays::zipall(...$arrays);


/*
    Attempt to determine if the given array is either sequential or hashed.

    This method works by extracting the keys of the array and performing a
    comparison of the keys of the given array and the indexes of the extracted
    key array to see if they match. If they do not then the provided array
    is likely associative.
*/
arrays::is_assoc(array $array);


/*
    Return a copy of an array with every item wrapped in the provided tokens. If no
    end token is provided then the $startToken is used on both ends.

    NOTE: This function expects all items in the array to convertable to a string.
*/
arrays::encapsulate(array $array, string $startToken, string $endToken = null);


/*
    Implode an associate array into a string where each element of the array is 
    imploded with a given delimiter and each key/value pair is imploding using a 
    different delimiter.
*/
arrays::implode_assoc(string $delim, array $array, string $keyValueDelim);


/* 
    Return the values in the provided array belonging to the specified keys.

    This method is primarily designed for associative arrays.
*/
arrays::values(array $array, ...$keys);


/*
    This method acts in a similar fashion to the native 'implode', however in addition it
    will recursively implode any sub-arrays found within the parent.

    You may optionally provide a $subDelimiter to be applied to any inner arrays. If 
    nothing is supplied then it will default to the primary delimiter.
*/
arrays::implode(string $delimiter, array $array, string $subDelimiter = null);

/*
    Implode the given array using the desired delimiter. This method differs from
    the built-in implode in that it will only implode the values associated with 
    the specified keys/indexes.

    Empty values are automatically removed prior to implosion.
*/
arrays::implode_only(string $delimiter, array $array, ...$keys);


// Search an array for the given needle (subject).
arrays::contains(array $haystack, $needle);


// Determines if the given haystack ends with the needle.
arrays::ends_with(array $haystack, $needle);


// Determines if the given haystack starts with the needle.
arrays::starts_with(array $haystack, $needle);

``` 

### Strings

A set of standard string functions designed to keep your code easier to read and remain obvious as to what is going on.

``` php
/*
    Wrapper for preg_match to gather the match array. Works more elegantly for inline
    operations.
*/
strings::matches(string $pattern, string $subject);

// Search either an array or a string for the given needle (subject).
strings::contains(string $haystack, string $needle);


// Determines if the given haystack ends with the needle.
strings::ends_with(string $haystack, string $needle);


// Determines if the given haystack starts with the needle.
strings::starts_with(string $haystack, string $needle);


// Modify a string by splitting it by the given delimiter and popping 'amount' of elements off of the end.
strings::pop(string $string, string $delimiter, int $amount);


// Modify a string by splitting it by the given delimiter and shifting 'amount' of elements off of the start.
strings::shift(string $string, string $delimiter, int $amount);


/* 
    Split the string by the delimiter and return the shortened input string, providing 
    the peopped item as output via the third parameter.

    If the delimiter was not found and no item was shifted then this method returns the 
    original string.
*/
strings::popex(string $string, string $delimiter, string &$poppedItem = null);


/* 
    Split the string by the delimiter and return the shortened input string, providing 
    the shifted item as output via the third parameter.

    If the delimiter was not found and no item was shifted then this method returns the 
    original string.
*/
strings::shiftex(string $string, string $delimiter, string &$shiftedItem = null);

/*
    Perform a search for a word in a string.
*/
strings::contains_word(string $haystack, string $word);

/*
    Perform a find & replace on a word in a string.
*/
strings::replace_word(string $haystack, string $word, string $replacement);

/*
    Replace a series of words with their counterpart provided in an
    associative array.
*/
strings::replace_words(string $haystack, array $wordMap);

/* 
    Translate the given text to a clean representation by removing all control or UTF characters that can produce unreadable artifacts on various mediums of output such as HTML or PDF. 

    It also assumes the desired output is a UTF-8 string. If you are working with a different character set you will need to employ an alternative cleaning system.

    Passing in an array will cycle through and return a copy with all elements cleaned.	

    This method requires both mbstring and inconv extensions to be installed.
*/
strings::clean(string $text);


/*
    To replace all types of whitespace with a single space.
*/
strings::one_space(string $str);


/* 
    Truncate a string if it's length exceeds the specified maximum value.
    Strings can be truncated from the left, middle or right.

    Position options:
    	- l: truncate left
    	- c: truncate middle
    	- r: truncate right
*/
strings::truncate(string $value, int $maxLength, string $position = 'l');


/*
    Filter out all non alpha-numeric characters. Optionally pass in a minimum 
    and maximum string length to invalidate any resulting string that does not 
    meet the given boundaries.
*/
strings::strip_non_alpha_numeric(string $string, ?int $min = null, ?int $max = null);


/*
    Format and print out a series of rows and columns using the provided array of headers
    as the table header.

    The data array provided should be in an array of rows, each row being an associative
    array of the column names (corresponding to those passed in as the header) and the 
    related value.
*/
strings::columnize(array $array, array $headers, bool $printHeaders = true, bool $printNumericIndexes = true);   
```

### Numbers

Utility methods for dealing with numerical values.

``` php
// Clip a numeric value, if necessary, to the given min and max boundaries.
numbers::constrain($value, $min, $max);


// Check if the given numeric value is in range.
numbers::is_within($value, $min, $max);
```

## Examples

A small selection of examples of the methods listed above.

#### println

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

#### ask

Designed for CLI SAPI, prompt the user for information and return the result.

``` php
$name = ask('What is your name?');

// Input your name.. e.g. John

println('Hello', $name);
// prints 'Hello John' (or whatever you typed into the input).
```


#### objectify

Convert an associative array into a standard object.

objectify:

``` php
$var = objectify(['a' => 2, 'b' => 5]);

println($var);
// return (a:2,b:5)

println($var->a);
// return 2
```

#### named_objectify
 
named_objectify creates a template for repeat usage:

``` php
$Point = named_objectify('x', 'y');
$p = $Point(2, 4);

println($p);
// return '(x:2,y:4)'
```
 
### Standard methods

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
// will print out 'The number is within range'.
```
 
## Credits

Theo Howell
 
## License

The MIT License (MIT). Please see [License File](license.txt) for more information.