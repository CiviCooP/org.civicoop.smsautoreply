CREATE TABLE IF NOT EXISTS `civicrm_sms_autoreply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(160) NOT NULL,
  `reply` varchar(255) NOT NULL,
  `provider_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;