timelogger
==========

PHP-CLI time logger with prompt guiding the command line.

@author
- Manuel Will <insphare@gmail.com>


@copyright
- Copyright (c) 2014, Manuel Will


@usage
- sh start.sh
OR
- php logger.php


@hint
- create a symlink to shell file (e.g. ln -s /path/to/timelogger/logging.sh /path/to/desktop/or/home/dir/logging.sh)


@requires
- minimum php 5.3 and above
- php-cli
- pcntl (maybe default installed http://www.php.net/manual/de/book.pcntl.php) for signal handling
- entitled to set/change include path
- entitled to create maybe missed directories on bootstrap


@suggestions:
- bootstrap check fur extension pcntl.
- Resume (start new work on last work name)
- Edit (Edit a task of the day by argument: 0->file|timestamp_begin)	1->intSecond (+|-))
