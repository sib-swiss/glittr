<?php

namespace App;

use App\Models\Submission;
use Illuminate\Support\Facades\Http;

class ApicuronClient
{
    public $env;
    public $resourceId;
    public $client;
    public $url;

    public function __construct()
    {
        $this->env = config('apicuron.env');
        $this->resourceId = config('apicuron.resource_id');
        $token = config('apicuron.token');
        $this->client = Http::withToken($token);
        $this->url = $this->env === 'production' ? 'https://apicuron.org' : 'https://dev.apicuron.org';
    }

    public function sendNewSubmission(Submission $submission)
    {
        $response = $this->client->post(
            $this->url . '/api/reports/single',
            [
                'curator_orcid' => $submission->apicuron_orcid,
                'entity_uri' => '',
            ]
        );

        return $response->json();
    }
}
