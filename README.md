# SIB - Training Collection App

Laravel application to manage SIB Training Collection related repositories

## Requirement

- PHP 8.1+
- MySQL or Postgres database
- Redis
- Github PAT (Personal access token)
- Gitlab Access Token

## Installation

### Local

```
sail up
sail composer install
sail artisan migrate:fresh --seed
sail artisan repo:import
sail npm install
sain npm run dev
```

### Production

```
composer install --no-dev -o
npm install
npm run build
artisan migrate:fresh --seed
artisan repo:import
```
