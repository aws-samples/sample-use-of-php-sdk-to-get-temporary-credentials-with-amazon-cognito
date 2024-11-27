# Sample use of PHP SDK to get temporary credentials with Amazon Cognito 

This code example shows how to retrieve temporary credentials from the Cognito service API, using the PHP SDK.

## Requirements

### PHP SDK and composer

Composer is a PHP package manager which handles project dependencies. A `composer.json` file declaring the dependency on the AWS SDK is provided. To install composer, follow the official composer install instructions [here](https://getcomposer.org/).

After composer is sucessfully installed, run `composer install` to install the AWS PHP SDK.

### AWS Cognito 

1. For this demo to work, we nned to setup a [Amazon Cognito user pool](https://docs.aws.amazon.com/cognito/latest/developerguide/cognito-user-pools.html). The app client that is beeing created needs to have the `ALLOW_USER_PASSWORD_AUTH` authentication flow active.

2. To get AWS credentials we also need a [Amazon Cognito Identity Pool](https://docs.aws.amazon.com/cognito/latest/developerguide/cognito-identity.html). The identity pool needs to have `Cognito user pool` as identity source, and the user pool and app client configured from step 1. To run this demo, the users IAM role needs to have access to S3. 

You need to chagne the first section of index.php and configure the variables with the real values:

```
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
```

> **DO NOT STORE USERNAME, PASSWORD OR ANY OTHER LOGIN CREDENTIALS INSIDE YOUR CODE** 

This is just an example to demonstrate the usage of sdk methods in php. Follow our security best practises: 
https://aws.amazon.com/architecture/security-identity-compliance/ 
https://aws.amazon.com/secrets-manager/ 


## Running the example

Go to your webroot and call the index.php. The sample code will authenticate via the cognito api and get a api token. With that token it will then recieve temporary credentials from the identity pool. 

To verify the connection a S3 client is beeing created with the credentials and a testcall is made to the S3 api via the PHP SDK. 

## License Summary

This sample code is made available under the [MIT-0 license](https://github.com/aws/mit-0). See the LICENSE file.
