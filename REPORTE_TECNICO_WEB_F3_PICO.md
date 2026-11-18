# Reporte técnico y especificación para desarrollo asistido por IA

**Proyecto:** Sitio web dinámico con autenticación de usuarios  
**Versión:** 1.1 — revisión de seguridad (18-11-2026)  
**Objetivo del documento:** Servir como especificación implementable para una IA de desarrollo.  
**Prioridad principal:** Compatibilidad con hosting compartido cPanel y **mínimo número de archivos/inodos** sin sacrificar seguridad, mantenibilidad ni accesibilidad.

## 1. Instrucción principal para la IA desarrolladora

Construye un sitio web dinámico, seguro y responsive con registro, inicio y cierre de sesión, recuperación de contraseña, perfiles de usuario y panel de administración básico. Usa **exclusivamente** el stack obligatorio indicado a continuación. Entrega código funcional, scripts SQL, instrucciones de instalación local y una guía de despliegue a cPanel. No introduzcas dependencias pesadas, herramientas de compilación ni frameworks adicionales sin una justificación técnica y aprobación expresa.

No conviertas este documento en una simple propuesta: úsalo como lista de requisitos y criterios de aceptación del proyecto. Ante detalles de negocio no especificados, usa defaults prudentes y documenta cualquier supuesto.

## 2. Stack tecnológico obligatorio

| Capa | Tecnología | Restricciones |
|---|---|---|
| Backend | **Fat-Free Framework (F3)** | Instalar una versión estable corregida (como mínimo 3.9.3 a fecha de esta revisión) y verificar los avisos de seguridad antes del despliegue; usar núcleo oficial mínimo. |
| Frontend | **Pico CSS** | Utilizar un solo archivo CSS minificado local, siempre que sea viable. |
| Base de datos | **MySQL / MariaDB** | Acceso por PDO y consultas preparadas. |
| Interactividad | **JavaScript nativo (Vanilla JS)** | Sin jQuery, React, Vue, Angular ni dependencias Node en producción. |
| Servidor | **Apache + PHP** | Compatible con hosting compartido y `.htaccess`. |
| Despliegue | **cPanel** | Sin requerir SSH, Docker, acceso root ni procesos persistentes. |

**No utilizar:** CodeIgniter, Laravel, Bootstrap, Tailwind con compilación en servidor, npm/`node_modules` en producción, SPA obligatoria, colas o workers persistentes, WebSockets o componentes que requieran VPS.

## 3. Requisito no funcional prioritario: límite de archivos/inodos

1. Reducir al mínimo razonable el número total de archivos subidos al hosting.
2. No subir repositorios Git (`.git`), pruebas, documentación de dependencias, `node_modules`, cachés locales ni archivos temporales.
3. Preferir la distribución mínima oficial de F3 y evitar complementos innecesarios.
4. No instalar paquetes Composer que no sean estrictamente necesarios. Si se utiliza Composer en desarrollo, subir solo dependencias realmente utilizadas en producción.
5. Entregar un **inventario de archivos de producción** y un script local o procedimiento que cuente archivos y directorios antes del despliegue. Establecer el objetivo inicial de **menos de 100 archivos propios y de terceros en el paquete de despliegue**, salvo justificación explícita; no es una garantía ni incluye archivos generados por el hosting.
6. Evitar caché de archivos y logs de crecimiento indefinido; establecer políticas de retención y considerar el consumo de inodos por sesiones, correo y archivos ajenos al proyecto.
7. Mantener secretos, copias de seguridad, archivos SQL y configuraciones sensibles fuera del directorio público siempre que el hosting lo permita.

## 4. Alcance funcional del MVP

### 4.1 Visitante no autenticado
- Ver una página de inicio pública.
- Acceder a las páginas **Iniciar sesión**, **Registrarse** y **Recuperar contraseña**.
- Navegar en móvil, tablet y escritorio con experiencia accesible.
- Visualizar toda la interfaz en tema oscuro, incluida la experiencia anterior al inicio de sesión.

### 4.2 Usuario registrado
- Crear una cuenta con nombre, correo y contraseña.
- Iniciar sesión y cerrar sesión de manera segura.
- Acceder a un **dashboard privado** protegido por sesión.
- Visualizar y editar nombre de perfil.
- Cambiar la contraseña proporcionando la contraseña actual.
- Solicitar un enlace de restablecimiento de contraseña por correo.

### 4.3 Administrador
- Iniciar sesión y acceder a `/admin`.
- Ver una lista paginada de usuarios.
- Buscar usuarios por nombre o correo.
- Ver estado y rol de cada usuario.
- Activar o desactivar cuentas, evitando desactivar accidentalmente la última cuenta administradora activa.
- Cambiar el rol de un usuario mediante autorización explícita del backend y controles de seguridad.
- Todas las acciones privilegiadas se validan **en el servidor**, no solo ocultando botones en el frontend.

### 4.4 Fuera del alcance inicial
- Pagos, carrito de compras, chats en tiempo real, subida pública de archivos, autenticación social, API pública, CMS completo y editor visual.
- No implementar estas capacidades hasta recibir requisitos específicos.

## 5. Diseño UI/UX y tema oscuro obligatorio

### 5.1 Enfoque visual
- **Tema oscuro como diseño predeterminado obligatorio**, en todas las pantallas, formularios, estados de error, menús y tablas.
- Aplicar Pico CSS con configuración de color oscuro (por ejemplo `data-theme="dark"` en el elemento raíz, de acuerdo con la versión instalada).
- Evitar destellos de tema claro al cargar las páginas; el atributo de tema debe estar presente desde el HTML inicial.
- Usar tipografía de sistema y archivos locales o fuentes del sistema; no depender de Google Fonts.
- Usar componentes semánticos de Pico CSS y un solo `app.css` para ajustes específicos.

### 5.2 Paleta sugerida (ajustable)
- Fondo principal: `#101419`
- Superficie: `#1B222B`
- Texto principal: `#EDF2F7`
- Texto secundario: `#AAB6C5`
- Acento: `#45B6A8`
- Error: `#F08080`
- Bordes: `#34404D`

Verificar contraste de color **WCAG AA** en textos, enlaces, formularios y estados de foco, en vez de asumir que los colores propuestos cumplen automáticamente.

### 5.3 Responsive obligatorio
- Aplicar diseño **mobile-first**.
- Asegurar funcionamiento al menos en anchos de referencia de **320, 375, 768, 1024 y 1440 px**.
- Navegación adaptable: barra superior para escritorio y menú móvil accesible, controlable por teclado.
- Formularios con ancho legible; botones y entradas táctiles cómodas.
- Tablas extensas dentro de contenedores con desplazamiento horizontal controlado, sin desbordar la página.
- Sin scroll horizontal de la página por errores de maquetación.
- El contenido y acciones principales deben ser accesibles sin depender de efectos hover.
- Respetar `prefers-reduced-motion` y utilizar animaciones mínimas.

### 5.4 Vistas mínimas
1. `home` — página pública.
2. `login` — acceso.
3. `register` — alta de cuenta.
4. `forgot-password` — solicitud de restablecimiento.
5. `reset-password` — formulario con token válido.
6. `dashboard` — resumen privado.
7. `profile` — edición de perfil y contraseña.
8. `admin/users` — listado y gestión de usuarios.
9. `403`, `404` y `500` — estados con interfaz consistente; no divulgar datos internos.

## 6. Arquitectura de aplicación

- Arquitectura SSR clásica: el servidor renderiza HTML con vistas PHP; JavaScript se reserva para interacciones progresivas.
- Patrón **MVC ligero**: rutas, controladores, servicios, acceso a datos y vistas.
- Utilizar plantillas PHP sencillas; no añadir un motor de plantillas de terceros si no es necesario.
- Centralizar validación, autorización, configuración y mensajes de error.
- Mantener el backend independiente de la presentación visual.
- No implementar autenticación o verificación de permisos en JavaScript.

### 6.1 Estructura sugerida (optimizar si reduce archivos sin afectar claridad)

```text
proyecto/
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ProfileController.php
│   │   └── AdminController.php
│   ├── Services/
│   │   └── AuthService.php
│   ├── Views/
│   │   ├── layout.php
│   │   ├── home.php
│   │   ├── auth.php
│   │   ├── dashboard.php
│   │   ├── profile.php
│   │   ├── admin_users.php
│   │   └── error.php
│   └── bootstrap.php
├── config/
│   └── config.php
├── lib/
│   └── base.php               # Núcleo oficial de F3, según distribución seleccionada
├── public/
│   ├── .htaccess
│   ├── index.php
│   └── assets/
│       ├── css/
│       │   ├── pico.min.css
│       │   └── app.css
│       └── js/
│           └── app.js
├── database/
│   └── schema.sql
├── .env.example                # Ejemplo, jamás credenciales reales
└── README.md
```

**Nota:** la estructura es orientativa y no presupone que el núcleo concreto de F3 siempre se distribuya con ese nombre de archivo. Verificar en la distribución oficial elegida. En producción, `app`, `config`, `lib` y `database` deben permanecer fuera de la raíz pública; si no se puede configurar el document root, documentar una alternativa segura compatible con cPanel y comprobar mediante HTTP que los archivos internos no son accesibles. No confiar en ocultarlos solamente por su extensión.

## 7. Modelo de datos MySQL

### 7.1 Tabla `users`

| Campo | Tipo sugerido | Regla |
|---|---|---|
| `id` | BIGINT UNSIGNED AUTO_INCREMENT | PRIMARY KEY |
| `name` | VARCHAR(120) | NOT NULL |
| `email` | VARCHAR(190) | NOT NULL, UNIQUE; normalizar antes de almacenar |
| `password_hash` | VARCHAR(255) | NOT NULL; nunca guardar contraseña en claro |
| `role` | ENUM('user','admin') | DEFAULT 'user' |
| `status` | ENUM('active','disabled') | DEFAULT 'active' |
| `created_at` | DATETIME | NOT NULL |
| `updated_at` | DATETIME | NOT NULL |
| `last_login_at` | DATETIME NULL | Opcional |

### 7.2 Tabla `password_resets`

| Campo | Tipo sugerido | Regla |
|---|---|---|
| `id` | BIGINT UNSIGNED AUTO_INCREMENT | PRIMARY KEY |
| `user_id` | BIGINT UNSIGNED | FK a `users` |
| `token_hash` | CHAR(64) | UNIQUE; almacenar hash SHA-256 del token aleatorio |
| `expires_at` | DATETIME | NOT NULL |
| `used_at` | DATETIME NULL | Uso único |
| `created_at` | DATETIME | NOT NULL |

Usar índices apropiados, claves foráneas, motor InnoDB y `utf8mb4`. Mantener tokens de recuperación con vida corta (p. ej. 30 minutos). El servidor debe invalidarlos tras el uso; no almacenar tokens de restablecimiento en texto plano.

### 7.3 Evolución
- Diseñar SQL inicial idempotente cuando sea posible y script de instalación separado de la aplicación pública.
- No permitir que la web pública ejecute recreación de tablas o elevación de privilegios.
- Crear la primera cuenta admin mediante procedimiento **local o de consola controlada**, nunca a través de un registro público con parámetro `role`.

## 8. Rutas esperadas

| Método | Ruta | Acceso | Comportamiento |
|---|---|---|---|
| GET | `/` | Público | Inicio |
| GET, POST | `/register` | Invitado | Registro; validación y protección CSRF |
| GET, POST | `/login` | Invitado | Autenticación |
| POST | `/logout` | Autenticado | Cierre de sesión con CSRF |
| GET, POST | `/forgot-password` | Público | Solicitar restablecimiento |
| GET, POST | `/reset-password` | Público + token válido | Restablecer contraseña |
| GET | `/dashboard` | Autenticado | Panel privado |
| GET, POST | `/profile` | Autenticado | Actualización de datos |
| GET | `/admin/users` | Admin | Usuarios paginados |
| POST | `/admin/users/{id}/status` | Admin | Cambio de estado |
| POST | `/admin/users/{id}/role` | Admin | Cambio de rol |

Se pueden usar rutas distintas si se mantiene la funcionalidad y se documentan. Para operaciones que modifican estado, nunca usar GET.

## 9. Seguridad obligatoria

1. **Contraseñas:** utilizar `password_hash()` y `password_verify()`; elegir Argon2id si el hosting lo soporta; de lo contrario `PASSWORD_DEFAULT`. Nunca utilizar MD5/SHA-1 para contraseñas.
2. **SQL:** PDO con parámetros enlazados; ninguna concatenación directa de datos de usuario en consultas.
3. **XSS:** escape de HTML en vistas, contextual según HTML/atributos; evitar insertar HTML no confiable con `innerHTML`.
4. **CSRF:** token criptográfico para todo formulario que cambie estado, incluidos login, logout, perfil, registro y administración.
5. **Sesiones:** cookies `HttpOnly`, `Secure` bajo HTTPS y `SameSite=Lax` o `Strict` según flujo; regenerar ID al iniciar sesión y al cambiar privilegios; invalidar la sesión al salir.
6. **Autorización:** proteger rutas desde backend; nunca aceptar el rol enviado por el formulario de registro.
7. **Fuerza bruta:** limitar intentos por cuenta e IP con mensajes neutros; tener en cuenta que la implementación debe funcionar sin Redis ni workers. Si se usa almacenamiento de base de datos para límites, definir limpieza y políticas de retención.
8. **Recuperación:** tokens aleatorios generados con `random_bytes`, de un solo uso y caducables; mensajes de respuesta que no revelen si un correo existe.
9. **Errores:** registrar detalles internamente con rotación o límites y mostrar mensajes genéricos al usuario; `display_errors=Off` en producción.
10. **Transporte:** HTTPS obligatorio en producción; no permitir credenciales transmitidas por HTTP.
11. **Configuración:** contraseñas DB y claves secretas fuera de `public`; no incluir secretos en Git ni en archivos descargables.
12. **Cabeceras:** configurar `X-Content-Type-Options`, política `Referrer-Policy`, protección contra framing y CSP compatible con recursos locales. Evaluar HSTS solo cuando HTTPS esté correctamente activo.
13. **Protección de acceso:** bloquear por HTTP los archivos ocultos, configuraciones y recursos privados; deshabilitar directory listing.
14. **Validación:** backend obligatorio y frontend complementario; normalizar email y validar unicidad sin condiciones de carrera.
15. **Correos:** usar un servicio SMTP proporcionado por hosting o configuración confiable; no asumir que `mail()` funciona correctamente. Evitar agregar grandes dependencias si existe una alternativa segura y mantenible.

### 9.1 Matriz de amenazas y respuesta obligatoria

| Amenaza | Implementación obligatoria | Prueba exigida |
|---|---|---|
| Inyección SQL | PDO y consultas preparadas; lista permitida para identificadores, filtros y ordenamiento dinámico | Probar entradas maliciosas en búsqueda, login y parámetros ID; verificar que no se altera la consulta |
| XSS reflejado/almacenado | Escape contextual al mostrar contenido; prohibido usar `raw` o `innerHTML` con datos no confiables | Insertar texto con etiquetas HTML y comprobar que se muestra como texto, no se ejecuta |
| CSRF | Token de sesión impredecible y verificación en el backend para todas las acciones que modifican estado | Solicitud POST sin token o con token incorrecto devuelve rechazo y no modifica datos |
| Fuerza bruta / enumeración | Límites de intentos por cuenta e IP; respuestas uniformes y caducidad de bloqueos | Intentos repetidos producen retraso/bloqueo temporal; la recuperación no revela usuarios existentes |
| Secuestro / fijación de sesión | Cookies seguras, regeneración al autenticar o elevar privilegios; tiempo de inactividad y expiración absoluta | Sesión previa al login pierde validez; logout invalida sesión del servidor |
| IDOR y elevación de privilegios | Comprobación en backend del rol **y de la titularidad del recurso** | Usuario A no puede leer/modificar datos de B ni invocar rutas de administración |
| Restablecimiento de contraseña | Token `random_bytes`, persistido como hash, vencimiento y consumo atómico de un solo uso | Token usado, alterado o expirado falla; enlace no aparece en logs públicos |
| Subida de archivos (si se habilita después) | Lista blanca de MIME y extensión, límites, nombre aleatorio y almacenamiento no ejecutable fuera de `public` | Rechaza PHP disfrazado de imagen y tamaños excesivos |
| Divulgación de información | Errores genéricos; secretos y carpetas internas inaccesibles por HTTP | Solicitar `/config`, archivos `.env`, SQL, logs, backups y PHP interno; todos inaccesibles |
| Dependencias y actualizaciones | Inventario, revisión de CVE y pruebas antes de actualización | Registrar versión de F3/PHP/Pico y evidencia de revisión de avisos |

### 9.2 Reglas de implementación que la IA no puede omitir

1. **No inventar librerías ni APIs de F3.** Contrastar métodos usados con documentación de la versión instalada; entregar pruebas ejecutables.
2. **Validación en servidor obligatoria:** tipo, longitud, formato, rangos, unicidad e integridad referencial. Para datos mostrados en HTML, escapar según contexto; no tratar la simple sanitización de entrada como sustituto.
3. **Consultas SQL:** no interpolar datos de usuario en SQL, incluso en `LIKE`, `LIMIT`, ordenamientos o nombres de columna; solo identificadores provenientes de listas de valores permitidos.
4. **Hashing:** usar `password_hash()` con Argon2id si está disponible; verificar con `password_verify()` y considerar `password_needs_rehash()` en el login; limitar tamaño de contraseñas de forma razonable para prevenir abuso.
5. **Sesiones:** usar ID regenerado después del login y del cambio de rol; cerrar sesión en el servidor; impedir que usuarios deshabilitados continúen operando. Configurar `session.use_strict_mode` cuando aplique y usar HTTPS real detrás de proxy si existe.
6. **CSRF:** comprobar token para cada operación que cambie estado, **independientemente de si fue enviada por formulario o `fetch()`**. No realizar cambios mediante GET.
7. **Permisos:** denegar por defecto; no confiar en valores `role`, `user_id`, `status` ni rutas recibidos del cliente. Validar autorización por acción y objeto, no solo por sección.
8. **Correo y recuperación:** no construir URLs de reinicio con cabeceras `Host` sin validar; usar origen de aplicación configurado. No enviar contraseñas por correo. No revelar si una dirección está registrada.
9. **Configuración PHP/Apache:** en producción `DEBUG=0`, errores no visibles, cabeceras mínimas, SSL forzado, listado de directorios desactivado. Asegurar que cachés y archivos temporales no se exponen.
10. **Interfaz:** JavaScript nativo no almacena secretos ni reemplaza controles del servidor. Mantener CSP compatible con scripts y hojas locales, evitando inline JS cuando sea posible.
11. **Prohibido desactivar verificación TLS** en solicitudes HTTPS salientes, incluso para correo o integraciones futuras.
12. **Nunca optimizar el número de archivos copiando o modificando a mano código del núcleo de F3** ni eliminando partes del mismo sin auditar consecuencias y documentarlo.

### 9.3 Seguridad de versiones, historial y licencia

- **Versión mínima corregida de F3:** 3.9.3 según la información verificada el 18-11-2026; comprobar nueva versión estable y nuevos avisos en el momento real de instalación, no asumir que seguirá siendo la última.
- **CVE-2026-112103:** afecta la verificación de certificados TLS en cliente HTTP de `fatfree-core` hasta 3.9.2; corregido en 3.9.3. No instalar copias antiguas del framework sin comprobación.
- **CVE-2020-5203:** vulnerabilidad histórica de ejecución de código asociada a usos inseguros de una API; corregida en versiones anteriores. La existencia de una corrección no elimina la necesidad de restringir entradas no confiables.
- Mantener **PHP dentro de una rama soportada y parchada**, junto con Apache, OpenSSL y módulos del hosting; la elección del hosting determina parte de la superficie de riesgo.
- F3 utiliza **GPLv3**. Antes de distribuir el sistema, vender copias o entregarlo como producto cerrado, validar obligaciones de licencia y eventual alternativa comercial; el uso en un sitio alojado requiere un análisis distinto de la distribución de código.
- Fuentes para revisar: https://github.com/f3-factory/fatfree-core/releases ; https://github.com/f3-factory/fatfree-core/security ; https://osv.dev/vulnerability/CVE-2026-112103 ; https://github.com/advisories/GHSA-hpj2-4hfj-g233 ; https://cheatsheetseries.owasp.org/ ; https://fatfreeframework.com/3.9/development .

### 9.4 Mantenimiento y respuesta a incidentes (cPanel sin SSH)

- **Antes de publicar:** registrar versiones, verificar que no hay avisos críticos pendientes de mitigación, respaldar archivos y exportar MySQL; probar recuperación del respaldo en un entorno aislado.
- **Mensualmente y ante un aviso relevante:** revisar versiones, CVE de F3/PHP y extensiones; evaluar impacto y actualizar en un entorno de pruebas, después en producción.
- **En cada actualización:** probar flujos de registro, sesiones, recuperación, roles, CSRF, consultas SQL y tema oscuro; conservar procedimiento de rollback con criterio claro para no revertir migraciones de datos incompatibles.
- **Logs y alertas:** registrar eventos de seguridad (logins fallidos, cambios de roles, restablecimientos, errores graves) sin contraseñas, tokens o datos excesivos. Poner límites de tamaño/retención para respetar inodos y capacidad del hosting.
- **Ante sospecha de compromiso:** deshabilitar accesos afectados, preservar evidencia, rotar secretos, aplicar parches, restaurar desde respaldo verificado si procede y revisar los registros disponibles del hosting.
- El documento no promete ausencia futura de vulnerabilidades ni que usar F3 elimine la necesidad de actualizar.

## 10. JavaScript nativo

Usar `app.js` únicamente para:
- Mostrar/ocultar contraseña sin exponer datos.
- Abrir/cerrar menú móvil con soporte de teclado, ARIA y foco.
- Confirmar acciones administrativas sensibles.
- Mejoras progresivas en formularios (validación de experiencia de usuario, sin sustituir al backend).
- Notificaciones de interfaz accesibles.

La navegación, autenticación y funcionalidades críticas deben seguir funcionando con JavaScript deshabilitado, salvo interacciones exclusivamente visuales.

## 11. Configuración y despliegue en cPanel

1. Verificar antes de programar la **versión de PHP**, extensiones requeridas por F3/PDO, disponibilidad MySQL, mod_rewrite y límites del hosting. No asumir versiones fijas sin verificar.
2. Crear base de datos y usuario MySQL desde cPanel, asignando únicamente los permisos necesarios.
3. Preparar en local el paquete mínimo de producción; incluir únicamente archivos ejecutables, assets, configuración necesaria y bibliotecas autorizadas.
4. Configurar el document root del dominio/subdominio a `public/` si cPanel lo permite.
5. Configurar las rutas de Apache con `.htaccess` hacia `index.php`, sin interferir con archivos estáticos.
6. Cargar configuración y credenciales mediante archivo PHP privado o variables de entorno soportadas por el hosting; restringir permisos de lectura.
7. Importar `schema.sql` desde phpMyAdmin; ejecutar el bootstrap del administrador de forma privada.
8. Activar HTTPS y cookies seguras.
9. Desactivar modo debug y verificar errores 403/404/500.
10. Probar registro, login, logout, recuperación por correo, permisos y diseño móvil.
11. Contabilizar inodos consumidos antes y después del despliegue. Verificar también registros, cachés, sesiones PHP y buzones del hosting.
12. Entregar procedimiento de actualización y rollback sin acceso SSH, utilizando copia de seguridad de archivos y base de datos.

## 12. Rendimiento y mantenimiento

- Evitar llamadas HTTP y dependencias CDN críticas para el renderizado; servir Pico CSS local.
- Añadir cabeceras de caché para recursos estáticos con versionado de archivos o query strings.
- Optimizar imágenes (preferiblemente WebP/AVIF cuando sea compatible) y evitar bibliotecas de iconos completas si solo se usan unos pocos iconos SVG.
- Aplicar paginación a listados grandes; no consultar toda la tabla `users` para cada pantalla.
- Mantener las páginas SSR y usar JS ligero, sin bundles grandes.
- Medir: tamaño total del paquete de producción, número de archivos/directorios, consultas por vista y comportamiento en red móvil.

## 13. Criterios de aceptación verificables

- [ ] El proyecto se ejecuta en Apache + PHP + MySQL sin Node.js, Docker ni procesos persistentes.
- [ ] Usa Fat-Free Framework como backend y Pico CSS como base visual.
- [ ] Los archivos del paquete de despliegue están inventariados y el recuento de inodos está documentado.
- [ ] La aplicación tiene tema oscuro desde el primer render, sin pantallas en tema claro no intencionales.
- [ ] Se adapta sin desbordamiento horizontal a 320, 375, 768, 1024 y 1440 px.
- [ ] Registro valida campos y crea usuarios con rol `user` exclusivamente.
- [ ] Login, logout y sesiones funcionan de forma segura.
- [ ] Recuperación de contraseña funciona mediante token de uso único y expiración.
- [ ] El dashboard y el perfil requieren autenticación real en backend.
- [ ] Usuarios normales no pueden acceder a endpoints de administración aunque llamen las URLs directamente.
- [ ] La lista de usuarios incorpora paginación y búsqueda segura.
- [ ] Los formularios sensibles incluyen protección CSRF y validación del lado del servidor.
- [ ] Se comprueba resistencia básica a SQLi, XSS, fuerza bruta y escalamiento de privilegios.
- [ ] Hay mensajes de error adecuados y no se exponen trazas o secretos en producción.
- [ ] Los archivos privados no se descargan mediante URL pública.
- [ ] El proyecto puede instalarse y actualizarse utilizando cPanel y phpMyAdmin.
- [ ] Existe README de instalación y guía de despliegue/rollback.
- [ ] F3 es 3.9.3 o superior estable y se revisaron los avisos de seguridad vigentes a la fecha de instalación.
- [ ] Se registran las versiones de PHP, F3, Pico CSS y Apache con evidencia de compatibilidad.
- [ ] Un usuario A no puede consultar ni modificar datos de otro usuario B (prueba IDOR).
- [ ] El cambio de rol y la desactivación de usuarios invalida o actualiza correctamente permisos/sesiones.
- [ ] CSRF se valida tanto en formularios como en solicitudes JavaScript.
- [ ] Se prueban condiciones de carrera en restablecimiento de contraseña y unicidad del correo.
- [ ] Los intentos repetidos de acceso están limitados y los errores no revelan existencia de cuentas.
- [ ] Está documentado el proceso mensual de actualización de dependencias y revisión de CVE.
- [ ] Se evaluaron las obligaciones GPLv3 para el modelo de distribución previsto.
- [ ] Se probó la restauración de una copia de seguridad de MySQL y archivos.

## 14. Entregables que debe producir la IA

1. Código fuente completo y funcional con la estructura acordada.
2. Esquema `database/schema.sql` y procedimiento seguro de creación del primer admin.
3. Configuración Apache `.htaccess`, ejemplo de configuración privada y `.env.example` si aplica.
4. Pico CSS local y personalizaciones de tema oscuro con `app.css`.
5. `app.js` mínimo para interacciones accesibles.
6. README con requisitos reales, instalación local, configuración, ejecución y pruebas manuales.
7. Guía paso a paso de despliegue en hosting compartido cPanel.
8. Inventario de producción: cantidad de archivos, carpetas y tamaño del paquete, explicando cómo fue contado.
9. Casos de prueba para autenticación, autorización, seguridad básica y responsive.
10. Matriz de amenazas con resultado de cada prueba y evidencia de las versiones desplegadas.
11. Plan de mantenimiento, revisión de vulnerabilidades, rotación de logs, respaldo probado y rollback.

## 15. Orden de ejecución solicitado

**Fase 1 — Base:** preparar F3, conexión MySQL, enrutamiento, `.htaccess`, layout con Pico CSS oscuro y estructura segura de archivos.  
**Fase 2 — Cuentas:** registro, login, logout, sesiones, validaciones y CSRF.  
**Fase 3 — Usuario:** dashboard, edición de perfil, cambio y recuperación de contraseña.  
**Fase 4 — Administración:** gestión básica de usuarios, roles y estados con autorización estricta.  
**Fase 5 — Entrega:** pruebas, revisión de accesibilidad y responsive, hardening, optimización de archivos y documentación cPanel.

En cada fase, mostrar **qué archivos se crean o modifican**, explicar sus responsabilidades, entregar el código completo necesario, indicar pruebas para validar la fase y evitar introducir dependencias injustificadas.

---

## Prompt de arranque para la IA de desarrollo

> Actúa como arquitecto y desarrollador senior PHP especializado en hosting compartido. Implementa el proyecto descrito en este reporte técnico utilizando Fat-Free Framework (F3), Pico CSS, MySQL, JavaScript nativo y Apache/cPanel. Mi restricción principal es el número de archivos/inodos, así que minimiza el paquete final sin comprometer seguridad ni legibilidad. El diseño debe ser mobile-first y completamente oscuro desde el primer render. Empieza por la Fase 1 y genera código real y ejecutable, archivos completos, SQL y las instrucciones para validarlo. No agregues frameworks ni dependencias diferentes sin justificarlo y consultarlo. Sigue los criterios de aceptación del documento. La seguridad prevalece sobre la reducción de archivos: aplica controles OWASP, revisa CVE y versiones reales, prueba SQLi/XSS/CSRF/IDOR y documenta las decisiones de licencia GPLv3.
