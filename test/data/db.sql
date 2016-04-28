CREATE TABLE if not exists `blog`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` char(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) CHARSET=utf8 
