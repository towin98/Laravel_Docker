# Usamos la imagen oficial de PHP 8.2 con FPM (FastCGI Process Manager) basada en Alpine Linux.
FROM php:8.2-fpm-alpine

# Definimos una variable de entorno para aceptar las licencias requeridas por los paquetes de Microsoft.
# Esto es necesario para evitar que se requiera intervención del usuario durante la instalación.
ENV ACCEPT_EULA=Y

# Instalamos herramientas y bibliotecas necesarias para las extensiones de PHP que se instalarán más adelante.
# - bash: Shell para ejecutar scripts y comandos.
# - gnupg: Utilidad para importar y verificar firmas GPG (usado para verificar la integridad de los paquetes).
# - less: Herramienta para paginar contenido (útil para ver archivos grandes).
# - libpng-dev: Biblioteca de desarrollo para trabajar con imágenes PNG.
# - libzip-dev: Biblioteca de desarrollo para manejar archivos ZIP.
# - su-exec: Herramienta para ejecutar comandos como un usuario diferente.
# - unzip: Herramienta para descomprimir archivos ZIP.
RUN apk add --update bash gnupg less libpng-dev libzip-dev su-exec unzip

# Instalamos las dependencias necesarias para las extensiones de PHP para conectar con SQL Server.
# Descargamos los paquetes de Microsoft que incluyen el controlador ODBC y las herramientas mssql.
# - msodbcsql18: Controlador ODBC de Microsoft para SQL Server.
# - mssql-tools18: Herramientas de línea de comandos para SQL Server (por ejemplo, sqlcmd).
# También descargamos las firmas de los archivos APK (.sig) para verificar su integridad antes de la instalación.
RUN curl -O https://download.microsoft.com/download/3/5/5/355d7943-a338-41a7-858d-53b259ea33f5/msodbcsql18_18.3.2.1-1_amd64.apk \
    && curl -O https://download.microsoft.com/download/3/5/5/355d7943-a338-41a7-858d-53b259ea33f5/mssql-tools18_18.3.1.1-1_amd64.apk \
    && curl -O https://download.microsoft.com/download/3/5/5/355d7943-a338-41a7-858d-53b259ea33f5/msodbcsql18_18.3.2.1-1_amd64.sig \
    && curl -O https://download.microsoft.com/download/3/5/5/355d7943-a338-41a7-858d-53b259ea33f5/mssql-tools18_18.3.1.1-1_amd64.sig \
    && curl https://packages.microsoft.com/keys/microsoft.asc  | gpg --import - \
    # Verificamos que los paquetes descargados coinciden con las firmas correspondientes.
    && gpg --verify msodbcsql18_18.3.2.1-1_amd64.sig msodbcsql18_18.3.2.1-1_amd64.apk \
    && gpg --verify mssql-tools18_18.3.1.1-1_amd64.sig mssql-tools18_18.3.1.1-1_amd64.apk \
    # Instalamos los paquetes descargados y verificamos su integridad.
    && apk add --allow-untrusted msodbcsql18_18.3.2.1-1_amd64.apk mssql-tools18_18.3.1.1-1_amd64.apk \
    # Eliminamos los archivos .apk y .sig para reducir el tamaño de la imagen.
    && rm *.apk *.sig

# Copiamos el script 'install-php-extensions' desde la imagen mlocati/php-extension-installer.
# Este script facilita la instalación de extensiones de PHP en contenedores Docker basados en Alpine.
# El script se copia a nuestro contenedor para ser utilizado más adelante.
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/install-php-extensions

# Damos permisos de ejecución al script 'install-php-extensions' para asegurarnos de que se pueda ejecutar.
# Luego, ejecutamos el script para instalar una lista de extensiones de PHP necesarias para nuestra aplicación.
# Las extensiones que se instalarán son: 
# - bcmath: Soporte para operaciones matemáticas de precisión arbitraria.
# - ds: Extensión para estructuras de datos.
# - exif: Soporte para leer metadatos EXIF de imágenes.
# - gd: Soporte para crear y manipular imágenes.
# - intl: Extensión para el manejo de datos internacionales (fechas, números, etc.).
# - opcache: Caché de código intermedio para mejorar el rendimiento.
# - pcntl: Soporte para trabajar con procesos en PHP.
# - pdo_sqlsrv: Controlador PDO para conectar con bases de datos SQL Server.
# - redis: Extensión para trabajar con Redis (almacenamiento en memoria).
# - sqlsrv: Controlador para conectar con bases de datos SQL Server.
# - zip: Soporte para trabajar con archivos ZIP.
RUN chmod uga+x /usr/bin/install-php-extensions \
    && sync \
    && install-php-extensions bcmath ds exif gd intl opcache pcntl pdo_sqlsrv redis sqlsrv zip

# Establecemos el directorio de trabajo en /var/www, donde normalmente se encuentra el código de la aplicación.
WORKDIR /var/www

# Creamos un grupo de usuarios llamado 'laravel' con ID 1000.
# Luego, creamos un usuario 'laravel' que será parte de ese grupo.
# Este usuario tendrá acceso al directorio de la aplicación, pero no tendrá privilegios de administrador.
RUN addgroup -g 1000 laravel && adduser -G laravel -g laravel -s /bin/sh -D laravel

# Cambiamos la propiedad del directorio /var/www/html para que el usuario 'laravel' tenga acceso a él.
RUN chown -R laravel /var/www/html

# Cambiamos el usuario activo al usuario 'laravel' para que no se ejecute como root, lo cual es más seguro.
USER laravel
