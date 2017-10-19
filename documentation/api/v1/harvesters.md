# Harvesters

## Get harvesters

`GET /api/v1/harvesters.json(?limit=20)` will return all harvesters you can request information about. See documentation of "Get a harvester" for details on how to query for a specific harvester record.

### Parameters

Name  | Type    | Description
----- | ------- | -----------
limit | integer | The number of harvesters to list per page/response.
page  | integer | Return the specific page from the paginated result set.

### Request

```
$ curl http://teresah.dariah.eu/api/v1/harvesters.json?limit=1
```

### Response

```json
{
  "status": {
    "code": 200
  },
  "harvesters": {
    "total": 1,
    "per_page": 2,
    "current_page": 1,
    "last_page": 1,
    "from": 1,
    "to": 1,
    "data": [
      {
        "id": 1,
        "data_source_id": 89,
        "label": "TERESAH-HARVESTER-TEST",
        "slug": "teresah-harvester-test",
        "url": "https://teresah.dariah.eu/harvester_page.html",
        "active": 1,
        "launch_now": 0,
        "last_launched": "2017-10-19 09:35:02",
        "user_id": 22,
        "created_at": "2017-10-19T07:37:38+00:00",
        "updated_at": "2017-10-19T09:35:02+00:00",
        "user": {
          "id": 22,
          "name": "Yoann Supervisor",
          "locale": "en",
          "password_reset_sent_at": null,
          "created_at": "2017-10-19T08:52:33+00:00",
          "updated_at": "2017-10-19T09:38:31+00:00",
          "deleted_at": null
        },
        "data_source": {
          "id": 89,
          "name": "TERESAH-HARVESTER-DATASOURCE",
          "slug": "teresah-harvester-datasource",
          "description": "The TERESAH harvester data source is used for testing the harvester implementation of TERESAH",
          "homepage": "http://teresah.dariah.eu",
          "user_id": 21,
          "created_at": "2017-10-19T07:37:09+00:00",
          "updated_at": "2017-10-19T07:37:09+00:00",
          "deleted_at": null,
          "user": {
            "id": 21,
            "name": "TERESAH Admin",
            "locale": "en",
            "created_at": "2017-10-10T09:24:31+00:00",
            "updated_at": "2017-10-10T09:24:31+00:00",
            "deleted_at": null
          }
        }
      }
    ]
  }
}
```


## Get a harvester record

`GET /api/v1/harvesters/{id}.json` will return specific harvester record you can request information about.

### Request

```
$ curl http://teresah.dariah.eu/api/v1/harvesters/1.json
```

### Response

```json
{
  "status": {
    "code": 200
  },
  "id": 1,
  "data_source_id": 89,
  "label": "TERESAH-HARVESTER-TEST",
  "slug": "teresah-harvester-test",
  "url": "https://teresah.dariah.eu/harvester_page.html",
  "active": 1,
  "launch_now": 0,
  "last_launched": "2017-10-19 09:35:02",
  "user_id": 22,
  "created_at": "2017-10-19T07:37:38+00:00",
  "updated_at": "2017-10-19T09:35:02+00:00",
  "user": {
    "id": 22,
    "name": "Yoann Supervisor",
    "locale": "en",
    "password_reset_sent_at": null,
    "created_at": "2017-10-19T08:52:33+00:00",
    "updated_at": "2017-10-19T09:38:31+00:00",
    "deleted_at": null
  },
  "data_source": {
    "id": 89,
    "name": "TERESAH-HARVESTER-DATASOURCE",
    "slug": "teresah-harvester-datasource",
    "description": "The TERESAH harvester data source is used for testing the harvester implementation of TERESAH",
    "homepage": "http://teresah.dariah.eu",
    "user_id": 21,
    "created_at": "2017-10-19T07:37:09+00:00",
    "updated_at": "2017-10-19T07:37:09+00:00",
    "deleted_at": null,
    "user": {
      "id": 21,
      "name": "TERESAH Admin",
      "locale": "en",
      "created_at": "2017-10-10T09:24:31+00:00",
      "updated_at": "2017-10-10T09:24:31+00:00",
      "deleted_at": null
    }
  }
}
```
