<?php

namespace App\Http\Controllers;
require '../vendor/autoload.php';
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use com\zoho\api\authenticator\Token;
use com\zoho\crm\api\exception\SDKException;
use com\zoho\api\authenticator\OAuthBuilder;
use com\zoho\api\authenticator\store\DBBuilder;
use com\zoho\api\authenticator\store\FileStore;
use com\zoho\crm\api\InitializeBuilder;
use com\zoho\crm\api\UserSignature;
use com\zoho\crm\api\dc\EUDataCenter;
use com\zoho\api\logger\LogBuilder;
use com\zoho\api\logger\Levels;
use com\zoho\crm\api\SDKConfigBuilder;
use com\zoho\crm\api\ProxyBuilder;

use com\zoho\crm\api\query\ResponseWrapper;
use com\zoho\crm\api\query\APIException;
use com\zoho\crm\api\query\BodyWrapper;
use com\zoho\crm\api\query\QueryOperations;



class ZohoOauthController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(string $grant_token)
    {
        Log::info('request received');
        Log::info('Token is'. $grant_token);
       
        $logger = (new LogBuilder())
    ->level(Levels::INFO)
    ->filePath("/Users/saeedbutt/Documents/php/zohoIntegration/storage/logs/php_sdk_log.log")
    ->build();
    $user = new UserSignature("saeedbutt320@gmail.com");
    $environment = EUDataCenter::PRODUCTION();
    $token = (new OAuthBuilder())
    ->clientId("1000.7HUGW9O3WU65V5O18Z75TVTLL9QWXS")
    ->clientSecret("542dcac0ea8813a1368662173453d4d5f8ff651bae")
    ->grantToken($grant_token)
    ->redirectURL("www.facebook.com")
    ->build();
    $resourcePath = "/Users/saeedbutt/Documents/php/zohoIntegration";
    $autoRefreshFields = false;
    $pickListValidation = false;
    $connectionTimeout = 2;
    $timeout = 2;
    $enableSSLVerification = true;

    $tokenstore = new FileStore("/Users/saeedbutt/Documents/php/zohoIntegration/storage/sdk_tokens.txt");
    $sdkConfig = (new SDKConfigBuilder())
    ->autoRefreshFields($autoRefreshFields)
    ->pickListValidation($pickListValidation)
    ->sslVerification($enableSSLVerification)
    ->connectionTimeout($connectionTimeout)
    ->timeout($timeout)
    ->build();

    
    (new InitializeBuilder())
    ->user($user)
    ->environment($environment)
    ->token($token)
    ->store($tokenstore)
    ->SDKConfig($sdkConfig)
    ->resourcePath($resourcePath)
    ->logger($logger)
    ->initialize();




    $queryOperations = new QueryOperations();

		//Get instance of BodyWrapper Class that will contain the request body
		$bodyWrapper = new BodyWrapper();

		$selectQuery = "select Last_Name from Leads where Last_Name is not null limit 200";

		$bodyWrapper->setSelectQuery($selectQuery);

		//Call getRecords method that takes BodyWrapper instance as parameter
		$response = $queryOperations->getRecords($bodyWrapper);

		if($response != null)
		{
			//Get the status code from response
			echo("Status Code: " . $response->getStatusCode() . "\n");

			//Check if expected response is received
			if($response->isExpected())
			{
				//Get the object from response
				$responseHandler = $response->getObject();

				if($responseHandler instanceof ResponseWrapper)
				{
					//Get the received ResponseWrapper instance
					$responseWrapper = $responseHandler;

					//Get the obtained Record instances
					$records = $responseWrapper->getData();

					foreach($records as $record)
					{
						//Get the ID of each Record
						echo("Record ID: " . $record->getId() . "\n");

						//Get the createdBy User instance of each Record
						$createdBy = $record->getCreatedBy();

						//Check if createdBy is not null
						if($createdBy != null)
						{
							//Get the ID of the createdBy User
							echo("Record Created By User-ID: " . $createdBy->getId() . "\n");

							//Get the name of the createdBy User
							echo("Record Created By User-Name: " . $createdBy->getName() . "\n");

							//Get the Email of the createdBy User
							echo("Record Created By User-Email: " . $createdBy->getEmail() . "\n");
						}

						//Get the CreatedTime of each Record
						echo("Record CreatedTime: " . $record->getCreatedTime() . "\n");

						//Get the modifiedBy User instance of each Record
						$modifiedBy = $record->getModifiedBy();

						//Check if modifiedBy is not null
						if($modifiedBy != null)
						{
							//Get the ID of the modifiedBy User
							echo("Record Modified By User-ID: " . $modifiedBy->getId() . "\n");

							//Get the name of the modifiedBy User
							echo("Record Modified By User-Name: " . $modifiedBy->getName() . "\n");

							//Get the Email of the modifiedBy User
							echo("Record Modified By User-Email: " . $modifiedBy->getEmail() . "\n");
						}

						//Get the ModifiedTime of each Record
						echo("Record ModifiedTime: " . $record->getModifiedTime() . "\n");

						//To get particular field value
						echo("Record Field Value: " . $record->getKeyValue("Last_Name") . "\n");// FieldApiName

						echo("Record KeyValues: \n");

						//Get the KeyValue map
                        foreach($record->getKeyValues() as $keyName => $value)
                        {
                            if($value != null)
                            {
                                if((is_array($value) && sizeof($value) > 0) && isset($value[0]))
                                {
                                    echo("Record KeyName : " . $keyName . "\n");

                                    $dataList = $value;

                                    foreach($dataList as $data)
                                    {
                                        if(is_array($data))
                                        {
                                            echo("Record KeyName : " . $keyName  . " - Value :  \n");

                                            foreach($data as $key => $arrayValue)
                                            {
                                                echo($key . " : " . $arrayValue);
                                            }
                                        }
                                        else
                                        {
                                            print_r($data); echo("\n");
                                        }
                                    }
                                }
                                else
                                {
                                    echo("Record KeyName : " . $keyName  . " - Value : " . print_r($value)); echo("\n");
                                }
                            }
                        }
                    }
					//Get the Object obtained Info instance
					$info = $responseWrapper->getInfo();

					//Check if info is not null
					if($info != null)
					{
						if($info->getCount() != null)
						{
							//Get the Count of the Info
							echo("Record Info Count: " . $info->getCount() . "\n");
						}

						if($info->getMoreRecords() != null)
						{
							//Get the MoreRecords of the Info
							echo("Record Info MoreRecords: " . $info->getMoreRecords() . "\n");
						}
					}
				}
				//Check if the request returned an exception
				else if($responseHandler instanceof APIException)
				{
					//Get the received APIException instance
					$exception = $responseHandler;

					//Get the Status
					echo("Status: " . $exception->getStatus()->getValue() . "\n");

					//Get the Code
					echo("Code: " . $exception->getCode()->getValue() . "\n");

					echo("Details: " );

					if($exception->getDetails() != null)
                    {
                        echo("Details: \n");

                        //Get the details map
                        foreach ($exception->getDetails() as $keyName => $keyValue)
                        {
                            //Get each value in the map
                            echo($keyName . ": " . $keyValue . "\n");
                        }
                    }

					//Get the Message
					echo("Message: " . $exception->getMessage()->getValue() . "\n");
				}
			}
			else
			{//If response is not as expected
				print_r($response);
			}
		}

}
}
