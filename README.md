# Diaring

## Ejecutar en otro PC con XAMPP

1. Instala XAMPP con Apache, PHP y MySQL/MariaDB.
2. Copia esta carpeta dentro de `C:\xampp\htdocs\Diaring`.
3. Inicia Apache y MySQL desde el panel de XAMPP.
4. Abre `http://localhost/phpmyadmin/`.
5. Importa el archivo `database/diaring.sql`.
   El archivo crea y selecciona automáticamente la base `diaring`.
6. Abre `http://localhost/Diaring/php/index.php`.

La conexión prueba primero el puerto `3307` y después el `3306`, que es el puerto habitual de XAMPP.

Si el usuario root tiene contraseña o usa otro puerto, define estas variables de entorno antes de abrir el proyecto:

- `DIARING_DB_HOST`
- `DIARING_DB_USER`
- `DIARING_DB_PASSWORD`
- `DIARING_DB_NAME`
- `DIARING_DB_PORT`

La base de datos y los usuarios no se sincronizan con GitHub. Cada instalación necesita importar el SQL por separado.
