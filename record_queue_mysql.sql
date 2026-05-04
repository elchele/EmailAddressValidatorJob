CREATE TABLE `record_queue` (
  `id` char(36) NOT NULL,
  `processed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
