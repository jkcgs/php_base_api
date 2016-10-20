## Proyecto base para API en PHP

Este repositorio se puede utilizar como una base para una API
en PHP, sin tener que preocuparse de configurar rutas en el
servidor web.

Por ejemplo, con el dominio "ejemplo.com", y la api en la carpeta
"api", la URL para cargar el módulo "hello/world" sería
`http://ejemplo.com/api/?hello/world`

### Ruteo de API

#### Ejemplo básico: hello/world
Los módulos están en la carpeta `modules` y se ordenan según
sus carpetas. Por ejemplo, `hello/world` carga el archivo
`world.php` dentro de la carpeta `modules/hello`. Si existe
el archivo `modules/hello/index.php` se cargará automáticamente,
antes de cargar el módulo solicitado.

#### Ejemplo de argumentos: hello/world/user (user es un argumento)
Siguiendo el ejemplo anterior, si el módulo `hello/world/user`
no existe, pero `hello/world` si, el resto de los componentes de
la ruta se consideran argumentos. En este caso, el módulo sería
`hello/world`, y `user` sería un argumento.

Al módulo se le pasan los argumentos mediante el arreglo `$args[]`,
en el cual son ennumerados en orden según se han ingresados en la
URL. En este caso, el arreglo se compondría así:

`$args = array( "user" )`

Si le agregamos otro argumento, por ejemplo
el número 1, quedando como `hello/world/user/1`, el arreglo quedaría:

`$args = array( "user", "1" )`

Nota: todos los argumentos son `string`s.