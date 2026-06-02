## DDEV environment

The project is set up to run in a [DDEV](https://ddev.com/) environment.
All commands below should be prefixed with `ddev exec` so they are run from within the DDEV container.

artisan commands should be prefixed with `ddev artisan`.
Composer commands should be prefixed with `ddev composer`.
PHP commands should be prefixed with `ddev php`.
Other commands should be prefixed with `ddev exec`.

Exception is made for NPM and Node commands which should be run from the host machine.
