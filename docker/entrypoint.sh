#!/bin/sh

echo Running entrypoint.sh


# Change ownership of all directories and files in the mounted volume:
chown -R dev:dev /app
chown -R dev:dev /tmp/composer
# Option '-R' applies the ownerhip change recursively on files and directories in /work


#################################################################################################################
# Finally invoke what has been specified as CMD in Dockerfile or command in docker-compose:
"$@"
