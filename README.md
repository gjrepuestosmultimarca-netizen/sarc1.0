# 🗺️ SARC — Sistema Avanzado de Rutas Comerciales

<div align="center">

![Version](https://img.shields.io/badge/versión-2.1.0-4F46E5?style=for-the-badge)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Licencia](https://img.shields.io/badge/licencia-MIT-10B981?style=for-the-badge)
![PWA](https://img.shields.io/badge/PWA-✓-06B6D4?style=for-the-badge)

**Sistema web integral para la gestión de asesores comerciales, clientes, rutas de visita, visitas GPS y medición de satisfacción — PWA con Bootstrap 5 y soporte completo para móvil.**

[Instalación](#-instalación) · [API REST](#-api-rest-completa) · [Base de Datos](#-base-de-datos) · [Roles](#-control-de-acceso-por-rol)

</div>

---

## ✨ Novedades en v2.1

| Característica | v2.0 | v2.1 |
|---|:---:|:---:|
| Módulo Visitas GPS | ❌ | ✅ |
| API `DELETE /satisfacciones` | ❌ | ✅ |
| Validación unicidad usuario/email | ❌ | ✅ |
| Validación rango calificación (1–5) | ❌ | ✅ |
| Verificación existencia antes de DELETE | ❌ | ✅ |
| Visitas exitosas de hoy en dashboard | ❌ | ✅ |
| Documentación PHPDoc en todos los endpoints | ❌ | ✅ |

---

## 📋 Características

### 🔐 Autenticación
- Login con validación y mensajes de error claros
- Registro de cuenta con rol seleccionable
- Recuperación de contraseña por email
- "Recordarme" persiste la sesión
- Control de acceso por roles: `admin`, `supervisor`, `asesor`

### 🗺️ Gestión de Rutas
- Registro con asesor, fecha, hora, zona, estado, vehículo y kilómetros
- Selección múltiple de clientes a visitar
- Filtros por asesor, estado y fecha
- Exportar a Excel

### 📍 Visitas GPS *(nuevo en v2.1)*
- Registro de check-in/check-out por cliente en cada ruta
- Coordenadas GPS de entrada y salida (7 decimales)
- Duración calculada en minutos
- Resultado: `exitosa`, `sin_contacto`, `reagendar`, `negativa`

### 👥 Gestión de Asesores
- CRUD completo con zona, tipo y estado
- Búsqueda en tiempo real y exportar a Excel

### 🏢 Gestión de Clientes
- CRUD completo con NIT, tipo, ciudad, zona
- Filtros por tipo, estado y búsqueda de texto
- Exportar a Excel

### ⭐ Satisfacción del Cliente
- Encuestas de 1 a 5 estrellas por ruta y cliente
- Una encuesta por par (ruta, cliente)
- Gráfica de distribución de calificaciones

### 📊 Dashboard
- KPIs: asesores activos, clientes, rutas, satisfacción promedio, visitas de hoy
- Gráfica de rutas por estado y top asesores

---

## 🛠️ Tecnologías

| Capa | Tecnología | Versión |
|------|-----------|---------|
| UI Framework | Bootstrap | 5.3.3 |
| Íconos | Bootstrap Icons | 1.11.3 |
| Tipografía | Plus Jakarta Sans | Google Fonts |
| Gráficas | Chart.js | 3.9.1 |
| Excel | SheetJS (xlsx) | 0.18.5 |
| QR | qrcodejs | 1.0.0 |
| Backend | PHP con PDO | 7.4+ |
| Base de datos | MySQL | 5.7+ |
| Servidor local | XAMPP / Laragon / WAMP | — |

---

## 🚀 Instalación

### Prerequisitos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web local (XAMPP, Laragon o WAMP)

### 1. Clonar el repositorio

```bash
git clone https://github.com/gjrepuestosmultimarca-netizen/sarc1.0.git
cd sarc1.0
```

### 2. Copiar al servidor web

```bash
# Windows (XAMPP)
xcopy /E /I sarc C:\xampp\htdocs\sarc1.0

# Linux / Mac
cp -r sarc /opt/lampp/htdocs/sarc1.0
```

### 3. Importar la base de datos

1. Inicia XAMPP → Apache y MySQL
2. Abre `http://localhost/phpmyadmin`
3. Crea la base `sarc_db` con cotejamiento `utf8mb4_unicode_ci`
4. Importa `sarc_database.sql`

### 4. Configurar credenciales MySQL

Edita `api/index.php` (líneas 7–10):

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sarc_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 5. Abrir el sistema

```
http://localhost/sarc1.0/
```

---

## 👤 Usuarios de prueba

| Usuario | Contraseña | Rol |
|---------|-----------|-----|
| `admin1` | `gejo1810` | Administrador |
| `supervisor1` | `diana123` | Supervisor |
| `asesor1` | `gerson123` | Asesor |

---

## 📁 Estructura del proyecto

```
sarc1.0/
├── index.html               # SPA principal — Bootstrap 5
├── sarc_database.sql        # Esquema y datos iniciales de la BD
├── manifest.json            # Configuración PWA
├── sw.js                    # Service Worker (offline)
├── README.md                # Este archivo
├── .gitignore
│
├── api/
│   ├── index.php            # API REST PHP (PDO + MySQL + Router)
│   └── .htaccess            # Reescritura de URLs para el router
│
└── icons/                   # Íconos PWA (72px – 512px)
```

---

## 🔌 API REST Completa

> **Base URL:** `http://localhost/sarc1.0/api`
>
> **Content-Type:** `application/json`
>
> **Formato de respuesta exitosa:**
> ```json
> { "ok": true, "data": { ... } }
> ```
>
> **Formato de error:**
> ```json
> { "ok": false, "error": "Descripción del error" }
> ```

---

### 🔐 Autenticación

#### `POST /auth`
Autentica un usuario activo y retorna sus datos de sesión.

**Body:**
```json
{
  "username": "admin1",
  "password": "gejo1810"
}
```

**Respuesta 200:**
```json
{
  "ok": true,
  "data": {
    "id": 1,
    "username": "admin1",
    "nombre": "Administrador",
    "email": "admin@sarc.com",
    "rol": "admin",
    "estado": "activo",
    "fecha_registro": "2026-03-21 10:00:00",
    "ultimo_acceso": "2026-04-05 09:30:00"
  }
}
```

| Código | Situación |
|--------|-----------|
| 200 | Login exitoso |
| 401 | Usuario o contraseña incorrectos |
| 405 | Método no permitido (solo POST) |
| 422 | Faltan campos requeridos |

---

### 👤 Usuarios

#### `GET /usuarios`
Lista todos los usuarios (sin `password_hash`).

**Respuesta 200:**
```json
{
  "ok": true,
  "data": [
    { "id": 1, "username": "admin1", "nombre": "...", "rol": "admin", "estado": "activo" }
  ]
}
```

---

#### `GET /usuarios/{id}`
Obtiene un usuario específico.

---

#### `POST /usuarios`
Crea un nuevo usuario.

**Body (campos con `*` son obligatorios):**
```json
{
  "username":  "nuevo_usuario",   // * único
  "nombre":    "Juan Pérez",      // *
  "email":     "juan@email.com",  // * único
  "password":  "miClave123",      // *
  "rol":       "asesor",          // * admin | supervisor | asesor
  "estado":    "activo"           //   activo (default) | inactivo
}
```

**Respuesta 201:**
```json
{ "ok": true, "id": 5, "message": "Usuario creado" }
```

| Código | Situación |
|--------|-----------|
| 201 | Creado |
| 409 | Username o email ya existe |
| 422 | Faltan campos requeridos |

---

#### `PUT /usuarios/{id}`
Actualiza datos del usuario. Solo envía los campos que deseas modificar.

```json
{
  "nombre":   "Nuevo Nombre",
  "email":    "nuevo@email.com",
  "rol":      "supervisor",
  "estado":   "inactivo",
  "password": "nuevaClave"       // opcional — recalcula el hash
}
```

---

#### `DELETE /usuarios/{id}`
Elimina permanentemente un usuario.

---

### 🧭 Asesores

#### `GET /asesores`
Lista asesores. Admite filtro opcional por estado.

**Query params:**
| Parámetro | Valores | Ejemplo |
|-----------|---------|---------|
| `estado` | `activo`, `inactivo`, `vacaciones` | `?estado=activo` |

---

#### `GET /asesores/{id}`
Obtiene un asesor por ID.

---

#### `POST /asesores`
Crea un asesor.

```json
{
  "nombre":   "María García",         // *
  "email":    "mgarcia@empresa.com",  // *
  "tipo":     "senior",               // * junior | senior | key account
  "telefono": "+57 301 234 5678",
  "zona":     "Norte",
  "estado":   "activo",
  "notas":    "Especialista en retail"
}
```

**Respuesta 201:** `{ "ok": true, "id": 3, "message": "Asesor creado" }`

---

#### `PUT /asesores/{id}`
Actualiza un asesor (envía solo los campos a cambiar).

---

#### `DELETE /asesores/{id}`
Elimina un asesor.

| Código | Situación |
|--------|-----------|
| 200 | Eliminado |
| 409 | El asesor tiene rutas asignadas |

---

### 🏢 Clientes

#### `GET /clientes`
Lista clientes con filtros opcionales.

**Query params:**
| Parámetro | Descripción |
|-----------|-------------|
| `tipo` | `mayorista`, `minorista`, `distribuidor`, `corporativo` |
| `estado` | `activo`, `inactivo`, `prospecto` |
| `q` | Texto libre (busca en nombre y contacto) |

**Ejemplo:** `GET /clientes?estado=activo&tipo=mayorista`

---

#### `GET /clientes/{id}`
Obtiene un cliente por ID.

---

#### `POST /clientes`
Crea un cliente.

```json
{
  "nombre":    "Supermercado Éxito",   // *
  "tipo":      "mayorista",            // *
  "nit":       "900123456-7",
  "contacto":  "Juan Pérez",
  "email":     "juan@exito.com",
  "telefono":  "+57 601 234 5678",
  "direccion": "Calle 100 #15-20",
  "ciudad":    "Medellín",
  "zona":      "Centro",
  "estado":    "activo",
  "notas":     "Prefiere visitas martes y jueves"
}
```

---

#### `PUT /clientes/{id}`
Actualiza un cliente (envía solo los campos a cambiar).

---

#### `DELETE /clientes/{id}`
Elimina permanentemente un cliente.

---

### 🗺️ Rutas

#### `GET /rutas`
Lista rutas con nombre del asesor incluido. `clientes_ids` se retorna como array.

**Query params:**
| Parámetro | Descripción |
|-----------|-------------|
| `asesor_id` | Filtra por asesor |
| `estado` | `planificada`, `en-progreso`, `completada`, `cancelada` |
| `fecha` | Fecha exacta en formato `YYYY-MM-DD` |

---

#### `GET /rutas/{id}`
Obtiene una ruta específica con `clientes_ids` decodificado.

**Respuesta 200:**
```json
{
  "ok": true,
  "data": {
    "id": 12,
    "asesor_id": 2,
    "asesor_nombre": "Carlos López",
    "fecha": "2026-04-05",
    "hora_inicio": "08:00",
    "hora_fin": "17:00",
    "zona": "Norte",
    "estado": "completada",
    "vehiculo": "ABC-123",
    "km_inicial": 45000,
    "km_final": 45180,
    "clientes_ids": [1, 3, 7],
    "observaciones": ""
  }
}
```

---

#### `POST /rutas`
Crea una ruta.

```json
{
  "asesor_id":    2,               // *
  "fecha":        "2026-04-06",    // *  YYYY-MM-DD
  "hora_inicio":  "08:00",         // *
  "zona":         "Norte",         // *
  "estado":       "planificada",   // *
  "hora_fin":     "17:00",
  "vehiculo":     "ABC-123",
  "km_inicial":   45000,
  "km_final":     0,
  "clientes_ids": [1, 3, 7],       // Array de IDs de clientes
  "observaciones": ""
}
```

---

#### `PUT /rutas/{id}`
Actualiza una ruta (envía solo los campos a cambiar).

Para marcar como completada:
```json
{ "estado": "completada", "hora_fin": "17:00", "km_final": 45180 }
```

---

#### `DELETE /rutas/{id}`
Elimina una ruta.

| Código | Situación |
|--------|-----------|
| 200 | Eliminada |
| 409 | La ruta está completada |

---

### ⭐ Satisfacciones

#### `GET /satisfacciones`
Lista todas las encuestas con `cliente_nombre` y `asesor_nombre`, ordenadas por fecha descendente.

---

#### `GET /satisfacciones/{id}`
Obtiene una encuesta por ID.

---

#### `POST /satisfacciones`
Registra una encuesta de satisfacción.

```json
{
  "ruta_id":      12,             // *
  "cliente_id":   3,              // *
  "calificacion": 5,              // *  entero entre 1 y 5
  "fecha":        "2026-04-05",   // *  YYYY-MM-DD
  "comentarios":  "Excelente atención"
}
```

> **Restricción:** Solo puede existir una encuesta por par `(ruta_id, cliente_id)`.

| Código | Situación |
|--------|-----------|
| 201 | Creada |
| 409 | Ya existe encuesta para ese par ruta/cliente |
| 422 | Calificación fuera de rango 1–5 |

---

#### `PUT /satisfacciones/{id}`
Actualiza una encuesta (envía solo los campos a cambiar).

```json
{ "calificacion": 4, "comentarios": "Buena visita" }
```

---

#### `DELETE /satisfacciones/{id}`
Elimina una encuesta.

---

### 📍 Visitas GPS

#### `GET /visitas`
Retorna las últimas 100 visitas GPS globales, incluyendo `cliente_nombre`, `asesor_nombre`, `zona` y `ruta_fecha`.

---

#### `GET /visitas/{ruta_id}`
Retorna todas las visitas de una ruta específica, ordenadas por `fecha_registro ASC`.

---

#### `POST /visitas`
Registra o actualiza una visita (check-in / check-out).

> Usa `ON DUPLICATE KEY UPDATE` para permitir que el mismo registro se actualice al registrar la salida sin crear duplicados.

```json
{
  "ruta_id":      12,          // *
  "cliente_id":   3,           // *
  "hora_entrada": "09:15",     // *  HH:MM
  "resultado":    "exitosa",   // *  exitosa | sin_contacto | reagendar | negativa
  "hora_salida":  "09:50",
  "duracion_min": 35,
  "lat_entrada":  6.2518400,   // GPS — hasta 7 decimales
  "lng_entrada":  -75.5635900,
  "lat_salida":   6.2518500,
  "lng_salida":   -75.5636000,
  "notas":        "Cliente solicitó nuevo catálogo"
}
```

**Respuesta 201:**
```json
{ "ok": true, "id": 42, "message": "Visita registrada" }
```

---

#### `PUT /visitas/{id}`
Actualiza campos de una visita. Útil para registrar la salida cuando se conoce el ID.

```json
{
  "hora_salida":  "10:00",
  "duracion_min": 45,
  "lat_salida":   6.2518500,
  "lng_salida":   -75.5636100,
  "resultado":    "exitosa",
  "notas":        "Pedido generado"
}
```

---

#### `DELETE /visitas/{id}`
Elimina permanentemente una visita GPS.

---

### 📊 Dashboard

#### `GET /dashboard`
Retorna todos los KPIs del sistema en una sola llamada.

**Respuesta 200:**
```json
{
  "ok": true,
  "data": {
    "total_asesores":        10,
    "asesores_activos":       8,
    "total_clientes":        45,
    "clientes_activos":      40,
    "total_rutas":          120,
    "rutas_completadas":     95,
    "rutas_pendientes":      18,
    "promedio_satisfaccion":  4.3,
    "visitas_hoy":           12,
    "visitas_exitosas_hoy":  10,
    "rutas_por_estado": [
      { "estado": "completada",  "total": 95 },
      { "estado": "planificada", "total": 12 },
      { "estado": "en-progreso", "total":  6 },
      { "estado": "cancelada",   "total":  7 }
    ],
    "top_asesores": [
      { "nombre": "Carlos López", "rutas": 28 },
      { "nombre": "María García", "rutas": 22 }
    ]
  }
}
```

---

## 📋 Resumen de Endpoints

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `POST` | `/auth` | Iniciar sesión |
| `GET` | `/usuarios` | Listar usuarios |
| `GET` | `/usuarios/{id}` | Obtener usuario |
| `POST` | `/usuarios` | Crear usuario |
| `PUT` | `/usuarios/{id}` | Actualizar usuario |
| `DELETE` | `/usuarios/{id}` | Eliminar usuario |
| `GET` | `/asesores` | Listar asesores |
| `GET` | `/asesores/{id}` | Obtener asesor |
| `POST` | `/asesores` | Crear asesor |
| `PUT` | `/asesores/{id}` | Actualizar asesor |
| `DELETE` | `/asesores/{id}` | Eliminar asesor |
| `GET` | `/clientes` | Listar clientes |
| `GET` | `/clientes/{id}` | Obtener cliente |
| `POST` | `/clientes` | Crear cliente |
| `PUT` | `/clientes/{id}` | Actualizar cliente |
| `DELETE` | `/clientes/{id}` | Eliminar cliente |
| `GET` | `/rutas` | Listar rutas |
| `GET` | `/rutas/{id}` | Obtener ruta |
| `POST` | `/rutas` | Crear ruta |
| `PUT` | `/rutas/{id}` | Actualizar ruta |
| `DELETE` | `/rutas/{id}` | Eliminar ruta |
| `GET` | `/satisfacciones` | Listar encuestas |
| `GET` | `/satisfacciones/{id}` | Obtener encuesta |
| `POST` | `/satisfacciones` | Registrar encuesta |
| `PUT` | `/satisfacciones/{id}` | Actualizar encuesta |
| `DELETE` | `/satisfacciones/{id}` | Eliminar encuesta |
| `GET` | `/visitas` | Últimas 100 visitas |
| `GET` | `/visitas/{ruta_id}` | Visitas de una ruta |
| `POST` | `/visitas` | Registrar visita |
| `PUT` | `/visitas/{id}` | Actualizar visita |
| `DELETE` | `/visitas/{id}` | Eliminar visita |
| `GET` | `/dashboard` | KPIs generales |

---

## 🗄️ Base de Datos

### Diagrama de relaciones

```
usuarios (autenticación del sistema)
    │
    ├── [independiente — login y gestión de cuentas]

asesores ──────────────────────────────────────────────────────┐
    │                                                           │
    └──< rutas >──────────────────────────────────────────────┐│
              │                                               ││
              ├──< satisfacciones >── clientes               ││
              │                                               ││
              └──< visitas_gps >────── clientes              ││
                                                             ││
clientes ────────────────────────────────────────────────────┘┘
```

### Tablas

| Tabla | Descripción |
|-------|-------------|
| `usuarios` | Cuentas del sistema con roles y contraseñas hasheadas (bcrypt) |
| `asesores` | Equipo comercial con zona, tipo y estado |
| `clientes` | Cartera de clientes con NIT, ciudad y zona |
| `rutas` | Visitas planificadas con asesor, fecha, km y lista de clientes (JSON) |
| `satisfacciones` | Encuestas 1–5 por par ruta/cliente (única) |
| `visitas_gps` | Check-in/out GPS por cliente dentro de cada ruta |

---

## 🔑 Control de Acceso por Rol

```
Administrador
  ├── Dashboard completo
  ├── Rutas (CRUD)
  ├── Asesores (CRUD)
  ├── Clientes (CRUD)
  ├── Satisfacción (CRUD)
  ├── Visitas GPS
  ├── Reportes
  ├── Gestión de Usuarios ✦
  └── Configuración del Sistema ✦

Supervisor
  ├── Dashboard
  ├── Rutas (lectura)
  ├── Asesores (lectura)
  ├── Clientes (lectura)
  ├── Satisfacción (lectura)
  └── Reportes

Asesor
  ├── Dashboard (propio)
  ├── Rutas (propias — CRUD)
  ├── Clientes
  ├── Visitas GPS (propias)
  └── Satisfacción
```

---

## 📱 Responsive

| Dispositivo | Comportamiento |
|-------------|---------------|
| 📱 Móvil (< 768px) | Sidebar oculto, accesible con hamburger |
| 📟 Tablet (768–992px) | Sidebar visible, grids de 2 columnas |
| 🖥️ Desktop (> 992px) | Sidebar fijo, grids completos |

---

## 🗓️ Historial de versiones

| Versión | Fecha | Cambios |
|---------|-------|---------|
| `v2.1.0` | Abr 2026 | API visitas GPS completa, validaciones, documentación PHPDoc |
| `v2.0.0` | Mar 2026 | Bootstrap 5, sidebar, menú por rol, registro, perfil, PWA |

---

## 📄 Licencia

Licencia [MIT](LICENSE) — libre uso, copia, modificación y distribución con aviso de copyright.

---

<div align="center">

**SARC v2.1** — Bootstrap 5 · PHP · MySQL · PWA

</div># sarc1.0
