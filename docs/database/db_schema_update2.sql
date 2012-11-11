-- Clean up tables (fix for OTC-166)
DELETE FROM `translations` WHERE `text`='';
DELETE FROM `history` WHERE `action`='changed' AND `oldValue`='-' AND newValue='';
