<?php

namespace App\Console\Commands;

use App\Models\Ontology;
use App\Models\Tag;
use Illuminate\Console\Command;
use League\Csv\Reader;

class ImportOntology extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-ontology';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import tags ontology from csv';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->comment('Start importing.');
        $this->newLine();

        $reader = Reader::createFromPath(database_path('import/topic_ontologies.csv', 'r'));
        foreach ($reader->getRecords() as $offset => $topic) {
            if ($offset > 0 && count($topic) > 5 && $topic[1] !== '') {
                $name = $topic[1];
                $ontology = $topic[2] ?? '';
                $ontologyClass = $topic[3] ?? '';
                $link = $topic[5] ?? '';
                $description = $topic[6] ?? '';
                if ($ontology != '') {
                    $tag = Tag::firstWhere('name', $name);
                    $ontology = Ontology::firstOrCreate([
                        'name' => $ontology,
                    ]);
                    if ($tag) {
                        $this->info('Updating tag '.$name);
                        $tag->update([
                            'ontology_id' => $ontology->id,
                            'ontology_class' => $ontologyClass,
                            'link' => $link,
                            'description' => $description,
                        ]);
                    } else {
                        $this->warn('Tag not found '.$name);
                    }
                }
            }
        }

        $this->newLine();
        $this->comment('Finished importing.');
    }
}
