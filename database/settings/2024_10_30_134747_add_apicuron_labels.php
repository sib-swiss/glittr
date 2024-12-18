<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.apicuron_title', 'Participate with the APICURON platform');

        $this->migrator->add('general.apicuron_introduction', 'APICURON collects and aggregates activity events from third party resources and generates statistics, achievements and leaderboards ');

        $this->migrator->add('general.apicuron_login_btn', 'Login with your ORCID iD');
    }
};
