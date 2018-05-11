<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require_once 'restUtilities.php';
require_once 'postUtilities.php';
require_once 'vendor/autoload.php';

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Builder;

$ini = parse_ini_file('../config/rest.ini');

//make sure they are posting this endpoint
$httpMethods = ["POST"];
enforceHttpMethods($httpMethods);

//get the JSON data from the post body
$data = getJsonFromHttpBody();

//make sure that they sent us a refreshToken
$keys = ['refreshToken'];
enforceNonEmptyKeys($data, $keys);

$refreshToken = $data['refreshToken'];

//to token string into a token object
$token = (new Parser())->parse((string) $refreshToken);

//verify the signature
if(!$token->verify(new Sha256(), $ini['refresh_key']) || !$token->hasClaim('uid'))
{
	//bad tokens are forbidden
	http_response_code(403);
	exit();
}

//validate the token contraints. i.e token has not expired and the servers are correct
$data = new ValidationData(); //it will use the current time to validate (iat, nbf and exp)
$data->setIssuer($ini['refresh_iss']);
$data->setAudience($ini['refresh_aud']);
$data->setSubject('refresh');
if(!$token->validate($data))
{
	//expired or non-correct credentials are unauthorized
	http_response_code(401);
	exit();
}

//now create a authToken
$authToken = (new Builder())->setIssuer($ini['auth_iss'])
                            ->setAudience($ini['auth_aud'])
                            ->setId(generateRandomSalt(16))
                            ->setIssuedAt(time())
                            ->setExpiration(time() + $ini['auth_exp'])
                            ->setSubject('auth')
                            ->set('uid', $token->getClaim('uid'))
                            ->sign(new Sha256(), $ini['auth_key'])
                            ->getToken();

$json = [ 'authToken' => "$authToken" ];
printf("%s", json_encode($json));

?>
