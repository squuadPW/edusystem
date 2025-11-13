# Edusystem Logs

## Breve descripción

Módulo para registrar, visualizar y eliminar eventos del sistema Edusystem (login/logout, errores, cambios en datos de estudiantes, etc.). Provee:
- Inserción de registros desde funciones.
- Página en el área de administración para filtrar y explorar logs.
- Página adicional para eliminar registros según criterios.
- Enlace en la pantalla de perfil para ver los logs de un usuario concreto.

## Índice

- [Requisitos](#requisitos)  
- [Instalación rápida](#instalación-rápida)  
- [Cómo funciona (resumen)](#cómo-funciona-resumen)  
- [Comportamiento por defecto (logs automáticos)](#comportamiento-por-defecto-logs-automáticos)
- [Tipos de log y traducciones](#tipos-de-log-y-traducciones)
- [Funciones públicas y ejemplos](#funciones-públicas-y-ejemplos)  
- [Interfaz de administración](#interfaz-de-administración) 
- [Eliminación de logs](#eliminación-de-logs)   
- [Roles y capacidades](#roles-y-capacidades)
- [Esquema de la base de datos](#esquema-de-la-base-de-datos)  

## Requisitos

- WordPress (funciones: add_action, add_menu_page, wp_enqueue_script, WP_List_Table, etc.)
- Definir en el plugin principal (si no están):
  - `EDUSYSTEM__FILE__`
  - `EDUSYSTEM_PATH`
  - `EDUSYSTEM_URL` (opcional, para assets)

## Instalación rápida

1. Copiar la carpeta `edusystem_log` al plugin principal.  
2. Asegurar las constantes mencionadas.  
3. Activar el plugin para ejecutar `register_activation_hook()` y crear la tabla (o ejecutar la SQL corregida manualmente).  
4. Abrir la página admin: Menú → Edusystem Logs.

## Cómo funciona (resumen)
- Generación manual de logs: al invocar la función pública
  `edusystem_get_log( $message, $type, $user_id )` con los datos correctos, el módulo inserta un registro en la tabla de logs. Es decir, llamar a esa función con mensaje, tipo y (opcional) user_id genera un log.
- Visualización: la página de administración carga `Edusystem_Log_Table`, aplica filtros (usuario, tipo, rango de fechas, búsqueda) y muestra los registros paginados.

## Comportamiento por defecto (logs automáticos)

El módulo registra de forma automática los siguientes eventos (sin intervención adicional):
- Inicio de sesión: hook `wp_login` — genera un log de tipo `login`.
- Cierre de sesión: mecanismo con `clear_auth_cookie` + `wp_logout` — genera un log de tipo `logout`.
- Actualización de datos de estudiante desde administración: hook `edusystem_save_student_data` — genera un log de tipo `save_student` o similar.

## Tipos de log y traducciones

Los tipos de logs registrados por el módulo se definen en la constante `EDUSYSTEM_TYPE_LOGS`.  
Si quieres que en la interfaz se muestre un texto distinto al identificador del tipo (y traducible), registra el tipo en esa constante asociándolo a su etiqueta traducida.

Ejemplo de definición (poner en el plugin principal o en este módulo):
```php
if ( ! defined( 'EDUSYSTEM_TYPE_LOGS' ) ) {
    define( 'EDUSYSTEM_TYPE_LOGS', serialize( array(
        'login'        => __( 'Inicio de sesión', 'edusystem' ),
        'logout'       => __( 'Cierre de sesión', 'edusystem' ),
        'save_student' => __( 'Guardar datos de estudiante', 'edusystem' ),
        'error'        => __( 'Error', 'edusystem' ),
    ) ) );
}
```

*Comportamiento:*
- Si un tipo está presente en `EDUSYSTEM_TYPE_LOGS`, la función `edusystem_get_log_type_label( $type )` devolverá la etiqueta registrada (traducida).  
- Si no está registrado, la función devolverá el identificador del tipo tal cual (fallback).  

*Personalización:*
- Puedes filtrar las etiquetas con el filtro `edusystem_get_log_type_label` para cambios dinámicos desde plugins/tema.

## Funciones públicas y ejemplos

- edusystem_get_log( $message, $type = 'info', $user_id = null )  
  Inserta un registro. Ejemplo:
  ```php
  edusystem_get_log( 'Alumno actualizado: ID 123', 'save_student', $student_user_id );
  ```

- edusystem_get_log_type_label( $type )  
  Devuelve legible/traducida para un tipo de log definido según `EDUSYSTEM_TYPE_LOGS`.
  Si el tipo no existe, devuelve el identificador recibido.

  Ejemplo de uso (código PHP):
  ```php
    // Definición de tipos (si no está definida en el plugin principal)
    if ( ! defined( 'EDUSYSTEM_TYPE_LOGS' ) ) {
        define( 'EDUSYSTEM_TYPE_LOGS', serialize( array(
            'login'        => __( 'Inicio de sesión', 'edusystem' ),
            'logout'       => __( 'Cierre de sesión', 'edusystem' ),
            'save_student' => __( 'Guardar datos de estudiante', 'edusystem' ),
        ) ) );
    }

    // Uso de la función
    echo esc_html( edusystem_get_log_type_label( 'login' ) );        // Imprime: Inicio de sesión
    echo esc_html( edusystem_get_log_type_label( 'save_student' ) );// Imprime: Guardar datos de estudiante
    echo esc_html( edusystem_get_log_type_label( 'unknown' ) );     // Imprime: unknown (fallback)
  ```

## Interfaz de administración
- Página: "Edusystem Logs" (registrada con add_menu_page). 
    Filtros por: `tipo de log`, `Fecha` y `busqueda por nombre y apellido del usuario`.  
- En la página de administración de usuarios, existe un enlace que permite acceder directamente a los registros (logs) de un usuario específico.

## Interfaz de administración

- **Página:** *Edusystem Logs* (registrada con `add_menu_page`).  
  - Filtros disponibles:  
    - `Tipo de log`  
    - `Fecha`  
    - `Búsqueda por nombre y apellido del usuario`  

- En la página de administración de usuarios se incluye un enlace que permite acceder directamente a los registros (logs) de un usuario específico.

## Eliminación de logs

El módulo incluye una página específica en el área de administración para **eliminar registros de logs**.  
Esta funcionalidad está restringida a usuarios con rol **Administrador**.

### Filtros disponibles
- **Usuario:** selector con búsqueda dinámica (Select2 AJAX) que permite encontrar usuarios por nombre, apellido o correo.  
- **Rango de fechas:** dos campos de tipo `date` (inicio y fin). Si se selecciona uno, el otro se marca como obligatorio.  
- **Combinación de filtros:** se pueden aplicar ambos filtros simultáneamente para eliminar solo los registros de un usuario en un rango de fechas específico.

### Comportamiento
- Si se selecciona únicamente un usuario, se eliminan todos sus registros.  
- Si se selecciona únicamente un rango de fechas, se eliminan todos los registros en ese rango.  
- Si se seleccionan ambos, se eliminan solo los registros del usuario en ese rango.  
- Si no se selecciona ningún criterio, no se ejecuta la eliminación y se muestra un mensaje de error.

### Seguridad
- Solo los administradores pueden acceder a esta página.  
- La acción de eliminación se ejecuta mediante consultas SQL seguras (`$wpdb->prepare`).  
- Se muestra un mensaje de confirmación al finalizar la operación.

## Roles y capacidades

El módulo define una capacidad personalizada llamada **`manager_logs`**.  
- Se asigna automáticamente al rol **Administrador** al activar el plugin.  
- Se elimina del rol al desactivar el plugin.  
- Esta capacidad controla el acceso al menú *Edusystem Logs* y sus subpáginas (incluida la de eliminación de logs).  

De esta forma, solo los usuarios con la capacidad `manager_logs` pueden visualizar y gestionar los registros desde el área de administración.

## Esquema de la base de datos

Nombre de Tabla: `edusystem_log` con el prefijo de la db, Ejemplo: `{$wpdb->prefix}edusystem_log`  
Columnas:
- id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY  
- user_id INT(11) NULL  
- message TEXT NOT NULL  
- type VARCHAR(100) NOT NULL  
- ip VARCHAR(45) NULL  
- meta JSON NULL (opcional)  
- created_at DATETIME DEFAULT CURRENT_TIMESTAMP  
- updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
