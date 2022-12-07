*******************
Console_CommandLine
*******************
A full featured command line options and arguments parser.

``Console_CommandLine`` is a full featured package for managing command-line
options and arguments highly inspired from python ``optparse`` module, it allows
the developer to easily build complex command line interfaces.


=============
Main features
=============
* handles sub commands (ie. ``$ myscript.php -q subcommand -f file``),
* can be completely built from an XML definition file,
* generate ``--help`` and ``--version`` options automatically,
* can be completely customized,
* builtin support for i18n,
* and much more...


============
Installation
============

PEAR
====
::

    $ pear install Console_CommandLine


Composer
========
::

    $ composer require pear/console_commandline


=====
Links
=====
Homepage
  http://pear.php.net/package/Console_CommandLine
Bug tracker
  http://pear.php.net/bugs/search.php?cmd=display&package_name[]=Console_CommandLine
Documentation
  http://pear.php.net/manual/en/package.console.console-commandline.php
Unit test status
  https://travis-ci.org/pear/Console_CommandLine

  .. image:: https://travis-ci.org/pear/Console_CommandLine.svg?branch=stable
     :target: https://travis-ci.org/pear/Console_CommandLine
Packagist
  https://packagist.org/packages/pear/console_commandline
