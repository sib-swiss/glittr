<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'Glittr.org');
        $this->migrator->add('general.site_description', 'Glittr.org is a curated list of bioinformatics training material.');
        $this->migrator->add(
            'general.header_text',
            'Is your (favourite) course not in there? Is a link dead? Did you find a typo?
Any contribution to this list is highly appreciated!'
        );

        $this->migrator->add(
            'general.about_text',
            '**Glittr.org** is a curated list of bioinformatics training material.
All material is:

* In a GitHub or GitLab repository
* Free to use
* Written in markdown or similar</li>

**NOTE:** This list of courses is selected only based on the above criteria.<br>There are no checks on quality.'
        );

        $this->migrator->add(
            'general.contribute_text',
            "First of all, great that you're considering to contribute. Anything that you can contribute is highly appreciated!

With this form you can request you can submit a new repository to be added to the collection or updates information for a repository.

If you just want to send us a general message, you can leave the <strong>Repository url</strong> field empty and just leave your message in the comment field.
"
        );

        $this->migrator->add(
            'general.footer_text',
            'Initiative of the the SIB Training Group.
Interested in SIB courses? [Visit our website](https://www.sib.swiss/training).'
        );

        $this->migrator->add('general.homepage_page_title', 'Glittr.org | Git repositories with educational materials for the computational life sciences');

        $this->migrator->add('general.show_repository_link', true);
        $this->migrator->add('general.repository_link', 'https://github.com/sib-swiss/glittr');
    }
};
