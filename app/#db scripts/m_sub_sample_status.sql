/*

-- DROP TABLE IF EXISTS m_sub_sample_status;

CREATE TABLE IF NOT EXISTS m_sub_sample_status (
	rowid INTEGER PRIMARY KEY
	,code VARCHAR(10) NOT NULL
	,name varchar(50)
	,description TEXT
	,remark TEXT DEFAULT NULL
	,create_by int DEFAULT NULL
	,create_date TIMESTAMP WITHOUT TIME ZONE
	,update_by int DEFAULT NULL
	,update_date TIMESTAMP WITHOUT TIME ZONE
	,is_cancel INT DEFAULT 0
);
*/
-- RULES INSTEAD DELETE ---

CREATE OR REPLACE RULE rule_d_m_sub_sample_status AS
  ON DELETE TO m_sub_sample_status
  DO INSTEAD
    UPDATE m_sub_sample_status 
	SET is_cancel = 1
	, update_by = -1
	, update_date = CURRENT_TIMESTAMP
    WHERE rowid = OLD.rowid;

-------------------------------------------------------------------------------------------

-- Permissions

GRANT ALL ON TABLE m_sub_sample_status TO polomaker_db;

------------------------------------------------------------------------

-- TRUNCATE TABLE m_sub_demo_status;

INSERT INTO m_sub_sample_status(rowid, code, name, description, create_by, create_date) VALUES
(10, 'DFT', 'รอตีบล๊อค', 'drafted', 0, CURRENT_TIMESTAMP)
,(20, 'DFT-A', 'รอปักตัวอย่าง', 'drafted', 0, CURRENT_TIMESTAMP)
,(30, 'CRT', 'สร้าง', 'created', 0, CURRENT_TIMESTAMP)
,(40, 'CRT-A', 'สร้าง', 'created', 0, CURRENT_TIMESTAMP)
;