# EduSystem

**Versión:** 4.2.5  
**Autor:** EduSof  
**URI del Autor:** https://edusof.com/  
**Licencia:** GPL2  

## Descripción

EduSystem transforma tu sitio de WordPress en un ecosistema educativo completo, profesional y escalable. Este plugin permite gestionar instituciones educativas, estudiantes, profesores, cursos, calificaciones y mucho más, integrándose perfectamente con WordPress.

### Características principales:
- Gestión programas académicos
- Administración de estudiantes
- Sistema de calificaciones y evaluaciones
- Integración con Moodle (opcional)
- Gestión de pagos y becas
- Notificaciones y correos electrónicos
- Reportes y estadísticas
- Y mucho más...

## Instalación

1. Descarga el archivo ZIP del plugin.
2. Ve a tu panel de administración de WordPress.
3. Navega a **Plugins > Añadir nuevo**.
4. Haz clic en **Subir plugin** y selecciona el archivo ZIP.
5. Haz clic en **Instalar ahora**.
6. Una vez instalado, activa el plugin desde la página de plugins.

<!-- 
### Requisitos del sistema:
- WordPress 5.0 o superior
- PHP 7.4 o superior
- MySQL 5.6 o superior 

## Uso

Después de activar el plugin, encontrarás un nuevo menú "EduSystem" en tu panel de administración. Desde allí, puedes configurar y gestionar todos los aspectos del sistema educativo.

### Configuración inicial:
1. Ve a **EduSystem > Instituto** para configurar los detalles de tu institución.
2. Agrega programas académicos en **EduSystem > Programa**.
3. Gestiona usuarios (estudiantes, profesores) desde **EduSystem > Usuarios**.

Para más detalles, consulta la documentación completa en [https://edusof.com/](https://edusof.com/).

## Preguntas Frecuentes

### ¿Es compatible con mi tema de WordPress?
Sí, EduSystem está diseñado para ser compatible con la mayoría de los temas de WordPress. Sin embargo, se recomienda usar un tema compatible con WooCommerce si planeas integrar pagos.

### ¿Cómo integro con Moodle?
EduSystem incluye módulos para integración con Moodle. Ve a **EduSystem > Moodle** para configurar la conexión.

### ¿Ofrecen soporte?
Sí, ofrecemos soporte técnico. Contacta a través de nuestro sitio web o foro de soporte.

## Capturas de Pantalla

1. **Panel de administración principal** - Vista general del dashboard de EduSystem.
2. **Gestión de estudiantes** - Interfaz para añadir y editar estudiantes.
3. **Reportes** - Generación de reportes académicos.
-->

## Registro de Cambios

### 4.2.6
- Implementación de sistema de recuperación de materias reprobadas ("raspadas") directamente desde el escritorio del estudiante.
- Integración con WooCommerce para la compra de materias, permitiendo la inscripción automática para el siguiente periodo académico una vez procesado el pago.
- Oferta los cursos y electivas en caso de hagotar los cupos disponibles.

### 4.2.5 sistema de actualizaciones
- Sistema de actualizacion.
- Bug resueltos.

### 4.2.4
- Se resolvio bug de compatibilidad con los metodos de pago y los pagos divididos
- Se restructuro la funcion de guardado y funcionanmiento de las becas

### 4.2.3
- Permite agregar cuotas al programa desde el panel de administracion.
- Se corrigio los ordenes por defecto al pagar por el lado del cliente.

### 4.2.2
- Optimización de Logs: Refactorización del sistema de registros de EduSystem para mejorar la trazabilidad de eventos.
- Soporte de Identificadores Cero: Se corrigió la validación de Payment Links para permitir planes de pago con identificador 0.
- Gestión de Pasarela: Implementación de un interruptor (toggle) para habilitar o deshabilitar la cuenta de Stripe por defecto.
- Se reparo el boton generar la orden a la cuota desde administracion.

### 4.2.1
- Corrección de errores menores:
  - Mejora en el procesamiento de estudiantes que ya no tienen términos en casos AES.
  - Corrección del bloqueo de materias al superar el límite de reinscripciones.
- Se ajusto la actualizacion manual de la matriz del estudiante.


### 4.2.0
- Actualización del método para generar la matriz de estudiantes.
- Implementación del bloqueo automático de materias cuando se excede el límite de reinscripciones.


<!--
## Licencia

Este plugin está licenciado bajo GPL2. Consulta el archivo LICENSE para más detalles.
-->

## Créditos

Desarrollado por EduSof.  
Sitio web: [https://edusof.com/](https://edusof.com/)