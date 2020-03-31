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

/*
    Utility methods for dealing with numerical values.
*/
class numbers
{
	// Clip a numeric value, if necessary, to the given min and max boundaries.
	static public function constrain($value, $min, $max)
	{
	    if (is_numeric($max))
	        $value = min($value, $max);
	    if (is_numeric($min))
	         $value = max($value, $min);
	    return $value;
	}

	// Check if the given numeric value is in range.
	static public function is_within($value, $min, $max)
	{
	    if (is_numeric($max) and is_numeric($min))
	        return $value <= $max and $value >= $min;
	    else if (is_numeric($max))
	        return $value <= $max;
	    else if (is_numeric($min))
	        return $value >= $min;
    
	    return true;
	}
}