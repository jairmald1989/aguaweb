-- Database updates for modern water billing system
-- Add new tables and modify existing ones

-- Update user table to add roles
ALTER TABLE `user` ADD COLUMN `role` VARCHAR(50) DEFAULT 'cobrador' AFTER `name`;
ALTER TABLE `user` ADD COLUMN `status` TINYINT(1) DEFAULT 1 AFTER `role`;
ALTER TABLE `user` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `status`;

-- Create roles table
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text,
  `permissions` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- Insert default roles
INSERT INTO `roles` (`name`, `description`, `permissions`) VALUES
('administrador', 'Administrador del sistema', 'all'),
('cobrador', 'Encargado de cobros', 'billing,clients,view_reports'),
('tesorero', 'Encargado de tesorería', 'billing,reports,payments'),
('toma_lecturas', 'Encargado de tomar lecturas', 'readings,view_clients');

-- Create zones table
CREATE TABLE IF NOT EXISTS `zones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- Create meters table
CREATE TABLE IF NOT EXISTS `meters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serial_number` varchar(50) NOT NULL,
  `client_id` int(11) NOT NULL,
  `zone_id` int(11),
  `installation_date` date,
  `status` enum('active','inactive','maintenance') DEFAULT 'active',
  `last_reading` decimal(10,2) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `serial_number` (`serial_number`),
  KEY `client_id` (`client_id`),
  KEY `zone_id` (`zone_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- Create readings table
CREATE TABLE IF NOT EXISTS `readings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meter_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `previous_reading` decimal(10,2) NOT NULL,
  `current_reading` decimal(10,2) NOT NULL,
  `consumption` decimal(10,2) GENERATED ALWAYS AS (`current_reading` - `previous_reading`) STORED,
  `reading_date` date NOT NULL,
  `photo` varchar(255),
  `notes` text,
  `taken_by` int(11),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `meter_id` (`meter_id`),
  KEY `client_id` (`client_id`),
  KEY `taken_by` (`taken_by`),
  KEY `reading_date` (`reading_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- Create categories table for billing rates
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `base_rate` decimal(10,2) NOT NULL,
  `rate_per_unit` decimal(10,2) NOT NULL,
  `min_consumption` decimal(10,2) DEFAULT 0,
  `max_consumption` decimal(10,2),
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- Insert default categories
INSERT INTO `categories` (`name`, `base_rate`, `rate_per_unit`, `min_consumption`, `max_consumption`) VALUES
('Residencial', 15.00, 0.50, 0, 20),
('Comercial', 25.00, 0.75, 0, 50),
('Industrial', 50.00, 1.00, 0, NULL);

-- Update owners/clients table
ALTER TABLE `owners` ADD COLUMN `category_id` int(11) DEFAULT 1 AFTER `contact`;
ALTER TABLE `owners` ADD COLUMN `zone_id` int(11) AFTER `category_id`;
ALTER TABLE `owners` ADD COLUMN `contract_number` varchar(50) AFTER `zone_id`;
ALTER TABLE `owners` ADD COLUMN `status` enum('active','inactive','suspended') DEFAULT 'active' AFTER `contract_number`;
ALTER TABLE `owners` ADD COLUMN `created_at` timestamp DEFAULT CURRENT_TIMESTAMP AFTER `status`;

-- Create payments table
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `amount_due` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `change_amount` decimal(10,2) DEFAULT 0,
  `payment_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `payment_method` enum('cash','card','transfer') DEFAULT 'cash',
  `receipt_number` varchar(50),
  `processed_by` int(11),
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `bill_id` (`bill_id`),
  KEY `processed_by` (`processed_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- Create settings table
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `setting_type` enum('text','number','email','url','json') DEFAULT 'text',
  `description` text,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- Insert default settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('company_name', 'Sistema de Agua Potable', 'text', 'Nombre de la empresa'),
('company_ruc', '12345678901', 'text', 'RUC de la empresa'),
('company_address', 'Dirección de la empresa', 'text', 'Dirección física'),
('company_phone', '000-000-0000', 'text', 'Teléfono de contacto'),
('company_email', 'info@aguapotable.com', 'email', 'Email de contacto'),
('smtp_host', '', 'text', 'Servidor SMTP'),
('smtp_port', '587', 'number', 'Puerto SMTP'),
('smtp_username', '', 'text', 'Usuario SMTP'),
('smtp_password', '', 'text', 'Contraseña SMTP'),
('email_template_payment', 'Estimado {client_name}, su pago de {amount} ha sido procesado exitosamente.', 'text', 'Plantilla email de pago');

-- Update bill table
ALTER TABLE `bill` ADD COLUMN `consumption` decimal(10,2) AFTER `pres`;
ALTER TABLE `bill` ADD COLUMN `rate` decimal(10,2) AFTER `consumption`;
ALTER TABLE `bill` ADD COLUMN `status` enum('pending','paid','overdue') DEFAULT 'pending' AFTER `price`;
ALTER TABLE `bill` ADD COLUMN `due_date` date AFTER `date`;
ALTER TABLE `bill` ADD COLUMN `created_at` timestamp DEFAULT CURRENT_TIMESTAMP AFTER `due_date`;