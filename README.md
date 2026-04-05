# 🗺️ SARC — Sistema Avanzado de Rutas Comerciales

<div align="center">

![Version](https://img.shields.io/badge/versión-2.0.0-4F46E5?style=for-the-badge)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Licencia](https://img.shields.io/badge/licencia-MIT-10B981?style=for-the-badge)
![Responsive](https://img.shields.io/badge/Responsive-✓-06B6D4?style=for-the-badge)

**Sistema web integral para la gestión de asesores comerciales, clientes, rutas de visita y medición de satisfacción — rediseñado en Bootstrap 5 con soporte completo para móvil.**

[Demo](#-demo) · [Instalación](#-instalación) · [API](#-api-rest) · [Capturas](#-capturas)

</div>

---

## ✨ Novedades en v2.0 (Bootstrap 5)

| Característica | v1.0 | v2.0 |
|----------------|:----:|:----:|
| Bootstrap 5 | ❌ | ✅ |
| Sidebar con navegación | ❌ | ✅ |
| Responsive para móvil | Parcial | ✅ Completo |
| Menú de usuario por rol | ❌ | ✅ |
| Registro de cuenta funcional | ❌ | ✅ |
| Recuperación de contraseña | ❌ | ✅ |
| Mi Perfil editable | ❌ | ✅ |
| Cambiar contraseña | ❌ | ✅ |
| Zona como lista desplegable | ❌ | ✅ |
| Configuración del sistema | ❌ | ✅ |
| Gestión de usuarios (admin) | Básica | ✅ Completa |


---

## 📋 Características

### 🔐 Autenticación
- Login con validación y mensajes de error claros
- **Registrarse**: formulario completo con rol seleccionable
- **¿Olvidó su contraseña?**: flujo de recuperación por email
- "Recordarme" persiste la sesión entre cierres del navegador
- Control de acceso por roles: `admin`, `supervisor`, `asesor`

### 🧭 Navegación
- **Sidebar fijo** con íconos, secciones y estado activo
- **Menú desplegable de usuario** con opciones diferenciadas por rol:
  - 👑 **Administrador** → Configuración del sistema + Gestión de usuarios
  - 👁️ **Supervisor** → Reportes + Supervisar asesores
  - 🙋 **Asesor** → Mis rutas + Mis clientes
- Topbar con hamburger en móvil
- Overlay para cerrar sidebar en pantallas pequeñas

### 🗺️ Gestión de Rutas
- Registro con asesor, fecha, hora, zona (desplegable), estado, vehículo y kilómetros
- Selección múltiple de clientes a visitar
- Filtros por asesor, estado y fecha
- Marcar ruta como completada con hora automática
- Exportar a Excel

### 👥 Gestión de Asesores
- CRUD completo (crear, editar, eliminar)
- Zona asignada como lista desplegable
- Búsqueda en tiempo real
- Exportar a Excel

### 🏢 Gestión de Clientes
- CRUD completo con NIT, tipo, contacto, ciudad, zona y notas
- Filtros por tipo y estado
- Zona como lista desplegable
- Exportar a Excel

### ⭐ Satisfacción del Cliente
- Encuestas de 1 a 5 estrellas por ruta y cliente
- Gráfica de distribución de calificaciones
- Historial en tabla ordenada por fecha

### 📊 Dashboard
- KPIs: asesores activos, clientes, rutas completadas, pendientes y satisfacción promedio
- Gráfica de rutas por estado (dona)
- Gráfica de rendimiento de asesores (barras)
- Actividad reciente

### 📈 Reportes
- Resumen de rendimiento por asesor (% de éxito, satisfacción)
- Distribución de clientes por tipo con barra de progreso visual
- Exportar reporte completo a Excel

### 👤 Perfil de usuario
- Editar nombre y email
- Cambiar contraseña con validación
- Actualización en tiempo real del sidebar

### ⚙️ Configuración del Sistema *(admin)*
- Nombre del sistema, zona horaria e idioma
- Gestión visual de zonas comerciales
- Opciones de respaldo de base de datos

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
git clone https://github.com/TU_USUARIO/sarc1.1.git
cd sarc1.0
```

### 2. Copiar al servidor web

```bash
# Windows (XAMPP)
xcopy /E /I sarc C:\xampp\htdocs\sarc1.0

# Linux / Mac
cp -r sarc /opt/lampp/htdocs/sarc
```

### 3. Importar la base de datos

1. Inicia XAMPP y arranca **Apache** y **MySQL**
2. Abre **phpMyAdmin** → `http://localhost/phpmyadmin`
3. Clic en **Nueva** base de datos → nombre: `sarc_db` → cotejamiento: `utf8mb4_unicode_ci`
4. Ve a la pestaña **Importar**
5. Selecciona el archivo `sarc_database.sql`
6. Clic en **Continuar**

### 4. Configurar credenciales MySQL

Edita `api/index.php` (líneas 7–10):

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sarc_db');
define('DB_USER', 'root');    // ← tu usuario MySQL
define('DB_PASS', '');         // ← tu contraseña MySQL
```

### 5. Abrir el sistema

```
http://localhost/sarc1.0/
```

---

## 👤 Usuarios de prueba

| Usuario | Contraseña | Rol | Acceso |
|---------|-----------|-----|--------|
| `admin1` | `gejo1810` | Administrador | Acceso total |
| `supervisor1` | `diana123` | Supervisor | Reportes + Asesores |
| `asesor1` | `gerson123` | Asesor | Rutas + Clientes |


## 📁 Estructura del proyecto

```
sarc/
├── index.html               # SPA principal — Bootstrap 5
├── diagnostico.php          # Diagnóstico de conexión MySQL
├── sarc_database.sql        # Esquema y datos iniciales de la BD
├── manifest.json            # Configuración PWA
├── sw.js                    # Service Worker (offline)
├── README.md                # Este archivo
├── .gitignore               # Archivos excluidos del repositorio
│
├── api/
│   ├── index.php            # API REST PHP (PDO + MySQL + Router)
│   └── .htaccess            # Reescritura de URLs para el router
│
└── icons/                   # Íconos PWA (72px – 512px)
    ├── icon-72.png
    ├── icon-96.png
    ├── icon-128.png
    ├── icon-144.png
    ├── icon-152.png
    ├── icon-192.png
    ├── icon-384.png
    └── icon-512.png
```

---

### Autenticación

| Método | Endpoint | Cuerpo | Descripción |
|--------|----------|--------|-------------|
| `POST` | `/auth` | `{ username, password }` | Iniciar sesión |

### Usuarios

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/usuarios` | Listar todos los usuarios |
| `POST` | `/usuarios` | Crear usuario `{ username, nombre, email, password, rol }` |
| `PUT` | `/usuarios/{id}` | Actualizar usuario |
| `PUT` | `/usuarios/{id}/password` | Cambiar contraseña |
| `DELETE` | `/usuarios/{id}` | Eliminar usuario |

### Asesores

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/asesores` | Listar asesores |
| `POST` | `/asesores` | Crear asesor |
| `PUT` | `/asesores/{id}` | Actualizar asesor |
| `DELETE` | `/asesores/{id}` | Eliminar asesor |

### Clientes

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/clientes` | Listar clientes |
| `POST` | `/clientes` | Crear cliente |
| `PUT` | `/clientes/{id}` | Actualizar cliente |
| `DELETE` | `/clientes/{id}` | Eliminar cliente |

### Rutas

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/rutas` | Listar rutas |
| `POST` | `/rutas` | Crear ruta |
| `PUT` | `/rutas/{id}` | Actualizar ruta |
| `DELETE` | `/rutas/{id}` | Eliminar ruta |

### Satisfacciones

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/satisfacciones` | Listar encuestas |
| `POST` | `/satisfacciones` | Registrar encuesta `{ ruta_id, cliente_id, calificacion, fecha, comentarios }` |

### Dashboard

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/dashboard` | KPIs: asesores, clientes, rutas, satisfacción promedio |

---

## 📱 Responsive

El sistema está optimizado para todos los tamaños de pantalla:

| Dispositivo | Comportamiento |
|-------------|---------------|
| 📱 Móvil (< 768px) | Sidebar oculto, accesible con hamburger; modales desde abajo |
| 📟 Tablet (768px–992px) | Sidebar visible, grids de 2 columnas |
| 🖥️ Desktop (> 992px) | Sidebar fijo, grids completos de hasta 6 columnas |

---

## 🌐 Zonas comerciales

Las zonas están configuradas como lista desplegable en el formulario de rutas, asesores y clientes:

- 🏙️ **Centro**
- 🧭 **Norte**
- 🌿 **Sur**
- ☀️ **Oriente**
- 🌄 **Occidente**

Se pueden ampliar desde **Configuración del sistema** (requiere rol `admin1`).

---

## 🔑 Control de acceso por rol

```
Administrador
  ├── Dashboard completo
  ├── Rutas (CRUD)
  ├── Asesores (CRUD)
  ├── Clientes (CRUD)
  ├── Satisfacción
  ├── Reportes
  ├── Gestión de Usuarios ✦
  └── Configuración del Sistema ✦

Supervisor
  ├── Dashboard
  ├── Rutas (lectura)
  ├── Asesores (lectura)
  ├── Clientes (lectura)
  ├── Satisfacción
  └── Reportes

Asesor
  ├── Dashboard (propio)
  ├── Rutas (propias)
  ├── Clientes
  └── Satisfacción
```

---

## 🗄️ Base de datos

### Esquema de tablas

```sql
usuarios       -- Cuentas del sistema (username, password_hash, rol, estado)
asesores       -- Equipo comercial (nombre, email, telefono, tipo, zona)
clientes       -- Cartera de clientes (nombre, nit, tipo, ciudad, zona)
rutas          -- Visitas planificadas (asesor_id, fecha, zona, estado, km)
satisfacciones -- Encuestas (ruta_id, cliente_id, calificacion 1-5)
```

### Diagrama de relaciones

```
usuarios ─────────────────────────────┐
                                       │ (autenticación)
asesores ──┬── rutas ──┬── satisfacciones
           │           │
clientes ──┴───────────┘
```

---

## 🤝 Contribuir

1. Haz un fork del repositorio
2. Crea tu rama: `git checkout -b feature/nueva-funcionalidad`
3. Realiza tus cambios y haz commit: `git commit -m "feat: descripción"`
4. Sube tu rama: `git push origin feature/nueva-funcionalidad`
5. Abre un Pull Request

### Convención de commits

```
feat:     nueva funcionalidad
fix:      corrección de bug
style:    cambios de estilos / UI
refactor: refactorización de código
docs:     cambios en documentación
chore:    cambios de configuración
```

---

## 📄 Licencia

Este proyecto está bajo la licencia [MIT](LICENSE).

Puedes usar, copiar, modificar, fusionar, publicar, distribuir y sublicenciar este software libremente, siempre que incluyas el aviso de copyright original.

---

## 🗓️ Historial de versiones

| Versión | Fecha | Cambios |
|---------|-------|---------|
| `v2.0.0` | Mar 2026 | Diseño completo Bootstrap 5, sidebar, menú por rol, registro, perfil, responsive total |


---

<div align="center">

**SARC v1.0** — Bootstrap 5 · PHP · MySQL

</div>
