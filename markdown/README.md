Notice
======

The library is under active development. API may change without notice.

What is Markdown?
=================

Markdown is a text-to-HTML conversion tool for web writers.
It is intended to be as easy-to-read and easy-to-write as is feasible.

Readability, however, is emphasized above all else.
A Markdown-formatted document should be publishable as-is, as plain text,
without looking like it’s been marked up with tags or formatting instructions.

See [official website](http://daringfireball.net/projects/markdown/syntax) for syntax.


What is markdown-oo-php?
========================

It's an object-oriented, PSR compatible PHP library capable of converting markdown text to XHTML.


Quick start
=========

```php
set_include_path(get_include_path() . PATH_SEPARATOR . realpath('src'));

require_once 'SplClassLoader.php';
$l = new SplClassLoader('MaxTsepkov');
$l->register();

use MaxTsepkov\Markdown\Text;

echo new Text($markdown);
```

Requirements
===========

  *  PHP  >= 5.3

Contribution
==========

  1.  [Fork me](https://github.com/maxtsepkov/markdown-oo-php)
  2.  [Mail me](mailto:max@yogi.pw)
