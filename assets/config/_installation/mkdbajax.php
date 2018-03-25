<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//datadir
$fp = fopen('../datadir.json', 'w');
fwrite($fp, json_encode($_POST));
fclose($fp);

$filename = file_get_contents('../datadir.json');

$datadir = json_decode( $filename, true);

// Desired folder structure
$structure = $datadir['datadir'];

// Create users.db: //

// config
$db_type = "sqlite";
$db_sqlite_path = $structure . 'users.db';

if (file_exists($db_sqlite_path)) { //check if file exists
    rename("users.db", "users.db.old"); //delete file if does exist
} else {
    // create new database file / connection (the file will be automatically created the first time a connection is made up)
    $db_connection = new PDO($db_type . ':' . $db_sqlite_path);

    // create new empty table inside the database (if table does not already exist)
    $sql = 'CREATE TABLE IF NOT EXISTS `users` (
        `user_id` INTEGER PRIMARY KEY,
        `user_name` varchar(64),
        `user_password_hash` varchar(255),
        `user_email` varchar(64));
        CREATE UNIQUE INDEX `user_name_UNIQUE` ON `users` (`user_name` ASC);
        CREATE UNIQUE INDEX `user_email_UNIQUE` ON `users` (`user_email` ASC);
    ';

// execute the above query

$query = $db_connection->prepare($sql);
$query->execute();

echo "<br>";

if (!$query) {

echo '<div class="reglog">';
echo '<div id="loginerror">';
  echo "failed to create user database";
echo '</div">';
echo '</div>';

$fp = fopen('../datadir.json', 'w');
fwrite( $fp, "Failed to create sqlite database in: $structure");
fclose($fp);

rename("../datadir.json", "../datadir.fail.txt");

}

else {

echo '<div class="reglog">';

echo '<div id="dbmessagesuccess">';
  echo "User database creation complete: ";
  echo realpath($db_sqlite_path);
echo '</div>';
  echo "<br>";

echo '<div id="dbmessagesuccess">';
  echo "All required data files succesfully copied to user data dir:";
      echo "<br>";
  echo realpath($structure);
echo '</div>';
  echo "<br>";

echo '<div id="loginmessage">';
  echo "Monitorr data directory creation complete. You can now create a user.";
echo '</div>';

echo '</div>';

unlink('../datadir.fail.txt');


// Temporary OLD config file removal // CHANGE ME //

$fileold1 = '../../config.php.sample';

if(is_file($fileold1)){

rename('../../config.php.sample', '../../config.php.sample.old');
}

$fileold2 = '../../config.php';

if(is_file($fileold2)){

rename('../../config.php', '../../config.php.old');
}

}
}
}

echo "<br>";

// ...

exit;

?>
