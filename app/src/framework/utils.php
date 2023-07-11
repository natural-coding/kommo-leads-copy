<?php
namespace Framework\Utils;

use League\OAuth2\Client\Token\AccessToken;

function getAccessToken() : AccessToken
{
   $returnAssociativeArray = true;
   $rawToken = json_decode(file_get_contents($_ENV['APP_TOKEN_FILE']), $returnAssociativeArray);
   return (new AccessToken($rawToken));
}

function dump_nice_header(string $p_headerString)
{
   printf('<p style="margin-top: 2rem">%s</p>', $p_headerString);
}

function dump_nice($p_variable, bool $p_isExit = true)
{
   print '<pre>';
   if (gettype($p_variable)==='array')
      print_r($p_variable);
   else
      var_dump($p_variable);
   print '</pre>';

   if ($p_isExit)
      die;
}

/**
 * l means "to live" :-)
 */
function dump_nice_l($p_variable)
{
   dump_nice($p_variable,false);
}