<?php

namespace App\Livewire\Admin;

use App\Concerns\InteractsWithNotifications;
use App\Models\Ontology;
use Livewire\Component;

class Ontologies extends Component
{
    use InteractsWithNotifications;

    public $showAdd = false;

    public $showEdit = false;

    public $confirmingDeletion = false;

    public $idBeingUpdated = null;

    public $idBeingDeleted = null;

    public $addIncrement = 0;

    protected $listeners = [
        'editOntologyCancel',
        'addOntologyCancel',
        'editOntologySuccess',
        'addOntologySuccess',
    ];

    public function addOntologyCancel(): void
    {
        $this->showAdd = false;
        $this->addIncrement++;
    }

    public function editOntologyCancel(): void
    {
        $this->idBeingUpdated = null;
        $this->showEdit = false;
    }

    public function addOntologySuccess(Ontology $ontology): void
    {
        $this->notify(__("Ontology {$ontology->name} created successfully."));

        $this->showAdd = false;
    }

    public function editOntologySuccess(Ontology $ontology): void
    {
        $this->notify(__("Ontology {$ontology->name} updated successfully."));

        $this->idBeingUpdated = null;
        $this->showEdit = false;
    }

    public function add()
    {
        $this->showAdd = true;
        $this->addIncrement++;
    }

    public function edit($id)
    {
        $this->idBeingUpdated = $id;
        $this->showEdit = true;
    }

    public function delete($id)
    {
        $this->idBeingDeleted = $id;
        $this->confirmingDeletion = true;
    }

    public function deleteConfirm()
    {
        $ontology = Ontology::find($this->idBeingDeleted);
        $ontology->delete();
        $this->idBeingDeleted = null;
        $this->confirmingDeletion = false;
    }

    public function render()
    {
        return view('livewire.admin.ontologies', [
            'ontologies' => Ontology::orderBy('name')->get()
        ]);
    }
}
