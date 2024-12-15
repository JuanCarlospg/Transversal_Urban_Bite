# Transversal_Urban_Bite
# El Manantial - Proyecto de Reservas y Administración de Recursos

## Objetivo
Mejorar las funcionalidades del Proyecto 01 para permitir la reserva anticipada de mesas y recursos, así como ofrecer opciones de administración de datos (usuarios, recursos, etc.) desde la aplicación. Además, se debe incorporar el uso de nuevas técnicas de programación, como el acceso a bases de datos con PDO.

## Descripción de la Actividad
A partir del Proyecto 01 realizado con el grupo, se solicita añadir las siguientes funcionalidades:

### Funcionalidades principales:
1. **Reservas de recursos:** Permitir la reserva de un recurso (por ejemplo, cambreros) en un día y franja horaria específicos.
2. **CRUD de usuarios:** Crear, leer, actualizar y eliminar usuarios (como cambreros, gerentes, personal de mantenimiento) por parte del administrador de la web.
3. **CRUD de recursos:** Crear, leer, actualizar y eliminar recursos (como salas, mesas, sillas) por parte del administrador de la web.
4. **Asociación de imágenes a salas:** Los administradores pueden asociar imágenes a las salas y modificarlas según sea necesario.

### Módulos y Técnicas Involucradas:
- **M2 - Bases de Datos:** Ampliar la base de datos según sea necesario para soportar las nuevas funcionalidades.
- **M6 - Desarrollo Web en Entorno Cliente:** Uso de JavaScript para realizar acciones dinámicas sobre la misma página, como la validación de formularios y la implementación de SweetAlerts.
- **M7 - Desarrollo Web en Entorno Servidor:** Utilización de PDO para la conexión a la base de datos y la ejecución de consultas SQL.
- **M8 - Despliegue de Aplicaciones Web:** Generar un nuevo repositorio en GitHub y mantenerlo sincronizado con el repositorio local.
- **M9 - Diseño de Interfaces:** Mantener un aspecto homogéneo tanto en la parte de producción (reservas) como en la parte administrativa (CRUDs).

---

## Instrucciones de Uso

### Instalación:
1. **Clonación del repositorio:**
   ```bash
   git clone https://github.com/JuanCarlospg/Transversal_Urban_Bite.git
   ```

2. **Configuración de la base de datos:**
   - Importa el archivo `bd.sql` situado en la raíz del proyecto para configurar las tablas necesarias.

3. **Despliegue local:**
   - Ejecuta un servidor local con soporte PHP, como XAMPP o Laragon.

4. **Acceso:**
   - Abre el navegador en: `http://localhost/<nombre_del_proyecto>`

### Usuarios para Pruebas:

#### Administrador:
- **Usuario:** Admin
- **Contraseña:** asdASD123

#### Camarero:
- **Usuario:** Olga
- **Contraseña:** asdASD123

---

## Tecnologías Utilizadas
1. **Frontend:**
   - HTML5, CSS3, Bootstrap 5
   - JavaScript con SweetAlert

2. **Backend:**
   - PHP con PDO

3. **Base de datos:**
   - MySQL

4. **Control de versiones:**
   - Git (local y GitHub)

---

## Validaciones con JavaScript
- Validación de campos obligatorios.
- Validación de formatos (correos, contraseñas, etc.).
- Alertas visuales para acciones críticas o errores.

---

## Desarrollo Dinámico

### SweetAlert
Se utiliza para mostrar:
- Confirmación antes de eliminar registros.
- Notificaciones de éxito o error.

### Ejemplo de Código

**Validación con SweetAlert:**
```javascript
Swal.fire({
  title: '¿Estás seguro?',
  text: "¡Esta acción no se puede deshacer!",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Sí, elimínalo!'
}).then((result) => {
  if (result.isConfirmed) {
    // Acción para eliminar
  }
});
```

---

## Notas Finales
- Para cualquier problema o duda, consulta la documentación en las siguientes referencias:
  - [PHP PDO](https://www.php.net/manual/en/book.pdo.php)
  - [Bootstrap](https://getbootstrap.com/)
  - [MySQL](https://dev.mysql.com/doc/)
