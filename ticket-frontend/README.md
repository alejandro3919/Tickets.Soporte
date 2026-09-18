Sistema de Tickets para Soporte Técnico

Mis Datos
Nombre: Alejandro Sánchez Carrizalez
Puesto al que postulo: Desarrollador Junior Full Stack
Herramientas usadas: Laravel 12, Vue 3, Vite y XAMPP con MySQL.

Como tome las decisiones del proyecto

La Base de Datos con MySQL: Configuré el proyecto para conectarse directamente al MySQL que viene con XAMPP. Todo el diseño de las tablas se crea de forma automática usando las migraciones de Laravel, así no hay que estar importando archivos sql a mano.

Estructura limpia: Separé por completo el servidor backend que corre en el puerto 8000 de la interfaz visual de Vue que corre en el puerto 5173. Para que pudieran comunicarse sin bloqueos en el buscador, activé los permisos de CORS usando la herramienta nativa de Laravel de la aplicación.

Sobre Laravel Sanctum: Al instalar las rutas de la API, dejé preparado el ambiente de Sanctum en la base de datos y se vera en la tabla de tokens creada.

Correcciones después de la primera revisión

Después de la primera revisión, corregí lo siguiente:
Faltaba el método store() en el controlador de tickets, por lo que el formulario mandaba el POST pero Laravel no lo procesaba. Ya está agregado con su validación correspondiente.
Agregué la fecha de creación en el listado de tickets.
Cambié los indicadores de prioridad y estado para que se distingan por color en vez de solo texto plano.
Rediseñé la interfaz completa para que se viera más elegante y amena al usuario y no los coloes fuertes que se nostraban.

Guía paso a paso para correrlo en tu computadora

Requisitos previos
Asegúrarse de tener encendido tu panel de XAMPP con Apache y MySQL activos, tener Composer y Node.js instalados.

Levantar el Backend con Laravel 12
Abrir una terminal, entra a la carpeta del servidor escribiendo el comando: cd ticket-backend
Descarga los paquetes necesarios de PHP escribiendo el comando: composer install
Configura tus accesos. Si revisas mi archivo .env, verás la conexión local a MySQL apuntando a XAMPP:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ticket_backend
DB_USERNAME=root
DB_PASSWORD=

Entra a tu phpMyAdmin, crea una base de datos vacía llamada ticket_backend y luego ejecuta este comando en tu terminal para crear las tablas automáticamente: php artisan migrate
Enciende el servidor del backend escribiendo el comando: php artisan serve

Levantar el Frontend con Vue 3 y Vite
Abre una segunda terminal diferente para no apagar Laravel y entra a la carpeta visual escribiendo el comando: cd ticket-frontend
Instala los paquetes de Node correspondientes escribiendo el comando: npm install
Arranca el servidor de desarrollo escribiendo el comando: npm run dev
Ingresa a la direccion: http://localhost:5173/
