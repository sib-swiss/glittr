<?php

namespace App\Livewire;

use App\Actions\CreateSubmission;
use App\Data\SubmissionData;
use App\Models\Repository;
use App\Models\Tag;
use App\Settings\ApicuronSettings;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Livewire\Component;
use Michelf\Markdown;
use Michelf\MarkdownExtra;

class SubmitForm extends Component
{
    /**
     * List of selected tag ids
     *
     * @var array
     */
    public $tags = [];

    /**
     * Repository url
     *
     * @var string
     */
    public $url;

    /**
     * Submitter's email
     *
     * @var string
     */
    public $email;

    /**
     * Submitter's name
     *
     * @var string
     */
    public $name;

    /**
     * Submitter comment
     *
     * @var string
     */
    public $comment = '';

    /**
     * Display warning if repository already exists
     *
     * @var bool
     */
    public $existingWarning = false;

    /**
     * Form submitted
     *
     * @var bool
     */
    public $submitted = false;

    protected $listeners = [
        'tagsUpdated',
    ];

    public function tagsUpdated(array $tagIds): void
    {
        $this->tags = $tagIds;
    }

    public function updatedUrl($value)
    {
        // Check no repository with the same url exists
        if (Repository::where('url', $value)->exists()) {
            $this->existingWarning = true;
        } else {
            $this->existingWarning = false;
        }
    }

    public function render(): View
    {
        return view('livewire.submit-form', [
            'text' => MarkdownExtra::defaultTransform(app(GeneralSettings::class)->contribute_text),
            'apicuron_enabled' => app(ApicuronSettings::class)->apicuron_enabled,
            'apicuron_title' => app(ApicuronSettings::class)->apicuron_title,
            'apicuron_introduction' => MarkdownExtra::defaultTransform(app(ApicuronSettings::class)->apicuron_introduction),
            'apicuron_login_btn' => app(ApicuronSettings::class)->apicuron_login_btn,
            'apicuron_logged_warning' => Markdown::defaultTransform(app(ApicuronSettings::class)->apicuron_logged_warning),
            'apicuron_logout_btn' => app(ApicuronSettings::class)->apicuron_logout_btn,
        ]);
    }

    public function save(): void
    {
        foreach ($this->tags as $tagId) {
            if (! Tag::find($tagId)) {
                $this->addError('tags', 'Invalid tag submitted.');
            }
        }

        $submissionData = SubmissionData::validateAndCreate([
            'url' => $this->url,
            'name' => $this->name,
            'email' => $this->email,
            'tags' => $this->tags,
            'comment' => $this->comment,
        ]);

        /** @var CreateSubmission $createSumbissionAction */
        $createSumbissionAction = App::make(CreateSubmission::class);
        $submission = $createSumbissionAction->execute($submissionData);

        if ($submission) {
            $this->submitted = true;
        }
    }
}
