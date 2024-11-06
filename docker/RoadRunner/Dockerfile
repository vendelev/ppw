FROM alpine:3.20 as builder

RUN apk add php83-dev \
    git \
    gcc \
    build-base \
    musl-dev

RUN git clone --branch master --single-branch https://github.com/tony2001/pinba_extension.git 

WORKDIR /pinba_extension

RUN phpize && ./configure && make

FROM ghcr.io/roadrunner-server/roadrunner:2024.2 AS roadrunner
FROM php:8.3-cli-alpine

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

COPY --from=builder /pinba_extension/modules/pinba.so /usr/local/lib/php/extensions/no-debug-non-zts-20230831/pinba.so

RUN echo "extension=pinba.so" > /usr/local/etc/php/conf.d/pinba.ini

CMD rr serve -c .rr.yaml