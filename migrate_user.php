<?php
  //date_default_timezone_set('Asia/Jakarta');

  include dirname(__FILE__) . '/include_db_config.php';
  include dirname(__FILE__) . '/include_db_config_humhub.php';

  function getGUID() {
    if(function_exists('com_create_guid')){
      return com_create_guid();
    } else {
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = ""//chr(123)// "{"
            .substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
            //.chr(125);// "}"
        return strtolower($uuid);
    }
  }

  // loop into your user database
  // suppose you have table called users and it contains the plain password :)
  $sqlString = "SELECT * FROM users WHERE username";
  $resultUser = mysql_query($sqlString, $dbConn);

  while($row = mysql_fetch_array($resultUser)) {
    $username = $row['username'];
    $plainPassword = $row['plain_password'];
    $email = $row['email'];
    $registerDatetime = $row['register_datetime'];
    $guid = getGUID();
    
    // salt for password
    $aSaltConstant = "1008441905544962b1ea5467.17914535";

    // start to insert to humhub tables
    $sqlString = "INSERT INTO user (guid, wall_id, group_id, status, super_admin, username, email, auth_mode, created_at) VALUES ('" . $guid . "', NULL, 1, 1, 0, '" . $username . "', '" . $email . "', 'local', '" . $registerDatetime . "')";
    mysql_query($sqlString, $dbConnHumHub);

    $lastInsertedId = mysql_insert_id();
    
    $newPassword = hash('sha512', hash('whirlpool', $plainPassword . $aSaltConstant));

    $sqlString = "INSERT INTO user_password (user_id, algorithm, password, salt) VALUES (" . $lastInsertedId . ", 'sha512whirlpool', '" . $newPassword . "', '1008441905544962b1ea5467.17914535')";
    mysql_query($sqlString, $dbConnHumHub);

    $sqlString = "INSERT INTO profile (user_id, firstname) VALUES (" . $lastInsertedId . ", '" . $username . "')";
    mysql_query($sqlString, $dbConnHumHub);

    // we assume you already have space in your humhum and we set it to that space id
    $spaceIdOnHumHub = 1;
    $sqlString = "INSERT INTO space_membership (space_id, user_id, status, invite_role, admin_role, share_role) VALUES (" . $spaceIdOnHumHub . ", " . $lastInsertedId . ", 3, 0, 0, 0)";
    mysql_query($sqlString, $dbConnHumHub);
  }

  echo "User Migration Done!";
