FROM nginx:stable-alpine

# update dependencies
RUN apk upgrade --no-cache --update

# install mkcert
WORKDIR /root/.local/share/mkcert

# add our mkcert binary
ADD https://dl.filippo.io/mkcert/latest?for=linux/amd64 /usr/local/bin/mkcert
RUN chmod +x /usr/local/bin/mkcert