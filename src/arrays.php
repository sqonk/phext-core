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
* @license		MIT license.txt
* @copyright	2019 Sqonk Pty Ltd.
*
*
* This file is distributed
* on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
* express or implied. See the License for the specific language governing
* permissions and limitations under the License.
*/


/**
 * A set of standard array functions designed to keep your code easier to read
 * and remain obvious as to what is going on.
 */
class arrays
{
    /**
     * Safely return the value from the given array under the given key. If the key does not
     * exist in the array (or is `NULL`) then the value specified by $defaultValue is returned 
     * instead.
     * 
     * This method allows you to avoid potential errors caused by trying to directly access
     * non-existent keys by normalising the result regardless of whether the key is not set
     * or if the value is empty.
     */
	static public function safe_value(array $array, $key, $defaultValue = null): mixed {
		return ! isset($array[$key]) ? $defaultValue : $array[$key];		
	}
    
    /**
     * Alias for `safe_value`.
     */
    static public function get(array $array, $key, $defaultValue = null): mixed {
        return self::safe_value($array, $key, $defaultValue);
    }
    
    /**
     * Pop elements off the end of the array to the number specified in the $amount parameter.
     */
    static public function pop(array $array, int $amount, &$poppedItems = []): array
    {
        for ($i = 0; $i < $amount; $i++)
            $poppedItems[] = array_pop($array);
        return $array;
    }
    
    /**
     * Shift elements off the start of the array to the number specified in the $amount parameter.
     */
    static public function shift(array $array, int $amount, &$shiftedItems = []): array
    {
        for ($i = 0; $i < $amount; $i++)
            $shiftedItems[] = array_shift($array);
        return $array;
    }
	
	/**
	 * Add an item to end of an array. If the array count exceeds maxItems then shift first item off.
	 */
	static public function add_constrain(array &$array, mixed $value, int $maxItems): array
	{
	    $array[] = $value;
        if (count($array) > $maxItems)
            array_shift($array);
        
        return $array;
	}
    
    /**
     * Sort the given array using a standard sort method. This method is intended as a wrapper
     * for the in-built native sorting methods, which typically modify the original array by
     * reference instead of returning a modified copy.
     * 
     * [md-block]
     * $mode can have three possible values:
     * - `BY_VALUE` (default): standard sort of the array values.
     * - `BY_KEY`: Sort based on the array indexes.
     * - `MAINTAIN_ASSOC`: Standard sort of the array values but maintaining index association.
     * 
     * Refer to the PHP documentation for all possible values on the $sort_flags.
     * 
     * Depending on the value of $mode this method will utilise either `sort`, `asort` or `ksort`
     */    
    static public function sorted(array $array, int $mode = BY_VALUE, int $sort_flags = SORT_REGULAR): array
    {
        if ($mode == BY_KEY)
            ksort($array, $sort_flags);
        else if ($mode == MAINTAIN_ASSOC)
            asort($array, $sort_flags); 
        else
            sort($array, $sort_flags);
        
        return $array;
    }
    
    /**
     * Sort the given array in reverse order using a standard sort method. This method is intended
     * as a wrapper for the in-built native sorting methods, which typically modify the original
     * array by reference instead of returning a modified copy.
     * 
     * [md-block]
     * $mode can have three possible values:
     * - `BY_VALUE` (default): standard sort of the array values.
     * - `BY_KEY`: Sort based on the array indexes.
     * - `MAINTAIN_ASSOC`: Standard sort of the array values but maintaining index association.
     * 
     * Refer to the PHP documentation for all possible values on the $sort_flags.
     * 
     * Depending on the value of $mode this method will utilise either `rsort`, `arsort` or `krsort`
     */
    static public function rsorted(array $array, int $mode = BY_VALUE, int $sort_flags = SORT_REGULAR): array
    {
        if ($mode == BY_KEY)
            krsort($array, $sort_flags);
        else if ($mode == MAINTAIN_ASSOC)
            arsort($array, $sort_flags); 
        else
            rsort($array, $sort_flags);
        
        return $array;
    }
	
    /**
     * Sort an array of arrays or objects based on the value of a key inside of the sub-array/object.
     * 
     * If $key is an array then this method will perform a multi-sort, ordering by each key with
     * sort priority given in ascending order.
     * 
     * As per the native sorting methods, the array passed in will be modified directly. As an added
     * convenience the array is also returned to allow method chaining.
     * 
     * Internally this function will use either usort or uasort depending on whether $maintainKeyAssoc
     * is set to TRUE or FALSE. Setting it to TRUE will ensure the array indexes are maintained.
     */
	static public function key_sort(array &$array, string|int|float $key, bool $maintainKeyAssoc = false): array
	{
		$keys = is_array($key) ? $key : [ $key ];
		
		$comp = function($a, $b) use ($keys) 
		{
			$r = 0;
			foreach ($keys as $k)
			{
				$a_val = is_object($a) ? $a->{$k} : ($a[$k] ?? null);
				$b_val = is_object($b) ? $b->{$k} : ($b[$k] ?? null);
			
				if (is_string($a_val)) 
					$r = strcmp($a_val, $b_val);
			
				else if ($a_val == $b_val)
					continue;
			
				else {
					$r = ($a_val < $b_val) ? -1 : 1;
					break;
				}
				
				if ($r != 0)
					break;	
			}
			return $r;
		};
		
		if ($maintainKeyAssoc)
			uasort($array, $comp);
		else
			usort($array, $comp);
		
		return $array;
	}
    
    /**
     * Takes a flat array of elements and splits them into a tree of associative arrays based on
     * the keys passed in.
     * 
     * You need to ensure the array is sorted by the same order as the set of keys being used
     * prior to calling this method. If only one key is required to split the array then a singular
     * string may be provided, otherwise pass in an array.
     * 
     * Unless $keepEmptyKeys is set to TRUE then any key values that are empty will be omitted.
     * 
     * This method operates in a recursive fashion and the last parameter $pos is used internally
     * when in operation. You should never need to pass in a custom value to $pos yourself.
     */
	static public function group_by(array $items, array|string $keys, bool $keepEmptyKeys = false, int $pos = 0): array
	{
		if (is_string($keys)) {
			if ($pos > 0)
				throw new \InvalidArgumentException("supplied keys must be an array when position parameter is greater than 0, string given ('$pos' as pos and '$keys' for key).");
			$keys = [$keys];
		}

		$key = $keys[$pos];
		$sets = [];
		$currentSet = $currentKeyValue = null;
		
		foreach ($items as $item)
		{
			$keyValue = is_iterable($item) ? $item[$key] : $item->{$key};
			if ($keyValue or $keepEmptyKeys) {
				if ($keyValue != $currentKeyValue)
				{
					$nextPos = $pos+1;
					if ($currentSet && $nextPos < count($keys))
						$sets[$currentKeyValue] = self::group_by($currentSet, $keys, $keepEmptyKeys, $nextPos);
					
					else if ($currentSet)
						$sets[$currentKeyValue] = $currentSet;
					
					$currentSet = [];
					$currentKeyValue = $keyValue;
				}
				$currentSet[] = $item;
			}
		} 
		
		// trailing set
		if ($currentSet && $pos+1 < count($keys))
			$sets[$currentKeyValue] = self::group_by($currentSet, $keys, $keepEmptyKeys, $pos+1);
		
		else if ($currentSet)
			$sets[$currentKeyValue] = $currentSet;
		
		return $sets;
	}
    
    /**
     * Alias of group_by.
     */
    static public function groupby(array $items, $keys, bool $keepEmptyKeys = false, int $pos = 0): array {
        return self::group_by($items, $keys, $keepEmptyKeys, $pos);
    }
    
    /**
     * Split an array into a series of arrays based the varying results returned from a supplied callback.
     * 
     * This method differs from `groupby` in that it does not care about the underlying elements
     * within the array and relies solely on the callback to determine how the elements are divided up, 
     * where as `groupby` is explicity designed to work with an array of objects or entities that
     * respond to key lookups. Further to this, `groupby` can produce a tree structure of nested arrays
     * where as `splitby` will only ever produce one level of arrays.
     * 
     * The values returned from the callback must be capable of being used as an array key
     * (e.g. strings, numbers). This is done by a `var_is_stringable` check. NULL values are allowed 
     * but used to omit the associated item from any of the sets.
     * 
     * -- parameters:
     * @param $callback A callback method that will produce the varying results used to sort each element into its own set.
     * 
     * Callback format: `myFunc($value, $index) -> mixed`
     * 
     * @throws UnexpectedValueException If the value returned from the callback is not capable of being used as an array key.
     * 
     * @return An array of arrays, one each for each different result returned from the callback.
     * 
     * Example Usage:
     * 
     * ``` php
     * $numbers = [1,2,3,4,5,6,7,8,9,10];
     * $sets = arrays::splitby($numbers, fn($v) => ($v % 2 == 0) ? 'even' : 'odd');
     * println($sets);
     * // array (
     * //   'odd' =>
     * //   array (
     * //     0 => 1,
     * //     1 => 3,
     * //     2 => 5,
     * //     3 => 7,
     * //     4 => 9,
     * //   ),
     * //   'even' =>
     * //   array (
     * //     0 => 2,
     * //     1 => 4,
     * //     2 => 6,
     * //     3 => 8,
     * //     4 => 10,
     * //   ),
     * // )
     * ```
     */
    static public function splitby(array $array, callable $callback): array
    {
        $groups = [];
        foreach ($array as $index => $item) {
            $k = $callback($item, $index);
            
            if ($k !== null && ! var_is_stringable($k))
                throw new \UnexpectedValueException("Non-stringable value returned from callback. Your callback must return a value that is capable of being used as an array key.");
            
            if ($k !== null)
                $groups[$k][] = $item;
        }
        
        return $groups;
    }
    
    /**
     * Transform a set of rows and columns with vertical data into a horizontal configuration
     * where the resulting array contains a column for each different value for the given
     * fields in the merge map (associative array).
     * 
     * -- parameters:
     * @param $array Associative (keyed) array of values.
     * @param $groupKey Used to specify which key in the $array will be used to flatten multiple rows into one.
     * @param $mergeMap Associative (keyed) array specified pairs of columns that will be merged into header -> value.
     * 
     * Example:
     * 
     * ``` php
     * $data = [
     *     ['character' => 'Actor A', 'decade' => 1970, 'appearances' => 1],
     *     ['character' => 'Actor A', 'decade' => 1980, 'appearances' => 2],
     *     ['character' => 'Actor A', 'decade' => 1990, 'appearances' => 2],
     *     ['character' => 'Actor A', 'decade' => 2000, 'appearances' => 1],
     *     ['character' => 'Actor A', 'decade' => 2010, 'appearances' => 1],
     *     ['character' => 'Actor B', 'decade' => 1980, 'appearances' => 1],
     *     ['character' => 'Actor B', 'decade' => 1990, 'appearances' => 1],
     *     ['character' => 'Actor B', 'decade' => 2000, 'appearances' => 1],
     * ];
     * println(strings::columnize($data, ['decade', 'character', 'appearances']));
     * //          decade    character    appearances
     * // _____    ______    _________    ___________
     * // 0          1970      Actor A              1
     * // 1          1980      Actor A              2
     * // 2          1990      Actor A              2
     * // 3          2000      Actor A              1
     * // 4          2010      Actor A              1
     * // 5          1980      Actor B              1
     * // 6          1990      Actor B              1
     * // 7          2000      Actor B              1
     * // TAKE NOTE: The $data array is pre-sorted by the group key prior to being transposed, this is critical for correct behaviour. 
     * $data = arrays::key_sort($data, 'decade');
     * // Transform the matrix using transpose() so that each character becomes a column
     * // with their resulting appearances listed alongside the decade.
     * $transformed = arrays::transpose($data, 'decade', ['character' => 'appearances']);
     * println(strings::columnize($transformed, ['decade', 'Actor A', 'Actor B']));
     * //          decade    Actor A    Actor B
     * // _____    ______    _______    _______
     * // 0          1970          1
     * // 1          1980          2          1
     * // 2          1990          2          1
     * // 3          2000          1          1
     * // 4          2010          1    
     * ```
     */
    static public function transpose(array $array, string $groupKey, array $mergeMap): array
    {   
        $mergeKeys = array_keys($mergeMap);
        $all_key_types = [];
        foreach ($mergeKeys as $key) {
            $values = [];
            foreach ($array as $row)
                $values[] = $row[$key];
            $all_key_types[$key] = array_unique($values);
        }
        
        $grouped = self::group_by($array, $groupKey, true); 
        $rows = [];
        $mapKeys = array_keys($mergeMap);
        $mapValues = array_values($mergeMap);
        
        foreach ($grouped as $identifier => $set)
        {
            $row = [$groupKey => $identifier];
            
            foreach ($set as $v) {
                foreach ($mergeMap as $key => $valueKey)
                    $row[$v[$key]] = $v[$valueKey]; 
            }
            $all_types = $all_key_types[$key];
            foreach ($all_types as $tvalue) {
                if (! isset($row[$tvalue])) {
                    $row[$tvalue] = ''; 
                }
            }
            foreach ($set[0] as $vk => $vv) {
                // add all other values from the row not in the merge map from 
                // the first item in the set.
                if (! self::contains($mapKeys, $vk) && ! self::contains($mapValues, $vk))
                    $row[$vk] = $vv; 
            }
            
            $rows[] = $row;
        }
        
        return $rows;
    }
    
    /**
     * Alias for self::first.
     */
	static public function start(iterable $array): mixed {
		return self::first($array);
	}
	
    /**
     * Return the first object in the array or null if array is empty.
     */
	static public function first(iterable $array): mixed
	{
		if (is_iterable($array) && count($array) > 0) {
			$keys = array_keys($array);
			return $array[$keys[0]];
		}
		return null;
	}
	
    /**
     * Return the last object in the array or null if array is empty.
     */
	static public function end(iterable $array): mixed {
		return (is_iterable($array) && count($array) > 0) ? end($array) : null;
	}
	
    /**
     * Alias for self::end.
     */
	static public function last(iterable $array): mixed {
		return self::end($array);
	}
    
    /**
     * Return the object closest to the middle of the array.
     * 
     * -- parameters:
     * @param $array The array containing the items.
     * @param $weightedToFront TRUE to favour centre items closer to the start of the array and FALSE to prefer items closer to the end.
     * 
     * @return object closest to the middle of the array.
     * 
     * [md-block]
     * - If the array is empty, returns null.
     * - If the array has less than 3 items, then return the first or last item depending
     * on the value of $weightedToFront.
     * - Otherwise return the object closest to the centre. When dealing with arrays containing
     * and even number of items then it will use the value of $weightedToFront to determine if it
     * picks the item closer to the start or closer to the end.
     */
    static public function middle(iterable $array, bool $weightedToFront = true): mixed
    {
        if (is_iterable($array))
        {
            $cnt = count($array);
            if ($cnt > 0)
            {
                if ($cnt == 1) {
                    return $array[0];
                }
                else if ($cnt == 2) {
                    return $weightedToFront ? $array[0] : $array[1];
                }
                else {
                    $midpoint = (float)($cnt / 2);
                    if (($cnt % 2) != 0) {
                        $midpoint = floor($midpoint);
                        return $array[$midpoint];
                    }
                    else {
                        return $weightedToFront ? $array[$midpoint-1] : $array[$midpoint];
                    }
                }
            }
        }
        return null;
    }
    
    /**
     * Creates a copy of the provided array where all values corresponding to 'empties' are omitted.
     */
    static public function prune(iterable $array, $empties = ''): array
    {
        $comp = [];
        foreach ($array as $key => $value) { 
            if ($value !== $empties) 
                $comp[$key] = $value;
        }
        return $comp;
    }
    
    /**
     * Creates a copy of the provided array where all NULL values are omitted.
     */
    static public function compact(iterable $array): array
    {
        $comp = [];
        foreach ($array as $key => $value) { 
            if ($value !== null) 
                $comp[$key] = $value;
        }
        return $comp;
    }
	
    /**
     * Return a copy of an array containing only the values for the specified keys,
     * with index association being maintained.
     * 
     * This method is primarily designed for associative arrays. It should be
     * noted that if a key is not present in the provided array then it will not
     * be present in the resulting array.
     */
	static public function only_keys(array $array, ...$keys): array
	{
        if (count($keys) == 1 and is_array($keys[0]))
            $keys = $keys[0];
        
		foreach ($array as $key => $value)
			if (! self::contains($keys, $key))
				$array[$key] = null;
		return self::compact($array);
	}
	
    /**
     * Apply a callback function to the supplied array. This version will optionally
     * supply the corresponding index/key of the value when needed (unlike the built-in
     * array_map() method).
     * 
     * Callback format: `myFunc($value, $index) -> mixed`
     */
	static public function map(iterable $array, callable $callback): array
	{
		$out = [];
		foreach ($array as $index => $value) {
			$out[$index] = $callback($value, $index);
		}
		return $out;
	}
    
    /**
     * Randomly choose an item from the given array.
     * 
     * Example:
     * 
     * ``` php
     * $numbers = [1,2,3,4,5,6,7,8,9,10];
     * $choice = arrays::choose($numbers);
     * // return a random selection from provided array.
     * ```
     */
    static public function choose(iterable $array): mixed
    {
        if (count($array) == 0)
            return null;
		
		$keys = array_keys($array);
		$selection = $keys[ rand(0, count($keys)-1) ];
		
        return $array[ $selection ];
    }
    
    /**
     * Generate an array of random numbers between the given $min and
     * $max. The array will be $amount long.
     */
    static public function sample(int $min, int $max, int $amount): array
    {
        $out = [];
        foreach (sequence(1, $amount) as $i)
            $out[] = rand($min, $max);
        return $out;
    }
	
    /**
     * Iterate through a series of arrays, yielding the value of the corresponding index
     * in each a sequential array to your own loop.
     * 
     * This method can handle both associative and non-associative arrays.
     * 
     * Example usage:
     * 
     * ``` php
     * $array1 = ['a', 'b', 'c'];
     * $array2 = [1, 2, 3, 4];
     * $array3 = ['#', '?'];
     * foreach (arrays::zip($array1, $array2, $array3) as [$v1, $v2, $v3])
     * 	println($v1, $v2, $v3);
     * // Prints:
     * // a 1 #
     * // b 2 ?
     * // c 3 
     * //   4 
     * ```
     */
    static public function zip(iterable ...$arrays): \Generator
    {
        foreach ($arrays as $item)
            if (! is_iterable($item))
                throw new \InvalidArgumentException('All parameters passed to zip must be iterable.');
        
        $counts = array_map(fn($arr) => count($arr), $arrays);
        $keys = array_map(fn($arr) => array_keys($arr), $arrays);
        
        foreach (sequence(0, max($counts)-1) as $index)
        {
            $values = [];
            foreach (range(0, count($arrays)-1) as $arrayNo)
            {
				$subarray = $arrays[$arrayNo] ?? [];
                $key = $keys[$arrayNo][$index] ?? null;
                $values[] = $subarray[$key] ?? null;
            }
            
            yield $values;
        }
    }
	
    /**
     * Iterate through a series of arrays, yielding the values for every possible
     * combination of values.
     * 
     * For example, with 2 arrays this function will yield for every element in array 2 with
     * the value in the first index of array 1. It will then yield for every element in
     * array 2 with the value in the second index of array 1, etc.
     * 
     * This method can handle both associative and non-associative arrays.
     * 
     * Example usage:
     * 
     * ``` php
     * $array1 = ['a', 'b', 'c'];
     * $array2 = [1, 2, 3, 4];
     * $array3 = ['#', '?'];
     * foreach (arrays::zipall($array1, $array2, $array3) as [$v1, $v2, $v3])
     * 	println($v1, $v2, $v3);
     * // a 1 #
     * // a 1 ?
     * // a 2 #
     * // a 2 ?
     * // a 3 #
     * // a 3 ?
     * // a 4 #
     * // a 4 ?
     * // b 1 #
     * // b 1 ?
     * // b 2 #
     * // b 2 ?
     * // b 3 #
     * // b 3 ?
     * // b 4 #
     * // b 4 ?
     * // c 1 #
     * // c 1 ?
     * // c 2 #
     * // c 2 ?
     * // c 3 #
     * // c 3 ?
     * // c 4 #
     * // c 4 ?
     * ```
     */
	static public function zipall(iterable ...$arrays): \Generator
	{
		if (count($arrays) < 2)
			throw new \InvalidArgumentException('This method expects at least 2 arrays');
			
        foreach ($arrays as $item)
            if (! is_iterable($item))
                throw new \InvalidArgumentException('All parameters passed to zip must be iterable.');
		
		yield from self::_yieldvalues(array_shift($arrays), $arrays);
	}
	
	/**
	 * Internal method. Companion method to zipall.
	 */
	static protected function _yieldvalues(array $primary, array $others, array $currentValues = []): \Generator
	{
		$count = count($others);
		$newPrimary = array_shift($others);
		foreach ($primary as $mvalue)
		{
			$values = array_merge($currentValues, [$mvalue]); 
			if ($count == 0)
				yield $values;
			
			else 
				yield from self::_yieldvalues($newPrimary, $others, $values);
		}			
	}
	
    /**
     * Attempt to determine if the given array is either sequential or hashed.
     * 
     * In PHP 8.1 or later this method return the inverse of `array_is_list`.
     * 
     * In PHP 8, this method works by extracting the keys of the array and performing a
     * comparison of the keys of the given array and the indexes of the extracted
     * key array to see if they match. If they do not then the provided array
     * is likely associative.
     */
	static public function is_assoc(array $array): bool
	{
        if (function_exists('array_is_list')) {
            return ! array_is_list($array);
        }
	    // Keys of the array
	    $keys = array_keys($array);

	    // If the array keys of the keys match the keys, then the array must
	    // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
	    $keys_of_keys = array_keys($keys);
		$diff = array_diff($keys, $keys_of_keys);
		return count($diff) > 0; 
	}
	
    /**
     * Return a copy of an array with every item wrapped in the provided tokens. If no
     * end token is provided then the $startToken is used on both ends.
     * 
     * NOTE: This function expects all items in the array to convertible to a string.
     */
	static public function encapsulate(array $array, string $startToken, string $endToken = null): array
	{
		if ($endToken === null)
			$endToken = $startToken;
		
		return array_map(function($value) use($startToken, $endToken) { 
			return sprintf("%s%s%s", $startToken, $value, $endToken); 
		}, $array);
	}
	
    /**
     * Implode an associate array into a string where each element of the array is
     * imploded with a given delimiter and each key/value pair is imploding using a
     * different delimiter.
     */
	static public function implode_assoc(string $delim, array $array, string $keyValueDelim): string
	{
		$new_array = self::map($array, function($value, $key) use ($keyValueDelim) {
			return $key.$keyValueDelim.$value;
		});

		return implode($delim, $new_array);
	}
	
    /**
     * Return the values in the provided array belonging to the specified keys.
     * 
     * This method is primarily designed for associative arrays.
     * 
     * Example:
     * 
     * ``` php
     * $info = ['name' => 'Doug', 'age' => 30, 'job' => 'Policeman'];
     * println(arrays::values($info, 'name', 'age'));
     * // Prints: array (
     * //  0 => 'Doug',
     * //  1 => 30,
     * //)
     * ```
     */
	static public function values(array $array, ...$keys): array
	{
		$item_vals = [];
		foreach ($keys as $key) {
			if (array_key_exists($key, $array))
				$item_vals[] = $array[$key];
		}
		return $item_vals;
	}
	
	
    /**
     * This method acts in a similar fashion to the native 'implode', however in addition it
     * will recursively implode any sub-arrays found within the parent.
     * 
     * You may optionally provide a $subDelimiter to be applied to any inner arrays. If
     * nothing is supplied then it will default to the primary delimiter.
     */
	static public function implode(string $delimiter, array $array, string $subDelimiter = null): string
	{
		if ($subDelimiter === null)
			$subDelimiter = $delimiter;
		
		$copy = self::map($array, function($element) use ($subDelimiter) {
			
			if (is_array($element))
				return self::implode($subDelimiter, $element);
			
			return $element;
		});
		
		return implode($delimiter, $copy);
	}
	
    /**
     * Implode the given array using the desired delimiter. This method differs from
     * the built-in implode in that it will only implode the values associated with
     * the specified keys/indexes.
     * 
     * Empty values are automatically removed prior to implosion.
     */
	static public function implode_only(string $delimiter, array $array, ...$keys): string
	{
		return implode($delimiter, array_filter(self::values($array, ...$keys), function($v) {
			return ! empty($v);
		}));
	}
    
    
    /**
     * Search an array for the given needle (subject). If the needles is a callable reference then
     * each value is provided to the callback and expects to receive a TRUE/FALSE answer.
     * 
     * If the needle is anything else then this method utilises `in_array` for determining the answer.
     */
    static public function contains(array $haystack, mixed $needle, bool $strict = false): bool
    {
		if (is_callable($needle) && ! is_string($needle))
		{
			foreach ($haystack as $value) {
				if ($needle($value))
					return true;
			}
			return false;
		}
        
        return in_array($needle, $haystack, $strict);
    }
    
    /**
     * Search the array for an item that matches an arbitrary condition specified
     * by a callback method. 
     * 
     * This method can be useful for searching multi-dimensional arrays to locate 
     * a specific item.
     * 
     * -- parameters:
     * @param $haystack The array to search.
     * @param $callback The callback method that will examine each item within the array.
     * 
     * Callback format: `myFunc($value, $index) -> bool`
     * 
     * @return The first item where $callback returns TRUE will be returned as the result, NULL if there are no matches.
     */
    static public function first_match(array $haystack, callable $callback): mixed
    {
        foreach ($haystack as $index => $item) {
            if ($callback($item, $index))
                return $item;
        }
        return null;
    }
    
    /**
     * Alias of contains().
     */
    static public function any(array $haystack, mixed $needle, bool $strict = false): bool {
        return self::contains($haystack, $needle, $strict);
    }
    
    /**
     * Returns TRUE if all of the values within the array are equal to the value
     * provided, FALSE otherwise.
     * 
     * A callback may be provided as the match to perform more complex testing.
     * 
     * Callback format: `myFunc($value) -> bool`
     * 
     * For basic (non-callback) matches, setting $strict to TRUE will enforce
     * type-safe comparisons.
     */
    static public function all(array $haystack, mixed $needle, bool $strict = false): bool
    {
		$isCallback = is_callable($needle);
		foreach ($haystack as $value) {
			if (($isCallback and ! $needle($value)) or 
				(! $isCallback and (! $strict && $value != $needle) or ($strict && $value !== $needle)) )
				return false;
		}
		return true;
    }
    
    
    /**
     * Determines if the given haystack ends with the needle. The comparison is non-strict.
     */
    static public function ends_with(array $haystack, mixed $needle): bool {
        return (count($haystack) > 0) ? self::last($haystack) == $needle : false;
    }
    
    /**
     * Determines if the given haystack starts with the needle. The comparison is non-strict.
     */
    static public function starts_with(array $haystack, mixed $needle): bool {
        return (count($haystack) > 0) ? $haystack[0] == $needle : false;
    }
    
    /**
     * Is the given value both a valid array and does it contain at least one element?
     * 
     * @deprecated Consider simply calling empty() on your variable instead.
     */
    static public function is_populated($value): bool {
        return is_array($value) && ! empty($value);
    }
}