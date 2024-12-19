<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.apicuron_logout_btn', 'Logout');
        $this->migrator->add('general.apicuron_logged_warning', 'You are currently logged in with your ORCID iD for additional submissions. If you prefer you can logout from ORCID.');
    }
};
