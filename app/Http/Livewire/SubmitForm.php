<?php

namespace App\Http\Livewire;

use App\Actions\CreateSubmission;
use App\Data\SubmissionData;
use App\Models\Tag;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Livewire\Component;

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

    public function render(): View
    {
        return view('livewire.submit-form');
    }

    public function save(): void
    {
        // Additional manual tags checks.
        if (empty($this->tags)) {
            $this->addError('tags', 'You need to provide at least on tag.');
        }

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
