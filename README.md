# API de Gestión de Inventarios (Laravel 11)

Este proyecto es una API RESTful desarrollada con **Laravel 11**, diseñada para gestionar el catálogo de productos y los movimientos de inventario de una empresa. Implementa sólidas prácticas de arquitectura y seguridad, destacando el uso de Resource Controllers, autenticación state-less, autorización basada en roles (RBAC) y prevención de *Race Conditions*.

## 🚀 Arquitectura y Tecnologías

*   **Framework:** Laravel 11 (PHP 8.2+)
*   **Seguridad:** Laravel Sanctum para autenticación API basada en tokens. Prevención de concurrencia mediante Pessimistic Locking (`lockForUpdate()`) e Integridad Transaccional de Base de Datos.
*   **Base de Datos:** Estructura modelada mediante migraciones de Eloquent, con soporte para borrado lógico (`SoftDeletes`) para salvaguardar la integridad de asientos contables.
*   **Documentación:** API documentada con OpenAPI / Swagger.
*   **Testing:** Pest/PHPUnit cubriendo pruebas unitarias y de integración end-to-end (Feature Tests).

---

## 🔐 Seguridad y Autorización

La API está protegida por defecto utilizando el middleware `auth:sanctum`. El modelo de seguridad se divide en dos capas:
1.  **Autenticación**: Mediante el endpoint `/api/login` se expide un access token state-less.
2.  **Autorización (Policies)**: El sistema incorpora `Policies` (como la `ProductPolicy`) que evalúan roles en los endpoints. La Política asegura que las funciones destructivas o de alteración sobre el catálogo estén fuertemente delimitadas sólo a perfiles administradores, previniendo accesos de usuarios de baja jerarquía (ej. bodegueros).

---

## ⚙️ Modelos Principales
El ORM Eloquent mapea las siguientes entidades clave y sus relaciones:
*   `User` & `Role` (Sistema de usuarios y jerarquía)
*   `Product`, `Category`, `Supplier` y `MeasurementUnit` (Catálogo maestro relacional)
*   `StockMovement` (Kardex: Registro de entradas y salidas referenciado y validado matemáticamente).

---

## 📡 Endpoints Principales

### Swagger Docs
Visita `http://localhost:8000/api/documentation` para consumir los esquemas detallados e interactuar con la plataforma de prueba de la API.

### Autenticación
*   `POST /api/login` - Inicio de sesión corporativo.
*   `POST /api/logout` - Revocación de token actual (Requiere Auth).

### Gestión de Catálogo y Entidades (Requieren Auth)
Mapeo REST (`GET`, `POST`, `PUT/PATCH` y `DELETE`):
*   `/api/products` (Controlado por `ProductPolicy`)
*   `/api/categories`
*   `/api/suppliers`
*   `/api/measurement-units`

### Movimientos de Stock (Reglas Especiales)
Dado el riesgo operacional, los movimientos de stock no son editables para preservar su naturaleza de auditoría contable. Los cálculos implementan DB Transactions para garantizar su fiabilidad:
*   `GET /api/stock-movements` - Lectura de historial.
*   `POST /api/stock-movements` - Asentamiento de entrada (`entry`) o despacho (`exit`). Evalúa insuficiencias de stock emitiendo códigos de error validos HTTP `422`.

---
*Desarrollado como proyecto final de Arquitectura Backend.*
