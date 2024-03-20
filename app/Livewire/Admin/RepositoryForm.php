<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Concerns\InteractsWithNotifications;
use App\Facades\Remote;
use App\Mail\SubmissionAccepted;
use App\Models\Repository;
use App\Models\Submission;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Livewire\Component;
use Spatie\Url\Url;

class RepositoryForm extends Component
{
    use InteractsWithNotifications;

    /**
     * Repository data
     *
     * @var array
     */
    public $repository = [
        'url' => '',
        'author_id' => null,
        'website' => '',
        'tags' => [],
    ];

    /**
     * Display tests results
     *
     * @var bool
     */
    public $showTests = false;

    /**
     * Tests results data
     *
     * @var array
     */
    public $tests = [];

    /**
     * Cancel button event to emit
     *
     * @var string
     */
    public $cancelEvent = '';

    /**
     * add or update form action depnding on recieved id
     *
     * @var string
     */
    public $action = 'add';

    /**
     * Optional submission for createion
     *
     * @var int
     */
    public $submissionId;

    /**
     * Submisssion validation message
     *
     * @var string
     */
    public $submissionMessage = '';

    /**
     * Display warning if repository already exists
     *
     * @var bool
     */
    public $existingWarning = false;

    protected $listeners = [
        'tagsUpdated',
    ];

    public function mount(?int $id, ?string $cancelEvent, ?int $fromSubmissionId = null): void
    {
        if ($id) {
            $r = Repository::find($id);
            $this->repository = [
                'id' => $r->id,
                'url' => (string) $r->url,
                'author_id' => $r->author_id,
                'website' => (string) $r->website,
                'tags' => $r->tags->pluck('id')->toArray(),
            ];
            $this->action = 'edit';
        } else {
            $this->action = 'add';
        }

        if ($fromSubmissionId && $this->action == 'add') {
            $submission = Submission::find($fromSubmissionId);
            if ($submission) {
                $this->submissionId = $submission->id;
                $this->repository['url'] = (string) $submission->url;
                $this->repository['tags'] = $submission->tags->pluck('id')->toArray();
            }
        }

        if ($cancelEvent) {
            $this->cancelEvent = $cancelEvent;
        }

        $this->checkExisting();
        $this->resetTests();
    }

    public function tagsUpdated(array $tagIds): void
    {
        $this->repository['tags'] = $tagIds;
    }

    public function updated($name, $value): void
    {
        if ($name == 'repository.url') {
            $this->checkExisting();
        }
    }

    public function testRemote()
    {
        if ($this->repository['url'] != '') {
            $url = Url::fromString($this->repository['url']);
            $api = Remote::resolveAPI(url: $url);
            if ($api) {
                $tests['api'] = $api;
                try {
                    $repo = Remote::driver($api)->getData($url);
                    $this->tests['repo'] = true;
                } catch (Exception $e) {
                    $this->tests['errors'][] = 'REPO: '.$e->getMessage();
                }
                try {
                    $author = Remote::driver($api)->getAuthorData($url);
                    $this->tests['author'] = true;
                } catch (Exception $e) {
                    $this->tests['errors'][] = 'AUTHOR: '.$e->getMessage();
                }
                if ($this->tests['repo'] && $this->tests['author']) {
                    $this->tests['class'] = 'bg-green-50 text-green-500 border-green-500';
                } elseif ($this->tests['repo'] && ! $this->tests['author']) {
                    $this->tests['class'] = 'bg-orange-50 text-orange-500 border-orange-500';
                }
            } else {
                $this->tests['errors'][] = 'No api resolved for the url';
            }
        } else {
            $this->tests['errors'][] = 'No url defined';
        }
        $this->showTests = true;
    }

    protected function checkExisting(): void
    {
        $this->existingWarning = false;
        if ($this->repository['url'] != '') {
            $query = Repository::where('url', $this->repository['url']);
            // in edit mode, remove current id from check
            if ($this->action == 'edit') {
                $query->where('id', '!=', $this->repository['id']);
            }
            // remove trailing slash if any
            if (substr($this->repository['url'], -1) == '/') {
                $url = substr($this->repository['url'], 0, -1);
            } else {
                $url = $this->repository['url'];
            }
            if ($query->exists()) {
                $this->existingWarning = true;
            }
        }
    }

    protected function resetTests()
    {
        $this->tests = [
            'api' => '',
            'repo' => false,
            'author' => false,
            'errors' => [],
            'class' => 'bg-red-50 text-red-500 border-red-500',
        ];
    }

    public function save(): void
    {
        if ($this->existingWarning) {
            $this->addError('url', 'Repository already exists.');

            return;
        }
        $validatedData = $this->validate();
        $displayName = $validatedData['repository']['url'];
        if ($this->action == 'add') {
            $repository = Repository::create($validatedData['repository']);
            if ($repository) {
                $repository->tags()->sync($validatedData['repository']['tags']);

                // Mark submission as validated
                if ($this->submissionId) {
                    $submission = Submission::find($this->submissionId);
                    $submission->repository_id = $repository->id;
                    $submission->validated = true;
                    $submission->validation_message = $this->submissionMessage;
                    $submission->validated_by = Auth::user()->id;
                    $submission->validated_at = Carbon::now();
                    $submission->save();

                    // Send email to the submitter
                    if ($submission->email && filter_var($submission->email, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($submission->email)->bcc(config('glittr.notification_emails'))->send(new SubmissionAccepted($submission));
                    }
                }

                $this->notify("Repository {$displayName} successfully added.");
                $this->dispatch(
                    'addRepositorySuccess',
                    repository: $repository->id,
                );
            } else {
                $this->errorNotification("Error trying to create repository {$displayName}.");
            }
        } else {
            $repository = Repository::find($this->repository['id']);
            $repository->update($validatedData['repository']);
            //re-attach tags for ordering
            $repository->tags()->detach();
            $repository->tags()->sync($validatedData['repository']['tags']);

            $this->notify("Repository {$displayName} successfully updated.");

            $this->dispatch(
                'editRepositorySuccess',
                repository: $repository->id,
            );
        }
    }

    public function render(): View
    {
        return view('livewire.admin.repository-form');
    }

    protected function rules(): array
    {
        //TODO: maybe different rules depending on add/update?
        return [
            'repository.url' => 'required|starts_with:https://',
            'repository.website' => 'nullable|starts_with:https://,http://',
            'repository.tags' => 'required|array|min:1',
            'repository.author_id' => 'nullable|exists:App\Models\Author,id',
        ];
    }
}
