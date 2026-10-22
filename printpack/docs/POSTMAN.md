# Postman / manual smoke tests
This application currently exposes server-rendered form routes rather than a JSON REST API. Use Postman with cookie persistence for session-based tests; include `_csrf` from the form when sending POST requests.

| Method | Path | Purpose |
|---|---|---|
| GET | `/printpack/public/` | Home page |
| GET | `/printpack/public/services?q=card` | Search catalogue |
| GET/POST | `/printpack/public/register` | Registration |
| GET/POST | `/printpack/public/login` | Login |
| GET | `/printpack/public/cart` | Cart |
| POST | `/printpack/public/checkout` | Create vendor-specific orders (COD) |
| GET | `/printpack/public/my-orders` | Customer order history |
| GET | `/printpack/public/vendor` | Vendor workspace |
| GET | `/printpack/public/admin` | Admin dashboard |

For a future REST API, add API routes with token authentication, request/response schemas, and automated contract tests rather than reusing browser-session routes blindly.
