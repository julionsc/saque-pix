# SaquePix

Running the service:

```
make dev
make migrate
```

Making a request:
```sh
curl --request POST \
  --url http://localhost:9501/account/balance/withdraw/544fed37-e292-4fd9-abea-ab27d4bd42ad \
  --header 'content-type: application/json' \
  --data '{
  "method": "PIX",
  "pix": {
    "type": "email",
    "key": "fulano@email.com"
  },
  "amount": 7.02,
  "schedule": null
}'
```