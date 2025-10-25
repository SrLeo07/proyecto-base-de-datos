Resumen rápido — poner Laravel a usar Oracle (pasos seguros)

1) Crear usuario Oracle para la app (ejecutar como DBA):
   - Abre SQL*Plus o SQL Developer y ejecuta el script `database/sql/create_oracle_user.sql`.

2) Actualiza `.env` con las credenciales del nuevo usuario (ejemplo):

DB_CONNECTION=oracle
DB_HOST=Ventilador
DB_PORT=1522
DB_DATABASE=XE
DB_SERVICE_NAME=XEPDB1
DB_USERNAME=leo
DB_PASSWORD=0770

3) Limpiar caché de Laravel (PowerShell dentro del proyecto):

php artisan config:clear
php artisan cache:clear
php artisan route:clear
if (Test-Path bootstrap\cache\config.php) { Remove-Item bootstrap\cache\config.php -Force }

4) Verificar la conexión y estado de migraciones:

php artisan tinker --execute="var_export(config('database.connections.oracle'));"
php artisan migrate:status --database=oracle

5) Ejecutar migraciones (si es necesario):

php artisan migrate --database=oracle
# o para recrear tablas (cuidado: borra datos)
php artisan migrate:fresh --database=oracle

6) Notas de seguridad
- No uses `SYSTEM` como usuario de la aplicación.
- Usa contraseñas fuertes y no las subas al repositorio.
- Si prefieres, crea un rol restringido y otórgale solo los permisos necesarios.

7) Si ves errores de conexión
- Verifica que Oracle esté escuchando en `Ventilador:1522` y que tu usuario tenga permisos.
- Asegúrate de que la extensión OCI8/PDO_OCI esté instalada y que PHP puede usarla.

8) Volver a producción
- Cuando todo funcione, elimina cualquier conexión temporal en `config/database.php` y documenta los pasos en `README.md`.

