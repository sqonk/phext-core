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
 * Utility methods for dealing with numerical values.
 */
class numbers
{
	/**
	 * Clip a numeric value, if necessary, to the given min and max boundaries.
	 * 
	 * Example:
	 * 
	 * ``` php
	 * $value = 4.9;
     * println("value:", numbers::constrain($value, 5.0, 5.5));
     * // will print out '5'.
	 * ```
	 */
	static public function constrain($value, $min, $max)
	{
	    if (is_numeric($max))
	        $value = min($value, $max);
	    if (is_numeric($min))
	         $value = max($value, $min);
	    return $value;
	}

	/**
	 * Check if the given numeric value is in range.
	 * 
	 * Example:
	 * 
	 * ``` php
	 * $value = 20;
     * if (numbers::is_within($value, 10, 30))
     * println('The number is within range');
     * // will print out 'The number is within range'.
	 * ```
	 */
	static public function is_within($value, $min, $max): bool
	{
	    if (is_numeric($max) and is_numeric($min))
	        return $value <= $max and $value >= $min;
	    else if (is_numeric($max))
	        return $value <= $max;
	    else if (is_numeric($min))
	        return $value >= $min;
    
	    return true;
	}
    
    /**
     * Random Float Generator.
     * 
     * Generate a random number between $start and $end to a series of decimal places.
     * 
     * -- parameters:
     * @param $start Optional lowest value to be returned (default: 0) 
     * @param $end Optional highest value to be returned (default: 1.0)
     * @param $mul Optional multiplier that will determine the number of decimal places (default: 1000000)
     * 
     * @return A random float between `$min` and `$max`.
     */
    static public function rand_float(float $min = 0.0, float $max = 1.0, int $mul = 1000000): float
    {
        if ($min > $max) 
            throw new \InvalidArgumentException("min can not be greater than max.");
        
        return (float)(mt_rand($min * $mul, $max * $mul) / $mul);
    }
}