# 🚀 SocialHub Manager

> **Aplicación web para la administración y programación de publicaciones en redes sociales**

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC.svg)](https://tailwindcss.com)
[![MariaDB](https://img.shields.io/badge/MariaDB-10.x-003545.svg)](https://mariadb.org)

## 📋 Descripción del Proyecto

**SocialHub Manager** es una solución web desarrollada para la empresa "Social Hub Manager" que permite a los usuarios gestionar y programar publicaciones en múltiples redes sociales desde una plataforma centralizada.

### 🎯 Funcionalidades Implementadas

#### ✅ Sistema de Autenticación
- **Registro de usuarios** con validación de datos
- **Inicio de sesión** tradicional (email + contraseña)
- **Autenticación OAuth 2.0** con Google
- **Sesiones persistentes** con opción "Recuérdame"
- **Logout seguro** con invalidación de sesión

#### 🔐 **Segundo Factor de Autenticación (2FA)**
- **Autenticación de dos factores** con Google Authenticator
- **Activación opcional** para usuarios
- **Verificación obligatoria** en rutas protegidas
- **Códigos QR** para configuración fácil
- **Middleware de protección** para rutas sensibles
- **Integración completa** con login tradicional y Google OAuth

#### 🔒 Características de Seguridad
- **Validación robusta** de formularios
- **Hash seguro** de contraseñas (bcrypt)
- **Protección CSRF** en todos los formularios
- **Middleware de autenticación** para rutas protegidas
- **Protección de rutas** con verificación 2FA

#### 🎨 Interfaz de Usuario
- **Componentes reutilizables** (layouts, formularios)
- **Navegación intuitiva** entre páginas
- **Feedback visual** para errores y validaciones
- **Vistas responsivas** para configuración de seguridad

## 🛠️ Tecnologías Utilizadas

### Backend
- **Laravel 11** - Framework PHP moderno y elegante
- **PHP 8.2+** - Lenguaje de programación
- **MariaDB** - Base de datos relacional
- **Laravel Socialite** - Integración OAuth con redes sociales
- **Google2FA** - Autenticación de dos factores
- **Bacon QR Code** - Generación de códigos QR

### Frontend
- **Tailwind CSS** - Framework CSS utility-first
- **Blade Templates** - Motor de plantillas de Laravel

### Infraestructura
- **Vagrant** - Entorno de desarrollo virtualizado
- **Apache 2.4** - Servidor web
- **Composer** - Gestor de dependencias PHP

## 🚀 Instalación y Configuración

### Prerrequisitos
- Vagrant
- VirtualBox o Hyper-V
- Git

### 1. Clonar el Repositorio
```bash
git clone <URL_DEL_REPO>
cd SocialHub.xyz
```

### 2. Configurar Entorno Virtual
```bash
cd /ruta/a/tu/proyecto/VMs/webserver
vagrant up
vagrant ssh
```

### 3. Instalar Dependencias
```bash
cd ~/sites/SocialHub.xyz
composer install
npm install
```

### 4. Configurar Base de Datos
```bash
# En la VM de base de datos
sudo mariadb
CREATE DATABASE SocialHub;
CREATE USER 'socialhub_user'@'192.168.56.%' IDENTIFIED BY '123';
GRANT ALL PRIVILEGES ON SocialHub.* TO 'socialhub_user'@'192.168.56.%';
FLUSH PRIVILEGES;
EXIT;
```

### 5. Configurar Variables de Entorno
```bash
cp .env.example .env
nano .env
```

Configurar:
```env
APP_URL=http://socialhub.xyz
DB_CONNECTION=mysql
DB_HOST=192.168.56.11
DB_PORT=3306
DB_DATABASE=SocialHub
DB_USERNAME=socialhub_user
DB_PASSWORD=123

GOOGLE_CLIENT_ID=client_id
GOOGLE_CLIENT_SECRET=client_secret
GOOGLE_REDIRECT_URI=http://socialhub.xyz/auth/google/callback
```

### 6. Ejecutar Migraciones
```bash
php artisan migrate
```

### 7. Configurar Apache
```bash
sudo a2ensite SocialHub.xyz.conf
sudo systemctl reload apache2
```

## 🔧 Configuración de Google OAuth

### 1. Crear Proyecto en Google Cloud Console
- Ir a [Google Cloud Console](https://console.cloud.google.com)
- Crea un nuevo proyecto o selecciona uno existente
- Habilitar la API de Google+ 

### 2. Configurar OAuth 2.0
- Ir a "APIs & Services" → "Credentials"
- Crea "OAuth 2.0 Client IDs"
- Tipo: "Web application"
- URIs autorizados: `http://socialhub.xyz/auth/google/callback`

### 3. Configurar Pantalla de Consentimiento
- Agregar el dominio: `socialhub.xyz`
- Configura usuarios de prueba si es necesario

## 📱 Uso de la Aplicación

### Registro de Usuario
1. Ve a `/register`
2. Completa el formulario con:
   - Nombre completo
   - Email válido
   - Contraseña (mínimo 8 caracteres)
   - Confirmación de contraseña
3. Haz clic en "Registrarme"

### Inicio de Sesión
1. Ve a `/login`
2. Ingresa tu email y contraseña
3. Opcional: marca "Recuérdame"
4. Haz clic en "Entrar"

### Autenticación con Google
1. En cualquier formulario de auth, haz clic en "Continuar con Google"
2. Autoriza la aplicación en Google
3. **Si tienes 2FA habilitado**, serás redirigido a verificación 2FA
4. **Si no tienes 2FA**, serás redirigido al dashboard

### Configuración de 2FA
1. **Activar 2FA:**
   - Ve a `/dashboard`
   - Haz clic en "Activar 2FA"
   - Escanea el código QR con Google Authenticator
   - Ingresa el código de 6 dígitos
   
2. **Verificación 2FA:**
   - Después del login, si tienes 2FA activado
   - Ingresa el código de 6 dígitos de tu app
   - Acceso al dashboard

### Dashboard
- Vista protegida solo para usuarios autenticados
- **Protección adicional con 2FA** si está habilitado
- Muestra información del usuario logueado
- Opciones de configuración de seguridad
- Opción para cerrar sesión

## 🗂️ Estructura del Proyecto

```
SocialHub.xyz/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php          # Auth tradicional
│   │   ├── GoogleAuthController.php    # OAuth Google
│   │   └── Google2FAController.php    # 2FA y seguridad
│   ├── Http/Middleware/
│   │   └── Verificar2FA.php           # Middleware de protección 2FA
│   └── Models/
│       └── User.php                    # Modelo de usuario con campos 2FA
├── resources/views/
│   ├── auth/
│   │   ├── login.blade.php            # Vista de login
│   │   ├── register.blade.php         # Vista de registro
│   │   ├── setup-2fa.blade.php        # Configuración 2FA
│   │   └── verify-2fa.blade.php       # Verificación 2FA
│   ├── layouts/
│   │   └── app.blade.php              # Layout principal
│   └── dashboard.blade.php            # Dashboard protegido
├── routes/
│   └── web.php                        # Rutas de la aplicación
└── database/
    └── migrations/                    # Migraciones de BD incluyendo 2FA
```

## 🔐 Rutas Protegidas

- `/dashboard` - Requiere autenticación + verificación 2FA (si está habilitado)
- `/logout` - Solo usuarios logueados
- `/2fa/*` - Rutas de configuración y verificación 2FA

## 🚧 Próximas Funcionalidades

- [ ] Integración con Twitter API
- [ ] Integración con Facebook API
- [ ] Sistema de publicaciones programadas
- [ ] Gestión de horarios de publicación
- [ ] Cola de publicaciones
- [x] **Autenticación de dos factores (2FA)** ✅ **IMPLEMENTADO COMPLETAMENTE**
- [ ] Panel de administración
- [ ] API REST para integraciones

## 🔒 **Sistema de Seguridad Implementado**

### **Autenticación de Dos Factores (2FA)**
- ✅ **Activación opcional** para usuarios
- ✅ **Verificación obligatoria** en rutas protegidas
- ✅ **Códigos QR** para configuración fácil
- ✅ **Middleware de protección** para rutas sensibles
- ✅ **Integración completa** con login tradicional y Google OAuth
- ✅ **Base de datos** con campos de seguridad
- ✅ **Controladores** para gestión de 2FA
- ✅ **Vistas** para configuración y verificación
- ✅ **Rutas protegidas** con verificación 2FA

---

<div align="center">
  <p>Desarrollado con ❤️ usando Laravel y Tailwind CSS</p>
  <p><strong>SocialHub Manager</strong> - Gestiona tus redes sociales desde un solo lugar</p>
</div>
