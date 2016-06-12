#!/usr/bin/env php
<?php

date_default_timezone_set("UTC");

function GenArrisPasswords($startdate = null, $enddate = null) {
  $password_count = 0;
  $one_day_in_seconds = 24 * 60 * 60; // 1 day in seconds

  // Check how many passwords we're going to generate.
  if (isset($startdate) && isset($enddate)) {
    $password_count = ceil(($enddate - $startdate) / $one_day_in_seconds);
  } else {
    $password_count = 1;
    if (!isset($startdate)) {
      $startdate = time();
    }
  }

  // See if we have a valid number of passwords
  if (($password_count < 1) | ($password_count > 365)) {
    echo 'Since we can only generate passwords for a full year at a time, the number of passwords must be between 1 and 365.';
  } else {

    $seed = 'MPSJKMDHAI';
    $seedeight = substr($seed, 0, 8);
    $seedten = $seed;

    $table1 = array( 
      array(15, 15, 24, 20, 24),
      array(13, 14, 27, 32, 10),
      array(29, 14, 32, 29, 24),
      array(23, 32, 24, 29, 29),
      array(14, 29, 10, 21, 29),
      array(34, 27, 16, 23, 30),
      array(14, 22, 24, 17, 13)
    );

    $table2 = array(
      array(0, 1, 2, 9, 3, 4, 5, 6, 7, 8),
      array(1, 4, 3, 9, 0, 7, 8, 2, 5, 6),
      array(7, 2, 8, 9, 4, 1, 6, 0, 3, 5),
      array(6, 3, 5, 9, 1, 8, 2, 7, 4, 0),
      array(4, 7, 0, 9, 5, 2, 3, 1, 8, 6),
      array(5, 6, 1, 9, 8, 0, 4, 3, 2, 7)
    );

    $alphanum = array(
      '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D',
      'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
      'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
    );

    $list1 = array();
    $list2 = array();
    $list3 = array();
    $list4 = array();
    $list5 = array();
//    var year;
//    var month;
//    var day_of_month;
//    var day_of_week;
//    var iter, i;

    // Now let's generate one password for each day
    for($iter = 0; $iter < $password_count; $iter++) {
      // For each iteration advance the date one day
      $date = $startdate + ($iter * $one_day_in_seconds);

      // Last two digits of the year
      $year = date("y", $date);

      // Number of the month (no leading zero; January == 0)
      $month = date("n", $date);

      // Day of the month
      $day_of_month = date("d", $date);

      // Day of the week. Normally 0 would be Sunday but we need it to be Monday.
      $day_of_week = date("w", $date) - 1;
      if ($day_of_week < 0) {
      	$day_of_week = 6;
      }

      // Now build the lists that will be used by each other.
      
      // list1
      for ($i = 0; $i <= 4; $i++) {
        $list1[$i] = $table1[$day_of_week][$i];
      }
      $list1[5] = $day_of_month;
      if ((($year + $month) - $day_of_month) < 0) {
        $list1[6] = ((($year + $month) - $day_of_month) + 36) % 36;
      } else {
        $list1[6] = (($year + $month) - $day_of_month) % 36;
      }
      $list1[7] = (((3 + (($year + $month) % 12)) * $day_of_month) % 37) % 36;

      // list2
      for ($i = 0; $i <= 7; $i++) {
        $list2[$i] = (ord(substr($seedeight, $i, 1)) % 36);
      }

      // list3
      for ($i = 0; $i <= 7; $i++) {
        $list3[$i] = ((($list1[$i] + $list2[$i])) % 36);
      }
      $list3[8] = ($list3[0] + $list3[1] + $list3[2] + $list3[3] + $list3[4] +
          $list3[5] + $list3[6] + $list3[7]) % 36;
      $num8 = ($list3[8] % 6);
      $list3[9] = round(pow($num8, 2));

      // list4
      for ($i = 0; $i <= 9; $i++) {
        $list4[$i] = $list3[$table2[$num8][$i]];
      }

      // list5
      for ($i = 0; $i <= 9; $i++) {
        $list5[$i] = (ord(substr($seedten, $i, 1)) + $list4[$i]) % 36;
      }

      // Finally, build the password of the day.
      $password_of_the_day = array(); 
      $len = count($list5);
      for ($i = 0; $i < $len; $i++) {
        $password_of_the_day[$i] = $alphanum[$list5[$i]];
      }

      $password_of_the_day = implode('', $password_of_the_day);

      // TODO: Should this be presented in an overlay on the current page?
      echo $password_of_the_day . "\n";
    }
  }
}

GenArrisPasswords();

?>
