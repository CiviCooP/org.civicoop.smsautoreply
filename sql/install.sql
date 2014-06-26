CREATE TABLE IF NOT EXISTS `civicrm_sms_autoreply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `keyword` varchar(160) NOT NULL,
  `reply` varchar(255) NOT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `is_active` INT(11) DEFAULT '1',
  `charge` INT(11) DEFAULT NULL,
  `financial_type_id` INT(11) DEFAULT NULL,
  `weight` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;