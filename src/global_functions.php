<?php
declare(strict_types = 0);

use sqonk\phext\core\{strings,arrays};

/**
*
* Core Utilities
* 
* @package		phext
* @subpackage	core
* @version		1
* 
* @license		MIT see license.txt
* @copyright	2019 Sqonk Pty Ltd.
*
*
* This file is distributed
* on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
* express or implied. See the License for the specific language governing
* permissions and limitations under the License.
*/

/*
    A collection of general purpose utility methods. 

    NOTE: These functions import across the global name space to keep
    usability to the maximum.
*/

define('BY_VALUE', 0);
define('BY_KEY', 1);
define('MAINTAIN_ASSOC', 2);


/**
 * Print a value to the output, adding a newline character at the end. If the value
 * passed in is an array or an object then the text representation will be
 * parsed and output.
 * 
 * This method can also take a variable number of arguments.
 * 
 * NOTE: This method can cause a performance hit in CPU intensive tasks due to its
 * flexible intake of parameters and its automatic handling of data types. If you
 * need to print in such situations you should instead use `printstr()`
 * 
 * Example:
 * 
 * ``` php
 * println('This is an array:', [1,2,3]);
 * // prints:
 * // This is an array: array (
 * //   0 => 1,
 * //   1 => 2,
 * //   2 => 3,
 * // )
 * ```
 */
function println(...$values)
{
    $out = [];
    foreach ($values as $v) 
	{
		if (is_array($v) or (is_object($v) and ! method_exists($v, '__toString' ))) 
		    $v = var_export($v, true);
        
        $out[] = $v;
    }
    
    print implode(' ', $out).PHP_EOL;
}

/**
 * Convienience method for printing a string with a line ending.
 */
function printstr(string $str = '')
{
    print $str.PHP_EOL;
}

/**
 * Read the user input from the command prompt. Optionally pass a question/prompt to
 * the user, to be printed before input is read.
 * 
 * NOTE: This method is intended for use with the CLI.
 * 
 * -- parameters:
 * @param  $prompt The optional prompt to be displayed to the user prior to reading input.
 * @param  $newLineAfterPrompt If TRUE, add a new line in after the prompt.
 * 
 * @return The response from the user in string format.
 * 
 * Example:
 * 
 * ``` php
 * $name = ask('What is your name?');
 * // Input your name.. e.g. John
 * println('Hello', $name);
 * // prints 'Hello John' (or whatever you typed into the input).
 * ```
 */
function ask(string $prompt = '', bool $newLineAfterPrompt = false)
{
    if ($prompt) {
        $seperator = $newLineAfterPrompt ? PHP_EOL : " ";
        print $prompt.$seperator;
    }
        
	$handle = fopen("php://stdin", "r");
	$line = fgets($handle);
	fclose($handle);
	return trim($line);
}

/**
 * Convert an associative array into an object.
 * 
 * This method works by creating an instance of a generic class and extracting
 * the provided data array into its variable namespace.
 * 
 * Example Usage:
 * 
 * ``` php
 * $var = objectify(['a' => 2, 'b' => 5]);
 * println($var);
 * // prints (a:2,b:5)
 * println($var->a);
 * // prints 2
 * ```
 */
function objectify(array $data)
{
    return new class($data) 
    {
        private $data;
        
        public function __construct(array $mappedVars)
        {
            $this->data = $mappedVars;
        }
        
        public function __get(string $name)
        {
            if (array_key_exists($name, $this->data))
                return $this->data[$name];
        
            throw new Exception("Undefined property: $name");
        }
    
        public function __set(string $name, $value)
        {
            $this->data[$name] = $value;
            return $this;
        }
		
		private function propToString($value)
		{
			if (is_array($value)) {
				return implode(':', array_map(function($v) {
					return $self->propToString($v);
				}, $value));
			}
			return $value;
		}
        
        public function __toString()
        {
            return sprintf("(%s)", implode(',', arrays::map($this->data, function($v, $k) { 
                return $k . ':' . $this->propToString($v); 
            })));
        }
    };
}

/**
 * Create a object template that can be instantiated multiple times. The given
 * array takes a sequential list of variable names that will later represent
 * the supplied data.
 * 
 * You can either pass in an array of keys or each key as a seperate parameter.
 * 
 * Example usage:
 * 
 * ``` php
 * $Point = named_objectify('x', 'y');
 * $p = $Point(2, 4);
 * println($p);
 * // prints '(x:2,y:4)'
 * ```
 */
function named_objectify(...$prototype)
{
	if (count($prototype) == 0)
		throw new \LengthException('You must supply at least one parameter.');
	else if (count($prototype) == 1 and is_array($prototype[0]))
		$prototype = $prototype[0];
	else {
		foreach ($prototype as $item)
			if (! is_string($item))
				throw new \InvalidArgumentException('All parameters must be strings.');
	}
	
    return function() use ($prototype) {
        return objectify(array_combine($prototype, func_get_args()));
    };        
}

/**
 * Print a stack trace (with an optional prefix message) at the current point in the code.
 */
function dump_stack(string $message = '')
{
    if ($message)
	    println($message);
    println((new Exception)->getTraceAsString());
}

/**
 * A memory efficient alternative to range(). Loop through $start and
 * $end and yield the result to your own foreach.
 * 
 * If $end is not supplied then a sequence is auto constructed either
 * ranging from 0 (when $start is positive) or approaching 0 (when
 * start is negative).
 */
function sequence(int $start, int $end = null, int $step = 1)
{
    if ($end === null) {
        if ($start < 0)
            $end = 0;
        else {
            $end = $start;
            $start = 0;
        }
    }
    for ($i = $start; $i <= $end; $i += $step)
        yield $i;
}


/**
 * Is the supplied variable capable of being transformed into a string?
 */
function var_is_stringable($value)
{
	return is_string($value) or is_numeric($value) or
		(is_object($value) and method_exists($value, '__toString'));
}

// ----- Auto-route to specific class.
/* 
	These functions present a conistent interface that will work on either
	strings or arrays.
*/

/**
 * Does the haystack start with the needle? Accepts either an array or string as the haystack
 * and routes to the equivalent method in `strings` or `arrays`.
 */
function starts_with($haystack, $needle)
{
    return is_array($haystack) ? arrays::starts_with($haystack, $needle) : 
        strings::starts_with($haystack, $needle);
}

/**
 * Does the haystack end with the needle? Accepts either an array or string as the haystack
 * and routes to the equivalent method in `strings` or `arrays`.
 */
function ends_with($haystack, $needle)
{
    return is_array($haystack) ? arrays::ends_with($haystack, $needle) :
        strings::ends_with($haystack, $needle);
}

/**
 * Does the needle occur within the haystack? Accepts either an array or string as the haystack
 * and routes to the equivalent method in `strings` or `arrays`.
 */
function contains($haystack, $needle)
{
    return is_array($haystack) ? arrays::contains($haystack, $needle) :
        strings::contains($haystack, $needle);
}
