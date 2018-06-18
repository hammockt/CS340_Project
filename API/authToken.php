<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require_once 'phpUtilities.php';
require_once 'restUtilities.php';
require_once 'tokenUtilities.php';
require_once 'vendor/autoload.php';

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Builder;

//make sure they are posting this endpoint
$httpMethods = ["POST"];
enforceHttpMethods($httpMethods);

//get the JSON data from the post body
$data = getJsonFromHttpBody();

//make sure that they sent us a refreshToken
$requiredKeys = ['refreshToken'];
$optionalKeys = [];
enforceKeys($data, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($data, $requiredKeys);

$refreshToken = $data['refreshToken'];

//to token string into a token object
$token = (new Parser())->parse($refreshToken);

$config = loadConfig();

verifyRefreshToken($config, $token);

//now create a authToken
$authToken = (new Builder())->setIssuer($config['auth_iss'])
                            ->setAudience($config['auth_aud'])
                            ->setId(generateRandomSalt(16))
                            ->setIssuedAt(time())
                            ->setExpiration(time() + $config['auth_exp'])
                            ->setSubject('auth')
                            ->set('uid', $token->getClaim('uid'))
                            ->sign(new Sha256(), $config['auth_key'])
                            ->getToken();

$json = [ 'authToken' => "$authToken" ];
printf("%s", json_encode($json));

?>
