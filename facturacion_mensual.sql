-- Script SQL para crear la tabla facturacion_mensual
-- Tabla para almacenar la facturación mensual de todos los clientes

CREATE TABLE `facturacion_mensual` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `owners_id` int(10) NOT NULL,
  `mes` varchar(20) NOT NULL,
  `consumo` varchar(20) NOT NULL,
  `monto` varchar(20) NOT NULL,
  `fecha_emision` varchar(20) NOT NULL,
  `pagada` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Índices para optimizar consultas
ALTER TABLE `facturacion_mensual` ADD INDEX `idx_owners_id` (`owners_id`);
ALTER TABLE `facturacion_mensual` ADD INDEX `idx_mes` (`mes`);
ALTER TABLE `facturacion_mensual` ADD INDEX `idx_pagada` (`pagada`);