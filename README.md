# Between #

Between is simple mail exchange system.
Using a template, you can embed a URL that you can browse through them, or body-subject.
This system is developed in CakePHP.

## Installation ##

1. Extract all files, and place into a directory that is accessible to the web server, and able to run PHP.
2. Setup correct permissions on files and folders:
	* `chmod -R 777 app/tmp`
3. `cp app/Config/account.php.default app/Config/account.php` 
4. Edit `app/Config/account.php`
5. Edit crontab

	*       *       *       *       *       {APP Directory}/Console/cake -app {APP Directory} fetch
	# remove old mail cache
	5       *       *       *       *       find {APP Directory}/tmp/cache/mails -type f -mmin +4320|xargs rm -f

##License

The MIT License (MIT)

Copyright (c) 2013 Noriaki Ishihara

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

