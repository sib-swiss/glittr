<?php

namespace App\Livewire\Admin;

use App\Concerns\InteractsWithNotifications;
use App\Settings\GeneralSettings;
use App\Settings\TermsSettings;
use Livewire\Component;

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

    public function mount(GeneralSettings $settings, TermsSettings $termsSettings)
    {
        $this->site_name = $settings->site_name;
        $this->site_description = $settings->site_description;
        $this->homepage_page_title = $settings->homepage_page_title;
        $this->header_text = $settings->header_text;
        $this->about_text = $settings->about_text;
        $this->footer_text = $settings->footer_text;
        $this->contribute_text = $settings->contribute_text;
        $this->mail_signature = $settings->mail_signature;

        $this->terms = $termsSettings->terms;
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }

    public function save(GeneralSettings $settings, TermsSettings $termsSettings)
    {
        $settings->site_name = $this->site_name;
        $settings->site_description = $this->site_description;
        $settings->homepage_page_title = $this->homepage_page_title;
        $settings->header_text = $this->header_text;
        $settings->about_text = $this->about_text;
        $settings->footer_text = $this->footer_text;
        $settings->contribute_text = $this->contribute_text;
        $settings->mail_signature = $this->mail_signature;

        $termsSettings->terms = $this->terms;

        $settings->save();
        $termsSettings->save();

        $this->notify('Settings successfully updated.');
    }
}
