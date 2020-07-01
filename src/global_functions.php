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

/*
    Print a value to the output, adding a newline character at the end. If the value
    passed in is an array or an object then the text representation will be 
    parsed and output.

    This method can also take a variable number of arguments.

    NOTE: This method can cause a performance hit in CPU intensive tasks due to its
    flexible intake of parameters and its automatic handling of data types. If you 
    need to print in such situations you should instead use printstr()
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

// Convienience method for printing a string with a line ending.
function printstr(string $str = '')
{
    print $str.PHP_EOL;
}

/*
    Read the user input from the command prompt. Optionally pass a question/prompt to 
    the user, to be printed before input is read.

    NOTE: This method is intended for use with the CLI.

    @param  $prompt                 The optional prompt to be displayed to the user prior to reading input.
    @param  $newLineAfterPrompt     If TRUE, add a new line in after the prompt.

    @returns                        The response from the user in string format.
*/
function ask($prompt = '', $newLineAfterPrompt = false)
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

/*
    Convert an associative array into an object.

    This method works by instanciating a new generic class and extracting
    the provided data array into its variable namespace.

    Example Usage:
        $p = objectify(['x' => 10, 'y' => 3]);
        println($p->x, $p->y);
*/
function objectify(array $data)
{
    $cl = new class() {
        public function fill($data)
        {
            foreach (json_decode(json_encode($data), true) as $key => $value)
                $this->{$key} = $value;
        }
		
		protected function propToString($value)
		{
			if (is_array($value)) {
				$self = $this;
				return implode(':', array_map(function($v) use ($self){
					return $self->propToString($v);
				}, $value));
			}
			return $value;
		}
        
        public function __toString()
        {
            $obj = new ReflectionObject($this);
            $props = $obj->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
            return sprintf("(%s)", implode(',', array_map(function($p) { 
                return $p->getName().':'.$this->propToString($p->getValue($this)); 
            }, $props)));
        }
    };
    $cl->fill($data);
    
    return $cl;
}

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

// Print a stack trace (with an optional prefix message) at the current point in the code.
function dump_stack(string $message = '')
{
    if ($message)
	    println($message);
    println((new Exception)->getTraceAsString());
}

/* 
    A memory efficient alternative to range(). Loop through $start and
    $end and yield the result to your own foreach.

    If $end is not supplied then a sequence is auto constructed either
    ranging from 0 (when $start is positive) or approaching 0 (when
    start is negative). 
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


/* 
	Is the supplied variable capable of being transformed into a string?
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

// Does the haystack start with the needle? Accepts either an array or string as the haystack.
function starts_with($haystack, $needle)
{
    return is_array($haystack) ? arrays::starts_with($haystack, $needle) : 
        strings::starts_with($haystack, $needle);
}

// Does the heystack end with the needle? Accepts either an array or string as the haystack.
function ends_with($haystack, $needle)
{
    return is_array($haystack) ? arrays::ends_with($haystack, $needle) :
        strings::ends_with($haystack, $needle);
}

// Does the needle occur within the haystack? Accepts either an array or string as the haystack.
function contains($haystack, $needle)
{
    return is_array($haystack) ? arrays::contains($haystack, $needle) :
        strings::contains($haystack, $needle);
}

/*
    ------- PHP 8 backwards compatibility methods.
*/

if (! function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return starts_with($haystack, $needle);
    }
}

if (! function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        return ends_with($haystack, $needle);
    }
}

if (! function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return contains($haystack, $needle);
    }
}
