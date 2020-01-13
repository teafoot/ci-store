<?php

class Timedate extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }

  function get_nice_date($timestamp, $format) {
    switch ($format) {
      case 'full':
        // Friday 18th of February 2011 at 10:00:00 AM
        $the_date = date("l jS \of F Y \a\\t h:i:s A", $timestamp);
        break;
      case 'cool':
        // Friday 18th of February 2011
        $the_date = date("l jS \of F Y", $timestamp);
        break;
      case 'shorter':
        // 18th of February 2011
        $the_date = date("jS \of F Y", $timestamp);
        break;
      case 'mini':
        // 18th Feb 2011
        $the_date = date("jS M Y", $timestamp);
        break;
      case 'oldschool':
        // 18/2/11
        $the_date = date("j\/n\/y", $timestamp);
        break;
      case 'datepicker_uk':
        // 18/2/11
        $the_date = date("d\-m\-Y", $timestamp);
        break;
      case 'datepicker_us':
        // 2/18/11
        $the_date = date("m\/d\/Y", $timestamp);
        break;
      case 'monyear':
        // 18th Feb 2011
        $the_date = date("F Y", $timestamp);
        break;
    }

    return $the_date;
  }

  function make_timestamp_from_datepicker_uk($datepicker) {
    $hour = 7;
    $minute = 0;
    $second = 0;

    $day = substr($datepicker, 0, 2);
    $month = substr($datepicker, 3, 2);
    $year = substr($datepicker, 6, 4);

    $timestamp = mktime($hour, $minute, $second, $month, $day, $year);

    return $timestamp;
  }

  function make_timestamp_from_datepicker_us($datepicker) {
    $hour = 7;
    $minute = 0;
    $second = 0;

    $month = substr($datepicker, 0, 2);
    $day = substr($datepicker, 3, 2);
    $year = substr($datepicker, 6, 4);

    $timestamp = mktime($hour, $minute, $second, $month, $day, $year);

    return $timestamp;
  }

  function make_timestamp($day, $month, $year) {
    $hour = 7;
    $minute = 0;
    $second = 0;

    $timestamp = mktime($hour, $minute, $second, $month, $day, $year);

    return $timestamp;
  }

}
