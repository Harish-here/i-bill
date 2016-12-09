
<?php

/* The following code snippet with set the maximum execution time
 * of your script to 300 seconds (5 minutes)
 * Note: set_time_limit() does not work with safe_mode enabled
 */

$safeMode = ( @ini_get("safe_mode") == 'On' || @ini_get("safe_mode") === 1 ) ? TRUE : FALSE;
if ( $safeMode === FALSE ) {
  set_time_limit(300); // Sets maximum execution time to 5 minutes (300 seconds)
  // ini_set("max_execution_time", "300"); // this does the same as "set_time_limit(300)"
}

echo "max_execution_time " . ini_get('max_execution_time') . "<br>";

/* if you are using a loop to execute your mailing list (example: from a database),
 * put the command in the loop
 */

while (1==1) {
  set_time_limit(30); // sets (or resets) maximum  execution time to 30 seconds)
  // .... put code to process in here
  if (1!=1) {
    break;
  }
}

?>