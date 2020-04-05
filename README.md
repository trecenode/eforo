# Eforo 4.0
La idea es rehacer el eforo creado por Electros.net hace más de 15 años.


# Indice
Léeme - eForo v3.1

* Antes de comenzar
* Instalación típica
* Actualización
* Integración con web
* Uso con mod_rewrite de Apache

## Antes de comenzar

Una vez que has descomprimido los archivos en tu disco duro, deberás modificar el archivo config.php con los datos de conexión a la base de datos. Antes de hacer una instalación o actualización cerciórate de lo siguiente:

### ¿Tienes un respaldo reciente de tu base de datos?

Por un descuido durante la actualización puede llegar a pasar que se corrompa información o se pierda, por lo tanto recomiendo ampliamente realizar un respaldo de tu base de datos antes de realizar cualquier actualización.

### ¿Utilizas algún sistema de usuarios de www.electros.net?

Si utilizas algún script como eUsuarios v1.0, Registro de usuarios v1.2, o cualquier versión reciente y ya tienes un gran número de usuarios registrados, puede que desees unir eForo a tu sistema de usuarios, para hacer esto modifica el archivo foroconfig.php y en la variable **$tabla_usuarios** indica el nombre de la tabla donde se almacenan tus usuarios que comúnmente es usuarios. Con esto todos los usuarios registrados de tu web estarán registrados automáticamente en el foro.

### ¿En alguna versión anterior de eForo ya usabas el modo con compatibilidad con el sistema de usuarios de tu web?

Si ya utilizabas la compatibilidad con el sistema de usuarios de tu web, repite el procedimiento anterior.

Ya que hemos realizado estos pasos básicos, procedemos con la instalación o actualización.
##   Instalación típica
1. Sube el contenido de la carpeta eForo_v3.1 directamente a la carpeta principal de tu web, no se recomienda subir la carpeta en sí, esto es para facilitar el uso de las opciones como integración con web y/o uso con mod_rewrite de Apache.
1. Dale permiso CHMOD 777 a la carpeta eforo_adjuntos y a la carpeta avatares que está dentro de eforo_imagenes, para aplicar este permiso utiliza algún cliente FTP, haz click derecho sobre el archivo del lado servidor y busca la opción de CHMOD, Permisos o Propiedades.
1. Ejecuta el archivo instalar.php y sigue las instrucciones.
1. Una vez que ha finalizado la instalación, elimina los archivos instalar.php, actualizar.php y la carpeta eforo_parches, es muy importante este paso, ya que de otra forma tu foro será vulnerable a ataques.
1. Disfruta de eForo.

##    Actualización

Desde cualquier versión sea la v.2.2, v.2.2.1 o v3.0, deberás respaldar la carpeta avatares que se encuentra en eforo_imagenes, te recomiendo cambiar el nombre de esta última carpeta a eforo_imagenes2, una vez que hayas subido los archivos nuevos podrás mover la carpeta avatares y por último eliminar eforo_imagenes2.

Los parches se aplican en orden secuencial, si tienes la versión v.2.2.1 deberás primero aplicar este parche y posteriormente el parche para la versión v3.0. Para la actualización sigue los siguientes pasos:

1. Elimina todos los archivos del foro anterior (excepto la carpeta eforo_imagenes2), sube el contenido de la carpeta eForo_v3.1 al directorio raíz del servidor (no subas la carpeta).
1. Mueve la carpeta avatares de eforo_imagenes2 y sobreescribe la que está en eforo_imagenes, con esto restaurarás todos los avatares de tus usuarios.
1. Dale permiso CHMOD 777 a la carpeta eforo_adjuntos y a la carpeta avatares.
1. Ejecuta el archivo actualizar.php y selecciona el parche que corresponde a tu actual versión.
1. Si usas la compatibilidad con el sistema de usuarios indica la tabla donde se almacenan tus usuarios, es muy importante este paso ya que en caso de error no podrás volver a ejecutar el parche o corromperás la base de datos.
1. Una vez terminada la actualización, elimina los archivos instalar.php, actualizar.php y la carpeta eforo_parches.
1. Disfruta de eForo.

##    Integración con web

La integración con web permite insertar eForo como una nueva sección de tu web, esto es, si usas enlaces como este index.php?id=seccion, todas las URLs de eForo apuntarán a esta dirección y por lo tanto eForo podrá ser utilizado dentro del mismo diseño de tu web.

Este proceso es algo complicado, sin embargo eForo está preparado para usar esta opción sin ningún problema, para hacerlo deberás hacer lo siguiente:

1. Modifica el archivo foroconfig.php, si utilizas enlaces del tipo index.php?id=seccion la variable $u deberá tener los siguientes valores: $u = array('index.php?id=','','&','&','=','') (para la integración con web, sólo se deben modificar los primeros 3 valores).
1. Elimina el contenido de los archivos cabecera.pta y piedepagina.pta localizados en eforo_plantillas/electros.
1. En tu página principal, en este caso index.php, inserta el siguiente código entre las etiquetas <head> y </head>:
`<link rel="stylesheet" type="text/css" href="eforo_plantillas/electros/estilos/electros.css">`
1. Crea un enlace hacia eForo (ej. index.php?id=foro).
1. Sube los archivo modificados foroconfig.php, index.php y cualquiera que hayas tenido que modificar.
1. Si todo está correcto verás a eForo integrado con tu web con su diseño original, haz click en los enlaces para verificar que la integración fue realizada correctamente.

Si decides utilizar otra plantilla, deberás realizar los pasos del 2 hasta el final, pero tomando en cuenta que se está trabajando con una nueva plantilla. Los archivos cabecera.pta y piedepagina.pta los vas a encontrar en eforo_plantillas/[plantilla] y el estilo será eforo_plantillas/[plantilla]/estilos/[plantilla].css
