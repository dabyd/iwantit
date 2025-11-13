# Introduction

REST API to query products detected in videos using AI

# Welcome to the I Want It API

## Introduction

The **I Want It API** is a REST service that allows querying information about products detected in videos through an AI recognition system. This API is designed to integrate with audiovisual content analysis systems.

## Base URL

    http://uat.i-want-it.es/

For production, the URL will be provided by the technical team.

## Authentication

This API uses an authentication system based on **SHA-512 hash keys**. Each request must include the following authentication parameters:

**Authentication & Context Parameters:**

- **key**: Securely generated unique identifier using SHA-512 hash encryption for request validation
- **time**: Precise timestamp (measured in seconds and miliseconds) defining the temporal location within the project for object detection analysis
- **vid**: Unique resource identifier specifying the target project/video for processing

### Authentication example

    GET /api-iwi?action=get&time=142.2&key=b6a6cba60643cc188730bb1e80110d79...&vid=12

⚠️ **Important**: The key must be kept secure and should never be shared publicly.

## Response format

All API responses are in **JSON** format and follow this structure:

### Successful response (200)

    {
      "success": true,
      "data": {
        ...requested data...
      },
      "message": "Descriptive message"
    }

### Error response (4xx, 5xx)

    {
      "success": false,
      "message": "Error description",
      "errors": {
        ...specific details...
      }
    }

## HTTP Status Codes

| Code | Meaning | Description |
|--------|-------------|-------------|
| 200 | OK | Request processed successfully |
| 400 | Bad Request | Missing or invalid parameters |
| 401 | Unauthorized | Invalid or expired authentication key |
| 404 | Not Found | Resource not found |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server error |

## Rate Limiting

To ensure service availability, the following limits are applied:

- **60 requests per minute** per authentication key
- **1000 requests per hour** per authentication key

When the limit is exceeded, the API will return a 429 code.

## Supported HTTP Methods

The API supports the following HTTP methods:

- **GET**: For read queries

## Versioning

The API is currently in its initial version. Future versions will be indicated through URL prefixes:

- v1: /api-iwi (current)
- v2: /api/v2/iwi (future)

## Testing Environment

For testing, you can use:

- **Postman**: Import the collection from public/docs/collection.json
- **cURL**: Use the examples provided in each endpoint
- **Browser**: Access the interactive documentation at /docs

## Support

If you have problems or questions about the API:

- 📧 Email: david.herrero@i-want-it.es
- 📚 Documentation: http://uat.i-want-it.es/docs
- 🐛 Bug reports: Internal ticket system

## Changelog

### Version 1.0.0 (Current)
- ✨ Initial API release
- ✅ GET /api-iwi endpoint
- ✅ Key-based authentication system
- ✅ Support for product queries by timestamp

---

**Last update**: October 2025