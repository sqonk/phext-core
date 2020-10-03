###### PHEXT > [Core](../README.md) > [API Reference](index.md) > numbers
------
### numbers
Utility methods for dealing with numerical values.
#### Methods
[constrain](#constrain)
[is_within](#is_within)

------
##### constrain
```php
static public function constrain($value, $min, $max) 
```
Clip a numeric value, if necessary, to the given min and max boundaries.

Example:

``` php
$value = 4.9;
println("value:", numbers::constrain($value, 5.0, 5.5));
// will print out '5'.
```


------
##### is_within
```php
static public function is_within($value, $min, $max) 
```
Check if the given numeric value is in range.

Example:

``` php
$value = 20;
if (numbers::is_within($value, 10, 30))
println('The number is within range');
// will print out 'The number is within range'.
```


------
