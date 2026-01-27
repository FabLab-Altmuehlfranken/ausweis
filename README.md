# ausweis

## Startup local docker compose environment

    docker compose build --pull
    docker compose up -d
    docker compose run --rm -u 1000 app bin/console doctrine:migrations:migrate -n

After that the app should be reachable via http://localhost:8000/
