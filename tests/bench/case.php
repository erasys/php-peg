<?php

require_once 'Benchmark/Timer.php';

function casecmp_internal($str1)
{
  $str2 = '7bit';
  if(0 === strcasecmp($str1, $str2)) return true;
  $str2 = '8bit';
  if(0 === strcasecmp($str1, $str2)) return true;
  $str2 = 'binary';
  if(0 === strcasecmp($str1, $str2)) return true;
  $str2 = 'base64';
  if(0 === strcasecmp($str1, $str2)) return true;
  $str2 = 'quoted-printable';
  if(0 === strcasecmp($str1, $str2)) return true;
  $str2 = 'gzip';
  if(0 === strcasecmp($str1, $str2)) return true;
  return false;
}
function casecmp_pcre($str1)
{
  return preg_match('/^(?:7bit|8bit|binary|base64|quoted-printable|gzip)$/i', $str1);
}

$timer = new Benchmark_Timer();
$nb_iterations = 100000;
$timer->start();

$str = "7bits";

echo "casecmp_internal\n";
for ($i = 0; $i < $nb_iterations; $i++) {
  $r = casecmp_internal($str);
}
var_dump($r);
$timer->setMarker('casecmp_internal');

echo "casecmp_pcre\n";
for ($i = 0; $i < $nb_iterations; $i++) {
   $r = casecmp_pcre($str);
}
var_dump($r);
$timer->setMarker('casecmp_pcre');

echo $timer->getOutput();

