###### PHEXT > [Core](../README.md) > [API Reference](index.md) > dates
------
### dates
A series of utilities for dealing with date formats and timestamp conversions.
#### Methods
- [days_between](#days_between)
- [flip_aus_us](#flip_aus_us)
- [is_date](#is_date)
- [is_valid](#is_valid)
- [diff2seconds](#diff2seconds)
- [diff](#diff)
- [strtotime](#strtotime)

------
##### days_between
```php
static public function days_between(string $date1 = null, string $date2 = null, bool $inclusive = false) : int
```
Calculate the amount of days that exist between two dates.

- **$date1** A date string capable of being converted to a time via `strtotime()`. Pass ``NULL`` or 'now' to have it set to the current date. Defaults to ``NULL``.
- **$date2** A date string capable of being converted to a time via `strtotime()`. Pass ``NULL`` or 'now' to have it set to the current date. Defaults to ``NULL``.
- **$inclusive** Whether or not the difference in days rounds up to the end of ending date. Defaults to ``FALSE``.

**Returns:**  An int value containing the total days between the two dates.

The order of `$date1` and `$date2` is not important. The difference returned will be an absolute value.


------
##### flip_aus_us
```php
static public function flip_aus_us(string $date) : string
```
A method for quickly swapping date strings in the format of dd/mm/yy or mm/dd/yy to the opposite.

This method does a simple unintelligent swap of the characters between the first 2 forward slashes. If there is a time attached (indicated by any trailing characters proceeded by a white space) then they will be preserved during the swap.

Will throw an exception if the provided string is not dd/mm/yy[yy] or mm/dd/yy[yy].


------
##### is_date
```php
static public function is_date(string $date) : bool
```
Test the provided string to see if it corresponds to a known date.

NOTE: _This method is not a perfect mechanism_. It can be useful when dealing with blocks of text that you are trying to extract data from, or when assessing dates where the format is not known ahead of time.

For cases where you are looking to validate a date string against an expected format you should instead use dates::is_valid()

**Returns:**  `TRUE` if a date was detected, `FALSE` otherwise.


------
##### is_valid
```php
static public function is_valid(string $date, string $format) : bool
```
Verify if the given text string is a valid date according to the provided date format.

- **$date** The date string to validate.
- **$format** The format the given date is to be validated against.

**Returns:**  `TRUE` if, _and only if_, the given date is in the correct format and passes with 0 warnings or errors, `FALSE` otherwise.


------
##### diff2seconds
```php
static public function diff2seconds(DateInterval $diff) : int
```
Produce the total number of seconds from the provided DateInterval object.

- **$diff** A DateInterval representing a duration or period of time.

**Returns:**  The total number of seconds the $diff spans over.


------
##### diff
```php
static public function diff(string $start, string $end) : int
```
Reliably calculate the total number of seconds between two dates regardless of the timezone currently in use. This method caters for certain situations where standard unix timestamps produce underdesired results.

Simply 'diff'ing the start from the end time will not help either. A base point in time is needed compare both in order to get the correct seconds.

- **$start** A date string representing the earliest point of the duration.
- **$end** A date string representing the latest point of the duration.

**Returns:**  The amount of whole seconds that exist between two points in time.


------
##### strtotime
```php
static public function strtotime(string $date = null) : int
```
Return the total number of seconds since Jan 1, 1970 and the given date, irrespective of timezone.

_This method should not be treated as a replacement for PHP's built-in `strtotime`._ It caters for certain situations where standard unix timestamps, and the native strtotime(), produce undesired results.

- **$date** The date to calculate the number of seconds in. The date may be any valid string that is accepted by the DateTime class.

**Returns:**  The amount of whole seconds that exist between two points in time.


------
