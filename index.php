<?php

require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_CalendarService.php';
session_start();

if ((isset($_SESSION)) && (!empty($_SESSION))) {
   echo "There are cookies<br>";
   echo "<pre>";
   print_r($_SESSION);
   echo "</pre>";
}

$client = new Google_Client();
$client->setApplicationName("Google Calendar PHP Starter Application");
$client->setClientId('474901631751-de9ggb7gn78pt3ckgbo3kp0eck0crkgc.apps.googleusercontent.com');
$client->setClientSecret('oC0nklGO02iUdpSebWgBNltc');
$client->setRedirectUri('http://localhost/calender/index.php');
$client->setDeveloperKey('###');
$cal = new Google_CalendarService($client);

if (isset($_GET['logout'])) {
  echo "<br><br><font size=+2>Logging out</font>";
  unset($_SESSION['token']);
}

if (isset($_GET['code'])) {
  echo "<br>I got a code from Google = ".$_GET['code']; // You won't see this if redirected later
  $client->authenticate($_GET['code']);
  $_SESSION['token'] = $client->getAccessToken();
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
  echo "<br>I got the token = ".$_SESSION['token']; // <-- not needed to get here unless location uncommented
}

if (isset($_SESSION['token'])) {
  echo "<br>Getting access";
  $client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()){

  echo "<hr><font size=+1>I have access to your calendar</font>";
  $event = new Google_Event();
  $event->setSummary('Halloween');
  $event->setLocation('The Neighbourhood');
  $start = new Google_EventDateTime();
  $start->setDateTime('2018-8-21T10:00:00.000-05:00');
  $event->setStart($start);
  $end = new Google_EventDateTime();
  $end->setDateTime('2018-8-22T10:25:00.000-05:00');
  $event->setEnd($end);
  $createdEvent = $cal->events->insert('primary', $event);
  echo "<br><font size=+1>Event created</font>";

  echo "<hr><br><font size=+1>Already connected</font> (No need to login)";

} else {

  $authUrl = $client->createAuthUrl();
  print "<hr><br><font size=+2><a href='$authUrl'>Connect Me!</a></font>";

}

$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
echo "<br><br><font size=+2><a href=$url?logout>Logout</a></font>";

?>