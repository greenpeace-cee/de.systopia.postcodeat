CREATE TABLE IF NOT EXISTS `civicrm_postcodeat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gemnr` char(5) NOT NULL,
  `gemnam38` char(50) NOT NULL,
  `okz` char(5) NOT NULL,
  `ortnam` char(50) NOT NULL,
  `skz` char(6) NOT NULL,
  `stroffi` varchar(60) NOT NULL,
  `plznr` char(4) NOT NULL,
  `gemnr2` char(5) NOT NULL,
  `zustort` varchar(75) NULL,
  PRIMARY KEY (`id`),
  KEY (`plznr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `civicrm_statistikaustria_import` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gemnr` char(5) NOT NULL,
  `gemnam38` char(50) NOT NULL,
  `okz` char(5) NOT NULL,
  `ortnam` char(50) NOT NULL,
  `skz` char(6) NOT NULL,
  `stroffi` varchar(60) NOT NULL,
  `plznr` char(4) NOT NULL,
  `gemnr2` char(5) NOT NULL,
  `zustort` varchar(75) NULL,
  PRIMARY KEY (`id`),
  KEY (`plznr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
