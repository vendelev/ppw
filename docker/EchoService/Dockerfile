FROM golang:alpine3.20

RUN apk update && apk add git
RUN git clone https://github.com/devhands-io/bootcamp-samples.git

WORKDIR /go/bootcamp-samples/golang/vanilla/

RUN go build -o srv main.go

CMD ./srv --port=8088 --host=0.0.0.0