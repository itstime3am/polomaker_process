
/*

-- DROP TABLE IF EXISTS m_sub_screen_status;

CREATE TABLE IF NOT EXISTS m_sub_screen_status (
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

CREATE OR REPLACE RULE rule_d_m_sub_screen_status AS
  ON DELETE TO m_sub_screen_status
  DO INSTEAD
    UPDATE m_sub_screen_status 
	SET is_cancel = 1
	, update_by = -1
	, update_date = CURRENT_TIMESTAMP
    WHERE rowid = OLD.rowid;

-------------------------------------------------------------------------------------------

-- Permissions

GRANT ALL ON TABLE m_sub_screen_status TO polomaker_db;

------------------------------------------------------------------------

-- TRUNCATE TABLE m_sub_screen_status;

INSERT INTO m_sub_screen_status(rowid, code, name, description, create_by, create_date) VALUES
(1, 'DFT', 'ร่าง', 'drafted', 0, CURRENT_TIMESTAMP)
,(2, 'DFT-A', 'ร่าง', 'drafted', 0, CURRENT_TIMESTAMP)
,(10, 'CRT', 'สร้าง', 'created', 0, CURRENT_TIMESTAMP)
,(11, 'CRT-A', 'สร้าง', 'created', 0, CURRENT_TIMESTAMP)
,(20, 'EDT', 'แก้ไข', 'editted', 0, CURRENT_TIMESTAMP)
,(30, 'WIP', 'เริ่มกระบวนการทำงาน', 'working in process', 0, CURRENT_TIMESTAMP)
,(50, 'HLD', 'พักกระบวนการชั่วคราว', 'holding', 0, CURRENT_TIMESTAMP)
,(60, 'PRD', 'จบกระบวนการผลิต', 'produced', 0, CURRENT_TIMESTAMP)
,(145, 'RQE', 'คำร้องขอแก้ไขข้อมูล', 'status rollback for editing requested', 0, CURRENT_TIMESTAMP)
,(150, 'RGR', 'อนุญาตให้แก้ไขข้อมูล', 'rollbacked for edit', 0, CURRENT_TIMESTAMP)
,(151, 'RGR', 'พักการผลิตเพื่อแก้ไขข้อมูล', 'rollbacked for edit', 0, CURRENT_TIMESTAMP)
,(155, 'EDN', 'แก้ไขข้อมูลเรียบร้อย', 'done editting', 0, CURRENT_TIMESTAMP)
;