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

/*
    A set of standard array functions designed to keep your code easier to read
    and remain obvious as to what is going on.
*/
class arrays
{
    /*
        Safely return the value from the given array under the given key. If the key does not
        exist in the array then the value specified by $defaultValue is returned instead.
    
        This method allows you to avoid protential errors caused by trying to directly access
        non-existant keys by normalising the result regardless of whether the key is not set
        or if the value is empty.
	
		As of PHP 7.4 $anArray[$key] ??= $defaultValue does the same thing.
    */
	static public function safe_value($anArray, $key, $defaultValue = null)
	{
		return ! isset($anArray[$key]) ? $defaultValue : $anArray[$key];		
	}
    
    // Alias for safe_value().
    static public function get($anArray, $key, $defaultValue = null)
    {
        return self::safe_value($anArray, $key, $defaultValue);
    }
    
    // Pop elements off the end of the array to the number specified in the 'amount' parameter.
    static public function pop(array $array, int $amount, &$poppedItems = [])
    {
        for ($i = 0; $i < $amount; $i++)
            $poppedItems[] = array_pop($array);
        return $array;
    }
    
    // Shift elements off the start of the array to the number specified in the 'amount' parameter.
    static public function shift(array $array, int $amount, &$shiftedItems = [])
    {
        for ($i = 0; $i < $amount; $i++)
            $shiftedItems[] = array_shift($array);
        return $array;
    }
	
	// Add an item to end of an array. If the array count exceeds maxItems then shift first item off.
	static public function add_constrain(array &$array, $value, int $maxItems)
	{
	    $array[] = $value;
        if (count($array) > $maxItems)
            array_shift($array);
	}
	
	/*
		Sort an array of arrays or objects based on the value of a key inside of the sub-array/object.
	
		If $key is an array then this method will perform a multi-sort, ordering by each key with 	
		sort priroity given in ascending order.
	
		As per the native sorting methods, the array passed in will be modified directly. As an added
		convienience the array is also returned to allow method chaining.
	
		Internally this function will use either usort or uasort depending on whether $maintainKeyAssoc
		is set to TRUE or FALSE. Setting it to TRUE will ensure the array indexes are maintained.
	*/
	static public function key_sort(array &$array, $key, bool $maintainKeyAssoc = false)
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
	static public function group_by(array $items, $keys, bool $keepEmptyKeys = false, int $pos = 0)
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
    static public function transpose(array $array, string $groupKey, array $mergeMap)
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
    
    // Alias for self::first.
	static public function start(iterable $array)
	{
		return self::first($array);
	}
	
    // Return the first object in the array or null if array is empty.
	static public function first(iterable $array)
	{
		if (is_iterable($array) && count($array) > 0) {
			$keys = array_keys($array);
			return $array[$keys[0]];
		}
		return null;
	}
	
    // Return the last object in the array or null if array is empty.
	static public function end(iterable $array)
	{
		return (is_iterable($array) && count($array) > 0) ? end($array) : null;
	}
	
    // Alias for self::end.
	static public function last(iterable $array)
	{
		return self::end($array);
	}
    
    /*
        Return the object closest to the middle of the array. 
        - If the array is empty, returns null.
        - If the array has less than 3 items, then return the first or last item depending 
        on the value of $weightedToFront.
        - Otherwise return the object closest to the centre. When dealing with arrays containing
        and even number of items then it will use the value of $weightedToFront to determine if it
        picks the item closer to the start or closer to the end.
    
        @param $array               The array containing the items.
        @param $weightedToFront     TRUE to favour centre items closer to the start of the array 
                                    and FALSE to prefer items closer to the end.
    */
    static public function middle(iterable $array, bool $weightedToFront = true)
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
    
    /*
        Creates a copy of the provided array where all values corresponding to 'empties' are omitted.
    */
    static public function prune(array $array, $empties = '')
    {
        $comp = [];
        foreach ($array as $key => $value) { 
            if ($value !== $empties) 
                $comp[$key] = $value;
        }
        return $comp;
    }
    
    /*
        Creates a copy of the provided array where all NULL values are omitted.
    */
    static public function compact(array $array)
    {
        $comp = [];
        foreach ($array as $key => $value) { 
            if ($value !== null) 
                $comp[$key] = $value;
        }
        return $comp;
    }
	
	// Prune an associative array so that all keys other than ones provided are removed.
	static public function only_keys(array $array, ...$keys)
	{
		foreach ($array as $key => $value)
			if (! self::contains($keys, $key))
				$array[$key] = null;
		return self::compact($array);
	}
	
	/*
		Apply a callback function to the supplied array. This version will optionally
		supply the corresponding index/key of the value when needed (unlike the built-in
		array_map() method).
	
		Callback format: myFunc($value, $index) -> mixed
	*/
	static public function map(array $array, callable $callback)
	{
		$out = [];
		foreach ($array as $index => $value) {
			$out[$index] = $callback($value, $index);
		}
		return $out;
	}
    
    /*
        Randomly choose an item from the given array.
    */
    static public function choose(iterable $array)
    {
        if (count($array) == 0)
            return null;
		
		$keys = array_keys($array);
		$selection = $keys[ rand(0, count($keys)-1) ];
		
        return $array[ $selection ];
    }
	
    /*
        Iterate through a series of arrays, yielding the value of the correpsonding index
        in each a sequential array to your own loop.
    
        This method can handle both associative and non-associative arrays.
    
        Example usage:
            foreach (arrays::zip($array1, $array2, $array3) as list($v1, $v2, $v3));
    */
    static public function zip(...$arrays)
    {
        foreach ($arrays as $item)
            if (! is_iterable($item))
                throw new \InvalidArgumentException('All parameters passed to zip must be iterable.');
        
        $counts = array_map(function($arr) { 
			return count($arr); 
		}, $arrays);
        $keys = array_map(function($arr) { 
			return array_keys($arr); 
		}, $arrays);
        
        foreach (sequence(0, max($counts)-1) as $index)
        {
            $values = [];
            foreach (range(0, count($arrays)-1) as $arrayNo)
            {
				$subarray = self::get($arrays, $arrayNo, []);
                $key = self::get($keys[$arrayNo], $index);
                $values[] = self::get($subarray, $key);
            }
            
            yield $values;
        }
    }
	
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
	static public function zipall(...$arrays)
	{
		if (count($arrays) < 2)
			throw new \InvalidArgumentException('This method expects at least 2 arrays');
			
        foreach ($arrays as $item)
            if (! is_iterable($item))
                throw new \InvalidArgumentException('All parameters passed to zip must be iterable.');
		
		yield from self::_yieldvalues(array_shift($arrays), $arrays);
	}
	
	// Internal method. Companion method to zipall.
	static protected function _yieldvalues(array $primary, array $others, array $currentValues = [])
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
	
    /*
        Attempt to determine if the given array is either sequential or hashed.
    
        This method works by extracting the keys of the array and performing a
        comparison of the keys of the given array and the indexes of the extracted
        key array to see if they match. If they do not then the provided array
        is likely associative.
    */
	static public function is_assoc(array $array)
	{
	    // Keys of the array
	    $keys = array_keys($array);

	    // If the array keys of the keys match the keys, then the array must
	    // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
	    $keys_of_keys = array_keys($keys);
		$diff = array_diff($keys, $keys_of_keys);
		return count($diff) > 0; 
	}
	
	/*
		Return a copy of an array with every item wrapped in the provided tokens. If no
		end token is provided then the $startToken is used on both ends.
	
		NOTE: This function expects all items in the array to convertable to a string.
	*/
	static public function encapsulate(array $array, string $startToken, string $endToken = null)
	{
		if ($endToken === null)
			$endToken = $startToken;
		
		return array_map(function($value) use($startToken, $endToken) { 
			return sprintf("%s%s%s", $startToken, $value, $endToken); 
		}, $array);
	}
	
    /*
        Implode an associate array into a string where each element of the array is 
		imploded with a given delimiter and each key/value pair is imploding using a 
		different delimiter.
    */
	static public function implode_assoc(string $delim, array $array, string $keyValueDelim)
	{
		$new_array = self::map(function($value, $key) use ($keyValueDelim) {
			return $key.$keyValueDelim.$value;
		}, $array);

		return implode($delim, $new_array);
	}
    
    // Search an array for the given needle (subject).
    static public function contains(array $haystack, $needle)
    {
        return in_array($needle, $haystack);
    }
    
    // Determines if the given haystack ends with the needle.
    static public function ends_with(array $haystack, $needle)
    {
        if (! $needle) 
            throw new \InvalidArgumentException("Empty needle is not allowed.");
        
        return (count($haystack) > 0) ? self::last($haystack) == $needle : false;
    }
    
    // Determines if the given haystack starts with the needle.
    static public function starts_with(array $haystack, $needle)
    {
        if (! $needle) 
            throw new \InvalidArgumentException("Empty needle is not allowed.");
        
        return (count($haystack) > 0) ? $haystack[0] == $needle : false;
    }
}