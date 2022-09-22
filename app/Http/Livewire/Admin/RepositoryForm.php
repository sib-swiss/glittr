<?php

declare(strict_types=1);

namespace App\Http\Livewire\Admin;

use App\Concerns\InteractsWithNotifications;
use App\Models\Repository;
use Illuminate\View\View;
use Livewire\Component;

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

    protected $listeners = [
        'tagsUpdated',
    ];

    public function mount(?int $id, ?string $cancelEvent): void
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

        if ($cancelEvent) {
            $this->cancelEvent = $cancelEvent;
        }
    }

    public function tagsUpdated(array $tagIds): void
    {
        $this->repository['tags'] = $tagIds;
    }

    public function save(): void
    {
        $validatedData = $this->validate();
        $displayName = $validatedData['repository']['url'];
        if ($this->action == 'add') {
            $repository = Repository::create($validatedData['repository']);
            if ($repository) {
                $repository->tags()->sync($validatedData['repository']['tags']);
                $this->notify("Repository {$displayName} successfully added.");
                $this->emitUp('addRepositorySuccess', [
                    'repository' => $repository->id,
                ]);
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
            $this->emitUp('editRepositorySuccess', [
                'repository' => $repository->id,
            ]);
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
