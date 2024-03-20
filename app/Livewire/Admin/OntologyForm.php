<?php

namespace App\Livewire\Admin;

use App\Data\OntologyData;
use App\Models\Ontology;
use App\Models\Tag;
use Livewire\Attributes\Locked;
use Livewire\Component;

class OntologyForm extends Component
{
    /**
     * Tag data array
     *
     * @var array
     */
    public $ontology = [];

    #[Locked]
    public $id;

    /**
     * add or update form action depnding on recieved id
     *
     * @var string
     */
    public $action = 'add';

    /**
     * Modal title
     *
     * @var string
     */
    public $title = '';

    /**
     * Cancel button event to emit
     *
     * @var string
     */
    public $cancelEvent = '';

    public function mount(?int $id, ?string $cancelEvent)
    {
        if ($id) {
            $ontology = Ontology::findOrFail($id);
            $this->id = $id;
            $this->ontology = OntologyData::from($ontology)->toArray();
            $this->action = 'edit';
            $this->title = 'Edit ontology';
        } else {
            $this->ontology = OntologyData::empty();
            $this->action = 'add';
            $this->title = 'Add ontology';
        }
        $this->cancelEvent = $cancelEvent;
    }

    public function save()
    {
        OntologyData::validate($this->ontology);
        $data = OntologyData::from($this->ontology)->toArray();

        if ($this->id) {
            $current = Ontology::find($this->id);
            $current->update($data);
        } else {
            $current = Ontology::create($data);
        }

        $this->dispatch(
            "{$this->action}OntologySuccess",
            ontology: $current->id,
        );
    }

    public function render()
    {
        return view('livewire.admin.ontology-form');
    }
}
