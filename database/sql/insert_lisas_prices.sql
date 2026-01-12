-- ===================================================
-- SCRIPT SQL PARA ADICIONAR PREÇOS DE "LISAS"
-- Baseado no banco de dados de referência (John)
-- ===================================================

-- 1. Adicionar "Lisas" como opção de personalização
INSERT INTO `product_options` (`type`, `name`, `price`, `parent_type`, `parent_id`, `active`, `order`, `created_at`, `updated_at`) 
VALUES ('personalizacao', 'Lisas', 0.00, NULL, NULL, 1, 7, NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

-- Pegar o ID de "Lisas" que acabou de ser inserido
SET @lisas_id = (SELECT `id` FROM `product_options` WHERE `type` = 'personalizacao' AND `name` = 'Lisas' LIMIT 1);

-- 2. Buscar IDs dos tecidos existentes
SET @algodao_id = (SELECT `id` FROM `product_options` WHERE `type` = 'tecido' AND `name` = 'Algodão' LIMIT 1);
SET @poliester_id = (SELECT `id` FROM `product_options` WHERE `type` = 'tecido' AND `name` = 'Poliéster' LIMIT 1);

-- 3. Adicionar ou verificar se o tecido PV existe
INSERT INTO `product_options` (`type`, `name`, `price`, `parent_type`, `parent_id`, `active`, `order`, `created_at`, `updated_at`) 
VALUES ('tecido', 'PV', 0.00, 'personalizacao', (SELECT `id` FROM `product_options` WHERE `type` = 'personalizacao' AND `name` = 'SERIGRAFIA' LIMIT 1), 1, 3, NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

SET @pv_id = (SELECT `id` FROM `product_options` WHERE `type` = 'tecido' AND `name` = 'PV' LIMIT 1);

-- 4. Criar relações entre "Lisas" e os tecidos
INSERT INTO `product_option_relations` (`parent_id`, `option_id`, `created_at`, `updated_at`) 
VALUES 
    (@lisas_id, @algodao_id, NOW(), NOW()),
    (@lisas_id, @poliester_id, NOW(), NOW()),
    (@lisas_id, @pv_id, NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

-- 5. Adicionar preços para LISAS - PP (100% Algodão)
INSERT INTO `personalization_prices` (`personalization_type`, `size_name`, `size_dimensions`, `quantity_from`, `quantity_to`, `price`, `active`, `order`, `created_at`, `updated_at`) VALUES
('LISAS', 'PP', '100% Algodão', 1, 9, 18.00, 1, 0, NOW(), NOW()),
('LISAS', 'PP', '100% Algodão', 10, 29, 15.00, 1, 0, NOW(), NOW()),
('LISAS', 'PP', '100% Algodão', 30, 49, 13.50, 1, 0, NOW(), NOW()),
('LISAS', 'PP', '100% Algodão', 50, 99, 12.00, 1, 0, NOW(), NOW()),
('LISAS', 'PP', '100% Algodão', 100, 299, 10.50, 1, 0, NOW(), NOW()),
('LISAS', 'PP', '100% Algodão', 300, 499, 9.50, 1, 0, NOW(), NOW()),
('LISAS', 'PP', '100% Algodão', 500, 999, 8.50, 1, 0, NOW(), NOW()),
('LISAS', 'PP', '100% Algodão', 1000, 9999, 7.50, 1, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE `price` = VALUES(`price`), `updated_at` = NOW();

-- 6. Adicionar preços para LISAS - PV (Misto)
INSERT INTO `personalization_prices` (`personalization_type`, `size_name`, `size_dimensions`, `quantity_from`, `quantity_to`, `price`, `active`, `order`, `created_at`, `updated_at`) VALUES
('LISAS', 'PV', 'Misto PV', 1, 9, 16.00, 1, 0, NOW(), NOW()),
('LISAS', 'PV', 'Misto PV', 10, 29, 13.50, 1, 0, NOW(), NOW()),
('LISAS', 'PV', 'Misto PV', 30, 49, 12.00, 1, 0, NOW(), NOW()),
('LISAS', 'PV', 'Misto PV', 50, 99, 10.50, 1, 0, NOW(), NOW()),
('LISAS', 'PV', 'Misto PV', 100, 299, 9.00, 1, 0, NOW(), NOW()),
('LISAS', 'PV', 'Misto PV', 300, 499, 8.00, 1, 0, NOW(), NOW()),
('LISAS', 'PV', 'Misto PV', 500, 999, 7.00, 1, 0, NOW(), NOW()),
('LISAS', 'PV', 'Misto PV', 1000, 9999, 6.50, 1, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE `price` = VALUES(`price`), `updated_at` = NOW();

-- 7. Adicionar preços para LISAS - POLIÉSTER (100% Poliéster)
INSERT INTO `personalization_prices` (`personalization_type`, `size_name`, `size_dimensions`, `quantity_from`, `quantity_to`, `price`, `active`, `order`, `created_at`, `updated_at`) VALUES
('LISAS', 'POLIÉSTER', '100% Poliéster', 1, 9, 20.00, 1, 0, NOW(), NOW()),
('LISAS', 'POLIÉSTER', '100% Poliéster', 10, 29, 17.00, 1, 0, NOW(), NOW()),
('LISAS', 'POLIÉSTER', '100% Poliéster', 30, 49, 15.00, 1, 0, NOW(), NOW()),
('LISAS', 'POLIÉSTER', '100% Poliéster', 50, 99, 13.00, 1, 0, NOW(), NOW()),
('LISAS', 'POLIÉSTER', '100% Poliéster', 100, 299, 11.50, 1, 0, NOW(), NOW()),
('LISAS', 'POLIÉSTER', '100% Poliéster', 300, 499, 10.50, 1, 0, NOW(), NOW()),
('LISAS', 'POLIÉSTER', '100% Poliéster', 500, 999, 9.50, 1, 0, NOW(), NOW()),
('LISAS', 'POLIÉSTER', '100% Poliéster', 1000, 9999, 8.50, 1, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE `price` = VALUES(`price`), `updated_at` = NOW();

-- 8. Adicionar preços para LISAS - CACHARREL
INSERT INTO `personalization_prices` (`personalization_type`, `size_name`, `size_dimensions`, `quantity_from`, `quantity_to`, `price`, `active`, `order`, `created_at`, `updated_at`) VALUES
('LISAS', 'CACHARREL', 'Cacharrel', 1, 9, 22.00, 1, 0, NOW(), NOW()),
('LISAS', 'CACHARREL', 'Cacharrel', 10, 29, 19.00, 1, 0, NOW(), NOW()),
('LISAS', 'CACHARREL', 'Cacharrel', 30, 49, 17.00, 1, 0, NOW(), NOW()),
('LISAS', 'CACHARREL', 'Cacharrel', 50, 99, 15.00, 1, 0, NOW(), NOW()),
('LISAS', 'CACHARREL', 'Cacharrel', 100, 299, 13.00, 1, 0, NOW(), NOW()),
('LISAS', 'CACHARREL', 'Cacharrel', 300, 499, 11.50, 1, 0, NOW(), NOW()),
('LISAS', 'CACHARREL', 'Cacharrel', 500, 999, 10.50, 1, 0, NOW(), NOW()),
('LISAS', 'CACHARREL', 'Cacharrel', 1000, 9999, 9.50, 1, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE `price` = VALUES(`price`), `updated_at` = NOW();

-- 9. Adicionar preços para LISAS - DRY FIT
INSERT INTO `personalization_prices` (`personalization_type`, `size_name`, `size_dimensions`, `quantity_from`, `quantity_to`, `price`, `active`, `order`, `created_at`, `updated_at`) VALUES
('LISAS', 'DRY FIT', 'Dry Fit', 1, 9, 25.00, 1, 0, NOW(), NOW()),
('LISAS', 'DRY FIT', 'Dry Fit', 10, 29, 22.00, 1, 0, NOW(), NOW()),
('LISAS', 'DRY FIT', 'Dry Fit', 30, 49, 20.00, 1, 0, NOW(), NOW()),
('LISAS', 'DRY FIT', 'Dry Fit', 50, 99, 18.00, 1, 0, NOW(), NOW()),
('LISAS', 'DRY FIT', 'Dry Fit', 100, 299, 16.00, 1, 0, NOW(), NOW()),
('LISAS', 'DRY FIT', 'Dry Fit', 300, 499, 14.50, 1, 0, NOW(), NOW()),
('LISAS', 'DRY FIT', 'Dry Fit', 500, 999, 13.00, 1, 0, NOW(), NOW()),
('LISAS', 'DRY FIT', 'Dry Fit', 1000, 9999, 12.00, 1, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE `price` = VALUES(`price`), `updated_at` = NOW();

-- ===================================================
-- FIM DO SCRIPT
-- ===================================================
-- 
-- RESUMO DOS DADOS INSERIDOS:
-- - 1 opção de personalização: "Lisas"
-- - 1 tecido adicional: "PV" (se não existir)
-- - 3 relações de tecidos: Algodão, Poliéster e PV
-- - 40 registros de preços (5 tipos de tecido x 8 faixas de quantidade)
--
-- TIPOS DE TECIDOS COM PREÇOS:
-- 1. PP (100% Algodão) - R$ 18,00 a R$ 7,50
-- 2. PV (Misto) - R$ 16,00 a R$ 6,50
-- 3. POLIÉSTER (100% Poliéster) - R$ 20,00 a R$ 8,50
-- 4. CACHARREL - R$ 22,00 a R$ 9,50
-- 5. DRY FIT - R$ 25,00 a R$ 12,00
--
-- FAIXAS DE QUANTIDADE:
-- - 1-9 unidades
-- - 10-29 unidades
-- - 30-49 unidades
-- - 50-99 unidades
-- - 100-299 unidades
-- - 300-499 unidades
-- - 500-999 unidades
-- - 1000-9999 unidades
-- ===================================================

