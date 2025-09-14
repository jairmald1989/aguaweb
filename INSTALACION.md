# Instrucciones de Instalación

## Base de Datos
1. Ejecutar el script SQL para crear la nueva tabla:
```sql
-- Ejecutar en MySQL/phpMyAdmin
SOURCE facturacion_mensual.sql;
```

## Archivos Requeridos
Todos los archivos nuevos han sido creados y están listos para usar:

### Archivos Principales
- `lectura_medidor.php` - Formulario de lectura de medidor
- `lectura_medidor1.php` - Procesamiento de lectura
- `facturacion_mensual.php` - Generación de facturación mensual
- `ver_facturacion.php` - Visualización de facturas
- `get_lectura_anterior.php` - Helper AJAX
- `marcar_pagada.php` - Helper para marcar como pagada

### Archivos Modificados
- `clients.php` - Agregados nuevos botones y modals

### Base de Datos
- `facturacion_mensual.sql` - Script para crear nueva tabla

## Verificación
1. Verificar que la conexión a la base de datos funcione correctamente
2. Ejecutar el script SQL para crear la tabla facturacion_mensual
3. Acceder a clients.php y verificar que aparezcan los nuevos botones
4. Probar la funcionalidad de lectura de medidor
5. Probar la generación de facturación mensual

## Características Implementadas
✅ Ventanas flotantes tipo Bootstrap/Facebox (mantiene estilo original)
✅ Sistema de precios escalonado por categorías  
✅ Integración con tempo_bill existente
✅ Tabla facturacion_mensual con estructura completa
✅ Botones de acceso en clients.php
✅ Funcionalidad AJAX para carga dinámica
✅ Validaciones de entrada
✅ Compatibilidad con sistema existente
✅ Sin modificaciones a flujos originales