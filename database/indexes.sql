-- Idempotent index creation without dropping FK-backed indexes.

-- helper: create index only if it doesn't already exist
DROP PROCEDURE IF EXISTS ensure_index;
DELIMITER //
CREATE PROCEDURE ensure_index(IN t VARCHAR(64), IN idx VARCHAR(64), IN col_list VARCHAR(255), IN is_unique BOOLEAN)
BEGIN
  IF NOT EXISTS (
    SELECT 1
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE table_schema = DATABASE()
      AND table_name   = t
      AND index_name   = idx
  ) THEN
    SET @s = CONCAT('CREATE ', CASE WHEN is_unique THEN 'UNIQUE ' ELSE '' END,
                    'INDEX ', idx, ' ON `', t, '` (', col_list, ')');
    PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END//
DELIMITER ;

-- users
CALL ensure_index('users',  'idx_username',        'username',  TRUE);
CALL ensure_index('users',  'idx_email',           'email',     TRUE);

-- groups
CALL ensure_index('groups', 'idx_owner_id',        'owner_id',  FALSE);

-- lists
CALL ensure_index('lists',  'idx_user_id_lists',   'user_id',   FALSE);
CALL ensure_index('lists',  'idx_group_id_lists',  'group_id',  FALSE);

-- tasks
CALL ensure_index('tasks',  'idx_user_id_tasks',   'user_id',   FALSE);
CALL ensure_index('tasks',  'idx_list_id_tasks',   'list_id',   FALSE);
CALL ensure_index('tasks',  'idx_due_date_tasks',  'due_date',  FALSE);
CALL ensure_index('tasks',  'idx_status_tasks',    'status',    FALSE);
CALL ensure_index('tasks',  'idx_priority_tasks',  'priority',  FALSE);
CALL ensure_index('tasks',  'idx_is_deleted_tasks','is_deleted',FALSE);