#!/bin/sh

# install our root certificates
mkcert -install

# begin generating our command
COMMAND="mkcert -cert-file dev.pem -key-file dev.key "

# generate our domain strings
for domain in $(echo $SSL_DOMAINS | sed "s/,/ /g")
do
  COMMAND="$COMMAND $domain"
done

# move to our mapped certificate directory
cd /certs

# run our command which will generate our certificates
echo "Running $COMMAND"
eval "$COMMAND"

# run nginx in daemon mode
echo "🔥 Firing up nginx..."
nginx -g 'daemon off;'