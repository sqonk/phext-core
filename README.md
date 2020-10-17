

# PHEXT Core functions and extensions library

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg)](https://php.net/)
[![License](https://sqonk.com/opensource/license.svg)](license.txt) [![Build Status](https://img.shields.io/travis/sqonk/phext-core/master.svg?style=flat-square)](https://travis-ci.org/sqonk/phext-core)

This is the core package to the PHEXT set of libraries for PHP. It includes general utility methods for strings, arrays, dates and numbers, each of which exist as a grouped class with the methods statically accessible.

It also contains a small set of stand-alone functions that import across the global namespace.

Most of the other PHEXT modules rely on the core library.

Some of the string methods require mbstring and iconv to be installed and active.



## About PHEXT

The PHEXT package is a set of libraries for PHP that aim to solve common problems with a syntax that helps to keep your code both concise and readable.

PHEXT aims to not only be useful on the web SAPI but to also provide a productivity boost to command line scripts, whether they be for automation, data analysis or general research.



## Install

Via Composer

``` bash
$ composer require sqonk/phext-core
```



Documentation
------------

[Full API Reference](docs/api/index.md) with examples now available here.




## Credits

Theo Howell



## License

The MIT License (MIT). Please see [License File](license.txt) for more information.