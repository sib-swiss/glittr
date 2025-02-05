@setup
require __DIR__.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$server_dev = $_ENV['DEPLOY_DEV_HOST'] ?? null;
$server_prod = $_ENV['DEPLOY_PROD_HOST'] ?? null;

$deploy_path_dev = $_ENV['DEPLOY_DEV_PATH'] ?? null;
$deploy_path_prod = $_ENV['DEPLOY_PROD_PATH'] ?? null;

$app_dir = $server === 'prod' ?  $deploy_path_prod :  $deploy_path_dev ;

$server = isset($server) ? $server : 'dev' ;

@endsetup


@servers(['dev' => $server_dev, 'prod' => $server_prod])

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
