<?php
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
 * A series of utilities for dealing with date formats and timestamp conversions.
 */
class dates
{
    /**
     * A method for quickly swapping date strings in the format of dd/mm/yy** or mm/dd/yy** to the opposite.
     * 
     * This method does a simple unintelligent swap of the characters between the first 2 forward slashes. If there 
     * is a time attached (indicated by any trailing characters preceeded by a white space) then they will be 
     * preserved during the swap.
     * 
     * Will throw an exception if the provided string is not dd/mm/yy[yy] or mm/dd/yy[yy].
     */
	static public function flip_aus_us(string $date): string
	{
        $time = '';
        if (strings::contains($date, ' '))
            $time = strings::shiftex($date, ' ', $date);
        
		$parts = explode("/", $date);
        if (count($parts) < 3)
            throw new \InvalidArgumentException("The provided string does not appear to be a valid date. Please make sure the format is **/**/**[**]");
            
		$date = $parts[1]."/".$parts[0]."/".$parts[2];
        
        return ($time) ? "$date $time" : $date;
	}
	
    /**
     * Test the provided string to see if it corresponds to a known date.
     * 
     * *NOTE*: _This method is not a perfect mechanism_. It can be useful when dealing
     * with blocks of text that you are trying to extract data from, or when assessing
     * dates where the format is not known ahead of time.
     * 
     * For cases where you are looking to validate a date string against an expected
     * format you should instead use dates::is_valid()
     * 
     * @return TRUE if a date was detected, FALSE otherwise.
     */
	static public function is_date(string $date): bool
	{
		if (is_numeric($date))
			return false; // stop plain numbers from evaluating to true.
		
        $date = trim($date);
        $isDate = (bool)strtotime($date);
        if (! $isDate) {
            $date = str_replace('/', '-', str_replace('-', '', $date));
            $isDate = (bool)strtotime($date);
        }  
        return (bool)$isDate;
	}
    
    /**
     * Verify if the given text string is a valid date according to the provided
     * date format.
     * 
     * -- parameters:
     * @param $date The date string to validate.
     * @param $format The format the given date is to be validated against.
     * 
     * @return TRUE if, _and only if_, the given date is in the correct format and passes with 0 warnings or errors, FALSE otherwise.
     */
	static public function is_valid(string $date, string $format): bool
	{
        $pass = false;
        $r = \DateTime::createFromFormat($format, $date);
        if ($r instanceof \DateTime) {
            $errors = \DateTime::getLastErrors();
            $pass = ($errors['warning_count'] == 0 && $errors['error_count'] == 0);
        }
        return $pass;
	}
    
    /**
     * Produce the total number of seconds from the provided DateInterval object.
     * 
     * -- parameters:
     * @param $diff A DateInterval representing a duration or period of time.
     * 
     * @return The total number of seconds the $diff spans over.
     */
    static public function diff2seconds(\DateInterval $diff): int
    {
        return $diff->format('%r').( // prepend the sign - if negative, change it to R if you want the +, too
                ($diff->s)+ // seconds (no errors)
                (60*($diff->i))+ // minutes (no errors)
                (60*60*($diff->h))+ // hours (no errors)
                (24*60*60*($diff->d))+ // days (no errors)
                (30*24*60*60*($diff->m))+ // months (???)
                (365*24*60*60*($diff->y)) // years (???)
            );
    }
    
    /**
     * Reliably calculate the total number of seconds between two dates regardless
     * of the timezone currently in use. This method caters for certain situations where
     * standard unix timestamps produce underdesired results.
     * 
     * Simply 'diff'ing the start from the end time will not help either. A base point in time is needed compare
     * both in order to get the correct seconds.
     * 
     * -- parameters:
     * @param $start A date string representing the earliest point of the duration.
     * @param $end A date string representing the latest point of the duration.
     * 
     * @return The amount of whole seconds that exist between two points in time.
     */
    static public function diff(string $start, string $end): int
    {
        $point = new \DateTime('1970-01-01');
        $a = self::diff2seconds((new \DateTime($start))->diff($point, true));
        $b = self::diff2seconds((new \DateTime($end))->diff($point, true));
        return $b - $a;
    }
    
    /**
     * Return the total number of seconds since Jan 1, 1970 and the given date, *irrespective of timezone*.
     * 
     * _This method should not be treated as a replacement for PHP's built-in `strtotime`._ It caters for 
     * certain situations where standard unix timestamps, and the native strtotime(), produce undesired results.
     * 
     * -- parameters:
     * @param $date The date to calculate the number of seconds in. The date may be any valid string that is accecpted by the DateTime class.
     * 
     * @return The amount of whole seconds that exist between two points in time.
     */
    static public function strtotime(?string $date = null): int
    {
        if (! $date)
            $date = 'now';
        
        $point = new \DateTime('1970-01-01');
        return self::diff2seconds((new \DateTime($date))->diff($point, true));
    }
}