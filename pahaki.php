<?php
/*---------------------------------------------------------
  Pahaki
  Geocoding and Location Awareness API
  -------------------------------------------------------*/

class pahaki
{
  public $lat;         /* Latitude */
  public $lon;         /* Longitude */
  public $coords;      /* String. Latitude,Longitude */
  public $city;        /* City Name */
  public $county;      /* County Name */
  public $state;       /* Two Letter State Name */
  public $zip;         /* Zip Code */
  public $street1;     /* Street #1 (Intersection) */
  public $street2;     /* Street #2 (Intersection) */

  public function __construct($uuid)
  {
    /* Query Xtify for basic position */
    $cpid = "";
    $xml = download('http://query.xtify.com/api/1.0/xml/location?userkey='.$uuid.'&cpid='.$cpid);
    $x = new SimpleXMLElement(utf8_decode($xml));

    $this->lat = $x->locationset->location->coords->lat;
    $this->lon = $x->locationset->location->coords->lon;
    $this->coords = $lat.','.$lon;

    /* Reverse Geocoding from geonames.org */
    $geo = download('http://ws.geonames.org/findNearestIntersection?lat='.$lat.'&lng='.$lon);
    $x   = new SimpleXMLElement(utf8_decode($geo));
    $this->state = $x->intersection->adminCode1;
    $this->county = $x->intersection->adminName2;
  }

  private function download($url)
  {
    $crl = curl_init();
    $timeout = 8;
    curl_setopt ($crl, CURLOPT_URL,$url);
    curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
    $ret = curl_exec($crl);
    curl_close($crl);
    return $ret;
  }
}


/* Your Xtify CPID */
$cpid = "";


function pahaki_get($url)
{
  $crl = curl_init();
  $timeout = 8;
  curl_setopt ($crl, CURLOPT_URL,$url);
  curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
  $ret = curl_exec($crl);
  curl_close($crl);
  return $ret;
}



function pahaki_locate($uuid)
{
  $xml = pahaki_get('http://query.xtify.com/api/1.0/xml/location?userkey='.$uuid.'&cpid='.$cpid);
  return $xml;
}


function pahaki_lat($xml)
{
  $x = new SimpleXMLElement(utf8_decode($xml));
  return $x->locationset->location->coords->lat;
}


function pahaki_lon($xml)
{
  $x = new SimpleXMLElement(utf8_decode($xml));
  return $x->locationset->location->coords->lon;
}


function pahaki_timestamp($xml)
{
  $x = new SimpleXMLElement(utf8_decode($xml));
  return $x->locationset->location->timestamp;
}


function pahaki_getCoords($xml)
{
  return pahaki_lat($xml).','.pahaki_lon($xml);
}


function pahaki_getState($xml)
{
  $lat = pahaki_lat($xml);
  $lon = pahaki_lon($xml);
  $geo = pahaki_get('http://ws.geonames.org/findNearestIntersection?lat='.$lat.'&lng='.$lon);
  $x   = new SimpleXMLElement(utf8_decode($geo));
  return $x->intersection->adminCode1;
}


function pahaki_getCounty($xml)
{
  $lat = pahaki_lat($xml);
  $lon = pahaki_lon($xml);
  $geo = pahaki_get('http://ws.geonames.org/findNearestIntersection?lat='.$lat.'&lng='.$lon);
  $x   = new SimpleXMLElement(utf8_decode($geo));
  return $x->intersection->adminName2;
}

?>
