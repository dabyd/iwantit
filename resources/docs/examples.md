# Ejemplos de uso de la API

## Consulta GET con autenticación

### Ejemplo básico
```bash
GET /api-iwi?action=get&time=142.2&key=b6a6cba60643cc188730bb1e80110d79e325079ba269f80d56fdaa65e0535052821f3dbcbd337cd4d8f5d9a84cf8d3182f853f3f984bdfc378dfa0df13bfe509&vid=12
```

### Con cURL
```bash
curl -X GET "http://uat.i-want-it.es/api-iwi?action=get&time=142.2&key=b6a6cba60643cc188730bb1e80110d79e325079ba269f80d56fdaa65e0535052821f3dbcbd337cd4d8f5d9a84cf8d3182f853f3f984bdfc378dfa0df13bfe509a1e77b0534e22f24fa910459139ded455874981696d9e8dfd848d5998406c426&vid=12" \
  -H "Accept: application/json"
```

### Con JavaScript (Fetch)
```javascript
fetch('http://uat.i-want-it.es/api-iwi?action=get&time=142.2&key=b6a6cba60643cc188730bb1e80110d79e325079ba269f80d56fdaa65e0535052&vid=12', {
  method: 'GET',
  headers: {
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data));
```

### Con PHP (Guzzle)
```php
use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://uat.i-want-it.es/']);

$response = $client->request('GET', '/api-iwi', [
    'query' => [
        'action' => 'get',
        'time' => 142.2,
        'key' => 'b6a6cba60643cc188730bb1e80110d79e325079ba269f80d56fdaa65e0535052',
        'vid' => 12
    ]
]);

$data = json_decode($response->getBody(), true);
```

## Parámetros

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| action | string | Sí | Acción a realizar (ej: "get") |
| time | float | Sí | Timestamp de la petición |
| key | string | Sí | Clave de autenticación SHA512 |
| vid | integer | Sí | ID del video o recurso |