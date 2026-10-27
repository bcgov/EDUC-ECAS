# Getting Started

## Requirements
1. Have a working installation of [Docker](https://www.docker.com/)
2. Use a shell such as `bash` ( or [Git bash](https://git-scm.com/downloads) if on Windows)
3. Have the [S2I binaries](https://github.com/openshift/source-to-image/releases) on your path

## Running the project
The `manage` script in this directory contains a set of utilities that help getting up and running in Docker in no time. The command `./manage --help` can be run at any time to read the usage instructions.

Some of the images used by the project use a source-to-image build strategy and will therefore need to be build before the container can be started. Just run `./manage build` to complete this.

To run the project execute `./manage start`: the api container will be serving content at http://localhost:8080, while the frontend container will be serving at http://localhost:8081

### Running the frontend locally against dev
[docker-compose.frontend.yml](docker-compose.frontend.yml) runs just the frontend (`web-app`) from [ecas-frontend/Dockerfile](ecas-frontend/Dockerfile), separately from the S2I setup above: no S2I or Red Hat registry login needed. It mirrors the OpenShift image: Apache + PHP 8.3 on port 8080, assets built with `npm run production`, and the same PHP limits as the CVE stopgap ConfigMap.

1. `cp .env.example .env` in this directory, then fill it in from the `ecas-frontend` and `ecas-api-credentials` secrets in the dev namespace.
2. Port-forward the dev ecas-api, which `DYNAMICSBASEURL` points at by default: `oc -n <namespace>-dev port-forward svc/ecas-api 8090:8080`
3. From the repository root: `docker compose -f docker/docker-compose.frontend.yml up --build`
4. Open http://localhost:7070. Laravel logs go to the container output.

Build arguments:
- `PHP_VERSION` (default `8.3`) should match the PHP release of the S2I builder image.
- `COMPOSER_NO_DEV=0` installs dev packages so `./vendor/bin/phpunit` can run inside the container. Leave `APP_ENV` at its default unless you need Dusk: `local` and `testing` register the Dusk service provider, which only exists in this build.

Signing in requires the Keycloak client to accept `http://localhost:7070/*` as a redirect URI.

### Development Mode
During development it is useful to have code hot-reloading enabled, so that containers won't need to be rebuilt after each change.
To start both the api and frontend container in development mode, using code hot-reloading, run `./manage start-dev`.