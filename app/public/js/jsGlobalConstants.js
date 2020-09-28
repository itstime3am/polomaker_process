//search_panel.js
var _intMaxQueryDayRange = 45;
var _intMilliSecPerDay = 1000 * 60 * 60 * 24;
var MSG_ALERT_OVER_DATE_RANGE = 'กรุณาเลือกช่วงวันในการค้นหาไม่เกินกว่า v_XX_1 วัน เพื่อประสิทธิภาพในการทำงานของโปรแกรม และป้องกันความผิดพลาดในการจัดการข้อมูล';
var MSG_ALERT_QUERY_NO_DATA_FOUND = 'ไม่พบข้อมูลในเงื่อนไขที่ทำการค้นหา';
var MSG_ALERT_QUERY_FAILED = 'สืบค้นข้อมูลล้มเหลว v_XX_1';
var MSG_ALERT_COMMIT_FAILED = 'บันทึกข้อมูลล้มเหลว v_XX_1';
var MSG_ALERT_COMMIT_SUCCESS = "บันทึกข้อมูลสำเร็จ v_XX_1";
var MSG_ALERT_DELETE_FAILED = 'ลบข้อมูลล้มเหลว v_XX_1';
var MSG_ALERT_DELETE_SUCCESS = "ลบข้อมูลสำเร็จ v_XX_1";
var MSG_ALERT_CANCEL_FAILED = 'ยกเลิกข้อมูลล้มเหลว v_XX_1';
var MSG_ALERT_CANCEL_SUCCESS = "ยกเลิกข้อมูลสำเร็จ v_XX_1";
//	dialog
var MSG_DLG_TITLE_QUERY = 'กรุณารอสักครู่';
var MSG_DLG_HTML_QUERY = 'ระบบกำลังดำเนินการค้นหาข้อมูล กรุณารอจนกว่ากล่องข้อความจะปิดไปเอง';
var MSG_DLG_TITLE_COMMIT = 'กำลังบันทึกข้อมูล';
var MSG_DLG_HTML_COMMIT = 'ระบบกำลังดำเนินการบันทึกข้อมูล กรุณารอจนกว่ากล่องข้อความจะปิดไปเอง';
var MSG_DLG_TITLE_DELETE = 'กำลังลบข้อมูล';
var MSG_DLG_HTML_DELETE = 'ระบบกำลังดำเนินการลบข้อมูล กรุณารอจนกว่ากล่องข้อความจะปิดไปเอง';
var MSG_DLG_TITLE_CANCEL = 'กำลังยกเลืกข้อมูล';
var MSG_DLG_HTML_CANCEL = 'ระบบกำลังดำเนินการยกเลิกข้อมูล กรุณารอจนกว่ากล่องข้อความจะปิดไปเอง';
var MSG_DLG_TITLE_VIEW_FORM = 'เรียกดูข้อมูลv_XX_1';
var MSG_DLG_TITLE_EDIT_FORM = 'แก้ไขข้อมูลv_XX_1';
var MSG_DLG_TITLE_INSERT_FORM = 'เพิ่มข้อมูลv_XX_1';
var MSG_DLG_TITLE_SELECT_FIELDS = 'กรุณาเลือกข้อมูลที่ต้องการแสดง'

var MSG_ALERT_FORM_INVALID = 'รูปแบบข้อมูลไม่ถูกต้อง กรุณาตรวจสอบก่อนดำเนินการต่อไป';
var MSG_FORM_INVALID_NO_VALUE_INPUT = 'ไม่กรอกข้อมูลสำคัญ';
var MSG_FORM_INVALID_INTEGER_INPUT = 'รูปแบบข้อมูลไม่ถูกต้อง ( ตัวเลขจำนวนเต็ม )';
var MSG_FORM_INVALID_NUMBER_INPUT = 'รูปแบบข้อมูลไม่ถูกต้อง ( ตัวเลข )';
var MSG_FORM_INVALID_EMAIL_INPUT = 'รูปแบบข้อมูลไม่ถูกต้อง ( E-mail )';
var MSG_FORM_INVALID_MOBILE_INPUT = 'รูปแบบข้อมูลไม่ถูกต้อง ( เบอร์มือถือ )';
var MSG_FORM_INVALID_TEL_INPUT = 'รูปแบบข้อมูลไม่ถูกต้อง ( เบอร์โทรศัพท์ )';
var MSG_FORM_INVALID_FAX_INPUT = 'รูปแบบข้อมูลไม่ถูกต้อง ( แฟกซ์ )';

var MSG_VLDR_INVALID_REQUIRED = 'ข้อมูลสำคัญ v_XX_1';
var MSG_VLDR_INVALID_DATATYPE = 'รูปแบบข้อมูลไม่ถูกต้อง v_XX_1';
//search_panel.js

//list.js
var MSG_ALERT_INVALID_NUMBER_TYPE_INPUT = 'กรุณาใส่ข้อมูลเป็นตัวเลขที่ถูกต้อง';
var MSG_ALERT_INVALID_NO_VALUE_INPUT = 'กรุณาใส่ข้อมูลให้ครบทุกช่อง';

var MSG_CONFIRM_LEAVE_PAGE_WITHOUT_SAVE = 'ข้อมูลมีการแก้ไขโดยไม่ได้บันทึก กรุณาตรวจสอบและยืนยันการออก';
var MSG_CONFIRM_DELETE_ROW = 'ยืนยันการลบข้อมูล v_XX_1 ?';
var MSG_CONFIRM_CANCEL_ROW = 'ยืนยันการยกเลิกข้อมูล v_XX_1 ? ( กรณี MASTER DATA อาจทำให้ไม่สามารถเลือกหรือใช้งานข้อมูลได้หลังยืนยันการยกเลิก)';
var MSG_CONFIRM_CANCEL_EDITED_PANEL = 'ค่าล่าสุดมีการแก้ไขโดยที่ไม่ได้บันทึก ต้องการยกเลิกหรือไม่?';
var MSG_ALERT_EXPORT_DATA_CHANGED = 'ค่าล่าสุดมีการแก้ไขโดยที่ไม่ได้บันทึก กรุณาบันทึกก่อนทำการ Export';
var MSG_ALERT_COMMIT_NO_CHANGE = 'ยังไม่มีการเปลี่ยนแปลงข้อมูล';
//list.js

//Data icon
var MSG_ICON_VIEW = 'ดู';
var MSG_ICON_PDF = 'PDF';
var MSG_ICON_EDIT = 'แก้ไข';
var MSG_ICON_DELETE = 'ลบ';
var MSG_ICON_CANCEL = 'ยกเลิก';
var MSG_ICON_CLOSE = 'ปิด';
//Data icon title
var MSG_ICON_TITLE_VIEW = 'ดูข้อมูล';
var MSG_ICON_TITLE_PDF = 'ออกรายงาน';
var MSG_ICON_TITLE_EDIT = 'แก้ไขข้อมูล';
var MSG_ICON_TITLE_DELETE = 'ลบข้อมูล';
var MSG_ICON_TITLE_CANCEL = 'ยกเลิก';
var MSG_ICON_TITLE_DISABLED = 'ไม่สามารถทำรายการv_XX_1ได้ v_XX_2';
var MSG_ICON_TITLE_EDIT_DISABLED = 'ไม่สามารถทำรายการแก้ไขข้อมูลได้ v_XX_1';
var MSG_ICON_TITLE_NOT_AUTHORIZED = 'ไม่สามารถv_XX_1ได้เนื่องจากสิทธิ์ไม่เพียงพอ';


//Deliver report
var MSG_ALERT_DELIVER_DETAIL_ROWS_LIMIT = 'รายละเอียดราคาสินค้าจำกัดไว้ที่ v_XX_1 หัวข้อ ต่อหน้ารายงาน';