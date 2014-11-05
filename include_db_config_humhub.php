<?php
  // this is the setting for database of that contains HumHub user tables
  $dbHost = "database_host";
  $dbUsername = "database_username";
  $dbPassword = "database_password";
  $dbName = "database_name";
  
  $dbConnHumHub = mysql_connect($dbHost, $dbUsername, $dbPassword) or die("Database now under site maintenance! Please be patient and we will back soon!");
  mysql_select_db($dbName, $dbConnHumHub) or die("Database name is not available !!");
  mysql_set_charset('utf8', $dbConnHumHub);
  mysql_query("SET NAMES 'utf8'", $dbConnHumHub);
