<?php

/**
 * Test url: http://gareport.wc-testserv.com/GAreport/mybusiness/index.php
 *
 * API setup.
 * https://developers.google.com/my-business/content/basic-setup?hl=ja
 *
 * Test account.
 * https://business.google.com/edit/l/04661659058810033930
 *
 * Playground: https://developers.google.com/oauthplayground
 *
 * requirements: https://github.com/googleapis/google-api-php-client
 * troubles: https://www.en.advertisercommunity.com/t5/Verification/Main-Google-account-in-unverified/td-p/903331
 */
ini_set('display_errors', 'On');
header('content-type: application/json; charset=utf-8');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/GoogleMyBusiness.php';

$credential = realpath(__DIR__ . '/../storage/phpfan-7fc7674cf95d.json');

$apiTokenFile = __DIR__ . '/../storage/framework/cache/.access_token';
if (!file_exists($apiTokenFile)) {
    touch($apiTokenFile, time());
}

$client = new Google_Client();
$client->setClientId("325711703392-9ld0d8n6dadmsaboh8s2mtomn4k1kd6r.apps.googleusercontent.com");
$client->setClientSecret("XbTpCZ1MG78G4Xil3mFELgT-");
$client->setApplicationName("Web Site Report");
$client->setDeveloperKey('AIzaSyAkBUg7x7FwNHydD_w_P7Dz3MeOsi4xcv8');
$client->setScopes(['https://www.googleapis.com/auth/plus.business.manage']);
$client->setRedirectUri('https://phpfan.net');

putenv("GOOGLE_APPLICATION_CREDENTIALS={$credential}");
$client->useApplicationDefaultCredentials();

if ($accessToken = file_get_contents($apiTokenFile)) {
    $client->setAccessToken(json_decode($accessToken, 1));
}
if ($client->isAccessTokenExpired()) {
    $accessToken = $client->fetchAccessTokenWithAssertion();
    $client->setAccessToken($accessToken);
    file_put_contents($apiTokenFile, json_encode($accessToken));
}
/**
 * カテゴリ取得/検索
 *
 * API usage.
 * https://developers.google.com/my-business/reference/rest/v4/categories/list
 *
 * @param Google_Client $client
 * @param string $query
 */
function categories(Google_Client $client, $query = null)
{
    $service = new Google_Service_MyBusiness($client);
    die(json_encode($service->categories->listCategories([
                'regionCode'   => 'jp',
                'languageCode' => 'ja',
                'pageSize'     => 10,
                'searchTerm'   => $query // ex. 保育園
    ])));
}

/**
 * https://developers.google.com/my-business/reference/rest/v4/accounts.locations/reportInsights
 * @param Google_Client $client
 */
function insights(Google_Client $client)
{
    //
}

/**
 * https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder
 *
 * https://www.en.advertisercommunity.com/t5/Google-My-Business-API/Google-my-business-error-quot-Request-contains-an-invalid/td-p/1792316
 * @param type $client
 */
function reviews(Google_Client $client)
{
    $service = new Google_Service_MyBusiness($client);
    $locationName = "accounts/western-company-project/locations/04661659058810033930";
    return $service->accounts_locations_reviews->listAccountsLocationsReviews($locationName);
}

/**
 * https://developers.google.com/my-business/reference/rest/v4/accounts.locations
 * https://www.en.advertisercommunity.com/t5/Google-My-Business-API/GBU-API-Get-Locations-returns-emply-locations/m-p/1826718#M14017
 * @param type $client
 * @return type
 */
function search(Google_Client $client)
{
    $service = new Google_Service_MyBusiness($client);
    //$place_id = 'ChIJrTLr-GyuEmsRBfy61i59si0';

    $location = new Google_Service_MyBusiness_Location();
    $location->setStoreCode('phpfan');
    $request = new Google_Service_MyBusiness_SearchGoogleLocationsRequest();
    $request->setLocation($location);

    // 500エラー
    return $service->googleLocations->search($request);
}
search($client);

echo '[]';
