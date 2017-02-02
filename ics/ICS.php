<?php
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
  private $availableProperties = [
    'description',
    'dtend',
    'dtstart',
    'location',
    'summary',
    'url'
  ];
  /**
   * 
   */
  public function __construct($properties) {
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
      $properties[strtoupper($k . ($k === 'url' ? ';VALUE=URI' : ''))] = $this->{$k};
        
    // Set default values
    $properties['DTSTAMP'] = $this->formatTimestamp('now');
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
    
    if($key=='dtstart') 
      $value = $this->formatTimestamp($value);

    if($key!='dtend' && $key!='dtstamp'&& $key!='dtstart')  
      $value = $this->escapeString($value);
      
    return $value;
  }
  /**
   * 
   * @return string
   */
  private function formatTimestamp($timestamp) {
      $dt = new DateTime($timestamp);
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
