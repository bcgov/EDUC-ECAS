# Getting Started

## Requirements
1. Have a working installation of [Docker](https://www.docker.com/)
2. Use a shell such as `bash` ( or [Git bash](https://git-scm.com/downloads) if on Windows)
3. Have the [S2I binaries](https://github.com/openshift/source-to-image/releases) on your path

## Running the project
The `manage` script in this directory contains a set of utilities that help getting up and running in Docker in no time. The command `./manage --help` can be run at any time to read the usage instructions.

Some of the images used by the project use a source-to-image build strategy and will therefore need to be build before the container can be started. Just run `./manage build` to complete this.

To run the project execute `./manage start`: the api container will be serving content at http://localhost:8080, while the frontend container will be serving at http://localhost:8081

### Development Mode
During development it is useful to have code hot-reloading enabled, so that containers won't need to be rebuilt after each change.
To start both the api and frontend container in development mode, using code hot-reloading, run `./manage start-dev`.