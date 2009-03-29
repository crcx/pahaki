<?php
/*---------------------------------------------------------
  Pahaki
  Geocoding and Location Awareness API
  -------------------------------------------------------*/

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
  return pahaki_lon($xml).','.pahaki_lon($xml);
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
