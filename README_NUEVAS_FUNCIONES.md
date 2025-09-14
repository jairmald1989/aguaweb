# Sistema de Facturación Mensual y Lectura de Medidores

## Archivos Implementados

### 1. Base de Datos
- **facturacion_mensual.sql**: Script SQL para crear la tabla de facturación mensual con índices optimizados

### 2. Módulo de Lectura de Medidores
- **lectura_medidor.php**: Formulario flotante para ingresar lecturas de medidor
- **lectura_medidor1.php**: Procesamiento y guardado de lecturas en tempo_bill
- **get_lectura_anterior.php**: Helper AJAX para cargar lecturas anteriores dinámicamente

### 3. Módulo de Facturación Mensual
- **facturacion_mensual.php**: Generación de facturación mensual con sistema escalonado
- **ver_facturacion.php**: Visualización de registros de facturación mensual
- **marcar_pagada.php**: Helper AJAX para marcar facturas como pagadas

### 4. Integración con el Sistema Existente
- **clients.php**: Modificado para agregar botones de "Lectura Medidor" y "Ver Facturación"

## Características Implementadas

### Sistema de Precios Escalonado
```php
function calcular_monto_escalonado($consumo) {
    // 0-10 unidades: $5 por unidad
    // 11-30 unidades: $8 por unidad  
    // 31-50 unidades: $12 por unidad
    // Más de 50 unidades: $15 por unidad
}
```

### Ventanas Flotantes
- Utiliza Bootstrap modals manteniendo el estilo original del sistema
- Integración con Facebox para comportamiento consistente
- Tamaños responsivos (400px para lectura, 800px para facturación)

### Funcionalidad AJAX
- Carga dinámica de lecturas anteriores al seleccionar cliente
- Marcado de facturas como pagadas sin recargar página
- Validaciones en tiempo real

### Integración con Sistema Existente
- No modifica flujos originales de facturación
- Utiliza tabla tempo_bill existente para lecturas
- Mantiene compatibilidad con sistema de usuarios y sesiones
- Preserva estilos CSS y JavaScript originales

## Uso del Sistema

### Ingreso de Lectura de Medidor
1. Acceder a clients.php
2. Clic en botón "Lectura Medidor"
3. Seleccionar cliente (carga automáticamente lectura anterior)
4. Ingresar lectura actual y fecha
5. Guardar (actualiza tempo_bill automáticamente)

### Generación de Facturación Mensual
1. Acceder a clients.php
2. Clic en botón "Ver Facturación"
3. Clic en "Generar Nueva Facturación"
4. Seleccionar mes/año
5. El sistema calcula automáticamente consumos y montos escalonados

### Visualización de Facturas
- Lista completa de facturas generadas
- Estados: Pagada/Pendiente
- Acciones: Marcar como pagada, Ver detalles
- Información del cliente y consumo

## Estructura de la Tabla facturacion_mensual
```sql
CREATE TABLE `facturacion_mensual` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `owners_id` int(10) NOT NULL,
  `mes` varchar(20) NOT NULL,
  `consumo` varchar(20) NOT NULL,
  `monto` varchar(20) NOT NULL,
  `fecha_emision` varchar(20) NOT NULL,
  `pagada` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);
```

## Notas de Implementación
- Mantiene 100% compatibilidad con sistema existente
- No requiere modificaciones a tablas existentes
- Utiliza patrones de código consistentes con el resto de la aplicación
- Implementa validaciones de seguridad básicas
- Sigue convenciones de nomenclatura del proyecto original