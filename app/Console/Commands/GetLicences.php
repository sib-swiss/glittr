<?php

namespace App\Console\Commands;

use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetLicences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:licences-url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get licences urls from github';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $licences_url = [];

        $current_licences = DB::table('repositories')->select('license')->distinct()->get();
        foreach ($current_licences as $current_licence) {
            $this->addLicences($current_licence->license, $licences_url);
        }

        $main_licenses = collect(GitHub::licenses()->all());
        foreach ($main_licenses as $license) {
            $this->addLicences($license['key'], $licences_url);
        };

        // Sort array by key
        ksort($licences_url);

        foreach ($licences_url as $key => $value) {
            $this->info("'" . $key . "'  => '" . $value . "',");
        }
    }

    protected function addLicences($license_key, &$licences_url) {
        if (!$license_key || $license_key == "" || isset($licences_url[$license_key])) {
            return;
        }
        try {
            $ld = GitHub::licenses()->show($license_key);
            if (isset($ld['html_url'])) {
                $licences_url[$license_key] = $ld['html_url'];
            }
        } catch (\Exception $e) {
            $this->error($license_key . ' => ' . $e->getMessage());
        }
    }
}
