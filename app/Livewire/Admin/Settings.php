<?php

namespace App\Livewire\Admin;

use App\Concerns\InteractsWithNotifications;
use App\Settings\ApicuronSettings;
use App\Settings\GeneralSettings;
use App\Settings\TermsSettings;
use Livewire\Component;
use Spatie\FlareClient\Api;

class Settings extends Component
{
    use InteractsWithNotifications;

    public $site_name;

    public $site_description;

    public $homepage_page_title;

    public $header_text;

    public $about_text;

    public $footer_text;

    public $contribute_text;

    public $mail_signature;

    public $terms;

    public bool $apicuron_enabled;

    public string $apicuron_submission_activity_term;

    public string $apicuron_title;

    public string $apicuron_introduction;

    public string $apicuron_login_btn;

    public string $apicuron_logged_warning;

    public string $apicuron_logout_btn;

    public function mount(GeneralSettings $settings, TermsSettings $termsSettings, ApicuronSettings $apicuronSettings)
    {
        $this->site_name = $settings->site_name;
        $this->site_description = $settings->site_description;
        $this->homepage_page_title = $settings->homepage_page_title;
        $this->header_text = $settings->header_text;
        $this->about_text = $settings->about_text;
        $this->footer_text = $settings->footer_text;
        $this->contribute_text = $settings->contribute_text;
        $this->mail_signature = $settings->mail_signature;

        $this->apicuron_enabled = $apicuronSettings->apicuron_enabled;
        $this->apicuron_submission_activity_term = $apicuronSettings->apicuron_submission_activity_term;
        $this->apicuron_title = $apicuronSettings->apicuron_title;
        $this->apicuron_introduction = $apicuronSettings->apicuron_introduction;
        $this->apicuron_login_btn = $apicuronSettings->apicuron_login_btn;
        $this->apicuron_logged_warning = $apicuronSettings->apicuron_logged_warning;
        $this->apicuron_logout_btn = $apicuronSettings->apicuron_logout_btn;

        $this->terms = $termsSettings->terms;
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }

    public function save(GeneralSettings $settings, TermsSettings $termsSettings, ApicuronSettings $apicuronSettings)
    {
        $settings->site_name = $this->site_name;
        $settings->site_description = $this->site_description;
        $settings->homepage_page_title = $this->homepage_page_title;
        $settings->header_text = $this->header_text;
        $settings->about_text = $this->about_text;
        $settings->footer_text = $this->footer_text;
        $settings->contribute_text = $this->contribute_text;
        $settings->mail_signature = $this->mail_signature;

        $apicuronSettings->apicuron_enabled = $this->apicuron_enabled;
        $apicuronSettings->apicuron_submission_activity_term = $this->apicuron_submission_activity_term;
        $apicuronSettings->apicuron_title = $this->apicuron_title;
        $apicuronSettings->apicuron_introduction = $this->apicuron_introduction;
        $apicuronSettings->apicuron_login_btn = $this->apicuron_login_btn;
        $apicuronSettings->apicuron_logged_warning = $this->apicuron_logged_warning;
        $apicuronSettings->apicuron_logout_btn = $this->apicuron_logout_btn;

        $termsSettings->terms = $this->terms;

        $settings->save();
        $termsSettings->save();
        $apicuronSettings->save();

        $this->notify('Settings successfully updated.');
    }
}
