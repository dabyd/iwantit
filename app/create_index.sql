-- =============================================
-- Script de Índices para MySQL Workbench
-- =============================================

DELIMITER //

DROP PROCEDURE IF EXISTS create_index_if_not_exists//

CREATE PROCEDURE create_index_if_not_exists(
    IN p_table VARCHAR(64),
    IN p_index VARCHAR(64),
    IN p_columns VARCHAR(255)
)
BEGIN
    DECLARE index_exists INT DEFAULT 0;
    
    SELECT COUNT(*) INTO index_exists
    FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = p_table
      AND index_name = p_index;
    
    IF index_exists = 0 THEN
        SET @sql = CONCAT('CREATE INDEX ', p_index, ' ON ', p_table, '(', p_columns, ')');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
        SELECT CONCAT('Índice creado: ', p_index, ' en ', p_table) AS resultado;
    ELSE
        SELECT CONCAT('Índice ya existe: ', p_index, ' en ', p_table) AS resultado;
    END IF;
END//

DELIMITER ;

-- =============================================
-- ÍNDICES DE ALTA PRIORIDAD
-- =============================================
CALL create_index_if_not_exists('hotpoints', 'idx_hotpoints_versions_id', 'versions_id');
CALL create_index_if_not_exists('hotpoints', 'idx_hotpoints_products_id', 'products_id');
CALL create_index_if_not_exists('hotpoints', 'idx_hotpoints_version_product', 'versions_id, products_id');
CALL create_index_if_not_exists('hotpoints', 'idx_hotpoints_version_time', 'versions_id, time');
CALL create_index_if_not_exists('licenses', 'idx_licenses_versions_id', 'versions_id');
CALL create_index_if_not_exists('projects_users', 'idx_projects_users_projects_id', 'projects_id');
CALL create_index_if_not_exists('projects_users', 'idx_projects_users_users_id', 'users_id');
CALL create_index_if_not_exists('projects_users', 'idx_projects_users_user_owner', 'users_id, as_owner');
CALL create_index_if_not_exists('datision_detections', 'idx_datision_detections_result_id', 'datision_result_id');
CALL create_index_if_not_exists('datision_detections', 'idx_datision_detections_frame', 'frame');
CALL create_index_if_not_exists('datision_detections', 'idx_datision_detections_result_frame', 'datision_result_id, frame');

-- =============================================
-- ÍNDICES DE PRIORIDAD MEDIA
-- =============================================
CALL create_index_if_not_exists('datision_results', 'idx_datision_results_datision_id', 'datision_id');
CALL create_index_if_not_exists('datision_results', 'idx_datision_results_class', 'class(100)');
CALL create_index_if_not_exists('datision_results', 'idx_datision_results_id_object', 'id_object');
CALL create_index_if_not_exists('datisions', 'idx_datisions_id_project', 'id_project');
CALL create_index_if_not_exists('hotpoints_dates', 'idx_hotpoints_dates_project', 'project_id');
CALL create_index_if_not_exists('hotpoints_dates', 'idx_hotpoints_dates_product', 'product_id');
CALL create_index_if_not_exists('products', 'idx_products_disabled', 'disabled');
CALL create_index_if_not_exists('products', 'idx_products_brands_id', 'brands_id');
CALL create_index_if_not_exists('brands', 'idx_brands_disabled', 'disabled');

-- =============================================
-- ÍNDICES DE PRIORIDAD BAJA
-- =============================================
CALL create_index_if_not_exists('products_tags', 'idx_products_tags_products_id', 'products_id');
CALL create_index_if_not_exists('products_tags', 'idx_products_tags_tags_id', 'tags_id');
CALL create_index_if_not_exists('products_tags', 'idx_products_tags_disabled', 'disabled');
CALL create_index_if_not_exists('tags', 'idx_tags_disabled', 'disabled');
CALL create_index_if_not_exists('versions_tags', 'idx_versions_tags_versions_id', 'versions_id');
CALL create_index_if_not_exists('versions_tags', 'idx_versions_tags_tags_id', 'tags_id');
CALL create_index_if_not_exists('territories_tags', 'idx_territories_tags_tags_id', 'tags_id');
CALL create_index_if_not_exists('territories_tags', 'idx_territories_tags_territories_id', 'territories_id');
CALL create_index_if_not_exists('datos_editor_hotpoints', 'idx_datos_editor_versiones_id', 'versiones_id');
CALL create_index_if_not_exists('user_options', 'idx_user_options_user_id', 'user_id');
CALL create_index_if_not_exists('products_datision_objects_ia_classes', 'idx_products_ia_products_id', 'products_id');
CALL create_index_if_not_exists('products_datision_objects_ia_classes', 'idx_products_ia_classes_id', 'datision_objects_ia_classes_id');
CALL create_index_if_not_exists('projects', 'idx_projects_users_id', 'users_id');
CALL create_index_if_not_exists('projects', 'idx_projects_territories_id', 'territories_id');

-- Limpiar el procedimiento temporal
DROP PROCEDURE IF EXISTS create_index_if_not_exists;

SELECT '¡Script completado!' AS resultado;