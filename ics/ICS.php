<?php
namespace jmartinez\yii\ics;

use DateTime;
use ReflectionClass;
use ReflectionProperty;
use jmartinez\yii\ics\ICS_Exception;
/**
 * Class to create an .ics file.
 */

class ICS {

  const DATETIME_FORMAT = 'Ymd\THis\Z';

  public $description;
  public $dtend;
  public $dtstart;
  public $location;
  public $summary;
  public $url;
  /**
   * 
   */
  private $availableProperties = [];
  /**
   * 
   */
  public function __construct($properties=[]) {
    $reflect = new ReflectionClass($this);
    foreach ($reflect->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) 
          $this->availableProperties[] = $prop->getName() ;

    $this->set($properties);


  }
  /**
   * 
   */
  public function set($key=[]) {
      foreach ($key as $k => $value) 
        $this->setPropertie($k, $value);
  }
  /**
  * 
  */
  private function setPropertie($key,$value)
  {
    if (in_array($key, $this->availableProperties)) 
        $this->{$key} = $this->sanitizeValue($value, $key);
  }
  /**
   *
   * @return string
   */
  public function toString() {
    $rows = $this->buildProperties();
    return implode("\r\n", $rows);
  }
  /**
   * @param string $filename
   * @return file
   */
  public function Download($filename="ical.ics")
  {
       if(empty($this->dtstart)) throw new ICS_Exception("Error Processing Request", 1);
      
       header('Content-type: text/calendar; charset=utf-8');
       header('Content-Disposition: attachment; filename='.$filename);
       echo $this->toString();
       exit;
  }
  /**
   * @return mixed 
   */
  private function buildProperties() {
    // Build ICS properties - header
    $icsProperties = [
      'BEGIN:VCALENDAR',
      'VERSION:2.0',
      'PRODID:-//hacksw/handcal//NONSGML v1.0//EN',
      'CALSCALE:GREGORIAN',
      'BEGIN:VEVENT'
    ];

    $properties = [];
    foreach($this->availableProperties as $k => $value) 
      $properties[strtoupper($value . ($value === 'url' ? ';VALUE=URI' : ''))] = $this->{$value};
        
    // Set default values
    $properties['DTSTAMP'] = $this->formatTimestampString('now');
    $properties['UID'] = uniqid();

    // Append properties
    foreach ($properties as $k => $value) 
      $icsProperties[] = "$k:$value";
    
    // Build ICS properties - footer
    $icsProperties[] = 'END:VEVENT';
    $icsProperties[] = 'END:VCALENDAR';

    return $icsProperties;
  }
  /**
   * 
   * @return string
   */
  private function sanitizeValue($value, $key = false) {
    
    if($key=='dtstart' || $key=='dtend') 
      $value = $this->formatTimestamp($value);

    if($key!='dtend' && $key!='dtstamp'&& $key!='dtstart')  
      $value = $this->escapeString($value);
      
    return $value;
  }
  /**
  * @return string
  */
  private function formatTimestampString($string){
      $dt = new DateTime($string);
      return $dt->format(self::DATETIME_FORMAT);

  }
  /**
   * 
   * @return string
   */
  private function formatTimestamp($timestamp) {
    /*echo "$timestamp";
    echo intval($timestamp);
    */
      $dt = new DateTime();
      $dt->setTimestamp(intval(substr($timestamp, 0, 10)));
      //return $dt->format(self::DATETIME_FORMAT);
      return $dt->format(self::DATETIME_FORMAT);
      
  }
  /**
   * 
   * @return string
   */
  private function escapeString($str) {
      return preg_replace('/([\,;])/','\\\$1', $str);
  }


}
