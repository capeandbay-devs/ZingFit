<?php

namespace CapeAndBay\ZingFit;

use Ixudra\Curl\Facades\Curl;

class ZingFit
{
    protected $access_token;

    public function __construct(string $access_token = null)
    {
        if(!is_null($access_token))
        {
            $this->access_token = $access_token;
        }
    }

    public function getOwnerAccessToken()
    {
        $results = false;

        $url = $this->getRootUrl().'/oauth/token';
        $headers = [
            'Authorization' => 'Basic '.base64_encode(config('zingfit.client_id').':'.config('zingfit.client_secret'))
        ];
        $payload = ['grant_type'=> 'client_credentials'];

        $response = Curl::to($url.'?'.http_build_query($payload))
            ->withHeaders($headers)
            ->withContentType('application/json')
            ->asJson(true)
            ->post();

        if($response)
        {
            $results = $response;
            $this->setAccessToken($response);
        }

        return $results;
    }

    private function setAccessToken($response)
    {
        $this->access_token = $response['access_token'];
    }

    public function getRootUrl()
    {
        $results = config('zingfit.sandbox_url');

        if((env('APP_ENV', 'local') == 'production') || (env('APP_ENV', 'local') == 'prod'))
        {
            $results = config('zingfit.production_url');
        }

        return $results;
    }
}