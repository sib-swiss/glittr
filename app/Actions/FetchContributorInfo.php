<?php

namespace App\Actions;

use App\Models\Contributor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchContributorInfo
{
    public function execute(Contributor $contributor): bool
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (compatible; training-collection-bot/1.0)',
        ])->get('https://github.com/' . $contributor->username);

        if ($response->status() === 429) {
            Log::warning('FetchContributorInfo: rate limited by GitHub', [
                'contributor_id' => $contributor->id,
                'username' => $contributor->username,
            ]);

            return false;
        }

        if (! $response->successful()) {
            return false;
        }

        $body = $response->body();
        $updateData = [
            'orcid' => null,
            'orcid_fetched_at' => now(),
        ];

        if (preg_match('/href="https:\/\/orcid\.org\/([\d]{4}-[\d]{4}-[\d]{4}-[\d]{3}[\dX])"/i', $body, $matches)) {
            $updateData['orcid'] = $matches[1];
        }

        if (preg_match('/<span[^>]+itemprop=["\']name["\'][^>]*>\s*(.*?)\s*<\/span>/si', $body, $nameMatches)) {
            $name = trim(strip_tags($nameMatches[1]));
            $updateData['full_name'] = $name !== '' ? $name : null;
        }

        if (preg_match('/<span[^>]+class=["\'][^"\']*p-org[^"\']*["\'][^>]*>(.*?)<\/span>/si', $body, $companyMatches)) {
            $company = ltrim(trim(strip_tags($companyMatches[1])), '@');
            $updateData['company'] = $company !== '' ? $company : null;
        }

        $contributor->update($updateData);

        return true;
    }
}
