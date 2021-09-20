USE mysql_test;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
                          `username` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
                          `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
                          `created_at` datetime NOT NULL,
                          `updated_at` datetime NULL DEFAULT NULL,
                          PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
