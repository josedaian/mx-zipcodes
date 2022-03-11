## ZipCodes
### Características
- Laravel 9.2
- MySql 8.0
- PHP 8.1
- Redis 6.2.6

### Resumen
Se separan los datos relacionados a los zipcodes en tablas con sus respectivas relaciones.
Para las consultas se utilizó Eloquent siguiendo la manera más óptima de cargar las realiciones con [Eager Loading](https://laravel.com/docs/9.x/eloquent-relationships#eager-loading).
También se implemento el caché de los resultados con [Redis](https://redis.io/).
De ésta manera optimizamos tanto la consulta a la base de datos, como así también el resultado que retorna y lo guardamos en caché.