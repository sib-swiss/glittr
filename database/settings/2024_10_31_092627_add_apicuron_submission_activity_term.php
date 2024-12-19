<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.apicuron_enabled', false);
        $this->migrator->add('general.apicuron_submission_activity_term', 'repository_submission');
    }
};
