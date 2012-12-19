## Running tests ##

### Unit & integration tests ###

Install PHPUnit:

	pear config-set auto_discover 1
	pear install pear.phpunit.de/PHPUnit
	pear install phpunit/PHPUnit_Selenium

Run unit & integration tests:

	phpunit ./tests/case/unit/

### Selenium tests ###

Download Selenium server (JAR):

	http://selenium.googlecode.com/files/selenium-server-standalone-2.20.0.jar

Run Selenium server:

	java -jar selenium-server-standalone.jar

Run selenium tests:

	cd tests
	phpunit ./tests/case/selenium/