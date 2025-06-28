-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 04, 2024 at 09:22 AM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ptl_fw`
--

-- --------------------------------------------------------

--
-- Table structure for table `m_audit`
--

CREATE TABLE `m_audit` (
  `id` int(11) NOT NULL,
  `tanggal` datetime NOT NULL,
  `created` varchar(25) NOT NULL,
  `ket` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `m_audit`
--

INSERT INTO `m_audit` (`id`, `tanggal`, `created`, `ket`) VALUES
(1, '2024-09-19 04:09:05', 'admin', 'ADD BL JOB ORDER NO. FW-2400002'),
(2, '2024-09-19 04:12:18', 'admin', 'ADD AWB JOB ORDER NO. FW-2400003'),
(3, '2024-09-22 02:22:44', 'admin', 'ADD KASBON JO AMIR JOB ORDER NO. FW-2400007'),
(4, '2024-09-22 02:23:03', 'admin', 'EDIT KASBON JO AMIR JOB ORDER NO. FW-2400007'),
(5, '2024-09-22 02:27:21', 'admin', 'ADD KASBON JO BUDI JOB ORDER NO. FW-2400004'),
(6, '2024-09-22 02:27:45', 'admin', 'EDIT KASBON JO BUDI JOB ORDER NO. FW-2400004'),
(7, '2024-09-22 02:29:07', 'admin', 'ADD PAYMENT KASBON JO NO. 00002/BON-FW/2024'),
(8, '2024-09-22 02:30:12', 'admin', 'ADD KASBON LAIN NO. BON-240001'),
(9, '2024-09-22 02:31:17', 'admin', 'ADD PAYMENT KASBON LAIN NO. BON-240001'),
(10, '2024-09-22 02:31:44', 'admin', 'ADD PAYMENT KASBON LAIN NO. BON-240001'),
(11, '2024-09-22 02:32:39', 'admin', 'ADD KASBON LAIN NO. BON-240002'),
(12, '2024-09-22 02:33:06', 'admin', 'ADD PAYMENT KASBON LAIN NO. BON-240002'),
(13, '2024-09-22 02:35:13', 'admin', 'ADD JAMINAN NO. J-2400001'),
(14, '2024-09-22 02:36:14', 'admin', 'ADD JAMINAN NO. J-2400002'),
(15, '2024-09-22 02:36:19', 'admin', 'ADD PAY BACK JAMINAN NO. J-2400002'),
(16, '2024-09-22 02:36:26', 'admin', 'ADD PAY BACK JAMINAN NO. J-2400001'),
(17, '2024-09-22 02:37:09', 'admin', 'ADD DP CUSTOMER NO. DP-2400001'),
(18, '2024-09-22 02:39:20', 'admin', 'ADD DP CUSTOMER NO. DP-2400002');

-- --------------------------------------------------------

--
-- Table structure for table `m_bank`
--

CREATE TABLE `m_bank` (
  `id_bank` int(11) NOT NULL,
  `id_coa` int(11) NOT NULL,
  `nama_bank` varchar(255) NOT NULL,
  `no_bank` varchar(25) NOT NULL,
  `cur` varchar(5) NOT NULL,
  `alamat` text NOT NULL,
  `saldo_awal` double NOT NULL,
  `saldo` double NOT NULL,
  `status` int(11) NOT NULL,
  `invoice` int(11) NOT NULL,
  `an` varchar(255) NOT NULL,
  `swift` varchar(25) NOT NULL,
  `kcp` varchar(75) NOT NULL,
  `created` varchar(25) NOT NULL,
  `tipe` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_bank`
--

INSERT INTO `m_bank` (`id_bank`, `id_coa`, `nama_bank`, `no_bank`, `cur`, `alamat`, `saldo_awal`, `saldo`, `status`, `invoice`, `an`, `swift`, `kcp`, `created`, `tipe`) VALUES
(1788, 1788, 'Bank BCA', '888-000-8888', 'USD', '', 0, 0, 1, 1, 'MY FORWARDING', '', '', 'admin', 30),
(1787, 1787, 'Bank BCA', '999-000-9999', 'IDR', '', 0, 0, 1, 1, 'MY FORWARDING', '', '', 'admin', 30),
(1785, 1785, 'Cash', 'IDR', 'IDR', '', 0, 0, 1, 0, '', '', '', 'admin', 8),
(1786, 1786, 'Cash', 'USD', 'USD', '', 0, 0, 1, 0, '', '', '', 'admin', 8);

-- --------------------------------------------------------

--
-- Table structure for table `m_coa`
--

CREATE TABLE `m_coa` (
  `id_coa` int(11) NOT NULL,
  `id_parent` int(11) NOT NULL,
  `kode_coa` varchar(20) NOT NULL,
  `nama_coa` varchar(255) NOT NULL,
  `level` int(11) NOT NULL,
  `sub` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `saldo` double NOT NULL,
  `kunci` int(11) NOT NULL,
  `id_user` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_coa`
--

INSERT INTO `m_coa` (`id_coa`, `id_parent`, `kode_coa`, `nama_coa`, `level`, `sub`, `type`, `saldo`, `kunci`, `id_user`) VALUES
(1, 0, '1', 'ASSET', 1, 1, 1, 0, 1, '0'),
(2, 0, '2', 'HUTANG', 1, 1, 2, 0, 1, '0'),
(3, 0, '3', 'MODAL', 1, 1, 3, 0, 1, '0'),
(4, 0, '4', 'PENDAPATAN', 1, 1, 4, 0, 1, '0'),
(28, 1, '', 'Asset Lancar', 2, 1, 1, 0, 1, 'admin'),
(29, 1, '', 'Asset Tidak Lancar', 2, 1, 1, 0, 1, 'admin'),
(30, 28, '', 'Bank', 3, 1, 1, 0, 1, 'admin'),
(36, 28, '', 'Piutang Customer', 3, 0, 1, 0, 1, 'admin'),
(37, 28, '', 'Piutang Agent', 3, 0, 1, 0, 1, 'admin'),
(38, 28, '', 'Piutang Lain-lain', 3, 0, 1, 0, 1, 'admin'),
(5, 0, '5', 'HARGA POKOK PENJUALAN', 1, 1, 5, 0, 1, ''),
(48, 2, '', 'Hutang Agent', 2, 0, 2, 0, 1, 'admin'),
(40, 29, '', 'Peralatan', 3, 0, 1, 0, 1, 'admin'),
(41, 29, '', 'Akumulasi Peralatan', 3, 0, 1, 0, 1, 'admin'),
(42, 29, '', 'Kendaraan', 3, 0, 1, 0, 1, 'admin'),
(43, 29, '', 'Akumulasi Penyusutan Kendaraan', 3, 0, 1, 0, 1, 'admin'),
(44, 2, '', 'Hutang Vendor', 2, 0, 2, 0, 1, 'admin'),
(47, 3, '', 'Modal', 2, 0, 3, 0, 1, 'admin'),
(49, 4, '', 'Pendapatan Import', 2, 0, 4, 0, 1, 'admin'),
(50, 4, '', 'Pendapatan Ekspor', 2, 0, 4, 0, 1, 'admin'),
(1785, 8, '', 'Cash (IDR)', 4, 0, 1, 0, 1, ''),
(1783, 6, '', 'Biaya Entertaint', 2, 0, 6, 0, 1, 'admin'),
(69, 3, '', 'Laba Ditahan', 2, 0, 3, 0, 1, 'admin'),
(68, 2, '', 'Hutang Bank', 2, 0, 2, 0, 1, 'admin'),
(70, 3, '', 'Prive', 2, 0, 3, 0, 1, 'admin'),
(67, 28, '', 'PPN Masukan', 3, 0, 1, 0, 1, 'admin'),
(6, 0, '6', 'BIAYA/PENGELUARAN', 1, 1, 6, 0, 1, 'admin'),
(76, 5, '', 'HPP Import', 2, 0, 5, 0, 1, 'admin'),
(85, 5, '', 'HPP Ekspor', 2, 0, 5, 0, 1, 'admin'),
(89, 2, '', 'PPN Keluaran', 2, 0, 2, 0, 1, 'admin'),
(173, 28, '', 'PPH 23 Di Muka', 3, 0, 1, 0, 1, 'admin'),
(123, 103, '', 'Pajak', 1, 0, 7, 0, 1, 'admin'),
(174, 2, '', 'PPH 23 Hutang Pajak', 2, 0, 2, 0, 1, 'admin'),
(7, 999, '', 'Transaksi Antar Bank', 0, 0, 0, 0, 1, ''),
(192, 2, '', 'PPH 21 Hutang Pajak', 2, 0, 2, 0, 1, 'accounting1'),
(217, 28, '', 'Jaminan', 3, 0, 1, 0, 1, 'admin'),
(207, 4, '', 'Pendapatan Domestic', 2, 0, 4, 0, 1, 'admin'),
(221, 2, '', 'Deposit Customer', 2, 0, 2, 0, 1, 'admin'),
(265, 3, '', 'Laba Tahun Berjalan', 2, 0, 3, 0, 1, 'FN-01'),
(266, 28, '', 'Temporary Payment', 3, 0, 1, 0, 1, 'FN-01'),
(8, 28, '', 'Cash', 3, 1, 1, 0, 1, 'admin'),
(177, 28, '', 'PPH 21 Dimuka', 3, 0, 1, 0, 1, 'admin'),
(1784, 6, '', 'Biaya Asuransi', 2, 0, 6, 0, 1, 'admin'),
(1778, 6, '', 'Biaya Perlengkapan Kantor', 2, 0, 6, 0, 1, 'admin'),
(208, 5, '', 'HPP Domestic', 2, 0, 5, 0, 1, 'admin'),
(292, 4, '', 'Pendapatan Lain-Lain', 2, 0, 4, 0, 1, 'admin'),
(1779, 6, '', 'Biaya Gaji', 2, 0, 6, 0, 1, 'admin'),
(1780, 6, '', 'Biaya Listrik', 2, 0, 6, 0, 1, 'admin'),
(1781, 6, '', 'Biaya Telepon', 2, 0, 6, 0, 1, 'admin'),
(1782, 6, '', 'Biaya Internet', 2, 0, 6, 0, 1, 'admin'),
(1786, 8, '', 'Cash (USD)', 4, 0, 1, 0, 1, ''),
(1787, 30, '', 'Bank BCA (999-000-9999)', 4, 0, 1, 0, 1, ''),
(1788, 30, '', 'Bank BCA (888-000-8888)', 4, 0, 1, 0, 1, ''),
(1789, 6, '', 'Biaya Operasional', 2, 0, 6, 0, 0, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `m_cost`
--

CREATE TABLE `m_cost` (
  `id_cost` int(11) NOT NULL,
  `nama_cost` varchar(255) NOT NULL,
  `jenis` int(11) NOT NULL,
  `id_user` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_cost`
--

INSERT INTO `m_cost` (`id_cost`, `nama_cost`, `jenis`, `id_user`, `status`) VALUES
(415, 'EDI ORIGIN', 0, 'Admin', 1),
(414, 'DOC FEE DESTINATION', 0, 'Admin', 1),
(413, 'DOC FEE ORIGIN', 0, 'Admin', 1),
(416, 'EDI DESTINATION', 0, 'Admin', 1),
(417, 'CUSTOM EXPORT', 0, 'Admin', 1),
(418, 'CUSTOM IMPORT', 0, 'Admin', 1),
(419, 'PICK UP', 0, 'Admin', 1),
(420, 'DELIVERY', 0, 'Admin', 1),
(421, 'HANDLING FEE ORIGIN', 0, 'Admin', 1),
(422, 'HANDLING FEE DESTINATION', 0, 'Admin', 1),
(423, 'BL FEE', 0, 'Admin', 1),
(424, 'AWB FEE', 0, 'Admin', 1),
(425, 'INSURANCE', 0, 'Admin', 1),
(426, 'GREEN LINE HANDLING', 0, 'Admin', 1),
(427, 'RED LINE HANDLING', 0, 'Admin', 1),
(428, 'YELLOW LINE HANDLING', 0, 'Admin', 1),
(429, 'PORT CHARGES ORIGIN', 0, 'Admin', 1),
(430, 'PORT CHARGES DESTINATION', 0, 'Admin', 1),
(431, 'PSA', 0, 'Admin', 1),
(432, 'GRI', 0, 'Admin', 1),
(433, 'EBS', 0, 'Admin', 1),
(434, 'CIC', 0, 'Admin', 1),
(435, 'LSS', 0, 'Admin', 1),
(436, 'FAF', 0, 'Admin', 1),
(437, 'YAS', 0, 'Admin', 1),
(438, 'AMS', 0, 'Admin', 1),
(439, 'CFS ORIGIN', 0, 'Admin', 1),
(440, 'CFS DESTINATION', 0, 'Admin', 1),
(441, 'MECHANIC', 0, 'Admin', 1),
(442, 'OTHERS FEE DESTINATION', 0, 'Admin', 1),
(443, 'STORAGE ORIGIN', 0, 'Admin', 1),
(444, 'STORAGE DESTINATION', 0, 'Admin', 1),
(445, 'ADMIN FEE ORIGIN', 0, 'Admin', 1),
(446, 'ADMIN FEE DESTINATION', 0, 'Admin', 1),
(447, 'VGM', 0, 'Admin', 1),
(448, 'SEAL FEE', 0, 'Admin', 1),
(449, 'THC ORIGIN', 0, 'Admin', 1),
(450, 'THC DESTINATION', 0, 'Admin', 1),
(451, 'LIFT OFF ORIGIN', 0, 'Admin', 1),
(452, 'LIFT OFF DESTINATION', 0, 'Admin', 1),
(453, 'LIFT ON ORIGIN', 0, 'Admin', 1),
(454, 'LIFT ON DESTINATION', 0, 'Admin', 1),
(455, 'LABOUR ORIGIN', 0, 'Admin', 1),
(456, 'LABOUR DESTINATION', 0, 'Admin', 1),
(457, 'INSPECTION ORIGIN', 0, 'Admin', 1),
(458, 'INSPECTION DESTINATION', 0, 'Admin', 1),
(459, 'EXTRA HANDLING', 0, 'Admin', 1),
(460, 'OVERNIGHT ORIGIN CHARGES', 0, 'Admin', 1),
(461, 'OVERNIGHT DESTINATION CHARGES', 0, 'Admin', 1),
(462, 'ISPS', 0, 'Admin', 1),
(463, 'FORKLIFT', 0, 'Admin', 1),
(464, 'BEHANDLE', 0, 'Admin', 1),
(465, 'EXW CHARGES', 0, 'Admin', 1),
(466, 'FCA CHARGES', 0, 'Admin', 1),
(467, 'TELEX FEE', 0, 'Admin', 1),
(468, 'AGENCY FEE', 0, 'Admin', 1),
(469, 'COLLECT FEE', 0, 'Admin', 1),
(470, 'ESCORT FEE', 0, 'Admin', 1),
(471, 'AIR FREIGHT', 0, 'Admin', 1),
(472, 'SEA FREIGHT', 0, 'Admin', 1),
(473, 'STAMP FEE', 0, 'Admin', 1),
(474, 'MANIFEST FEE', 0, 'Admin', 1),
(475, 'PSS', 0, 'Admin', 1),
(476, 'EXPORT LICENSE', 0, 'Admin', 1),
(477, 'INLAND FEE', 0, 'Admin', 1),
(478, 'ADDITIONAL RED LINE', 0, 'Admin', 1),
(479, 'ADDITIONAL GREEN LINE', 0, 'Admin', 1),
(480, 'BL REVISION', 0, 'Admin', 1),
(481, 'COURIER FEE', 0, 'Admin', 1),
(482, 'EIS', 0, 'Admin', 1),
(483, 'REWORK CHARGES', 0, 'Admin', 1),
(484, 'RE-IMPORT FEE', 0, 'Admin', 1),
(485, 'RE-EXPORT FEE', 0, 'Admin', 1),
(486, 'READDRESS FEE', 0, 'Admin', 1),
(487, 'FUMIGATION FEE', 0, 'Admin', 1),
(488, 'PHYTOSANITARY', 0, 'Admin', 1),
(489, 'EMKL FEE', 0, 'Admin', 1),
(490, 'PCO', 0, 'Admin', 1),
(491, 'DO FEE', 0, 'Admin', 1),
(492, 'PU FEE', 0, 'Admin', 1),
(493, 'ADDITIONAL YELLOW LINE', 0, 'Admin', 1),
(494, 'TEMPORARY STORAGE', 0, 'Admin', 1),
(495, 'PLB FEE', 0, 'Admin', 1),
(496, 'BC 1.6', 0, 'Admin', 1),
(497, 'BC 1.8', 0, 'Admin', 1),
(498, 'LO/LO', 0, 'Admin', 1),
(499, 'BOOKING FEE', 0, 'Admin', 1),
(500, 'LOADING FEE', 0, 'Admin', 1),
(501, 'UNLOADING FEE', 0, 'Admin', 1),
(502, 'EFF', 0, 'Admin', 1),
(503, 'SOLAS FEE', 0, 'Admin', 1),
(504, 'RR FEE', 0, 'Admin', 1),
(505, 'INSENTIVE BL', 0, 'Admin', 1),
(506, 'MARKETING FEE', 0, 'Admin', 1),
(507, 'OVERTIME', 0, 'Admin', 1),
(508, 'DO CHARGE', 0, 'Admin', 1),
(509, 'SURVEYOR HANDLING', 0, 'Admin', 1),
(510, 'SURVEYOR CHARGES', 0, 'Admin', 1),
(511, 'UNDERNAME FEE', 0, 'Admin', 1),
(512, 'DUTY & IMPORT TAX', 0, 'Admin', 1),
(513, 'AIRPORT TRANSFER FEE', 0, 'Admin', 1),
(514, 'X-RAY', 0, 'Admin', 1),
(515, 'SSC (SECURITY SURCHARGE)', 0, 'Admin', 1),
(516, 'PENGANTAR DO', 0, 'Admin', 1),
(517, 'INSENTIVE DO', 0, 'Admin', 1),
(518, 'PROFIT SHARE', 0, 'Admin', 1),
(519, 'LOADING EMPTY', 0, 'Admin', 1),
(520, 'BOOKING CONTAINER', 0, 'Admin', 1),
(521, 'FIAT', 0, 'Admin', 1),
(522, 'ECRS', 0, 'Admin', 1),
(523, 'VAT 1%', 0, 'Admin', 1),
(524, 'DG SURCHARGES', 0, 'Admin', 1),
(525, 'REPAIR', 0, 'Admin', 1),
(526, 'CANCELLATION FEE', 0, 'Admin', 1),
(527, 'ADMIN BANK', 0, 'Admin', 1),
(528, 'FUEL SURCHARGE', 0, 'Admin', 1),
(529, 'HANDLING K3L', 0, 'Admin', 1),
(530, 'ZB', 0, 'Admin', 1),
(531, 'DEMURRAGE', 0, 'Admin', 1),
(532, 'DP SHIPMENT', 0, 'Admin', 1),
(533, 'LS CHARGES', 0, 'Admin', 1),
(534, 'COVID RAPID TEST', 0, 'Admin', 1),
(535, 'NEW BUNKER SURCHARGE (NBS)', 0, 'Admin', 1),
(536, 'PARKIR', 0, 'Admin', 1),
(537, 'ADMIN REPAIR', 0, 'Admin', 1),
(538, 'PPN', 0, 'Admin', 1),
(539, 'EXPORT SERVICE CHARGE', 0, 'Admin', 1),
(540, 'FORM E', 0, 'Admin', 1),
(541, 'ESD', 0, 'Admin', 1),
(542, 'TOLL FEE', 0, 'Admin', 1),
(543, 'IMO CHARGES', 0, 'Admin', 1),
(544, 'RECEIVED CHARGES', 0, 'Admin', 1),
(545, 'PELUNASAN SHIPMENT', 0, 'Admin', 1),
(546, 'WAREHOUSE CHARGES', 0, 'Admin', 1),
(547, 'CONTAINER SERVICE CHARGES ORIGIN', 0, 'Admin', 1),
(548, 'ADMIN PERPANJANGAN DO', 0, 'Admin', 1),
(549, 'CHANNEL FEE', 0, 'Admin', 1),
(550, 'ISS', 0, 'Admin', 1),
(551, 'FSC', 0, 'Admin', 1),
(552, 'SUPERVISION FEE', 0, 'Admin', 1),
(553, 'OTHERS', 0, 'Admin', 1),
(554, 'DG HANDLING CHARGES', 0, 'Admin', 1),
(555, 'GATE CHARGES', 0, 'Admin', 1),
(556, 'TUNNEL FEE', 0, 'Admin', 1),
(557, 'DG PACKING CHARGES', 0, 'Admin', 1),
(558, 'DG DECLARATION', 0, 'Admin', 1),
(559, 'SYAHBANDAR', 0, 'Admin', 1),
(560, 'LOCAL CHARGES ORIGIN', 0, 'Admin', 1),
(561, 'LOCAL CHARGES DESTINATION', 0, 'Admin', 1),
(562, 'REFUND DEMURRAGE', 0, 'Admin', 1),
(563, 'SURABAYA CANAL FEE', 0, 'Admin', 1),
(564, 'JASA BONGKAR', 0, 'Admin', 1),
(565, 'OVERWEIGHT SURCHARGE', 0, 'Admin', 1),
(566, 'TEMPORARY STOWAGE CHARGE', 0, 'Admin', 1),
(567, 'AFS FEE', 0, 'Admin', 1),
(568, 'SWITCH BL', 0, 'Admin', 1),
(569, 'INDRA WIRA', 0, 'Admin', 1),
(570, 'QUARANTINE INSPECTION', 0, 'Admin', 1),
(571, 'KT 9', 0, 'Admin', 1),
(572, 'SSM ONLINE', 0, 'Admin', 1),
(573, 'HANDLING PPK ONLINE', 0, 'Admin', 1),
(574, 'HANDLING REGISTRATION', 0, 'Admin', 1),
(575, 'EIR', 0, 'Admin', 1),
(576, 'AMENDMENT FEE', 0, 'Admin', 1),
(577, 'REPRINT BL', 0, 'Admin', 1),
(578, 'PPH 23', 0, 'Admin', 1),
(579, 'ENS', 0, 'Admin', 1),
(580, 'REFUND', 0, 'Admin', 1),
(581, 'FREIGHT ALL IN', 0, 'Admin', 1),
(582, 'LATE PICK UP B/L', 0, 'Admin', 1),
(583, 'TAC', 0, 'Admin', 1),
(584, 'HAULAGE DEPO CLEANING', 0, 'Admin', 1),
(585, 'UP LOAD COPARN', 0, 'Admin', 1),
(586, 'LOGISTIC', 0, 'Admin', 1),
(587, 'GENSET', 0, 'Admin', 1),
(588, 'CLEANING CHARGES', 0, 'Admin', 1),
(589, 'GST 18%', 0, 'Admin', 1),
(590, 'ENTRY WAREHOUSE FEE', 0, 'Admin', 1),
(591, 'COO', 0, 'Admin', 1),
(592, 'STRIPPING', 0, 'Admin', 1),
(593, 'PECAH POS', 0, 'Admin', 1),
(594, 'ADMINISTRATION FEE', 0, 'Admin', 1),
(595, 'OT HANDLING', 0, 'Admin', 1),
(596, 'DHC / PORTNET', 0, 'Admin', 1),
(597, 'UNPACKING CHARGES', 0, 'Admin', 1),
(598, 'EXPORT DOC VALIDATION', 0, 'Admin', 1),
(599, 'ANGSUR OB', 0, 'Admin', 1),
(600, 'LCL CHARGES', 0, 'Admin', 1),
(601, 'PPFTZ 01', 0, 'Admin', 1),
(602, 'CDD', 0, 'Admin', 1),
(603, 'FUSO', 0, 'Admin', 1),
(604, 'TRONTON', 0, 'Admin', 1),
(605, 'WINGBOX', 0, 'Admin', 1),
(608, 'CDE', 0, 'Admin', 1),
(609, 'GRANDMAX', 0, 'Admin', 1),
(610, 'DHE FEE', 0, 'Admin', 1),
(611, 'PACKAGING FEE', 0, 'Admin', 1),
(612, 'OVERNIGHT TRUCKING', 0, 'Admin', 1),
(613, 'BC 2.8', 0, 'Admin', 1),
(614, 'HANDLING', 0, 'admin', 1),
(615, 'HANDLING OUT', 0, 'Admin', 1),
(616, 'ENVIRONMENTAL FUEL FEE', 0, 'Admin', 1),
(617, 'LPC01', 0, 'Admin', 1),
(618, 'CCAM', 0, 'Admin', 1),
(619, 'OVERLENGTH SURCHARGE', 0, 'Admin', 1),
(620, 'REGISTRASI SKI', 0, 'Admin', 1),
(621, 'HANDLING SKI', 0, 'Admin', 1),
(622, 'PNBP SKI', 0, 'Admin', 1),
(623, 'WHARFAGE', 0, 'Admin', 1),
(624, 'DRAYAGE CHARGE', 0, 'Admin', 1),
(625, 'CONCOR CHARGES', 0, 'Admin', 1),
(626, 'RAIL FREIGHT HAULAGE', 0, 'Admin', 1),
(627, 'DETENTION ORIGIN', 0, 'Admin', 1),
(628, 'SUBSEQUENT HANDLING FEES', 0, 'Admin', 1),
(629, 'AIRWAY BILL', 0, 'Admin', 1),
(630, 'TERMINAL CHARGES', 0, 'Admin', 1),
(631, 'KGS', 0, 'Admin', 1),
(632, 'QUARANTINE HANDLING', 0, 'Admin', 1),
(633, 'KG', 0, 'Admin', 1),
(634, 'MATERAI', 0, 'Admin', 1),
(635, 'LANDING', 0, 'Admin', 1),
(636, 'MANIFEST CORRECTION FEE', 0, 'Admin', 1),
(637, 'EX-WORK BL FEE', 0, 'Admin', 1),
(638, 'DATA PROCESSING FEE', 0, 'Admin', 1),
(639, 'EX-WORK TELEX RELASE', 0, 'Admin', 1),
(640, 'FUEL ADJUSTEMNT FACTOR', 0, 'Admin', 1),
(641, 'EX-WORK CUSTOM CLEARANCE', 0, 'Admin', 1),
(642, 'EX-WORK DOCUMENTATION', 0, 'Admin', 1),
(643, 'EX-WORK HANDLING FEE', 0, 'Admin', 1),
(644, 'EX-WORK TRANSPORTATION FEE', 0, 'Admin', 1),
(645, 'SST AMT', 0, 'Admin', 1),
(646, 'ARBITRARY LOAD', 0, 'Admin', 1),
(647, 'CONTAINER FACILITY CHARGE', 0, 'Admin', 1),
(648, 'IMPORT SERVICE FEE', 0, 'Admin', 1),
(649, 'LESS PAYMENT', 0, 'Admin', 1),
(650, 'HEAVYLIFTING CHARGES', 0, 'Admin', 1),
(651, 'SPECIAL PERMIT', 0, 'Admin', 1),
(652, 'SPECIAL HANDLING', 0, 'Admin', 1),
(653, 'LABEL DG', 0, 'Admin', 1),
(654, 'DG PERMIT', 0, 'Admin', 1),
(655, 'CHANGE OF DESTINATION', 0, 'Admin', 1),
(656, 'BL AMENDMENT FEE', 0, 'Admin', 1),
(657, 'OFT', 0, 'Admin', 1),
(658, 'HEALTH CERTIFICATE', 0, 'Admin', 1),
(659, 'CFC ORIGIN', 0, 'Admin', 1),
(660, 'CARTAGE CHARGES', 0, 'Admin', 1),
(661, 'SCMC', 0, 'Admin', 1),
(662, 'PALLETIZE CHARGES', 0, 'Admin', 1),
(664, 'PHYSICAL CHECKING', 0, 'Admin', 1),
(665, 'H/C', 0, 'Admin', 1),
(666, 'BC 2.3', 0, 'Admin', 1),
(667, 'TRUCKING CHARGE ', 0, 'Admin', 1),
(668, 'TID/TPI', 0, 'Admin', 1),
(669, 'EU ETS SURCHARGE', 0, 'Admin', 1),
(670, 'CONTINGENCY CHARGE', 0, 'Admin', 1),
(671, 'PPN Repair', 0, 'Admin', 1),
(672, 'HAULAGE CHARGES', 0, 'Admin', 1),
(673, 'PEB', 0, 'Admin', 1),
(674, 'DEPOSIT CONTAINER', 0, 'Admin', 1),
(675, 'FORFAIT FOB CHARGES', 0, 'Admin', 1),
(676, 'ADDITIONAL HANDLING FEE DEST', 0, 'Admin', 1),
(677, 'REPO', 0, 'Admin', 1),
(678, 'ADDITIONAL DELIVERY', 0, 'Admin', 1),
(679, 'TOESLAG', 0, 'Admin', 1),
(680, 'PENITIPAN KONTAINER ', 0, 'Admin', 1),
(681, 'GUARANTEE CONTAINER', 0, 'Admin', 1),
(682, 'WEIGHING TANK', 0, 'Admin', 1),
(683, 'RE-WEIGHING ', 0, 'Admin', 1),
(684, 'HARBOR DATA FEES', 0, 'Admin', 1),
(685, 'DELTA C FEES', 0, 'Admin', 1),
(686, 'SURCHARGE GAS OIL', 0, 'Admin', 1),
(687, 'EXTEND DO', 0, 'Admin', 1),
(688, 'DELTA ARCHIVING FEES', 0, 'Admin', 1),
(689, 'VAT', 0, 'Admin', 1),
(690, 'INDOOR MONITORING (PLB)', 0, 'Admin', 1),
(691, 'OUTDOOR MONITORING (PLB)', 0, 'Admin', 1),
(692, 'HANDLING IN (PLB)', 0, 'Admin', 1),
(693, 'HANDLING OUT (PLB)', 0, 'Admin', 1),
(694, 'PALLET RENTAL (PLB)', 0, 'Admin', 1),
(695, 'CUSTOM BC 2.8 (PLB)', 0, 'Admin', 1),
(696, 'ESEAL (PLB)', 0, 'Admin', 1),
(697, 'HANDLE RED LINE (PLB)', 0, 'Admin', 1),
(698, 'LOLO (PLB)', 0, 'Admin', 1),
(699, 'TRUCKING (PLB)', 0, 'Admin', 1),
(700, 'MAGNETIL INSPEETION', 0, 'Admin', 1),
(701, 'REVISI INVOICE', 0, 'Admin', 1),
(702, 'EXPORT DECLARATION', 0, 'Admin', 1),
(703, 'TARE WEIGHT', 0, 'Admin', 1),
(704, 'Custom Clearance BC 1.6 (PLB)', 0, 'Admin', 1),
(705, 'EQUIPMENT SERVICE CHARGE', 0, 'Admin', 1),
(706, 'WEIGHING FEE', 0, 'Admin', 1),
(707, 'PT. UPS CARDIG INTERNATIONAL', 0, 'Admin', 1),
(708, 'ISPM FUMIGATION FEE', 0, 'Admin', 1),
(709, 'ADM LIFT OFF', 0, 'Admin', 1),
(710, 'LS CERTIFICATE ', 0, 'Admin', 1),
(711, 'OPERATION FEE', 0, 'Admin', 1),
(712, 'LUMP SUM CHARGE', 0, 'Admin', 1),
(713, 'ALL IN CHARGE', 0, 'Admin', 1),
(714, 'SPREADER', 0, 'Admin', 1),
(715, 'CGO INSPECTION REPORT', 0, 'Admin', 1),
(716, 'CAF (Currency Adjustment Factor)', 0, 'Admin', 1),
(717, 'AFR', 0, 'Admin', 1),
(718, 'ADVANCE FILING RULES', 0, 'Admin', 1),
(719, 'OPERATION COST CONTRIBUTION', 0, 'Admin', 1),
(720, 'DOMESTIK ', 0, 'Admin', 1),
(721, 'HANDLING RE-EXPORT', 0, 'Admin', 1),
(722, 'PENGAJUAN RE-EXPORT', 0, 'Admin', 1),
(723, 'MAGNETIC', 0, 'Admin', 1),
(724, 'SEWA KONTAINER', 0, 'Admin', 1),
(725, 'JAMINAN STORAGE KONTAINER', 0, 'Admin', 1),
(726, 'Gerakan Kontainer Barang Penegahan', 0, 'Admin', 1),
(727, 'peak season surcharge', 0, 'Admin', 1),
(728, 'RE-PACKING', 0, 'Admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `m_cost_tr`
--

CREATE TABLE `m_cost_tr` (
  `id_cost` int(11) NOT NULL,
  `nama_cost` varchar(255) NOT NULL,
  `jenis` int(11) NOT NULL,
  `id_user` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_cost_tr`
--

INSERT INTO `m_cost_tr` (`id_cost`, `nama_cost`, `jenis`, `id_user`, `status`) VALUES
(2, 'LOLO', 0, 'admin', 1),
(3, 'KAWALAN', 0, 'admin', 1),
(1, 'BIAYA KIRIM', 0, 'admin', 1),
(4, 'BIAYA PACKING', 0, 'admin', 1),
(0, 'DDX', 0, 'admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `m_cur`
--

CREATE TABLE `m_cur` (
  `id` int(11) NOT NULL,
  `nama` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `m_cur`
--

INSERT INTO `m_cur` (`id`, `nama`) VALUES
(1, 'IDR'),
(2, 'USD');

-- --------------------------------------------------------

--
-- Table structure for table `m_cur_quo`
--

CREATE TABLE `m_cur_quo` (
  `id` int(11) NOT NULL,
  `nama` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_cur_quo`
--

INSERT INTO `m_cur_quo` (`id`, `nama`) VALUES
(1, 'IDR'),
(2, 'USD'),
(3, 'EUR'),
(4, 'SGD'),
(5, 'RMB'),
(6, 'CNY');

-- --------------------------------------------------------

--
-- Table structure for table `m_cust`
--

CREATE TABLE `m_cust` (
  `id_cust` int(11) NOT NULL,
  `nama_cust` varchar(255) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `caption` varchar(5) NOT NULL,
  `kontak` varchar(50) NOT NULL,
  `no_npwp` varchar(50) NOT NULL,
  `alamat` text NOT NULL,
  `telp` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `created` varchar(50) NOT NULL,
  `dirut` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `m_cust`
--

INSERT INTO `m_cust` (`id_cust`, `nama_cust`, `unit`, `caption`, `kontak`, `no_npwp`, `alamat`, `telp`, `email`, `status`, `created`, `dirut`) VALUES
(3, 'EVERGREEN SHIPPING INDONESIA', '', '', '', '', 'Mega Plaza building, 9th floors\nJL. H.R Rasuna said Kav. C-3, Jakarta 12920', '', '', 1, 'admin', ''),
(4, 'GARUDA INDONESIA', '', '', '', '', 'Jl. Kebon Sirih No. 44\nJakarta 10110, Indonesia', '', '', 1, 'admin', ''),
(5, 'PT.  UNI INDO JAYA', '', '', 'Bp. ALex', '', 'Jl. Raya Bekasi Km. X \nPulo Gadung III Plumpang\nJakarta Timu', '', '', 1, 'admin', ''),
(6, 'PT. ALAM MAJU TERUS', '', '', '', '', 'Sunter Mall Blok X No.7, \nJl.Danau Sunter Utara Kav.G23\nJakarta 14350', '', '', 1, 'admin', ''),
(7, 'PT. BAHARI INDONESIA TERUS', '', '', '', '', 'Wisma Indonesia 21th Floor\nJl.H.R.Rasuna Kav.X-1 \nJakarta Selatan', '', '', 1, 'admin', ''),
(8, 'PT. BANGKITLAH INDONESIA', '', '', '', '', 'Jl.Kebon Kacang Raya,\nJakarta 10240', '', '', 1, 'admin', ''),
(9, 'PT. BUKIT INDAH PRIMA INDONESIA', '', '', '', '', 'Kompleks ROXY MAS,Blok S-2 No.X-11\nJL.KH.Hasyim Ashari\nJakarta Pusat', '', '', 1, 'admin', ''),
(10, 'PT. CIBITUNG TUNGGAL PERKASA INDONESIA', 'GUDANG', '', '', '', 'Central Plaza, 5rd Floor\nJl.Jend.Sudirman Kav.X \nJakarta 12930', '', '', 1, 'admin', ''),
(11, 'PT. CIPTA INDONESIA MAJU', '', '', '', '', 'Jl.Pulo Ayang raya,\nKawasan industri Pulogadung \nJakarta13930', '', '', 1, 'admin', ''),
(12, 'PT. CIPTA KARYA KU INDONESIA', '', '', '', '', 'Jl.Wijaya XI,No.X AB\nKebayoran Baru\nJakarta Selatan', '', '', 1, 'admin', ''),
(13, 'PT. DAYA GUNA SELALU', '', '', '', '', 'Jl.Gunung Sahari Raya\nJakarta Pusat', '', '', 1, 'admin', ''),
(14, 'PT. DUTA INDONESIA SUKSES', '', '', '', '', 'Kompleks Perkantoran \nJl.Majaoahit 18-22 Blk B\nJakarta 10160', '', '', 1, 'admin', ''),
(15, 'PT. FAJAR PAGI SIANG', '', '', '', '', 'Jl.Berkah No 9 \nJakarta 12860', '', '', 1, 'admin', ''),
(16, 'PT. GUNUNG BUNDAR SEJAHTERA', '', '', '', '', 'Jl.Letjen Suprapto\nJakarta 10530', '', '', 1, 'admin', ''),
(17, 'PT. GUNUNG MAKMUR TERUS', '', '', '', '', 'Jl.Tanah Abang II\nJakarta 10160', '', '', 1, 'admin', ''),
(18, 'PT. HASIL USAHA HALAL', '', '', '', '', 'Wisma INDO Tower 5th Floor\nJl. S.Parman,Kav X\nJakarta 11410', '', '', 1, 'admin', ''),
(19, 'PT. INTI GUNA MAKMUR', '', '', '', '', 'Jl.Tanah Abang II\nJakarta', '', '', 1, 'admin', ''),
(20, 'PT. JAKARTA METROPOLITAN', '', '', '', '', 'Jl. HR. Rasuna Said \nKuningan Jakarta 12920', '', '', 1, 'admin', ''),
(21, 'PT. JAKARTA RAYA SUKSES', '', '', '', '', 'Jl.Gajah Mada No. X\nJakarta Pusat', '', '', 1, 'admin', ''),
(22, 'PT. JAYA SENTOSA MAKMUR', '', '', '', '', 'Jl. Raya Jakarta\nIndonesia', '', '', 1, 'admin', ''),
(23, 'PT. SURYA CEMERLANG SELALU', '', '', '', '', 'Jl. Kawasan Industri Bekasi \nCibitung Bekasi 17520', '', '', 1, 'admin', ''),
(24, 'PT. SINOKOR INTERNATIONAL', '', '', '', '', '', '', '', 1, 'admin', ''),
(25, 'PT. STAR CONCORD INDONESIA', '', '', '', '', 'JALAN LETJEN SUPPRAPTO \nCEMPAKA BARU KEMAYORAN JAKARTA PUSAT \nDKI JAKARTA 10650', '', '', 1, 'admin', ''),
(26, 'COSCO SHIPPING LINE', '', '', 'Mr. Uj', '', 'qwew', '436456', '', 1, 'admin', ''),
(27, 'QATAR AIRWAYS CARGO', '', '', '', '', '', '', '', 1, 'admin', ''),
(28, 'GALAK FREIGHT LTD', '', '', '', '', 'Star Hub, Next To ITC Grand Maratha Sheraton, \nInternational Airport Road, \nAndheri East Mumbai - 400 059, Maharashtra, India\n', '', '', 1, 'admin', ''),
(29, 'PT. MEGA TOTAL TRANSPORTASI', '', '', '', '', 'Gedung Maspion Plaza Gunung Sahari 10th Floor,\nJl.Gunung Sahari, Kavling 18, Kel. Pademangan,\nKec. Pademangan, Jakarta Utara 14420, \nIndonesia', '', '', 1, 'admin', ''),
(30, 'PT. BINTANG DIATAS ANGKASA', '', '', '', '', 'Wisma Mitra Sunter \nJl. Yos Sudarso Boulevard Mitra Sunter\nJakarta Utara 14350', '', '', 1, 'admin', ''),
(31, 'CARGO CONSOL PVT LTD', '', '', '', '', 'CHENNAI CITI CENTRE\nUNIT NO. A34 NO 10 & 11 \nMylapore - Chennai - 600 004.\n', '', '', 1, 'admin', ''),
(32, 'TRANSPORT CONTAINERLINE, INC.', '', '', '', '', '733 Third Avenue, 16th Floor\nNew York, NY 10017', '', '', 1, 'admin', ''),
(33, 'ATLANTIS PARCEL LOGISTICS SDN BHD', '', '', '', '', 'JALAN SIERRA 210/1,\nBANDAR 16 SIERRA PUCHONG\n47120 PUCHONG SELANGOR', '', '', 1, 'admin', ''),
(34, 'AGENCY PHILIPPINES INC.', '', '', 'Mr. SDD', '', 'BLOCK 322 LOT 346 PLASTIC CITY AVE,\nVIENTE REALES, VALENZUELA CITY\nZIP CODE: 1440', '97455', 'a@yahoo.com', 1, 'admin', 'ALI'),
(35, 'VIETNAM CHEMICAL CO., LTD.', '', '', '', '', 'ON BEHALF OF KURARAY HONG\nKONG CO., LTD\nLONG THANH I.Z., LONG THANH\nDISTRICT DONG NAI PROVINCE,\nVIETNAM', '', '', 1, 'admin', ''),
(36, 'PACIFIC CONCORD INTERNATIONAL LTD', '', '', '', '', 'NO.156, DUNHUAN RD., SONGSHAN DISTRICT,\nTAIPEI CITY 105, TAIWAN (R.O.C.)', '', '', 1, 'admin', ''),
(37, 'OCEAN LINK FREIGHT SERVICES SDN BHD', '', '', '', '', 'JALAN KASUARINA 170/KS57,\nBANDAR BOTANIC CAPITAL,\n41200 KLANG, SELANGOR', '', '', 1, 'admin', ''),
(38, 'BLUESTAR WORDWIDE LOGISTICS', '', '', '', '', '', '', '', 1, 'admin', ''),
(39, 'WORLDLINK CARGO SERVICES SDN BHD', '', '', '', '', 'BANDAR PUTERI KLANG, 41200 KLANG,\nSELANGOR DARUL EHSAN, MALAYSIA', '', '', 1, 'admin', ''),
(40, 'UNITEX INTERNATIONAL FORWADING', '', '', '', '', 'OWER A BILLION CENTRE 1 \nWANG KWONG ROAD KOWLOON BAY KOWLOON HONGKONG', '', '', 1, 'admin', ''),
(41, 'AGENT FREIGHT INTERNATIONAL CO., LTD', '', '', '', '', '', '', '', 1, 'admin', ''),
(42, 'AGENT UNI LOGISTICS', '', '', '', '', '', '', '', 1, 'admin', ''),
(43, 'WHITE CARGO PTE LTD', '', '', '', '', 'UBI CRESCENT \n037-64 UBITECH PARK SINGAPORE\n408564', '', '', 1, 'admin', ''),
(44, 'AGENT GLOBAL SHIPPING', '', '', '', '', 'BLOCK 322 LOT 346 PLASTIC CITY AVE,\nVIENTE REALES, VALENZUELA CITY\nZIP CODE: 1440', '', '', 1, 'admin', ''),
(45, 'HOLA GLOBAL SOURCING LOGISTICS CO.,LIMITED', '', '', '', '', '', '', '', 1, 'admin', ''),
(46, 'PT YANG MING SHIPPING INDONESIA', '', '', '', '', '135, SENEN RAYA ROAD #9-901 COWEL TOWER\nJAKARTA 10410 JAKARTA', '55555555', 'tes@gmail.com', 1, 'admin', 'MR. ALEX'),
(47, 'ORIENT OVERSEAS CONTAINER LINE', '', '', '', '', '', '', '', 1, 'admin', ''),
(48, 'PT. TRUCKING', '', '', '', '', '', '', '', 1, 'admin', ''),
(59, 'PT. SHIPPER INDONESIA', '', '', '', '', '', '', '', 1, 'admin', ''),
(60, 'PT. CONSIGNEE INDONESIA', '', '', '', '', '', '', '', 1, 'admin', ''),
(67, 'MEINA SHIPPING', '', '', '', '', '', '', '', 1, 'admin', ''),
(68, 'AMTL', '', '', '', '', '', '', '', 1, 'admin', ''),
(69, 'HYE', '', '', '', '', 'JL.TEH NO 3.C TAMANSARI PINANGSIA JAKARTA BARAT', '0216927181', '', 1, 'admin', '');

-- --------------------------------------------------------

--
-- Table structure for table `m_cust_jenis`
--

CREATE TABLE `m_cust_jenis` (
  `id_jenis` int(11) NOT NULL,
  `nama` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `m_cust_jenis`
--

INSERT INTO `m_cust_jenis` (`id_jenis`, `nama`) VALUES
(0, 'CUSTOMER'),
(1, 'VENDOR'),
(2, 'AGENT'),
(3, 'SHIPPING LINES');

-- --------------------------------------------------------

--
-- Table structure for table `m_cust_tr`
--

CREATE TABLE `m_cust_tr` (
  `id_cust` int(11) NOT NULL,
  `nama_cust` varchar(100) NOT NULL,
  `caption` varchar(50) NOT NULL,
  `kontak` varchar(50) NOT NULL,
  `alamat` text NOT NULL,
  `telp` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `created` varchar(50) NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `m_cust_tr`
--

INSERT INTO `m_cust_tr` (`id_cust`, `nama_cust`, `caption`, `kontak`, `alamat`, `telp`, `email`, `status`, `created`, `tanggal`) VALUES
(2, 'PT. ALAM MAJU TERUS', '', '', '', '', '', 1, 'FW', '0000-00-00'),
(3, 'PT. GUNUNG BUNDAR SEJAHTERA', '', '', '', '', '', 1, 'FW', '0000-00-00'),
(5, 'PT. BUKIT INDAH PRIMA INDONESIA', '', '', '', '', '', 1, 'FW', '0000-00-00'),
(9, 'PT. BINTANG DIATAS ANGKASA', '', '', '', '', '', 1, 'FW', '0000-00-00'),
(14, 'PT.  UNI INDO JAYA', '', '', '', '', '', 1, 'FW', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `m_jenis_mobil_tr`
--

CREATE TABLE `m_jenis_mobil_tr` (
  `id_jenis` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `status` int(11) NOT NULL,
  `created` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `m_jenis_mobil_tr`
--

INSERT INTO `m_jenis_mobil_tr` (`id_jenis`, `nama`, `status`, `created`) VALUES
(0, 'sd', 1, 'admin'),
(1, '1 x 20', 1, 'admin'),
(2, '1 x 40', 1, 'admin'),
(3, 'CDD', 1, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `m_jenis_tagihan`
--

CREATE TABLE `m_jenis_tagihan` (
  `id` int(11) NOT NULL,
  `nama` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `m_jenis_tagihan`
--

INSERT INTO `m_jenis_tagihan` (`id`, `nama`) VALUES
(1, 'INV'),
(2, 'RE'),
(3, 'DN'),
(4, 'PR'),
(5, 'CN');

-- --------------------------------------------------------

--
-- Table structure for table `m_kode_inv`
--

CREATE TABLE `m_kode_inv` (
  `id` int(11) NOT NULL,
  `kode` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `m_kode_inv`
--

INSERT INTO `m_kode_inv` (`id`, `kode`) VALUES
(1, '001'),
(2, '002'),
(3, '003'),
(4, '004');

-- --------------------------------------------------------

--
-- Table structure for table `m_kota_tr`
--

CREATE TABLE `m_kota_tr` (
  `id_kota` int(11) NOT NULL,
  `nama_kota` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `created` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `m_kota_tr`
--

INSERT INTO `m_kota_tr` (`id_kota`, `nama_kota`, `status`, `created`) VALUES
(2, 'SURABAYA', 1, 'admin'),
(3, 'JAKARTA', 1, 'admin'),
(4, 'BANDUNG', 1, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `m_kurs`
--

CREATE TABLE `m_kurs` (
  `id` int(11) NOT NULL,
  `kurs` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_kurs`
--

INSERT INTO `m_kurs` (`id`, `kurs`) VALUES
(1, 15500);

-- --------------------------------------------------------

--
-- Table structure for table `m_log`
--

CREATE TABLE `m_log` (
  `id` int(11) NOT NULL,
  `tgl` datetime NOT NULL,
  `ip` varchar(50) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `m_log`
--

INSERT INTO `m_log` (`id`, `tgl`, `ip`, `status`) VALUES
(1, '2024-09-23 07:09:09', '::1', 1),
(2, '2024-09-24 05:09:30', '::1', 1),
(3, '2024-09-24 06:09:24', '::1', 1),
(4, '2024-09-26 05:09:20', '::1', 1),
(5, '2024-09-26 06:09:13', '::1', 1),
(6, '2024-09-27 06:09:35', '::1', 1),
(7, '2024-09-30 06:09:51', '::1', 1),
(8, '2024-09-30 08:09:30', '::1', 1),
(9, '2024-10-01 05:10:09', '::1', 1),
(10, '2024-10-01 16:10:42', '::1', 1),
(11, '2024-10-03 05:10:00', '::1', 1),
(12, '2024-10-03 07:10:06', '::1', 1),
(13, '2024-10-04 05:10:57', '::1', 1),
(14, '2024-10-04 07:10:16', '::1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `m_menu`
--

CREATE TABLE `m_menu` (
  `id_menu` int(11) NOT NULL,
  `id_parent` int(11) NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `link` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `urut` int(11) NOT NULL,
  `warna` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_menu`
--

INSERT INTO `m_menu` (`id_menu`, `id_parent`, `nama_menu`, `link`, `img`, `urut`, `warna`) VALUES
(1, 0, 'DASHBOARD', 'dashboard.php', 'fa fa-dashboard', 1, '#f0ad4e'),
(2, 0, 'QUOTATION', 'quo.php', 'fa fa-pencil-square-o', 2, '#1087dd'),
(3, 0, 'JOB ORDER', 'jo.php', 'fa  fa-reorder', 3, '#3b5998'),
(4, 0, 'DOCUMENT', 'doc.php', 'fa fa-file-text-o', 4, '#2c4762'),
(5, 0, 'FINANCE', '	', 'fa fa-building-o', 5, '#dd4b39'),
(6, 0, 'MASTER DATA', '', 'fa fa-gear', 6, '#587ea3'),
(7, 6, 'Data Partner', 'cust.php', '', 1, ''),
(8, 6, 'Data Cost', 'cost.php', '', 3, ''),
(9, 6, 'Data Port', 'port.php', '', 9, ''),
(10, 6, 'Data Cash/Bank', 'bank.php', '', 10, ''),
(11, 6, 'Data COA', 'coa.php', '', 11, ''),
(12, 6, 'Data User', 'user.php', '', 12, ''),
(13, 4, 'Bill of Lading', 'bl.php', '', 3, ''),
(14, 4, 'Air Way Bill', 'awb.php', '', 3, ''),
(15, 5, 'Kasbon JO', 'kasbon.php', '', 1, ''),
(16, 5, 'Invoice', 'inv.php', '', 16, ''),
(17, 5, 'Payment Request', 'pr.php', '', 17, ''),
(18, 5, 'Credit Note', 'cn.php', '', 18, ''),
(19, 5, 'Debit Note', 'dn.php', '', 19, ''),
(20, 5, 'Cash and Bank', 'cash.php', '', 20, ''),
(21, 5, 'Journal', 'jurnal.php', '', 21, ''),
(22, 5, 'Balance Sheet', 'neraca.php', '', 22, ''),
(23, 5, 'Income Statement', 'lr.php', '', 23, ''),
(24, 5, 'Job Profit', 'gp.php', '', 24, ''),
(25, 6, 'Data Unit Cost', 'unit.php', '', 4, ''),
(27, 5, 'Reimbursement', 're.php', '', 4, ''),
(33, 5, 'Jaminan', 'jaminan.php', '', 2, ''),
(32, 5, 'Kasbon Lain-lain', 'kasbon_lain.php', '', 1, ''),
(34, 5, 'Deposit Customer', 'dp.php', '', 3, ''),
(36, 4, 'Shipping Instruction', 'si.php', '', 1, ''),
(39, 4, 'Notice of Arrival', 'noa.php', '', 5, ''),
(40, 4, 'Booking Confirmation', 'bc.php', '', 2, ''),
(45, 6, 'Data Operasional ', 'opera.php', '', 9, ''),
(50, 6, 'Data Exc. Rate', 'kurs.php', '', 13, ''),
(51, 6, 'Data Unit Item', 'item.php', '', 4, ''),
(52, 0, 'JOB PROFIT', 'gp.php	', 'fa fa-building-o', 5, '#dd4b39');

-- --------------------------------------------------------

--
-- Table structure for table `m_menu_tr`
--

CREATE TABLE `m_menu_tr` (
  `id_menu` int(11) NOT NULL,
  `id_parent` int(11) NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `link` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `urut` int(11) NOT NULL,
  `warna` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_menu_tr`
--

INSERT INTO `m_menu_tr` (`id_menu`, `id_parent`, `nama_menu`, `link`, `img`, `urut`, `warna`) VALUES
(1, 0, 'DASHBOARD', 'dashboard.php', 'fa fa-dashboard', 1, '#f0ad4e'),
(2, 0, 'DATA ORDER', 'jo.php', 'fa fa-pencil-square-o', 2, '#1087dd'),
(3, 0, 'DATA DELIVERY', 'deliv.php', 'fa fa-truck', 3, '#2c4762'),
(5, 0, 'FINANCE', '	', 'fa fa-building-o', 5, '#dd4b39'),
(6, 0, 'MASTER DATA', '', 'fa fa-gear', 6, '#587ea3'),
(20, 6, 'Data Kota', 'kota.php', '', 6, ''),
(19, 6, 'Data Jenis Mobil', 'jenis.php', '', 5, ''),
(16, 6, 'Data User', 'user.php', '', 9, ''),
(15, 6, 'Data Bank', 'bank.php', '', 7, ''),
(14, 6, 'Data COA', 'coa.php', '', 8, ''),
(13, 6, 'Data Biaya', 'cost.php', '', 6, ''),
(12, 6, 'Data Pengurus', 'pengurus.php', '', 4, ''),
(11, 6, 'Data Mobil', 'mobil.php', '', 5, ''),
(10, 6, 'Data Supir', 'supir.php', '', 3, ''),
(9, 6, 'Data Vendor', 'vendor.php', '', 2, ''),
(8, 6, 'Data Customer', 'cust.php', '', 1, ''),
(4, 0, 'DATA SPAREPART', 'spare.php', 'fa  fa-reorder', 3, '#3b5998'),
(21, 6, 'Data Harga', 'rate.php', '', 6, ''),
(22, 2, 'Ekspedisi', 'lcl.php', '', 2, ''),
(7, 2, 'FCL (Full Container)', 'fcl.php', '', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `m_mobil_tr`
--

CREATE TABLE `m_mobil_tr` (
  `id_mobil` int(11) NOT NULL,
  `no_polisi` varchar(50) NOT NULL,
  `merk` varchar(50) NOT NULL,
  `tahun_buat` varchar(4) NOT NULL,
  `tahun_rakit` varchar(4) NOT NULL,
  `silinder` double NOT NULL,
  `warna_truck` varchar(25) NOT NULL,
  `no_rangka` varchar(25) NOT NULL,
  `no_mesin` varchar(25) NOT NULL,
  `no_bpkb` varchar(25) NOT NULL,
  `no_kabin` varchar(25) NOT NULL,
  `iden` varchar(25) NOT NULL,
  `warna_tnkb` varchar(25) NOT NULL,
  `bbm` varchar(10) NOT NULL,
  `berat_max` varchar(15) NOT NULL,
  `no_reg` varchar(25) NOT NULL,
  `status` int(11) NOT NULL,
  `created` varchar(25) NOT NULL,
  `tanggal` date NOT NULL,
  `tgl_stnk` date NOT NULL,
  `tgl_kir` date NOT NULL,
  `photo` varchar(255) NOT NULL,
  `bpkp` varchar(255) NOT NULL,
  `stnk` varchar(255) NOT NULL,
  `id_card` varchar(255) NOT NULL,
  `telp` varchar(50) NOT NULL,
  `kir` varchar(255) NOT NULL,
  `tgl_card` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_mobil_tr`
--

INSERT INTO `m_mobil_tr` (`id_mobil`, `no_polisi`, `merk`, `tahun_buat`, `tahun_rakit`, `silinder`, `warna_truck`, `no_rangka`, `no_mesin`, `no_bpkb`, `no_kabin`, `iden`, `warna_tnkb`, `bbm`, `berat_max`, `no_reg`, `status`, `created`, `tanggal`, `tgl_stnk`, `tgl_kir`, `photo`, `bpkp`, `stnk`, `id_card`, `telp`, `kir`, `tgl_card`) VALUES
(1, 'B3543', 'mitsubishi', '1', '2', 3, 'HITAM', '5', '6', '7', '', '8', '9', 'Gas', '13213', '2313', 1, 'admin', '2024-07-29', '2024-07-29', '2024-07-29', '', 'mobil/730675879107b 9978 uwv.jpeg', 'mobil/890929557437b 9973 uwv.jpeg', '', '', 'mobil/743225246279whatsapp image 2021-06-23 at 9.07.04 am.jpeg', '0000-00-00'),
(2, 'B 65234 AS', 'toyota', '2024', '', 0, 'KUNING', '0832764532', '', '', '', '', '', '', '', '', 1, 'admin', '2024-07-29', '2024-12-25', '2024-03-14', 'mobil/276485130718whatsapp image 2021-06-09 at 11.08.37 am.jpg', 'mobil/59365944354untitled.jpg', 'mobil/194912361815whatsapp image 2021-06-23 at 9.16.26 am.jpeg', '', '', '', '0000-00-00'),
(3, 'B 6634 TRE', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', 1, 'admin', '2024-08-08', '2024-08-08', '2024-08-08', '', '', '', '', '', '', '0000-00-00'),
(4, 'DDD', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', 1, 'admin', '2024-09-30', '2024-09-30', '2024-09-30', '', '', '', '', '', '', '0000-00-00'),
(5, 'AAA', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', 1, 'admin', '2024-09-30', '2024-09-30', '2024-09-30', 'mobil/49458276485130718whatsapp image 2021-06-09 at 11.08.37 am.jpg', 'mobil/385358890929557437b 9973 uwv.jpeg', '', '', '', '', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `m_opera`
--

CREATE TABLE `m_opera` (
  `id_opera` int(11) NOT NULL,
  `nama` varchar(175) NOT NULL,
  `plafond` double NOT NULL,
  `status` int(11) NOT NULL,
  `created` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `m_opera`
--

INSERT INTO `m_opera` (`id_opera`, `nama`, `plafond`, `status`, `created`) VALUES
(9, 'AMIR', 0, 1, 'admin'),
(10, 'JOKO', 0, 1, 'admin'),
(11, 'BUDI', 0, 1, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `m_paging`
--

CREATE TABLE `m_paging` (
  `id` int(11) NOT NULL,
  `baris` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_paging`
--

INSERT INTO `m_paging` (`id`, `baris`) VALUES
(2, 10),
(3, 25),
(4, 50),
(5, 100),
(6, 300),
(7, 500),
(8, 1500),
(9, 5000),
(10, 10000);

-- --------------------------------------------------------

--
-- Table structure for table `m_paket`
--

CREATE TABLE `m_paket` (
  `id` int(11) NOT NULL,
  `nama_paket` varchar(25) NOT NULL,
  `id_user` varchar(25) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_paket`
--

INSERT INTO `m_paket` (`id`, `nama_paket`, `id_user`, `status`) VALUES
(1, 'Package', 'admin', 1),
(2, 'Pallet', 'admin', 1),
(3, 'Carton', 'admin', 1),
(4, 'Roll', 'admin', 1),
(5, 'Drum', 'admin', 1),
(6, 'Case', 'admin', 1),
(7, 'Unit', 'admin', 1),
(8, 'Crate', 'admin', 1),
(9, 'Box', 'admin', 1),
(10, 'Bag', 'admin', 1),
(11, 'Bale', 'admin', 1),
(12, 'Bundle', 'admin', 1),
(13, 'Coil', 'admin', 1),
(14, 'Piece', 'admin', 1),
(15, 'Colly', 'admin', 1),
(16, 'Liftvan', 'admin', 1),
(17, 'Bins', 'admin', 1),
(18, 'Set', 'admin', 1),
(20, 'Wodden Package', 'admin', 1),
(21, 'Isotank', 'admin', 1),
(22, 'Crates', 'admin', 1),
(23, 'Container', 'admin', 1),
(24, 'Carton Box', 'admin', 1),
(25, 'Carton Boxes', 'admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `m_port`
--

CREATE TABLE `m_port` (
  `id_port` int(11) NOT NULL,
  `nama_port` varchar(175) NOT NULL,
  `caption` varchar(5) NOT NULL,
  `status` int(11) NOT NULL,
  `created` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `m_port`
--

INSERT INTO `m_port` (`id_port`, `nama_port`, `caption`, `status`, `created`) VALUES
(337, 'JAKARTA, INDONESIA', 'JKT', 1, 'Admin'),
(338, 'HO CHI MINH, VIETNAM', 'HCM', 1, 'Admin'),
(339, 'SHANGHAI, CHINA', '', 1, 'Admin'),
(340, 'SHENZHEN, CHINA', '', 1, 'Admin'),
(341, 'QINGDAO, CHINA', '', 1, 'Admin'),
(342, 'TIANJIN, CHINA', '', 1, 'Admin'),
(343, 'SEMARANG SEAPORT, INDONESIA', '', 1, 'Admin'),
(344, 'SURABAYA, INDONESIA', '', 1, 'Admin'),
(345, 'NINGBO, CHINA', '', 1, 'Admin'),
(346, 'BUSAN, KOREA', '', 1, 'Admin'),
(347, 'SINGAPORE, SINGAPORE', '', 1, 'Admin'),
(348, 'PORT KLANG, MALAYSIA', 'PKL', 1, 'Admin'),
(349, 'FUZHOU, CHINA', '', 1, 'Admin'),
(350, 'KEELUNG, TAIWAN', 'KEE', 1, 'Admin'),
(351, 'ASUNCION, PARAGUAY', '', 1, 'Admin'),
(352, 'SAN JUAN, PUERTO RICO', '', 1, 'Admin'),
(353, 'MELBOURNE, AUSTRALIA', '', 1, 'Admin'),
(354, 'XIAMEN, CHINA', '', 1, 'Admin'),
(355, 'GENOA, ITALY', '', 1, 'Admin'),
(356, 'BARCELONA, SPAIN', 'BCN', 1, 'Admin'),
(357, 'NANSHA, CHINA', '', 1, 'Admin'),
(358, 'SAVANNAH, GA, USA', '', 1, 'Admin'),
(359, 'BELAWAN, INDONESIA', 'BLW', 1, 'Admin'),
(360, 'BUENAVENTURA, COLOMBIA', 'BUE', 1, 'Admin'),
(361, 'SAIGON, VIETNAM', 'SGN', 1, 'Admin'),
(362, 'KAOHSIUNG, TAIWAN', 'KAO', 1, 'Admin'),
(363, 'HONG KONG, HONG KONG', 'HKG', 1, 'Admin'),
(364, 'MONTEVIDEO, URUGUAY', '', 1, 'Admin'),
(365, 'TAICHUNG, TAIWAN', 'TCH', 1, 'Admin'),
(366, 'TAIPEI, TAIWAN', 'TPE', 1, 'Admin'),
(367, 'VARIOUS PORT', '', 1, 'Admin'),
(368, 'BANGKOK, THAILAND', 'BKK', 1, 'Admin'),
(369, 'SHEKOU, CHINA', '', 1, 'Admin'),
(370, 'LAEM CHABANG, THAILAND', 'LCH', 1, 'Admin'),
(371, 'LELIU, CHINA', '', 1, 'Admin'),
(372, 'SANSHUI, CHINA', '', 1, 'Admin'),
(373, 'ZHONGSHAN, CHINA', '', 1, 'Admin'),
(374, 'HUANGPU, CHINA', '', 1, 'Admin'),
(375, 'XIAOLAN, CHINA', '', 1, 'Admin'),
(376, 'MILAN, ITALY', '', 1, 'Admin'),
(377, 'CHATTOGRAM, BANGLADESH', '', 1, 'Admin'),
(378, 'PYONGTAEK, KOREA', '', 1, 'Admin'),
(379, 'DALIAN, CHINA', 'DLC', 1, 'Admin'),
(380, 'VALENCIA, SPAIN', '', 1, 'Admin'),
(381, 'KOBE, JAPAN', '', 1, 'Admin'),
(382, 'ROTTERDAM, NETHERLAND', 'RTM', 1, 'Admin'),
(383, 'GUAYAQUIL, ECUADOR', '', 1, 'Admin'),
(384, 'MANILA, PHILIPHINES', 'MNL', 1, 'Admin'),
(385, 'LJUBLJANA, SLOVENIA', 'LJU', 1, 'Admin'),
(386, 'TRIESTE, ITALY', '', 1, 'Admin'),
(387, 'AHMEDABAD, INDIA', '', 1, 'Admin'),
(388, 'XINGANG, CHINA', '', 1, 'Admin'),
(389, 'GUANGZHOU, CHINA', 'CAN', 1, 'Admin'),
(390, 'PUERTO QUETZAL, GUATEMALA', '', 1, 'Admin'),
(391, 'MUNICH, GERMANY', '', 1, 'Admin'),
(392, 'SOKHNA, EGYPT', '', 1, 'Admin'),
(393, 'COPENHAGEN, DENMARK', '', 1, 'Admin'),
(394, 'HUMEN, CHINA', '', 1, 'Admin'),
(395, 'ANTWERP, BELGIUM', '', 1, 'Admin'),
(396, 'HAIPONG, VIETNAM', 'HPH', 1, 'Admin'),
(397, 'FOS SUR MER, FRANCE', 'FOS', 1, 'Admin'),
(398, 'KWANGYANG, KOREA', '', 1, 'Admin'),
(399, 'VENICE, ITALY', 'VCE', 1, 'Admin'),
(400, 'ZURICH, SWITZERLAND', '', 1, 'Admin'),
(401, 'RIGA, LATVIA', 'RIX', 1, 'Admin'),
(402, 'CHENNAI, INDIA', 'MAA', 1, 'Admin'),
(403, 'GOTHENBURG, SWEDEN', '', 1, 'Admin'),
(404, 'MADRID, SPAIN', 'MAD', 1, 'Admin'),
(405, 'BEIJING, CHINA', '', 1, 'Admin'),
(406, 'CAT LAI PORT, VIETNAM', 'CAT', 1, 'Admin'),
(407, 'SYDNEY, AUSTRALIA', 'SYD', 1, 'Admin'),
(408, 'VANCOUVER, CANADA', '', 1, 'Admin'),
(409, 'WARSAWA, POLAND', '', 1, 'Admin'),
(410, 'COLON, PANAMA', '', 1, 'Admin'),
(411, 'TOKYO, JAPAN', '', 1, 'Admin'),
(412, 'LIANYUNGANG, CHINA', '', 1, 'Admin'),
(413, 'ACAJUTLA. EL SALVADOR', '', 1, 'Admin'),
(414, 'BEIJIAO, CHINA', '', 1, 'Admin'),
(415, 'WUHAN, CHINA', 'WUH', 1, 'Admin'),
(416, 'PORT LOUIS, MAURITIUS', 'PLU', 1, 'Admin'),
(417, 'BANDAR ABBAS, IRAN', '', 1, 'Admin'),
(418, 'HO CHI MINH, CAT LAI PORT', '', 1, 'Admin'),
(419, 'SUVA, FIJI ISLANDS', '', 1, 'Admin'),
(420, 'AUCKLAND, NEW ZEALAND', '', 1, 'Admin'),
(421, 'DACHAN BAY, CHINA', '', 1, 'Admin'),
(422, 'SALVADOR, BRAZIL', '', 1, 'Admin'),
(423, 'STOCKHOLM-ARLANDA, SWEDEN', 'ARN', 1, 'Admin'),
(424, 'CHONGQING, CHINA', '', 1, 'Admin'),
(425, 'ZHAPU, CHINA', '', 1, 'Admin'),
(426, 'ZHUHAI, CHINA', '', 1, 'Admin'),
(427, 'GDANSK, POLAND', '', 1, 'Admin'),
(428, 'GDYNIA, POLAND', '', 1, 'Admin'),
(429, 'MAFANG, CHINA', '', 1, 'Admin'),
(430, 'ANHUI, CHINA', '', 1, 'Admin'),
(431, 'LE HAVRE, FRANCE', '', 1, 'Admin'),
(432, 'PANJANG, INDONESIA', '', 1, 'Admin'),
(433, 'KHORRAMSHAHR PORT, IRAN', '', 1, 'Admin'),
(434, 'TAURANGA, NEW ZEALAND', 'TNZ', 1, 'Admin'),
(435, 'MUMBAI, INDIA', '', 1, 'Admin'),
(436, 'CHITTAGONG, BANGLADESH', '', 1, 'Admin'),
(437, 'HONG KONG', '', 1, 'Admin'),
(438, 'SVAY RIENG, CAMBODIA.', '', 1, 'Admin'),
(439, 'SIHANOUKVILLE, CAMBODIA', '', 1, 'Admin'),
(440, 'YANGON, MYANMAR', '', 1, 'Admin'),
(441, 'SHUNDE NEW PORT, CHINA', '', 1, 'Admin'),
(442, 'NHAVA SHEVA, INDIA', '', 1, 'Admin'),
(443, 'COLON CONTAINER TERMINAL, *', '', 1, 'Admin'),
(444, 'SAN PEDRO SULA, HONDURAS', '', 1, 'Admin'),
(445, 'SOEKARNO HATTA', 'ST', 1, 'Admin'),
(446, 'HO CHI MINH( CAT LAI ) ,VIET NAM', '', 1, 'Admin'),
(447, 'FRANKFURT, GERMANY', '', 1, 'Admin'),
(448, 'COLON FREE ZONE, PANAMA', '', 1, 'Admin'),
(449, 'LATKRABANG, THAILAND', '', 1, 'Admin'),
(450, 'PORTLAND, USA', '', 1, 'Admin'),
(451, 'JIANGMEN NEW PORT, CHINA', '', 1, 'Admin'),
(452, 'BRISBANE, AUSTRALIA', '', 1, 'Admin'),
(453, 'FOSHAN, CHINA', 'FOS', 1, 'Admin'),
(454, 'PUERTO CALDERA, COSTA RICA', '', 1, 'Admin'),
(455, 'LISBON, PORTUGAL', '', 1, 'Admin'),
(456, 'CARTAGENA SEAPORT, COLOMBIA', '', 1, 'Admin'),
(457, 'CHICAGO, USA', 'ORD', 1, 'Admin'),
(458, 'NEW DELHI, INDIA', '', 1, 'Admin'),
(459, 'HAMBURG, GERMANY', '', 1, 'Admin'),
(460, 'BATAM, INDONESIA', '', 1, 'Admin'),
(461, 'LOS ANGELES, USA', '', 1, 'Admin'),
(462, 'SEMARANG, INDONESIA', '', 1, 'Admin'),
(463, 'GAOSHA, CHINA', '', 1, 'Admin'),
(464, 'AMSTERDAM, NETHERLAND', 'AMS', 1, 'Admin'),
(465, 'FELIXTOWE , UK', '', 1, 'Admin'),
(466, 'ISTANBUL, TURKEY', '', 1, 'Admin'),
(467, 'KUALA LUMPUR, MALAYSIA', '', 1, 'Admin'),
(468, 'OSAKA, JAPAN', '', 1, 'Admin'),
(469, 'LONDON, UK', 'LHR', 1, 'Admin'),
(470, 'JEBEL ALI, U.A.E.', '', 1, 'Admin'),
(471, 'TANJUNG PRIOK, INDONESIA', '', 1, 'Admin'),
(472, 'DILI, TIMOR LESTE', '', 1, 'Admin'),
(473, 'INCHEON, SOUTH KOREA', '', 1, 'Admin'),
(474, 'BANJARMASIN, INDONESIA', '', 1, 'Admin'),
(475, 'PUERTO SEGURO FLUVIAL', '', 1, 'Admin'),
(476, 'FUZHOU PORT,CHINA', 'FZ', 1, 'Admin'),
(477, 'MEMPHIS, USA', '', 1, 'Admin'),
(478, 'ATLANTA, USA', '', 1, 'Admin'),
(479, 'SOUTHAMPTON, UK', '', 1, 'Admin'),
(480, 'KLAIPEDA, LITHUANIA', '', 1, 'Admin'),
(493, 'TBA', 'TBA', 1, 'admin'),
(494, 'MOSCOW, RUSSIA', '', 1, 'admin'),
(495, 'PEKANBARU, INDONESIA', '', 1, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `m_pt`
--

CREATE TABLE `m_pt` (
  `id_pt` int(11) NOT NULL,
  `nama_pt` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `telp` varchar(100) NOT NULL,
  `fax` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `web` varchar(255) NOT NULL,
  `caption` varchar(10) NOT NULL,
  `direktur` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `npwp` varchar(30) NOT NULL,
  `alamat_npwp` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_pt`
--

INSERT INTO `m_pt` (`id_pt`, `nama_pt`, `alamat`, `telp`, `fax`, `email`, `web`, `caption`, `direktur`, `logo`, `status`, `npwp`, `alamat_npwp`) VALUES
(1, 'PT. PLANET TRANS LOGISTICS', 'Pakuwon Center - Superblok Tunjungan City Lantai 19-05 A', '+6231-99247381', '', '', '', 'PTL', '-', 'img_pt/logo1.jpg', 1, '-', '');

-- --------------------------------------------------------

--
-- Table structure for table `m_rate_tr`
--

CREATE TABLE `m_rate_tr` (
  `id_rate` int(11) NOT NULL,
  `id_asal` int(11) NOT NULL,
  `id_tujuan` int(11) NOT NULL,
  `jenis_mobil` varchar(25) NOT NULL,
  `rate` double NOT NULL,
  `uj` double NOT NULL,
  `komisi` double NOT NULL,
  `status` int(11) NOT NULL,
  `created` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `m_rate_tr`
--

INSERT INTO `m_rate_tr` (`id_rate`, `id_asal`, `id_tujuan`, `jenis_mobil`, `rate`, `uj`, `komisi`, `status`, `created`) VALUES
(0, 4, 4, 'CDD', 22, 60, 70, 1, 'admin'),
(1, 3, 4, '1 x 20', 1000, 50000, 10000, 1, 'admin'),
(2, 3, 2, '1 x 20', 20000, 75000, 15000, 1, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `m_remark`
--

CREATE TABLE `m_remark` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `m_remark`
--

INSERT INTO `m_remark` (`id`, `nama`) VALUES
(2, 'Shipping is not insured unless specifically requested by the customer. '),
(3, 'Importers or exporters are required to prepare NPWP/NIB and other supporting documents to complete the order.'),
(4, 'Term of Payment and penalty for late charges will be implemented according to the general Term and condition. '),
(5, 'Every Cancellation shipment is required to pay a Cancellation Fee if any. '),
(6, 'Payment is made 14 days from the invoice received by the customer. '),
(7, 'All prices above are valid according to the available valid date and are not binding if it has passed. ');

-- --------------------------------------------------------

--
-- Table structure for table `m_role`
--

CREATE TABLE `m_role` (
  `id_role` int(11) NOT NULL,
  `nama_role` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_role`
--

INSERT INTO `m_role` (`id_role`, `nama_role`, `status`) VALUES
(2, 'Marketing', 1),
(3, 'Finance', 1),
(1, 'Administrator', 1),
(6, 'CS', 1);

-- --------------------------------------------------------

--
-- Table structure for table `m_role_akses`
--

CREATE TABLE `m_role_akses` (
  `id_role` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `m_view` int(11) NOT NULL,
  `m_add` int(11) NOT NULL,
  `m_edit` int(11) NOT NULL,
  `m_del` int(11) NOT NULL,
  `m_exe` int(11) NOT NULL,
  `m_app` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_role_akses`
--

INSERT INTO `m_role_akses` (`id_role`, `id_menu`, `m_view`, `m_add`, `m_edit`, `m_del`, `m_exe`, `m_app`) VALUES
(1, 7, 1, 1, 1, 1, 0, 0),
(1, 15, 1, 1, 1, 1, 0, 0),
(1, 10, 0, 1, 1, 1, 0, 0),
(1, 14, 1, 1, 1, 1, 0, 0),
(1, 24, 1, 1, 1, 1, 0, 0),
(1, 4, 1, 1, 1, 1, 1, 0),
(1, 25, 1, 1, 1, 1, 0, 0),
(1, 18, 1, 1, 1, 1, 0, 0),
(1, 51, 1, 1, 1, 1, 0, 0),
(1, 16, 1, 1, 1, 1, 0, 0),
(1, 6, 1, 1, 1, 1, 0, 0),
(1, 3, 1, 1, 1, 1, 1, 0),
(1, 13, 1, 1, 1, 1, 1, 0),
(1, 17, 1, 1, 1, 1, 1, 0),
(3, 39, 0, 0, 0, 0, 0, 0),
(3, 27, 1, 1, 1, 1, 1, 0),
(3, 9, 1, 1, 1, 1, 1, 0),
(3, 15, 1, 1, 1, 1, 1, 0),
(3, 5, 0, 1, 1, 1, 1, 0),
(3, 21, 1, 1, 1, 1, 1, 0),
(3, 1, 1, 0, 0, 0, 0, 0),
(3, 17, 1, 1, 1, 1, 0, 0),
(3, 34, 1, 1, 1, 1, 1, 0),
(3, 22, 1, 1, 1, 1, 1, 0),
(3, 16, 1, 1, 1, 1, 1, 0),
(3, 40, 0, 0, 0, 0, 0, 0),
(3, 19, 1, 1, 1, 1, 1, 0),
(3, 18, 1, 1, 1, 1, 1, 0),
(4, 13, 1, 1, 1, 1, 1, 0),
(4, 21, 0, 0, 0, 0, 0, 0),
(4, 7, 1, 1, 1, 1, 1, 0),
(4, 43, 0, 0, 0, 0, 0, 0),
(4, 42, 0, 0, 0, 0, 0, 0),
(4, 24, 0, 0, 0, 0, 0, 0),
(4, 0, 0, 0, 0, 0, 0, 0),
(4, 1, 0, 0, 0, 0, 0, 0),
(4, 33, 0, 0, 0, 0, 0, 0),
(4, 41, 0, 0, 0, 0, 0, 0),
(4, 19, 0, 0, 0, 0, 0, 0),
(4, 23, 0, 0, 0, 0, 0, 0),
(4, 11, 0, 0, 0, 0, 0, 0),
(4, 18, 0, 0, 0, 0, 0, 0),
(4, 17, 0, 0, 0, 0, 0, 0),
(1, 33, 1, 1, 1, 1, 0, 0),
(1, 11, 0, 1, 1, 1, 0, 0),
(1, 39, 1, 1, 1, 1, 0, 0),
(2, 34, 0, 0, 0, 0, 0, 0),
(2, 24, 0, 0, 0, 0, 0, 0),
(2, 27, 0, 0, 0, 0, 0, 0),
(2, 50, 0, 0, 0, 0, 0, 0),
(2, 16, 0, 0, 0, 0, 0, 0),
(2, 19, 0, 0, 0, 0, 0, 0),
(2, 20, 0, 0, 0, 0, 0, 0),
(2, 2, 1, 1, 1, 1, 1, 0),
(2, 21, 0, 0, 0, 0, 0, 0),
(2, 8, 1, 1, 1, 1, 1, 0),
(2, 14, 0, 0, 0, 0, 0, 0),
(2, 36, 0, 0, 0, 0, 0, 0),
(2, 18, 0, 0, 0, 0, 0, 0),
(2, 22, 0, 0, 0, 0, 0, 0),
(1, 21, 1, 1, 1, 1, 0, 0),
(2, 23, 0, 0, 0, 0, 0, 0),
(5, 7, 0, 0, 0, 0, 0, 0),
(5, 3, 0, 0, 0, 0, 0, 0),
(5, 6, 0, 0, 0, 0, 0, 0),
(5, 10, 0, 0, 0, 0, 0, 0),
(5, 4, 1, 1, 0, 0, 0, 0),
(5, 8, 0, 0, 0, 0, 0, 0),
(5, 26, 0, 0, 0, 0, 0, 0),
(5, 9, 0, 0, 0, 0, 0, 0),
(5, 11, 0, 0, 0, 0, 0, 0),
(5, 20, 0, 0, 0, 0, 0, 0),
(5, 2, 0, 0, 0, 0, 0, 0),
(5, 0, 0, 0, 0, 0, 0, 0),
(5, 12, 0, 0, 0, 0, 0, 0),
(5, 13, 0, 0, 0, 0, 0, 0),
(5, 14, 0, 0, 0, 0, 0, 0),
(5, 15, 1, 1, 1, 1, 1, 0),
(5, 16, 0, 0, 0, 0, 0, 0),
(5, 30, 0, 0, 0, 0, 0, 0),
(5, 28, 0, 0, 0, 0, 0, 0),
(5, 5, 0, 0, 0, 0, 0, 0),
(5, 18, 0, 0, 0, 0, 0, 0),
(5, 17, 0, 0, 0, 0, 0, 0),
(5, 29, 0, 0, 0, 0, 0, 0),
(5, 21, 0, 0, 0, 0, 0, 0),
(5, 19, 0, 0, 0, 0, 0, 0),
(5, 24, 0, 0, 0, 0, 0, 0),
(5, 1, 0, 0, 0, 0, 0, 0),
(5, 27, 0, 0, 0, 0, 0, 0),
(5, 25, 0, 0, 0, 0, 0, 0),
(5, 23, 0, 0, 0, 0, 0, 0),
(6, 51, 1, 1, 1, 1, 1, 0),
(6, 27, 0, 0, 0, 0, 0, 0),
(6, 24, 0, 0, 0, 0, 0, 0),
(6, 13, 1, 1, 1, 1, 1, 0),
(6, 9, 1, 1, 1, 1, 1, 0),
(6, 11, 0, 0, 0, 0, 0, 0),
(6, 10, 0, 0, 0, 0, 0, 0),
(6, 33, 0, 0, 0, 0, 0, 0),
(6, 32, 0, 0, 0, 0, 0, 0),
(6, 40, 0, 0, 0, 0, 0, 0),
(6, 23, 0, 0, 0, 0, 0, 0),
(6, 22, 0, 0, 0, 0, 0, 0),
(6, 21, 0, 0, 0, 0, 0, 0),
(6, 20, 0, 0, 0, 0, 0, 0),
(6, 19, 0, 0, 0, 0, 0, 0),
(6, 18, 0, 0, 0, 0, 0, 0),
(6, 6, 1, 1, 1, 1, 1, 0),
(6, 34, 0, 0, 0, 0, 0, 0),
(6, 17, 0, 0, 0, 0, 0, 0),
(6, 4, 1, 1, 1, 1, 1, 0),
(6, 45, 0, 0, 0, 0, 0, 0),
(6, 14, 1, 1, 1, 1, 1, 0),
(6, 8, 1, 1, 1, 1, 1, 0),
(6, 36, 1, 1, 1, 1, 1, 0),
(6, 7, 1, 1, 1, 1, 1, 0),
(6, 50, 0, 0, 0, 0, 0, 0),
(6, 16, 0, 0, 0, 0, 0, 0),
(6, 15, 0, 0, 0, 0, 0, 0),
(6, 25, 1, 1, 1, 1, 1, 0),
(4, 39, 1, 1, 1, 1, 1, 0),
(4, 20, 0, 0, 0, 0, 0, 0),
(4, 44, 1, 1, 1, 1, 1, 0),
(4, 36, 1, 1, 1, 1, 1, 0),
(3, 20, 1, 1, 1, 1, 1, 0),
(3, 3, 1, 1, 1, 1, 1, 0),
(3, 12, 0, 0, 0, 0, 0, 0),
(1, 27, 1, 1, 1, 1, 0, 0),
(1, 40, 1, 1, 1, 1, 0, 0),
(0, 8, 1, 1, 1, 1, 1, 0),
(0, 9, 1, 1, 1, 1, 1, 0),
(1, 34, 1, 1, 1, 1, 0, 0),
(0, 19, 0, 1, 1, 1, 1, 0),
(0, 20, 0, 1, 1, 1, 1, 0),
(0, 24, 0, 1, 1, 1, 1, 0),
(0, 17, 0, 1, 1, 1, 1, 0),
(0, 21, 0, 1, 1, 1, 1, 0),
(0, 22, 0, 1, 1, 1, 1, 0),
(0, 7, 1, 1, 1, 1, 1, 0),
(0, 2, 1, 1, 1, 1, 1, 0),
(0, 39, 0, 0, 0, 0, 0, 0),
(0, 27, 0, 0, 0, 0, 0, 0),
(0, 32, 0, 0, 0, 0, 0, 0),
(0, 14, 0, 1, 1, 1, 1, 0),
(0, 40, 0, 0, 0, 0, 0, 0),
(0, 34, 0, 0, 0, 0, 0, 0),
(0, 43, 0, 0, 0, 0, 0, 0),
(0, 18, 0, 1, 1, 1, 1, 0),
(0, 36, 0, 0, 0, 0, 0, 0),
(0, 12, 0, 1, 1, 1, 1, 0),
(0, 33, 0, 0, 0, 0, 0, 0),
(0, 5, 0, 1, 1, 1, 1, 0),
(0, 23, 0, 1, 1, 1, 1, 0),
(0, 1, 0, 1, 1, 1, 1, 0),
(0, 15, 0, 1, 1, 1, 1, 0),
(0, 16, 0, 1, 1, 1, 1, 0),
(1, 9, 1, 1, 1, 1, 0, 0),
(1, 19, 1, 1, 1, 1, 0, 0),
(3, 4, 0, 0, 0, 0, 0, 0),
(3, 33, 1, 1, 1, 1, 1, 0),
(3, 25, 1, 1, 1, 1, 1, 0),
(3, 6, 1, 1, 1, 1, 1, 0),
(3, 36, 0, 0, 0, 0, 0, 0),
(3, 14, 0, 0, 0, 0, 0, 0),
(3, 23, 1, 1, 1, 1, 1, 0),
(3, 8, 1, 1, 1, 1, 1, 0),
(3, 13, 0, 0, 0, 0, 0, 0),
(1, 20, 1, 1, 1, 1, 0, 0),
(1, 23, 1, 1, 1, 1, 0, 0),
(4, 40, 1, 1, 1, 1, 1, 0),
(4, 45, 1, 1, 1, 1, 1, 0),
(4, 34, 0, 0, 0, 0, 0, 0),
(4, 32, 0, 0, 0, 0, 0, 0),
(4, 2, 0, 0, 0, 0, 0, 0),
(4, 8, 0, 0, 0, 0, 0, 0),
(4, 10, 0, 0, 0, 0, 0, 0),
(4, 22, 0, 0, 0, 0, 0, 0),
(4, 15, 0, 0, 0, 0, 0, 0),
(4, 16, 0, 0, 0, 0, 0, 0),
(4, 6, 1, 0, 0, 0, 0, 0),
(4, 9, 1, 1, 1, 1, 1, 0),
(3, 2, 1, 0, 0, 0, 0, 0),
(1, 50, 0, 1, 1, 1, 0, 0),
(2, 25, 1, 1, 1, 1, 1, 0),
(2, 7, 1, 1, 1, 1, 1, 0),
(2, 3, 0, 1, 1, 1, 1, 0),
(2, 5, 0, 0, 0, 0, 0, 0),
(2, 6, 0, 0, 0, 0, 0, 0),
(2, 10, 0, 0, 0, 0, 0, 0),
(2, 11, 0, 0, 0, 0, 0, 0),
(2, 12, 0, 0, 0, 0, 0, 0),
(2, 17, 0, 0, 0, 0, 0, 0),
(2, 45, 0, 0, 0, 0, 0, 0),
(2, 4, 0, 0, 0, 0, 0, 0),
(2, 33, 0, 0, 0, 0, 0, 0),
(2, 40, 0, 0, 0, 0, 0, 0),
(2, 15, 0, 0, 0, 0, 0, 0),
(3, 7, 1, 1, 1, 1, 1, 0),
(3, 11, 0, 1, 1, 1, 1, 0),
(0, 44, 0, 0, 0, 0, 0, 0),
(0, 6, 1, 1, 1, 1, 1, 0),
(0, 3, 0, 0, 0, 0, 0, 0),
(0, 4, 0, 1, 1, 1, 1, 0),
(0, 13, 0, 1, 1, 1, 1, 0),
(0, 0, 0, 0, 0, 0, 0, 0),
(0, 10, 0, 1, 1, 1, 1, 0),
(0, 11, 0, 1, 1, 1, 1, 0),
(1, 32, 1, 1, 1, 1, 0, 0),
(2, 39, 0, 0, 0, 0, 0, 0),
(6, 0, 0, 0, 0, 0, 0, 0),
(2, 13, 0, 0, 0, 0, 0, 0),
(2, 9, 1, 1, 1, 1, 1, 0),
(4, 5, 0, 0, 0, 0, 0, 0),
(4, 27, 0, 0, 0, 0, 0, 0),
(4, 4, 1, 0, 0, 0, 0, 0),
(4, 3, 0, 0, 0, 0, 0, 0),
(4, 14, 1, 1, 1, 1, 1, 0),
(1, 5, 0, 1, 1, 1, 0, 0),
(2, 32, 0, 0, 0, 0, 0, 0),
(3, 32, 1, 1, 1, 1, 1, 0),
(3, 0, 0, 0, 0, 0, 0, 0),
(3, 10, 0, 1, 1, 1, 1, 0),
(3, 24, 1, 1, 1, 1, 1, 0),
(1, 36, 1, 1, 1, 1, 0, 0),
(6, 2, 1, 0, 0, 0, 0, 0),
(6, 3, 1, 1, 1, 1, 1, 0),
(6, 5, 0, 0, 0, 0, 0, 0),
(6, 39, 0, 0, 0, 0, 0, 0),
(6, 12, 0, 0, 0, 0, 0, 0),
(1, 22, 1, 1, 1, 1, 0, 0),
(4, 12, 0, 0, 0, 0, 0, 0),
(4, 25, 0, 0, 0, 0, 0, 0),
(1, 8, 1, 1, 1, 1, 0, 0),
(1, 12, 1, 1, 1, 1, 0, 0),
(1, 45, 0, 1, 1, 1, 0, 0),
(7, 0, 0, 0, 0, 0, 0, 0),
(7, 1, 1, 0, 0, 0, 0, 0),
(7, 15, 1, 1, 1, 1, 1, 0),
(7, 34, 1, 1, 1, 1, 1, 0),
(7, 43, 1, 1, 1, 1, 1, 0),
(7, 39, 0, 0, 0, 0, 0, 0),
(7, 40, 0, 0, 0, 0, 0, 0),
(7, 32, 1, 1, 1, 1, 1, 0),
(7, 33, 1, 1, 1, 1, 1, 0),
(7, 11, 1, 1, 1, 1, 1, 0),
(7, 44, 1, 1, 1, 1, 1, 0),
(7, 5, 1, 0, 0, 0, 0, 0),
(7, 16, 1, 1, 1, 1, 1, 0),
(7, 17, 1, 1, 1, 1, 1, 0),
(7, 18, 1, 1, 1, 1, 1, 0),
(7, 19, 1, 1, 1, 1, 1, 0),
(7, 20, 1, 1, 1, 1, 1, 0),
(7, 21, 1, 1, 1, 1, 1, 0),
(7, 22, 1, 1, 1, 1, 1, 0),
(7, 23, 1, 1, 1, 1, 1, 0),
(7, 24, 1, 1, 1, 1, 1, 0),
(7, 10, 1, 1, 1, 1, 1, 0),
(7, 4, 0, 0, 0, 0, 0, 0),
(7, 13, 0, 0, 0, 0, 0, 0),
(7, 14, 0, 0, 0, 0, 0, 0),
(7, 12, 0, 0, 0, 0, 0, 0),
(7, 25, 1, 1, 1, 1, 1, 0),
(7, 6, 1, 0, 0, 0, 0, 0),
(7, 7, 1, 1, 1, 1, 1, 0),
(7, 8, 1, 1, 1, 1, 1, 0),
(7, 9, 1, 1, 1, 1, 1, 0),
(7, 2, 1, 0, 0, 0, 0, 0),
(7, 3, 1, 1, 1, 1, 1, 0),
(7, 36, 0, 0, 0, 0, 0, 0),
(7, 27, 1, 1, 1, 1, 1, 0),
(7, 45, 1, 1, 1, 1, 1, 0),
(7, 46, 0, 0, 0, 0, 0, 0),
(0, 25, 0, 0, 0, 0, 0, 0),
(0, 45, 0, 0, 0, 0, 0, 0),
(0, 46, 0, 0, 0, 0, 0, 0),
(8, 46, 0, 0, 0, 0, 0, 0),
(8, 45, 0, 0, 0, 0, 0, 0),
(8, 27, 0, 0, 0, 0, 0, 0),
(8, 4, 0, 0, 0, 0, 0, 0),
(8, 11, 0, 0, 0, 0, 0, 0),
(8, 12, 0, 0, 0, 0, 0, 0),
(8, 33, 0, 0, 0, 0, 0, 0),
(8, 40, 0, 0, 0, 0, 0, 0),
(8, 13, 0, 0, 0, 0, 0, 0),
(8, 17, 0, 0, 0, 0, 0, 0),
(8, 10, 0, 0, 0, 0, 0, 0),
(8, 32, 0, 0, 0, 0, 0, 0),
(8, 24, 0, 0, 0, 0, 0, 0),
(8, 23, 0, 0, 0, 0, 0, 0),
(8, 22, 0, 0, 0, 0, 0, 0),
(8, 21, 0, 0, 0, 0, 0, 0),
(8, 20, 0, 0, 0, 0, 0, 0),
(8, 19, 0, 0, 0, 0, 0, 0),
(8, 18, 0, 0, 0, 0, 0, 0),
(8, 16, 0, 0, 0, 0, 0, 0),
(8, 15, 0, 0, 0, 0, 0, 0),
(8, 44, 0, 0, 0, 0, 0, 0),
(8, 34, 0, 0, 0, 0, 0, 0),
(8, 39, 0, 0, 0, 0, 0, 0),
(8, 36, 0, 0, 0, 0, 0, 0),
(8, 3, 0, 0, 0, 0, 0, 0),
(8, 2, 1, 1, 1, 1, 1, 0),
(8, 9, 1, 1, 1, 1, 1, 0),
(8, 8, 1, 1, 1, 1, 1, 0),
(8, 7, 1, 1, 1, 1, 1, 0),
(8, 6, 0, 0, 0, 0, 0, 0),
(8, 25, 0, 0, 0, 0, 0, 0),
(8, 43, 0, 0, 0, 0, 0, 0),
(8, 14, 0, 0, 0, 0, 0, 0),
(8, 5, 0, 0, 0, 0, 0, 0),
(8, 0, 0, 0, 0, 0, 0, 0),
(8, 1, 0, 0, 0, 0, 0, 0),
(9, 0, 0, 0, 0, 0, 0, 0),
(9, 1, 0, 0, 0, 0, 0, 0),
(9, 2, 0, 0, 0, 0, 0, 0),
(9, 3, 1, 1, 1, 1, 1, 0),
(9, 4, 0, 0, 0, 0, 0, 0),
(9, 13, 0, 0, 0, 0, 0, 0),
(9, 14, 0, 0, 0, 0, 0, 0),
(9, 36, 0, 0, 0, 0, 0, 0),
(9, 39, 0, 0, 0, 0, 0, 0),
(9, 40, 0, 0, 0, 0, 0, 0),
(9, 5, 0, 0, 0, 0, 0, 0),
(9, 15, 0, 0, 0, 0, 0, 0),
(9, 16, 0, 0, 0, 0, 0, 0),
(9, 17, 0, 0, 0, 0, 0, 0),
(9, 18, 0, 0, 0, 0, 0, 0),
(9, 19, 0, 0, 0, 0, 0, 0),
(9, 20, 0, 0, 0, 0, 0, 0),
(9, 21, 0, 0, 0, 0, 0, 0),
(9, 22, 0, 0, 0, 0, 0, 0),
(9, 23, 0, 0, 0, 0, 0, 0),
(9, 24, 0, 0, 0, 0, 0, 0),
(9, 27, 0, 0, 0, 0, 0, 0),
(9, 32, 0, 0, 0, 0, 0, 0),
(9, 33, 0, 0, 0, 0, 0, 0),
(9, 34, 0, 0, 0, 0, 0, 0),
(9, 43, 0, 0, 0, 0, 0, 0),
(9, 44, 0, 0, 0, 0, 0, 0),
(9, 6, 1, 0, 0, 0, 0, 0),
(9, 7, 0, 0, 0, 0, 0, 0),
(9, 8, 1, 1, 1, 1, 1, 0),
(9, 9, 0, 0, 0, 0, 0, 0),
(9, 10, 0, 0, 0, 0, 0, 0),
(9, 11, 0, 0, 0, 0, 0, 0),
(9, 12, 0, 0, 0, 0, 0, 0),
(9, 25, 0, 0, 0, 0, 0, 0),
(9, 45, 0, 0, 0, 0, 0, 0),
(9, 46, 0, 0, 0, 0, 0, 0),
(2, 0, 0, 0, 0, 0, 0, 0),
(2, 1, 0, 0, 0, 0, 0, 0),
(3, 50, 0, 1, 1, 1, 0, 0),
(3, 45, 0, 0, 0, 0, 0, 0),
(1, 2, 1, 1, 1, 0, 1, 0),
(1, 1, 1, 1, 1, 1, 1, 0),
(6, 1, 0, 0, 0, 0, 0, 0),
(10, 1, 0, 0, 0, 0, 0, 0),
(10, 0, 0, 0, 0, 0, 0, 0),
(10, 2, 1, 1, 1, 1, 1, 0),
(10, 3, 1, 1, 1, 1, 1, 0),
(10, 17, 0, 0, 0, 0, 0, 0),
(10, 13, 0, 0, 0, 0, 0, 0),
(10, 40, 0, 0, 0, 0, 0, 0),
(10, 36, 0, 0, 0, 0, 0, 0),
(10, 39, 0, 0, 0, 0, 0, 0),
(10, 34, 0, 0, 0, 0, 0, 0),
(10, 44, 0, 0, 0, 0, 0, 0),
(10, 5, 0, 0, 0, 0, 0, 0),
(10, 15, 0, 0, 0, 0, 0, 0),
(10, 16, 0, 0, 0, 0, 0, 0),
(10, 18, 0, 0, 0, 0, 0, 0),
(10, 19, 0, 0, 0, 0, 0, 0),
(10, 20, 0, 0, 0, 0, 0, 0),
(10, 21, 0, 0, 0, 0, 0, 0),
(10, 22, 0, 0, 0, 0, 0, 0),
(10, 23, 0, 0, 0, 0, 0, 0),
(10, 24, 0, 0, 0, 0, 0, 0),
(10, 32, 0, 0, 0, 0, 0, 0),
(10, 33, 0, 0, 0, 0, 0, 0),
(10, 12, 0, 0, 0, 0, 0, 0),
(10, 14, 0, 0, 0, 0, 0, 0),
(10, 43, 0, 0, 0, 0, 0, 0),
(10, 25, 0, 0, 0, 0, 0, 0),
(10, 6, 0, 0, 0, 0, 0, 0),
(10, 7, 0, 0, 0, 0, 0, 0),
(10, 8, 0, 0, 0, 0, 0, 0),
(10, 9, 0, 0, 0, 0, 0, 0),
(10, 10, 0, 0, 0, 0, 0, 0),
(10, 11, 0, 0, 0, 0, 0, 0),
(10, 4, 0, 0, 0, 0, 0, 0),
(10, 27, 0, 0, 0, 0, 0, 0),
(10, 45, 0, 0, 0, 0, 0, 0),
(10, 46, 0, 0, 0, 0, 0, 0),
(10, 49, 1, 1, 1, 1, 1, 0),
(10, 50, 0, 0, 0, 0, 0, 0),
(8, 49, 0, 0, 0, 0, 0, 0),
(8, 50, 0, 0, 0, 0, 0, 0),
(1, 0, 0, 0, 0, 0, 0, 0),
(3, 51, 1, 0, 0, 0, 0, 0),
(1, 52, 1, 1, 1, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `m_role_akses_tr`
--

CREATE TABLE `m_role_akses_tr` (
  `id_role` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `m_view` int(11) NOT NULL,
  `m_add` int(11) NOT NULL,
  `m_edit` int(11) NOT NULL,
  `m_del` int(11) NOT NULL,
  `m_exe` int(11) NOT NULL,
  `m_app` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_role_akses_tr`
--

INSERT INTO `m_role_akses_tr` (`id_role`, `id_menu`, `m_view`, `m_add`, `m_edit`, `m_del`, `m_exe`, `m_app`) VALUES
(1, 9, 0, 1, 1, 1, 0, 0),
(1, 21, 0, 1, 1, 1, 0, 0),
(1, 3, 1, 1, 1, 1, 1, 0),
(1, 16, 1, 1, 1, 1, 0, 0),
(1, 22, 1, 1, 1, 1, 1, 0),
(1, 0, 0, 0, 0, 0, 0, 0),
(1, 20, 1, 1, 1, 1, 0, 0),
(1, 6, 1, 1, 1, 1, 0, 0),
(1, 2, 1, 1, 1, 1, 1, 0),
(3, 13, 0, 0, 0, 0, 0, 0),
(3, 3, 1, 1, 1, 1, 1, 0),
(3, 19, 1, 1, 1, 1, 1, 0),
(3, 11, 1, 1, 1, 1, 1, 0),
(3, 12, 0, 0, 0, 0, 0, 0),
(3, 26, 0, 0, 0, 0, 0, 0),
(3, 9, 1, 1, 1, 1, 1, 0),
(3, 25, 1, 1, 1, 1, 1, 0),
(3, 10, 1, 1, 1, 1, 1, 0),
(3, 5, 1, 1, 1, 1, 1, 0),
(3, 8, 1, 1, 1, 1, 1, 0),
(4, 13, 1, 1, 1, 1, 1, 0),
(4, 21, 0, 0, 0, 0, 0, 0),
(4, 7, 1, 1, 1, 1, 1, 0),
(4, 43, 0, 0, 0, 0, 0, 0),
(4, 42, 0, 0, 0, 0, 0, 0),
(4, 24, 0, 0, 0, 0, 0, 0),
(4, 0, 0, 0, 0, 0, 0, 0),
(4, 1, 0, 0, 0, 0, 0, 0),
(4, 33, 0, 0, 0, 0, 0, 0),
(4, 41, 0, 0, 0, 0, 0, 0),
(4, 19, 0, 0, 0, 0, 0, 0),
(4, 23, 0, 0, 0, 0, 0, 0),
(4, 11, 0, 0, 0, 0, 0, 0),
(4, 18, 0, 0, 0, 0, 0, 0),
(4, 17, 0, 0, 0, 0, 0, 0),
(2, 16, 0, 0, 0, 0, 0, 0),
(2, 12, 0, 0, 0, 0, 0, 0),
(2, 13, 0, 0, 0, 0, 0, 0),
(2, 25, 1, 1, 1, 1, 1, 0),
(2, 5, 0, 0, 0, 0, 0, 0),
(2, 8, 1, 1, 1, 1, 1, 0),
(2, 2, 1, 1, 1, 1, 1, 0),
(2, 9, 1, 1, 1, 1, 1, 0),
(2, 21, 0, 0, 0, 0, 0, 0),
(2, 4, 0, 0, 0, 0, 0, 0),
(2, 17, 0, 0, 0, 0, 0, 0),
(2, 27, 0, 0, 0, 0, 0, 0),
(2, 10, 0, 0, 0, 0, 0, 0),
(1, 12, 0, 0, 0, 0, 0, 0),
(2, 11, 0, 0, 0, 0, 0, 0),
(5, 7, 0, 0, 0, 0, 0, 0),
(5, 3, 0, 0, 0, 0, 0, 0),
(5, 6, 0, 0, 0, 0, 0, 0),
(5, 10, 0, 0, 0, 0, 0, 0),
(5, 4, 1, 1, 0, 0, 0, 0),
(5, 8, 0, 0, 0, 0, 0, 0),
(5, 26, 0, 0, 0, 0, 0, 0),
(5, 9, 0, 0, 0, 0, 0, 0),
(5, 11, 0, 0, 0, 0, 0, 0),
(5, 20, 0, 0, 0, 0, 0, 0),
(5, 2, 0, 0, 0, 0, 0, 0),
(5, 0, 0, 0, 0, 0, 0, 0),
(5, 12, 0, 0, 0, 0, 0, 0),
(5, 13, 0, 0, 0, 0, 0, 0),
(5, 14, 0, 0, 0, 0, 0, 0),
(5, 15, 1, 1, 1, 1, 1, 0),
(5, 16, 0, 0, 0, 0, 0, 0),
(5, 30, 0, 0, 0, 0, 0, 0),
(5, 28, 0, 0, 0, 0, 0, 0),
(5, 5, 0, 0, 0, 0, 0, 0),
(5, 18, 0, 0, 0, 0, 0, 0),
(5, 17, 0, 0, 0, 0, 0, 0),
(5, 29, 0, 0, 0, 0, 0, 0),
(5, 21, 0, 0, 0, 0, 0, 0),
(5, 19, 0, 0, 0, 0, 0, 0),
(5, 24, 0, 0, 0, 0, 0, 0),
(5, 1, 0, 0, 0, 0, 0, 0),
(5, 27, 0, 0, 0, 0, 0, 0),
(5, 25, 0, 0, 0, 0, 0, 0),
(5, 23, 0, 0, 0, 0, 0, 0),
(6, 3, 1, 1, 1, 1, 1, 0),
(6, 17, 0, 0, 0, 0, 0, 0),
(6, 18, 0, 0, 0, 0, 0, 0),
(6, 2, 1, 0, 0, 0, 0, 0),
(6, 43, 0, 0, 0, 0, 0, 0),
(6, 10, 0, 0, 0, 0, 0, 0),
(6, 8, 1, 1, 1, 1, 1, 0),
(6, 9, 1, 1, 1, 1, 1, 0),
(6, 15, 0, 0, 0, 0, 0, 0),
(6, 16, 0, 0, 0, 0, 0, 0),
(6, 34, 0, 0, 0, 0, 0, 0),
(6, 19, 0, 0, 0, 0, 0, 0),
(6, 20, 0, 0, 0, 0, 0, 0),
(6, 21, 0, 0, 0, 0, 0, 0),
(6, 22, 0, 0, 0, 0, 0, 0),
(6, 23, 0, 0, 0, 0, 0, 0),
(6, 24, 0, 0, 0, 0, 0, 0),
(6, 13, 1, 1, 1, 1, 1, 0),
(6, 14, 1, 1, 1, 1, 1, 0),
(6, 25, 0, 0, 0, 0, 0, 0),
(6, 44, 0, 0, 0, 0, 0, 0),
(6, 5, 0, 0, 0, 0, 0, 0),
(6, 40, 0, 0, 0, 0, 0, 0),
(6, 11, 0, 0, 0, 0, 0, 0),
(6, 39, 0, 0, 0, 0, 0, 0),
(6, 12, 0, 0, 0, 0, 0, 0),
(6, 4, 1, 1, 1, 1, 1, 0),
(6, 27, 0, 0, 0, 0, 0, 0),
(6, 32, 0, 0, 0, 0, 0, 0),
(6, 6, 0, 1, 1, 1, 1, 0),
(4, 39, 1, 1, 1, 1, 1, 0),
(4, 20, 0, 0, 0, 0, 0, 0),
(4, 44, 1, 1, 1, 1, 1, 0),
(4, 36, 1, 1, 1, 1, 1, 0),
(3, 27, 1, 1, 1, 1, 1, 0),
(3, 18, 1, 1, 1, 1, 1, 0),
(3, 6, 1, 1, 1, 1, 1, 0),
(1, 4, 1, 1, 1, 1, 1, 0),
(0, 8, 1, 1, 1, 1, 1, 0),
(0, 9, 1, 1, 1, 1, 1, 0),
(1, 8, 1, 1, 1, 1, 0, 0),
(0, 19, 0, 1, 1, 1, 1, 0),
(0, 20, 0, 1, 1, 1, 1, 0),
(0, 24, 0, 1, 1, 1, 1, 0),
(0, 17, 0, 1, 1, 1, 1, 0),
(0, 21, 0, 1, 1, 1, 1, 0),
(0, 22, 0, 1, 1, 1, 1, 0),
(0, 7, 1, 1, 1, 1, 1, 0),
(0, 2, 1, 1, 1, 1, 1, 0),
(0, 39, 0, 0, 0, 0, 0, 0),
(0, 27, 0, 0, 0, 0, 0, 0),
(0, 32, 0, 0, 0, 0, 0, 0),
(0, 14, 0, 1, 1, 1, 1, 0),
(0, 40, 0, 0, 0, 0, 0, 0),
(0, 34, 0, 0, 0, 0, 0, 0),
(0, 43, 0, 0, 0, 0, 0, 0),
(0, 18, 0, 1, 1, 1, 1, 0),
(0, 36, 0, 0, 0, 0, 0, 0),
(0, 12, 0, 1, 1, 1, 1, 0),
(0, 33, 0, 0, 0, 0, 0, 0),
(0, 5, 0, 1, 1, 1, 1, 0),
(0, 23, 0, 1, 1, 1, 1, 0),
(0, 1, 0, 1, 1, 1, 1, 0),
(0, 15, 0, 1, 1, 1, 1, 0),
(0, 16, 0, 1, 1, 1, 1, 0),
(1, 14, 0, 1, 1, 1, 0, 0),
(1, 5, 0, 1, 1, 1, 0, 0),
(3, 17, 1, 1, 1, 1, 0, 0),
(3, 2, 1, 0, 0, 0, 0, 0),
(3, 14, 0, 0, 0, 0, 0, 0),
(3, 15, 0, 1, 1, 1, 1, 0),
(3, 24, 1, 1, 1, 1, 1, 0),
(3, 20, 1, 1, 1, 1, 1, 0),
(3, 16, 1, 1, 1, 1, 1, 0),
(1, 15, 0, 1, 1, 1, 0, 0),
(1, 1, 0, 1, 1, 1, 1, 0),
(4, 40, 1, 1, 1, 1, 1, 0),
(4, 45, 1, 1, 1, 1, 1, 0),
(4, 34, 0, 0, 0, 0, 0, 0),
(4, 32, 0, 0, 0, 0, 0, 0),
(4, 2, 0, 0, 0, 0, 0, 0),
(4, 8, 0, 0, 0, 0, 0, 0),
(4, 10, 0, 0, 0, 0, 0, 0),
(4, 22, 0, 0, 0, 0, 0, 0),
(4, 15, 0, 0, 0, 0, 0, 0),
(4, 16, 0, 0, 0, 0, 0, 0),
(4, 6, 1, 0, 0, 0, 0, 0),
(4, 9, 1, 1, 1, 1, 1, 0),
(3, 22, 1, 1, 1, 1, 1, 0),
(2, 20, 0, 0, 0, 0, 0, 0),
(2, 6, 0, 0, 0, 0, 0, 0),
(2, 23, 0, 0, 0, 0, 0, 0),
(2, 19, 0, 0, 0, 0, 0, 0),
(2, 26, 0, 0, 0, 0, 0, 0),
(2, 7, 1, 1, 1, 1, 1, 0),
(2, 15, 0, 0, 0, 0, 0, 0),
(2, 22, 0, 0, 0, 0, 0, 0),
(2, 24, 0, 0, 0, 0, 0, 0),
(3, 21, 1, 1, 1, 1, 1, 0),
(3, 7, 1, 1, 1, 1, 1, 0),
(0, 44, 0, 0, 0, 0, 0, 0),
(0, 6, 1, 1, 1, 1, 1, 0),
(0, 3, 0, 0, 0, 0, 0, 0),
(0, 4, 0, 1, 1, 1, 1, 0),
(0, 13, 0, 1, 1, 1, 1, 0),
(0, 0, 0, 0, 0, 0, 0, 0),
(0, 10, 0, 1, 1, 1, 1, 0),
(0, 11, 0, 1, 1, 1, 1, 0),
(2, 18, 0, 0, 0, 0, 0, 0),
(6, 50, 0, 0, 0, 0, 0, 0),
(2, 3, 1, 1, 1, 1, 1, 0),
(4, 5, 0, 0, 0, 0, 0, 0),
(4, 27, 0, 0, 0, 0, 0, 0),
(4, 4, 1, 0, 0, 0, 0, 0),
(4, 3, 0, 0, 0, 0, 0, 0),
(4, 14, 1, 1, 1, 1, 1, 0),
(1, 13, 0, 1, 1, 1, 1, 0),
(2, 14, 0, 0, 0, 0, 0, 0),
(3, 23, 1, 1, 1, 1, 1, 0),
(3, 4, 0, 0, 0, 0, 0, 0),
(6, 46, 0, 0, 0, 0, 0, 0),
(6, 45, 0, 0, 0, 0, 0, 0),
(6, 33, 0, 0, 0, 0, 0, 0),
(6, 36, 1, 1, 1, 1, 1, 0),
(6, 7, 1, 1, 1, 1, 1, 0),
(1, 19, 1, 1, 1, 1, 0, 0),
(4, 12, 0, 0, 0, 0, 0, 0),
(4, 25, 0, 0, 0, 0, 0, 0),
(1, 11, 1, 1, 1, 1, 0, 0),
(1, 10, 1, 1, 1, 1, 0, 0),
(7, 0, 0, 0, 0, 0, 0, 0),
(7, 1, 1, 0, 0, 0, 0, 0),
(7, 15, 1, 1, 1, 1, 1, 0),
(7, 34, 1, 1, 1, 1, 1, 0),
(7, 43, 1, 1, 1, 1, 1, 0),
(7, 39, 0, 0, 0, 0, 0, 0),
(7, 40, 0, 0, 0, 0, 0, 0),
(7, 32, 1, 1, 1, 1, 1, 0),
(7, 33, 1, 1, 1, 1, 1, 0),
(7, 11, 1, 1, 1, 1, 1, 0),
(7, 44, 1, 1, 1, 1, 1, 0),
(7, 5, 1, 0, 0, 0, 0, 0),
(7, 16, 1, 1, 1, 1, 1, 0),
(7, 17, 1, 1, 1, 1, 1, 0),
(7, 18, 1, 1, 1, 1, 1, 0),
(7, 19, 1, 1, 1, 1, 1, 0),
(7, 20, 1, 1, 1, 1, 1, 0),
(7, 21, 1, 1, 1, 1, 1, 0),
(7, 22, 1, 1, 1, 1, 1, 0),
(7, 23, 1, 1, 1, 1, 1, 0),
(7, 24, 1, 1, 1, 1, 1, 0),
(7, 10, 1, 1, 1, 1, 1, 0),
(7, 4, 0, 0, 0, 0, 0, 0),
(7, 13, 0, 0, 0, 0, 0, 0),
(7, 14, 0, 0, 0, 0, 0, 0),
(7, 12, 0, 0, 0, 0, 0, 0),
(7, 25, 1, 1, 1, 1, 1, 0),
(7, 6, 1, 0, 0, 0, 0, 0),
(7, 7, 1, 1, 1, 1, 1, 0),
(7, 8, 1, 1, 1, 1, 1, 0),
(7, 9, 1, 1, 1, 1, 1, 0),
(7, 2, 1, 0, 0, 0, 0, 0),
(7, 3, 1, 1, 1, 1, 1, 0),
(7, 36, 0, 0, 0, 0, 0, 0),
(7, 27, 1, 1, 1, 1, 1, 0),
(7, 45, 1, 1, 1, 1, 1, 0),
(7, 46, 0, 0, 0, 0, 0, 0),
(0, 25, 0, 0, 0, 0, 0, 0),
(0, 45, 0, 0, 0, 0, 0, 0),
(0, 46, 0, 0, 0, 0, 0, 0),
(8, 46, 0, 0, 0, 0, 0, 0),
(8, 45, 0, 0, 0, 0, 0, 0),
(8, 27, 0, 0, 0, 0, 0, 0),
(8, 4, 0, 0, 0, 0, 0, 0),
(8, 11, 0, 0, 0, 0, 0, 0),
(8, 12, 0, 0, 0, 0, 0, 0),
(8, 33, 0, 0, 0, 0, 0, 0),
(8, 40, 0, 0, 0, 0, 0, 0),
(8, 13, 0, 0, 0, 0, 0, 0),
(8, 17, 0, 0, 0, 0, 0, 0),
(8, 10, 0, 0, 0, 0, 0, 0),
(8, 32, 0, 0, 0, 0, 0, 0),
(8, 24, 0, 0, 0, 0, 0, 0),
(8, 23, 0, 0, 0, 0, 0, 0),
(8, 22, 0, 0, 0, 0, 0, 0),
(8, 21, 0, 0, 0, 0, 0, 0),
(8, 20, 0, 0, 0, 0, 0, 0),
(8, 19, 0, 0, 0, 0, 0, 0),
(8, 18, 0, 0, 0, 0, 0, 0),
(8, 16, 0, 0, 0, 0, 0, 0),
(8, 15, 0, 0, 0, 0, 0, 0),
(8, 44, 0, 0, 0, 0, 0, 0),
(8, 34, 0, 0, 0, 0, 0, 0),
(8, 39, 0, 0, 0, 0, 0, 0),
(8, 36, 0, 0, 0, 0, 0, 0),
(8, 3, 0, 0, 0, 0, 0, 0),
(8, 2, 1, 1, 1, 1, 1, 0),
(8, 9, 1, 1, 1, 1, 1, 0),
(8, 8, 1, 1, 1, 1, 1, 0),
(8, 7, 1, 1, 1, 1, 1, 0),
(8, 6, 0, 0, 0, 0, 0, 0),
(8, 25, 0, 0, 0, 0, 0, 0),
(8, 43, 0, 0, 0, 0, 0, 0),
(8, 14, 0, 0, 0, 0, 0, 0),
(8, 5, 0, 0, 0, 0, 0, 0),
(8, 0, 0, 0, 0, 0, 0, 0),
(8, 1, 0, 0, 0, 0, 0, 0),
(9, 0, 0, 0, 0, 0, 0, 0),
(9, 1, 0, 0, 0, 0, 0, 0),
(9, 2, 0, 0, 0, 0, 0, 0),
(9, 3, 1, 1, 1, 1, 1, 0),
(9, 4, 0, 0, 0, 0, 0, 0),
(9, 13, 0, 0, 0, 0, 0, 0),
(9, 14, 0, 0, 0, 0, 0, 0),
(9, 36, 0, 0, 0, 0, 0, 0),
(9, 39, 0, 0, 0, 0, 0, 0),
(9, 40, 0, 0, 0, 0, 0, 0),
(9, 5, 0, 0, 0, 0, 0, 0),
(9, 15, 0, 0, 0, 0, 0, 0),
(9, 16, 0, 0, 0, 0, 0, 0),
(9, 17, 0, 0, 0, 0, 0, 0),
(9, 18, 0, 0, 0, 0, 0, 0),
(9, 19, 0, 0, 0, 0, 0, 0),
(9, 20, 0, 0, 0, 0, 0, 0),
(9, 21, 0, 0, 0, 0, 0, 0),
(9, 22, 0, 0, 0, 0, 0, 0),
(9, 23, 0, 0, 0, 0, 0, 0),
(9, 24, 0, 0, 0, 0, 0, 0),
(9, 27, 0, 0, 0, 0, 0, 0),
(9, 32, 0, 0, 0, 0, 0, 0),
(9, 33, 0, 0, 0, 0, 0, 0),
(9, 34, 0, 0, 0, 0, 0, 0),
(9, 43, 0, 0, 0, 0, 0, 0),
(9, 44, 0, 0, 0, 0, 0, 0),
(9, 6, 1, 0, 0, 0, 0, 0),
(9, 7, 0, 0, 0, 0, 0, 0),
(9, 8, 1, 1, 1, 1, 1, 0),
(9, 9, 0, 0, 0, 0, 0, 0),
(9, 10, 0, 0, 0, 0, 0, 0),
(9, 11, 0, 0, 0, 0, 0, 0),
(9, 12, 0, 0, 0, 0, 0, 0),
(9, 25, 0, 0, 0, 0, 0, 0),
(9, 45, 0, 0, 0, 0, 0, 0),
(9, 46, 0, 0, 0, 0, 0, 0),
(6, 1, 0, 0, 0, 0, 0, 0),
(6, 0, 0, 0, 0, 0, 0, 0),
(2, 0, 0, 0, 0, 0, 0, 0),
(2, 1, 0, 0, 0, 0, 0, 0),
(3, 0, 0, 0, 0, 0, 0, 0),
(3, 1, 0, 0, 0, 0, 0, 0),
(1, 7, 1, 1, 1, 1, 1, 0),
(6, 49, 0, 0, 0, 0, 0, 0),
(10, 1, 0, 0, 0, 0, 0, 0),
(10, 0, 0, 0, 0, 0, 0, 0),
(10, 2, 1, 1, 1, 1, 1, 0),
(10, 3, 1, 1, 1, 1, 1, 0),
(10, 17, 0, 0, 0, 0, 0, 0),
(10, 13, 0, 0, 0, 0, 0, 0),
(10, 40, 0, 0, 0, 0, 0, 0),
(10, 36, 0, 0, 0, 0, 0, 0),
(10, 39, 0, 0, 0, 0, 0, 0),
(10, 34, 0, 0, 0, 0, 0, 0),
(10, 44, 0, 0, 0, 0, 0, 0),
(10, 5, 0, 0, 0, 0, 0, 0),
(10, 15, 0, 0, 0, 0, 0, 0),
(10, 16, 0, 0, 0, 0, 0, 0),
(10, 18, 0, 0, 0, 0, 0, 0),
(10, 19, 0, 0, 0, 0, 0, 0),
(10, 20, 0, 0, 0, 0, 0, 0),
(10, 21, 0, 0, 0, 0, 0, 0),
(10, 22, 0, 0, 0, 0, 0, 0),
(10, 23, 0, 0, 0, 0, 0, 0),
(10, 24, 0, 0, 0, 0, 0, 0),
(10, 32, 0, 0, 0, 0, 0, 0),
(10, 33, 0, 0, 0, 0, 0, 0),
(10, 12, 0, 0, 0, 0, 0, 0),
(10, 14, 0, 0, 0, 0, 0, 0),
(10, 43, 0, 0, 0, 0, 0, 0),
(10, 25, 0, 0, 0, 0, 0, 0),
(10, 6, 0, 0, 0, 0, 0, 0),
(10, 7, 0, 0, 0, 0, 0, 0),
(10, 8, 0, 0, 0, 0, 0, 0),
(10, 9, 0, 0, 0, 0, 0, 0),
(10, 10, 0, 0, 0, 0, 0, 0),
(10, 11, 0, 0, 0, 0, 0, 0),
(10, 4, 0, 0, 0, 0, 0, 0),
(10, 27, 0, 0, 0, 0, 0, 0),
(10, 45, 0, 0, 0, 0, 0, 0),
(10, 46, 0, 0, 0, 0, 0, 0),
(10, 49, 1, 1, 1, 1, 1, 0),
(10, 50, 0, 0, 0, 0, 0, 0),
(8, 49, 0, 0, 0, 0, 0, 0),
(8, 50, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `m_role_tr`
--

CREATE TABLE `m_role_tr` (
  `id_role` int(11) NOT NULL,
  `nama_role` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_role_tr`
--

INSERT INTO `m_role_tr` (`id_role`, `nama_role`, `status`) VALUES
(2, 'Operasional', 1),
(1, 'Administrator', 1);

-- --------------------------------------------------------

--
-- Table structure for table `m_supir_tr`
--

CREATE TABLE `m_supir_tr` (
  `id_supir` int(11) NOT NULL,
  `nama_supir` varchar(255) NOT NULL,
  `no_ktp` varchar(25) NOT NULL,
  `tempat_lahir` varchar(50) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `kelamin` varchar(15) NOT NULL,
  `agama` varchar(10) NOT NULL,
  `perkawinan` varchar(15) NOT NULL,
  `alamat` text NOT NULL,
  `telp` varchar(50) NOT NULL,
  `no_sim` varchar(50) NOT NULL,
  `jenis_sim` varchar(15) NOT NULL,
  `masa_berlaku` date NOT NULL,
  `status` int(11) NOT NULL,
  `created` varchar(25) NOT NULL,
  `tanggal` date NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `tanggal_keluar` date NOT NULL,
  `alasan` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `ktp` varchar(255) NOT NULL,
  `kk` varchar(255) NOT NULL,
  `sim` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_supir_tr`
--

INSERT INTO `m_supir_tr` (`id_supir`, `nama_supir`, `no_ktp`, `tempat_lahir`, `tanggal_lahir`, `kelamin`, `agama`, `perkawinan`, `alamat`, `telp`, `no_sim`, `jenis_sim`, `masa_berlaku`, `status`, `created`, `tanggal`, `tanggal_masuk`, `tanggal_keluar`, `alasan`, `photo`, `ktp`, `kk`, `sim`) VALUES
(1, 'BUDI', '1', '2', '2024-07-18', 'Laki-laki', 'Islam', 'Nikah', '3', '4', '5', 'A', '2024-02-26', 1, 'admin', '2024-07-29', '0000-00-00', '0000-00-00', '', 'supir/43001218499sutiawan.jpeg', 'supir/30975715181ktp ikhsan.jpeg', '', ''),
(2, 'ASEP', 'ASDSA', 'ffd', '2024-07-18', 'Laki-laki', 'Islam', 'Nikah', 'aa', '', '', 'A', '0000-00-00', 1, 'admin', '2024-07-29', '0000-00-00', '0000-00-00', '', '', 'supir/3569288829kk jaka wardana.jpeg', 'supir/5944613763ktp asril paujan.jpg', 'supir/19006696831sim donny.jpeg'),
(3, 'AGUNG', 'D', 'd', '2024-07-18', 'Laki-laki', 'Islam', 'Nikah', 'sad', 'sd', 'D', 'A', '0000-00-00', 1, 'admin', '2024-07-29', '0000-00-00', '0000-00-00', '', 'supir/11914272376donny.jpeg', '', 'supir/272168301106kk agus tomy.jpeg', ''),
(0, 'SSSX', 'S', '', '0000-00-00', 'Laki-laki', '', 'Nikah', '', '', '', 'A', '0000-00-00', 1, 'admin', '2024-09-30', '2024-09-30', '0000-00-00', '', 'supir/76744911914272376donny.jpeg', 'supir/920751272168301106kk agus tomy.jpeg', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `m_unit`
--

CREATE TABLE `m_unit` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `id_user` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_unit`
--

INSERT INTO `m_unit` (`id`, `nama`, `id_user`, `status`) VALUES
(1, 'KG', 'admin', 1),
(2, 'Cbm', 'admin', 1),
(3, 'Doc', 'admin', 1),
(4, 'Trip', 'admin', 1),
(5, 'Pcs', 'admin', 1),
(6, '20FL', 'admin', 1),
(7, '20GP', 'admin', 1),
(8, '20OT', 'admin', 1),
(9, '40FL', 'admin', 1),
(10, 'Unit', 'admin', 1),
(11, 'WM', 'admin', 1),
(12, 'Cont', 'admin', 1),
(14, 'Shipment', 'admin', 1),
(15, '40GP', 'admin', 1),
(16, '20RF', 'admin', 1),
(17, '40RF', 'admin', 1),
(18, '40HQ', 'admin', 1),
(19, '20FT', 'admin', 1),
(20, '20HC', 'admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `m_user`
--

CREATE TABLE `m_user` (
  `id` int(11) NOT NULL,
  `id_user` varchar(100) NOT NULL,
  `nama_user` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `id_role` int(11) NOT NULL,
  `id_pt` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `create_date` datetime NOT NULL,
  `status` int(11) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `user_caption` varchar(10) NOT NULL,
  `telp` varchar(50) NOT NULL,
  `nama_bank` varchar(50) NOT NULL,
  `no_rek` varchar(25) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_user`
--

INSERT INTO `m_user` (`id`, `id_user`, `nama_user`, `password`, `pass`, `id_role`, `id_pt`, `email`, `create_date`, `status`, `photo`, `user_caption`, `telp`, `nama_bank`, `no_rek`) VALUES
(2, 'admin', 'ADMINISTRATOR', '0cc175b9c0f1b6a831c399e269772661', 'a', 1, 1, '', '2017-01-26 00:00:00', 1, 'photo/photo.png', '', '', '', ''),
(239, 'finance1', 'FINANCE1', '0cc175b9c0f1b6a831c399e269772661', 'a', 3, 1, '', '2024-09-18 04:31:38', 1, 'photo/no.jpg', '', '', '', ''),
(238, 'cs2', 'CS2', '0cc175b9c0f1b6a831c399e269772661', 'a', 6, 1, '', '2024-09-18 04:31:17', 1, 'photo/no.jpg', '', '', '', ''),
(237, 'cs1', 'CS1', '0cc175b9c0f1b6a831c399e269772661', 'a', 6, 1, '', '2024-09-18 04:31:05', 1, 'photo/no.jpg', '', '', '', ''),
(235, 'sales1', 'SALES1', '0cc175b9c0f1b6a831c399e269772661', 'a', 2, 1, '', '2024-09-18 04:29:57', 1, 'photo/no.jpg', '', '', '', ''),
(236, 'sales2', 'SALES2', '0cc175b9c0f1b6a831c399e269772661', 'a', 2, 1, '', '2024-09-18 04:30:18', 1, 'photo/no.jpg', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `m_user_tr`
--

CREATE TABLE `m_user_tr` (
  `id` int(11) NOT NULL,
  `id_user` varchar(100) NOT NULL,
  `nama_user` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `id_role` int(11) NOT NULL,
  `id_pt` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `create_date` datetime NOT NULL,
  `status` int(11) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `user_caption` varchar(10) NOT NULL,
  `telp` varchar(50) NOT NULL,
  `nama_bank` varchar(50) NOT NULL,
  `no_rek` varchar(25) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_user_tr`
--

INSERT INTO `m_user_tr` (`id`, `id_user`, `nama_user`, `password`, `pass`, `id_role`, `id_pt`, `email`, `create_date`, `status`, `photo`, `user_caption`, `telp`, `nama_bank`, `no_rek`) VALUES
(2, 'admin', 'ADMINISTRATOR', '0cc175b9c0f1b6a831c399e269772661', 'a', 1, 1, '', '2017-01-26 00:00:00', 1, 'photo/photo.png', '', '', '', ''),
(240, 'q', 'Q', '7694f4a66316e53c8cdd9d9954bd611d', 'q', 1, 1, '', '2024-09-30 07:43:32', 1, 'photo/no.jpg', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `m_vendor_tr`
--

CREATE TABLE `m_vendor_tr` (
  `id_vendor` int(11) NOT NULL,
  `nama_vendor` varchar(255) NOT NULL,
  `caption` varchar(50) NOT NULL,
  `kontak` varchar(50) NOT NULL,
  `alamat` text NOT NULL,
  `telp` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `created` varchar(50) NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `m_vendor_tr`
--

INSERT INTO `m_vendor_tr` (`id_vendor`, `nama_vendor`, `caption`, `kontak`, `alamat`, `telp`, `email`, `status`, `created`, `tanggal`) VALUES
(0, 'A', '', '', '', '', '', 1, 'admin', '2024-09-30'),
(1, 'VENDOR A', '12', '2', '3', '4', '5', 1, 'admin', '2024-07-29'),
(2, 'VENDOR B', '4', 'sd', 'Pusat Data dan Teknologi Informasi Ketenagakerjaan, Kementerian Ketenagakerjaan, Lt. 6 Gedung B, Jl. Jend. Gatot Subroto Kav.51, Jakarta Selatan', 'd', 'd', 1, 'admin', '2024-07-29');

-- --------------------------------------------------------

--
-- Table structure for table `t_antarbank`
--

CREATE TABLE `t_antarbank` (
  `id_antar` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `ket` varchar(255) NOT NULL,
  `amount` double NOT NULL,
  `rate` double NOT NULL,
  `cur` varchar(3) NOT NULL,
  `id_jurnal1` int(11) NOT NULL,
  `id_jurnal2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `t_awb_biaya`
--

CREATE TABLE `t_awb_biaya` (
  `id` int(11) NOT NULL,
  `id_jo` int(11) NOT NULL,
  `ket` varchar(255) NOT NULL,
  `nilai` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `t_dp`
--

CREATE TABLE `t_dp` (
  `id_dp` int(11) NOT NULL,
  `tgl_dp` date NOT NULL,
  `no_dp` varchar(15) NOT NULL,
  `id_cust` int(11) NOT NULL,
  `ket` varchar(255) NOT NULL,
  `tagihan` double NOT NULL,
  `bayar` double NOT NULL,
  `status` int(11) NOT NULL,
  `created` varchar(25) NOT NULL,
  `id_jurnal` int(11) NOT NULL,
  `id_bank` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_dp`
--

INSERT INTO `t_dp` (`id_dp`, `tgl_dp`, `no_dp`, `id_cust`, `ket`, `tagihan`, `bayar`, `status`, `created`, `id_jurnal`, `id_bank`) VALUES
(30, '2024-03-07', 'DP-2400001', 9, 'DP Job Order', 5000000, 1000000, 0, 'admin', 5191, 1787),
(31, '2024-05-15', 'DP-2400002', 16, 'DP JOB', 2500000, 0, 0, 'admin', 5194, 1787);

-- --------------------------------------------------------

--
-- Table structure for table `t_dp_bayar`
--

CREATE TABLE `t_dp_bayar` (
  `id_bayar` int(11) NOT NULL,
  `id_dp` int(11) NOT NULL,
  `id_tagihan` int(11) NOT NULL,
  `id_jurnal` int(11) NOT NULL,
  `jenis` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_dp_bayar`
--

INSERT INTO `t_dp_bayar` (`id_bayar`, `id_dp`, `id_tagihan`, `id_jurnal`, `jenis`) VALUES
(38, 30, 12, 5192, 0);

-- --------------------------------------------------------

--
-- Table structure for table `t_jaminan`
--

CREATE TABLE `t_jaminan` (
  `id_jaminan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `due_date` date NOT NULL,
  `no` varchar(15) NOT NULL,
  `id_partner` int(11) NOT NULL,
  `ket` varchar(255) NOT NULL,
  `cur` varchar(3) NOT NULL,
  `tagihan` double NOT NULL,
  `bayar` double NOT NULL,
  `id_jurnal` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_jaminan`
--

INSERT INTO `t_jaminan` (`id_jaminan`, `tanggal`, `due_date`, `no`, `id_partner`, `ket`, `cur`, `tagihan`, `bayar`, `id_jurnal`, `status`, `created`) VALUES
(1, '2024-06-11', '2024-06-28', 'J-2400001', 3, 'Jaminan Container ECNU2434744', 'IDR', 1000000, 1000000, 5187, 1, 'admin'),
(2, '2024-08-14', '2024-08-30', 'J-2400002', 67, 'JAMINAN IMP-2401042 BL LCHJKT9138', 'IDR', 1500000, 1500000, 5188, 1, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `t_jaminan_bayar`
--

CREATE TABLE `t_jaminan_bayar` (
  `id_bayar` int(11) NOT NULL,
  `id_jaminan` int(11) NOT NULL,
  `id_jurnal` int(11) NOT NULL,
  `jumlah` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_jaminan_bayar`
--

INSERT INTO `t_jaminan_bayar` (`id_bayar`, `id_jaminan`, `id_jurnal`, `jumlah`) VALUES
(1, 2, 5189, 1500000),
(2, 1, 5190, 1000000);

-- --------------------------------------------------------

--
-- Table structure for table `t_jo`
--

CREATE TABLE `t_jo` (
  `id_jo` int(11) NOT NULL,
  `jenis_jo` int(11) NOT NULL,
  `id_quo` int(11) NOT NULL,
  `jo_date` date NOT NULL,
  `jo_no` varchar(25) NOT NULL,
  `id_cust` int(11) NOT NULL,
  `tipe` varchar(15) NOT NULL,
  `deliv` varchar(15) NOT NULL,
  `profit_sales` double NOT NULL,
  `id_pol` int(11) NOT NULL,
  `id_pod` int(11) NOT NULL,
  `etd` date DEFAULT NULL,
  `eta` date DEFAULT NULL,
  `no_si` varchar(50) NOT NULL,
  `tgl_si` date DEFAULT NULL,
  `created_si` varchar(25) NOT NULL,
  `ship_note` text NOT NULL,
  `hbl` varchar(35) NOT NULL,
  `mbl` varchar(35) NOT NULL,
  `id_ship` int(11) NOT NULL,
  `id_cons` int(11) NOT NULL,
  `vessel` varchar(50) NOT NULL,
  `status` int(11) NOT NULL,
  `created` varchar(25) NOT NULL,
  `qty` double NOT NULL,
  `paket` varchar(20) NOT NULL,
  `party` varchar(25) NOT NULL,
  `gross` double NOT NULL,
  `meas` double NOT NULL,
  `comm` varchar(255) NOT NULL,
  `goods` text NOT NULL,
  `sale` double NOT NULL,
  `buy` double NOT NULL,
  `reim` double NOT NULL,
  `profit` double NOT NULL,
  `status_bl` int(11) NOT NULL,
  `tgl_bl` date DEFAULT NULL,
  `created_bl` varchar(25) NOT NULL,
  `freight` varchar(15) NOT NULL,
  `rate` varchar(15) NOT NULL,
  `id_noti` int(11) NOT NULL,
  `id_agent` int(11) NOT NULL,
  `carriage` varchar(50) NOT NULL,
  `id_rec` int(11) NOT NULL,
  `id_del` int(11) NOT NULL,
  `id_fin` int(11) NOT NULL,
  `net` double NOT NULL,
  `marks` text NOT NULL,
  `attach_marks` text NOT NULL,
  `attach_goods` text NOT NULL,
  `id_ship_mawb` int(11) NOT NULL,
  `id_cons_mawb` int(11) NOT NULL,
  `cur_awb` varchar(3) NOT NULL,
  `rate_awb` double NOT NULL,
  `flight1` varchar(25) NOT NULL,
  `tgl_flight1` date DEFAULT NULL,
  `flight2` varchar(25) NOT NULL,
  `tgl_flight2` date DEFAULT NULL,
  `id_transit` int(11) NOT NULL,
  `id_line` int(11) NOT NULL,
  `iata` varchar(25) NOT NULL,
  `account` varchar(25) NOT NULL,
  `customs` varchar(25) NOT NULL,
  `pieces` int(11) NOT NULL,
  `unit` varchar(5) NOT NULL,
  `cw` double NOT NULL,
  `asuransi` varchar(25) NOT NULL,
  `handling` text NOT NULL,
  `other_charges` double NOT NULL,
  `vessel_conn` varchar(50) NOT NULL,
  `etd_conn` date DEFAULT NULL,
  `eta_conn` date DEFAULT NULL,
  `id_sj` int(11) NOT NULL,
  `id_skdo` int(11) NOT NULL,
  `id_spdo` int(11) NOT NULL,
  `no_cont` varchar(15) NOT NULL,
  `tipe_cont` varchar(7) NOT NULL,
  `jml_jo` int(11) NOT NULL,
  `id_joc` int(11) NOT NULL,
  `tgl_bc` date DEFAULT NULL,
  `no_bc` varchar(25) NOT NULL,
  `created_bc` varchar(25) NOT NULL,
  `id_to` int(11) NOT NULL,
  `your_reff` varchar(50) NOT NULL,
  `id_cargo` int(11) NOT NULL,
  `doc_cut` date DEFAULT NULL,
  `doc_at` varchar(25) NOT NULL,
  `cy_cut` date DEFAULT NULL,
  `cy_at` varchar(25) NOT NULL,
  `del_cut` date DEFAULT NULL,
  `del_at` varchar(25) NOT NULL,
  `goods_mawb` text NOT NULL,
  `id_agent1` int(11) NOT NULL,
  `no_ori` varchar(25) NOT NULL,
  `agent1` varchar(255) NOT NULL,
  `tpt_isu` varchar(100) NOT NULL,
  `tgl_isu` date DEFAULT NULL,
  `ref_no` varchar(25) NOT NULL,
  `pay` varchar(255) NOT NULL,
  `exc` varchar(25) NOT NULL,
  `tgl_noa` date DEFAULT NULL,
  `no_noa` varchar(25) NOT NULL,
  `created_noa` varchar(25) NOT NULL,
  `id_ware` int(11) NOT NULL,
  `id_pbm` int(11) NOT NULL,
  `id_tran` int(11) NOT NULL,
  `tgl_berangkat` date DEFAULT NULL,
  `ket_berangkat` varchar(255) NOT NULL,
  `tgl_tiba` date DEFAULT NULL,
  `ket_tiba` varchar(255) NOT NULL,
  `tgl_doc` date DEFAULT NULL,
  `ket_doc` varchar(255) NOT NULL,
  `tgl_sppb` date DEFAULT NULL,
  `ket_sppb` varchar(255) NOT NULL,
  `tgl_deliv` date DEFAULT NULL,
  `ket_deliv` varchar(255) NOT NULL,
  `no_aju` varchar(50) NOT NULL,
  `tgl_inv` date DEFAULT NULL,
  `ket_inv` varchar(255) NOT NULL,
  `booking` varchar(255) NOT NULL,
  `services` varchar(15) NOT NULL,
  `contact_bc` varchar(255) NOT NULL,
  `id_pfpd` int(11) NOT NULL,
  `id_truck` int(11) NOT NULL,
  `tgl_kirim` date NOT NULL,
  `nopen` varchar(35) NOT NULL,
  `tgl_nopen` date NOT NULL,
  `jalur` varchar(25) NOT NULL,
  `tgl_jalur` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_jo`
--

INSERT INTO `t_jo` (`id_jo`, `jenis_jo`, `id_quo`, `jo_date`, `jo_no`, `id_cust`, `tipe`, `deliv`, `profit_sales`, `id_pol`, `id_pod`, `etd`, `eta`, `no_si`, `tgl_si`, `created_si`, `ship_note`, `hbl`, `mbl`, `id_ship`, `id_cons`, `vessel`, `status`, `created`, `qty`, `paket`, `party`, `gross`, `meas`, `comm`, `goods`, `sale`, `buy`, `reim`, `profit`, `status_bl`, `tgl_bl`, `created_bl`, `freight`, `rate`, `id_noti`, `id_agent`, `carriage`, `id_rec`, `id_del`, `id_fin`, `net`, `marks`, `attach_marks`, `attach_goods`, `id_ship_mawb`, `id_cons_mawb`, `cur_awb`, `rate_awb`, `flight1`, `tgl_flight1`, `flight2`, `tgl_flight2`, `id_transit`, `id_line`, `iata`, `account`, `customs`, `pieces`, `unit`, `cw`, `asuransi`, `handling`, `other_charges`, `vessel_conn`, `etd_conn`, `eta_conn`, `id_sj`, `id_skdo`, `id_spdo`, `no_cont`, `tipe_cont`, `jml_jo`, `id_joc`, `tgl_bc`, `no_bc`, `created_bc`, `id_to`, `your_reff`, `id_cargo`, `doc_cut`, `doc_at`, `cy_cut`, `cy_at`, `del_cut`, `del_at`, `goods_mawb`, `id_agent1`, `no_ori`, `agent1`, `tpt_isu`, `tgl_isu`, `ref_no`, `pay`, `exc`, `tgl_noa`, `no_noa`, `created_noa`, `id_ware`, `id_pbm`, `id_tran`, `tgl_berangkat`, `ket_berangkat`, `tgl_tiba`, `ket_tiba`, `tgl_doc`, `ket_doc`, `tgl_sppb`, `ket_sppb`, `tgl_deliv`, `ket_deliv`, `no_aju`, `tgl_inv`, `ket_inv`, `booking`, `services`, `contact_bc`, `id_pfpd`, `id_truck`, `tgl_kirim`, `nopen`, `tgl_nopen`, `jalur`, `tgl_jalur`) VALUES
(1, 0, 1, '2024-01-22', 'FW-2400001', 5, 'IMPORT', 'SEA', 0, 405, 337, '2024-09-23', '2024-09-26', '', NULL, '', '', 'EGLV05118272', 'EGLV05118272', 44, 5, 'EVER ORDER 0419', 0, 'admin', 100, 'Carton', '', 17523.79, 31.2884, 'MULTIGRAIN BLACK CEREAL SOYMILK BOTTLE', '', 4750000, 3000000, 1500000, 1750000, 0, NULL, '', '', '', 0, 0, '', 0, 0, 0, 0, '', '', '', 0, 0, '', 0, '', NULL, '', NULL, 0, 0, '', '', '', 0, '', 0, '', '', 0, '', NULL, NULL, 0, 0, 0, '', '', 0, 0, '2024-09-19', 'BC-FW-2400001', 'admin', 26, '12345', 5, '2024-09-12', '12', '2024-09-12', '34', '2024-09-13', '3', '', 0, '', '', '', NULL, '', '', '', NULL, '', '', 0, 0, 0, NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '0138567', NULL, '', '', '', '', 0, 0, '0000-00-00', '', '0000-00-00', '', '0000-00-00'),
(2, 0, 2, '2024-02-29', 'FW-2400002', 6, 'EXPORT', 'SEA', 0, 337, 347, '2024-03-05', '2024-03-14', 'SI-FW-2400002', '2024-09-19', 'admin', '', 'BLJHCM248070', '067DX20526X', 26, 45, 'WAN HAI 331', 1, 'admin', 25, 'Case', '1x20FT', 12930, 12930, '', 'wqe', 6150000, 1500000, 500000, 4650000, 1, '2024-09-19', 'admin', 'PREPAID', '', 38, 44, '', 337, 347, 347, 12930, 'we', 'we', 'qwe', 0, 0, '', 0, '', NULL, '', NULL, 0, 26, '', '', '', 0, '', 0, '', '', 0, '', '0000-00-00', '0000-00-00', 0, 0, 0, '', '', 0, 0, NULL, '', '', 0, '', 0, NULL, '', NULL, '', NULL, '', '', 0, '', '', 'JAKARTA', '2024-09-19', '202/OLL/SI/VII/2024', 'SINGAPORE, SINGAPORE', '', NULL, '', '', 0, 0, 0, NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', NULL, '', '', '', '', 0, 0, '0000-00-00', '', '0000-00-00', '', '0000-00-00'),
(3, 0, 3, '2024-04-10', 'FW-2400003', 30, 'EXPORT', 'AIR', 0, 337, 407, '2024-04-17', '2024-04-18', '', NULL, '', '', 'CGKSZX240001', '205-9843 4103', 4, 30, '013860', 1, 'admin', 250, 'Piece', '', 20, 0, '', '5 BOXES = 139 PCS OF\r\nGEN 2.75 COMMUNICATION MODULE,\r\nBRACKET MOUNT\r\nHS CODE: 8526.91', 27100000, 18750000, 0, 8350000, 1, '2024-09-19', 'admin', 'COLLECT', '', 0, 0, '', 0, 0, 0, 0, 'NO MARK', '', '', 0, 0, 'IDR', 0, '013860', '2024-09-19', '', '0000-00-00', 0, 4, '', '', '', 5, 'Kg', 0, '', '', 0, '', NULL, NULL, 0, 0, 0, '', '', 0, 0, NULL, '', '', 0, '', 0, NULL, '', NULL, '', NULL, '', '', 0, '', '', '', NULL, '', '', '', NULL, '', '', 0, 0, 0, NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', NULL, '', '', '', '', 0, 0, '0000-00-00', '', '0000-00-00', '', '0000-00-00'),
(4, 0, 4, '2024-05-14', 'FW-2400004', 9, 'IMPORT', 'SEA', 0, 339, 337, '2024-05-21', '2024-05-24', '', NULL, '', '', 'BL7823434', 'BL7823434', 0, 0, 'CMA CGM KRUGER', 0, 'admin', 60, 'Package', '', 9840, 0, 'FILTRATION MACHINE', '', 4350000, 1500000, 0, 2850000, 0, NULL, '', '', '', 0, 0, '', 0, 0, 0, 0, '', '', '', 0, 0, '', 0, '', NULL, '', NULL, 0, 0, '', '', '', 0, '', 0, '', '', 0, '', NULL, NULL, 0, 0, 0, '', '', 0, 0, NULL, '', '', 0, '', 0, NULL, '', NULL, '', NULL, '', '', 0, '', '', '', NULL, '', '', '', NULL, '', '', 0, 0, 0, NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '0138567', NULL, '', '', '', '', 0, 0, '0000-00-00', '', '0000-00-00', '', '0000-00-00'),
(5, 0, 5, '2024-06-07', 'FW-2400005', 47, 'EXPORT', 'SEA', 0, 337, 347, '2024-06-13', '2024-06-19', '', NULL, '', '', 'BLYUW', 'DD', 38, 26, 'CAPE TOWN 031S', 0, 'admin', 0, '', '', 0, 0, '', '', 5650000, 2500000, 0, 3150000, 0, NULL, '', '', '', 0, 0, '', 0, 0, 0, 0, '', '', '', 0, 0, '', 0, '', NULL, '', NULL, 0, 0, '', '', '', 0, '', 0, '', '', 0, '', NULL, NULL, 0, 0, 0, '', '', 0, 0, NULL, '', '', 0, '', 0, NULL, '', NULL, '', NULL, '', '', 0, '', '', '', NULL, '', '', '', NULL, '', '', 0, 0, 0, NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', NULL, '', '', '', '', 0, 0, '0000-00-00', '', '0000-00-00', '', '0000-00-00'),
(6, 0, 6, '2024-06-26', 'FW-2400006', 16, 'IMPORT', 'SEA', 0, 442, 337, '2024-09-05', '2024-09-26', '', NULL, '', '', 'BL7823434', 'BL7823434', 47, 37, 'CAPE TOWN 031S', 1, 'admin', 1, 'Box', '', 350000, 0, '', '', 7507500, 0, 0, 7507500, 0, NULL, '', 'PREPAID', '', 47, 0, '', 0, 0, 0, 43343, 'FLEXIBLE METAL-PLASTIC CONDUIT, GALVANIZED STEEL WITH PVC COATED 1', '', '', 0, 0, '', 0, '', NULL, '', NULL, 0, 3, '', '', '', 0, '', 0, '', '', 0, '', NULL, NULL, 0, 0, 0, '', '', 0, 0, NULL, '', '', 0, '', 0, NULL, '', NULL, '', NULL, '', '', 0, '', '', '', NULL, '', '', '', '2024-09-19', 'FW-2400006', 'admin', 8, 37, 0, NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '', NULL, '', '', '', '', 0, 0, '0000-00-00', '', '0000-00-00', '', '0000-00-00'),
(7, 0, 9, '2024-09-22', 'FW-2400007', 16, 'IMPORT', 'SEA', 0, 442, 337, '2024-09-05', '2024-09-10', '', NULL, '', '', 'BL7823434', 'BL7823434', 46, 5, 'CMA CGM KRUGER', 0, 'admin', 10, 'Crate', '1x3', 12500, 125000, 'CARGO HANDLING', '', 179475720, 2000000, 750000, 177475720, 0, NULL, '', '', '', 0, 0, '', 0, 0, 0, 0, '', '', '', 0, 0, '', 0, '', NULL, '', NULL, 0, 33, '', '', '', 0, '', 0, '', '', 0, '', NULL, NULL, 0, 0, 0, '', '', 0, 0, NULL, '', '', 0, '', 0, NULL, '', NULL, '', NULL, '', '', 0, '', '', '', NULL, '', '', '', NULL, '', '', 45, 0, 0, NULL, '', NULL, '', NULL, '', NULL, '', NULL, '', '0138567XXX', NULL, '', '', '', '', 45, 67, '2024-09-13', '1', '2024-09-19', 'HIJAU', '2024-09-26');

-- --------------------------------------------------------

--
-- Table structure for table `t_jo_cont`
--

CREATE TABLE `t_jo_cont` (
  `id_cont` int(11) NOT NULL,
  `id_jo` int(11) NOT NULL,
  `no_cont` varchar(25) NOT NULL,
  `no_seal` varchar(25) NOT NULL,
  `feet` varchar(10) NOT NULL,
  `id_sj` int(11) NOT NULL,
  `ket` varchar(255) NOT NULL,
  `qty` double NOT NULL,
  `paket` varchar(100) NOT NULL,
  `berat` double NOT NULL,
  `id_sj_tr` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_jo_cont`
--

INSERT INTO `t_jo_cont` (`id_cont`, `id_jo`, `no_cont`, `no_seal`, `feet`, `id_sj`, `ket`, `qty`, `paket`, `berat`, `id_sj_tr`) VALUES
(1, 2, 'CSLU2147077', 'JKTA479948', '20TK', 0, '', 0, '', 0, 0),
(2, 7, 'OOLU9985203', '', '20TK', 0, 'we', 12, 'Bag', 1500, 19),
(3, 7, 'TRHU5249612', '', '40HQ', 0, 'coba', 1500, 'Bag', 1534, 20),
(4, 7, '435435', '', '40GP', 0, 'dd', 34, 'Bag', 23333, 0);

-- --------------------------------------------------------

--
-- Table structure for table `t_jo_marks`
--

CREATE TABLE `t_jo_marks` (
  `id_detil` int(11) NOT NULL,
  `id_jo` int(11) NOT NULL,
  `no_marks` text NOT NULL,
  `ket` text NOT NULL,
  `gross` double NOT NULL,
  `net` double NOT NULL,
  `meas` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_jo_marks`
--

INSERT INTO `t_jo_marks` (`id_detil`, `id_jo`, `no_marks`, `ket`, `gross`, `net`, `meas`) VALUES
(23, 65, 'SII 2024-010\nCONTAINER 1', '136 UNPAILS @ 15 KGS = 15,960.00 KGS OF POLYURETHANE GU-555', 15960, 13680, 70);

-- --------------------------------------------------------

--
-- Table structure for table `t_jo_sj_tr`
--

CREATE TABLE `t_jo_sj_tr` (
  `id_sj` int(11) NOT NULL,
  `tipe` varchar(3) NOT NULL,
  `no_sj` varchar(25) NOT NULL,
  `tgl_sj` date NOT NULL,
  `no_cont` varchar(25) NOT NULL,
  `id_asal` int(11) NOT NULL,
  `id_tujuan` int(11) NOT NULL,
  `jenis_mobil` varchar(25) NOT NULL,
  `id_mobil` int(11) NOT NULL,
  `id_supir` int(11) NOT NULL,
  `uj` double NOT NULL,
  `ket` text NOT NULL,
  `status` int(11) NOT NULL,
  `created` varchar(25) NOT NULL,
  `jml_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_jo_sj_tr`
--

INSERT INTO `t_jo_sj_tr` (`id_sj`, `tipe`, `no_sj`, `tgl_sj`, `no_cont`, `id_asal`, `id_tujuan`, `jenis_mobil`, `id_mobil`, `id_supir`, `uj`, `ket`, `status`, `created`, `jml_order`) VALUES
(19, 'FCL', 'SJ-2400001', '2024-10-04', 'OOLU9985203', 4, 4, '1 x 20', 5, 3, 175000, '', 0, 'admin', 1),
(20, 'FCL', 'SJ-2400002', '2024-10-04', 'TRHU5249612', 4, 4, '1 x 20', 5, 3, 0, '', 0, 'admin', 1),
(21, 'LCL', 'SJ-2400003', '2024-10-04', 'ECHO12345X', 3, 4, '1 x 40', 3, 2, 250000, 'tes', 0, 'admin', 2);

-- --------------------------------------------------------

--
-- Table structure for table `t_jo_tagihan`
--

CREATE TABLE `t_jo_tagihan` (
  `id_tagihan` int(11) NOT NULL,
  `jenis` int(11) NOT NULL,
  `id_jo` int(11) NOT NULL,
  `kode` varchar(3) NOT NULL,
  `no_tagihan` varchar(25) NOT NULL,
  `tgl_tagihan` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `id_cust` int(11) NOT NULL,
  `kurs` double NOT NULL,
  `ppn` double NOT NULL,
  `materai` double NOT NULL,
  `id_dp` int(11) NOT NULL,
  `dp` double NOT NULL,
  `cur` varchar(3) NOT NULL,
  `id_jurnal` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `tagihan` double NOT NULL,
  `bayar` double NOT NULL,
  `jenis_pr` int(11) NOT NULL,
  `no_faktur` varchar(50) NOT NULL,
  `jenis_jo` int(11) NOT NULL,
  `tagihan_idr` double NOT NULL,
  `open` int(11) NOT NULL,
  `ket_inv` text NOT NULL,
  `no_payment` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_jo_tagihan`
--

INSERT INTO `t_jo_tagihan` (`id_tagihan`, `jenis`, `id_jo`, `kode`, `no_tagihan`, `tgl_tagihan`, `due_date`, `id_cust`, `kurs`, `ppn`, `materai`, `id_dp`, `dp`, `cur`, `id_jurnal`, `status`, `tagihan`, `bayar`, `jenis_pr`, `no_faktur`, `jenis_jo`, `tagihan_idr`, `open`, `ket_inv`, `no_payment`) VALUES
(1, 1, 1, '001', '00001/INV-FW/2024', '2024-02-01', '2024-02-12', 5, 0, 11, 0, 0, 0, 'IDR', 5138, 0, 3441000, 0, 0, '', 0, 0, 0, '', ''),
(2, 2, 1, '', '00001/RE-FW/2024', '2024-02-01', '2024-02-12', 42, 0, 0, 0, 0, 0, 'IDR', 5140, 0, 1500000, 0, 0, '', 0, 1500000, 0, '', ''),
(3, 4, 1, '', '00001/PR-FW/2024', '2024-01-11', '2024-09-26', 67, 0, 0, 0, 0, 0, 'IDR', 5151, 0, 1500000, 0, 1, '', 0, 1500000, 0, '', ''),
(4, 4, 1, '', '00002/PR-FW/2024', '2024-02-06', '2024-09-26', 48, 0, 0, 0, 0, 0, 'IDR', 5150, 0, 1500000, 0, 0, '', 0, 1500000, 0, '', ''),
(5, 3, 1, '', '00001/DN-FW/2024', '2024-09-19', '2024-09-26', 42, 15000, 0, 0, 0, 0, 'USD', 5143, 0, 10, 0, 0, '', 0, 150000, 0, '', ''),
(6, 1, 2, '001', '00002/INV-FW/2024', '2024-03-05', '2024-03-12', 6, 0, 11, 0, 0, 0, 'IDR', 5145, 0, 6271500, 0, 0, '', 0, 0, 0, '', ''),
(7, 2, 2, '', '00002/RE-FW/2024', '2024-03-11', '2024-03-11', 13, 0, 0, 0, 0, 0, 'IDR', 5147, 1, 500000, 500000, 0, '', 0, 500000, 0, '', ''),
(8, 4, 2, '', '00003/PR-FW/2024', '2024-03-06', '2024-09-26', 46, 0, 0, 0, 0, 0, 'IDR', 5149, 1, 1000000, 1000000, 0, '', 0, 1000000, 0, '', ''),
(9, 4, 2, '', '00004/PR-FW/2024', '2024-03-05', '2024-09-26', 8, 0, 0, 0, 0, 0, 'IDR', 5153, 0, 500000, 0, 1, '', 0, 500000, 0, '', ''),
(10, 1, 3, '001', '00003/INV-FW/2024', '2024-04-24', '2024-04-25', 30, 0, 11, 0, 0, 0, 'IDR', 5158, 1, 30081000, 30081000, 0, '', 0, 0, 0, '', ''),
(11, 4, 3, '', '00005/PR-FW/2024', '2024-04-11', '2024-09-26', 4, 0, 0, 0, 0, 0, 'IDR', 5157, 0, 18750000, 0, 0, '', 0, 18750000, 0, '', ''),
(12, 1, 4, '001', '00004/INV-FW/2024', '2024-05-28', '2024-05-29', 9, 0, 11, 0, 30, 1000000, 'IDR', 5193, 0, 3828500, 0, 0, '', 0, 0, 0, '', ''),
(13, 1, 5, '001', '00005/INV-FW/2024', '2024-06-26', '2024-07-17', 47, 0, 11, 0, 0, 0, 'IDR', 5160, 0, 6271500, 0, 0, '', 0, 0, 0, '', ''),
(14, 5, 5, '', '00001/CN-FW/2024', '2024-06-20', '2024-09-26', 42, 15000, 0, 0, 0, 0, 'USD', 5161, 0, 100, 0, 0, '', 0, 1500000, 0, '', ''),
(15, 1, 6, '001', '00006/INV-FW/2024', '2024-06-28', '2024-07-10', 16, 0, 11, 0, 0, 0, 'IDR', 5162, 0, 6668325, 0, 0, '', 0, 0, 0, '', ''),
(16, 3, 6, '', '00002/DN-FW/2024', '2024-06-26', '2024-07-03', 42, 15000, 0, 0, 0, 0, 'USD', 5170, 1, 100, 100, 0, '', 0, 1500000, 0, '', ''),
(17, 5, 6, '', '00002/CN-FW/2024', '2024-09-19', '2024-09-26', 33, 15000, 0, 0, 0, 0, 'USD', 5164, 1, 105, 105, 0, '', 0, 1575000, 0, '', ''),
(18, 4, 5, '', '00006/PR-FW/2024', '2024-09-19', '2024-09-26', 48, 0, 11, 0, 0, 0, 'IDR', 5172, 0, 1110000, 0, 0, '', 0, 1110000, 0, '', ''),
(19, 1, 7, '001', '00007/SO-INV/2024', '2024-09-27', '2024-10-04', 68, 0, 11, 0, 0, 0, 'IDR', 5204, 0, 3801750, 0, 0, '', 0, 0, 0, '', ''),
(20, 2, 7, '', '00003/RE-FW/2024', '2024-09-22', '2024-09-29', 6, 0, 0, 0, 0, 0, 'IDR', 5175, 0, 750000, 0, 0, '', 0, 750000, 0, '', ''),
(22, 4, 4, '', '00002/BON-FW/2024', '2024-09-22', NULL, 48, 0, 0, 0, 0, 0, 'IDR', 5179, 1, 1500000, 1500000, 1, '', 0, 0, 0, '', ''),
(23, 5, 7, '', '00003/PO-CN/2024', '2024-09-27', '2024-10-04', 33, 15000, 0, 0, 0, 0, 'USD', 0, 0, 100, 0, 0, '', 0, 0, 0, '', ''),
(24, 3, 7, '', '00003/SO-DN/2024', '2024-09-27', '2024-10-04', 31, 5000, 0, 0, 0, 0, 'USD', 5212, 0, 35060, 0, 0, '', 0, 0, 0, '', ''),
(25, 3, 7, '', '00004/SO-DN/2024', '2024-09-27', '2024-10-04', 33, 1, 0, 0, 0, 0, 'USD', 0, 0, 340, 0, 0, '', 0, 0, 0, '', ''),
(26, 3, 7, '', '00005/SO-DN/2024', '2024-09-27', '2024-10-04', 26, 1, 0, 0, 0, 0, 'IDR', 0, 0, 350, 0, 0, '', 0, 0, 0, '', ''),
(27, 1, 7, '002', '00008/SO-INV/2024', '2024-09-27', '2024-10-04', 16, 0, 0, 0, 0, 0, 'IDR', 0, 0, 30, 0, 0, '', 0, 0, 0, '', ''),
(28, 4, 7, '', '00007/PO-PR/2024', '2024-09-27', '2024-10-04', 3, 1, 0, 0, 0, 0, 'IDR', 5214, 0, 250000, 0, 0, '', 0, 0, 0, '', ''),
(29, 4, 7, '', '00008/PO-PR/2024', '2024-09-27', '2024-10-04', 37, 1, 0, 0, 0, 0, 'IDR', 0, 0, 250000, 0, 0, '', 0, 0, 0, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `t_jo_tagihan_bayar`
--

CREATE TABLE `t_jo_tagihan_bayar` (
  `id_bayar` int(11) NOT NULL,
  `id_tagihan` int(11) NOT NULL,
  `id_jurnal` int(11) NOT NULL,
  `cur_bayar` varchar(3) NOT NULL,
  `bayar` double NOT NULL,
  `kurs` double NOT NULL,
  `pph` double NOT NULL,
  `id_bank` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_jo_tagihan_bayar`
--

INSERT INTO `t_jo_tagihan_bayar` (`id_bayar`, `id_tagihan`, `id_jurnal`, `cur_bayar`, `bayar`, `kurs`, `pph`, `id_bank`) VALUES
(1, 7, 5165, 'IDR', 500000, 1, 0, 1787),
(2, 10, 5166, 'IDR', 30081000, 1, 0, 1787),
(4, 8, 5168, 'IDR', 1000000, 1, 0, 1787),
(5, 17, 5169, 'USD', 105, 15000, 0, 1788),
(6, 16, 5171, 'USD', 100, 15000, 0, 1788);

-- --------------------------------------------------------

--
-- Table structure for table `t_jo_tagihan_detil`
--

CREATE TABLE `t_jo_tagihan_detil` (
  `id_detil` int(11) NOT NULL,
  `id_tagihan` int(11) NOT NULL,
  `kode` varchar(3) NOT NULL,
  `id_jo` int(11) NOT NULL,
  `id_cost` int(11) NOT NULL,
  `qty` double NOT NULL,
  `unit` varchar(15) NOT NULL,
  `price` double NOT NULL,
  `cur` varchar(3) NOT NULL,
  `note` varchar(255) NOT NULL,
  `id_jo_kecil` int(11) NOT NULL,
  `kurs` double NOT NULL,
  `disc` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_jo_tagihan_detil`
--

INSERT INTO `t_jo_tagihan_detil` (`id_detil`, `id_tagihan`, `kode`, `id_jo`, `id_cost`, `qty`, `unit`, `price`, `cur`, `note`, `id_jo_kecil`, `kurs`, `disc`) VALUES
(1, 1, '001', 1, 426, 1, '20FT', 300000, 'IDR', '', 0, 0, 0),
(2, 1, '001', 1, 479, 1, '20FT', 250000, 'IDR', '', 0, 0, 0),
(3, 1, '001', 1, 667, 1, '20FT', 1900000, 'IDR', '', 0, 0, 0),
(4, 1, '001', 1, 454, 1, '20FT', 150000, 'IDR', '', 0, 0, 0),
(5, 1, '001', 1, 553, 1, '20FT', 500000, 'IDR', '', 0, 0, 0),
(6, 2, '', 0, 494, 1, '20FL', 1000000, '', '', 0, 0, 0),
(7, 2, '', 0, 452, 1, '20FL', 500000, '', '', 0, 0, 0),
(8, 3, '', 0, 494, 1, '20FL', 1000000, '', '', 0, 0, 0),
(9, 3, '', 0, 454, 1, '20FL', 500000, '', '', 0, 0, 0),
(10, 4, '', 0, 667, 1, '20FL', 1500000, '', '', 0, 0, 0),
(11, 5, '', 0, 468, 1, '20FL', 10, '', '', 0, 0, 0),
(12, 6, '001', 2, 472, 1, '20FL', 2500000, 'IDR', '', 0, 0, 0),
(13, 6, '001', 2, 422, 1, '20FL', 1000000, 'IDR', '', 0, 0, 0),
(14, 6, '001', 2, 416, 1, '20FL', 350000, 'IDR', '', 0, 0, 0),
(15, 6, '001', 2, 594, 1, '20FL', 300000, 'IDR', '', 0, 0, 0),
(16, 6, '001', 2, 667, 1, '20FL', 1000000, 'IDR', '', 0, 0, 0),
(17, 6, '001', 2, 494, 1, '20FL', 500000, 'IDR', 'AS RECEIPT	', 0, 0, 0),
(18, 7, '', 0, 451, 1, '20FL', 500000, '', '', 0, 0, 0),
(19, 8, '', 0, 472, 1, '20FL', 1000000, '', '', 0, 0, 0),
(20, 9, '', 0, 443, 1, '20FL', 500000, '', '', 0, 0, 0),
(21, 10, '001', 3, 415, 1, 'Doc', 250000, 'IDR', '', 0, 0, 0),
(22, 10, '001', 3, 614, 1, 'Unit', 1500000, 'IDR', '', 0, 0, 0),
(23, 10, '001', 3, 594, 1, 'Doc', 350000, 'IDR', '', 0, 0, 0),
(24, 11, '', 0, 471, 25, 'KG', 750000, '', '', 0, 0, 0),
(25, 10, '001', 3, 471, 25, 'KG', 1000000, 'IDR', '', 0, 0, 0),
(26, 12, '001', 4, 418, 2, '20HC', 1000000, 'IDR', '', 0, 0, 0),
(27, 12, '001', 4, 421, 2, 'Cont', 500000, 'IDR', '', 0, 0, 0),
(28, 12, '001', 4, 667, 1, '20HC', 1000000, 'IDR', '', 0, 0, 0),
(29, 12, '001', 4, 594, 1, 'Doc', 350000, 'IDR', '', 0, 0, 0),
(30, 13, '001', 5, 472, 1, '20FL', 2500000, 'IDR', '', 0, 0, 0),
(31, 13, '001', 5, 422, 1, '20FL', 1000000, 'IDR', '', 0, 0, 0),
(32, 13, '001', 5, 416, 1, '20FL', 350000, 'IDR', '', 0, 0, 0),
(33, 13, '001', 5, 594, 1, '20FL', 300000, 'IDR', '', 0, 0, 0),
(34, 13, '001', 5, 667, 1, '20FL', 1000000, 'IDR', '', 0, 0, 0),
(35, 13, '001', 5, 494, 1, '20FL', 500000, 'IDR', 'AS RECEIPT	', 0, 0, 0),
(36, 14, '', 0, 468, 1, 'Doc', 100, '', '', 0, 0, 0),
(37, 15, '001', 6, 472, 1, 'WM', 100, 'USD', '', 0, 15500, 0),
(38, 15, '001', 6, 440, 1, '20FL', 600000, 'IDR', '', 0, 0, 0),
(39, 15, '001', 6, 422, 1, 'Doc', 550000, 'IDR', '', 0, 0, 0),
(40, 15, '001', 6, 416, 1, 'Doc', 150000, 'IDR', '', 0, 0, 0),
(41, 15, '001', 6, 449, 1, 'WM', 35, 'USD', '', 0, 15500, 0),
(42, 15, '001', 6, 478, 1, 'WM', 600000, 'IDR', '', 0, 15500, 0),
(43, 15, '001', 6, 413, 1, 'Doc', 60, 'USD', '', 0, 15500, 0),
(44, 15, '001', 6, 423, 1, 'Doc', 70, 'USD', '', 0, 15500, 0),
(45, 16, '', 0, 468, 1, '20FL', 100, '', '', 0, 0, 0),
(46, 17, '', 0, 567, 1, 'Doc', 100, '', '', 0, 0, 0),
(47, 17, '', 0, 537, 1, 'Doc', 5, '', '', 0, 0, 0),
(48, 18, '', 0, 667, 1, '20FL', 1000000, '', '', 0, 0, 0),
(49, 19, '001', 7, 472, 1, 'WM', 100, 'USD', '', 0, 10000, 0),
(50, 19, '001', 7, 440, 1, '20FL', 600000, 'IDR', '', 0, 0, 0),
(51, 19, '001', 7, 422, 1, 'Doc', 550000, 'IDR', '', 0, 0, 0),
(52, 19, '001', 7, 416, 1, 'Doc', 150000, 'IDR', '', 0, 0, 0),
(53, 19, '001', 7, 449, 1, 'WM', 35, 'USD', '', 0, 15000, 0),
(54, 19, '001', 7, 478, 1, 'WM', 600000, 'IDR', '', 0, 0, 0),
(57, 20, '', 0, 451, 1, '20FL', 750000, '', '', 0, 0, 0),
(59, 22, '', 4, 451, 1, '20FL', 1500000, 'IDR', '', 0, 0, 0),
(61, 23, '', 0, 446, 1, '20FL', 100, '', '', 0, 0, 0),
(62, 24, '', 0, 446, 1, '20FL', 60, '', '', 0, 0, 0),
(63, 24, '', 0, 493, 1, '20FL', 35000, '', '', 0, 0, 0),
(64, 25, '', 0, 709, 1, '20FL', 340, '', '', 0, 0, 0),
(65, 26, '', 0, 446, 1, '20FL', 350, '', '', 0, 0, 0),
(66, 27, '002', 7, 493, 1, '20FL', 30, 'IDR', '', 0, 0, 0),
(67, 28, '', 0, 446, 1, '20FL', 250000, '', '', 0, 0, 0),
(68, 29, '', 0, 446, 1, '20FL', 250000, '', '', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `t_jo_tr`
--

CREATE TABLE `t_jo_tr` (
  `id_jo` int(11) NOT NULL,
  `tipe` varchar(3) NOT NULL,
  `no_jo` varchar(25) NOT NULL,
  `tgl_jo` date NOT NULL,
  `jenis` int(11) NOT NULL,
  `id_jo_cont` int(11) NOT NULL,
  `id_cust` int(11) NOT NULL,
  `id_asal` int(11) NOT NULL,
  `id_tujuan` int(11) NOT NULL,
  `penerima` varchar(255) NOT NULL,
  `alamat_penerima` text NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `qty` double NOT NULL,
  `unit` varchar(25) NOT NULL,
  `berat` double NOT NULL,
  `vol` double NOT NULL,
  `biaya_kirim` double NOT NULL,
  `ket` text NOT NULL,
  `created` varchar(25) NOT NULL,
  `status` int(11) NOT NULL,
  `id_sj` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_jo_tr`
--

INSERT INTO `t_jo_tr` (`id_jo`, `tipe`, `no_jo`, `tgl_jo`, `jenis`, `id_jo_cont`, `id_cust`, `id_asal`, `id_tujuan`, `penerima`, `alamat_penerima`, `nama_barang`, `qty`, `unit`, `berat`, `vol`, `biaya_kirim`, `ket`, `created`, `status`, `id_sj`) VALUES
(35, 'FCL', '2400001', '2024-10-04', 1, 2, 3, 4, 4, 'sdf', '', '', 12, 'Bag', 1500, 0, 456, '', 'admin', 0, 19),
(36, 'FCL', '2400002', '2024-10-04', 1, 3, 3, 4, 4, 'sfds', '', '', 1500, 'Bag', 1534, 0, 0, '', 'admin', 0, 20),
(37, 'LCL', '2400003', '2024-10-04', 0, 0, 9, 4, 4, 'qsds', 'dsad', 'dsds', 232, 'Drum', 323, 4, 4343, '', 'admin', 1, 21),
(38, 'LCL', '2400004', '2024-10-04', 0, 0, 5, 3, 4, 'udin', 'Jl. Raya', 'sd', 23, 'Bag', 33, 3, 323, '', 'admin', 1, 21);

-- --------------------------------------------------------

--
-- Table structure for table `t_jurnal`
--

CREATE TABLE `t_jurnal` (
  `id_jurnal` int(11) NOT NULL,
  `tgl_jurnal` date DEFAULT NULL,
  `no_jurnal` varchar(10) DEFAULT NULL,
  `ket` varchar(255) DEFAULT NULL,
  `jumlah` double DEFAULT NULL,
  `cur` varchar(5) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `id_user` varchar(50) DEFAULT NULL,
  `jenis` int(11) DEFAULT NULL,
  `kurs` double DEFAULT NULL,
  `no_ref` varchar(25) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_jurnal`
--

INSERT INTO `t_jurnal` (`id_jurnal`, `tgl_jurnal`, `no_jurnal`, `ket`, `jumlah`, `cur`, `status`, `id_user`, `jenis`, `kurs`, `no_ref`) VALUES
(5138, '2024-02-01', '240200001', 'INV IMPORT PT.  UNI INDO JAYA (00001/INV-FW/2024) (FW-2400001)', 3441000, 'IDR', 1, 'admin', 1, 0, ''),
(5140, '2024-02-01', '240200002', 'RE IMPORT PT.  UNI INDO JAYA (00001/RE-FW/2024) (FW-2400001)', 1500000, 'IDR', 1, 'admin', 1, 0, ''),
(5151, '2024-01-11', '240100001', 'PR IMPORT PT.  UNI INDO JAYA (00001/PR-FW/2024) (FW-2400001)', 1500000, 'IDR', 1, 'admin', 1, 0, ''),
(5150, '2024-02-06', '240200003', 'PR IMPORT PT.  UNI INDO JAYA (00002/PR-FW/2024) (FW-2400001)', 1500000, 'IDR', 1, 'admin', 1, 0, ''),
(5143, '2024-09-19', '240900003', 'DN IMPORT PT.  UNI INDO JAYA (00001/DN-FW/2024) (FW-2400001)', 10, 'USD', 1, 'admin', 1, 15000, ''),
(5145, '2024-03-05', '240300001', 'INV EXPORT PT. ALAM MAJU TERUS (00002/INV-FW/2024) (FW-2400002)', 6271500, 'IDR', 1, 'admin', 1, 0, ''),
(5147, '2024-03-11', '240300002', 'RE EXPORT PT. ALAM MAJU TERUS (00002/RE-FW/2024) (FW-2400002)', 500000, 'IDR', 1, 'admin', 1, 0, ''),
(5149, '2024-03-06', '240300003', 'PR EXPORT PT. ALAM MAJU TERUS (00003/PR-FW/2024) (FW-2400002)', 1000000, 'IDR', 1, 'admin', 1, 0, ''),
(5153, '2024-03-05', '240300004', 'PR EXPORT PT. ALAM MAJU TERUS (00004/PR-FW/2024) (FW-2400002)', 500000, 'IDR', 1, 'admin', 1, 0, ''),
(5158, '2024-04-24', '240400002', 'INV EXPORT PT. BINTANG DIATAS ANGKASA (00003/INV-FW/2024) (FW-2400003)', 30081000, 'IDR', 1, 'admin', 1, 0, ''),
(5157, '2024-04-11', '240400001', 'PR EXPORT PT. BINTANG DIATAS ANGKASA (00005/PR-FW/2024) (FW-2400003)', 18750000, 'IDR', 1, 'admin', 1, 0, ''),
(5193, '2024-05-28', '240500001', 'INV IMPORT PT. BUKIT INDAH PRIMA INDONESIA (00004/INV-FW/2024) (FW-2400004)', 3828500, 'IDR', 1, 'admin', 1, 0, ''),
(5160, '2024-09-19', '240900005', 'INV EXPORT ORIENT OVERSEAS CONTAINER LINE (00005/INV-FW/2024) (FW-2400005)', 6271500, 'IDR', 1, 'admin', 1, 0, ''),
(5161, '2024-09-19', '240900006', 'CN EXPORT ORIENT OVERSEAS CONTAINER LINE (00001/CN-FW/2024) (FW-2400005)', 100, 'USD', 1, 'admin', 1, 15000, ''),
(5162, '2024-09-19', '240900007', 'INV IMPORT PT. GUNUNG BUNDAR SEJAHTERA (00006/INV-FW/2024) (FW-2400006)', 6668325, 'IDR', 1, 'admin', 1, 15, ''),
(5170, '2024-06-26', '240600001', 'DN IMPORT PT. GUNUNG BUNDAR SEJAHTERA (00002/DN-FW/2024) (FW-2400006)', 100, 'USD', 1, 'admin', 1, 15000, ''),
(5164, '2024-09-19', '240900009', 'CN IMPORT PT. GUNUNG BUNDAR SEJAHTERA (00002/CN-FW/2024) (FW-2400006)', 105, 'USD', 1, 'admin', 1, 15000, ''),
(5165, '2024-09-19', '240900010', 'PAYMENT RE PT. DAYA GUNA SELALU (00002/RE-FW/2024) (FW-2400002)', 500000, 'IDR', 1, 'admin', 1, 1, ''),
(5166, '2024-09-19', '240900011', 'PAYMENT INV PT. BINTANG DIATAS ANGKASA (00003/INV-FW/2024) (FW-2400003)', 30081000, 'IDR', 1, 'admin', 1, 1, ''),
(5192, '2024-03-07', '240300007', 'PAYMENT DP INV FW-2400004 (DP-2400001)', 1000000, 'IDR', 1, 'admin', 1, 0, ''),
(5194, '2024-05-15', '240500002', 'DP. PT. GUNUNG BUNDAR SEJAHTERA (DP-2400002)', 2500000, 'IDR', 1, 'admin', 1, 0, ''),
(5168, '2024-09-19', '240900013', 'PAYMENT PR PT YANG MING SHIPPING INDONESIA (00003/PR-FW/2024) (FW-2400002)', 1000000, 'IDR', 1, 'admin', 1, 1, ''),
(5169, '2024-09-19', '240900014', 'PAYMENT CN ATLANTIS PARCEL LOGISTICS SDN BHD (00002/CN-FW/2024) (FW-2400006)', 105, 'USD', 1, 'admin', 1, 15000, ''),
(5171, '2024-09-19', '240900015', 'PAYMENT DN AGENT UNI LOGISTICS (00002/DN-FW/2024) (FW-2400006)', 100, 'USD', 1, 'admin', 1, 15000, ''),
(5172, '2024-09-19', '240900016', 'PR EXPORT ORIENT OVERSEAS CONTAINER LINE (00006/PR-FW/2024) (FW-2400005)', 1110000, 'IDR', 1, 'admin', 1, 0, ''),
(5173, '2024-02-14', '240200004', 'Pembelian ATK', 750000, 'IDR', 1, 'admin', 3, 1, ''),
(5174, '2024-03-21', '240300005', 'Listrik Biulan Maret 2024', 250000, 'IDR', 1, 'admin', 3, 1, ''),
(5175, '2024-09-22', '240900017', 'RE IMPORT PT. GUNUNG BUNDAR SEJAHTERA (00003/RE-FW/2024) (FW-2400007)', 750000, 'IDR', 1, 'admin', 1, 0, ''),
(5177, '2024-09-22', '240900019', 'Payment Kasbon JO BUDI (00002/BON-FW/2024)', 2000000, 'IDR', 1, 'admin', 1, 0, ''),
(5178, '2024-09-23', '240900020', 'Payment Kasbon JO AMIR (00001/BON-FW/2024)', 1500000, 'IDR', 1, 'admin', 1, 0, ''),
(5179, '2024-09-22', '240900021', 'Biaya Vendor PT. TRUCKING (00002/BON-FW/2024) (FW-2400004)', 1500000, 'IDR', 1, 'admin', 1, 1, ''),
(5180, '2024-09-25', '240900022', 'Pengembalian Kasbon BUDI (00002/BON-FW/2024)', 500000, 'IDR', 1, 'admin', 1, 0, ''),
(5181, '2024-09-22', '240900023', 'Kasbon AMIR Urus Pajak Kendaraan (BON-240001)', 2500000, 'IDR', 1, 'admin', 1, 0, ''),
(5182, '2024-09-22', '240900024', 'Biaya Parkir (BON-240001)', 10000, 'IDR', 1, 'admin', 1, 0, ''),
(5183, '2024-09-22', '240900025', 'Biaya STNK (BON-240001)', 2000000, 'IDR', 1, 'admin', 1, 0, ''),
(5184, '2024-09-22', '240900026', 'Pengembalian Kasbon AMIR (BON-240001)', 490000, 'IDR', 1, 'admin', 1, 0, ''),
(5185, '2024-09-22', '240900027', 'Kasbon MUSTAFA VISIT CUSTOMER (BON-240002)', 1500000, 'IDR', 1, 'admin', 1, 0, ''),
(5186, '2024-09-22', '240900028', 'Coffe Break (BON-240002)', 500000, 'IDR', 1, 'admin', 1, 0, ''),
(5187, '2024-06-11', '240600002', 'Jaminan Container ECNU2434744 (J-2400001)', 1000000, 'IDR', 1, 'admin', 1, 0, ''),
(5188, '2024-08-14', '240800001', 'JAMINAN IMP-2401042 BL LCHJKT9138 (J-2400002)', 1500000, 'IDR', 1, 'admin', 1, 0, ''),
(5189, '2024-09-22', '240900029', 'Pengembalian Jaminan (J-2400002)', 1500000, 'IDR', 1, 'admin', 1, 0, ''),
(5190, '2024-09-22', '240900030', 'Pengembalian Jaminan (J-2400001)', 1000000, 'IDR', 1, 'admin', 1, 0, ''),
(5191, '2024-03-07', '240300006', 'DP. PT. BUKIT INDAH PRIMA INDONESIA (DP-2400001)', 5000000, 'IDR', 1, 'admin', 1, 0, ''),
(5214, '2024-09-27', '240900031', 'PR IMPORT EVERGREEN SHIPPING INDONESIA (00007/PO-PR/2024) (FW-2400007)', 250000, 'IDR', 1, 'admin', 1, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `t_jurnal_detil`
--

CREATE TABLE `t_jurnal_detil` (
  `id` int(11) NOT NULL,
  `id_jurnal` int(11) NOT NULL,
  `id_coa` int(11) NOT NULL,
  `id_bank` int(11) NOT NULL,
  `jumlah` double NOT NULL,
  `status` char(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_jurnal_detil`
--

INSERT INTO `t_jurnal_detil` (`id`, `id_jurnal`, `id_coa`, `id_bank`, `jumlah`, `status`) VALUES
(11309, 5138, 89, 0, 341000, 'K'),
(11308, 5138, 49, 0, 3100000, 'K'),
(11307, 5138, 36, 0, 3441000, 'D'),
(11313, 5140, 266, 0, 1500000, 'K'),
(11312, 5140, 36, 0, 1500000, 'D'),
(11337, 5151, 44, 0, 1500000, 'K'),
(11336, 5151, 266, 0, 1500000, 'D'),
(11335, 5150, 44, 0, 1500000, 'K'),
(11334, 5150, 76, 0, 1500000, 'D'),
(11318, 5143, 37, 0, 10, 'D'),
(11319, 5143, 49, 0, 10, 'K'),
(11325, 5145, 89, 0, 621500, 'K'),
(11324, 5145, 50, 0, 5650000, 'K'),
(11323, 5145, 36, 0, 6271500, 'D'),
(11329, 5147, 266, 0, 500000, 'K'),
(11328, 5147, 36, 0, 500000, 'D'),
(11333, 5149, 44, 0, 1000000, 'K'),
(11332, 5149, 85, 0, 1000000, 'D'),
(11341, 5153, 44, 0, 500000, 'K'),
(11340, 5153, 266, 0, 500000, 'D'),
(11353, 5158, 89, 0, 2981000, 'K'),
(11352, 5158, 50, 0, 27100000, 'K'),
(11351, 5158, 36, 0, 30081000, 'D'),
(11350, 5157, 44, 0, 18750000, 'K'),
(11349, 5157, 85, 0, 18750000, 'D'),
(11428, 5193, 89, 0, 478500, 'K'),
(11427, 5193, 49, 0, 3350000, 'K'),
(11426, 5193, 36, 0, 3828500, 'D'),
(11357, 5160, 36, 0, 6271500, 'D'),
(11358, 5160, 50, 0, 5650000, 'K'),
(11359, 5160, 89, 0, 621500, 'K'),
(11360, 5161, 85, 0, 100, 'D'),
(11361, 5161, 48, 0, 100, 'K'),
(11362, 5162, 36, 0, 6668325, 'D'),
(11363, 5162, 49, 0, 6007500, 'K'),
(11364, 5162, 89, 0, 660825, 'K'),
(11380, 5170, 49, 0, 100, 'K'),
(11379, 5170, 37, 0, 100, 'D'),
(11367, 5164, 76, 0, 105, 'D'),
(11368, 5164, 48, 0, 105, 'K'),
(11369, 5165, 1787, 0, 500000, 'D'),
(11370, 5165, 36, 0, 500000, 'K'),
(11371, 5166, 1787, 0, 30081000, 'D'),
(11372, 5166, 36, 0, 30081000, 'K'),
(11429, 5194, 1787, 0, 2500000, 'D'),
(11425, 5192, 221, 0, 1000000, 'D'),
(11375, 5168, 44, 0, 1000000, 'D'),
(11376, 5168, 1787, 0, 1000000, 'K'),
(11377, 5169, 48, 0, 105, 'D'),
(11378, 5169, 1788, 0, 105, 'K'),
(11381, 5171, 1788, 0, 100, 'D'),
(11382, 5171, 37, 0, 100, 'K'),
(11383, 5172, 85, 0, 1000000, 'D'),
(11384, 5172, 44, 0, 1110000, 'K'),
(11385, 5172, 67, 0, 110000, 'D'),
(11386, 5173, 1778, 0, 750000, 'D'),
(11387, 5173, 1787, 0, 750000, 'K'),
(11388, 5174, 1780, 0, 250000, 'D'),
(11389, 5174, 1785, 0, 250000, 'K'),
(11390, 5175, 36, 0, 750000, 'D'),
(11391, 5175, 266, 0, 750000, 'K'),
(11478, 5214, 44, 0, 250000, 'K'),
(11477, 5214, 76, 0, 250000, 'D'),
(11395, 5177, 177, 0, 2000000, 'D'),
(11396, 5177, 1787, 0, 2000000, 'K'),
(11397, 5178, 177, 0, 1500000, 'D'),
(11398, 5178, 1787, 0, 1500000, 'K'),
(11399, 5179, 266, 0, 1500000, 'D'),
(11400, 5179, 177, 0, 1500000, 'K'),
(11401, 5180, 1787, 0, 500000, 'D'),
(11402, 5180, 177, 0, 500000, 'K'),
(11403, 5181, 177, 0, 2500000, 'D'),
(11404, 5181, 1787, 0, 2500000, 'K'),
(11405, 5182, 1789, 0, 10000, 'D'),
(11406, 5182, 177, 0, 10000, 'K'),
(11407, 5183, 1789, 0, 2000000, 'D'),
(11408, 5183, 177, 0, 2000000, 'K'),
(11409, 5184, 1787, 0, 490000, 'D'),
(11410, 5184, 177, 0, 490000, 'K'),
(11411, 5185, 177, 0, 1500000, 'D'),
(11412, 5185, 1787, 0, 1500000, 'K'),
(11413, 5186, 1783, 0, 500000, 'D'),
(11414, 5186, 177, 0, 500000, 'K'),
(11415, 5187, 217, 0, 1000000, 'D'),
(11416, 5187, 1787, 0, 1000000, 'K'),
(11417, 5188, 217, 0, 1500000, 'D'),
(11418, 5188, 1787, 0, 1500000, 'K'),
(11419, 5189, 1787, 0, 1500000, 'D'),
(11420, 5189, 217, 0, 1500000, 'K'),
(11421, 5190, 1787, 0, 1000000, 'D'),
(11422, 5190, 217, 0, 1000000, 'K'),
(11423, 5191, 1787, 0, 5000000, 'D'),
(11424, 5191, 221, 0, 5000000, 'K'),
(11430, 5194, 221, 0, 2500000, 'K');

-- --------------------------------------------------------

--
-- Table structure for table `t_jurnal_sum`
--

CREATE TABLE `t_jurnal_sum` (
  `bln` varchar(2) NOT NULL,
  `thn` varchar(4) NOT NULL,
  `id_coa` int(11) NOT NULL,
  `nilai` double NOT NULL,
  `jenis` varchar(1) NOT NULL,
  `cur` varchar(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_jurnal_sum`
--

INSERT INTO `t_jurnal_sum` (`bln`, `thn`, `id_coa`, `nilai`, `jenis`, `cur`) VALUES
('09', '2024', 36, -14891175, '', 'IDR'),
('09', '2024', 49, 6007500, '', 'IDR'),
('09', '2024', 89, 1282325, '', 'IDR'),
('02', '2024', 36, 4941000, '', 'IDR'),
('02', '2024', 49, 3100000, '', 'IDR'),
('02', '2024', 89, 341000, '', 'IDR'),
('09', '2024', 266, 1250000, '', 'IDR'),
('02', '2024', 266, -1500000, '', 'IDR'),
('09', '2024', 44, 860000, '', 'IDR'),
('09', '2024', 76, 250000, '', 'IDR'),
('09', '2024', 37, -90, '', 'USD'),
('09', '2024', 49, 10, '', 'USD'),
('09', '2024', 50, 5650000, '', 'IDR'),
('03', '2024', 36, 6771500, '', 'IDR'),
('03', '2024', 50, 5650000, '', 'IDR'),
('03', '2024', 89, 621500, '', 'IDR'),
('03', '2024', 266, 0, '', 'IDR'),
('09', '2024', 85, 1000000, '', 'IDR'),
('03', '2024', 85, 1000000, '', 'IDR'),
('03', '2024', 44, 1500000, '', 'IDR'),
('02', '2024', 76, 1500000, '', 'IDR'),
('02', '2024', 44, 1500000, '', 'IDR'),
('01', '2024', 266, 1500000, '', 'IDR'),
('01', '2024', 44, 1500000, '', 'IDR'),
('04', '2024', 85, 18750000, '', 'IDR'),
('04', '2024', 44, 18750000, '', 'IDR'),
('04', '2024', 36, 30081000, '', 'IDR'),
('04', '2024', 50, 27100000, '', 'IDR'),
('04', '2024', 89, 2981000, '', 'IDR'),
('09', '2024', 85, 100, '', 'USD'),
('09', '2024', 48, 100, '', 'USD'),
('09', '2024', 76, 105, '', 'USD'),
('09', '2024', 1787, 25571000, '', 'IDR'),
('09', '2024', 1788, -5, '', 'USD'),
('06', '2024', 37, 100, '', 'USD'),
('06', '2024', 49, 100, '', 'USD'),
('12', '2023', 1785, 1500000, '', 'IDR'),
('12', '2023', 1786, 500000, '', 'IDR'),
('12', '2023', 1787, 35000000, '', 'IDR'),
('12', '2023', 1788, 20000000, '', 'IDR'),
('09', '2024', 67, 110000, '', 'IDR'),
('02', '2024', 1778, -750000, '', 'IDR'),
('02', '2024', 1787, -750000, '', 'IDR'),
('03', '2024', 1780, -250000, '', 'IDR'),
('03', '2024', 1785, -250000, '', 'IDR'),
('09', '2024', 177, 2500000, '', 'IDR'),
('09', '2024', 1789, -2010000, '', 'IDR'),
('09', '2024', 1783, -500000, '', 'IDR'),
('06', '2024', 217, 1000000, '', 'IDR'),
('06', '2024', 1787, -1000000, '', 'IDR'),
('08', '2024', 217, 1500000, '', 'IDR'),
('08', '2024', 1787, -1500000, '', 'IDR'),
('09', '2024', 217, -2500000, '', 'IDR'),
('03', '2024', 1787, 5000000, '', 'IDR'),
('03', '2024', 221, 4000000, '', 'IDR'),
('05', '2024', 36, 3828500, '', 'IDR'),
('05', '2024', 49, 3350000, '', 'IDR'),
('05', '2024', 89, 478500, '', 'IDR'),
('05', '2024', 1787, 2500000, '', 'IDR'),
('05', '2024', 221, 2500000, '', 'IDR'),
('09', '2024', 36, 0, '', 'USD');

-- --------------------------------------------------------

--
-- Table structure for table `t_kasbon`
--

CREATE TABLE `t_kasbon` (
  `id_kasbon` int(11) NOT NULL,
  `id_jo` int(11) NOT NULL,
  `no_kasbon` varchar(25) NOT NULL,
  `tanggal` date NOT NULL,
  `id_opera` int(11) NOT NULL,
  `ket` varchar(255) NOT NULL,
  `tagihan` double NOT NULL,
  `bayar` double NOT NULL,
  `status` int(11) NOT NULL,
  `id_jurnal` int(11) NOT NULL,
  `nilai_balance` double NOT NULL,
  `id_jurnal_balance` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_kasbon`
--

INSERT INTO `t_kasbon` (`id_kasbon`, `id_jo`, `no_kasbon`, `tanggal`, `id_opera`, `ket`, `tagihan`, `bayar`, `status`, `id_jurnal`, `nilai_balance`, `id_jurnal_balance`) VALUES
(8031, 7, '00001/BON-FW/2024', '2024-09-22', 9, 'DO FW-2400007', 1500000, 0, 0, 5178, 0, 0),
(8032, 4, '00002/BON-FW/2024', '2024-09-22', 11, 'Trucking Lift Off', 2000000, 2000000, 1, 5177, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `t_kasbon_bayar`
--

CREATE TABLE `t_kasbon_bayar` (
  `id_bayar` int(11) NOT NULL,
  `id_kasbon` int(11) NOT NULL,
  `id_jurnal` int(11) NOT NULL,
  `id_tagihan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_kasbon_bayar`
--

INSERT INTO `t_kasbon_bayar` (`id_bayar`, `id_kasbon`, `id_jurnal`, `id_tagihan`) VALUES
(1, 8032, 5179, 22),
(2, 8032, 5180, 0);

-- --------------------------------------------------------

--
-- Table structure for table `t_kasbon_detil`
--

CREATE TABLE `t_kasbon_detil` (
  `id_detil` int(11) NOT NULL,
  `id_kasbon` int(11) NOT NULL,
  `ket` varchar(255) NOT NULL,
  `jumlah` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_kasbon_detil`
--

INSERT INTO `t_kasbon_detil` (`id_detil`, `id_kasbon`, `ket`, `jumlah`) VALUES
(1, 8031, 'Biaya DO', 1500000),
(2, 8032, 'TRUCKING LIFT OFF', 2000000);

-- --------------------------------------------------------

--
-- Table structure for table `t_kasbon_lain`
--

CREATE TABLE `t_kasbon_lain` (
  `id_kasbon` int(11) NOT NULL,
  `no_kasbon` varchar(15) NOT NULL,
  `tgl_kasbon` date NOT NULL,
  `nama` varchar(255) NOT NULL,
  `ket` varchar(255) NOT NULL,
  `tagihan` double NOT NULL,
  `bayar` double NOT NULL,
  `id_jurnal` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `nilai_balance` double NOT NULL,
  `id_jurnal_balance` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_kasbon_lain`
--

INSERT INTO `t_kasbon_lain` (`id_kasbon`, `no_kasbon`, `tgl_kasbon`, `nama`, `ket`, `tagihan`, `bayar`, `id_jurnal`, `status`, `nilai_balance`, `id_jurnal_balance`) VALUES
(1, 'BON-240001', '2024-09-22', 'AMIR', 'Urus Pajak Kendaraan', 2500000, 2500000, 5181, 1, 0, 0),
(2, 'BON-240002', '2024-09-22', 'MUSTAFA', 'VISIT CUSTOMER', 1500000, 500000, 5185, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `t_kasbon_lain_bayar`
--

CREATE TABLE `t_kasbon_lain_bayar` (
  `id_bayar` int(11) NOT NULL,
  `id_kasbon` int(11) NOT NULL,
  `id_jurnal` int(11) NOT NULL,
  `jumlah` double NOT NULL,
  `jenis` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_kasbon_lain_bayar`
--

INSERT INTO `t_kasbon_lain_bayar` (`id_bayar`, `id_kasbon`, `id_jurnal`, `jumlah`, `jenis`) VALUES
(1, 1, 5182, 10000, 0),
(2, 1, 5183, 2000000, 0),
(3, 1, 5184, 490000, 0),
(4, 2, 5186, 500000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `t_kasbon_temp`
--

CREATE TABLE `t_kasbon_temp` (
  `id_temp` int(11) NOT NULL,
  `id_kasbon` int(11) NOT NULL,
  `id_cost` int(11) NOT NULL,
  `qty` double NOT NULL,
  `unit` varchar(15) NOT NULL,
  `price` double NOT NULL,
  `id_cust` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `t_kurs`
--

CREATE TABLE `t_kurs` (
  `id_kurs` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `kurs` double NOT NULL,
  `created` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `t_quo`
--

CREATE TABLE `t_quo` (
  `id_quo` int(11) NOT NULL,
  `quo_date` date NOT NULL,
  `quo_no` varchar(20) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `id_cust` int(11) NOT NULL,
  `attn` varchar(255) NOT NULL,
  `tipe` varchar(12) NOT NULL,
  `deliv` varchar(5) NOT NULL,
  `id_pol` int(11) NOT NULL,
  `id_pod` int(11) NOT NULL,
  `header` text NOT NULL,
  `footer` text NOT NULL,
  `kurs` double NOT NULL,
  `sales` varchar(25) NOT NULL,
  `created` varchar(25) NOT NULL,
  `status` int(11) NOT NULL,
  `validity` date DEFAULT NULL,
  `jml_rem` int(11) NOT NULL,
  `quan` varchar(255) NOT NULL,
  `comm` varchar(255) NOT NULL,
  `note` text NOT NULL,
  `tampil_total` int(11) NOT NULL,
  `wm` varchar(50) NOT NULL,
  `term` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_quo`
--

INSERT INTO `t_quo` (`id_quo`, `quo_date`, `quo_no`, `subject`, `id_cust`, `attn`, `tipe`, `deliv`, `id_pol`, `id_pod`, `header`, `footer`, `kurs`, `sales`, `created`, `status`, `validity`, `jml_rem`, `quan`, `comm`, `note`, `tampil_total`, `wm`, `term`) VALUES
(1, '2024-01-08', 'QUO-2400001', 'CUSTOMS CLEARANCE IMPORT JAKARTA', 5, 'Bp. Asep', 'IMPORT', 'SEA', 405, 337, 'We are pleased to quote you the following :', 'Will be happy to assist your shipment and for further information you may need please do not hesitate to.', 0, 'sales1', 'admin', 1, '2024-01-15', 6, '1 x 20', 'GENERAL CARGO', '', 0, '', 'CIF'),
(2, '2024-02-07', 'QUO-2400002', 'TEMPORARY EXPORT', 6, 'Pak Kuba', 'EXPORT', 'SEA', 337, 347, 'We are pleased to quote you the following :', 'Will be happy to assist your shipment and for further information you may need please do not hesitate to.', 0, 'sales2', 'admin', 1, '2024-02-12', 6, '45 KG (MINIMUM)', '-', '', 1, '', 'EXW'),
(3, '2024-04-08', 'QUO-2400003', 'CIF AIR - EXPORT', 30, 'Brendon', 'EXPORT', 'AIR', 337, 407, 'We are pleased to quote you the following :', 'Will be happy to assist your shipment and for further information you may need please do not hesitate to.', 0, 'sales2', 'admin', 1, '2024-04-15', 6, '', 'TILE', '', 0, '', 'CIF'),
(4, '2024-05-08', 'QUO-2400004', 'DDU SHIPMENT 2X20HC', 9, 'PAK HENDRO', 'IMPORT', 'SEA', 339, 337, 'We are pleased to quote you the following :', 'Will be happy to assist your shipment and for further information you may need please do not hesitate to.', 0, 'sales2', 'admin', 1, '2024-05-17', 6, '2X20HC', 'FILTRATION MACHINE', '', 1, '', 'DDU'),
(5, '2024-06-03', 'QUO-2400005', 'TEMPORARY EXPORT', 47, 'Pak Kuba', 'EXPORT', 'SEA', 337, 347, 'We are pleased to quote you the following :', 'Will be happy to assist your shipment and for further information you may need please do not hesitate to.', 0, 'sales2', 'admin', 1, '2024-05-08', 6, '45 KG (MINIMUM)', '-', '', 0, '', 'EXW'),
(6, '2024-06-18', 'QUO-2400006', 'EXWORK ', 16, 'Johnson', 'IMPORT', 'SEA', 442, 337, 'We are pleased to quote you the following :', 'Will be happy to assist your shipment and for further information you may need please do not hesitate to.', 0, 'sales1', 'admin', 1, '2024-06-24', 6, '1x20', 'TEKSTIL', '', 1, '', 'EXW'),
(7, '2024-07-02', 'QUO-2400007', 'Custom Clearence ', 6, 'Johnson', 'IMPORT', 'SEA', 442, 337, 'We are pleased to quote you the following :', 'Will be happy to assist your shipment and for further information you may need please do not hesitate to.', 0, 'sales1', 'admin', 2, '2024-07-10', 6, '1x20', 'TEKSTIL', '', 0, '', 'DAP'),
(8, '2024-08-15', 'QUO-2400008', 'CUSTOMS CLEARANCE IMPORT JAKARTA', 5, 'Bp. Asep', 'IMPORT', 'SEA', 405, 337, 'We are pleased to quote you the following :', 'Will be happy to assist your shipment and for further information you may need please do not hesitate to.', 0, 'sales1', 'admin', 0, '2024-08-22', 6, '1 x 20', 'GENERAL CARGO', '', 0, '', 'CIF'),
(9, '2024-09-19', 'QUO-2400009', 'EXWORK ', 16, 'Johnson', 'IMPORT', 'SEA', 442, 337, 'We are pleased to quote you the following :', 'Will be happy to assist your shipment and for further information you may need please do not hesitate to.', 0, 'sales1', 'admin', 1, '2024-06-24', 6, '1x20', 'TEKSTIL', '', 0, '', 'EXW');

-- --------------------------------------------------------

--
-- Table structure for table `t_quo_inv`
--

CREATE TABLE `t_quo_inv` (
  `id_detil` int(11) NOT NULL,
  `id_quo` int(11) NOT NULL,
  `id_cost` int(11) NOT NULL,
  `unit` varchar(15) NOT NULL,
  `qty` double NOT NULL,
  `price` double NOT NULL,
  `cur` varchar(3) NOT NULL,
  `note` varchar(255) NOT NULL,
  `kurs` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_quo_inv`
--

INSERT INTO `t_quo_inv` (`id_detil`, `id_quo`, `id_cost`, `unit`, `qty`, `price`, `cur`, `note`, `kurs`) VALUES
(2368, 1, 426, '20FT', 1, 300000, 'IDR', '', 0),
(2369, 1, 479, '20FT', 1, 250000, 'IDR', '2ND CTNR ONWARDS	', 0),
(2370, 1, 667, '20FT', 1, 1900000, 'IDR', '', 0),
(2371, 1, 454, '20FT', 1, 150000, 'IDR', '', 0),
(2372, 1, 553, '20FT', 1, 500000, 'IDR', '', 0),
(2373, 2, 472, '20FL', 1, 2500000, 'IDR', '', 0),
(2374, 2, 422, '20FL', 1, 1000000, 'IDR', '', 0),
(2375, 2, 416, '20FL', 1, 350000, 'IDR', '', 0),
(2376, 2, 594, '20FL', 1, 300000, 'IDR', '', 0),
(2377, 2, 667, '20FL', 1, 1000000, 'IDR', '', 0),
(2378, 2, 494, '20FL', 1, 500000, 'IDR', 'AS RECEIPT	', 0),
(2379, 3, 415, '20FL', 1, 250000, 'IDR', '', 0),
(2380, 3, 614, '20FL', 1, 1500000, 'IDR', '', 0),
(2381, 3, 594, '20FL', 1, 350000, 'IDR', '', 0),
(2382, 4, 418, '20HC', 2, 1000000, 'IDR', '', 0),
(2383, 4, 421, 'Cont', 2, 500000, 'IDR', '', 0),
(2384, 4, 667, '20HC', 1, 1000000, 'IDR', '', 0),
(2385, 4, 594, 'Doc', 1, 350000, 'IDR', '', 0),
(2386, 5, 472, '20FL', 1, 2500000, 'IDR', '', 0),
(2387, 5, 422, '20FL', 1, 1000000, 'IDR', '', 0),
(2388, 5, 416, '20FL', 1, 350000, 'IDR', '', 0),
(2389, 5, 594, '20FL', 1, 300000, 'IDR', '', 0),
(2390, 5, 667, '20FL', 1, 1000000, 'IDR', '', 0),
(2391, 5, 494, '20FL', 1, 500000, 'IDR', 'AS RECEIPT	', 0),
(2392, 6, 472, 'WM', 1, 100, 'USD', '', 15500),
(2393, 6, 440, '20FL', 1, 600000, 'IDR', '', 0),
(2394, 6, 422, 'Doc', 1, 550000, 'IDR', '', 0),
(2395, 6, 416, 'Doc', 1, 150000, 'IDR', '', 0),
(2396, 6, 449, 'WM', 1, 35, 'USD', '', 15500),
(2397, 6, 478, 'WM', 1, 600000, 'IDR', '', 15500),
(2398, 6, 413, 'Doc', 1, 60, 'USD', '', 15500),
(2399, 6, 423, 'Doc', 1, 70, 'USD', '', 15500),
(2400, 7, 472, 'WM', 1, 100, 'USD', '', 0),
(2401, 7, 440, '20FL', 1, 600000, 'IDR', '', 0),
(2402, 7, 422, 'Doc', 1, 550000, 'IDR', '', 0),
(2403, 7, 416, 'Doc', 1, 150000, 'IDR', '', 0),
(2404, 7, 449, 'WM', 1, 35, 'USD', '', 0),
(2405, 7, 478, 'WM', 1, 600000, 'IDR', '', 0),
(2406, 7, 413, 'Doc', 1, 60, 'USD', '', 0),
(2407, 7, 423, 'Doc', 1, 70, 'USD', '', 0),
(2408, 8, 426, '20FT', 1, 300000, 'IDR', '', 0),
(2409, 8, 479, '20FT', 1, 250000, 'IDR', '2ND CTNR ONWARDS	', 0),
(2410, 8, 667, '20FT', 1, 1900000, 'IDR', '', 0),
(2411, 8, 454, '20FT', 1, 150000, 'IDR', '', 0),
(2412, 8, 553, '20FT', 1, 500000, 'IDR', '', 0),
(2413, 9, 472, 'WM', 1, 100, 'USD', '', 0),
(2414, 9, 440, '20FL', 1, 600000, 'IDR', '', 0),
(2415, 9, 422, 'Doc', 1, 550000, 'IDR', '', 0),
(2416, 9, 416, 'Doc', 1, 150000, 'IDR', '', 0),
(2417, 9, 449, 'WM', 1, 35, 'USD', '', 0),
(2418, 9, 478, 'WM', 1, 600000, 'IDR', '', 0),
(2419, 9, 413, 'Doc', 1, 60, 'USD', '', 0),
(2420, 9, 423, 'Doc', 1, 70, 'USD', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `t_quo_rem`
--

CREATE TABLE `t_quo_rem` (
  `id_rem` int(11) NOT NULL,
  `id_quo` int(11) NOT NULL,
  `rem` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_quo_rem`
--

INSERT INTO `t_quo_rem` (`id_rem`, `id_quo`, `rem`) VALUES
(979, 211, 'All fees and charges listed must be paid in IDR (Indonesian Rupiah) according to the Invoice date. '),
(980, 211, 'Shipping is not insured unless specifically requested by the customer. '),
(981, 211, 'Importers or exporters are required to prepare NPWP/NIB and other supporting documents to complete the order.'),
(982, 211, 'Term of Payment and penalty for late charges will be implemented according to the general Term and condition. '),
(983, 211, 'Every Cancellation shipment is required to pay a Cancellation Fee if any. '),
(984, 211, 'Payment is made 14 days from the invoice received by the customer. '),
(986, 212, 'All fees and charges listed must be paid in IDR (Indonesian Rupiah) according to the Invoice date. '),
(987, 212, 'Shipping is not insured unless specifically requested by the customer. '),
(988, 212, 'Importers or exporters are required to prepare NPWP/NIB and other supporting documents to complete the order.'),
(989, 212, 'Term of Payment and penalty for late charges will be implemented according to the general Term and condition. '),
(990, 212, 'Every Cancellation shipment is required to pay a Cancellation Fee if any. '),
(991, 212, 'Payment is made 14 days from the invoice received by the customer. '),
(992, 212, 'All prices above are valid according to the available valid date and are not binding if it has passed. '),
(993, 213, 'All fees and charges listed must be paid in IDR (Indonesian Rupiah) according to the Invoice date. '),
(994, 213, 'Shipping is not insured unless specifically requested by the customer. '),
(995, 213, 'Importers or exporters are required to prepare NPWP/NIB and other supporting documents to complete the order.'),
(996, 213, 'Term of Payment and penalty for late charges will be implemented according to the general Term and condition. '),
(997, 213, 'Every Cancellation shipment is required to pay a Cancellation Fee if any. '),
(998, 213, 'Payment is made 14 days from the invoice received by the customer. '),
(999, 213, 'All prices above are valid according to the available valid date and are not binding if it has passed. '),
(1007, 2, 'Shipping is not insured unless specifically requested by the customer. '),
(1001, 1, 'Shipping is not insured unless specifically requested by the customer. '),
(1002, 1, 'Importers or exporters are required to prepare NPWP/NIB and other supporting documents to complete the order.'),
(1003, 1, 'Term of Payment and penalty for late charges will be implemented according to the general Term and condition. '),
(1004, 1, 'Every Cancellation shipment is required to pay a Cancellation Fee if any. '),
(1005, 1, 'Payment is made 14 days from the invoice received by the customer. '),
(1006, 1, 'All prices above are valid according to the available valid date and are not binding if it has passed. '),
(1008, 2, 'Importers or exporters are required to prepare NPWP/NIB and other supporting documents to complete the order.'),
(1009, 2, 'Term of Payment and penalty for late charges will be implemented according to the general Term and condition. '),
(1010, 2, 'Every Cancellation shipment is required to pay a Cancellation Fee if any. '),
(1011, 2, 'Payment is made 14 days from the invoice received by the customer. '),
(1012, 2, 'All prices above are valid according to the available valid date and are not binding if it has passed. '),
(1013, 3, 'Shipping is not insured unless specifically requested by the customer. '),
(1014, 3, 'Importers or exporters are required to prepare NPWP/NIB and other supporting documents to complete the order.'),
(1015, 3, 'Term of Payment and penalty for late charges will be implemented according to the general Term and condition. '),
(1016, 3, 'Every Cancellation shipment is required to pay a Cancellation Fee if any. '),
(1017, 3, 'Payment is made 14 days from the invoice received by the customer. '),
(1018, 3, 'All prices above are valid according to the available valid date and are not binding if it has passed. '),
(1019, 4, 'Shipping is not insured unless specifically requested by the customer. '),
(1020, 4, 'Importers or exporters are required to prepare NPWP/NIB and other supporting documents to complete the order.'),
(1021, 4, 'Term of Payment and penalty for late charges will be implemented according to the general Term and condition. '),
(1022, 4, 'Every Cancellation shipment is required to pay a Cancellation Fee if any. '),
(1023, 4, 'Payment is made 14 days from the invoice received by the customer. '),
(1024, 4, 'All prices above are valid according to the available valid date and are not binding if it has passed. '),
(1025, 5, 'Shipping is not insured unless specifically requested by the customer. '),
(1026, 5, 'Importers or exporters are required to prepare NPWP/NIB and other supporting documents to complete the order.'),
(1027, 5, 'Term of Payment and penalty for late charges will be implemented according to the general Term and condition. '),
(1028, 5, 'Every Cancellation shipment is required to pay a Cancellation Fee if any. '),
(1029, 5, 'Payment is made 14 days from the invoice received by the customer. '),
(1030, 5, 'All prices above are valid according to the available valid date and are not binding if it has passed. '),
(1031, 6, 'Shipping is not insured unless specifically requested by the customer. '),
(1032, 6, 'Importers or exporters are required to prepare NPWP/NIB and other supporting documents to complete the order.'),
(1033, 6, 'Term of Payment and penalty for late charges will be implemented according to the general Term and condition. '),
(1034, 6, 'Every Cancellation shipment is required to pay a Cancellation Fee if any. '),
(1035, 6, 'Payment is made 14 days from the invoice received by the customer. '),
(1036, 6, 'All prices above are valid according to the available valid date and are not binding if it has passed. '),
(1037, 7, 'Shipping is not insured unless specifically requested by the customer. '),
(1038, 7, 'Importers or exporters are required to prepare NPWP/NIB and other supporting documents to complete the order.'),
(1039, 7, 'Term of Payment and penalty for late charges will be implemented according to the general Term and condition. '),
(1040, 7, 'Every Cancellation shipment is required to pay a Cancellation Fee if any. '),
(1041, 7, 'Payment is made 14 days from the invoice received by the customer. '),
(1042, 7, 'All prices above are valid according to the available valid date and are not binding if it has passed. '),
(1043, 8, 'Shipping is not insured unless specifically requested by the customer. '),
(1044, 8, 'Importers or exporters are required to prepare NPWP/NIB and other supporting documents to complete the order.'),
(1045, 8, 'Term of Payment and penalty for late charges will be implemented according to the general Term and condition. '),
(1046, 8, 'Every Cancellation shipment is required to pay a Cancellation Fee if any. '),
(1047, 8, 'Payment is made 14 days from the invoice received by the customer. '),
(1048, 8, 'All prices above are valid according to the available valid date and are not binding if it has passed. '),
(1049, 9, 'Shipping is not insured unless specifically requested by the customer. '),
(1050, 9, 'Importers or exporters are required to prepare NPWP/NIB and other supporting documents to complete the order.'),
(1051, 9, 'Term of Payment and penalty for late charges will be implemented according to the general Term and condition. '),
(1052, 9, 'Every Cancellation shipment is required to pay a Cancellation Fee if any. '),
(1053, 9, 'Payment is made 14 days from the invoice received by the customer. '),
(1054, 9, 'All prices above are valid according to the available valid date and are not binding if it has passed. ');

-- --------------------------------------------------------

--
-- Table structure for table `t_sj`
--

CREATE TABLE `t_sj` (
  `id_sj` int(11) NOT NULL,
  `jenis` varchar(3) NOT NULL,
  `no_sj` varchar(25) NOT NULL,
  `tgl_sj` date NOT NULL,
  `id_jo` int(11) NOT NULL,
  `id_cont` int(11) NOT NULL,
  `deliv_to` text NOT NULL,
  `created` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_sj`
--

INSERT INTO `t_sj` (`id_sj`, `jenis`, `no_sj`, `tgl_sj`, `id_jo`, `id_cont`, `deliv_to`, `created`) VALUES
(6, 'FCL', 'SJ-F2300001', '2023-02-08', 0, 8, 'Wisma Mitra Sunter \nJl. Yos Sudarso Boulevard Mitra Sunter\nJakarta Utara 14350', 'admin'),
(7, 'FCL', 'SJ-F2300002', '2023-02-22', 0, 11, 'Jl. Raya Bekasi Km. X \nPulo Gadung III Plumpang\nJakarta Timu', 'admin'),
(8, 'LCL', 'SJ-L2300001', '2023-02-09', 24, 0, 'Central Plaza, 5rd Floor\nJl.Jend.Sudirman Kav.X \nJakarta 12930', 'admin'),
(9, 'FCL', 'SJ-F2300003', '2023-02-22', 0, 9, 'Wisma Mitra Sunter \nJl. Yos Sudarso Boulevard Mitra Sunter\nJakarta Utara 14350', 'admin'),
(10, 'LCL', 'SJ-L2300002', '2023-02-15', 23, 0, 'Jl.Letjen Suprapto\nJakarta 10530', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `t_skdo`
--

CREATE TABLE `t_skdo` (
  `id_skdo` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `id_jo` int(11) NOT NULL,
  `id_beri` int(11) NOT NULL,
  `id_terima` int(11) NOT NULL,
  `created` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_skdo`
--

INSERT INTO `t_skdo` (`id_skdo`, `tanggal`, `id_jo`, `id_beri`, `id_terima`, `created`) VALUES
(1, '2023-02-23', 26, 34, 46, 'admin'),
(2, '2023-02-23', 17, 6, 7, 'admin'),
(3, '2023-02-23', 25, 44, 26, 'admin'),
(4, '2023-02-23', 21, 38, 42, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `t_spdo`
--

CREATE TABLE `t_spdo` (
  `id_spdo` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `id_jo` int(11) NOT NULL,
  `id_cust` int(11) NOT NULL,
  `created` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_spdo`
--

INSERT INTO `t_spdo` (`id_spdo`, `tanggal`, `id_jo`, `id_cust`, `created`) VALUES
(1, '2023-02-23', 22, 44, 'admin'),
(2, '2023-02-23', 24, 45, 'admin'),
(3, '2023-02-23', 20, 37, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `t_st`
--

CREATE TABLE `t_st` (
  `id_st` int(11) NOT NULL,
  `id_jo` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `nama` varchar(255) NOT NULL,
  `ket` text NOT NULL,
  `created` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_st`
--

INSERT INTO `t_st` (`id_st`, `id_jo`, `tanggal`, `nama`, `ket`, `created`) VALUES
(1, 9, '2023-02-01', 'ARIF', 'Untuk mengambil DO / BL, Mengurus Karantina, Kepabeanan dan Tugas kantor lainnya yang berkaitan dengan bidang usaha freight forwarding, dengan data-data.', 'admin'),
(2, 26, '2023-02-22', 'UJANG', 'gh', 'admin'),
(3, 27, '2023-02-15', 'BUDI', 'Untuk mengambil DO / BL, Mengurus Karantina, Kepabeanan dan Tugas kantor lainnya yang berkaitan dengan bidang usaha freight forwarding, dengan data-data.', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `m_audit`
--
ALTER TABLE `m_audit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_bank`
--
ALTER TABLE `m_bank`
  ADD PRIMARY KEY (`id_bank`),
  ADD UNIQUE KEY `nama_bank` (`nama_bank`,`no_bank`);

--
-- Indexes for table `m_coa`
--
ALTER TABLE `m_coa`
  ADD PRIMARY KEY (`id_coa`);

--
-- Indexes for table `m_cost`
--
ALTER TABLE `m_cost`
  ADD PRIMARY KEY (`id_cost`),
  ADD UNIQUE KEY `nama_cost` (`nama_cost`);

--
-- Indexes for table `m_cost_tr`
--
ALTER TABLE `m_cost_tr`
  ADD PRIMARY KEY (`id_cost`),
  ADD UNIQUE KEY `nama_cost` (`nama_cost`);

--
-- Indexes for table `m_cur`
--
ALTER TABLE `m_cur`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_cur_quo`
--
ALTER TABLE `m_cur_quo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_cust`
--
ALTER TABLE `m_cust`
  ADD PRIMARY KEY (`id_cust`);

--
-- Indexes for table `m_cust_jenis`
--
ALTER TABLE `m_cust_jenis`
  ADD PRIMARY KEY (`id_jenis`);

--
-- Indexes for table `m_cust_tr`
--
ALTER TABLE `m_cust_tr`
  ADD PRIMARY KEY (`id_cust`),
  ADD UNIQUE KEY `nama_cust` (`nama_cust`);

--
-- Indexes for table `m_jenis_mobil_tr`
--
ALTER TABLE `m_jenis_mobil_tr`
  ADD PRIMARY KEY (`id_jenis`);

--
-- Indexes for table `m_jenis_tagihan`
--
ALTER TABLE `m_jenis_tagihan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_kode_inv`
--
ALTER TABLE `m_kode_inv`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_kota_tr`
--
ALTER TABLE `m_kota_tr`
  ADD PRIMARY KEY (`id_kota`);

--
-- Indexes for table `m_kurs`
--
ALTER TABLE `m_kurs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_log`
--
ALTER TABLE `m_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_menu`
--
ALTER TABLE `m_menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexes for table `m_menu_tr`
--
ALTER TABLE `m_menu_tr`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexes for table `m_mobil_tr`
--
ALTER TABLE `m_mobil_tr`
  ADD PRIMARY KEY (`id_mobil`);

--
-- Indexes for table `m_opera`
--
ALTER TABLE `m_opera`
  ADD PRIMARY KEY (`id_opera`),
  ADD UNIQUE KEY `nama` (`nama`);

--
-- Indexes for table `m_paging`
--
ALTER TABLE `m_paging`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_paket`
--
ALTER TABLE `m_paket`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_paket` (`nama_paket`);

--
-- Indexes for table `m_port`
--
ALTER TABLE `m_port`
  ADD PRIMARY KEY (`id_port`),
  ADD UNIQUE KEY `nama_port` (`nama_port`);

--
-- Indexes for table `m_pt`
--
ALTER TABLE `m_pt`
  ADD PRIMARY KEY (`id_pt`);

--
-- Indexes for table `m_rate_tr`
--
ALTER TABLE `m_rate_tr`
  ADD PRIMARY KEY (`id_rate`);

--
-- Indexes for table `m_remark`
--
ALTER TABLE `m_remark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_role`
--
ALTER TABLE `m_role`
  ADD PRIMARY KEY (`id_role`);

--
-- Indexes for table `m_role_akses`
--
ALTER TABLE `m_role_akses`
  ADD PRIMARY KEY (`id_role`,`id_menu`);

--
-- Indexes for table `m_role_akses_tr`
--
ALTER TABLE `m_role_akses_tr`
  ADD PRIMARY KEY (`id_role`,`id_menu`);

--
-- Indexes for table `m_role_tr`
--
ALTER TABLE `m_role_tr`
  ADD PRIMARY KEY (`id_role`);

--
-- Indexes for table `m_supir_tr`
--
ALTER TABLE `m_supir_tr`
  ADD PRIMARY KEY (`id_supir`);

--
-- Indexes for table `m_unit`
--
ALTER TABLE `m_unit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_user`
--
ALTER TABLE `m_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_user` (`id_user`);

--
-- Indexes for table `m_user_tr`
--
ALTER TABLE `m_user_tr`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_user` (`id_user`);

--
-- Indexes for table `m_vendor_tr`
--
ALTER TABLE `m_vendor_tr`
  ADD PRIMARY KEY (`id_vendor`);

--
-- Indexes for table `t_antarbank`
--
ALTER TABLE `t_antarbank`
  ADD PRIMARY KEY (`id_antar`);

--
-- Indexes for table `t_awb_biaya`
--
ALTER TABLE `t_awb_biaya`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_dp`
--
ALTER TABLE `t_dp`
  ADD PRIMARY KEY (`id_dp`);

--
-- Indexes for table `t_dp_bayar`
--
ALTER TABLE `t_dp_bayar`
  ADD PRIMARY KEY (`id_bayar`);

--
-- Indexes for table `t_jaminan`
--
ALTER TABLE `t_jaminan`
  ADD PRIMARY KEY (`id_jaminan`);

--
-- Indexes for table `t_jaminan_bayar`
--
ALTER TABLE `t_jaminan_bayar`
  ADD PRIMARY KEY (`id_bayar`);

--
-- Indexes for table `t_jo`
--
ALTER TABLE `t_jo`
  ADD PRIMARY KEY (`id_jo`);

--
-- Indexes for table `t_jo_cont`
--
ALTER TABLE `t_jo_cont`
  ADD PRIMARY KEY (`id_cont`),
  ADD UNIQUE KEY `id_jo` (`id_jo`,`no_cont`);

--
-- Indexes for table `t_jo_marks`
--
ALTER TABLE `t_jo_marks`
  ADD PRIMARY KEY (`id_detil`);

--
-- Indexes for table `t_jo_sj_tr`
--
ALTER TABLE `t_jo_sj_tr`
  ADD PRIMARY KEY (`id_sj`);

--
-- Indexes for table `t_jo_tagihan`
--
ALTER TABLE `t_jo_tagihan`
  ADD PRIMARY KEY (`id_tagihan`);

--
-- Indexes for table `t_jo_tagihan_bayar`
--
ALTER TABLE `t_jo_tagihan_bayar`
  ADD PRIMARY KEY (`id_bayar`);

--
-- Indexes for table `t_jo_tagihan_detil`
--
ALTER TABLE `t_jo_tagihan_detil`
  ADD PRIMARY KEY (`id_detil`);

--
-- Indexes for table `t_jo_tr`
--
ALTER TABLE `t_jo_tr`
  ADD PRIMARY KEY (`id_jo`);

--
-- Indexes for table `t_jurnal`
--
ALTER TABLE `t_jurnal`
  ADD PRIMARY KEY (`id_jurnal`),
  ADD KEY `id_jurnal` (`id_jurnal`);

--
-- Indexes for table `t_jurnal_detil`
--
ALTER TABLE `t_jurnal_detil`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `t_jurnal_sum`
--
ALTER TABLE `t_jurnal_sum`
  ADD PRIMARY KEY (`bln`,`thn`,`id_coa`,`cur`);

--
-- Indexes for table `t_kasbon`
--
ALTER TABLE `t_kasbon`
  ADD PRIMARY KEY (`id_kasbon`);

--
-- Indexes for table `t_kasbon_bayar`
--
ALTER TABLE `t_kasbon_bayar`
  ADD PRIMARY KEY (`id_bayar`);

--
-- Indexes for table `t_kasbon_detil`
--
ALTER TABLE `t_kasbon_detil`
  ADD PRIMARY KEY (`id_detil`);

--
-- Indexes for table `t_kasbon_lain`
--
ALTER TABLE `t_kasbon_lain`
  ADD PRIMARY KEY (`id_kasbon`);

--
-- Indexes for table `t_kasbon_lain_bayar`
--
ALTER TABLE `t_kasbon_lain_bayar`
  ADD PRIMARY KEY (`id_bayar`);

--
-- Indexes for table `t_kasbon_temp`
--
ALTER TABLE `t_kasbon_temp`
  ADD PRIMARY KEY (`id_temp`);

--
-- Indexes for table `t_kurs`
--
ALTER TABLE `t_kurs`
  ADD PRIMARY KEY (`id_kurs`),
  ADD UNIQUE KEY `tanggal` (`tanggal`);

--
-- Indexes for table `t_quo`
--
ALTER TABLE `t_quo`
  ADD PRIMARY KEY (`id_quo`);

--
-- Indexes for table `t_quo_inv`
--
ALTER TABLE `t_quo_inv`
  ADD PRIMARY KEY (`id_detil`);

--
-- Indexes for table `t_quo_rem`
--
ALTER TABLE `t_quo_rem`
  ADD PRIMARY KEY (`id_rem`);

--
-- Indexes for table `t_sj`
--
ALTER TABLE `t_sj`
  ADD PRIMARY KEY (`id_sj`);

--
-- Indexes for table `t_skdo`
--
ALTER TABLE `t_skdo`
  ADD PRIMARY KEY (`id_skdo`);

--
-- Indexes for table `t_spdo`
--
ALTER TABLE `t_spdo`
  ADD PRIMARY KEY (`id_spdo`);

--
-- Indexes for table `t_st`
--
ALTER TABLE `t_st`
  ADD PRIMARY KEY (`id_st`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `m_audit`
--
ALTER TABLE `m_audit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `m_coa`
--
ALTER TABLE `m_coa`
  MODIFY `id_coa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1790;

--
-- AUTO_INCREMENT for table `m_cost`
--
ALTER TABLE `m_cost`
  MODIFY `id_cost` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10589;

--
-- AUTO_INCREMENT for table `m_cur`
--
ALTER TABLE `m_cur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `m_cur_quo`
--
ALTER TABLE `m_cur_quo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `m_cust`
--
ALTER TABLE `m_cust`
  MODIFY `id_cust` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `m_cust_tr`
--
ALTER TABLE `m_cust_tr`
  MODIFY `id_cust` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `m_kode_inv`
--
ALTER TABLE `m_kode_inv`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `m_kota_tr`
--
ALTER TABLE `m_kota_tr`
  MODIFY `id_kota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `m_kurs`
--
ALTER TABLE `m_kurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `m_log`
--
ALTER TABLE `m_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `m_menu`
--
ALTER TABLE `m_menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `m_menu_tr`
--
ALTER TABLE `m_menu_tr`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `m_mobil_tr`
--
ALTER TABLE `m_mobil_tr`
  MODIFY `id_mobil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `m_opera`
--
ALTER TABLE `m_opera`
  MODIFY `id_opera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `m_paging`
--
ALTER TABLE `m_paging`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `m_paket`
--
ALTER TABLE `m_paket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `m_port`
--
ALTER TABLE `m_port`
  MODIFY `id_port` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=496;

--
-- AUTO_INCREMENT for table `m_remark`
--
ALTER TABLE `m_remark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `m_role`
--
ALTER TABLE `m_role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `m_role_tr`
--
ALTER TABLE `m_role_tr`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `m_unit`
--
ALTER TABLE `m_unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `m_user`
--
ALTER TABLE `m_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;

--
-- AUTO_INCREMENT for table `m_user_tr`
--
ALTER TABLE `m_user_tr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `t_antarbank`
--
ALTER TABLE `t_antarbank`
  MODIFY `id_antar` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_awb_biaya`
--
ALTER TABLE `t_awb_biaya`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_dp`
--
ALTER TABLE `t_dp`
  MODIFY `id_dp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `t_dp_bayar`
--
ALTER TABLE `t_dp_bayar`
  MODIFY `id_bayar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `t_jaminan`
--
ALTER TABLE `t_jaminan`
  MODIFY `id_jaminan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `t_jaminan_bayar`
--
ALTER TABLE `t_jaminan_bayar`
  MODIFY `id_bayar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `t_jo`
--
ALTER TABLE `t_jo`
  MODIFY `id_jo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `t_jo_cont`
--
ALTER TABLE `t_jo_cont`
  MODIFY `id_cont` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `t_jo_marks`
--
ALTER TABLE `t_jo_marks`
  MODIFY `id_detil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `t_jo_sj_tr`
--
ALTER TABLE `t_jo_sj_tr`
  MODIFY `id_sj` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `t_jo_tagihan`
--
ALTER TABLE `t_jo_tagihan`
  MODIFY `id_tagihan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `t_jo_tagihan_bayar`
--
ALTER TABLE `t_jo_tagihan_bayar`
  MODIFY `id_bayar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `t_jo_tagihan_detil`
--
ALTER TABLE `t_jo_tagihan_detil`
  MODIFY `id_detil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `t_jo_tr`
--
ALTER TABLE `t_jo_tr`
  MODIFY `id_jo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `t_jurnal`
--
ALTER TABLE `t_jurnal`
  MODIFY `id_jurnal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5215;

--
-- AUTO_INCREMENT for table `t_jurnal_detil`
--
ALTER TABLE `t_jurnal_detil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11479;

--
-- AUTO_INCREMENT for table `t_kasbon`
--
ALTER TABLE `t_kasbon`
  MODIFY `id_kasbon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8033;

--
-- AUTO_INCREMENT for table `t_kasbon_bayar`
--
ALTER TABLE `t_kasbon_bayar`
  MODIFY `id_bayar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `t_kasbon_detil`
--
ALTER TABLE `t_kasbon_detil`
  MODIFY `id_detil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `t_kasbon_lain`
--
ALTER TABLE `t_kasbon_lain`
  MODIFY `id_kasbon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `t_kasbon_lain_bayar`
--
ALTER TABLE `t_kasbon_lain_bayar`
  MODIFY `id_bayar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `t_kasbon_temp`
--
ALTER TABLE `t_kasbon_temp`
  MODIFY `id_temp` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_kurs`
--
ALTER TABLE `t_kurs`
  MODIFY `id_kurs` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_quo`
--
ALTER TABLE `t_quo`
  MODIFY `id_quo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `t_quo_inv`
--
ALTER TABLE `t_quo_inv`
  MODIFY `id_detil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2421;

--
-- AUTO_INCREMENT for table `t_quo_rem`
--
ALTER TABLE `t_quo_rem`
  MODIFY `id_rem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1055;

--
-- AUTO_INCREMENT for table `t_sj`
--
ALTER TABLE `t_sj`
  MODIFY `id_sj` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `t_skdo`
--
ALTER TABLE `t_skdo`
  MODIFY `id_skdo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `t_spdo`
--
ALTER TABLE `t_spdo`
  MODIFY `id_spdo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `t_st`
--
ALTER TABLE `t_st`
  MODIFY `id_st` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
