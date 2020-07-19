<?php

namespace CapeAndBay\ZingFit\Console\Commands;

use CapeAndBay\ZingFit\ZingFitToken;
use CapeAndBay\ZingFit\ZingFit;

class RefreshZingFitToken extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zingfit:refresh-owner-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes the ZigFit Access Token';

    public $cron_name = 'ZingFit Owner OAuth Token Refresh Cron';
    public $cron_log = 'zing-fit-token-refresh-command-log';

    protected $zingfit, $z_token;

    /**
     * Create a new command instance.
     * @param ZingFit $zingfit
     * @return void
     */
    public function __construct(ZingFit $zingfit, ZingFitToken $z_token)
    {
        parent::__construct();
        $this->zingfit = $zingfit;
        $this->z_token = $z_token;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function start()
    {
        // Check the ZigFitTokens token for the latest active Owner token or skip
        $this->info('Location Previously Stored Access Token Record...');
        if($record = $this->z_token->getMostRecentToken())
        {
            $this->info('Located', $record->toArray);
        }
        else
        {
            $this->info('Not Found, Moving On');
        }


        // Regardless of existence, ping ZingFit for new token.
        $this->info('Requesting new Token From Zingfit...');
        $token = $this->zingfit->getOwnerAccessToken();

        if($token)
        {
            $this->info('Success! Access Token Obj - '. json_encode($token));
            $token['expires_in'] = strtotime('now') + $token['expires_in'];
            $this->info('Expires at '.date('Y-m-d h:i:s', $token['expires_in']));
        }
        else
        {
            $this->alert('Failed to obtain token. Ending..');
            return;
        }

        // If record exists in DB, deactivate.
        if($record)
        {
            if($record->access_token == $token['access_token'])
            {
                $this->info('No change detected. Finishing...');
                return;
            }
            else
            {
                $this->info('Deactivating Old Record');
                $record->active = 0;
                $record->save();
            }
        }

        $payload = [
            'type' => 'owner',
            'active' => 1
        ];

        $payload = array_merge($payload, $token);

        $new_token_record = $this->z_token->insertNew($payload);

        if($new_token_record)
        {
            $this->info('Success! New Record - '. json_encode($new_token_record->toArray()));
        }
        else
        {
            $this->alert('Failed! Try Again Later...');
        }
    }
}
