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
        $clientSecret = 'LAQUf2rMFb2lVUVm1n1GcUfslXgBIiYpGRxT3f48W4taisqVgUDzyhKgayL59Ev8';
        $clientId = '53ee0943-9f8a-4ce5-8e38-85a92090b2b5';
        $redirectUri = 'https://cursos.ultima.school/amocrm/callback';
        $code = 'def502004023480e4c357543138971b62128ef77ab1c08285bcc2437cf6c32960145f29f5a7d9e5b1c1c3fc2f2492bac8c2d1543a0321076263fc0daf62ae964e73165c03966538031f92229dfbdcc319e7370600639130cfef8bc82bae552b9baf1c3715d59342afbbe228411b61c7ba134bc72b210d46e47df06d0719478a7afbe4af2188938e407de25368bec6aa3f6631462097ddd02bc14fc14e4b1bacd77de0a04d8e93f52606973402f6c4e0776e81c04aad54c9ec2a026cd7dd534ae10fe83be90e9b110342892a6d9b46ca6af9f089db318c9d0ab8ea8872194d14bde53fe67029e4545378daf6e0de50b4e3d8c1461cabde6e55cfbeeb5e2fdef86fdf56b260917f71aaea3bb995947063061c7cb2b98da323ffb9bbba75f8c7295270541728022c79c9ba5f75ed4984f31b577923b111cce2c9763b12530745250b2590c9303b9f29bcc59da3feb48493046878921bcc4e32965fca4255388bb9e90c2940f2b4a5165ebcea7485b69fb8aca2330f84beeb380884e19688dbb3dab9a2e0bb8c47ae7a45fb7e63714958c535d2866a044f4aeefed58e460b740914145895d4608a0ab296b0dcb4aeb8c8163a2c4ea25072bfb5aae45a487ed6c51944749416cb5aac1a6dc80b81a57167847ed75941bc2d7778d';
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
