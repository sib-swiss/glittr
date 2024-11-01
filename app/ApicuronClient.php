<?php

namespace App;

use App\Models\Submission;
use App\Settings\ApicuronSettings;
use Illuminate\Http\Client\Response;
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

    public function sendNewSubmission(Submission $submission): Response
    {
        $term_id = app(ApicuronSettings::class)->apicuron_submission_activity_term ?? config('apicuron.submission_activity_term');
        $response = $this->client->post(
            $this->url . '/api/reports/single',
            [
                'activity_term' => $term_id,
                'curator_orcid' => $submission->apicuron_orcid,
                'entity_uri' => $submission->repository ? route('repository', $submission->repository) : null,
                'timestamp' => $submission->created_at->toISOString(),
            ]
        );

        return $response;
    }
}
