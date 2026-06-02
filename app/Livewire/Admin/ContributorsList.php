<?php

namespace App\Livewire\Admin;

use App\Actions\FetchContributorInfo;
use App\Concerns\InteractsWithNotifications;
use App\Models\Contributor;
use App\Models\Repository;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ContributorsList extends Component
{
    use InteractsWithNotifications;
    use WithPagination;

    public string $search = '';

    public string $repositoryFilter = '';

    public string $sortBy = 'username_asc';

    /** @var array<int> */
    public array $selectedIds = [];

    public bool $selectAll = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedRepositoryFilter(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selectedIds = $this->buildSortedQuery()
                ->forPage($this->getPage(), 25)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->all();
        } else {
            $this->selectedIds = [];
        }
    }

    public function toggleBot(Contributor $contributor): void
    {
        $contributor->is_bot = ! $contributor->is_bot;
        $contributor->save();

        $status = $contributor->is_bot ? 'flagged as bot' : 'unflagged as bot';
        $this->notify("Contributor {$contributor->username} {$status}.");
    }

    public function fetchInfo(Contributor $contributor): void
    {
        $fetched = app(FetchContributorInfo::class)->execute($contributor);

        if ($fetched) {
            $this->notify("Info fetched for {$contributor->username}.");
        } else {
            $this->errorNotification("Could not fetch info for {$contributor->username} (rate limited or unavailable).");
        }
    }

    public function bulkFlagAsBots(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }

        Contributor::query()->whereIn('id', $this->selectedIds)->update(['is_bot' => true]);
        $count = count($this->selectedIds);
        $this->clearSelection();
        $this->notify("{$count} contributor(s) flagged as bot.");
    }

    public function bulkUnflagAsBots(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }

        Contributor::query()->whereIn('id', $this->selectedIds)->update(['is_bot' => false]);
        $count = count($this->selectedIds);
        $this->clearSelection();
        $this->notify("{$count} contributor(s) unflagged as bot.");
    }

    public function bulkFetchInfo(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }

        $action = app(FetchContributorInfo::class);
        $fetched = 0;
        $failed = 0;

        Contributor::query()->whereIn('id', $this->selectedIds)->each(function (Contributor $contributor) use ($action, &$fetched, &$failed) {
            if ($action->execute($contributor)) {
                $fetched++;
            } else {
                $failed++;
            }
        });

        $this->clearSelection();

        if ($fetched > 0) {
            $this->notify("Info fetched for {$fetched} contributor(s).");
        }

        if ($failed > 0) {
            $this->errorNotification("{$failed} contributor(s) could not be fetched (rate limited or unavailable).");
        }
    }

    private function clearSelection(): void
    {
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    private function buildQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = Contributor::query();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('username', 'like', '%' . $this->search . '%')
                    ->orWhere('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('orcid', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->repositoryFilter !== '') {
            $query->whereHas('repositories', fn ($q) => $q->where('repositories.id', $this->repositoryFilter));
        }

        return $query;
    }

    private function buildSortedQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = $this->buildQuery()->withCount('repositories');
        $dir = str_ends_with($this->sortBy, '_desc') ? 'desc' : 'asc';

        match (true) {
            str_starts_with($this->sortBy, 'full_name') => $query->orderByRaw("full_name IS NULL, full_name {$dir}"),
            str_starts_with($this->sortBy, 'orcid_fetched_at') => $query->orderByRaw("orcid_fetched_at IS NULL, orcid_fetched_at {$dir}"),
            str_starts_with($this->sortBy, 'orcid') => $query->orderByRaw("orcid IS NULL, orcid {$dir}"),
            str_starts_with($this->sortBy, 'repositories') => $query->orderBy('repositories_count', $dir),
            str_starts_with($this->sortBy, 'bot') => $query->orderBy('is_bot', $dir)->orderBy('username'),
            default => $query->orderBy('username', $dir),
        };

        return $query;
    }

    public function render(): View
    {
        $contributors = $this->buildSortedQuery()
            ->with(['repositories:id,url'])
            ->paginate(25);

        $repositories = Repository::query()
            ->select('id', 'name')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.contributors-list', [
            'contributors' => $contributors,
            'repositories' => $repositories,
        ]);
    }
}
