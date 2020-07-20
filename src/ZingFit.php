<?php

namespace CapeAndBay\ZingFit;

use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;

class ZingFit
{
    protected $access_token, $mode;

    public function __construct(string $access_token = null)
    {
        if(!is_null($access_token))
        {
            $this->access_token = $access_token;
        }
        else
        {
            $this->init();
        }
    }

    public function init()
    {
        try {
            $access_token = $this->getStoredOwnerAccessToken();

            if($access_token)
            {
                $this->access_token = $access_token['access_token'];
            }
            else
            {
                $this->getOwnerAccessToken();
            }
        }
        catch(\Exception $e)
        {
            Log::info($e->getMessage());
        }

    }

    public function getStoredOwnerAccessToken()
    {
        $results = false;

        $model = new ZingFitToken();

        if($record = $model->getMostRecentToken())
        {
            $results = $record->toArray();
        }

        return $results;
    }

    public function getCustomerAccessToken($customer_user_name, $customer_password)
    {
        $results = false;

        $url = $this->getRootUrl().'/oauth/token';
        $headers = [
            'Authorization' => 'Basic '.base64_encode(config('zingfit.client_id').':'.config('zingfit.client_secret'))
        ];

        $payload = [
            'grant_type'=> 'password',
            'username' => $customer_user_name,
            'password' => $customer_password
        ];

        $response = Curl::to($url.'?'.http_build_query($payload))
            ->withHeaders($headers)
            ->withContentType('application/json')
            ->asJson(true)
            ->post();

        if($response)
        {
            $results = $response;
            $this->mode = 'customer';
            $this->setAccessToken($response);
        }

        return $results;
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
            $this->mode = 'owner';
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

    public function getAllRegions()
    {
        $results = [];

        $url = $this->getRootUrl().'/regions';

        $headers = [
            'Authorization' => 'Bearer '.$this->access_token
        ];

        $response = Curl::to($url)
            ->withHeaders($headers)
            ->withContentType('application/json')
            ->asJson(true)
            ->get();

        if($response)
        {
            $results = $response;
        }

        return $results;
    }

    public function getAllSites($region_id)
    {
        $results = [];

        $url = $this->getRootUrl().'/sites';

        $headers = [
            'Authorization' => 'Bearer '.$this->access_token,
            'X-ZINGFIT-REGION-ID' => $region_id
        ];

        $response = Curl::to($url)
            ->withHeaders($headers)
            ->withContentType('application/json')
            ->asJson(true)
            ->get();

        if($response)
        {
            $results = $response;
        }

        return $results;
    }

    public function getAllSeriesForSite($region_id, $site_id)
    {
        $results = [];

        $url = $this->getRootUrl().'/series?siteId='.$site_id;

        $headers = [
            'Authorization' => 'Bearer '.$this->access_token,
            'X-ZINGFIT-REGION-ID' => $region_id
        ];

        $response = Curl::to($url)
            ->withHeaders($headers)
            ->withContentType('application/json')
            ->asJson(true)
            ->get();

        if($response)
        {
            $results = $response;
        }

        return $results;
    }

    public function createNewCustomer($region_id, $payload)
    {
        $results = false;

        $url = $this->getRootUrl().'/account';

        $headers = [
            'Authorization' => 'Bearer '.$this->access_token,
            'X-ZINGFIT-REGION-ID' => $region_id
        ];

        $response = Curl::to($url)
            ->withHeaders($headers)
            ->withContentType('application/json')
            ->withData($payload)
            ->asJson(true)
            ->post();

        if($response)
        {
            $results = $response;
        }

        return $results;
    }

    public function getCustomer($region_id)
    {
        $results = false;

        $url = $this->getRootUrl().'/account';

        $headers = [
            'Authorization' => 'Bearer '.$this->access_token,
            'X-ZINGFIT-REGION-ID' => $region_id
        ];

        $response = Curl::to($url)
            ->withHeaders($headers)
            ->withContentType('application/json')
            ->asJson(true)
            ->get();

        if($response)
        {
            $results = $response;
        }

        return $results;
    }

    public function saveCustomerAccessToken($payload)
    {
        $results = false;

        if($this->getAccessMode() == 'customer')
        {
            $token = new ZingFitToken();
            if($token = $token->insertNew($payload))
            {
                $results = $token;

                // de-activate and softly delete any previous existing records
                $previous_tokens = $token->whereCustomerId($token->customer_id)->get();

                foreach ($previous_tokens as $t)
                {
                    if($t->id != $token->id)
                    {
                        $t->active = 0;
                        $t->save();
                        $t->delete();
                    }
                }
            }
        }

        return $results;
    }

    public function getAccessMode()
    {
        return $this->mode;
    }
}
