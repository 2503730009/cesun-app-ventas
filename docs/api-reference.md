# API Reference (App Ventas)

Documento de referencia basado en rutas reales del proyecto (`routes/api.php`) y controladores asociados. Formato de respuestas consistente con el proyecto: `ok`, `data`, `meta`, `message`.

## Convenciones generales

**Base URL**
```
http://localhost/api
```

**Headers comunes**
- `Accept: application/json`
- `Content-Type: application/json` (solo para POST/PUT/PATCH)
- `Authorization: Bearer <token>` (solo endpoints con JWT)

**Paginación (listados)**
- `page` (opcional, por defecto 1)
- `per_page` (opcional, default 10, máximo 100; si es inválido o <= 0, se usa 10)

---

# Auth

## POST /api/auth/login
- **JWT:** No
- **Descripción:** Inicia sesión y devuelve token JWT.
- **Headers:** `Accept`, `Content-Type`
- **Body (JSON)**
```json
{
  "email": "admin@demo.test",
  "password": "secret123"
}
```
- **Respuesta 200 (JSON)**
```json
{
  "ok": true,
  "access_token": "<jwt>",
  "token_type": "bearer",
  "expires_in": 3600
}
```
- **Errores comunes**
  - **401**
```json
{
  "ok": false,
  "message": "Credenciales inválidas."
}
```
  - **422** (validación)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

## GET /api/auth/me
- **JWT:** Sí
- **Descripción:** Retorna el usuario autenticado.
- **Headers:** `Accept`, `Authorization`
- **Respuesta 200 (JSON)**
```json
{
  "ok": true,
  "data": {
    "id": 1,
    "name": "Admin",
    "email": "admin@demo.test"
  }
}
```
- **Errores comunes**
  - **401**
```json
{
  "message": "Unauthenticated."
}
```

## POST /api/auth/logout
- **JWT:** Sí
- **Descripción:** Cierra la sesión (invalida el token actual).
- **Headers:** `Accept`, `Authorization`
- **Respuesta 200 (JSON)**
```json
{
  "ok": true,
  "message": "Sesión cerrada."
}
```
- **Errores comunes**
  - **401**
```json
{
  "message": "Unauthenticated."
}
```

## POST /api/auth/refresh
- **JWT:** Sí
- **Descripción:** Renueva el token JWT.
- **Headers:** `Accept`, `Authorization`
- **Respuesta 200 (JSON)**
```json
{
  "ok": true,
  "access_token": "<jwt>",
  "token_type": "bearer",
  "expires_in": 3600
}
```
- **Errores comunes**
  - **401**
```json
{
  "ok": false,
  "message": "Token inválido o expirado."
}
```

---

# Vendors

## GET /api/vendors
- **JWT:** No
- **Descripción:** Lista de proveedores (paginada).
- **Headers:** `Accept`
- **Query params:** `page`, `per_page`
- **Respuesta 200 (JSON)**
```json
{
  "ok": true,
  "data": [
    {
      "id": 1,
      "name": "Proveedor Uno",
      "email": "ventas@proveedor1.com",
      "phone": "+52 555 0101",
      "created_at": "2026-02-01T12:30:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 10,
    "total": 25
  }
}
```

## POST /api/vendors
- **JWT:** No
- **Descripción:** Crea un proveedor.
- **Headers:** `Accept`, `Content-Type`
- **Body (JSON)**
```json
{
  "name": "Proveedor Uno",
  "email": "ventas@proveedor1.com",
  "phone": "+52 555 0101"
}
```
- **Respuesta 201 (JSON)**
```json
{
  "ok": true,
  "data": {
    "id": 1,
    "name": "Proveedor Uno",
    "email": "ventas@proveedor1.com",
    "phone": "+52 555 0101",
    "created_at": "2026-02-01T12:30:00Z"
  }
}
```
- **Errores comunes**
  - **422** (validación)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["The name field is required."]
  }
}
```

## GET /api/vendors/{vendor}
- **JWT:** No
- **Descripción:** Detalle de proveedor.
- **Headers:** `Accept`
- **Path params:** `vendor` (id)
- **Respuesta 200 (JSON)**
```json
{
  "ok": true,
  "data": {
    "id": 1,
    "name": "Proveedor Uno",
    "email": "ventas@proveedor1.com",
    "phone": "+52 555 0101",
    "created_at": "2026-02-01T12:30:00Z"
  }
}
```
- **Errores comunes**
  - **404**
```json
{
  "message": "No query results for model [App\\Models\\Vendor] 999"
}
```

## PUT/PATCH /api/vendors/{vendor}
- **JWT:** No
- **Descripción:** Actualiza un proveedor.
- **Headers:** `Accept`, `Content-Type`
- **Path params:** `vendor` (id)
- **Body (JSON)**
```json
{
  "name": "Proveedor Uno SA",
  "phone": "+52 555 0102"
}
```
- **Respuesta 200 (JSON)**
```json
{
  "ok": true,
  "data": {
    "id": 1,
    "name": "Proveedor Uno SA",
    "email": "ventas@proveedor1.com",
    "phone": "+52 555 0102",
    "created_at": "2026-02-01T12:30:00Z"
  }
}
```
- **Errores comunes**
  - **404**
  - **422**

## DELETE /api/vendors/{vendor}
- **JWT:** No
- **Descripción:** Elimina un proveedor.
- **Headers:** `Accept`
- **Path params:** `vendor` (id)
- **Respuesta 200 (JSON)**
```json
{
  "ok": true,
  "message": "Vendor deleted"
}
```
- **Errores comunes**
  - **404**

---

# Products

## GET /api/products
- **JWT:** No
- **Descripción:** Lista de productos (paginada) con vendor asociado.
- **Headers:** `Accept`
- **Query params:** `page`, `per_page`
- **Respuesta 200 (JSON)**
```json
{
  "ok": true,
  "data": [
    {
      "id": 10,
      "vendor_id": 1,
      "name": "Teclado Mecánico",
      "description": "Switches azules",
      "price": "120.00",
      "stock": 15,
      "vendor": {
        "id": 1,
        "name": "Proveedor Uno",
        "email": "ventas@proveedor1.com",
        "phone": "+52 555 0101",
        "created_at": "2026-02-01T12:30:00Z"
      },
      "created_at": "2026-02-03T10:15:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 10,
    "total": 12
  }
}
```

## POST /api/products
- **JWT:** No
- **Descripción:** Crea un producto.
- **Headers:** `Accept`, `Content-Type`
- **Body (JSON)**
```json
{
  "vendor_id": 1,
  "name": "Teclado Mecánico",
  "description": "Switches azules",
  "price": 120.00,
  "stock": 15
}
```
- **Respuesta 201 (JSON)**
```json
{
  "ok": true,
  "data": {
    "id": 10,
    "vendor_id": 1,
    "name": "Teclado Mecánico",
    "description": "Switches azules",
    "price": "120.00",
    "stock": 15,
    "vendor": {
      "id": 1,
      "name": "Proveedor Uno",
      "email": "ventas@proveedor1.com",
      "phone": "+52 555 0101",
      "created_at": "2026-02-01T12:30:00Z"
    },
    "created_at": "2026-02-03T10:15:00Z"
  }
}
```
- **Errores comunes**
  - **422**

## GET /api/products/{product}
- **JWT:** No
- **Descripción:** Detalle de producto con vendor asociado.
- **Headers:** `Accept`
- **Path params:** `product` (id)
- **Respuesta 200 (JSON)**
```json
{
  "ok": true,
  "data": {
    "id": 10,
    "vendor_id": 1,
    "name": "Teclado Mecánico",
    "description": "Switches azules",
    "price": "120.00",
    "stock": 15,
    "vendor": {
      "id": 1,
      "name": "Proveedor Uno",
      "email": "ventas@proveedor1.com",
      "phone": "+52 555 0101",
      "created_at": "2026-02-01T12:30:00Z"
    },
    "created_at": "2026-02-03T10:15:00Z"
  }
}
```
- **Errores comunes**
  - **404**

## PUT/PATCH /api/products/{product}
- **JWT:** No
- **Descripción:** Actualiza un producto.
- **Headers:** `Accept`, `Content-Type`
- **Path params:** `product` (id)
- **Body (JSON)**
```json
{
  "price": 115.00,
  "stock": 20
}
```
- **Respuesta 200 (JSON)**
```json
{
  "ok": true,
  "data": {
    "id": 10,
    "vendor_id": 1,
    "name": "Teclado Mecánico",
    "description": "Switches azules",
    "price": "115.00",
    "stock": 20,
    "vendor": {
      "id": 1,
      "name": "Proveedor Uno",
      "email": "ventas@proveedor1.com",
      "phone": "+52 555 0101",
      "created_at": "2026-02-01T12:30:00Z"
    },
    "created_at": "2026-02-03T10:15:00Z"
  }
}
```
- **Errores comunes**
  - **404**
  - **422**

## DELETE /api/products/{product}
- **JWT:** No
- **Descripción:** Elimina un producto.
- **Headers:** `Accept`
- **Path params:** `product` (id)
- **Respuesta 200 (JSON)**
```json
{
  "ok": true,
  "message": "Product deleted"
}
```
- **Errores comunes**
  - **404**

---

# Inventory

## GET /api/inventory
- **JWT:** No
- **Descripción:** Lista de inventario (paginada) con stock y precio.
- **Headers:** `Accept`
- **Query params:** `page`, `per_page`
- **Respuesta 200 (JSON)**
```json
{
  "ok": true,
  "data": [
    {
      "id": 10,
      "name": "Teclado Mecánico",
      "stock": 15,
      "price": "120.00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 10,
    "total": 12
  }
}
```

## GET /api/inventory/{product}
- **JWT:** No
- **Descripción:** Detalle de inventario por producto.
- **Headers:** `Accept`
- **Path params:** `product` (id)
- **Respuesta 200 (JSON)**
```json
{
  "ok": true,
  "data": {
    "id": 10,
    "name": "Teclado Mecánico",
    "stock": 15,
    "price": "120.00"
  }
}
```
- **Errores comunes**
  - **404**

## PATCH /api/inventory/{product}
- **JWT:** Sí
- **Descripción:** Actualiza el stock de un producto.
- **Headers:** `Accept`, `Content-Type`, `Authorization`
- **Path params:** `product` (id)
- **Body (JSON)**
```json
{
  "stock": 30
}
```
- **Respuesta 200 (JSON)**
```json
{
  "ok": true,
  "data": {
    "id": 10,
    "name": "Teclado Mecánico",
    "stock": 30,
    "price": "120.00"
  }
}
```
- **Errores comunes**
  - **401**
```json
{
  "message": "Unauthenticated."
}
```
  - **404**
  - **422**

---

# Orders

## GET /api/orders
- **JWT:** No
- **Descripción:** Lista de pedidos (paginada) con vendor e items.
- **Headers:** `Accept`
- **Query params:** `page`, `per_page`
- **Respuesta 200 (JSON)**
```json
{
  "ok": true,
  "data": [
    {
      "id": 100,
      "vendor": {
        "id": 1,
        "name": "Proveedor Uno",
        "email": "ventas@proveedor1.com",
        "phone": "+52 555 0101"
      },
      "status": "pending",
      "total": "240.00",
      "customer_name": "Juan Pérez",
      "customer_phone": "+52 555 0202",
      "items": [
        {
          "id": 500,
          "product_id": 10,
          "product": {
            "id": 10,
            "name": "Teclado Mecánico",
            "price": "120.00",
            "stock": 15
          },
          "quantity": 2,
          "unit_price": "120.00",
          "subtotal": "240.00"
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 1
  }
}
```

## GET /api/orders/{order}
- **JWT:** No
- **Descripción:** Detalle de pedido.
- **Headers:** `Accept`
- **Path params:** `order` (id)
- **Respuesta 200 (JSON)**
```json
{
  "ok": true,
  "data": {
    "id": 100,
    "vendor": {
      "id": 1,
      "name": "Proveedor Uno",
      "email": "ventas@proveedor1.com",
      "phone": "+52 555 0101"
    },
    "status": "pending",
    "total": "240.00",
    "customer_name": "Juan Pérez",
    "customer_phone": "+52 555 0202",
    "items": [
      {
        "id": 500,
        "product_id": 10,
        "product": {
          "id": 10,
          "name": "Teclado Mecánico",
          "price": "120.00",
          "stock": 15
        },
        "quantity": 2,
        "unit_price": "120.00",
        "subtotal": "240.00"
      }
    ]
  }
}
```
- **Errores comunes**
  - **404**

## POST /api/orders
- **JWT:** Sí
- **Descripción:** Crea un pedido y descuenta stock.
- **Headers:** `Accept`, `Content-Type`, `Authorization`
- **Body (JSON)**
```json
{
  "vendor_id": 1,
  "customer_name": "Juan Pérez",
  "customer_phone": "+52 555 0202",
  "items": [
    { "product_id": 10, "quantity": 2 },
    { "product_id": 11, "quantity": 1 }
  ]
}
```
- **Respuesta 201 (JSON)**
```json
{
  "ok": true,
  "data": {
    "id": 101,
    "vendor": {
      "id": 1,
      "name": "Proveedor Uno",
      "email": "ventas@proveedor1.com",
      "phone": "+52 555 0101"
    },
    "status": "pending",
    "total": "360.00",
    "customer_name": "Juan Pérez",
    "customer_phone": "+52 555 0202",
    "items": [
      {
        "id": 501,
        "product_id": 10,
        "product": {
          "id": 10,
          "name": "Teclado Mecánico",
          "price": "120.00",
          "stock": 13
        },
        "quantity": 2,
        "unit_price": "120.00",
        "subtotal": "240.00"
      },
      {
        "id": 502,
        "product_id": 11,
        "product": {
          "id": 11,
          "name": "Mouse Óptico",
          "price": "120.00",
          "stock": 9
        },
        "quantity": 1,
        "unit_price": "120.00",
        "subtotal": "120.00"
      }
    ]
  }
}
```
- **Errores comunes**
  - **401**
```json
{
  "message": "Unauthenticated."
}
```
  - **422**
```json
{
  "ok": false,
  "message": "Stock insuficiente para el producto ID 10."
}
```

---

# Quick Start con Postman

1) **Login y obtener token**
   - `POST /api/auth/login`
   - Guarda `access_token`.

2) **Configurar Bearer token**
   - En Postman: Authorization > Type: Bearer Token
   - Token: `access_token` del login.

3) **Secuencia recomendada de pruebas**
   - Vendors: crear y listar
   - Products: crear, listar y ver detalle
   - Inventory: listar y ajustar stock (PATCH requiere token)
   - Orders: crear pedido (requiere token) y consultar listado/detalle
