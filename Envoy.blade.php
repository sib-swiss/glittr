@setup
    require __DIR__.'/vendor/autoload.php';
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

    $server = isset($server) ? $server : 'dev' ;

    $app_dir = $server == 'dev' ? '/var/vhosts/vital-it.ch/training-collection-dev/htdocs' : '/var/vhosts/vital-it.ch/training-collection-prod/htdocs';
@endsetup


@servers(['dev' => 'webdev@training-collection-dev.vital-it.ch', 'prod' => 'webdev@training-collection-prod.vital-it.ch', 'localhost' => '127.0.0.1'])

@story('deploy', ['on' => $server])
    pull_repository
    run_composer
    migrate_database
    restart_queue_workers
@endstory

@task('pull_repository')
    echo "Pulling Repository"
    cd {{ $app_dir }}
    git fetch
    git pull
@endtask

@task('run_composer')
    echo "Running composer"
    cd {{ $app_dir }}
    composer install
@endtask

@task('migrate_database')
    echo "Migrating database"
    cd {{ $app_dir }}
    php artisan migrate --force
@endtask

@task('restart_queue_workers')
    echo "Restart Queue Workers"
    cd {{ $app_dir }}
    php artisan queue:restart
@endtask
