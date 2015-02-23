<?php

define('SOURCEDIR', "/");
define('BACKUPDIR', "/Volumes/Macintosh HD2");

date_default_timezone_set("Europe/Berlin");
$time = time();
$days = 24 * 60 * 60;


// this tool creates images of the boot partition as a (weak) backup to another disk.
// it rotates those images by keeping the last three days, then the last week, and the last month.
// that means:
// create a new backup
// keep every backup that's younger than 3 days
// delete backups that are older than 3 days, but keep the one from last saturday and the first saturday of the current month (unless they're the same, then keep the one from the first saturday of the last 2 months)

echo "making a backup to ".filepath($time)."\n";
echo "command: > hdiutil create ".filepath($time)." -format UDZO -nocrossdev -srcdir ".SOURCEDIR."\n";
exec("hdiutil create ".filepath($time)." -format UDZO -nocrossdev -srcdir ".SOURCEDIR);


// if backup 3 days ago was _not_ a saturday:
if(date("N", $time - $days * 3) != 6) { // "N" is the day of the week
	// check if a backup exists there
	if(file_exists(filepath($time - $days * 3))) {
		// if so, delete it
		unlink(filepath($time - $days * 3));
		echo "deleted backup that was older than 3 days but not from a saturday\n";
	}
}


// if backup 7 days ago was _not_ the first saturday in a month
// (must be saturday, because there are no other backups older than 3 days)
if(date("j", $time - $days * 7) > 7) { // "j" is the day of the month, 
	// echo date("j", $time - $days * 7)."\n";
	if(file_exists(filepath($time - $days * 7))) {
		// if so, delete it
		unlink(filepath($time - $days * 7));
		echo "deleted backup that was a week old but not from the first saturday of a month\n";
	}
}

// delete backups that are older than 2 months
if(file_exists(filepath($time - $days * 28 * 3))) {
	unlink(filepath($time - $days * 28 * 3));
	echo "deleted backup that was older than 3 months\n";
}




function filepath($time) {
	return BACKUPDIR."/".date("o-W-N", $time).".dmg";
}


?>