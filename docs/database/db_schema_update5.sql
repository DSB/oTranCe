CREATE VIEW `history_change` AS
  SELECT `user_id`, count(`id`) AS `editActions`, max(`dt`) AS `lastAction` from `history`
  where `action` = 'changed'
  group by `user_id`

