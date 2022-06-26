# Flight Route Search (Docker Image)

>Store and search for flight route with fewest connecting flights from an airport to another airport.

## Requirements
  1) Select a reasonable system architecture to handle such a system in production
  2) Dockerize so it can be easily run with `docker compose up`
  3) Get request that handles the query string `?from=<iata code>&to=<iata code>`
  4) Response should be a json in the form below
  5) Production quality code as much as possible

## Example Request
GET http://localhost:3000?from=TPE&to=SFO

## Example Responses

```
single direct flight
[{ "id": "<flight id>", "from": "<iata code>", "to": "<iata code>"}]

multi-leg flight
[{ "id": "<flight id>", "from": "<iata code>", "to": "<iata code>"} ...]

no flight path that reaches
[]
```
