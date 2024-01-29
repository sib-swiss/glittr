# Glittr

[Glitter](https://glittr.org/) is a web app to find bioinformatics training materials on GitHub and GitLab based on Laravel.

## Requirement

- PHP 8.1+
- MySQL or Postgres database
- Redis
- Github PAT (Personal access token)
- Gitlab Access Token

## Installation

Copy `.env.example` to `.env` and set your access tokens and server configuration.

### Local

You can run in local using [DDEV](https://ddev.readthedocs.io/en/stable/).

```bash
ddev start
ddev composer install
ddev artisan migrate:fresh --seed
ddev artisan repo:import
ddev npm install
ddev npm run dev
```

To have repositories updated when added/modified you need to run the queue worker.

```bash
ddev artisan queue:work
```

### Production

```bash
composer install --no-dev -o
npm install
npm run build
artisan migrate:fresh --seed
artisan repo:import
```

See Laravel documentation for [jobs task runner configuration](https://laravel.com/docs/9.x/queues#running-the-queue-worker) and [cron job](https://laravel.com/docs/9.x/scheduling#running-the-scheduler).

## API

The API endpoint is available at `/api/repositories`. It is using [Spatie Query Builder](https://spatie.be/docs/laravel-query-builder/v5/introduction) to filter the data with the following parameters:

### Filters

Example of filters usage

```plaintext
// Search repositories with author name containing "sib" and license containing "cc"
GET /api/repositories?filter[author.name]=sib&filter[license]=cc

// Search respositories taged with "r" or "machine learning"
GET /api/repositories?filter[tags.name]=r,machine%20learning
```

| Fiter               | Exact match | Description                              |
| ------------------- | ----------- | ---------------------------------------- |
| name                | No          | Repository name                          |
| licence             | No          | Repository license                       |
| description         | No          | Repository description                   |
| author.name         | Yes         | Author Github/Gitlab username            |
| author.display_name | No          | Author Github/Gitlab display name        |
| tags.name           | Yes         | Repository tags                          |
| tags.category.name  | No          | Repository with a tag from this category |

To retrieve the list of tags and categories you can use the `/api/tags` endpoint which retunr the list of tags grouped by category with repositories count.

#### Sorting

Default sorting is by number of stars in descending order. You can change the sorting direction by adding `-` before the sort field.

```plaintext
// Sorting by most recently updated (last push)
GET /api/repositories?sort=-last_push
```

| Sort        | Description                   |
| ----------- | ----------------------------- |
| name        | Repository name               |
| stargazers  | Number of stars               |
| last_push   | Last push                     |
| author.name | Author Github/Gitlab username |

#### Pagination

By default, the API will return all results. You can retrieve paginated results by adding the `page` parameter. It will use [spatie/laravel-json-api-paginate package](https://github.com/spatie/laravel-json-api-paginate) which follow the [JSON API spcec](https://jsonapi.org/profiles/ethanresnick/cursor-pagination) (max results per page is set to **100**).

```plaintext
// Page 2 with 10 results per page
GET /api/repositories?page[size]=10&page[number]=2
```

### Repositories List

There is an additional endpoint `/api/list` which returns the list of repositories grouped by their main category, as presented on the [sib-swiss/training-collection repository](https://github.com/sib-swiss/training-collection).


## License

Glittr is licensed under The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
