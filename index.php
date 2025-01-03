<?
require 'vendor/autoload.php';

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\CognitoIdentity\CognitoIdentityClient;

/*
===
!! DO NOT STORE USERNAME, PASSWORD OR ANY OTHER LOGIN CREDENTIALS INSIDE YOUR CODE !!
===
This is just an example to demonstrate the usage of sdk methods in php.  
Use AWS Secrets Manager or follow our security best practises:
https://aws.amazon.com/secrets-manager/
https://aws.amazon.com/architecture/security-identity-compliance/
*/

// aws region to be used
$region = 'eu-central-1';
// cognito user login (username, id or email)
$userName = 'testuser';
// cognito password
$userPassword = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXX';
// user pool id
$userPoolId = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXX';
// user pool app client id 
$appClientId = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXX';
// cognito identity pool id 
$identityPoolId = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXX';
// cognito provider name == issuer URL cognito-idp.[region].amazonaws.com/[userPoolId]
$providerName = 'cognito-idp.' . $region .'.amazonaws.com/' . $userPoolId;

// create a new cognito client without credentials
$cognitoClient = new CognitoIdentityProviderClient([
        'version' => 'latest',
        'region'  => $region,
        'credentials' => false
]);

// start auth with provided user from user pool
$result = $cognitoClient->initiateAuth([
    'AuthFlow' => 'USER_PASSWORD_AUTH',
    'AuthParameters' => [
        'USERNAME' => $userName,
        'PASSWORD' => $userPassword,
    ],
    'ClientId' => $appClientId,
]);

// we have now a cognito id token to use for further API calls 
$apiToken = $result['AuthenticationResult']['IdToken'];
echo("<h2>API token:</h2>");
var_dump($apiToken);
echo("<hr>");

// create a cognito identity client, without credentials
$cognitoIdentitClient = new CognitoIdentityClient([
    'region'  => $region,
    'credentials' => false
]);

// call get id and pass the valid cognito id token as login for the provider name (issuer URL)
$idResp = $cognitoIdentitClient->getId(array(
    'IdentityPoolId' => $identityPoolId,
    'Logins' => array($providerName => (string)$apiToken),
    'credentials' => false
  )
);

// we have now a identity ID to get the credentials
$identityId = $idResp["IdentityId"];
echo "<h2>Identity ID:</h2>";
var_dump($identityId);
echo("<hr>");

// get temporary credentials 
$getCredsResult = $cognitoIdentitClient->getCredentialsForIdentity(array(
    'IdentityId' => $identityId,
    'Logins' => array($providerName => (string)$apiToken),
));

// save credentials
$credentials = $getCredsResult['Credentials'];

// create a s3 client using the temporary credentials
$s3client = new S3Client([
    'region' => $region,
    'credentials'  => new Credentials(
                $credentials['AccessKeyId'],
                $credentials['SecretKey'],
                $credentials['SessionToken']
            )
]);

// list and print bucket list 
$buckets = $s3client->listBuckets();
echo("<h2>Buckets:</h2>");
for ($i = 0; $i < count($buckets['Buckets']); $i++) {
    echo($buckets['Buckets'][$i]['Name']);
    echo("<br>");
}

?>