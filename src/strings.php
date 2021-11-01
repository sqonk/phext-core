<?php
declare(strict_types = 0);

namespace sqonk\phext\core;
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


/**
 * A set of standard string functions designed to keep your code easier to read
 * and remain obvious as to what is going on.
 */
class strings
{   
    /**
     * Wrapper for preg_match to gather the match array. Works more elegantly for inline
     * operations.
     */
    static public function matches(string $pattern, string $subject): array
    {
        preg_match($pattern, $subject, $matches);
        return $matches;
    }
    
    /**
     * Search either an array or a string for the given needle (subject).
     * 
     * Example:
     * 
     * ``` php
     * $str = 'The lazy fox jumped over the sleeping dog.';
     * if (strings::contains($str, 'lazy fox'))
     *    println('lazy fox found.');
     * // will print 'lazy fox found.'
     * ```
     */
    static public function contains(string $haystack, string $needle): bool {
        return (strpos($haystack, $needle) !== false);
    }
    
    /**
     * Determines if the given haystack ends with the needle. When running on 
     * PHP >= 8.0 this function simply calls str_ends_with().
     * 
     * Example:
     * 
     * ``` php
     * if (strings::ends_with('What a nice day', 'day')) 
     *    println('The string ends with "day"');
     * // will print 'The string ends with "day"'.
     * ```
     */
    static public function ends_with(string $haystack, string $needle): bool
    {
        if (function_exists('str_ends_with'))
            return str_ends_with($haystack, $needle);
        
        if ($needle === '') 
            throw new \InvalidArgumentException("Empty needle is not allowed.");
        
	    if (strlen($needle) > strlen($haystack))
	        return false;
		$posFromRight = strlen($haystack) - strlen($needle);
	    return strrpos($haystack, $needle, $posFromRight) === $posFromRight;
    }
    
    
    /**
     * Determines if the given haystack starts with the needle. When running on 
     * PHP >= 8.0 this function simply calls str_starts_with().
     */
    static public function starts_with(string $haystack, string $needle): bool
    {
        if (function_exists('str_starts_with'))
            return str_starts_with($haystack, $needle);
        
        if ($needle === '') 
            throw new \InvalidArgumentException("Empty needle is not allowed.");
                
	    if (strlen($needle) > strlen($haystack))
	        return false;
		return strpos($haystack, $needle) === 0;
    }
    
    /**
     * Modify a string by splitting it by the given delimiter and popping $amount of elements off of the end.
     */
    static public function pop(string $string, string $delimiter, int $amount): string {
        return implode($delimiter, arrays::pop(explode($delimiter, $string), $amount));
    }
    
    /**
     * Modify a string by splitting it by the given delimiter and shifting $amount of elements off of the start.
     */
    static public function shift(string $string, string $delimiter, int $amount): string {
        return implode($delimiter, arrays::shift(explode($delimiter, $string), $amount));
    }
    
    
    /**
     * Split the string by the delimiter and return the shortened input string, providing
     * the popped item as output via the third parameter.
     * 
     * If the delimiter was not found and no item was shifted then this method returns the
     * original string.
     * 
     * Example:
     * 
     * ``` php
     * $modified = strings::popex("doug,30,manager", ',', $item);
     * // return 'doug,30' with 'manager' stored in $item
     * ```
     */
    static public function popex(string $string, string $delimiter, string &$poppedItem = null): string
    {
        if (strpos($string, $delimiter) !== false) {
            $array = arrays::pop(explode($delimiter, $string), 1, $items);
		    $poppedItem = $items[0];
            return implode($delimiter, $array);
        }
		return $string;
    }
    
    /**
     * Split the string by the delimiter and return the shortened input string, providing
     * the shifted item as output via the third parameter.
     * 
     * If the delimiter was not found and no item was shifted then this method returns the
     * original string.
     * 
     * Example:
     * 
     * ``` php
     * $modified = strings::shiftex("doug,30,manager", ',', $item);
     * // return '30,manager' with 'doug' stored in $item
     * ``` 
     */
    static public function shiftex(string $string, string $delimiter, string &$shiftedItem = null): string
    {
        if (strpos($string, $delimiter) !== false) {
            $array = arrays::shift(explode($delimiter, $string), 1, $items);
		    $shiftedItem = $items[0];
            return implode($delimiter, $array);
        }
		return $string;    
    }
    
    /**
     * Perform a search for a word in a string.
     */
	static public function contains_word(string $haystack, string $word)
	{
		return !!preg_match('#\\b'.preg_quote($word, '#').'\\b#i', $haystack);
	}
    
    /**
     * Perform a find & replace on a word in a string.
     */
    static public function replace_word(string $haystack, string $word, string $replacement)
    {
        $pattern = "/\b$word\b/i";
        return preg_replace($pattern, $replacement, $haystack);
    }
    
    /**
     * Replace a series of words with their counterpart provided in an
     * associative array.
     */
    static public function replace_words(string $haystack, array $wordMap)
    {
        foreach ($wordMap as $str => $replacement) 
            $haystack = self::replace_word($haystack, $str, $replacement);
        
        return $haystack;
    }
	
    /**
     * Translate the given text to a clean representation by removing all control or UTF characters that can
     * produce unreadable artefacts on various mediums of output such as HTML or PDF.
     * 
     * It also assumes the desired output is a UTF-8 string. If you are working with a different character set you
     * will need to employ an alternative cleaning system.
     * 
     * Passing in an array will cycle through and return a copy with all elements cleaned.
     * 
     * This method requires both mbstring and inconv extensions to be installed.
     */
	static public function clean(string $text)
	{
        if (is_array($text))
        {
            $out = [];
            foreach ($text as $item)
                $out[] = self::clean($item);
            return $out;
        }
		$detectedEncoding = mb_detect_encoding($text, mb_detect_order(), true);
        if ($detectedEncoding != 'UTF-8')
		    $text = iconv($detectedEncoding, 'UTF-8', $text);

		// First, replace UTF-8 characters.
		$text = str_replace(array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
		 	array("'", "'", '"', '"', '-', '--', '...'),
		 	$text
		);

		// Next, replace their Windows-1252 equivalents.
		$text = str_replace(array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
		 	array("'", "'", '"', '"', '-', '--', '...'),
		 	$text
		);
        
        $text = preg_replace('/[^\x0A\x20-\x7E]/','', $text);

		return trim($text);
	}
	
    /**
     * To replace all types of whitespace with a single space.
     */
    static public function one_space(string $str) 
    {
        $result = $str;
		$replacements = [
	        "  ", " \t",  " \r",  " \n",
	        "\t\t", "\t ", "\t\r", "\t\n",
	        "\r\r", "\r ", "\r\t", "\r\n",
	        "\n\n", "\n ", "\n\t", "\n\r",
        ];
        foreach ($replacements as $replacement) {
       	 	$result = str_replace($replacement, $replacement[0], $result);
        }
        return $str !== $result ? self::one_space($result) : $result;
    }
	
    /**
     * Truncate a string if it's length exceeds the specified maximum value.
     * Strings can be truncated from the left, middle or right.
     * 
     * [md-block]
     * Position options:
     * - `l`: truncate left
     * - `c`: truncate middle
     * - `r`: truncate right
     */
	static public function truncate(string $value, int $maxLength, string $position = 'l')
	{
		if ($position == 'r')
			$value = substr($value, 0, $maxLength)."...";
		
		else if ($position == 'l')
		{
			$diff = strlen($value) - $maxLength;
			$value = "...".substr($value, $diff);
		}
		else if ($position == 'c')
		{
			$len = strlen($value);
			$diff = $len - $maxLength;
			$midpoint = $len / 2;
			$left = substr($value, 0, $midpoint - ceil($diff / 2));
			$right = substr($value, $midpoint + floor($diff / 2));
	
			$value = "$left...$right";
		}
		
		else
			throw new \InvalidArgumentException("Unknown value passed to position parameter: '$position'");
		
		return $value;
	}
	
    /**
     * Filter out all non alpha-numeric characters. Optionally pass in a minimum and maximum string length
     * to invalidate any resulting string that does not meet the given boundaries.
     */
	static public function strip_non_alpha_numeric(string $string, ?int $min = null, ?int $max = null)
	{
	    $string = preg_replace("/[^a-zA-Z0-9]/", "", $string);
	    $len = strlen($string);
	    
		if (($min && ($len < $min)) || ($max && ($len > $max)))
	      return false;
		
	    return $string;
	}
	
    /**
     * Format and print out a series of rows and columns using the provided array of headers
     * as the table header.
     * 
     * The data array provided should be in an array of rows, each row being an associative
     * array of the column names (corresponding to those passed in as the header) and the
     * related value.
     */
    static public function columnize(array $array, array $headers, bool $printHeaders = true, bool $printNumericIndexes = true)    
    {
        $spacers = [];
        
        // get the spacing for the index column.
        $keys = array_keys($array);
        $longest = 5;
        foreach ($keys as $k) 
        {
             $len = strlen($k);
             if ($len > $longest)
                 $longest = $len;  
        }
        $m = ["%-$longest".'s'];
        $spacers['_index'] = $longest;
        
        // spacing for all the headers.
        
        foreach ($headers as $h) {
            $longest = strlen($h);
            foreach ($array as $row) {
                $value = $row[$h] ?? ''; 
                $len = strlen((string)$value);
                if ($len > $longest)
                    $longest = $len;
            }
            $m[] = "%$longest".'s'; 
            $spacers[$h] = $longest;
        }
        $mask = implode("\t", $m);
        $lines = [];
        if ($printHeaders)
            $lines[] = vsprintf($mask, array_merge([' '], $headers));
        else {
            $placeholders = [];
            foreach ($headers as $h)
                $placeholders[] = ' ';
            $lines[] = vsprintf($mask, array_merge([' '], $placeholders));
        }
        
        if ($printHeaders)
        {
            // print underlines.
            $out = [str_repeat('_', $spacers['_index'])];
            foreach ($headers as $h)
                $out[] = str_repeat('_', $spacers[$h]);
            $lines[] = vsprintf($mask, $out);
        }
            
        foreach ($array as $k => $v)
        {
            $out = [];
            $out[] = $printNumericIndexes || ! is_int($k) ? $k : ' ';
            foreach ($headers as $h) {
                $out[] = $array[$k][$h] ?? '';
            }
            $lines[] = vsprintf($mask, $out);
        }
    
        return PHP_EOL.implode(PHP_EOL, $lines);
    }
}