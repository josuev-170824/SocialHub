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

#### 🔗 **Integración con Redes Sociales**
- **LinkedIn OAuth 2.0** ✅ **FUNCIONANDO**
  - Autenticación completa
  - Vinculación de cuentas
  - Acceso a perfil y datos básicos
  - Tokens de acceso y refresco
- **Mastodon OAuth 2.0** ✅ **FUNCIONANDO**
  - Autenticación completa
  - Vinculación de cuentas
  - Acceso a perfil y datos básicos
  - Tokens de acceso y refresco
- **Twitter OAuth 2.0** ⚠️ **CONFIGURADO PERO CON PROBLEMAS HTTPS**
  - Estructura implementada
  - Requiere configuración HTTPS para funcionar

#### �� Características de Seguridad
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
- **Panel de configuración** para gestión de redes sociales

## 🛠️ Tecnologías Utilizadas

### Backend
- **Laravel 11** - Framework PHP moderno y elegante
- **PHP 8.2+** - Lenguaje de programación
- **MariaDB** - Base de datos relacional
- **Laravel Socialite** - Integración OAuth con redes sociales
- **Google2FA** - Autenticación de dos factores
- **Bacon QR Code** - Generación de códigos QR
- **League OAuth2 Client** - Cliente OAuth 2.0 personalizado

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

# Google OAuth
GOOGLE_CLIENT_ID=client_id
GOOGLE_CLIENT_SECRET=client_secret
GOOGLE_REDIRECT_URI=http://socialhub.xyz/auth/google/callback

# LinkedIn OAuth
LINKEDIN_CLIENT_ID=client_id
LINKEDIN_CLIENT_SECRET=client_secret
LINKEDIN_REDIRECT_URI=http://socialhub.xyz/auth/linkedin/callback

# Mastodon OAuth
MASTODON_CLIENT_ID=client_id
MASTODON_CLIENT_SECRET=client_secret
MASTODON_REDIRECT_URI=http://socialhub.xyz/auth/mastodon/callback
MASTODON_INSTANCE_URL=https://mastodon.social

# Twitter OAuth (requiere HTTPS)
TWITTER_CLIENT_ID=client_id
TWITTER_CLIENT_SECRET=client_secret
TWITTER_REDIRECT_URI=https://socialhub.xyz/auth/twitter/callback
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

## �� Configuración de OAuth 2.0

### Google OAuth
1. Crear proyecto en [Google Cloud Console](https://console.cloud.google.com)
2. Habilitar API de Google+
3. Configurar OAuth 2.0 con URIs autorizados

### LinkedIn OAuth
1. Crear app en [LinkedIn Developers](https://www.linkedin.com/developers/)
2. Configurar OAuth 2.0 scopes: `openid`, `profile`, `email`
3. Agregar URL de redirección autorizada

### Mastodon OAuth
1. Crear app en instancia de Mastodon (ej: mastodon.social)
2. Configurar scopes: `read`, `write`
3. Agregar URL de redirección autorizada

### Twitter OAuth
1. Crear app en [Twitter Developer Portal](https://developer.twitter.com/)
2. **Requiere HTTPS** para funcionar
3. Configurar OAuth 2.0 con scopes apropiados

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

### Gestión de Redes Sociales
1. **Ve a Settings** (`/settings`)
2. **Conecta tus cuentas:**
   - **LinkedIn:** Haz clic en "Conectar LinkedIn"
   - **Mastodon:** Haz clic en "Conectar Mastodon"
   - **Twitter:** Requiere configuración HTTPS
3. **Gestiona conexiones** desde el panel de configuración

## 🗂️ Estructura del Proyecto

```
SocialHub.xyz/
├── app/
│ ├── Http/Controllers/
│ │ ├── AuthController.php # Auth tradicional
│ │ ├── GoogleAuthController.php # OAuth Google
│ │ ├── Google2FAController.php # 2FA y seguridad
│ │ ├── ControladorAutenticacionLinkedIn.php # OAuth LinkedIn
│ │ ├── ControladorAutenticacionMastodon.php # OAuth Mastodon
│ │ └── ControladorAutenticacionTwitter.php # OAuth Twitter
│ ├── Http/Middleware/
│ │ └── Verificar2FA.php # Middleware de protección 2FA
│ ├── Models/
│ │ ├── User.php # Modelo de usuario con campos 2FA
│ │ └── CuentaRedSocial.php # Modelo para cuentas de redes sociales
│ └── Services/OAuth/
│ ├── LinkedInProvider.php # Proveedor OAuth LinkedIn
│ ├── MastodonProvider.php # Proveedor OAuth Mastodon
│ └── TwitterProvider.php # Proveedor OAuth Twitter
├── resources/views/
│ ├── auth/
│ │ ├── login.blade.php # Vista de login
│ │ ├── register.blade.php # Vista de registro
│ │ ├── setup-2fa.blade.php # Configuración 2FA
│ │ ├── verify-2fa.blade.php # Verificación 2FA
│ │ └── settings.blade.php # Configuración de usuario y redes sociales
│ ├── layouts/
│ │ └── app.blade.php # Layout principal
│ └── dashboard.blade.php # Dashboard protegido
├── routes/
│ └── web.php # Rutas de la aplicación
└── database/
└── migrations/ # Migraciones de BD incluyendo 2FA y redes sociales
```


## �� Rutas Protegidas

- `/dashboard` - Requiere autenticación + verificación 2FA (si está habilitado)
- `/settings` - Configuración de usuario y redes sociales
- `/logout` - Solo usuarios logueados
- `/2fa/*` - Rutas de configuración y verificación 2FA
- `/auth/*` - Rutas de autenticación OAuth con redes sociales

## 🚧 Estado de Implementación

### ✅ **COMPLETAMENTE IMPLEMENTADO**
- [x] **Sistema de autenticación tradicional** ✅
- [x] **Autenticación OAuth con Google** ✅
- [x] **Autenticación de dos factores (2FA)** ✅
- [x] **Integración con LinkedIn** ✅
- [x] **Integración con Mastodon** ✅
- [x] **Sistema de cuentas de redes sociales** ✅
- [x] **Panel de configuración de usuario** ✅

### ⚠️ **PARCIALMENTE IMPLEMENTADO**
- [x] **Integración con Twitter** (estructura lista, requiere HTTPS)

### 🚧 **PRÓXIMAS FUNCIONALIDADES**
- [ ] **Sistema de publicaciones** desde SocialHub
- [ ] **Publicaciones programadas** en redes sociales
- [ ] **Gestión de horarios** de publicación
- [ ] **Cola de publicaciones** pendientes
- [ ] **Panel de administración** avanzado
- [ ] **API REST** para integraciones externas

## 🔒 **Sistema de Seguridad Implementado**

### **Autenticación de Dos Factores (2FA)**
- ✅ **Activación opcional** para usuarios
- ✅ **Verificación obligatoria** en rutas protegidas
- ✅ **Códigos QR** para configuración fácil
- ✅ **Middleware de protección** para rutas sensibles
- ✅ **Integración completa** con login tradicional y Google OAuth

### **Integración OAuth 2.0**
- ✅ **LinkedIn** - Funcionando completamente
- ✅ **Mastodon** - Funcionando completamente
- ✅ **Twitter** - Estructura implementada (requiere HTTPS)
- ✅ **Google** - Funcionando completamente

---

<div align="center">
  <p>Desarrollado usando Laravel y Tailwind CSS</p>
  <p><strong>SocialHub Manager</strong> - Gestiona tus redes sociales desde un solo lugar</p>
</div>