# Bienvenido a la API I Want It

## Introducción

La **API I Want It** es un servicio REST que permite consultar información sobre productos detectados en videos mediante un sistema de reconocimiento por IA. Esta API está diseñada para integrarse con sistemas de análisis de contenido audiovisual.

## URL Base

    http://uat.i-want-it.es/

Para producción, la URL será proporcionada por el equipo técnico.

## Autenticación

Esta API utiliza un sistema de autenticación basado en **claves hash SHA-512**. Cada petición debe incluir los siguientes parámetros de autenticación:

- **key**: Clave de autenticación única generada mediante hash SHA-512
- **time**: Timestamp que indica el momento de la petición
- **vid**: Identificador único del video o recurso

### Ejemplo de autenticación

    GET /api-iwi?action=get&time=142.2&key=b6a6cba60643cc188730bb1e80110d79...&vid=12

⚠️ **Importante**: La clave (key) debe mantenerse segura y nunca debe compartirse públicamente.

## Formato de respuesta

Todas las respuestas de la API están en formato **JSON** y siguen esta estructura:

### Respuesta exitosa (200)

    {
      "success": true,
      "data": {
        ...datos solicitados...
      },
      "message": "Mensaje descriptivo"
    }

### Respuesta de error (4xx, 5xx)

    {
      "success": false,
      "message": "Descripción del error",
      "errors": {
        ...detalles específicos...
      }
    }

## Códigos de estado HTTP

| Código | Significado | Descripción |
|--------|-------------|-------------|
| 200 | OK | La petición se procesó correctamente |
| 400 | Bad Request | Faltan parámetros o son inválidos |
| 401 | Unauthorized | Clave de autenticación inválida o expirada |
| 404 | Not Found | Recurso no encontrado |
| 429 | Too Many Requests | Límite de peticiones excedido |
| 500 | Internal Server Error | Error en el servidor |

## Rate Limiting

Para garantizar la disponibilidad del servicio, se aplican los siguientes límites:

- **60 peticiones por minuto** por clave de autenticación
- **1000 peticiones por hora** por clave de autenticación

Cuando se excede el límite, la API devolverá un código 429.

## Métodos HTTP soportados

La API soporta los siguientes métodos HTTP:

- **GET**: Para consultas de lectura
- **POST**: Para consultas con parámetros en el body

## Versionado

Actualmente la API está en su versión inicial. Futuras versiones se indicarán mediante prefijos en la URL:

- v1: /api-iwi (actual)
- v2: /api/v2/iwi (futura)

## Entorno de pruebas

Para realizar pruebas, puedes usar:

- **Postman**: Importa la colección desde public/docs/collection.json
- **cURL**: Usa los ejemplos proporcionados en cada endpoint
- **Navegador**: Accede a la documentación interactiva en /docs

## Soporte

Si tienes problemas o preguntas sobre la API:

- 📧 Email: david.herrero@i-want-it.es
- 📚 Documentación: http://uat.i-want-it.es/docs
- 🐛 Reportar bugs: Sistema de tickets interno

## Changelog

### Versión 1.0.0 (Actual)
- ✨ Lanzamiento inicial de la API
- ✅ Endpoint GET /api-iwi
- ✅ Endpoint POST /api-iwi
- ✅ Sistema de autenticación por key
- ✅ Soporte para consulta de productos por timestamp

---

**Última actualización**: Octubre 2025