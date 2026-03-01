# ausweis

## Startup local docker compose environment

    docker compose build --pull
    docker compose up -d

After that the app should be reachable via http://localhost:8000/

Sent mails can be inspected at http://localhost:8025/

## Dependency updates

    # Update composer packages
    docker compose run --rm app composer update

    # Check for symfony flex recipe updates
    docker compose run --rm app composer recipes --outdated

    # Update symfony asset mapper importmap
    docker compose run --rm app bin/console importmap:update

## Run all the tests

    docker compose run --rm -u 1000 app composer test

## Database migrations

    # Create new migration
    docker compose run --rm app bin/console make:migration

    # Apply migrations to the database
    docker compose run --rm app bin/console doctrine:migrations:migrate -n
