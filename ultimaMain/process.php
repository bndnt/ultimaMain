<?php

require 'vendor/autoload.php';

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\AccountModel;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Exceptions\AmoCRMApiNoContentException;
use AmoCRM\Collections\LinksCollection;
use AmoCRM\Models\LeadModel;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Filters\ContactsFilter;
use League\OAuth2\Client\Token\AccessTokenInterface;
use League\OAuth2\Client\Token\AccessToken;

Sentry\init(['dsn' => 'https://842fbeda697b4c0692e4e2ea03128a23@o951626.ingest.sentry.io/5900600' ]);

try {
    $name = $_REQUEST['name'];
    $tel = '+' . preg_replace('/\D/', '', $_REQUEST['tel']);
    $email = $_REQUEST['email'];

    if (!$name || !$tel || !$email) {
        return http_response_code(500);
    }

    GetResponseService::save($name, $email, $tel);

    $amoCrmService = new AmoCrmService();

    $amoCrmService->save($name, $email, $tel);
} catch (Exception $e) {
    return http_response_code(500);
}

class GetResponseService
{
    public static function save($name, $email, $tel)
    {
        $client = new GuzzleHttp\Client(['http_errors' => false]);

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        $response = $client->request(
            'POST',
            'https://api.getresponse.com/v3/contacts',
            [
                'headers' => [
                    'X-Auth-Token' => 'api-key yl9qr7p5o38hmds6wjdsemn305fvcmle',
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'name' => $name,
                    'email' => $email,
                    'dayOfCycle' => '0',
                    'campaign' => [
                        'campaignId' => 'j6CRU'
                    ],
                    'customFieldValues' => [
                        [
                            'customFieldId' => 'pcfXV2',
                            'value' => [$tel]
                        ]
                    ],
                    'ipAddress' => $ip
                ]
            ]
        );

        $statusCode = $response->getStatusCode();

        if ($statusCode != 409 && $statusCode >= 400) {
            throw new Exception(json_encode($response->getBody()->getContents()));
        }
    }
}

class AmoCrmService
{
    private $client;

    public function __construct()
    {
        $clientSecret = 'uAxYklIH9lQIIH7TG4plZLgJYv8mqAM7knDDr0DsEJgJISGBjHmnop0hGNig4BSG';
        $clientId = '53ee0943-9f8a-4ce5-8e38-85a92090b2b5';
        $redirectUri = 'https://cursos.ultima.school/amocrm/callback';
        $code = 'def502002e95311b63fb126ec62f4501fd8f222ebe91ffa168dc06ff6d40533f456cadc3fa2ef424ab2ffe862030ba8f4554bebe8618504291b90dc2340dfd3a866b570b1325be22e30460c00ac8c018e45d52051af784d9a923b6647a49e6c3e0e1edfc45132227373066f1d80f4be05d299aa1616f1d33cc800b2a622795b387b409d04dc48d0d5b0c52add4e844194277f44dffc85a613822ed2c26eeff25d013912ef29e34222128bc9ed41e0df7a40f23c6c6c12a42af6c956589ffcc1ef50e3c66dcce2af68068c432e635884ddf98b7857ebcd66fa291904334ec9a332434e2453466b15bbac5503be79bcb16431b3089b710dd5635bbcd6f000df28aefb3e70d7bc68c6b8be5786e2dc4f86a602cf1bc1520c9d407f8f6ce5dc7afe0b06023ba69cfa686fcffb91744aaaefa0118c5ca9a023f7bbbd12e8f3227629ef6b0c23e124b8b4fdc2b4c19aab1083258d12e9a730c7dc16901cddf94b00d19c3f9ad38f42c39a16af774fd34de13178e4d28770e298f1658900ea69758f32eeb484a83d1e8b598656535c9911a76c8408ef4a325b7265c1adb3e54be33bcbbb9c55114040dc70c0ebb42dd1e4bd9221e15bb2394da2268d9fc5fff0f22134fda61485530db8f28cffd931d72f5a2c58851817edab877ff';
        $baseDomain = 'ultimaschool.amocrm.com';

        $apiClient = new \AmoCRM\Client\AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
        $apiClient->setAccountBaseDomain($baseDomain);

        $accessToken = file_get_contents('amocrm.txt');

        if ($accessToken) {
            $accessToken = json_decode($accessToken, true);

            $accessToken = new AccessToken([
                'access_token' => $accessToken['accessToken'],
                'refresh_token' => $accessToken['refreshToken'],
                'expires' => $accessToken['expires'],
                'baseDomain' => $accessToken['baseDomain'],
            ]);
        } else {
            $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($code);

            file_put_contents('amocrm.txt', json_encode([
                'accessToken' => $accessToken->getToken(),
                'refreshToken' => $accessToken->getRefreshToken(),
                'expires' => $accessToken->getExpires(),
                'baseDomain' => $baseDomain
            ]));
        }

        $apiClient->setAccessToken($accessToken)
                ->setAccountBaseDomain('ultimaschool.amocrm.com')
                ->onAccessTokenRefresh(
                    function (AccessTokenInterface $accessToken, string $baseDomain) {
                        file_put_contents('amocrm.txt', json_encode([
                            'accessToken' => $accessToken->getToken(),
                            'refreshToken' => $accessToken->getRefreshToken(),
                            'expires' => $accessToken->getExpires(),
                            'baseDomain' => $baseDomain,
                        ]));
                    }
                );

        $this->client = $apiClient;
    }

    public function save($name, $email, $tel)
    {
        $contact = $this->storeOrGetContact($name, $email, $tel);

        $utms = [];
        $ref = $_SERVER['HTTP_REFERER'];
        $url = parse_url($ref);

        if (isset($url['query'])) {
            parse_str($url['query'], $utms);
        }

        $lead = $this->createLead('homepage', [
            1414839 => isset($utms['utm_medium']) ? $utms['utm_medium'] : '',
            1414841 => isset($utms['utm_term']) ? $utms['utm_term'] : '',
            1414843 => isset($utms['utm_campaign']) ? $utms['utm_campaign'] : '',
            1414845 => isset($utms['utm_content']) ? $utms['utm_content'] : '',
            1414849 => isset($utms['utm_name']) ? $utms['utm_name'] : '',
            1414851 => isset($utms['utm_source']) ? $utms['utm_source'] : '',
            1499105 => $ref,
        ]);

        $links = new LinksCollection();

        $links->add($contact);
            
        $this->client->leads()->link($lead, $links);
    }

    private function storeOrGetContact($name, $email, $tel)
    {
        $filter = new ContactsFilter();
        $filter->setQuery($email);

        try {
            $contacts = $this->client->contacts()->get($filter);
        } catch (AmoCRMApiNoContentException $e) {
            $contacts = null;
        }

        if (isset($contacts[0])) {
            return $contacts[0];
        } else {
            $contact = new ContactModel();
            $contact->setName($name);

            $contactsCustomFieldsValues = new CustomFieldsValuesCollection();

            $telField = (new TextCustomFieldValuesModel())->setFieldCode('PHONE');
            $telField->setValues(
                (new TextCustomFieldValueCollection())
                    ->add(
                        (new TextCustomFieldValueModel())
                            ->setValue($tel)
                    )
            );
            $contactsCustomFieldsValues->add($telField);

            $emailField = (new TextCustomFieldValuesModel())->setFieldCode('EMAIL');
            $emailField->setValues(
                (new TextCustomFieldValueCollection())
                    ->add(
                        (new TextCustomFieldValueModel())
                            ->setValue($email)
                    )
            );
            $contactsCustomFieldsValues->add($emailField);

            $contact->setCustomFieldsValues($contactsCustomFieldsValues);

            $contactModel = $this->client->contacts()->addOne($contact);

            return $contactModel;
        }
    }

    private function createLead($name, $fields)
    {
        $lead = new LeadModel();

        $leadCustomFieldsValues = new CustomFieldsValuesCollection();

        foreach ($fields as $key => $value) {
            $textCustomFieldValueModel = new TextCustomFieldValuesModel();
            $textCustomFieldValueModel->setFieldId($key);
            $textCustomFieldValueModel->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue($value))
            );
            $leadCustomFieldsValues->add($textCustomFieldValueModel);
        }

        $lead->setCustomFieldsValues($leadCustomFieldsValues);

        $lead->setName($name);

        $lead->setPipelineId(4490092)->setStatusId(41515582);

        $lead = $this->client->leads()->addOne($lead);

        return $lead;
    }
}
