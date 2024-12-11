<?php

namespace App;

use App\Models\Submission;
use App\Settings\ApicuronSettings;
use Exception;
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
        $this->client->withHeaders(['accept' => 'application/json', 'version' => '2']);
        $this->url = $this->env === 'production' ? 'https://apicuron.org' : 'https://dev.apicuron.org';
    }

    public function sendNewSubmission(Submission $submission): Response
    {
        $term_id = app(ApicuronSettings::class)->apicuron_submission_activity_term ?? config('apicuron.submission_activity_term');
        if (!$submission->repository) {
            throw new Exception('Submission does not have a repository');
        }
        if (!$submission->apicuron_orcid) {
            throw new Exception('Submission does not have an ORCID');
        }
        $data = [
            'activity_term' => $term_id,
            'curator_orcid' => $submission->apicuron_orcid,
            'entity_uri' => route('repository', $submission->repository),
            'timestamp' => $submission->created_at->toISOString(),
            'resource_id' => $this->resourceId,
        ];
        $response = $this->client->post(
            $this->url . '/api/reports/single',
            $data
        );

        return $response;
    }

    public function getTerms()
    {
        return $this->client->get($this->url . '/api/terms', ['resource_id' => $this->resourceId]);
    }
}
