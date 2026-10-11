# Weather Service API, version 1

Every request needs the header `X-API-Key`.
Base path: `/api/v1`

The key is set in the `.env` file on the server (`API_KEY`). It is not in the
repository, so ask the service owner for the current value. A request with a
missing or wrong key is rejected before it reaches the endpoint.

Every error is returned in the same shape, with the matching HTTP status code:

```json
{
    "status": 422,
    "error": {
        "code": "invalid_limit",
        "message": "limit must be 1 to 50."
    }
}
```

In the examples below, replace `<API_KEY>` with the real key.

---

## POST /weather/refresh

What it does: Reads the current temperature for Iligan City from the Open-Meteo
weather service and saves it as a new reading. This is a POST because it saves
data. It takes no input.

Calling it repeatedly creates a new reading each time, so it is not safe to
repeat by accident.

Success response: `201 Created` with the saved reading.

```
curl.exe -i -X POST -H "X-API-Key: <API_KEY>" "http://localhost:8080/api/v1/weather/refresh"
```

```json
{
    "status": 201,
    "data": {
        "id": 6,
        "city": "Iligan City",
        "temperatureC": 28,
        "fetchedAt": "2026-10-11T05:11:10"
    }
}
```

Errors:

| Status | Code | When it happens |
| --- | --- | --- |
| 401 | `unauthorized` | The `X-API-Key` header is missing or wrong. |
| 502 | `upstream_error` | The weather source could not be reached, or it did not return a temperature. Nothing is saved. |

---

## GET /weather/logs

What it does: Returns saved readings, newest first. This is a GET because it
only reads data, so it is always safe to repeat.

Options:

| Option | Values | Default | Notes |
| --- | --- | --- | --- |
| `limit` | whole number, 1 to 50 | `10` | How many readings to return. |
| `format` | `json` or `xml` | `json` | The output format. |

Success response: `200 OK`.

JSON (`format=json`, the default):

```
curl.exe -H "X-API-Key: <API_KEY>" "http://localhost:8080/api/v1/weather/logs?limit=2"
```

```json
{
    "status": 200,
    "data": [
        {
            "id": 6,
            "city": "Iligan City",
            "temperatureC": 28,
            "fetchedAt": "2026-10-11T05:11:10"
        },
        {
            "id": 5,
            "city": "Iligan City",
            "temperatureC": 28,
            "fetchedAt": "2026-10-11T05:11:09"
        }
    ]
}
```

XML (`format=xml`):

```
curl.exe -H "X-API-Key: <API_KEY>" "http://localhost:8080/api/v1/weather/logs?limit=2&format=xml"
```

```xml
<?xml version="1.0" encoding="UTF-8"?>
<weatherReport>
  <reading>
    <id>6</id>
    <city>Iligan City</city>
    <temperatureC>28</temperatureC>
    <fetchedAt>2026-10-11T05:11:10</fetchedAt>
  </reading>
  <reading>
    <id>5</id>
    <city>Iligan City</city>
    <temperatureC>28</temperatureC>
    <fetchedAt>2026-10-11T05:11:09</fetchedAt>
  </reading>
</weatherReport>
```

The XML is checked against `app/Schemas/weather-report.xsd` before it is sent.
If it does not match, the service returns a 500 error instead of sending
invalid data.

Errors:

| Status | Code | When it happens |
| --- | --- | --- |
| 401 | `unauthorized` | The `X-API-Key` header is missing or wrong. |
| 422 | `invalid_limit` | `limit` is not a whole number from 1 to 50. |
| 422 | `invalid_format` | `format` is not `json` or `xml`. |
| 500 | `invalid_xml` | The generated XML did not match the schema. |

---

## GET /weather/logs/{id}

What it does: Returns one saved reading by its `id`. This is a GET because it
only reads data.

Success response: `200 OK` with the single reading.

```
curl.exe -H "X-API-Key: <API_KEY>" "http://localhost:8080/api/v1/weather/logs/1"
```

```json
{
    "status": 200,
    "data": {
        "id": 1,
        "city": "Iligan City",
        "temperatureC": 31.6,
        "fetchedAt": "2026-10-01T03:37:24"
    }
}
```

Errors:

| Status | Code | When it happens |
| --- | --- | --- |
| 401 | `unauthorized` | The `X-API-Key` header is missing or wrong. |
| 404 | `not_found` | No reading has that `id`. |

Note: `{id}` must be a number. A request such as
`/api/v1/weather/logs/abc` does not match any route and returns the framework's
own 404 page rather than the JSON error above.

---

## Field reference

| Field | Type | Meaning |
| --- | --- | --- |
| `id` | whole number | The reading's row id. |
| `city` | text | The city the reading was taken for. |
| `temperatureC` | number | The temperature in degrees Celsius. |
| `fetchedAt` | text | When the reading was saved, in UTC (`YYYY-MM-DDTHH:MM:SS`). This is 8 hours behind Philippine time. |
