<?php

namespace CapeAndBay\ZingFit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

class ZingFitToken extends Model
{
    use Uuid, SoftDeletes;

    protected $table = 'zing_fit_tokens';

    public function getMostRecentToken($type = 'owner')
    {
        $results = false;

        $record = $this->whereOauthType($type)
            ->whereActive(1)
            ->whereDate('expires_at', '>=', date('Y-m-d h:i:s'))
            ->first();

        if(!is_null($record))
        {
            $results = $record;
        }

        return $results;
    }

    public function insertNew(array $data)
    {
        $results = false;

        $model = new $this;
        $model->oauth_type = $data['type'];
        $model->access_token = $data['access_token'];
        $model->token_type = $data['token_type'];
        $model->expires_at = date('Y-m-d h:i:s',$data['expires_in']);
        $model->scope = $data['scope'];

        if(array_key_exists('refresh_token', $data))
        {
            $model->refresh_token = $data['refresh_token'];
        }

        if(array_key_exists('active', $data))
        {
            $model->active = $data['active'];
        }

        if(array_key_exists('customer_id', $data))
        {
            $model->customer_id = $data['customer_id'];
        }


        if($model->save())
        {
            $results = $model;
        }

        return $results;
    }
}
