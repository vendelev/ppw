FROM nginx:1.27.2 as builder

RUN apt update && apt install -y \
    cmake \
    build-essential \
    libssl-dev \
    zlib1g-dev \
    libpcre3-dev \
    pkg-config \
    libc-ares-dev \
    libre2-dev \
    git

WORKDIR /

RUN git clone --branch release-1.27.2 https://github.com/nginx/nginx.git
RUN git clone https://github.com/nginxinc/nginx-otel.git

WORKDIR /nginx
RUN auto/configure --with-compat
RUN mkdir /nginx-otel/build
WORKDIR /nginx-otel/build
RUN cmake -DNGX_OTEL_NGINX_BUILD_DIR=/nginx/objs ..
RUN make


FROM nginx:1.27.2

RUN apt update && apt install -y \
    libc-ares-dev 

COPY --from=builder /nginx-otel/build/ngx_otel_module.so /etc/nginx/modules/
RUN echo "load_module modules/ngx_otel_module.so; $(cat /etc/nginx/nginx.conf)" > /etc/nginx/nginx.conf
