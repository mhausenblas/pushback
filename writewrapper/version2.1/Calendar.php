<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * PHP sample code for the Google Calendar data API.  Utilizes the 
 * Zend Framework Gdata components to communicate with the Google API.
 * 
 * Requires the Zend Framework Gdata components and PHP >= 5.1.4
 *
 * You can run this sample both from the command line (CLI) and also
 * from a web browser.  When running through a web browser, only
 * AuthSub and outputting a list of calendars is demonstrated.  When
 * running via CLI, all functionality except AuthSub is available and dependent
 * upon the command line options passed.  Run this script without any
 * command line options to see usage, eg:
 *     /usr/local/bin/php -f Calendar.php
 *
 * More information on the Command Line Interface is available at:
 *     http://www.php.net/features.commandline
 *
 * NOTE: You must ensure that the Zend Framework is in your PHP include
 * path.  You can do this via php.ini settings, or by modifying the 
 * argument to set_include_path in the code below.
 *
 * NOTE: As this is sample code, not all of the functions do full error
 * handling.  Please see getEvent for an example of how errors could
 * be handled and the online code samples for additional information.
 */


/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @see Zend_Gdata
 */
Zend_Loader::loadClass('Zend_Gdata');

/**
 * @see Zend_Gdata_AuthSub
 */
Zend_Loader::loadClass('Zend_Gdata_AuthSub');

/**
 * @see Zend_Gdata_ClientLogin
 */
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

/**
 * @see Zend_Gdata_HttpClient
 */
Zend_Loader::loadClass('Zend_Gdata_HttpClient');

/**
 * @see Zend_Gdata_Calendar
 */
Zend_Loader::loadClass('Zend_Gdata_Calendar');

/**
 * @var string Location of AuthSub key file.  include_path is used to find this
 */
$_authSubKeyFile = null; // Example value for secure use: 'mykey.pem'

/**
 * @var string Passphrase for AuthSub key file.
 */
$_authSubKeyFilePassphrase = null;

/**
 * Returns the full URL of the current page, based upon env variables
 * 
 * Env variables used:
 * $_SERVER['HTTPS'] = (on|off|)
 * $_SERVER['HTTP_HOST'] = value of the Host: header
 * $_SERVER['SERVER_PORT'] = port number (only used if not http/80,https/443)
 * $_SERVER['REQUEST_URI'] = the URI after the method of the HTTP request
 *
 * @return string Current URL
 */
function getCurrentUrl() 
{
  global $_SERVER;

  /**
   * Filter php_self to avoid a security vulnerability.
   */
  $php_request_uri = htmlentities(substr($_SERVER['REQUEST_URI'], 0, strcspn($_SERVER['REQUEST_URI'], "\n\r")), ENT_QUOTES);

  if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
    $protocol = 'https://';
  } else {
    $protocol = 'http://';
  }
  $host = $_SERVER['HTTP_HOST'];
  if ($_SERVER['SERVER_PORT'] != '' &&
     (($protocol == 'http://' && $_SERVER['SERVER_PORT'] != '80') ||
     ($protocol == 'https://' && $_SERVER['SERVER_PORT'] != '443'))) {
    $port = ':' . $_SERVER['SERVER_PORT'];
  } else {
    $port = '';
  }
  return $protocol . $host . $port . $php_request_uri;
}

/**
 * Returns the AuthSub URL which the user must visit to authenticate requests 
 * from this application.
 *
 * Uses getCurrentUrl() to get the next URL which the user will be redirected
 * to after successfully authenticating with the Google service.
 *
 * @return string AuthSub URL
 */
function getAuthSubUrl() 
{
  global $_authSubKeyFile;
  $next = getCurrentUrl();
  $scope = 'http://www.google.com/calendar/feeds/';
  $session = true;
  if ($_authSubKeyFile != null) {
    $secure = true;
  } else {
    $secure = false;
  }
  return Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure, 
      $session);
}

/**
 * Outputs a request to the user to login to their Google account, including
 * a link to the AuthSub URL.
 * 
 * Uses getAuthSubUrl() to get the URL which the user must visit to authenticate
 *
 * @return void
 */

function requestUserLogin($linkText) 
{
  $authSubUrl = getAuthSubUrl();
  echo "<a href=\""; 
  echo str_replace("amp%3B", "", $authSubUrl);
  echo "\">{$linkText}</a>"; 
 
}

/**
 * Returns a HTTP client object with the appropriate headers for communicating
 * with Google using AuthSub authentication.
 *
 * Uses the $_SESSION['sessionToken'] to store the AuthSub session token after
 * it is obtained.  The single use token supplied in the URL when redirected 
 * after the user succesfully authenticated to Google is retrieved from the 
 * $_GET['token'] variable.
 *
 * @return Zend_Http_Client
 */
function getAuthSubHttpClient() 
{
  global $_SESSION, $_GET, $_authSubKeyFile, $_authSubKeyFilePassphrase;
  $client = new Zend_Gdata_HttpClient();
  if ($_authSubKeyFile != null) {
    // set the AuthSub key
    $client->setAuthSubPrivateKeyFile($_authSubKeyFile, $_authSubKeyFilePassphrase, true);
  }
  if (!isset($_SESSION['sessionToken']) && isset($_GET['token'])) {
    $_SESSION['sessionToken'] = 
        Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token'], $client);
  } 
  $client->setAuthSubToken($_SESSION['sessionToken']);
  return $client;
}

/**
 * Processes loading of this sample code through a web browser.  Uses AuthSub
 * authentication and outputs a list of a user's calendars if succesfully 
 * authenticated.
 *
 * @return void
 */
 
function processPageLoad($id, $newSummary, $newStarttime, $newEndtime, $newLocation) 
{
  
  global $_SESSION, $_GET;
  
  if (file_exists("error_log.txt")) {
  	unlink("error_log.txt");
  }
  
  if (!isset($_SESSION['sessionToken']) && !isset($_GET['token'])) {
    requestUserLogin('Please login to your Google Account.');
  } else {
    $client = getAuthSubHttpClient();
	if (updateEventWithTitle($client, $id, $newSummary) != null) {
		$time = getFormattedTime($newStarttime);
		updateEventWithStartTime($client, $id, $time); //this is tricky: the newValue has to be in the form: yyyy-mm-ddThh:mm, like 2009-05-26T10:00
		$time = getFormattedTime($newEndtime);
		updateEventWithEndTime($client, $id, $time); //this is tricky: the newValue has to be in the form: yyyy-mm-ddThh:mm, like 2009-05-26T10:00
		updateEventWithLocation($client, $id, $newLocation); 
	}
	else {
		return null;
	}
  }
}

/*inserts a symbol in a string
for example:
insertSymbol("helloworld", 5, "-") return "hello-world"
*/
function insertSymbol($string, $position, $symbol) {
    $length=strlen($string);
    $temp1=substr($string,0,$position); //yyyy
	$temp2=substr($string,$position,$length);
    $rest=$temp1.$symbol.$temp2; 
	return $rest;
}

//for a time in format yyymmddThhmmssZ it returns yyyy-mm-ddThh:mm => what Google API understands
function getFormattedTime($time){
	return substr($time, 0, -4);
	//$rest = substr($time, 0, -3); //yyymmddThhmm
    //return insertSymbol(insertSymbol(insertSymbol($rest,4,"-"), 7, "-"), 13, ":");
}

/**
 * Returns an entry object representing the event with the specified ID.
 *
 * @param  Zend_Http_Client $client  The authenticated client object
 * @param  string           $eventId The event ID string
 * @return Zend_Gdata_Calendar_EventEntry|null if the event is found, null if it's not
 */
function getEvent($client, $eventId) 
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  $query = $gdataCal->newEventQuery();
  $query->setUser('default');
  $query->setVisibility('private');
  $query->setProjection('full');
  $query->setEvent($eventId);

  try {
    $eventEntry = $gdataCal->getCalendarEventEntry($query);
    return $eventEntry;
  } catch (Zend_Gdata_App_Exception $e) {
    echo "<h2>Might be that the ID of the event: " . $eventId . " is not correct, </h2><br><h3>you can check the <a href='error_log.txt'>error_log.txt</a> or contact oana.ureche@deri.org, attach the error_log.txt file </h3>"; 
	error_log($e, 3, "error_log.txt");
    return null;
  }
}

/**
 * Updates the title of the event with the specified ID to be
 * the title specified.  Also outputs the new and old title
 * with HTML br elements separating the lines
 *
 * @param  Zend_Http_Client $client   The authenticated client object
 * @param  string           $eventId  The event ID string
 * @param  string           $newTitle The new title to set on this event 
 * @return Zend_Gdata_Calendar_EventEntry|null The updated entry
 */
function updateEventWithTitle ($client, $eventId, $newTitle) 
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  if ($eventOld = getEvent($client, $eventId)) {
    echo "Old title: " . $eventOld->title->text . "<br />\n";
    $eventOld->title = $gdataCal->newTitle($newTitle);
    try {
        $eventOld->save();
    } catch (Zend_Gdata_App_Exception $e) {
        echo "<h2>Could not update event title</h2><br><h3>you can check the <a href='error_log.txt'>error_log.txt</a> or contact oana.ureche@deri.org, attach the error_log.txt file </h3>"; 
		error_log($e, 3, "error_log.txt");
        return null;
    }
    $eventNew = getEvent($client, $eventId);
    echo "New title: " . $eventNew->title->text . "<br />\n";
    return $eventNew;
  } else {
    return null;
  }
}

function updateEventWithStartTime ($client, $eventId, $startTime)
{
  $tzOffset = "+01";
  $gdataCal = new Zend_Gdata_Calendar($client);
  if ($eventOld = getEvent($client, $eventId)) {
  	foreach ($eventOld->when as $when) {
	  echo "Old start time: " . $when->startTime . "<br />\n";
	  $when->startTime = "{$startTime}:00.000{$tzOffset}:00"; //start time changes
      $eventOld->when = array($when);
	  
	  try {
        $eventOld->save();
      } catch (Zend_Gdata_App_Exception $e) {
        echo "<h2>Could not update start time</h2><br><h3>you can check the <a href='error_log.txt'>error_log.txt</a> or contact oana.ureche@deri.org, attach the error_log.txt file </h3>"; 
		error_log($e, 3, "error_log.txt");
        return null;
      }
	  $eventNew = getEvent($client, $eventId);
      foreach ($eventNew->when as $when) {
	  	echo "New start time: " . $when->startTime . "<br />\n";
	  }
    }
  } 
}
  
function updateEventWithEndTime ($client, $eventId, $endTime)
{
  $tzOffset = "+01";
  $gdataCal = new Zend_Gdata_Calendar($client);
  if ($eventOld = getEvent($client, $eventId)) {
  	foreach ($eventOld->when as $when) {
	  echo "Old end time: " . $when->endTime . "<br />\n";
	 
	  $when->endTime = "{$endTime}:00.000{$tzOffset}:00"; //start time changes
      $eventOld->when = array($when);
	  
	  try {
        $eventOld->save();
      } catch (Zend_Gdata_App_Exception $e) {
        echo "<h2>Could not update event end time</h2><br><h3>you can check the <a href='error_log.txt'>error_log.txt</a> or contact oana.ureche@deri.org, attach the error_log.txt file </h3>"; 
        error_log($e, 3, "error_log.txt");
        return null;
      }
	  $eventNew = getEvent($client, $eventId);
      foreach ($eventNew->when as $when) {
	  	echo "New end time: " . $when->endTime . "<br />\n";
	  }
    }
  } 
}

function updateEventWithLocation ($client, $eventId, $location)
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  if ($eventOld = getEvent($client, $eventId)) {
  	  echo "Old location: " . $eventOld->where[0] . "<br />\n";
	  $eventOld->where  = array($gdataCal->newWhere($location));
	  try {
        $eventOld->save();
      } catch (Zend_Gdata_App_Exception $e) {
        echo "<h2>Could not update location</h2><br><h3>you can check the <a href='error_log.txt'>error_log.txt</a> or contact oana.ureche@deri.org, attach the error_log.txt file </h3>"; 
        error_log($e, 3, "error_log.txt");
        return null;
      }
	  $eventNew = getEvent($client, $eventId);
      echo "New location: " . $eventNew->where[0] . "<br />\n";
   }
     
}