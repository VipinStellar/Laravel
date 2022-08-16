-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 10, 2022 at 07:17 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `mims_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_log`
--

CREATE TABLE `api_log` (
  `id` int(11) NOT NULL,
  `log_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `log_name` varchar(255) NOT NULL,
  `log_request` varchar(255) NOT NULL,
  `log_response` text NOT NULL,
  `log_request_url` varchar(255) NOT NULL,
  `log_referrer` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `api_log`
--

INSERT INTO `api_log` (`id`, `log_date`, `log_name`, `log_request`, `log_response`, `log_request_url`, `log_referrer`) VALUES
(1, '2022-05-06 05:41:01', 'Zoho Auth API', 'refresh_token=1000.860a9270502a6d0f574d7425ea2e7607.e3fdd21a4a6055f62914f44c35974fa4&client_id=1000.T6JSHXG93T9Y4FTURO5RV6UMG23GBE&client_secret=c45808c9c34377da420952f2c54f8696663f49011f&grant_type=refresh_token', 'SSL certificate problem: unable to get local issuer certificate', 'https://accounts.zoho.com/oauth/v2/token', 'https://localhost/mims/mims-api/public/api/media/medialist'),
(2, '2022-05-06 05:42:30', 'Zoho Auth API', 'refresh_token=1000.860a9270502a6d0f574d7425ea2e7607.e3fdd21a4a6055f62914f44c35974fa4&client_id=1000.T6JSHXG93T9Y4FTURO5RV6UMG23GBE&client_secret=c45808c9c34377da420952f2c54f8696663f49011f&grant_type=refresh_token', 'SSL certificate problem: unable to get local issuer certificate', 'https://accounts.zoho.com/oauth/v2/token', 'https://localhost/mims/mims-api/public/api/media/medialist'),
(3, '2022-05-08 22:17:41', 'Zoho Auth API', 'refresh_token=1000.860a9270502a6d0f574d7425ea2e7607.e3fdd21a4a6055f62914f44c35974fa4&client_id=1000.T6JSHXG93T9Y4FTURO5RV6UMG23GBE&client_secret=c45808c9c34377da420952f2c54f8696663f49011f&grant_type=refresh_token', 'SSL certificate problem: unable to get local issuer certificate', 'https://accounts.zoho.com/oauth/v2/token', 'https://localhost/mims/mims-api/public/api/media/medialist'),
(4, '2022-05-08 22:39:15', 'Zoho Auth API', 'refresh_token=1000.860a9270502a6d0f574d7425ea2e7607.e3fdd21a4a6055f62914f44c35974fa4&client_id=1000.T6JSHXG93T9Y4FTURO5RV6UMG23GBE&client_secret=c45808c9c34377da420952f2c54f8696663f49011f&grant_type=refresh_token', 'SSL certificate problem: unable to get local issuer certificate', 'https://accounts.zoho.com/oauth/v2/token', 'https://localhost/mims/mims-api/public/api/media/medialist');

-- --------------------------------------------------------

--
-- Table structure for table `branch`
--

CREATE TABLE `branch` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` smallint(20) NOT NULL,
  `state_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch`
--

INSERT INTO `branch` (`id`, `branch_name`, `country_id`, `state_name`, `address`, `created_at`, `updated_at`) VALUES
(1, 'Ahmedabad', 99, 'Uttar Pradesh', 'fgfg', '2022-04-24 22:01:01', '2022-05-02 03:27:51'),
(2, 'Bengaluru', 99, 'Himachal Pradesh', 'ddd', '2022-04-24 22:01:23', '2022-05-02 03:34:11'),
(3, 'Chandigarh', 99, 'Uttar Pradesh', 'xsx', '2022-04-24 22:15:00', '2022-05-02 03:28:44'),
(4, 'Chennai', 99, 'Himachal Pradesh', 'asxa', '2022-04-24 23:05:26', '2022-05-02 03:34:25'),
(5, 'Coimbatore', 99, 'Uttarakhand', 'test3', '2022-04-25 01:47:04', '2022-05-02 03:29:09'),
(6, 'Delhi NP', 99, 'Uttar Pradesh', 'tsg', '2022-04-25 02:57:54', '2022-05-02 03:29:32'),
(7, 'Delhi CP', 99, 'Himachal Pradesh', 'ss', '2022-05-02 03:29:58', '2022-05-02 03:29:58'),
(8, 'Gurugram', 99, 'Punjab', 'The company Location', '2022-05-02 03:30:25', '2022-05-05 00:41:13'),
(9, 'Hyderabad', 99, 'Punjab', 'ss', '2022-05-02 03:30:45', '2022-05-02 03:30:45'),
(10, 'Kolkata', 99, 'Punjab', 'ss', '2022-05-02 03:31:01', '2022-05-02 03:31:01'),
(11, 'Kochi', 99, 'Punjab', 'ss', '2022-05-02 03:31:19', '2022-05-02 03:31:19'),
(12, 'Mumbai', 99, 'Maharashtra', 'aaa', '2022-05-02 03:31:48', '2022-05-02 03:31:48'),
(13, 'Noida', 99, 'Uttar Pradesh', 'aa', '2022-05-02 03:32:13', '2022-05-02 03:32:13'),
(14, 'Pune', 99, 'Himachal Pradesh', 'aaa', '2022-05-02 03:32:35', '2022-05-02 03:32:35'),
(15, 'Vashi', 99, 'Jammu & Kashmir', 'aaa', '2022-05-02 03:32:52', '2022-05-02 03:32:52'),
(16, 'Nehru Place', 99, 'Delhi', 'ss', '2022-05-02 04:00:12', '2022-05-02 04:00:12'),
(17, 'Connaught Place', 99, 'Delhi', 'ss', '2022-05-02 04:00:39', '2022-05-02 04:00:39'),
(18, 'GGN Lab', 99, 'Delhi', 'ff', '2022-05-02 04:01:10', '2022-05-02 04:01:10'),
(22, 'Thane 2', 99, 'Maharashtra', 'Mumbai sub Branch add', '2022-05-05 00:46:21', '2022-05-05 01:18:35'),
(23, 'Head Office', 99, 'Haryana', 'Haryana,GGN', '2022-05-19 03:27:09', '2022-05-19 03:27:09');

-- --------------------------------------------------------

--
-- Table structure for table `branch_related`
--

CREATE TABLE `branch_related` (
  `user_id` tinyint(20) NOT NULL,
  `branch_id` tinyint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `branch_related`
--

INSERT INTO `branch_related` (`user_id`, `branch_id`) VALUES
(60, 2),
(62, 2),
(59, 2),
(60, 3),
(60, 4),
(63, 4),
(61, 23);

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `country_id` smallint(20) UNSIGNED NOT NULL,
  `country_code` char(2) NOT NULL,
  `country_code_long` char(3) NOT NULL,
  `country_num_code` char(4) NOT NULL,
  `country_phone_code` smallint(5) NOT NULL,
  `country_nicename` varchar(100) NOT NULL,
  `country_name` varchar(100) NOT NULL COMMENT 'country name',
  `country_created_time` datetime NOT NULL COMMENT 'date and time when country row created',
  `country_updated_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'date and time when country row updated',
  `country_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 for inactive and 1 for active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`country_id`, `country_code`, `country_code_long`, `country_num_code`, `country_phone_code`, `country_nicename`, `country_name`, `country_created_time`, `country_updated_time`, `country_active`) VALUES
(1, 'AF', 'AFG', '4', 93, 'AFGHANISTAN', 'Afghanistan', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(2, 'AL', 'ALB', '8', 355, 'ALBANIA', 'Albania', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(3, 'DZ', 'DZA', '12', 213, 'ALGERIA', 'Algeria', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(4, 'AS', 'ASM', '16', 1684, 'AMERICAN SAMOA', 'American Samoa', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(5, 'AD', 'AND', '20', 376, 'ANDORRA', 'Andorra', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(6, 'AO', 'AGO', '24', 244, 'ANGOLA', 'Angola', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(7, 'AI', 'AIA', '660', 1264, 'ANGUILLA', 'Anguilla', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(8, 'AQ', 'ATA', '10', 672, 'ANTARCTICA', 'Antarctica', '0000-00-00 00:00:00', '2020-09-06 09:08:06', 1),
(9, 'AG', 'ATG', '28', 1268, 'ANTIGUA AND BARBUDA', 'Antigua and Barbuda', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(10, 'AR', 'ARG', '32', 54, 'ARGENTINA', 'Argentina', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(11, 'AM', 'ARM', '51', 374, 'ARMENIA', 'Armenia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(12, 'AW', 'ABW', '533', 297, 'ARUBA', 'Aruba', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(13, 'AU', 'AUS', '36', 61, 'AUSTRALIA', 'Australia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(14, 'AT', 'AUT', '40', 43, 'AUSTRIA', 'Austria', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(15, 'AZ', 'AZE', '31', 994, 'AZERBAIJAN', 'Azerbaijan', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(16, 'BS', 'BHS', '44', 1242, 'BAHAMAS', 'Bahamas', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(17, 'BH', 'BHR', '48', 973, 'BAHRAIN', 'Bahrain', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(18, 'BD', 'BGD', '50', 880, 'BANGLADESH', 'Bangladesh', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(19, 'BB', 'BRB', '52', 1246, 'BARBADOS', 'Barbados', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(20, 'BY', 'BLR', '112', 375, 'BELARUS', 'Belarus', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(21, 'BE', 'BEL', '56', 32, 'BELGIUM', 'Belgium', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(22, 'BZ', 'BLZ', '84', 501, 'BELIZE', 'Belize', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(23, 'BJ', 'BEN', '204', 229, 'BENIN', 'Benin', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(24, 'BM', 'BMU', '60', 1441, 'BERMUDA', 'Bermuda', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(25, 'BT', 'BTN', '64', 975, 'BHUTAN', 'Bhutan', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(26, 'BO', 'BOL', '68', 591, 'BOLIVIA', 'Bolivia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(27, 'BA', 'BIH', '70', 387, 'BOSNIA AND HERZEGOVINA', 'Bosnia and Herzegovina', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(28, 'BW', 'BWA', '72', 267, 'BOTSWANA', 'Botswana', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(29, 'BV', 'BVT', '74', 47, 'BOUVET ISLAND', 'Bouvet Island', '0000-00-00 00:00:00', '2020-09-06 09:16:59', 1),
(30, 'BR', 'BRA', '76', 55, 'BRAZIL', 'Brazil', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(31, 'IO', 'IOT', '86', 246, 'BRITISH INDIAN OCEAN TERRITORY', 'British Indian Ocean Territory', '0000-00-00 00:00:00', '2020-09-06 09:12:52', 1),
(32, 'BN', 'BRN', '96', 673, 'BRUNEI DARUSSALAM', 'Brunei Darussalam', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(33, 'BG', 'BGR', '100', 359, 'BULGARIA', 'Bulgaria', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(34, 'BF', 'BFA', '854', 226, 'BURKINA FASO', 'Burkina Faso', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(35, 'BI', 'BDI', '108', 257, 'BURUNDI', 'Burundi', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(36, 'KH', 'KHM', '116', 855, 'CAMBODIA', 'Cambodia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(37, 'CM', 'CMR', '120', 237, 'CAMEROON', 'Cameroon', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(38, 'CA', 'CAN', '124', 1, 'CANADA', 'Canada', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(39, 'CV', 'CPV', '132', 238, 'CAPE VERDE', 'Cape Verde', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(40, 'KY', 'CYM', '136', 1345, 'CAYMAN ISLANDS', 'Cayman Islands', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(41, 'CF', 'CAF', '140', 236, 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(42, 'TD', 'TCD', '148', 235, 'CHAD', 'Chad', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(43, 'CL', 'CHL', '152', 56, 'CHILE', 'Chile', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(44, 'CN', 'CHN', '156', 86, 'CHINA', 'China', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(45, 'CX', 'CXR', '162', 61, 'CHRISTMAS ISLAND', 'Christmas Island', '0000-00-00 00:00:00', '2020-09-06 09:15:56', 1),
(46, 'CC', 'CCK', '166', 672, 'COCOS (KEELING) ISLANDS', 'Cocos (Keeling) Islands', '0000-00-00 00:00:00', '2020-09-06 09:11:15', 1),
(47, 'CO', 'COL', '170', 57, 'COLOMBIA', 'Colombia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(48, 'KM', 'COM', '174', 269, 'COMOROS', 'Comoros', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(49, 'CG', 'COG', '178', 242, 'CONGO', 'Congo', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(50, 'CD', 'COD', '180', 243, 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'Congo, the Democratic Republic of the', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(51, 'CK', 'COK', '184', 682, 'COOK ISLANDS', 'Cook Islands', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(52, 'CR', 'CRI', '188', 506, 'COSTA RICA', 'Costa Rica', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(53, 'CI', 'CIV', '384', 225, 'COTE D\'IVOIRE', 'Cote D\'Ivoire', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(54, 'HR', 'HRV', '191', 385, 'CROATIA', 'Croatia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(55, 'CU', 'CUB', '192', 53, 'CUBA', 'Cuba', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(56, 'CY', 'CYP', '196', 357, 'CYPRUS', 'Cyprus', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(57, 'CZ', 'CZE', '203', 420, 'CZECH REPUBLIC', 'Czech Republic', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(58, 'DK', 'DNK', '208', 45, 'DENMARK', 'Denmark', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(59, 'DJ', 'DJI', '262', 253, 'DJIBOUTI', 'Djibouti', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(60, 'DM', 'DMA', '212', 1767, 'DOMINICA', 'Dominica', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(61, 'DO', 'DOM', '214', 1809, 'DOMINICAN REPUBLIC', 'Dominican Republic', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(62, 'EC', 'ECU', '218', 593, 'ECUADOR', 'Ecuador', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(63, 'EG', 'EGY', '818', 20, 'EGYPT', 'Egypt', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(64, 'SV', 'SLV', '222', 503, 'EL SALVADOR', 'El Salvador', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(65, 'GQ', 'GNQ', '226', 240, 'EQUATORIAL GUINEA', 'Equatorial Guinea', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(66, 'ER', 'ERI', '232', 291, 'ERITREA', 'Eritrea', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(67, 'EE', 'EST', '233', 372, 'ESTONIA', 'Estonia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(68, 'ET', 'ETH', '231', 251, 'ETHIOPIA', 'Ethiopia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(69, 'FK', 'FLK', '238', 500, 'FALKLAND ISLANDS (MALVINAS)', 'Falkland Islands (Malvinas)', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(70, 'FO', 'FRO', '234', 298, 'FAROE ISLANDS', 'Faroe Islands', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(71, 'FJ', 'FJI', '242', 679, 'FIJI', 'Fiji', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(72, 'FI', 'FIN', '246', 358, 'FINLAND', 'Finland', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(73, 'FR', 'FRA', '250', 33, 'FRANCE', 'France', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(74, 'GF', 'GUF', '254', 594, 'FRENCH GUIANA', 'French Guiana', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(75, 'PF', 'PYF', '258', 689, 'FRENCH POLYNESIA', 'French Polynesia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(76, 'TF', 'ATF', '260', 262, 'FRENCH SOUTHERN TERRITORIES', 'French Southern Territories', '0000-00-00 00:00:00', '2020-09-06 09:18:09', 1),
(77, 'GA', 'GAB', '266', 241, 'GABON', 'Gabon', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(78, 'GM', 'GMB', '270', 220, 'GAMBIA', 'Gambia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(79, 'GE', 'GEO', '268', 995, 'GEORGIA', 'Georgia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(80, 'DE', 'DEU', '276', 49, 'GERMANY', 'Germany', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(81, 'GH', 'GHA', '288', 233, 'GHANA', 'Ghana', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(82, 'GI', 'GIB', '292', 350, 'GIBRALTAR', 'Gibraltar', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(83, 'GR', 'GRC', '300', 30, 'GREECE', 'Greece', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(84, 'GL', 'GRL', '304', 299, 'GREENLAND', 'Greenland', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(85, 'GD', 'GRD', '308', 1473, 'GRENADA', 'Grenada', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(86, 'GP', 'GLP', '312', 590, 'GUADELOUPE', 'Guadeloupe', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(87, 'GU', 'GUM', '316', 1671, 'GUAM', 'Guam', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(88, 'GT', 'GTM', '320', 502, 'GUATEMALA', 'Guatemala', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(89, 'GN', 'GIN', '324', 224, 'GUINEA', 'Guinea', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(90, 'GW', 'GNB', '624', 245, 'GUINEA-BISSAU', 'Guinea-Bissau', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(91, 'GY', 'GUY', '328', 592, 'GUYANA', 'Guyana', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(92, 'HT', 'HTI', '332', 509, 'HAITI', 'Haiti', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(93, 'HM', 'HMD', '334', 0, 'HEARD ISLAND AND MCDONALD ISLANDS', 'Heard Island and Mcdonald Islands', '0000-00-00 00:00:00', '2020-09-06 09:12:09', 1),
(94, 'VA', 'VAT', '336', 39, 'HOLY SEE (VATICAN CITY STATE)', 'Holy See (Vatican City State)', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(95, 'HN', 'HND', '340', 504, 'HONDURAS', 'Honduras', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(96, 'HK', 'HKG', '344', 852, 'HONG KONG', 'Hong Kong', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(97, 'HU', 'HUN', '348', 36, 'HUNGARY', 'Hungary', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(98, 'IS', 'ISL', '352', 354, 'ICELAND', 'Iceland', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(99, 'IN', 'IND', '356', 91, 'INDIA', 'India', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(100, 'ID', 'IDN', '360', 62, 'INDONESIA', 'Indonesia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(101, 'IR', 'IRN', '364', 98, 'IRAN, ISLAMIC REPUBLIC OF', 'Iran, Islamic Republic of', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(102, 'IQ', 'IRQ', '368', 964, 'IRAQ', 'Iraq', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(103, 'IE', 'IRL', '372', 353, 'IRELAND', 'Ireland', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(104, 'IL', 'ISR', '376', 972, 'ISRAEL', 'Israel', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(105, 'IT', 'ITA', '380', 39, 'ITALY', 'Italy', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(106, 'JM', 'JAM', '388', 1876, 'JAMAICA', 'Jamaica', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(107, 'JP', 'JPN', '392', 81, 'JAPAN', 'Japan', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(108, 'JO', 'JOR', '400', 962, 'JORDAN', 'Jordan', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(109, 'KZ', 'KAZ', '398', 7, 'KAZAKHSTAN', 'Kazakhstan', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(110, 'KE', 'KEN', '404', 254, 'KENYA', 'Kenya', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(111, 'KI', 'KIR', '296', 686, 'KIRIBATI', 'Kiribati', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(112, 'KP', 'PRK', '408', 850, 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF', 'Korea, Democratic People\'s Republic of', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(113, 'KR', 'KOR', '410', 82, 'KOREA, REPUBLIC OF', 'Korea, Republic of', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(114, 'KW', 'KWT', '414', 965, 'KUWAIT', 'Kuwait', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(115, 'KG', 'KGZ', '417', 996, 'KYRGYZSTAN', 'Kyrgyzstan', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(116, 'LA', 'LAO', '418', 856, 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC', 'Lao People\'s Democratic Republic', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(117, 'LV', 'LVA', '428', 371, 'LATVIA', 'Latvia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(118, 'LB', 'LBN', '422', 961, 'LEBANON', 'Lebanon', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(119, 'LS', 'LSO', '426', 266, 'LESOTHO', 'Lesotho', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(120, 'LR', 'LBR', '430', 231, 'LIBERIA', 'Liberia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(121, 'LY', 'LBY', '434', 218, 'LIBYAN ARAB JAMAHIRIYA', 'Libyan Arab Jamahiriya', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(122, 'LI', 'LIE', '438', 423, 'LIECHTENSTEIN', 'Liechtenstein', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(123, 'LT', 'LTU', '440', 370, 'LITHUANIA', 'Lithuania', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(124, 'LU', 'LUX', '442', 352, 'LUXEMBOURG', 'Luxembourg', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(125, 'MO', 'MAC', '446', 853, 'MACAO', 'Macao', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(126, 'MK', 'MKD', '807', 389, 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'Macedonia, the Former Yugoslav Republic of', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(127, 'MG', 'MDG', '450', 261, 'MADAGASCAR', 'Madagascar', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(128, 'MW', 'MWI', '454', 265, 'MALAWI', 'Malawi', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(129, 'MY', 'MYS', '458', 60, 'MALAYSIA', 'Malaysia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(130, 'MV', 'MDV', '462', 960, 'MALDIVES', 'Maldives', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(131, 'ML', 'MLI', '466', 223, 'MALI', 'Mali', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(132, 'MT', 'MLT', '470', 356, 'MALTA', 'Malta', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(133, 'MH', 'MHL', '584', 692, 'MARSHALL ISLANDS', 'Marshall Islands', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(134, 'MQ', 'MTQ', '474', 596, 'MARTINIQUE', 'Martinique', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(135, 'MR', 'MRT', '478', 222, 'MAURITANIA', 'Mauritania', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(136, 'MU', 'MUS', '480', 230, 'MAURITIUS', 'Mauritius', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(137, 'YT', 'MYT', '175', 269, 'MAYOTTE', 'Mayotte', '0000-00-00 00:00:00', '2020-09-06 09:19:01', 1),
(138, 'MX', 'MEX', '484', 52, 'MEXICO', 'Mexico', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(139, 'FM', 'FSM', '583', 691, 'MICRONESIA, FEDERATED STATES OF', 'Micronesia, Federated States of', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(140, 'MD', 'MDA', '498', 373, 'MOLDOVA, REPUBLIC OF', 'Moldova, Republic of', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(141, 'MC', 'MCO', '492', 377, 'MONACO', 'Monaco', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(142, 'MN', 'MNG', '496', 976, 'MONGOLIA', 'Mongolia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(143, 'MS', 'MSR', '500', 1664, 'MONTSERRAT', 'Montserrat', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(144, 'MA', 'MAR', '504', 212, 'MOROCCO', 'Morocco', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(145, 'MZ', 'MOZ', '508', 258, 'MOZAMBIQUE', 'Mozambique', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(146, 'MM', 'MMR', '104', 95, 'MYANMAR', 'Myanmar', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(147, 'NA', 'NAM', '516', 264, 'NAMIBIA', 'Namibia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(148, 'NR', 'NRU', '520', 674, 'NAURU', 'Nauru', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(149, 'NP', 'NPL', '524', 977, 'NEPAL', 'Nepal', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(150, 'NL', 'NLD', '528', 31, 'NETHERLANDS', 'Netherlands', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(151, 'AN', 'ANT', '530', 599, 'NETHERLANDS ANTILLES', 'Netherlands Antilles', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(152, 'NC', 'NCL', '540', 687, 'NEW CALEDONIA', 'New Caledonia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(153, 'NZ', 'NZL', '554', 64, 'NEW ZEALAND', 'New Zealand', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(154, 'NI', 'NIC', '558', 505, 'NICARAGUA', 'Nicaragua', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(155, 'NE', 'NER', '562', 227, 'NIGER', 'Niger', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(156, 'NG', 'NGA', '566', 234, 'NIGERIA', 'Nigeria', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(157, 'NU', 'NIU', '570', 683, 'NIUE', 'Niue', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(158, 'NF', 'NFK', '574', 672, 'NORFOLK ISLAND', 'Norfolk Island', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(159, 'MP', 'MNP', '580', 1670, 'NORTHERN MARIANA ISLANDS', 'Northern Mariana Islands', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(160, 'NO', 'NOR', '578', 47, 'NORWAY', 'Norway', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(161, 'OM', 'OMN', '512', 968, 'OMAN', 'Oman', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(162, 'PK', 'PAK', '586', 92, 'PAKISTAN', 'Pakistan', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(163, 'PW', 'PLW', '585', 680, 'PALAU', 'Palau', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(164, 'PS', 'PSE', '275', 970, 'PALESTINIAN TERRITORY, OCCUPIED', 'Palestinian Territory, Occupied', '0000-00-00 00:00:00', '2020-09-06 09:15:23', 1),
(165, 'PA', 'PAN', '591', 507, 'PANAMA', 'Panama', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(166, 'PG', 'PNG', '598', 675, 'PAPUA NEW GUINEA', 'Papua New Guinea', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(167, 'PY', 'PRY', '600', 595, 'PARAGUAY', 'Paraguay', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(168, 'PE', 'PER', '604', 51, 'PERU', 'Peru', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(169, 'PH', 'PHL', '608', 63, 'PHILIPPINES', 'Philippines', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(170, 'PN', 'PCN', '612', 64, 'PITCAIRN', 'Pitcairn', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(171, 'PL', 'POL', '616', 48, 'POLAND', 'Poland', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(172, 'PT', 'PRT', '620', 351, 'PORTUGAL', 'Portugal', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(173, 'PR', 'PRI', '630', 1787, 'PUERTO RICO', 'Puerto Rico', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(174, 'QA', 'QAT', '634', 974, 'QATAR', 'Qatar', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(175, 'RE', 'REU', '638', 262, 'REUNION', 'Reunion', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(176, 'RO', 'ROU', '642', 40, 'ROMANIA', 'Romania', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(177, 'RU', 'RUS', '643', 7, 'RUSSIAN FEDERATION', 'Russian Federation', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(178, 'RW', 'RWA', '646', 250, 'RWANDA', 'Rwanda', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(179, 'SH', 'SHN', '654', 290, 'SAINT HELENA', 'Saint Helena', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(180, 'KN', 'KNA', '659', 1869, 'SAINT KITTS AND NEVIS', 'Saint Kitts and Nevis', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(181, 'LC', 'LCA', '662', 1758, 'SAINT LUCIA', 'Saint Lucia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(182, 'PM', 'SPM', '666', 508, 'SAINT PIERRE AND MIQUELON', 'Saint Pierre and Miquelon', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(183, 'VC', 'VCT', '670', 1784, 'SAINT VINCENT AND THE GRENADINES', 'Saint Vincent and the Grenadines', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(184, 'WS', 'WSM', '882', 684, 'SAMOA', 'Samoa', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(185, 'SM', 'SMR', '674', 378, 'SAN MARINO', 'San Marino', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(186, 'ST', 'STP', '678', 239, 'SAO TOME AND PRINCIPE', 'Sao Tome and Principe', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(187, 'SA', 'SAU', '682', 966, 'SAUDI ARABIA', 'Saudi Arabia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(188, 'SN', 'SEN', '686', 221, 'SENEGAL', 'Senegal', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(190, 'SC', 'SYC', '690', 248, 'SEYCHELLES', 'Seychelles', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(191, 'SL', 'SLE', '694', 232, 'SIERRA LEONE', 'Sierra Leone', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(192, 'SG', 'SGP', '702', 65, 'SINGAPORE', 'Singapore', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(193, 'SK', 'SVK', '703', 421, 'SLOVAKIA', 'Slovakia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(194, 'SI', 'SVN', '705', 386, 'SLOVENIA', 'Slovenia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(195, 'SB', 'SLB', '90', 677, 'SOLOMON ISLANDS', 'Solomon Islands', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(196, 'SO', 'SOM', '706', 252, 'SOMALIA', 'Somalia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(197, 'ZA', 'ZAF', '710', 27, 'SOUTH AFRICA', 'South Africa', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(198, 'GS', 'SGS', '239', 500, 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'South Georgia and the South Sandwich Islands', '0000-00-00 00:00:00', '2020-09-06 09:16:28', 1),
(199, 'ES', 'ESP', '724', 34, 'SPAIN', 'Spain', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(200, 'LK', 'LKA', '144', 94, 'SRI LANKA', 'Sri Lanka', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(201, 'SD', 'SDN', '736', 249, 'SUDAN', 'Sudan', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(202, 'SR', 'SUR', '740', 597, 'SURINAME', 'Suriname', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(203, 'SJ', 'SJM', '744', 47, 'SVALBARD AND JAN MAYEN', 'Svalbard and Jan Mayen', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(204, 'SZ', 'SWZ', '748', 268, 'SWAZILAND', 'Swaziland', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(205, 'SE', 'SWE', '752', 46, 'SWEDEN', 'Sweden', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(206, 'CH', 'CHE', '756', 41, 'SWITZERLAND', 'Switzerland', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(207, 'SY', 'SYR', '760', 963, 'SYRIAN ARAB REPUBLIC', 'Syrian Arab Republic', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(208, 'TW', 'TWN', '158', 886, 'TAIWAN, PROVINCE OF CHINA', 'Taiwan, Province of China', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(209, 'TJ', 'TJK', '762', 992, 'TAJIKISTAN', 'Tajikistan', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(210, 'TZ', 'TZA', '834', 255, 'TANZANIA, UNITED REPUBLIC OF', 'Tanzania, United Republic of', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(211, 'TH', 'THA', '764', 66, 'THAILAND', 'Thailand', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(212, 'TL', 'TLS', '626', 670, 'TIMOR-LESTE', 'Timor-Leste', '0000-00-00 00:00:00', '2020-09-06 09:17:40', 1),
(213, 'TG', 'TGO', '768', 228, 'TOGO', 'Togo', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(214, 'TK', 'TKL', '772', 690, 'TOKELAU', 'Tokelau', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(215, 'TO', 'TON', '776', 676, 'TONGA', 'Tonga', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(216, 'TT', 'TTO', '780', 1868, 'TRINIDAD AND TOBAGO', 'Trinidad and Tobago', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(217, 'TN', 'TUN', '788', 216, 'TUNISIA', 'Tunisia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(218, 'TR', 'TUR', '792', 90, 'TURKEY', 'Turkey', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(219, 'TM', 'TKM', '795', 7370, 'TURKMENISTAN', 'Turkmenistan', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(220, 'TC', 'TCA', '796', 1649, 'TURKS AND CAICOS ISLANDS', 'Turks and Caicos Islands', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(221, 'TV', 'TUV', '798', 688, 'TUVALU', 'Tuvalu', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(222, 'UG', 'UGA', '800', 256, 'UGANDA', 'Uganda', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(223, 'UA', 'UKR', '804', 380, 'UKRAINE', 'Ukraine', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(224, 'AE', 'ARE', '784', 971, 'UNITED ARAB EMIRATES', 'United Arab Emirates', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(225, 'GB', 'GBR', '826', 44, 'UNITED KINGDOM', 'United Kingdom', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(226, 'US', 'USA', '840', 1, 'UNITED STATES OF AMERICA', 'United States of America', '0000-00-00 00:00:00', '2020-09-08 00:35:53', 1),
(227, 'UM', 'UMI', '581', 1, 'UNITED STATES MINOR OUTLYING ISLANDS', 'United States Minor Outlying Islands', '0000-00-00 00:00:00', '2020-09-06 09:13:34', 1),
(228, 'UY', 'URY', '858', 598, 'URUGUAY', 'Uruguay', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(229, 'UZ', 'UZB', '860', 998, 'UZBEKISTAN', 'Uzbekistan', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(230, 'VU', 'VUT', '548', 678, 'VANUATU', 'Vanuatu', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(231, 'VE', 'VEN', '862', 58, 'VENEZUELA', 'Venezuela', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(232, 'VN', 'VNM', '704', 84, 'VIET NAM', 'Viet Nam', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(233, 'VG', 'VGB', '92', 1284, 'VIRGIN ISLANDS, BRITISH', 'Virgin Islands, British', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(234, 'VI', 'VIR', '850', 1340, 'VIRGIN ISLANDS, U.S.', 'Virgin Islands, U.s.', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(235, 'WF', 'WLF', '876', 681, 'WALLIS AND FUTUNA', 'Wallis and Futuna', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(236, 'EH', 'ESH', '732', 212, 'WESTERN SAHARA', 'Western Sahara', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(237, 'YE', 'YEM', '887', 967, 'YEMEN', 'Yemen', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(238, 'ZM', 'ZMB', '894', 260, 'ZAMBIA', 'Zambia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(239, 'ZW', 'ZWE', '716', 263, 'ZIMBABWE', 'Zimbabwe', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(240, 'RS', 'SRB', '688', 381, 'SERBIA', 'Serbia', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(241, 'AP', 'APR', '0', 0, 'ASIA PACIFIC REGION', 'Asia / Pacific Region', '0000-00-00 00:00:00', '2020-09-06 09:10:27', 1),
(242, 'ME', 'MNE', '499', 382, 'MONTENEGRO', 'Montenegro', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(243, 'AX', 'ALA', '248', 358, 'ALAND ISLANDS', 'Aland Islands', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(244, 'BQ', 'BES', '535', 599, 'BONAIRE, SINT EUSTATIUS AND SABA', 'Bonaire, Sint Eustatius and Saba', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(245, 'CW', 'CUW', '531', 599, 'CURACAO', 'Curacao', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(246, 'GG', 'GGY', '831', 44, 'GUERNSEY', 'Guernsey', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(247, 'IM', 'IMN', '833', 44, 'ISLE OF MAN', 'Isle of Man', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(248, 'JE', 'JEY', '832', 44, 'JERSEY', 'Jersey', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(249, 'XK', 'XKX', '0', 383, 'KOSOVO', 'Kosovo', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(250, 'BL', 'BLM', '652', 590, 'SAINT BARTHELEMY', 'Saint Barthelemy', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(251, 'MF', 'MAF', '663', 590, 'SAINT MARTIN', 'Saint Martin', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(252, 'SX', 'SXM', '534', 1, 'SINT MAARTEN', 'Sint Maarten', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1),
(253, 'SS', 'SSD', '728', 211, 'SOUTH SUDAN', 'South Sudan', '0000-00-00 00:00:00', '2020-09-06 09:04:39', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customer_detail`
--

CREATE TABLE `customer_detail` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer_detail`
--

INSERT INTO `customer_detail` (`id`, `customer_name`) VALUES
(1, 'Rahul Kumar'),
(2, 'Anju Kumar'),
(3, 'Jay Kumar'),
(4, 'Om Kumar'),
(5, 'Om Kumar'),
(6, 'Vipin kumar'),
(7, 'Vipin kumar'),
(8, 'Vipin1 kumar');

-- --------------------------------------------------------

--
-- Table structure for table `job_status`
--

CREATE TABLE `job_status` (
  `id` int(11) NOT NULL,
  `media_id` smallint(5) NOT NULL,
  `status` smallint(5) NOT NULL,
  `remarks` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `zoho_id` varchar(100) DEFAULT NULL,
  `user_id` smallint(4) DEFAULT NULL,
  `customer_id` smallint(4) DEFAULT NULL,
  `branch_id` smallint(4) DEFAULT NULL,
  `transfer_id` smallint(4) DEFAULT NULL,
  `job_id` varchar(200) DEFAULT NULL,
  `zoho_job_id` varchar(200) DEFAULT NULL,
  `stage` varchar(100) DEFAULT NULL,
  `team_id` smallint(4) NOT NULL,
  `team_assign` smallint(4) NOT NULL,
  `media_type` varchar(100) DEFAULT NULL,
  `media_capacity` varchar(100) DEFAULT NULL,
  `service_mode` varchar(255) DEFAULT NULL,
  `service_type` varchar(255) DEFAULT NULL,
  `media_casing` varchar(255) DEFAULT NULL,
  `media_interface` varchar(100) DEFAULT NULL,
  `media_make` varchar(100) DEFAULT NULL,
  `media_model` varchar(100) DEFAULT NULL,
  `media_serial` varchar(100) DEFAULT NULL,
  `media_condition` varchar(100) DEFAULT NULL,
  `tampered_status` varchar(100) DEFAULT NULL,
  `peripherals_details` text DEFAULT NULL,
  `media_problem` varchar(100) DEFAULT NULL,
  `encryption_software` varchar(100) DEFAULT NULL,
  `encryption_version` varchar(100) DEFAULT NULL,
  `important_data` text DEFAULT NULL,
  `encryption_username` varchar(100) DEFAULT NULL,
  `encryption_password` varchar(200) DEFAULT NULL,
  `zoho_user` varchar(100) DEFAULT NULL,
  `case_type` varchar(100) DEFAULT NULL,
  `recovery_possibility` varchar(100) DEFAULT NULL,
  `required_days` varchar(100) DEFAULT NULL,
  `recovery_percentage` varchar(100) DEFAULT NULL,
  `access_percentage` varchar(100) DEFAULT NULL,
  `tampering_required` varchar(100) DEFAULT NULL,
  `recoverable_data` varchar(100) DEFAULT NULL,
  `no_recovery_reason` varchar(100) DEFAULT NULL,
  `assessment_due_reason` varchar(255) DEFAULT NULL,
  `assessment_due_other_reason` varchar(255) DEFAULT NULL,
  `selected_data` varchar(255) DEFAULT NULL,
  `job_status` varchar(100) DEFAULT NULL,
  `media_os` varchar(100) DEFAULT NULL,
  `media_firmware` varchar(255) DEFAULT NULL,
  `extension_required` varchar(100) DEFAULT NULL,
  `encryption_status` varchar(100) DEFAULT NULL,
  `extension_day` varchar(100) DEFAULT NULL,
  `created_on` timestamp NULL DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT NULL,
  `encryption_type` varchar(200) DEFAULT NULL,
  `encryption_details_correct` varchar(100) DEFAULT NULL,
  `media_damage` varchar(100) DEFAULT NULL,
  `noise_type` varchar(100) DEFAULT NULL,
  `drive_electronics` varchar(100) DEFAULT NULL,
  `rotary_function` varchar(100) DEFAULT NULL,
  `platters_condition` varchar(255) DEFAULT NULL,
  `further_use` varchar(255) DEFAULT NULL,
  `compression_status` varchar(255) DEFAULT NULL,
  `file_system_info` varchar(255) DEFAULT NULL,
  `data_loss_reason` varchar(255) DEFAULT NULL,
  `drive_count` varchar(255) DEFAULT NULL,
  `media_ubi` varchar(255) DEFAULT NULL,
  `backup_software` varchar(255) DEFAULT NULL,
  `cloning_possibility` varchar(255) DEFAULT NULL,
  `disk_type` varchar(255) DEFAULT NULL,
  `reading_process` varchar(255) DEFAULT NULL,
  `state_identified` varchar(255) DEFAULT NULL,
  `server_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `zoho_id`, `user_id`, `customer_id`, `branch_id`, `transfer_id`, `job_id`, `zoho_job_id`, `stage`, `team_id`, `team_assign`, `media_type`, `media_capacity`, `service_mode`, `service_type`, `media_casing`, `media_interface`, `media_make`, `media_model`, `media_serial`, `media_condition`, `tampered_status`, `peripherals_details`, `media_problem`, `encryption_software`, `encryption_version`, `important_data`, `encryption_username`, `encryption_password`, `zoho_user`, `case_type`, `recovery_possibility`, `required_days`, `recovery_percentage`, `access_percentage`, `tampering_required`, `recoverable_data`, `no_recovery_reason`, `assessment_due_reason`, `assessment_due_other_reason`, `selected_data`, `job_status`, `media_os`, `media_firmware`, `extension_required`, `encryption_status`, `extension_day`, `created_on`, `last_updated`, `encryption_type`, `encryption_details_correct`, `media_damage`, `noise_type`, `drive_electronics`, `rotary_function`, `platters_condition`, `further_use`, `compression_status`, `file_system_info`, `data_loss_reason`, `drive_count`, `media_ubi`, `backup_software`, `cloning_possibility`, `disk_type`, `reading_process`, `state_identified`, `server_type`) VALUES
(1, '7836089433', 60, 1, 2, 6, 'Ban/01', '123546', '4', 0, 0, 'SSD', '240 GB', NULL, NULL, NULL, 'FC', 'HDD', 'm SATA', '987514', 'Folded tape Ribbon', 'Tampered Media', NULL, 'N/A', 'N/A', 'N/A', '', 'N/A', 'N/A', 'anis Kumar', 'Logical', 'Yes', '16', '40%', NULL, 'Already Tampered', 'Data recoverable only in Raw form (without files & folders name)', NULL, 'Waiting  For Tempering Permission', NULL, NULL, 'Waiting For Assessment', 'Mac OS', NULL, 'Yes', 'Not determined at present stage', '7', '2022-06-01 08:18:26', '2022-08-09 06:10:04', 'Hardware', 'Yes', 'Damage found', 'No movement', 'Not functional', 'Normal', 'Light scratches on lower side of the platter', 'Not Possible', 'Compressed', 'File system information found corrupted', 'Overwritten data', '5', 'Not readable', 'Veritas', 'Yes', 'DVD', 'Other', 'light scratches on disk surface', 'Raid5'),
(2, '783608991', 60, 3, 2, 4, NULL, NULL, '2', 0, 0, 'Desktop HDD', '160 GB', NULL, NULL, NULL, NULL, 'HDD', 'SG23456885', '1234567', 'Tampered', 'Tampered Media', NULL, 'N/A', 'N/A', 'N/A', '', 'N/A', 'N/A', 'Ashu Kumar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-05 06:15:16', '2022-07-05 06:30:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, '783608992', 60, 4, 2, NULL, NULL, NULL, '2', 0, 0, 'Laptop HDD', '128 GB', NULL, NULL, NULL, NULL, 'HDD', 'SG23456856', '1234555', 'Non Tampered', 'Not Tampered', NULL, 'N/A', 'N/A', 'N/A', '', 'N/A', 'N/A', 'Sam Kumar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-06 09:24:52', '2022-07-06 09:25:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, '7836089982', 60, 5, 3, 12, NULL, NULL, '10', 1, 1, 'RAID', '128 GB', 'Remote-Online', 'Data erasing', 'Storage box', 'SCSI', 'HDD', 'SG23456856', '1234555', 'Non Tampered', 'Not Tampered', 'xvx', 'N/A', 'N/A', 'N/A', '', 'N/A', 'N/A', 'Sam Kumar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-06 09:41:00', '2022-07-29 05:24:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '15', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, '473255950034527846055', 60, 6, 2, 13, NULL, NULL, '2', 2, 1, 'CD/DVD', '16 GB', 'Remote-Online', 'Clonning', 'With casing', NULL, 'ghjg', 'ghjhg', 'test', 'Burnt', 'Not Tampered', NULL, '', '', '', '', '', '', 'Aman Kumar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Yes', NULL, '16', '2022-07-22 06:57:39', '2022-07-29 04:55:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, '4444444', 60, 7, 2, NULL, NULL, NULL, '10', 0, 0, 'SSD', '4 GB', 'Onsite- Customer\'s Site', 'Clonning', 'With casing', NULL, 'ghgf', 'hgfh', 'test', 'Burnt', 'Do Not Know', 'fghf', '', '', '', '', '', '', 'Aman Kumar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-29 05:34:22', '2022-08-09 04:37:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, '4444555444', 60, 8, 2, 14, NULL, NULL, '10', 0, 0, 'SSD', NULL, 'Onsite- Customer\'s Site', 'Clonning', 'With casing', NULL, 'dfgd', 'dfgdg', 'test', NULL, 'Tampered Media', NULL, '', '', '', '', '', '', 'Aman Kumar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Yes', NULL, '16', '2022-07-29 05:34:37', '2022-07-29 05:59:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `media_history`
--

CREATE TABLE `media_history` (
  `id` int(11) NOT NULL,
  `media_id` smallint(4) NOT NULL,
  `added_by` varchar(100) NOT NULL,
  `added_on` timestamp NULL DEFAULT NULL,
  `action_type` varchar(100) NOT NULL,
  `remarks` text NOT NULL,
  `status` smallint(4) NOT NULL,
  `module_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `media_history`
--

INSERT INTO `media_history` (`id`, `media_id`, `added_by`, `added_on`, `action_type`, `remarks`, `status`, `module_type`) VALUES
(1, 1, 'Sanjeev Kumar', '2022-06-30 08:18:26', 'edit', 'Case added by Zoho user Sanjeev Kumar', 1, 'media_in'),
(2, 1, '60', '2022-06-30 09:20:56', 'assign', 'Lab Technician changed to Amit by Amit.', 1, 'media_in'),
(3, 1, '60', '2022-06-30 09:21:37', 'transfer', 'Media Transferred Bengaluru to Chandigarh by Amit.', 1, 'media_in'),
(4, 1, '60', '2022-06-30 09:22:43', 'transfer', 'Media In by Amit', 1, 'media_in'),
(5, 1, '60', '2022-06-30 09:23:27', 'transfer', 'Media Transferred Chandigarh to Bengaluru by Amit.', 1, 'media_in'),
(6, 1, '60', '2022-06-30 09:24:20', 'transfer', 'Media In by Amit', 1, 'media_in'),
(7, 1, '60', '2022-06-30 09:24:34', 'edit', 'Test', 2, 'media_in'),
(8, 1, '60', '2022-06-30 09:26:06', 'transfer', 'Media Transferred Bengaluru to Chennai by Amit.', 2, 'media_in'),
(9, 1, '60', '2022-06-30 11:10:06', 'transfer', 'Media In by Amit', 2, 'media_in'),
(10, 1, 'anis Kumar', '2022-06-30 11:11:31', 'edit', 'Data updated by Zoho user anis Kumar', 3, 'assessment'),
(11, 1, '60', '2022-06-30 11:11:46', 'assign', 'Lab Technician changed to Amit by Amit.', 3, 'assessment'),
(12, 1, '60', '2022-06-30 11:41:12', 'edit', 'dfgdg', 4, 'assessment'),
(13, 1, '60', '2022-07-01 03:07:51', 'edit', 'fhh', 5, 'assessment'),
(14, 2, 'Ashu Kumar', '2022-07-05 06:15:17', 'edit', 'Case added by Zoho user Ashu Kumar', 1, 'media_in'),
(15, 2, '60', '2022-07-05 06:20:41', 'assign', 'Lab Technician changed to Amit by Amit.', 1, 'media_in'),
(16, 2, '60', '2022-07-05 06:21:32', 'transfer', 'Media Transferred Bengaluru to Chandigarh by Amit.', 1, 'media_in'),
(17, 2, '60', '2022-07-05 06:21:56', 'assign', 'Lab Technician changed to Ashok by Amit.', 1, 'media_in'),
(18, 2, '60', '2022-07-05 06:23:03', 'transfer', 'Media In by Amit', 1, 'media_in'),
(19, 2, '60', '2022-07-05 06:23:21', 'assign', 'Lab Technician changed to Amit by Amit.', 1, 'media_in'),
(20, 2, '60', '2022-07-05 06:30:00', 'edit', 'cbcb', 2, 'media_in'),
(21, 3, 'Sam Kumar', '2022-07-06 09:24:53', 'edit', 'Case added by Zoho user Sam Kumar', 1, 'media_in'),
(22, 3, '60', '2022-07-06 09:25:20', 'assign', 'Lab Technician changed to Amit by Amit.', 1, 'media_in'),
(23, 3, '60', '2022-07-06 09:25:37', 'edit', 'n,mn,', 2, 'media_in'),
(24, 4, 'Sam Kumar', '2022-07-06 09:41:00', 'edit', 'Case added by Zoho user Sam Kumar', 1, 'media_in'),
(25, 4, '60', '2022-07-06 09:41:24', 'assign', 'Lab Technician changed to Amit by Amit.', 1, 'media_in'),
(26, 1, '60', '2022-07-06 10:46:30', 'edit', 'dvsvdsv', 4, 'assessment'),
(27, 1, '60', '2022-07-07 08:29:12', 'edit', 'bcvbc', 4, 'assessment'),
(28, 1, '60', '2022-07-07 11:38:20', 'edit', 'xcvgd', 4, 'assessment'),
(29, 1, '60', '2022-07-07 11:39:06', 'edit', 'dascfsc', 4, 'assessment'),
(30, 4, '1', '2022-07-11 06:55:14', 'transfer', 'Media Transferred Chandigarh to Head Office by vipin kumar.', 1, 'media_in'),
(31, 1, '60', '2022-07-11 10:08:59', 'transfer', 'Media Transferred Chennai to Pune by Amit.', 4, 'media_in'),
(32, 1, '60', '2022-07-11 10:09:18', 'transfer', 'Media In by Amit', 4, 'media_in'),
(33, 4, '60', '2022-07-13 04:52:49', 'transfer', 'Media In by Amit', 1, 'media_in'),
(34, 4, '60', '2022-07-13 04:53:01', 'assign', 'Lab Technician changed to Subham kumar by Amit.', 1, 'media_in'),
(35, 4, '60', '2022-07-13 04:53:10', 'assign', 'Lab Technician changed to Amit by Amit.', 1, 'media_in'),
(36, 4, '60', '2022-07-13 04:53:25', 'transfer', 'Media Transferred Head Office to Chennai by Amit.', 1, 'media_in'),
(37, 4, '60', '2022-07-13 04:54:24', 'transfer', 'Media In by Amit', 1, 'media_in'),
(38, 4, '60', '2022-07-13 04:54:35', 'assign', 'Lab Technician changed to Aman by Amit.', 1, 'media_in'),
(39, 4, '60', '2022-07-13 04:54:55', 'assign', 'Lab Technician changed to Amit by Amit.', 1, 'media_in'),
(40, 4, '60', '2022-07-13 04:55:01', 'assign', 'Lab Technician changed to Aman by Amit.', 1, 'media_in'),
(41, 4, '60', '2022-07-13 04:55:13', 'assign', 'Lab Technician changed to Amit by Amit.', 1, 'media_in'),
(42, 4, '60', '2022-07-13 04:55:19', 'assign', 'Lab Technician changed to Aman by Amit.', 1, 'media_in'),
(43, 4, '60', '2022-07-13 05:01:53', 'assign', 'Lab Technician changed to Amit by Amit.', 1, 'media_in'),
(44, 4, '60', '2022-07-13 05:38:34', 'transfer', 'Media Transferred Chennai to Head Office by Amit.', 1, 'media_in'),
(45, 4, '60', '2022-07-13 05:48:07', 'transfer', 'Media In by Amit', 1, 'media_in'),
(46, 4, '60', '2022-07-13 05:50:52', 'assign', 'Lab Technician changed to Subham kumar by Amit.', 1, 'media_in'),
(47, 4, '60', '2022-07-13 05:51:00', 'assign', 'Lab Technician changed to Amit by Amit.', 1, 'media_in'),
(48, 4, '60', '2022-07-13 05:52:50', 'transfer', 'Media Transferred Head Office to Chandigarh by Amit.', 1, 'media_in'),
(49, 4, '60', '2022-07-13 05:54:18', 'transfer', 'Media In by Amit', 1, 'media_in'),
(50, 4, '60', '2022-07-13 07:14:11', 'transfer', 'Media Transferred Chandigarh to Head Office by Amit.', 1, 'media_in'),
(51, 4, '60', '2022-07-13 07:14:25', 'transfer', 'Media In by Amit', 1, 'media_in'),
(52, 4, '60', '2022-07-13 09:26:31', 'transfer', 'Media Assign New Team ', 1, 'media_in'),
(53, 4, '60', '2022-07-13 09:29:03', 'transfer', 'Media Transferred Head Office to Bengaluru by Amit.', 1, 'media_in'),
(54, 4, '60', '2022-07-13 09:29:17', 'transfer', 'Media In by Amit', 1, 'media_in'),
(55, 4, '60', '2022-07-13 09:29:33', 'transfer', 'Media Transferred Bengaluru to Head Office by Amit.', 1, 'media_in'),
(56, 4, '60', '2022-07-13 09:29:41', 'transfer', 'Media In by Amit', 1, 'media_in'),
(57, 4, '60', '2022-07-13 09:29:54', 'transfer', 'Media Assign New Team ', 1, 'media_in'),
(58, 4, '60', '2022-07-15 10:55:22', 'edit', 'xcvxv', 1, 'media_in'),
(59, 4, '60', '2022-07-15 10:55:41', 'edit', 'xcvx', 1, 'media_in'),
(60, 1, '60', '2022-07-19 06:08:23', 'edit', 'xxx', 4, 'assessment'),
(61, 1, '60', '2022-07-19 06:08:34', 'edit', 'zxcz', 4, 'assessment'),
(62, 1, '60', '2022-07-20 05:11:18', 'edit', 'dc', 4, 'assessment'),
(63, 4, '60', '2022-07-20 06:51:05', 'edit', 'vd', 1, 'media_in'),
(64, 4, '60', '2022-07-20 06:54:42', 'edit', 'csd', 1, 'media_in'),
(65, 5, 'Aman Kumar', '2022-07-22 06:57:39', 'edit', 'Case added by Zoho user Aman Kumar', 1, 'media_in'),
(66, 1, '60', '2022-07-22 08:57:17', 'edit', 'xcx', 4, 'assessment'),
(67, 5, '60', '2022-07-22 09:47:32', 'assign', 'Lab Technician changed to Amit by Amit.', 1, 'media_in'),
(68, 1, '60', '2022-07-26 06:11:33', 'edit', 'm,m,', 4, 'assessment'),
(69, 1, '60', '2022-07-26 06:50:36', 'edit', 'bnmb', 4, 'assessment'),
(70, 1, '60', '2022-07-26 06:53:17', 'edit', 'cvbc', 3, 'assessment'),
(71, 1, '60', '2022-07-26 07:17:52', 'edit', 'czsdc', 3, 'assessment'),
(72, 4, '60', '2022-07-27 04:14:25', 'edit', 'vcx', 1, 'media_in'),
(73, 1, '60', '2022-07-27 06:08:31', 'edit', 'xcxz', 3, 'assessment'),
(74, 4, '60', '2022-07-27 06:52:05', 'edit', 'ddd', 1, 'media_in'),
(75, 1, '60', '2022-07-27 09:04:58', 'edit', 'xcx', 3, 'assessment'),
(76, 1, '60', '2022-07-27 09:15:30', 'edit', 'sss', 3, 'assessment'),
(77, 5, '60', '2022-07-28 08:45:24', 'transfer', 'Media Transferred Bengaluru to Head Office by Amit.', 1, 'media_in'),
(78, 1, '60', '2022-07-29 04:47:45', 'edit', 'gggg', 4, 'assessment'),
(79, 5, '60', '2022-07-29 04:51:49', 'transfer', 'Media In by Amit', 1, 'media_in'),
(80, 5, '60', '2022-07-29 04:51:58', 'transfer', 'Media Assign New Team ', 1, 'media_in'),
(81, 5, '60', '2022-07-29 04:53:34', 'edit', 'ghjg', 10, 'media_in'),
(82, 5, '60', '2022-07-29 04:55:01', 'edit', 'vbnv', 2, 'media_in'),
(83, 4, '60', '2022-07-29 05:02:37', 'edit', 'zzz', 1, 'media_in'),
(84, 4, '60', '2022-07-29 05:24:55', 'edit', 'cvbc', 10, 'media_in'),
(85, 6, 'Aman Kumar', '2022-07-29 05:34:22', 'edit', 'Case added by Zoho user Aman Kumar', 1, 'media_in'),
(86, 7, 'Aman Kumar', '2022-07-29 05:34:37', 'edit', 'Case added by Zoho user Aman Kumar', 1, 'media_in'),
(87, 7, '60', '2022-07-29 05:58:36', 'assign', 'Lab Technician changed to Ankit by Amit.', 1, 'media_in'),
(88, 7, '60', '2022-07-29 05:59:06', 'assign', 'Lab Technician changed to Amit by Amit.', 1, 'media_in'),
(89, 7, '60', '2022-07-29 05:59:34', 'edit', 'dfgdgdfg', 10, 'media_in'),
(90, 7, '60', '2022-07-29 06:02:06', 'transfer', 'Media Transferred Bengaluru to Chennai by Amit.', 10, 'media_in'),
(91, 6, '60', '2022-08-08 10:04:10', 'assign', 'Lab Technician changed to Amit by Amit.', 1, 'media_in'),
(92, 6, '60', '2022-08-08 10:22:51', 'assign', 'Lab Technician changed to Ashok by Amit.', 1, 'media_in'),
(93, 6, '60', '2022-08-08 10:22:58', 'assign', 'Lab Technician changed to Amit by Amit.', 1, 'media_in'),
(94, 6, '60', '2022-08-08 11:35:17', 'edit', 'dsfsfs', 1, 'media_in'),
(95, 6, '60', '2022-08-08 11:38:01', 'edit', 'csdc', 1, 'media_in'),
(96, 7, '60', '2022-08-09 04:36:30', 'transfer', 'Media In by Amit', 10, 'media_in'),
(97, 6, '60', '2022-08-09 04:37:44', 'edit', 'fgfh', 10, 'media_in'),
(98, 1, '60', '2022-08-09 05:41:45', 'edit', 'dfg', 4, 'assessment'),
(99, 1, '60', '2022-08-09 05:50:22', 'edit', 'sdas', 4, 'assessment'),
(100, 1, '60', '2022-08-09 06:05:09', 'edit', 'ddd', 4, 'assessment'),
(101, 1, '60', '2022-08-09 06:05:42', 'edit', 'dd', 4, 'assessment'),
(102, 1, '60', '2022-08-09 06:10:04', 'edit', 'zxc', 4, 'assessment');

-- --------------------------------------------------------

--
-- Table structure for table `media_team`
--

CREATE TABLE `media_team` (
  `id` int(11) NOT NULL,
  `team_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `media_team`
--

INSERT INTO `media_team` (`id`, `team_name`) VALUES
(1, 'Clean Room'),
(2, 'Logical Room'),
(3, 'PC3 Room');

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE `module` (
  `id` tinyint(4) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `module`
--

INSERT INTO `module` (`id`, `name`, `slug`, `status`) VALUES
(1, 'User', 'user', 1),
(2, 'Role', 'role', 1),
(3, 'Branch', 'branch', 1),
(6, 'Media In', 'media', 1),
(7, 'Media Case Details', 'case-details', 1),
(9, 'Media Inspection', 'media-assessment', 1),
(10, 'Media Pre Inspection', 'pre-analysis', 1),
(11, 'Transfer Media', 'transfer-media', 1),
(12, 'Job Status', 'job-status', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `user_id` smallint(4) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `module_type` varchar(200) NOT NULL,
  `module_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('vipin.kumar@stellarinfo.com', 'g66OQGhVswNHivXhi5KwDvGPn4oJQZfA6Cub7bHok9aQC30NAngizXS1Df3Mh3pm', '2022-05-03 21:43:23');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `assign` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `role_name`, `parent_id`, `assign`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 0, '{\"access\":[\"user\",\"role\",\"branch\",\"media\",\"case-details\",\"media-assessment\",\"pre-analysis\",\"transfer-media\",\"job-status\"],\"modify\":[\"user\",\"role\",\"branch\",\"media\",\"case-details\",\"media-assessment\",\"pre-analysis\",\"transfer-media\",\"job-status\"],\"delete\":[\"user\",\"role\",\"branch\",\"media\",\"case-details\",\"media-assessment\",\"pre-analysis\",\"transfer-media\",\"job-status\"]}', NULL, '2022-08-09 07:23:36'),
(2, 'Admin', 1, '{\"access\":[\"user\",\"media\",\"case-details\",\"assign-to\",\"media-assessment\",\"pre-analysis\",\"transfer-media\"],\"modify\":[\"case-details\",\"assign-to\",\"media-assessment\",\"pre-analysis\",\"transfer-media\"],\"delete\":[\"case-details\",\"assign-to\",\"media-assessment\",\"pre-analysis\",\"transfer-media\"]}', '2022-05-04 21:42:58', '2022-06-15 03:16:19'),
(3, 'Manager', 1, '{\"access\":[\"media\",\"case-details\",\"assign-to\",\"media-assessment\",\"pre-analysis\",\"transfer-media\"],\"modify\":[\"case-details\",\"assign-to\",\"media-assessment\",\"pre-analysis\",\"transfer-media\"],\"delete\":[\"case-details\",\"assign-to\",\"media-assessment\",\"pre-analysis\",\"transfer-media\"]}', '2022-05-04 21:43:23', '2022-06-15 03:16:38'),
(8, 'Lab Technician', 3, '{\"access\":[\"media\",\"case-details\",\"media-assessment\",\"pre-analysis\",\"transfer-media\",\"job-status\"],\"modify\":[\"media\",\"case-details\",\"media-assessment\",\"pre-analysis\",\"transfer-media\",\"job-status\"],\"delete\":[\"media\",\"case-details\",\"media-assessment\",\"pre-analysis\",\"transfer-media\",\"job-status\"]}', '2022-05-13 03:23:13', '2022-08-09 09:04:58'),
(9, 'Gurugram(H/O)', 3, '{\"access\":[\"media\",\"case-details\",\"media-assessment\",\"pre-analysis\",\"transfer-media\",\"job-status\"],\"modify\":[\"case-details\",\"media-assessment\",\"pre-analysis\",\"transfer-media\",\"job-status\"],\"delete\":null}', '2022-05-24 04:03:59', '2022-08-09 07:24:16');

-- --------------------------------------------------------

--
-- Table structure for table `stage`
--

CREATE TABLE `stage` (
  `id` int(11) NOT NULL,
  `stage_name` varchar(100) NOT NULL,
  `type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `stage`
--

INSERT INTO `stage` (`id`, `stage_name`, `type`) VALUES
(1, 'Pre Inspection Required', 'analysis'),
(2, 'Pre Inspection Done', 'analysis'),
(3, 'Media In', 'assessment'),
(4, 'Inspection In Process', 'assessment'),
(5, 'Inspection Done', 'assessment'),
(6, 'Case Possible', NULL),
(7, 'Case Not Possible', NULL),
(10, 'Pre Inspection Process', 'analysis');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `state_id` int(11) NOT NULL,
  `state_name` varchar(150) DEFAULT NULL,
  `state_type` varchar(150) DEFAULT NULL,
  `state_code` varchar(33) DEFAULT NULL,
  `state_abb` varchar(30) DEFAULT NULL,
  `state_created_time` datetime DEFAULT NULL,
  `state_created_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `state_active` tinyint(4) DEFAULT NULL,
  `country_id` smallint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`state_id`, `state_name`, `state_type`, `state_code`, `state_abb`, `state_created_time`, `state_created_update`, `state_active`, `country_id`) VALUES
(1, 'Jammu & Kashmir', 'state', '01', 'JK', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(2, 'Himachal Pradesh', 'state', '02', 'HP', '0000-00-00 00:00:00', '2017-11-03 04:18:09', 1, 99),
(3, 'Punjab', 'state', '03', 'PB', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(4, 'Chandigarh', 'UT', '04', 'CH', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(5, 'Uttarakhand', 'state', '05', 'UK', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(6, 'Haryana', 'state', '06', 'HR', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(7, 'Delhi', 'state', '07', 'DL', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(8, 'Rajasthan', 'state', '08', 'RJ', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(9, 'Uttar Pradesh', 'state', '09', 'UP', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(10, 'Bihar', 'state', '10', 'BR', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(11, 'Sikkim', 'state', '11', 'SK', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(12, 'Arunachal Pradesh', 'state', '12', 'AR', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(13, 'Nagaland', 'state', '13', 'NL', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(14, 'Manipur', 'state', '14', 'MN', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(15, 'Mizoram', 'state', '15', 'MZ', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(16, 'Tripura', 'state', '16', 'TR', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(17, 'Meghalaya', 'state', '17', 'ML', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(18, 'Assam', 'state', '18', 'AS', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(19, 'West Bengal', 'state', '19', 'WB', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(20, 'Jharkhand', 'state', '20', 'JH', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(21, 'Odisha', 'state', '21', 'OR', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(22, 'Chhattisgarh', 'state', '22', 'CT', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(23, 'Madhya Pradesh', 'state', '23', 'MP', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(24, 'Gujarat', 'state', '24', 'GJ', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(25, 'Daman and Diu', 'UT', '25', 'DD', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(26, 'Dadra and Nagar Haveli', 'UT', '26', 'DH', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(27, 'Maharashtra', 'state', '27', 'MH', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(28, 'Andhra Pradesh', 'state', '37', 'AP', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(29, 'Karnataka', 'state', '29', 'KA', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(30, 'Goa', 'state', '30', 'GA', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(31, 'Lakshadweep', 'UT', '31', 'LD', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(32, 'Kerala', 'state', '32', 'KL', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(33, 'Tamil Nadu', 'state', '33', 'TN', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(34, 'Puducherry', 'state', '34', 'PY', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(35, 'Andaman & Nicobar Islands', 'UT', '35', 'AN', '0000-00-00 00:00:00', '2018-01-17 04:22:27', 1, 99),
(36, 'Telangana', 'state', '36', 'TS', '0000-00-00 00:00:00', '2017-11-03 02:15:23', 1, 99),
(37, 'Other Territory', 'state', '97', 'OT', '0000-00-00 00:00:00', '2018-01-17 04:23:24', 1, 99);

-- --------------------------------------------------------

--
-- Table structure for table `transfer_media`
--

CREATE TABLE `transfer_media` (
  `id` int(11) NOT NULL,
  `transfer_code` varchar(50) DEFAULT NULL,
  `old_branch_id` smallint(6) NOT NULL,
  `new_branch_id` smallint(6) NOT NULL,
  `reason` varchar(250) NOT NULL,
  `created_on` timestamp NULL DEFAULT NULL,
  `media_id` int(11) NOT NULL,
  `transfer_series` int(11) DEFAULT NULL,
  `media_in_status` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transfer_media`
--

INSERT INTO `transfer_media` (`id`, `transfer_code`, `old_branch_id`, `new_branch_id`, `reason`, `created_on`, `media_id`, `transfer_series`, `media_in_status`) VALUES
(1, NULL, 2, 3, 'Need to send to HO for pre-analysis', '2022-06-30 09:21:37', 1, NULL, '1'),
(2, NULL, 2, 2, 'Need to send to HO for pre-analysis', '2022-06-30 09:23:27', 1, NULL, '1'),
(3, NULL, 2, 4, 'Need to send to HO for assessment', '2022-06-30 09:26:06', 1, NULL, '1'),
(4, NULL, 2, 3, 'Need to send to HO for pre-analysis', '2022-07-05 06:21:32', 2, NULL, '1'),
(5, 'HO/0001', 3, 23, 'Need to send to HO for Recovery', '2022-07-11 06:55:14', 4, 1, '1'),
(6, 'Ban/01', 2, 14, 'Need to send to HO for Inspection', '2022-07-11 10:08:59', 1, NULL, '1'),
(7, NULL, 3, 4, 'Need to send to HO for Inspection', '2022-07-13 04:53:25', 4, NULL, '1'),
(8, 'HO/0002', 3, 23, 'Need to send to HO for Inspection', '2022-07-13 05:38:34', 4, 2, '1'),
(9, NULL, 3, 3, 'Need to send to HO for Inspection', '2022-07-13 05:52:49', 4, NULL, '1'),
(10, 'HO/0003', 3, 23, 'Need to send to Other Branch for Inspection', '2022-07-13 07:14:11', 4, 3, '1'),
(11, NULL, 3, 2, 'Need to send to HO for Inspection', '2022-07-13 09:29:02', 4, NULL, '1'),
(12, 'HO/0004', 3, 23, 'Need to send to HO for Inspection', '2022-07-13 09:29:33', 4, 4, '1'),
(13, 'HO/0005', 2, 23, 'Need to send to HO for Inspection', '2022-07-28 08:45:24', 5, 5, '1'),
(14, NULL, 2, 4, 'Need to send to HO for Inspection', '2022-07-29 06:02:05', 7, NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emp_code` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` smallint(20) NOT NULL,
  `supervisor_id` smallint(4) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `team_id` smallint(5) DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `emp_code`, `role_id`, `supervisor_id`, `email_verified_at`, `password`, `status`, `team_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'vipin kumar', 'vipin.kumar@stellarinfo.com', 'S1942', 1, NULL, '2022-04-18 00:18:37', '$2y$10$anYvJnqmUvwetVtYRwF2OuD1t72UpXTapxF6SoY14YRpEgWVWQLba', 1, NULL, 'YsQFxH11ht', '2022-04-18 00:18:38', '2022-08-08 06:58:15'),
(58, 'Anik', 'anil@stellarinfo.com', 's1085', 3, 1, NULL, '$2y$10$anYvJnqmUvwetVtYRwF2OuD1t72UpXTapxF6SoY14YRpEgWVWQLba', 1, NULL, NULL, NULL, '2022-08-08 09:05:54'),
(59, 'Ankit', 'ankit@stellarinfo.com', 'S1955', 3, 0, NULL, '$2y$10$EzZeLAcYwOrv6ZRfPCsV1OpmzIo4rQddyCs0MJiYFrgUrUtfOmRAa', 1, NULL, NULL, '2022-05-13 03:45:32', '2022-08-08 09:06:49'),
(60, 'Amit', 'amit@stellarinfo.com', 'S1943', 8, 59, NULL, '$2y$10$dfyKUdTJeNxRMyO6nCTGhuRlLKqqqof6EgRpizi1iVUkfuNBzuqbe', 1, NULL, NULL, '2022-05-13 04:06:43', '2022-08-08 07:28:07'),
(61, 'Subham kumar', 'subham@stellarinfo.com', 'S19425', 9, 0, NULL, '$2y$10$BQv7nbC15XXzbvln01Px0O6tDqnuXKi8BZUcBrnl5yLMWeKxhch/G', 1, 1, NULL, '2022-05-24 04:05:42', '2022-08-08 09:05:19'),
(62, 'Ashok', 'ashok@stellarinfo.com', 'S1958', 8, 59, NULL, '$2y$10$OcPVAxxjq6PhTK6ykdHbHeTqta103nNuztitbZKkGlnUQJpfUWr.y', 1, NULL, NULL, '2022-06-10 03:35:07', '2022-08-08 09:05:00'),
(63, 'Aman', 'aman@stellarinfo.com', 'S1548', 8, 0, NULL, '$2y$10$DxC5anHEaFG6Vhj0tF67hum9TNpUEeqlueNLRWfcafM1/DUROS2ki', 1, NULL, NULL, '2022-06-20 10:25:29', '2022-08-08 09:04:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api_log`
--
ALTER TABLE `api_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branch`
--
ALTER TABLE `branch`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_name_unique` (`branch_name`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`country_id`);

--
-- Indexes for table `customer_detail`
--
ALTER TABLE `customer_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_status`
--
ALTER TABLE `job_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media_history`
--
ALTER TABLE `media_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media_team`
--
ALTER TABLE `media_team`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`role_name`);

--
-- Indexes for table `stage`
--
ALTER TABLE `stage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`state_id`);

--
-- Indexes for table `transfer_media`
--
ALTER TABLE `transfer_media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `emp_code` (`emp_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `api_log`
--
ALTER TABLE `api_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `branch`
--
ALTER TABLE `branch`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `country_id` smallint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

--
-- AUTO_INCREMENT for table `customer_detail`
--
ALTER TABLE `customer_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `job_status`
--
ALTER TABLE `job_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `media_history`
--
ALTER TABLE `media_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `media_team`
--
ALTER TABLE `media_team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `module`
--
ALTER TABLE `module`
  MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `stage`
--
ALTER TABLE `stage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `state_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `transfer_media`
--
ALTER TABLE `transfer_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
COMMIT;
