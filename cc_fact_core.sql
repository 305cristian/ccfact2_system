/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 80403
 Source Host           : localhost:3306
 Source Schema         : cc_fact_core

 Target Server Type    : MySQL
 Target Server Version : 80403
 File Encoding         : 65001

 Date: 17/08/2026 17:05:15
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cc_acciones
-- ----------------------------
DROP TABLE IF EXISTS `cc_acciones`;
CREATE TABLE `cc_acciones`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `ac_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ac_estado` tinyint(0) NULL DEFAULT NULL,
  `ac_fecha_create` date NULL DEFAULT NULL,
  `fk_modulo` int(0) NULL DEFAULT NULL,
  `fk_submodulo` int(0) NULL DEFAULT NULL,
  `ac_detalle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_modulo`(`fk_modulo`) USING BTREE,
  INDEX `fk_submodulo`(`fk_submodulo`) USING BTREE,
  CONSTRAINT `cc_acciones_ibfk_1` FOREIGN KEY (`fk_modulo`) REFERENCES `cc_modulos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_acciones_ibfk_2` FOREIGN KEY (`fk_submodulo`) REFERENCES `cc_modulos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_acciones
-- ----------------------------
INSERT INTO `cc_acciones` VALUES (1, 'edit_employee', 1, '2023-12-20', 4, 4, NULL);
INSERT INTO `cc_acciones` VALUES (2, 'create_employee', 1, '2026-08-09', 4, 4, 'permite crear empleados');
INSERT INTO `cc_acciones` VALUES (3, 'edit_rol', 1, '2023-12-27', 4, 4, NULL);
INSERT INTO `cc_acciones` VALUES (4, 'control_inventario', 1, '2025-10-23', 3, 6, 'VER LOS INVENTARIOS');
INSERT INTO `cc_acciones` VALUES (5, 'admin', 1, '2024-02-14', 4, 4, NULL);
INSERT INTO `cc_acciones` VALUES (7, 'tester_te', 1, '2025-10-23', 2, 13, 'testeo en una accion ');
INSERT INTO `cc_acciones` VALUES (8, 'update_producto', 1, '2026-03-23', 3, 6, 'Sirve para actualizar los datos de un producto');
INSERT INTO `cc_acciones` VALUES (9, 'anular_transferencia', 1, '2026-03-23', 3, 9, 'SIrve para anular transferencias de bodega');
INSERT INTO `cc_acciones` VALUES (10, 'usar_iva_historico_compra', 1, '2026-08-02', 1, 1, 'Permite usar tarifas históricas de IVA al registrar compras con fecha de emisión anterior');
INSERT INTO `cc_acciones` VALUES (13, 'permitir_cambio_precio', 1, '2026-08-09', 2, 2, 'PERMITE HABILITAR SELECT DE TIPOS DE PVP');
INSERT INTO `cc_acciones` VALUES (16, 'anular_ventas', 1, '2026-08-09', 2, 2, 'sirve para anular las ventas');

-- ----------------------------
-- Table structure for cc_ajuste_entrada
-- ----------------------------
DROP TABLE IF EXISTS `cc_ajuste_entrada`;
CREATE TABLE `cc_ajuste_entrada`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa del ajuste de entrada',
  `ajen_secuencial` int(0) NULL DEFAULT NULL COMMENT 'Código o número de ajuste (ej. AJ-0001)',
  `ajen_fecha` date NULL DEFAULT NULL COMMENT 'Fecha del ajuste',
  `ajen_observaciones` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ajen_estado` tinyint(0) NULL DEFAULT NULL COMMENT '1= PENDIENTE 2=ARCHIVADO -1=ANULADO',
  `ajen_total_items` int(0) NULL DEFAULT NULL,
  `ajen_tipo` enum('AJUSTE_INICIAL','COMPRA_SIN_FACTURA','AJUSTE_NORMAL') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Si es ajuste inicial, compra sin factura',
  `ajen_fecha_anulacion` datetime(0) NULL DEFAULT NULL,
  `ajen_motivo_anulacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fk_user_anulacion` int(0) NULL DEFAULT NULL,
  `fk_motivo_ajuste` int(0) NULL DEFAULT NULL,
  `fk_bodega` int(0) NULL DEFAULT NULL,
  `fk_user_id` int(0) NULL DEFAULT NULL,
  `fk_user_id_aprueba` int(0) NULL DEFAULT NULL,
  `ajen_fecha_aprobacion` datetime(0) NULL DEFAULT NULL,
  `fk_proveedor` int(0) NULL DEFAULT NULL,
  `fk_centro_costo` int(0) NULL DEFAULT NULL,
  `codigo_sustento` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `iva_porcentaje` double(255, 2) NULL DEFAULT NULL,
  `ajen_subtotal_bienes` decimal(15, 4) NULL DEFAULT NULL,
  `ajen_subtotal_servicios` decimal(15, 4) NULL DEFAULT NULL,
  `ajen_totalcartiva` decimal(15, 4) NULL DEFAULT NULL COMMENT 'Precio total de la factura incluido IVA',
  `ajen_totaliva` decimal(15, 4) NULL DEFAULT NULL,
  `ajen_tarifacero` decimal(15, 4) NULL DEFAULT NULL,
  `ajen_tarifacero_neto` decimal(15, 4) NULL DEFAULT NULL,
  `ajen_tarifaiva` decimal(15, 2) NULL DEFAULT NULL,
  `ajen_tarifaiva_neto` decimal(15, 4) NULL DEFAULT NULL,
  `ajen_total` decimal(15, 4) NULL DEFAULT NULL COMMENT 'Este es la suma de ajen_tarifacero_neto mas ajen_tarifaiva_neto',
  `ajen_items_duplicados` enum('true','false') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ajen_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `ajen_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_user_anulacion`(`fk_user_anulacion`) USING BTREE,
  INDEX `fk_motivo_ajuste`(`fk_motivo_ajuste`) USING BTREE,
  INDEX `fk_bodega`(`fk_bodega`) USING BTREE,
  INDEX `fk_user_id`(`fk_user_id`) USING BTREE,
  INDEX `fk_proveedor`(`fk_proveedor`) USING BTREE,
  INDEX `fk_centro_costo`(`fk_centro_costo`) USING BTREE,
  INDEX `codigo_sustento`(`codigo_sustento`) USING BTREE,
  INDEX `fk_user_id_aprueba`(`fk_user_id_aprueba`) USING BTREE,
  INDEX `idx_ajen_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_ajuste_entrada_ibfk_1` FOREIGN KEY (`fk_user_anulacion`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_entrada_ibfk_2` FOREIGN KEY (`fk_motivo_ajuste`) REFERENCES `cc_motivos_ajuste` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_entrada_ibfk_3` FOREIGN KEY (`fk_bodega`) REFERENCES `cc_bodegas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_entrada_ibfk_4` FOREIGN KEY (`fk_user_id`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_entrada_ibfk_5` FOREIGN KEY (`fk_proveedor`) REFERENCES `cc_proveedores` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_entrada_ibfk_6` FOREIGN KEY (`fk_centro_costo`) REFERENCES `cc_centroscosto` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_entrada_ibfk_7` FOREIGN KEY (`codigo_sustento`) REFERENCES `cc_sustentos` (`sus_codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_entrada_ibfk_8` FOREIGN KEY (`fk_user_id_aprueba`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_ajen_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_ajuste_entrada
-- ----------------------------

-- ----------------------------
-- Table structure for cc_ajuste_entrada_det
-- ----------------------------
DROP TABLE IF EXISTS `cc_ajuste_entrada_det`;
CREATE TABLE `cc_ajuste_entrada_det`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_ajuste_entrada` int(0) NULL DEFAULT NULL,
  `fk_producto` int(0) NULL DEFAULT NULL,
  `fk_lote` int(0) NULL DEFAULT NULL,
  `ajend_itemcantidad` decimal(15, 3) NULL DEFAULT NULL,
  `ajend_itemcosto` decimal(15, 4) NULL DEFAULT NULL,
  `ajend_itemcostoxcantidad` decimal(15, 4) NULL DEFAULT NULL,
  `ajend_observacion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `ajend_estado` tinyint(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_ajuste_entrada`(`fk_ajuste_entrada`) USING BTREE,
  INDEX `fk_producto`(`fk_producto`) USING BTREE,
  INDEX `fk_lote`(`fk_lote`) USING BTREE,
  CONSTRAINT `cc_ajuste_entrada_det_ibfk_1` FOREIGN KEY (`fk_ajuste_entrada`) REFERENCES `cc_ajuste_entrada` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_entrada_det_ibfk_2` FOREIGN KEY (`fk_producto`) REFERENCES `cc_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_entrada_det_ibfk_3` FOREIGN KEY (`fk_lote`) REFERENCES `cc_lotes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_ajuste_entrada_det
-- ----------------------------

-- ----------------------------
-- Table structure for cc_ajuste_salida
-- ----------------------------
DROP TABLE IF EXISTS `cc_ajuste_salida`;
CREATE TABLE `cc_ajuste_salida`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa del ajuste de salida',
  `ajes_secuencial` int(0) NULL DEFAULT NULL COMMENT 'Código o número de ajuste (ej. AS-0001)',
  `ajes_fecha` date NULL DEFAULT NULL COMMENT 'Fecha del ajuste',
  `ajes_observaciones` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ajes_estado` tinyint(0) NULL DEFAULT NULL COMMENT '1= BORRADOR 2=ARCHIVADO -1=ANULADO',
  `ajes_total_items` int(0) NULL DEFAULT NULL,
  `ajes_fecha_anulacion` datetime(0) NULL DEFAULT NULL,
  `ajes_motivo_anulacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fk_user_anulacion` int(0) NULL DEFAULT NULL,
  `fk_motivo_ajuste` int(0) NULL DEFAULT NULL,
  `fk_bodega` int(0) NULL DEFAULT NULL,
  `fk_user_id` int(0) NULL DEFAULT NULL,
  `fk_user_id_aprueba` int(0) NULL DEFAULT NULL,
  `ajes_fecha_aprobacion` datetime(0) NULL DEFAULT NULL,
  `fk_centro_costo` int(0) NULL DEFAULT NULL,
  `iva_porcentaje` double(5, 2) NULL DEFAULT NULL,
  `ajes_subtotal_bienes` decimal(15, 4) NULL DEFAULT NULL,
  `ajes_subtotal_servicios` decimal(15, 4) NULL DEFAULT NULL,
  `ajes_totalcartiva` decimal(15, 4) NULL DEFAULT NULL,
  `ajes_totaliva` decimal(15, 4) NULL DEFAULT NULL,
  `ajes_tarifacero` decimal(15, 4) NULL DEFAULT NULL,
  `ajes_tarifacero_neto` decimal(15, 4) NULL DEFAULT NULL,
  `ajes_tarifaiva` decimal(15, 2) NULL DEFAULT NULL,
  `ajes_tarifaiva_neto` decimal(15, 4) NULL DEFAULT NULL,
  `ajes_total` decimal(15, 4) NULL DEFAULT NULL COMMENT 'Suma de ajes_tarifacero_neto + ajes_tarifaiva_neto',
  `ajes_items_duplicados` enum('true','false') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ajes_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `ajes_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `ajes_tipo` enum('AJUSTE_MERMA','CONSUMO_INTERNO','DESPACHO') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fk_servicio` int(0) NULL DEFAULT NULL,
  `fk_cliente` int(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_user_anulacion`(`fk_user_anulacion`) USING BTREE,
  INDEX `fk_motivo_ajuste`(`fk_motivo_ajuste`) USING BTREE,
  INDEX `fk_bodega`(`fk_bodega`) USING BTREE,
  INDEX `fk_user_id`(`fk_user_id`) USING BTREE,
  INDEX `fk_centro_costo`(`fk_centro_costo`) USING BTREE,
  INDEX `fk_user_id_aprueba`(`fk_user_id_aprueba`) USING BTREE,
  INDEX `fk_servicio`(`fk_servicio`) USING BTREE,
  INDEX `fk_cliente`(`fk_cliente`) USING BTREE,
  INDEX `idx_ajes_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_ajuste_salida_ibfk_1` FOREIGN KEY (`fk_user_anulacion`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_salida_ibfk_2` FOREIGN KEY (`fk_motivo_ajuste`) REFERENCES `cc_motivos_ajuste` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_salida_ibfk_3` FOREIGN KEY (`fk_bodega`) REFERENCES `cc_bodegas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_salida_ibfk_4` FOREIGN KEY (`fk_user_id`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_salida_ibfk_5` FOREIGN KEY (`fk_centro_costo`) REFERENCES `cc_centroscosto` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_salida_ibfk_6` FOREIGN KEY (`fk_user_id_aprueba`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_salida_ibfk_7` FOREIGN KEY (`fk_servicio`) REFERENCES `cc_bio_servicios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_salida_ibfk_8` FOREIGN KEY (`fk_cliente`) REFERENCES `cc_clientes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_ajes_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_ajuste_salida
-- ----------------------------

-- ----------------------------
-- Table structure for cc_ajuste_salida_det
-- ----------------------------
DROP TABLE IF EXISTS `cc_ajuste_salida_det`;
CREATE TABLE `cc_ajuste_salida_det`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_ajuste_salida` int(0) NULL DEFAULT NULL,
  `fk_producto` int(0) NULL DEFAULT NULL,
  `fk_lote` int(0) NULL DEFAULT NULL,
  `ajsd_itemcantidad` decimal(15, 3) NULL DEFAULT NULL,
  `ajsd_itemcosto` decimal(15, 4) NULL DEFAULT NULL,
  `ajsd_itemcostoxcantidad` decimal(15, 4) NULL DEFAULT NULL,
  `ajsd_observacion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `ajsd_estado` tinyint(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_ajuste_salida`(`fk_ajuste_salida`) USING BTREE,
  INDEX `fk_producto`(`fk_producto`) USING BTREE,
  INDEX `fk_lote`(`fk_lote`) USING BTREE,
  CONSTRAINT `cc_ajuste_salida_det_ibfk_1` FOREIGN KEY (`fk_ajuste_salida`) REFERENCES `cc_ajuste_salida` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_salida_det_ibfk_2` FOREIGN KEY (`fk_producto`) REFERENCES `cc_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_ajuste_salida_det_ibfk_3` FOREIGN KEY (`fk_lote`) REFERENCES `cc_lotes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_ajuste_salida_det
-- ----------------------------

-- ----------------------------
-- Table structure for cc_anillo
-- ----------------------------
DROP TABLE IF EXISTS `cc_anillo`;
CREATE TABLE `cc_anillo`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `an_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `an_descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `an_estado` tinyint(1) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_anillo
-- ----------------------------
INSERT INTO `cc_anillo` VALUES (1, 'ANILLO 1', 'AQUI VAN TODOS LOS SECTORES DEL ANILLO 1', 1);
INSERT INTO `cc_anillo` VALUES (2, 'ANILLO 2', 'AQUI VAN TODOS LOS SECTORES DEL ANILLO 2', 1);
INSERT INTO `cc_anillo` VALUES (3, 'ANILLO 3', 'AQUI VAN TODOS LOS SECTORES DEL ANILLOS 3', 1);
INSERT INTO `cc_anillo` VALUES (4, 'ANILLO 4', 'AQUI VAN TODOS LOS SECTORES DEL ANILLOS 4', 1);

-- ----------------------------
-- Table structure for cc_anticipo_proveedor
-- ----------------------------
DROP TABLE IF EXISTS `cc_anticipo_proveedor`;
CREATE TABLE `cc_anticipo_proveedor`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'ID del anticipo a proveedor',
  `antp_secuencial` int(0) NOT NULL COMMENT 'Secuencial interno del anticipo',
  `fk_proveedor` int(0) NOT NULL COMMENT 'Proveedor dueño del anticipo',
  `fk_proyecto` int(0) NOT NULL COMMENT 'Proyecto/empresa al que pertenece el anticipo',
  `antp_tipo` enum('NDC_COMPRA','PAGO_DIRECTO','AJUSTE') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Origen del anticipo: NDC, pago directo o ajuste',
  `fk_ndc` int(0) NULL DEFAULT NULL COMMENT 'FK a cc_compras cuando el anticipo nace de una nota de credito de compra',
  `antp_valor` decimal(15, 6) NOT NULL COMMENT 'Valor original del anticipo',
  `antp_saldo` decimal(15, 6) NOT NULL COMMENT 'Saldo disponible del anticipo',
  `antp_fecha` date NOT NULL COMMENT 'Fecha del anticipo',
  `antp_hora` time(0) NOT NULL COMMENT 'Hora del anticipo',
  `antp_detalle` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Detalle u observacion del anticipo',
  `antp_estado` enum('ACTIVO','APLICADO_PARCIAL','APLICADO_TOTAL','ANULADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'ACTIVO' COMMENT 'Estado del anticipo',
  `fk_user` int(0) NULL DEFAULT NULL COMMENT 'Usuario que registra',
  `fk_user_anula` int(0) NULL DEFAULT NULL COMMENT 'Usuario que anula',
  `antp_fecha_anulacion` datetime(0) NULL DEFAULT NULL COMMENT 'Fecha/hora de anulacion',
  `antp_motivo_anulacion` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Motivo de anulacion',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_antp_proveedor`(`fk_proveedor`) USING BTREE,
  INDEX `idx_antp_proyecto`(`fk_proyecto`) USING BTREE,
  INDEX `idx_antp_ndc`(`fk_ndc`) USING BTREE,
  INDEX `idx_antp_estado`(`antp_estado`) USING BTREE,
  INDEX `idx_antp_tipo`(`antp_tipo`) USING BTREE,
  CONSTRAINT `cc_anticipo_proveedor_ibfk_1` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_antp_ndc` FOREIGN KEY (`fk_ndc`) REFERENCES `cc_compras` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_antp_proveedor` FOREIGN KEY (`fk_proveedor`) REFERENCES `cc_proveedores` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'Anticipos registrados a favor de proveedores' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_anticipo_proveedor
-- ----------------------------

-- ----------------------------
-- Table structure for cc_anticipo_proveedor_aplicacion
-- ----------------------------
DROP TABLE IF EXISTS `cc_anticipo_proveedor_aplicacion`;
CREATE TABLE `cc_anticipo_proveedor_aplicacion`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'ID de aplicacion de anticipo',
  `fk_anticipo` int(0) NOT NULL COMMENT 'FK al anticipo proveedor',
  `fk_cxp` int(0) NOT NULL COMMENT 'FK a cuenta por pagar donde se aplica',
  `fk_cuota` int(0) NULL DEFAULT NULL COMMENT 'FK a cuota de CxP si aplica por cuota',
  `fk_proyecto` int(0) NOT NULL COMMENT 'Proyecto/empresa de la aplicacion',
  `apli_valor` decimal(15, 6) NOT NULL COMMENT 'Valor aplicado del anticipo',
  `apli_fecha` date NOT NULL COMMENT 'Fecha de aplicacion',
  `apli_hora` time(0) NOT NULL COMMENT 'Hora de aplicacion',
  `apli_estado` enum('ACTIVO','ANULADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'ACTIVO' COMMENT 'Estado de la aplicacion',
  `fk_user` int(0) NULL DEFAULT NULL COMMENT 'Usuario que aplica',
  `fk_user_anula` int(0) NULL DEFAULT NULL COMMENT 'Usuario que anula',
  `apli_fecha_anulacion` datetime(0) NULL DEFAULT NULL COMMENT 'Fecha/hora de anulacion',
  `apli_motivo_anulacion` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Motivo de anulacion',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_apli_anticipo`(`fk_anticipo`) USING BTREE,
  INDEX `idx_apli_cxp`(`fk_cxp`) USING BTREE,
  INDEX `idx_apli_cuota`(`fk_cuota`) USING BTREE,
  INDEX `idx_apli_proyecto`(`fk_proyecto`) USING BTREE,
  INDEX `idx_apli_estado`(`apli_estado`) USING BTREE,
  CONSTRAINT `cc_anticipo_proveedor_aplicacion_ibfk_1` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_apli_anticipo` FOREIGN KEY (`fk_anticipo`) REFERENCES `cc_anticipo_proveedor` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_apli_cuota` FOREIGN KEY (`fk_cuota`) REFERENCES `cc_cxp_cuotas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_apli_cxp` FOREIGN KEY (`fk_cxp`) REFERENCES `cc_cxp` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'Aplicaciones de anticipos de proveedor contra cuentas por pagar' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_anticipo_proveedor_aplicacion
-- ----------------------------

-- ----------------------------
-- Table structure for cc_anticipo_proveedor_saldos
-- ----------------------------
DROP TABLE IF EXISTS `cc_anticipo_proveedor_saldos`;
CREATE TABLE `cc_anticipo_proveedor_saldos`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'ID del saldo acumulado',
  `fk_proveedor` int(0) NOT NULL COMMENT 'Proveedor',
  `fk_proyecto` int(0) NOT NULL COMMENT 'Proyecto/empresa',
  `saldo` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Saldo acumulado disponible del proveedor en el proyecto',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `idx_saldo_proveedor_proyecto`(`fk_proveedor`, `fk_proyecto`) USING BTREE,
  INDEX `fk_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_anticipo_proveedor_saldos_ibfk_1` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_antps_proveedor` FOREIGN KEY (`fk_proveedor`) REFERENCES `cc_proveedores` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'Saldos acumulados de anticipos por proveedor y proyecto' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_anticipo_proveedor_saldos
-- ----------------------------

-- ----------------------------
-- Table structure for cc_asiento_contable
-- ----------------------------
DROP TABLE IF EXISTS `cc_asiento_contable`;
CREATE TABLE `cc_asiento_contable`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_periodo` int(0) NOT NULL,
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa del asiento contable',
  `ac_num_asiento` int(0) NOT NULL,
  `ac_anio` int(0) NULL DEFAULT NULL,
  `fk_mes` int(0) NULL DEFAULT NULL,
  `ac_fecha` date NULL DEFAULT NULL,
  `ac_hora` time(0) NULL DEFAULT NULL,
  `ac_estado` tinyint(0) NULL DEFAULT NULL,
  `fk_user_id` int(0) NULL DEFAULT NULL,
  `ac_codigo_transaccion` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ac_documento_id` int(0) NULL DEFAULT NULL,
  `ac_detalle` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `ac_secuencial` int(0) NULL DEFAULT NULL,
  `ac_fecha_anulacion` datetime(0) NULL DEFAULT NULL,
  `fk_user_id_anulacion` int(0) NULL DEFAULT NULL,
  `ac_motivo_anulacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_user_id`(`fk_user_id`) USING BTREE,
  INDEX `astc_codigo_transaccion`(`ac_codigo_transaccion`) USING BTREE,
  INDEX `fk_mes`(`fk_mes`) USING BTREE,
  INDEX `fk_periodo`(`fk_periodo`) USING BTREE,
  INDEX `fk_user_id_anulacion`(`fk_user_id_anulacion`) USING BTREE,
  INDEX `idx_asiento_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_asiento_contable_ibfk_1` FOREIGN KEY (`fk_user_id`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_asiento_contable_ibfk_2` FOREIGN KEY (`ac_codigo_transaccion`) REFERENCES `cc_transacciones` (`tr_codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_asiento_contable_ibfk_3` FOREIGN KEY (`fk_mes`) REFERENCES `cc_mes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_asiento_contable_ibfk_4` FOREIGN KEY (`fk_periodo`) REFERENCES `cc_periodos_contables` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_asiento_contable_ibfk_5` FOREIGN KEY (`fk_user_id_anulacion`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_asiento_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_asiento_contable
-- ----------------------------

-- ----------------------------
-- Table structure for cc_asiento_contable_det
-- ----------------------------
DROP TABLE IF EXISTS `cc_asiento_contable_det`;
CREATE TABLE `cc_asiento_contable_det`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_asiento_contable` int(0) NULL DEFAULT NULL,
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa del detalle de asiento',
  `codigo_cuenta_contable` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `acd_tipo` enum('DEBE','HABER') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `acd_valor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `acd_codigo_transaccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `acd_documento_id` int(0) NULL DEFAULT NULL,
  `acd_detalle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fk_centro_costos` int(0) NULL DEFAULT NULL,
  `acd_estado` tinyint(0) NULL DEFAULT 1,
  `acd_documento_id_pago` int(0) NULL DEFAULT NULL,
  `acd_tipo_pago` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_asiento_contable`(`fk_asiento_contable`) USING BTREE,
  INDEX `codigo_cuenta_contable`(`codigo_cuenta_contable`) USING BTREE,
  INDEX `astcd_codigo_transaccion`(`acd_codigo_transaccion`) USING BTREE,
  INDEX `fk_centro_costos`(`fk_centro_costos`) USING BTREE,
  INDEX `idx_asiento_det_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_asiento_contable_det_ibfk_1` FOREIGN KEY (`fk_asiento_contable`) REFERENCES `cc_asiento_contable` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_asiento_contable_det_ibfk_2` FOREIGN KEY (`codigo_cuenta_contable`) REFERENCES `cc_cuenta_contabledet` (`ctad_codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_asiento_contable_det_ibfk_3` FOREIGN KEY (`acd_codigo_transaccion`) REFERENCES `cc_transacciones` (`tr_codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_asiento_contable_det_ibfk_4` FOREIGN KEY (`fk_centro_costos`) REFERENCES `cc_centroscosto` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_asiento_det_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_asiento_contable_det
-- ----------------------------

-- ----------------------------
-- Table structure for cc_autocodigo
-- ----------------------------
DROP TABLE IF EXISTS `cc_autocodigo`;
CREATE TABLE `cc_autocodigo`  (
  `cod` int(0) NOT NULL,
  `abreviatura` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_autocodigo
-- ----------------------------
INSERT INTO `cc_autocodigo` VALUES (1, 'CCF-');

-- ----------------------------
-- Table structure for cc_banco_tipo_cuenta
-- ----------------------------
DROP TABLE IF EXISTS `cc_banco_tipo_cuenta`;
CREATE TABLE `cc_banco_tipo_cuenta`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `tipo_cuenta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_banco_tipo_cuenta
-- ----------------------------
INSERT INTO `cc_banco_tipo_cuenta` VALUES (1, 'AHORROS', 'Cta. Ahorros');
INSERT INTO `cc_banco_tipo_cuenta` VALUES (2, 'CORRIENTE', 'Cta. Corriente');

-- ----------------------------
-- Table structure for cc_bancos_list
-- ----------------------------
DROP TABLE IF EXISTS `cc_bancos_list`;
CREATE TABLE `cc_bancos_list`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `banc_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `banc_estado` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `banc_tipo` enum('BANCO','COOPERATIVA') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'BANCO',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 26 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_bancos_list
-- ----------------------------
INSERT INTO `cc_bancos_list` VALUES (1, 'BANCO PICHINCHA', '1', 'BANCO');
INSERT INTO `cc_bancos_list` VALUES (2, 'Banco del Pacífico', '1', 'BANCO');
INSERT INTO `cc_bancos_list` VALUES (3, 'Banco de Guayaquil', '1', 'BANCO');
INSERT INTO `cc_bancos_list` VALUES (4, 'Banco Produbanco', '1', 'BANCO');
INSERT INTO `cc_bancos_list` VALUES (5, 'Banco Internacional', '1', 'BANCO');
INSERT INTO `cc_bancos_list` VALUES (6, 'Banco Bolivariano', '1', 'BANCO');
INSERT INTO `cc_bancos_list` VALUES (7, 'BANCO DEL AUSTRO', '0', 'BANCO');
INSERT INTO `cc_bancos_list` VALUES (8, 'Banco de Loja', '1', 'BANCO');
INSERT INTO `cc_bancos_list` VALUES (9, 'Banco General Rumiñahui', '1', 'BANCO');
INSERT INTO `cc_bancos_list` VALUES (10, 'Banco Solidario', '1', 'BANCO');
INSERT INTO `cc_bancos_list` VALUES (11, 'Banco D-Miro', '1', 'BANCO');
INSERT INTO `cc_bancos_list` VALUES (12, 'Banco CoopNacional (BanCoopnacional)', '1', 'BANCO');
INSERT INTO `cc_bancos_list` VALUES (13, 'Cooperativa JEP', '1', 'COOPERATIVA');
INSERT INTO `cc_bancos_list` VALUES (14, 'Cooperativa Alianza del Valle', '1', 'COOPERATIVA');
INSERT INTO `cc_bancos_list` VALUES (15, 'Cooperativa Policía Nacional', '1', 'COOPERATIVA');
INSERT INTO `cc_bancos_list` VALUES (16, 'Cooperativa Mushuc Runa', '1', 'COOPERATIVA');
INSERT INTO `cc_bancos_list` VALUES (17, 'Cooperativa 29 de Octubre', '1', 'COOPERATIVA');
INSERT INTO `cc_bancos_list` VALUES (18, 'Cooperativa Cooprogreso', '1', 'COOPERATIVA');
INSERT INTO `cc_bancos_list` VALUES (19, 'Cooperativa Andalucía', '1', 'COOPERATIVA');
INSERT INTO `cc_bancos_list` VALUES (20, 'Cooperativa Santa Rosa', '1', 'COOPERATIVA');
INSERT INTO `cc_bancos_list` VALUES (21, 'Cooperativa Oscus', '1', 'COOPERATIVA');
INSERT INTO `cc_bancos_list` VALUES (22, 'Cooperativa San Francisco', '1', 'COOPERATIVA');
INSERT INTO `cc_bancos_list` VALUES (23, 'Cooperativa Coopmego', '1', 'COOPERATIVA');
INSERT INTO `cc_bancos_list` VALUES (24, 'Cooperativa CACPE Yantzaza', '1', 'COOPERATIVA');
INSERT INTO `cc_bancos_list` VALUES (25, 'BANCO PRODUBANCO CRIS', '0', 'BANCO');

-- ----------------------------
-- Table structure for cc_bio_areas
-- ----------------------------
DROP TABLE IF EXISTS `cc_bio_areas`;
CREATE TABLE `cc_bio_areas`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico del area',
  `fk_departamento` int(0) NULL DEFAULT NULL COMMENT 'Departamento relacionado',
  `area_codigo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Codigo interno del area',
  `area_nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Nombre del area',
  `area_descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT 'Descripcion del area',
  `area_estado` tinyint(0) NULL DEFAULT 1 COMMENT 'Estado: 1 activo, 0 inactivo',
  `area_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Fecha de creacion',
  `area_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT 'Fecha de actualizacion',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_bio_area_codigo`(`area_codigo`) USING BTREE,
  INDEX `idx_bio_area_departamento`(`fk_departamento`) USING BTREE,
  CONSTRAINT `fk_bio_area_departamento` FOREIGN KEY (`fk_departamento`) REFERENCES `cc_bio_departamentos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_bio_areas
-- ----------------------------

-- ----------------------------
-- Table structure for cc_bio_auditoria
-- ----------------------------
DROP TABLE IF EXISTS `cc_bio_auditoria`;
CREATE TABLE `cc_bio_auditoria`  (
  `id` bigint(0) NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de auditoria',
  `aud_tabla` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Tabla afectada',
  `aud_registro_id` bigint(0) NOT NULL COMMENT 'ID del registro afectado',
  `aud_accion` enum('CREATE','UPDATE','DELETE','ANULAR','PROCESAR') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Accion realizada',
  `aud_data_anterior` json NULL COMMENT 'Datos anteriores antes del cambio',
  `aud_data_nueva` json NULL COMMENT 'Datos nuevos despues del cambio',
  `fk_usuario` int(0) NULL DEFAULT NULL COMMENT 'Usuario que realizo la accion',
  `aud_fecha_hora` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Fecha y hora de la auditoria',
  `aud_ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'IP desde donde se realizo la accion',
  `aud_observacion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT 'Observacion adicional',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_bio_aud_tabla_registro`(`aud_tabla`, `aud_registro_id`) USING BTREE,
  INDEX `idx_bio_aud_fecha`(`aud_fecha_hora`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'Auditoria general de cambios del sistema biometrico' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_bio_auditoria
-- ----------------------------

-- ----------------------------
-- Table structure for cc_bio_comedores
-- ----------------------------
DROP TABLE IF EXISTS `cc_bio_comedores`;
CREATE TABLE `cc_bio_comedores`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico del comedor',
  `fk_proyecto_sistema` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa del sistema',
  `com_codigo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Codigo interno del comedor',
  `com_nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Nombre del comedor',
  `com_ubicacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Ubicacion fisica del comedor',
  `com_descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT 'Descripcion general del comedor',
  `com_estado` tinyint(0) NULL DEFAULT 1 COMMENT 'Estado del comedor: 1 activo, 0 inactivo',
  `com_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Fecha de creacion del registro',
  `com_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT 'Fecha de ultima actualizacion',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_bio_com_proyecto_codigo`(`fk_proyecto_sistema`, `com_codigo`) USING BTREE,
  INDEX `idx_bio_comedor_proyecto_sistema`(`fk_proyecto_sistema`) USING BTREE,
  CONSTRAINT `fk_bio_comedor_proyecto_sistema` FOREIGN KEY (`fk_proyecto_sistema`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'Comedores disponibles dentro de la operacion' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_bio_comedores
-- ----------------------------
INSERT INTO `cc_bio_comedores` VALUES (1, 1, 'COM-001', 'COMEDOR PRINCIPAL', 'UBICACION PRINCIPAL', 'Comedor principal de la operacion', 1, '2026-08-17 17:04:48', '2026-08-17 17:04:48');

-- ----------------------------
-- Table structure for cc_bio_comensales
-- ----------------------------
DROP TABLE IF EXISTS `cc_bio_comensales`;
CREATE TABLE `cc_bio_comensales`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico del comensal',
  `comens_codigo` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Codigo interno unico del comensal; puede usarse como forma de marcacion',
  `comens_cedula` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Cedula o identificacion del comensal',
  `comens_nombres` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Nombres del comensal',
  `comens_apellidos` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Apellidos del comensal',
  `comens_foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Ruta o nombre de archivo de la fotografia',
  `comens_identificador_biometrico` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Identificador usado por el equipo biometrico',
  `comens_uid_rfid` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'UID de tarjeta RFID o proximidad asignada al comensal',
  `fk_area` int(0) NULL DEFAULT NULL COMMENT 'Area vigente del comensal; desde el area se obtiene el departamento',
  `fk_contratista` int(0) NOT NULL COMMENT 'Contratista vigente del comensal',
  `fk_proyecto` int(0) NOT NULL COMMENT 'Proyecto vigente del comensal',
  `comens_estado` tinyint(0) NULL DEFAULT 1 COMMENT 'Estado: 1 activo, 0 inactivo',
  `comens_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Fecha de creacion del registro',
  `comens_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT 'Fecha de ultima actualizacion',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_bio_comens_codigo`(`comens_codigo`) USING BTREE,
  UNIQUE INDEX `uk_bio_comens_cedula`(`comens_cedula`) USING BTREE,
  UNIQUE INDEX `uk_bio_comens_biometrico`(`comens_identificador_biometrico`) USING BTREE,
  UNIQUE INDEX `uk_bio_comens_uid_rfid`(`comens_uid_rfid`) USING BTREE,
  INDEX `idx_bio_comens_area`(`fk_area`) USING BTREE,
  INDEX `idx_bio_comens_contratista`(`fk_contratista`) USING BTREE,
  INDEX `idx_bio_comens_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `fk_bio_comens_area` FOREIGN KEY (`fk_area`) REFERENCES `cc_bio_areas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_bio_comens_contratista` FOREIGN KEY (`fk_contratista`) REFERENCES `cc_bio_contratistas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_bio_comens_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_bio_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'Personas autorizadas para consumir alimentos' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_bio_comensales
-- ----------------------------

-- ----------------------------
-- Table structure for cc_bio_contratistas
-- ----------------------------
DROP TABLE IF EXISTS `cc_bio_contratistas`;
CREATE TABLE `cc_bio_contratistas`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de la contratista',
  `cont_ruc` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'RUC o identificacion tributaria de la contratista',
  `cont_nombre` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Nombre o razon social de la contratista',
  `cont_direccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Direccion de la contratista',
  `cont_telefono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Telefono de contacto',
  `cont_email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Correo electronico de contacto',
  `cont_estado` tinyint(0) NULL DEFAULT 1 COMMENT 'Estado: 1 activo, 0 inactivo',
  `cont_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Fecha de creacion del registro',
  `cont_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT 'Fecha de ultima actualizacion',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_bio_cont_ruc`(`cont_ruc`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'Empresas contratistas relacionadas al consumo de alimentos' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_bio_contratistas
-- ----------------------------

-- ----------------------------
-- Table structure for cc_bio_departamentos
-- ----------------------------
DROP TABLE IF EXISTS `cc_bio_departamentos`;
CREATE TABLE `cc_bio_departamentos`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico del departamento',
  `dep_codigo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Codigo interno del departamento',
  `dep_nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Nombre del departamento',
  `dep_descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT 'Descripcion del departamento',
  `dep_estado` tinyint(0) NULL DEFAULT 1 COMMENT 'Estado: 1 activo, 0 inactivo',
  `dep_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Fecha de creacion',
  `dep_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT 'Fecha de actualizacion',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_bio_dep_codigo`(`dep_codigo`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_bio_departamentos
-- ----------------------------

-- ----------------------------
-- Table structure for cc_bio_equipos
-- ----------------------------
DROP TABLE IF EXISTS `cc_bio_equipos`;
CREATE TABLE `cc_bio_equipos`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico del equipo biometrico',
  `fk_comedor` int(0) NOT NULL COMMENT 'Comedor al que pertenece el equipo',
  `eq_codigo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Codigo unico del equipo biometrico',
  `eq_nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Nombre o alias del equipo',
  `eq_marca` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Marca del equipo biometrico',
  `eq_modelo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Modelo del equipo biometrico',
  `eq_ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Direccion IP del equipo',
  `eq_puerto` int(0) NULL DEFAULT NULL COMMENT 'Puerto de comunicacion del equipo',
  `eq_ubicacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Ubicacion fisica del equipo dentro del comedor',
  `eq_estado` tinyint(0) NULL DEFAULT 1 COMMENT 'Estado del equipo: 1 activo, 0 inactivo',
  `eq_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Fecha de creacion del registro',
  `eq_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT 'Fecha de ultima actualizacion',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_bio_eq_comedor_codigo`(`fk_comedor`, `eq_codigo`) USING BTREE,
  INDEX `idx_bio_eq_comedor`(`fk_comedor`) USING BTREE,
  CONSTRAINT `fk_bio_eq_comedor` FOREIGN KEY (`fk_comedor`) REFERENCES `cc_bio_comedores` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'Equipos biometricos asociados a comedores' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_bio_equipos
-- ----------------------------
INSERT INTO `cc_bio_equipos` VALUES (1, 1, '001', 'EQUIPO PRINCIPAL', '', '', '', NULL, 'Acceso principal del comedor', 1, '2026-08-17 17:04:48', '2026-08-17 17:04:48');

-- ----------------------------
-- Table structure for cc_bio_marcaciones
-- ----------------------------
DROP TABLE IF EXISTS `cc_bio_marcaciones`;
CREATE TABLE `cc_bio_marcaciones`  (
  `id` bigint(0) NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de la marcacion',
  `fk_proyecto_sistema` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa del sistema al momento de marcar',
  `fk_comensal` int(0) NOT NULL COMMENT 'Comensal que realizo la marcacion',
  `fk_comedor` int(0) NOT NULL COMMENT 'Comedor donde se realizo la marcacion',
  `fk_equipo` int(0) NOT NULL COMMENT 'Equipo biometrico que registro la marcacion',
  `fk_servicio` int(0) NOT NULL COMMENT 'Servicio identificado automaticamente segun la hora',
  `fk_contratista` int(0) NOT NULL COMMENT 'Contratista vigente del comensal al momento de marcar',
  `fk_proyecto` int(0) NOT NULL COMMENT 'Proyecto vigente del comensal al momento de marcar',
  `marc_fecha` date NOT NULL COMMENT 'Fecha de la marcacion',
  `marc_hora` time(0) NOT NULL COMMENT 'Hora de la marcacion',
  `marc_fecha_hora` datetime(0) NOT NULL COMMENT 'Fecha y hora completa de la marcacion',
  `marc_estado` enum('VALIDA','REPETIDA','ANULADA') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'VALIDA' COMMENT 'Estado de la marcacion',
  `marc_genera_consumo` tinyint(0) NOT NULL DEFAULT 1 COMMENT 'Indica si la marcacion cuenta como consumo: 1 si cuenta, 0 si no',
  `marc_es_retraso` tinyint(0) NOT NULL DEFAULT 0 COMMENT 'Indica si la marcacion fue realizada fuera del horario normal pero dentro del horario permitido',
  `marc_origen` enum('TERMINAL','MANUAL','IMPORTACION') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'TERMINAL' COMMENT 'Origen de la marcacion',
  `marc_codigo_biometrico` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Codigo recibido desde el equipo biometrico',
  `marc_observacion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT 'Observacion o detalle adicional de la marcacion',
  `marc_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Fecha de creacion del registro',
  `marc_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT 'Fecha de ultima actualizacion',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_bio_marc_fecha`(`marc_fecha`) USING BTREE,
  INDEX `idx_bio_marc_fecha_hora`(`marc_fecha_hora`) USING BTREE,
  INDEX `idx_bio_marc_comensal`(`fk_comensal`) USING BTREE,
  INDEX `idx_bio_marc_servicio`(`fk_servicio`) USING BTREE,
  INDEX `idx_bio_marc_comedor`(`fk_comedor`) USING BTREE,
  INDEX `idx_bio_marc_equipo`(`fk_equipo`) USING BTREE,
  INDEX `idx_bio_marc_estado`(`marc_estado`) USING BTREE,
  INDEX `fk_bio_marc_contratista`(`fk_contratista`) USING BTREE,
  INDEX `fk_bio_marc_proyecto`(`fk_proyecto`) USING BTREE,
  INDEX `idx_bio_marc_filtros`(`marc_fecha`, `fk_comedor`, `fk_servicio`, `marc_estado`, `marc_es_retraso`) USING BTREE,
  INDEX `idx_bio_marc_repetida`(`fk_comensal`, `fk_servicio`, `marc_fecha`, `marc_estado`) USING BTREE,
  INDEX `idx_bio_marc_tolerancia`(`fk_comensal`, `marc_estado`, `marc_fecha_hora`) USING BTREE,
  INDEX `idx_bio_marc_proyecto_sistema`(`fk_proyecto_sistema`) USING BTREE,
  CONSTRAINT `fk_bio_marc_comedor` FOREIGN KEY (`fk_comedor`) REFERENCES `cc_bio_comedores` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_bio_marc_comensal` FOREIGN KEY (`fk_comensal`) REFERENCES `cc_bio_comensales` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_bio_marc_contratista` FOREIGN KEY (`fk_contratista`) REFERENCES `cc_bio_contratistas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_bio_marc_equipo` FOREIGN KEY (`fk_equipo`) REFERENCES `cc_bio_equipos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_bio_marc_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_bio_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_bio_marc_proyecto_sistema` FOREIGN KEY (`fk_proyecto_sistema`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_bio_marc_servicio` FOREIGN KEY (`fk_servicio`) REFERENCES `cc_bio_servicios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'Marcaciones registradas por biometrico, manualmente o por importacion' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_bio_marcaciones
-- ----------------------------

-- ----------------------------
-- Table structure for cc_bio_proyectos
-- ----------------------------
DROP TABLE IF EXISTS `cc_bio_proyectos`;
CREATE TABLE `cc_bio_proyectos`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico del proyecto',
  `proy_codigo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Codigo interno del proyecto',
  `proy_nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Nombre del proyecto operativo',
  `proy_descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT 'Descripcion del proyecto',
  `proy_estado` tinyint(0) NULL DEFAULT 1 COMMENT 'Estado: 1 activo, 0 inactivo',
  `proy_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Fecha de creacion del registro',
  `proy_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT 'Fecha de ultima actualizacion',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_bio_proy_codigo`(`proy_codigo`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'Proyectos operativos donde trabaja el personal' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_bio_proyectos
-- ----------------------------

-- ----------------------------
-- Table structure for cc_bio_servicio_horarios
-- ----------------------------
DROP TABLE IF EXISTS `cc_bio_servicio_horarios`;
CREATE TABLE `cc_bio_servicio_horarios`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico del horario',
  `fk_servicio` int(0) NOT NULL COMMENT 'Servicio alimenticio relacionado',
  `hor_hora_inicio` time(0) NOT NULL COMMENT 'Hora de inicio del rango horario',
  `hor_hora_fin` time(0) NOT NULL COMMENT 'Hora de fin del rango horario',
  `hor_hora_fin_normal` time(0) NOT NULL COMMENT 'Hora limite para considerar consumo normal; despues de esta hora cuenta como retraso',
  `hor_cruza_medianoche` tinyint(0) NULL DEFAULT 0 COMMENT 'Indica si el rango cruza medianoche: 1 si cruza, 0 si no',
  `hor_estado` tinyint(0) NULL DEFAULT 1 COMMENT 'Estado: 1 activo, 0 inactivo',
  `hor_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Fecha de creacion del registro',
  `hor_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT 'Fecha de ultima actualizacion',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_bio_hor_servicio`(`fk_servicio`) USING BTREE,
  INDEX `idx_bio_hor_rango`(`hor_hora_inicio`, `hor_hora_fin`) USING BTREE,
  CONSTRAINT `fk_bio_hor_servicio` FOREIGN KEY (`fk_servicio`) REFERENCES `cc_bio_servicios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'Rangos horarios configurables para identificar servicios automaticamente' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_bio_servicio_horarios
-- ----------------------------
INSERT INTO `cc_bio_servicio_horarios` VALUES (1, 1, '03:30:00', '10:00:00', '08:30:00', 0, 1, '2026-07-24 01:16:49', '2026-07-24 01:31:07');
INSERT INTO `cc_bio_servicio_horarios` VALUES (2, 2, '10:00:05', '16:00:00', '13:30:00', 0, 1, '2026-07-24 01:18:47', '2026-07-24 01:31:27');
INSERT INTO `cc_bio_servicio_horarios` VALUES (3, 3, '16:00:05', '21:45:00', '21:00:00', 0, 1, '2026-07-24 01:20:15', '2026-07-24 01:31:48');
INSERT INTO `cc_bio_servicio_horarios` VALUES (4, 4, '21:45:05', '23:59:50', '23:59:50', 0, 1, '2026-07-24 01:20:58', '2026-07-24 01:32:16');
INSERT INTO `cc_bio_servicio_horarios` VALUES (5, 4, '00:00:00', '03:29:00', '03:29:00', 0, 1, '2026-07-24 01:21:31', '2026-07-24 01:32:29');

-- ----------------------------
-- Table structure for cc_bio_servicios
-- ----------------------------
DROP TABLE IF EXISTS `cc_bio_servicios`;
CREATE TABLE `cc_bio_servicios`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico del servicio',
  `serv_codigo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Codigo interno del servicio',
  `serv_nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Nombre del servicio: desayuno, almuerzo, merienda, cena u otro',
  `serv_descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT 'Descripcion del servicio',
  `serv_orden` int(0) NULL DEFAULT 1 COMMENT 'Orden de visualizacion del servicio',
  `serv_color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `serv_estado` tinyint(0) NULL DEFAULT 1 COMMENT 'Estado: 1 activo, 0 inactivo',
  `serv_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Fecha de creacion del registro',
  `serv_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT 'Fecha de ultima actualizacion',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_bio_serv_codigo`(`serv_codigo`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'Servicios de alimentacion configurables' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_bio_servicios
-- ----------------------------
INSERT INTO `cc_bio_servicios` VALUES (1, 'DES', 'DESAYUNO', 'COMIDAS DEL DESAYUNO', 1, NULL, 1, '2026-07-23 22:08:53', '2026-07-24 01:05:58');
INSERT INTO `cc_bio_servicios` VALUES (2, 'ALM', 'ALMUERZO', NULL, 2, NULL, 1, '2026-07-23 22:08:53', '2026-07-23 22:08:53');
INSERT INTO `cc_bio_servicios` VALUES (3, 'MER', 'MERIENDA', NULL, 3, NULL, 1, '2026-07-23 22:08:53', '2026-07-23 22:08:53');
INSERT INTO `cc_bio_servicios` VALUES (4, 'CEN', 'CENA', NULL, 4, NULL, 1, '2026-07-23 22:08:53', '2026-07-23 22:08:53');

-- ----------------------------
-- Table structure for cc_bodegas
-- ----------------------------
DROP TABLE IF EXISTS `cc_bodegas`;
CREATE TABLE `cc_bodegas`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `bod_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `bod_descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `bod_estado` tinyint(1) NULL DEFAULT NULL,
  `bod_ctacont0` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `bod_ctacont_iva` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_bodegas
-- ----------------------------
INSERT INTO `cc_bodegas` VALUES (1, 'BODEGA PRINCIPAL', 'Bodega principal', 1, NULL, NULL);

-- ----------------------------
-- Table structure for cc_canton
-- ----------------------------
DROP TABLE IF EXISTS `cc_canton`;
CREATE TABLE `cc_canton`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `ctn_codigo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ctn_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ctn_estado` tinyint(1) NULL DEFAULT NULL,
  `fk_provincia` int(0) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_provincia`(`fk_provincia`) USING BTREE,
  CONSTRAINT `cc_canton_ibfk_1` FOREIGN KEY (`fk_provincia`) REFERENCES `cc_provincia` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 226 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_canton
-- ----------------------------
INSERT INTO `cc_canton` VALUES (1, '01', 'CUENCA', 1, 1);
INSERT INTO `cc_canton` VALUES (2, '02', 'GIRÓN', 1, 1);
INSERT INTO `cc_canton` VALUES (3, '03', 'GUALACEO', 1, 1);
INSERT INTO `cc_canton` VALUES (4, '04', 'NABÓN', 1, 1);
INSERT INTO `cc_canton` VALUES (5, '05', 'PAUTE', 1, 1);
INSERT INTO `cc_canton` VALUES (6, '06', 'PUCARA', 1, 1);
INSERT INTO `cc_canton` VALUES (7, '07', 'SAN FERNANDO', 1, 1);
INSERT INTO `cc_canton` VALUES (8, '08', 'SANTA ISABEL', 1, 1);
INSERT INTO `cc_canton` VALUES (9, '09', 'SIGSIG', 1, 1);
INSERT INTO `cc_canton` VALUES (10, '10', 'OÑA', 1, 1);
INSERT INTO `cc_canton` VALUES (11, '11', 'CHORDELEG', 1, 1);
INSERT INTO `cc_canton` VALUES (12, '12', 'EL PAN', 1, 1);
INSERT INTO `cc_canton` VALUES (13, '13', 'SEVILLA DE ORO', 1, 1);
INSERT INTO `cc_canton` VALUES (14, '14', 'GUACHAPALA', 1, 1);
INSERT INTO `cc_canton` VALUES (15, '15', 'CAMILO PONCE ENRÍQUEZ', 1, 1);
INSERT INTO `cc_canton` VALUES (16, '01', 'GUARANDA', 1, 2);
INSERT INTO `cc_canton` VALUES (17, '02', 'CHILLANES', 1, 2);
INSERT INTO `cc_canton` VALUES (18, '03', 'CHIMBO', 1, 2);
INSERT INTO `cc_canton` VALUES (19, '04', 'ECHEANDÍA', 1, 2);
INSERT INTO `cc_canton` VALUES (20, '05', 'SAN MIGUEL', 1, 2);
INSERT INTO `cc_canton` VALUES (21, '06', 'CALUMA', 1, 2);
INSERT INTO `cc_canton` VALUES (22, '07', 'LAS NAVES', 1, 2);
INSERT INTO `cc_canton` VALUES (23, '01', 'AZOGUES', 1, 3);
INSERT INTO `cc_canton` VALUES (24, '02', 'BIBLIÁN', 1, 3);
INSERT INTO `cc_canton` VALUES (25, '03', 'CAÑAR', 1, 3);
INSERT INTO `cc_canton` VALUES (26, '04', 'LA TRONCAL', 1, 3);
INSERT INTO `cc_canton` VALUES (27, '05', 'EL TAMBO', 1, 3);
INSERT INTO `cc_canton` VALUES (28, '06', 'DÉLEG', 1, 3);
INSERT INTO `cc_canton` VALUES (29, '07', 'SUSCAL', 1, 3);
INSERT INTO `cc_canton` VALUES (30, '01', 'TULCÁN', 1, 4);
INSERT INTO `cc_canton` VALUES (31, '02', 'BOLÍVAR', 1, 4);
INSERT INTO `cc_canton` VALUES (32, '03', 'ESPEJO', 1, 4);
INSERT INTO `cc_canton` VALUES (33, '04', 'MIRA', 1, 4);
INSERT INTO `cc_canton` VALUES (34, '05', 'MONTÚFAR', 1, 4);
INSERT INTO `cc_canton` VALUES (35, '06', 'SAN PEDRO DE HUACA', 1, 4);
INSERT INTO `cc_canton` VALUES (36, '01', 'LATACUNGA', 1, 5);
INSERT INTO `cc_canton` VALUES (37, '02', 'LA MANÁ', 1, 5);
INSERT INTO `cc_canton` VALUES (38, '03', 'PANGUA', 1, 5);
INSERT INTO `cc_canton` VALUES (39, '04', 'PUJILI', 1, 5);
INSERT INTO `cc_canton` VALUES (40, '05', 'SALCEDO', 1, 5);
INSERT INTO `cc_canton` VALUES (41, '06', 'SAQUISILÍ', 1, 5);
INSERT INTO `cc_canton` VALUES (42, '07', 'SIGCHOS', 1, 5);
INSERT INTO `cc_canton` VALUES (43, '01', 'RIOBAMBA', 1, 6);
INSERT INTO `cc_canton` VALUES (44, '02', 'ALAUSI', 1, 6);
INSERT INTO `cc_canton` VALUES (45, '03', 'COLTA', 1, 6);
INSERT INTO `cc_canton` VALUES (46, '04', 'CHAMBO', 1, 6);
INSERT INTO `cc_canton` VALUES (47, '05', 'CHUNCHI', 1, 6);
INSERT INTO `cc_canton` VALUES (48, '06', 'GUAMOTE', 1, 6);
INSERT INTO `cc_canton` VALUES (49, '07', 'GUANO', 1, 6);
INSERT INTO `cc_canton` VALUES (50, '08', 'PALLATANGA', 1, 6);
INSERT INTO `cc_canton` VALUES (51, '09', 'PENIPE', 1, 6);
INSERT INTO `cc_canton` VALUES (52, '10', 'CUMANDÁ', 1, 6);
INSERT INTO `cc_canton` VALUES (53, '01', 'MACHALA', 1, 7);
INSERT INTO `cc_canton` VALUES (54, '02', 'ARENILLAS', 1, 7);
INSERT INTO `cc_canton` VALUES (55, '03', 'ATAHUALPA', 1, 7);
INSERT INTO `cc_canton` VALUES (56, '04', 'BALSAS', 1, 7);
INSERT INTO `cc_canton` VALUES (57, '05', 'CHILLA', 1, 7);
INSERT INTO `cc_canton` VALUES (58, '06', 'EL GUABO', 1, 7);
INSERT INTO `cc_canton` VALUES (59, '07', 'HUAQUILLAS', 1, 7);
INSERT INTO `cc_canton` VALUES (60, '08', 'MARCABELÍ', 1, 7);
INSERT INTO `cc_canton` VALUES (61, '09', 'PASAJE', 1, 7);
INSERT INTO `cc_canton` VALUES (62, '10', 'PIÑAS', 1, 7);
INSERT INTO `cc_canton` VALUES (63, '11', 'PORTOVELO', 1, 7);
INSERT INTO `cc_canton` VALUES (64, '12', 'SANTA ROSA', 1, 7);
INSERT INTO `cc_canton` VALUES (65, '13', 'ZARUMA', 1, 7);
INSERT INTO `cc_canton` VALUES (66, '14', 'LAS LAJAS', 1, 7);
INSERT INTO `cc_canton` VALUES (67, '01', 'ESMERALDAS', 1, 8);
INSERT INTO `cc_canton` VALUES (68, '02', 'ELOY ALFARO', 1, 8);
INSERT INTO `cc_canton` VALUES (69, '03', 'MUISNE', 1, 8);
INSERT INTO `cc_canton` VALUES (70, '04', 'QUININDÉ', 1, 8);
INSERT INTO `cc_canton` VALUES (71, '05', 'SAN LORENZO', 1, 8);
INSERT INTO `cc_canton` VALUES (72, '06', 'ATACAMES', 1, 8);
INSERT INTO `cc_canton` VALUES (73, '07', 'RIOVERDE', 1, 8);
INSERT INTO `cc_canton` VALUES (74, '08', 'LA CONCORDIA', 1, 8);
INSERT INTO `cc_canton` VALUES (75, '01', 'GUAYAQUIL', 1, 9);
INSERT INTO `cc_canton` VALUES (76, '02', 'ALFREDO BAQUERIZO MORENO (JUJÁN)', 1, 9);
INSERT INTO `cc_canton` VALUES (77, '03', 'BALAO', 1, 9);
INSERT INTO `cc_canton` VALUES (78, '04', 'BALZAR', 1, 9);
INSERT INTO `cc_canton` VALUES (79, '05', 'COLIMES', 1, 9);
INSERT INTO `cc_canton` VALUES (80, '06', 'DAULE', 1, 9);
INSERT INTO `cc_canton` VALUES (81, '07', 'DURÁN', 1, 9);
INSERT INTO `cc_canton` VALUES (82, '08', 'EL EMPALME', 1, 9);
INSERT INTO `cc_canton` VALUES (83, '09', 'EL TRIUNFO', 1, 9);
INSERT INTO `cc_canton` VALUES (84, '10', 'MILAGRO', 1, 9);
INSERT INTO `cc_canton` VALUES (85, '11', 'NARANJAL', 1, 9);
INSERT INTO `cc_canton` VALUES (86, '12', 'NARANJITO', 1, 9);
INSERT INTO `cc_canton` VALUES (87, '13', 'PALESTINA', 1, 9);
INSERT INTO `cc_canton` VALUES (88, '14', 'PEDRO CARBO', 1, 9);
INSERT INTO `cc_canton` VALUES (89, '16', 'SAMBORONDÓN', 1, 9);
INSERT INTO `cc_canton` VALUES (90, '18', 'SANTA LUCÍA', 1, 9);
INSERT INTO `cc_canton` VALUES (91, '19', 'SALITRE (URBINA JADO)', 1, 9);
INSERT INTO `cc_canton` VALUES (92, '20', 'SAN JACINTO DE YAGUACHI', 1, 9);
INSERT INTO `cc_canton` VALUES (93, '21', 'PLAYAS', 1, 9);
INSERT INTO `cc_canton` VALUES (94, '22', 'SIMÓN BOLÍVAR', 1, 9);
INSERT INTO `cc_canton` VALUES (95, '23', 'CORONEL MARCELINO MARIDUEÑA', 1, 9);
INSERT INTO `cc_canton` VALUES (96, '24', 'LOMAS DE SARGENTILLO', 1, 9);
INSERT INTO `cc_canton` VALUES (97, '25', 'NOBOL', 1, 9);
INSERT INTO `cc_canton` VALUES (98, '27', 'GENERAL ANTONIO ELIZALDE', 1, 9);
INSERT INTO `cc_canton` VALUES (99, '28', 'ISIDRO AYORA', 1, 9);
INSERT INTO `cc_canton` VALUES (100, '01', 'IBARRA', 1, 10);
INSERT INTO `cc_canton` VALUES (101, '02', 'ANTONIO ANTE', 1, 10);
INSERT INTO `cc_canton` VALUES (102, '03', 'COTACACHI', 1, 10);
INSERT INTO `cc_canton` VALUES (103, '04', 'OTAVALO', 1, 10);
INSERT INTO `cc_canton` VALUES (104, '05', 'PIMAMPIRO', 1, 10);
INSERT INTO `cc_canton` VALUES (105, '06', 'SAN MIGUEL DE URCUQUÍ', 1, 10);
INSERT INTO `cc_canton` VALUES (106, '01', 'LOJA', 1, 11);
INSERT INTO `cc_canton` VALUES (107, '02', 'CALVAS', 1, 11);
INSERT INTO `cc_canton` VALUES (108, '03', 'CATAMAYO', 1, 11);
INSERT INTO `cc_canton` VALUES (109, '04', 'CELICA', 1, 11);
INSERT INTO `cc_canton` VALUES (110, '05', 'CHAGUARPAMBA', 1, 11);
INSERT INTO `cc_canton` VALUES (111, '06', 'ESPÍNDOLA', 1, 11);
INSERT INTO `cc_canton` VALUES (112, '07', 'GONZANAMÁ', 1, 11);
INSERT INTO `cc_canton` VALUES (113, '08', 'MACARÁ', 1, 11);
INSERT INTO `cc_canton` VALUES (114, '09', 'PALTAS', 1, 11);
INSERT INTO `cc_canton` VALUES (115, '10', 'PUYANGO', 1, 11);
INSERT INTO `cc_canton` VALUES (116, '11', 'SARAGURO', 1, 11);
INSERT INTO `cc_canton` VALUES (117, '12', 'SOZORANGA', 1, 11);
INSERT INTO `cc_canton` VALUES (118, '13', 'ZAPOTILLO', 1, 11);
INSERT INTO `cc_canton` VALUES (119, '14', 'PINDAL', 1, 11);
INSERT INTO `cc_canton` VALUES (120, '15', 'QUILANGA', 1, 11);
INSERT INTO `cc_canton` VALUES (121, '16', 'OLMEDO', 1, 11);
INSERT INTO `cc_canton` VALUES (122, '01', 'BABAHOYO', 1, 12);
INSERT INTO `cc_canton` VALUES (123, '02', 'BABA', 1, 12);
INSERT INTO `cc_canton` VALUES (124, '03', 'MONTALVO', 1, 12);
INSERT INTO `cc_canton` VALUES (125, '04', 'PUEBLOVIEJO', 1, 12);
INSERT INTO `cc_canton` VALUES (126, '05', 'QUEVEDO', 1, 12);
INSERT INTO `cc_canton` VALUES (127, '06', 'URDANETA', 1, 12);
INSERT INTO `cc_canton` VALUES (128, '07', 'VENTANAS', 1, 12);
INSERT INTO `cc_canton` VALUES (129, '08', 'VÍNCES', 1, 12);
INSERT INTO `cc_canton` VALUES (130, '09', 'PALENQUE', 1, 12);
INSERT INTO `cc_canton` VALUES (131, '10', 'BUENA FÉ', 1, 12);
INSERT INTO `cc_canton` VALUES (132, '11', 'VALENCIA', 1, 12);
INSERT INTO `cc_canton` VALUES (133, '12', 'MOCACHE', 1, 12);
INSERT INTO `cc_canton` VALUES (134, '13', 'QUINSALOMA', 1, 12);
INSERT INTO `cc_canton` VALUES (135, '01', 'PORTOVIEJO', 1, 13);
INSERT INTO `cc_canton` VALUES (136, '02', 'BOLÍVAR', 1, 13);
INSERT INTO `cc_canton` VALUES (137, '03', 'CHONE', 1, 13);
INSERT INTO `cc_canton` VALUES (138, '04', 'EL CARMEN', 1, 13);
INSERT INTO `cc_canton` VALUES (139, '05', 'FLAVIO ALFARO', 1, 13);
INSERT INTO `cc_canton` VALUES (140, '06', 'JIPIJAPA', 1, 13);
INSERT INTO `cc_canton` VALUES (141, '07', 'JUNÍN', 1, 13);
INSERT INTO `cc_canton` VALUES (142, '08', 'MANTA', 1, 13);
INSERT INTO `cc_canton` VALUES (143, '09', 'MONTECRISTI', 1, 13);
INSERT INTO `cc_canton` VALUES (144, '10', 'PAJÁN', 1, 13);
INSERT INTO `cc_canton` VALUES (145, '11', 'PICHINCHA', 1, 13);
INSERT INTO `cc_canton` VALUES (146, '12', 'ROCAFUERTE', 1, 13);
INSERT INTO `cc_canton` VALUES (147, '13', 'SANTA ANA', 1, 13);
INSERT INTO `cc_canton` VALUES (148, '14', 'SUCRE', 1, 13);
INSERT INTO `cc_canton` VALUES (149, '15', 'TOSAGUA', 1, 13);
INSERT INTO `cc_canton` VALUES (150, '16', '24 DE MAYO', 1, 13);
INSERT INTO `cc_canton` VALUES (151, '17', 'PEDERNALES', 1, 13);
INSERT INTO `cc_canton` VALUES (152, '18', 'OLMEDO', 1, 13);
INSERT INTO `cc_canton` VALUES (153, '19', 'PUERTO LÓPEZ', 1, 13);
INSERT INTO `cc_canton` VALUES (154, '20', 'JAMA', 1, 13);
INSERT INTO `cc_canton` VALUES (155, '21', 'JARAMIJÓ', 1, 13);
INSERT INTO `cc_canton` VALUES (156, '22', 'SAN VICENTE', 1, 13);
INSERT INTO `cc_canton` VALUES (157, '01', 'MORONA', 1, 14);
INSERT INTO `cc_canton` VALUES (158, '02', 'GUALAQUIZA', 1, 14);
INSERT INTO `cc_canton` VALUES (159, '03', 'LIMÓN INDANZA', 1, 14);
INSERT INTO `cc_canton` VALUES (160, '04', 'PALORA', 1, 14);
INSERT INTO `cc_canton` VALUES (161, '05', 'SANTIAGO', 1, 14);
INSERT INTO `cc_canton` VALUES (162, '06', 'SUCÚA', 1, 14);
INSERT INTO `cc_canton` VALUES (163, '07', 'HUAMBOYA', 1, 14);
INSERT INTO `cc_canton` VALUES (164, '08', 'SAN JUAN BOSCO', 1, 14);
INSERT INTO `cc_canton` VALUES (165, '09', 'TAISHA', 1, 14);
INSERT INTO `cc_canton` VALUES (166, '10', 'LOGROÑO', 1, 14);
INSERT INTO `cc_canton` VALUES (167, '11', 'PABLO SEXTO', 1, 14);
INSERT INTO `cc_canton` VALUES (168, '12', 'TIWINTZA', 1, 14);
INSERT INTO `cc_canton` VALUES (169, '01', 'TENA', 1, 15);
INSERT INTO `cc_canton` VALUES (170, '03', 'ARCHIDONA', 1, 15);
INSERT INTO `cc_canton` VALUES (171, '04', 'EL CHACO', 1, 15);
INSERT INTO `cc_canton` VALUES (172, '07', 'QUIJOS', 1, 15);
INSERT INTO `cc_canton` VALUES (173, '09', 'CARLOS JULIO AROSEMENA TOLA', 1, 15);
INSERT INTO `cc_canton` VALUES (174, '01', 'PASTAZA', 1, 16);
INSERT INTO `cc_canton` VALUES (175, '02', 'MERA', 1, 16);
INSERT INTO `cc_canton` VALUES (176, '03', 'SANTA CLARA', 1, 16);
INSERT INTO `cc_canton` VALUES (177, '04', 'ARAJUNO', 1, 16);
INSERT INTO `cc_canton` VALUES (178, '01', 'QUITO', 1, 17);
INSERT INTO `cc_canton` VALUES (179, '02', 'CAYAMBE', 1, 17);
INSERT INTO `cc_canton` VALUES (180, '03', 'MEJIA', 1, 17);
INSERT INTO `cc_canton` VALUES (181, '04', 'PEDRO MONCAYO', 1, 17);
INSERT INTO `cc_canton` VALUES (182, '05', 'RUMIÑAHUI', 1, 17);
INSERT INTO `cc_canton` VALUES (183, '07', 'SAN MIGUEL DE LOS BANCOS', 1, 17);
INSERT INTO `cc_canton` VALUES (184, '08', 'PEDRO VICENTE MALDONADO', 1, 17);
INSERT INTO `cc_canton` VALUES (185, '09', 'PUERTO QUITO', 1, 17);
INSERT INTO `cc_canton` VALUES (186, '01', 'AMBATO', 1, 18);
INSERT INTO `cc_canton` VALUES (187, '02', 'BAÑOS DE AGUA SANTA', 1, 18);
INSERT INTO `cc_canton` VALUES (188, '03', 'CEVALLOS', 1, 18);
INSERT INTO `cc_canton` VALUES (189, '04', 'MOCHA', 1, 18);
INSERT INTO `cc_canton` VALUES (190, '05', 'PATATE', 1, 18);
INSERT INTO `cc_canton` VALUES (191, '06', 'QUERO', 1, 18);
INSERT INTO `cc_canton` VALUES (192, '07', 'SAN PEDRO DE PELILEO', 1, 18);
INSERT INTO `cc_canton` VALUES (193, '08', 'SANTIAGO DE PÍLLARO', 1, 18);
INSERT INTO `cc_canton` VALUES (194, '09', 'TISALEO', 1, 18);
INSERT INTO `cc_canton` VALUES (195, '01', 'ZAMORA', 1, 19);
INSERT INTO `cc_canton` VALUES (196, '02', 'CHINCHIPE', 1, 19);
INSERT INTO `cc_canton` VALUES (197, '03', 'NANGARITZA', 1, 19);
INSERT INTO `cc_canton` VALUES (198, '04', 'YACUAMBI', 1, 19);
INSERT INTO `cc_canton` VALUES (199, '05', 'YANTZAZA (YANZATZA)', 1, 19);
INSERT INTO `cc_canton` VALUES (200, '06', 'EL PANGUI', 1, 19);
INSERT INTO `cc_canton` VALUES (201, '07', 'CENTINELA DEL CÓNDOR', 1, 19);
INSERT INTO `cc_canton` VALUES (202, '08', 'PALANDA', 1, 19);
INSERT INTO `cc_canton` VALUES (203, '09', 'PAQUISHA', 1, 19);
INSERT INTO `cc_canton` VALUES (204, '01', 'SAN CRISTÓBAL', 1, 20);
INSERT INTO `cc_canton` VALUES (205, '02', 'ISABELA', 1, 20);
INSERT INTO `cc_canton` VALUES (206, '03', 'SANTA CRUZ', 1, 20);
INSERT INTO `cc_canton` VALUES (207, '01', 'LAGO AGRIO', 1, 21);
INSERT INTO `cc_canton` VALUES (208, '02', 'GONZALO PIZARRO', 1, 21);
INSERT INTO `cc_canton` VALUES (209, '03', 'PUTUMAYO', 1, 21);
INSERT INTO `cc_canton` VALUES (210, '04', 'SHUSHUFINDI', 1, 21);
INSERT INTO `cc_canton` VALUES (211, '05', 'SUCUMBÍOS', 1, 21);
INSERT INTO `cc_canton` VALUES (212, '06', 'CASCALES', 1, 21);
INSERT INTO `cc_canton` VALUES (213, '07', 'CUYABENO', 1, 21);
INSERT INTO `cc_canton` VALUES (214, '01', 'ORELLANA', 1, 22);
INSERT INTO `cc_canton` VALUES (215, '02', 'AGUARICO', 1, 22);
INSERT INTO `cc_canton` VALUES (216, '03', 'LA JOYA DE LOS SACHAS', 1, 22);
INSERT INTO `cc_canton` VALUES (217, '04', 'LORETO', 1, 22);
INSERT INTO `cc_canton` VALUES (218, '01', 'SANTO DOMINGO', 1, 23);
INSERT INTO `cc_canton` VALUES (219, '01', 'SANTA ELENA', 1, 24);
INSERT INTO `cc_canton` VALUES (220, '02', 'LA LIBERTAD', 1, 24);
INSERT INTO `cc_canton` VALUES (221, '03', 'SALINAS', 1, 24);
INSERT INTO `cc_canton` VALUES (222, '01', 'LAS GOLONDRINAS', 1, 25);
INSERT INTO `cc_canton` VALUES (223, '03', 'MANGA DEL CURA', 1, 25);
INSERT INTO `cc_canton` VALUES (225, '04', 'EL PIEDRERO', 1, 25);

-- ----------------------------
-- Table structure for cc_cargo
-- ----------------------------
DROP TABLE IF EXISTS `cc_cargo`;
CREATE TABLE `cc_cargo`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `carg_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `carg_estado` tinyint(1) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_cargo
-- ----------------------------
INSERT INTO `cc_cargo` VALUES (1, 'ADMINISTRADOR', 1);
INSERT INTO `cc_cargo` VALUES (2, 'BODEGUERO', 1);
INSERT INTO `cc_cargo` VALUES (3, 'COMPRAS', 1);
INSERT INTO `cc_cargo` VALUES (4, 'CONTADOR', 1);
INSERT INTO `cc_cargo` VALUES (5, 'DIGITADOR', 1);
INSERT INTO `cc_cargo` VALUES (6, 'GERENTE', 1);
INSERT INTO `cc_cargo` VALUES (7, 'GERENTE IT', 1);

-- ----------------------------
-- Table structure for cc_centroscosto
-- ----------------------------
DROP TABLE IF EXISTS `cc_centroscosto`;
CREATE TABLE `cc_centroscosto`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `cc_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cc_descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `cc_fecha_creacion` date NOT NULL,
  `cc_estado` tinyint(0) NOT NULL,
  `cc_facturacion_elect` tinyint(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_centroscosto
-- ----------------------------
INSERT INTO `cc_centroscosto` VALUES (1, 'GENERAL', 'Centro de costo general', '2026-08-17', 1, 1);

-- ----------------------------
-- Table structure for cc_clientes
-- ----------------------------
DROP TABLE IF EXISTS `cc_clientes`;
CREATE TABLE `cc_clientes`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `clie_nombres` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `clie_apellidos` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `fk_tipo_documento` int(0) NULL DEFAULT NULL COMMENT 'CEDULA,RUC,PASAPORTE',
  `clie_dni` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'Puede ser cédula, ruc o pasaporte',
  `clie_razon_social` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `clie_telefono` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `clie_celular` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `clie_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `clie_direccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `fk_parroquia` int(0) NULL DEFAULT NULL,
  `clie_estado` tinyint(0) NULL DEFAULT NULL,
  `clie_fecha_creacion` datetime(0) NULL DEFAULT NULL,
  `clie_dias_credito` int(0) NULL DEFAULT NULL,
  `clie_usuario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `clie_clave` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `clie_genero` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `fk_tipo_sujeto` int(0) NULL DEFAULT NULL COMMENT 'NATURAL, JURIDICA, EXTRANGERA',
  `fk_proyecto` int(0) NULL DEFAULT NULL,
  `clie_sexo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `clie_fecha_actualizacion` datetime(0) NULL DEFAULT NULL,
  `clie_cupo_credito` decimal(65, 4) NULL DEFAULT NULL,
  `clie_comentarios` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `clie_cuenta_gastos` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_tipo_documento`(`fk_tipo_documento`) USING BTREE,
  INDEX `fk_parroquia`(`fk_parroquia`) USING BTREE,
  INDEX `fk_tipo_sujeto`(`fk_tipo_sujeto`) USING BTREE,
  CONSTRAINT `cc_clientes_ibfk_1` FOREIGN KEY (`fk_tipo_documento`) REFERENCES `cc_tipo_documento` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_clientes_ibfk_2` FOREIGN KEY (`fk_parroquia`) REFERENCES `cc_parroquia` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_clientes_ibfk_3` FOREIGN KEY (`fk_tipo_sujeto`) REFERENCES `cc_tipo_sujetos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_clientes
-- ----------------------------
INSERT INTO `cc_clientes` VALUES (1, 'CLIENTE', 'PRUEBA', 1, '9999999999999', 'CLIENTE PRUEBA', '', '', '', '', NULL, 1, '2026-08-17 16:50:24', 0, NULL, NULL, NULL, 2, 1, NULL, '2026-08-17 16:50:24', 0.0000, 'Cliente base de prueba', NULL);

-- ----------------------------
-- Table structure for cc_cobros
-- ----------------------------
DROP TABLE IF EXISTS `cc_cobros`;
CREATE TABLE `cc_cobros`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'Cobro realizado a cliente',
  `fk_cliente` int(0) NOT NULL COMMENT 'Cliente que paga',
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa',
  `cob_numero_secuencial` int(0) NOT NULL COMMENT 'Secuencial interno cobro',
  `cob_fecha` date NOT NULL COMMENT 'Fecha cobro',
  `fk_forma_pago` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Forma de pago',
  `fk_cuenta_contable` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Cuenta contable caja/banco',
  `fk_banco` int(0) NULL DEFAULT NULL COMMENT 'Banco si aplica',
  `cob_referencia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Referencia cobro',
  `cob_numero_transferencia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Numero o referencia de transferencia',
  `cob_fecha_transferencia` date NULL DEFAULT NULL COMMENT 'Fecha de transferencia',
  `cob_numero_cheque` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Numero de cheque',
  `cob_fecha_cheque` date NULL DEFAULT NULL COMMENT 'Fecha del cheque',
  `cob_marca_tarjeta` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Marca de tarjeta utilizada',
  `cob_lote_tarjeta` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Lote de voucher de tarjeta',
  `cob_autorizacion_tarjeta` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Codigo de autorizacion de tarjeta',
  `cob_ultimos_digitos` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Ultimos 4 digitos de la tarjeta',
  `cob_fecha_voucher` date NULL DEFAULT NULL COMMENT 'Fecha del voucher de tarjeta',
  `cob_valor` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Valor total cobrado',
  `cob_valor_recibido` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Valor recibido del cliente, aplica principalmente en cobros de contado/efectivo',
  `cob_cambio` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Cambio entregado al cliente cuando el valor recibido supera el valor cobrado',
  `cob_estado` enum('ACTIVO','ANULADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'ACTIVO',
  `cob_observacion` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Observacion cobro',
  `fk_user` int(0) NULL DEFAULT NULL COMMENT 'Usuario registra',
  `cob_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `cob_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_cob_cliente`(`fk_cliente`) USING BTREE,
  INDEX `idx_cob_proyecto`(`fk_proyecto`) USING BTREE,
  INDEX `fk_cob_user`(`fk_user`) USING BTREE,
  INDEX `idx_cob_banco`(`fk_banco`) USING BTREE,
  CONSTRAINT `fk_cob_banco` FOREIGN KEY (`fk_banco`) REFERENCES `cc_bancos_list` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_cob_cliente` FOREIGN KEY (`fk_cliente`) REFERENCES `cc_clientes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_cob_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_cob_user` FOREIGN KEY (`fk_user`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_cobros
-- ----------------------------

-- ----------------------------
-- Table structure for cc_cobros_det
-- ----------------------------
DROP TABLE IF EXISTS `cc_cobros_det`;
CREATE TABLE `cc_cobros_det`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'Detalle aplicacion de cobro',
  `fk_cobro` int(0) NOT NULL COMMENT 'Cobro cabecera',
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa',
  `fk_cxc` int(0) NOT NULL COMMENT 'Cuenta por cobrar aplicada',
  `fk_cuota` int(0) NULL DEFAULT NULL COMMENT 'Cuota aplicada si existe',
  `cobd_valor` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Valor aplicado',
  `cobd_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_cobd_cobro`(`fk_cobro`) USING BTREE,
  INDEX `idx_cobd_cxc`(`fk_cxc`) USING BTREE,
  INDEX `idx_cobd_proyecto`(`fk_proyecto`) USING BTREE,
  INDEX `fk_cobd_cuota`(`fk_cuota`) USING BTREE,
  CONSTRAINT `fk_cobd_cobro` FOREIGN KEY (`fk_cobro`) REFERENCES `cc_cobros` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_cobd_cuota` FOREIGN KEY (`fk_cuota`) REFERENCES `cc_cxc_cuotas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_cobd_cxc` FOREIGN KEY (`fk_cxc`) REFERENCES `cc_cxc` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_cobd_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_cobros_det
-- ----------------------------

-- ----------------------------
-- Table structure for cc_compras
-- ----------------------------
DROP TABLE IF EXISTS `cc_compras`;
CREATE TABLE `cc_compras`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'ID de la compra',
  `comp_secuencial` int(0) NULL DEFAULT NULL,
  `fk_proveedor` int(0) NOT NULL COMMENT 'FK del proveedor',
  `comp_numero_comprobante` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Numero de factura / comprobante',
  `comp_numero_establecimiento` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `comp_numero_emision` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `comp_autorizacion_sri` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Numero de autorizacion SRI',
  `comp_fecha_vencimiento_autorizacion` date NULL DEFAULT NULL COMMENT 'Fecha de vencimiento en compras a credito',
  `comp_tipo_comprobante_cod` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'Codigo tipo comprobante SRI (FACT, NDC, LIQ, etc)',
  `comp_fecha_emision` date NOT NULL COMMENT 'Fecha de emision del comprobante',
  `comp_fecha_registro` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Fecha de registro en sistema',
  `fk_bodega` int(0) NULL DEFAULT NULL COMMENT 'FK de la bodega',
  `fk_centro_costo` int(0) NULL DEFAULT NULL COMMENT 'FK del centro de costo',
  `fk_tipo_compra` int(0) NULL DEFAULT NULL COMMENT '1 Inventario, 2 Gasto, 3 Activo Fijo',
  `cod_sustento` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Codigo de sustento tributario SRI',
  `comp_es_gasto` tinyint(0) NULL DEFAULT 0 COMMENT '1 si es gasto',
  `comp_es_activo_fijo` tinyint(0) NULL DEFAULT 0 COMMENT '1 si es activo fijo',
  `comp_subtotal_bruto` decimal(15, 6) NULL DEFAULT NULL COMMENT 'Valor total de la factura sin descuentos',
  `comp_descuento_items` decimal(15, 6) NOT NULL DEFAULT 0.000000,
  `comp_descuento_global` decimal(15, 6) NOT NULL DEFAULT 0.000000,
  `comp_descuento_valor` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Valor total de descuento aplicado',
  `comp_subtotal_neto` decimal(15, 6) NULL DEFAULT NULL COMMENT 'Valor total despues de aplicar descuentos e impuestos',
  `comp_totaliva` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Total IVA',
  `comp_totalice` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Total ICE',
  `comp_recargo` decimal(15, 6) NOT NULL DEFAULT 0.000000,
  `comp_servicios_adicionales` decimal(15, 6) NOT NULL DEFAULT 0.000000,
  `comp_total` decimal(15, 6) NOT NULL COMMENT 'Total final de la compra',
  `comp_tarifacero_bruto` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Base tarifa 0%',
  `comp_tarifacero_neto` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Base tarifa 0% neto',
  `comp_tarifaiva_bruto` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Base tarifa IVA',
  `comp_tarifaiva_neto` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Base tarifa IVA neto',
  `comp_subtotal_bienes_bruto` decimal(15, 6) NULL DEFAULT NULL COMMENT 'Subtotal bienes antes de descuentos',
  `comp_subtotal_bienes_neto` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Subtotal bienes despues de descuentos',
  `comp_subtotal_servicios_bruto` decimal(15, 6) NULL DEFAULT NULL COMMENT 'Subtotal servicios antes de descuentos',
  `comp_subtotal_servicios_neto` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Subtotal servicios despues de descuentos',
  `comp_base_iva` decimal(15, 6) NULL DEFAULT NULL COMMENT 'Valor base del cual se sacara el iva',
  `comp_aplica_retencion` tinyint(0) NULL DEFAULT 1 COMMENT '1 si aplica retencion',
  `fk_retencion` int(0) NULL DEFAULT NULL COMMENT 'FK a tabla cc_retencion',
  `comp_asume_retencion` enum('NO_ASUMIR','ASUMIR_RENTA','ASUMIR_IVA_RENTA') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Si la empresa asume o no la retencion',
  `cod_forma_pago` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'FK a tabla cc_formas_pago (efectivo, transferencia, cheque)',
  `comp_tipo_pago` enum('CREDITO','CONTADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'CONTADO o CREDITO',
  `comp_dias_credito` int(0) NULL DEFAULT NULL COMMENT 'Numero de dias de credito',
  `comp_num_cuotas` int(0) NULL DEFAULT NULL COMMENT 'Numero de cuotas en caso de credito',
  `comp_items_duplicados` enum('true','false') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'false' COMMENT 'Indica si existen items duplicados en el detalle',
  `comp_estado` enum('BORRADOR','ARCHIVADO','ANULADA','ANULADA_EN_PENDIENTE','ANULADA_EN_ARCHIVADA') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fk_orden_compra` int(0) NULL DEFAULT NULL COMMENT 'Orden de compra asociada',
  `fk_user` int(0) NULL DEFAULT NULL COMMENT 'Usuario que registra',
  `comp_observacion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT 'Observaciones generales',
  `tipo_costo` enum('DIRECTOS','INDIRECTOS') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'DIRECTOS' COMMENT 'DIRECTOS=>COMPRAS NORMALES',
  `fk_compra_relacionada` int(0) NULL DEFAULT NULL COMMENT 'Compra original en caso de nota de credito',
  `comp_tipo_nota_credito` enum('DEVOLUCION','DESCUENTO') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `comp_pago_residente` enum('RESIDENTE','NO_RESIDENTE') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'SI el proveedor que paga es recidente o no del pais',
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa del sistema',
  `comp_fecha_anulacion` datetime(0) NULL DEFAULT NULL,
  `comp_motivo_anulacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fk_user_anulacion` int(0) NULL DEFAULT NULL,
  `comp_fecha_archivada` datetime(0) NULL DEFAULT NULL,
  `comp_total_excento_impuestos` decimal(14, 6) NULL DEFAULT NULL,
  `comp_total_no_objeto_impuestos` decimal(14, 6) NULL DEFAULT NULL,
  `comp_totalirbpnr` decimal(14, 6) NULL DEFAULT NULL,
  `comp_autorizado_sri` tinyint(0) NULL DEFAULT NULL COMMENT '1=> autorizado 0=no autorizado',
  `comp_mensaje_sri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `comp_codigo_factura_electronica` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `comp_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `comp_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_comp_proveedor`(`fk_proveedor`) USING BTREE,
  INDEX `idx_comp_fecha`(`comp_fecha_emision`) USING BTREE,
  INDEX `idx_comp_estado`(`comp_estado`) USING BTREE,
  INDEX `fk_bodega`(`fk_bodega`) USING BTREE,
  INDEX `fk_centro_costo`(`fk_centro_costo`) USING BTREE,
  INDEX `fk_tipo_compra`(`fk_tipo_compra`) USING BTREE,
  INDEX `fk_retencion`(`fk_retencion`) USING BTREE,
  INDEX `cod_forma_pago`(`cod_forma_pago`) USING BTREE,
  INDEX `fk_user`(`fk_user`) USING BTREE,
  INDEX `idx_comp_relacionada`(`fk_compra_relacionada`) USING BTREE,
  INDEX `cod_sustento`(`cod_sustento`) USING BTREE,
  INDEX `idx_compra_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_compras_ibfk_1` FOREIGN KEY (`fk_proveedor`) REFERENCES `cc_proveedores` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_compras_ibfk_2` FOREIGN KEY (`fk_bodega`) REFERENCES `cc_bodegas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_compras_ibfk_3` FOREIGN KEY (`fk_centro_costo`) REFERENCES `cc_centroscosto` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_compras_ibfk_4` FOREIGN KEY (`fk_tipo_compra`) REFERENCES `cc_tipo_compra` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_compras_ibfk_6` FOREIGN KEY (`fk_retencion`) REFERENCES `cc_retencion` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_compras_ibfk_7` FOREIGN KEY (`cod_forma_pago`) REFERENCES `cc_formas_pago` (`cod`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_compras_ibfk_8` FOREIGN KEY (`fk_user`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_compras_ibfk_9` FOREIGN KEY (`cod_sustento`) REFERENCES `cc_sustentos` (`sus_codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_compra_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_compra_relacionada` FOREIGN KEY (`fk_compra_relacionada`) REFERENCES `cc_compras` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_compras
-- ----------------------------

-- ----------------------------
-- Table structure for cc_compras_ats_formas_pago
-- ----------------------------
DROP TABLE IF EXISTS `cc_compras_ats_formas_pago`;
CREATE TABLE `cc_compras_ats_formas_pago`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_compra` int(0) NULL DEFAULT NULL,
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa de la forma ATS',
  `fk_forma_pago_ats` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_compra`(`fk_compra`) USING BTREE,
  INDEX `fk_forma_pago_ats`(`fk_forma_pago_ats`) USING BTREE,
  INDEX `idx_compra_ats_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_compras_ats_formas_pago_ibfk_1` FOREIGN KEY (`fk_compra`) REFERENCES `cc_compras` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cc_compras_ats_formas_pago_ibfk_2` FOREIGN KEY (`fk_forma_pago_ats`) REFERENCES `cc_formas_pago_sri` (`codigo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_compra_ats_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_compras_ats_formas_pago
-- ----------------------------

-- ----------------------------
-- Table structure for cc_compras_bases_impuesto
-- ----------------------------
DROP TABLE IF EXISTS `cc_compras_bases_impuesto`;
CREATE TABLE `cc_compras_bases_impuesto`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_compra` int(0) NOT NULL,
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa de la base de impuesto',
  `fk_impuesto_tarifa` int(0) NULL DEFAULT NULL,
  `imp_codigo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `imp_detalle` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `imp_porcentaje` decimal(10, 4) NOT NULL DEFAULT 0.0000,
  `subtotal_bruto` decimal(14, 6) NOT NULL DEFAULT 0.000000,
  `subtotal_neto` decimal(14, 6) NOT NULL DEFAULT 0.000000,
  `iva_valor` decimal(14, 6) NOT NULL DEFAULT 0.000000,
  `tipo_impuesto` enum('IVA','ICE','IRBPNR') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'IVA',
  `fecha_registro` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_compra`(`fk_compra`) USING BTREE,
  INDEX `idx_impuesto_tarifa`(`fk_impuesto_tarifa`) USING BTREE,
  INDEX `idx_compra_base_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_compras_bases_impuesto_ibfk_1` FOREIGN KEY (`fk_impuesto_tarifa`) REFERENCES `cc_impuesto_tarifa` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_cc_compra_base_impuesto` FOREIGN KEY (`fk_compra`) REFERENCES `cc_compras` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_compra_base_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_compras_bases_impuesto
-- ----------------------------

-- ----------------------------
-- Table structure for cc_compras_det
-- ----------------------------
DROP TABLE IF EXISTS `cc_compras_det`;
CREATE TABLE `cc_compras_det`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'ID del detalle de compra',
  `fk_compra` int(0) NOT NULL COMMENT 'FK a cc_compras',
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa del detalle de compra',
  `fk_producto` int(0) NOT NULL COMMENT 'FK del producto',
  `fk_bodega` int(0) NOT NULL COMMENT 'FK de la bodega',
  `compd_cantidad` decimal(15, 6) NOT NULL COMMENT 'Cantidad del producto',
  `compd_precio_bruto` decimal(15, 6) NOT NULL COMMENT 'Precio unitario sin descuentos (price)',
  `compd_descuento_valor` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Descuento directo del item',
  `compd_descuento_porcentaje` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Descuento % del item',
  `compd_precio_neto` decimal(15, 6) NOT NULL COMMENT 'Precio unitario despues ya restado los descuentos  (priceneto)',
  `compd_total_neto` decimal(15, 6) NOT NULL COMMENT 'Cantidad * precio neto (sin impuestos)  (totalpriceneto)',
  `compd_ice_porcentaje` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Porcentaje ICE (icePorcent)',
  `compd_ice_valor` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Valor ICE initario del producto (iceval)',
  `compd_total_ice_valor` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'ICE del producto * cantidad (toticeval)',
  `compd_precio_con_ice` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Precio unitario neto + ICE (priceice)',
  `compd_total_precio_con_ice` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Precio neto con ICE * cantidad  (totalpriceice)',
  `fk_impuesto_tarifa` int(0) NULL DEFAULT NULL COMMENT 'FK a cc_impuesto_tarifa',
  `compd_impt_codigo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Codigo SRI del impuesto (4,5,0)',
  `compd_valor_iva` decimal(15, 6) NULL DEFAULT NULL COMMENT 'Valor total IVA ejm: 110 * 15% = 16.5',
  `compd_impt_porcentaje` decimal(15, 6) NULL DEFAULT NULL COMMENT 'Porcentaje IVA (15,5,0) (ivaPorcent)',
  `compd_iva_valor` decimal(15, 6) NULL DEFAULT NULL COMMENT 'Valor IVA por unidad del producto (ivaval)',
  `compd_total_iva_valor` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'IVA total del item (iva del producto * cantidad  (totivaval))',
  `compd_precio_con_iva` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Precio unitario neto + iva (priceiva)',
  `compd_total_precio_con_iva` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Precio neto con IVA * cantidad  (totalpriceiva)',
  `compd_base_iva` decimal(15, 6) NULL DEFAULT NULL COMMENT 'Base imponible para IVA ejm: precio neto + ice = 110 (itembaseiva)',
  `compd_total_base_iva` decimal(15, 6) NULL DEFAULT NULL COMMENT 'Base imponible para IVA ejm:  110 * cantidad (totitembaseiva)',
  `compd_irbpnr` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Valor impuesto IRBPNR',
  `compd_irbpnr_total` decimal(15, 6) NULL DEFAULT 0.000000 COMMENT 'Valor IRBPNR total del ítem',
  `compd_total` decimal(15, 6) NOT NULL COMMENT 'Total final del item: compd_total_neto + iva + ice + irbpnr (total)',
  `compd_cta_entrada` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'Cuenta contable del producto',
  `compd_cod_sustento` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Codigo de sustento tributario',
  `compd_centro_costo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Centro de costo',
  `fk_lote` int(0) NULL DEFAULT NULL,
  `compd_lote` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Numero de lote',
  `compd_fecha_caducidad` date NULL DEFAULT NULL COMMENT 'Fecha de caducidad',
  `compd_fecha_elaboracion` date NULL DEFAULT NULL COMMENT 'Fecha de elaboracion',
  `compd_estado` tinyint(0) NULL DEFAULT 1 COMMENT '1 ACTIVO, 0 ANULADO',
  `compd_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `compd_impuesto_seleccionado` int(0) NULL DEFAULT NULL COMMENT 'si es iva15, iva5, excento, no objeta o iva 0',
  `compd_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `fk_compra_det_relacionada` int(0) NULL DEFAULT NULL COMMENT 'Detalle original en caso de nota de credito',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_compd_compra`(`fk_compra`) USING BTREE,
  INDEX `idx_compd_producto`(`fk_producto`) USING BTREE,
  INDEX `fk_bodega`(`fk_bodega`) USING BTREE,
  INDEX `fk_lote`(`fk_lote`) USING BTREE,
  INDEX `idx_compd_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_compras_det_ibfk_1` FOREIGN KEY (`fk_lote`) REFERENCES `cc_lotes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_bodega` FOREIGN KEY (`fk_bodega`) REFERENCES `cc_bodegas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_compd_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_compra` FOREIGN KEY (`fk_compra`) REFERENCES `cc_compras` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_producto` FOREIGN KEY (`fk_producto`) REFERENCES `cc_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_compras_det
-- ----------------------------

-- ----------------------------
-- Table structure for cc_cuenta_contable
-- ----------------------------
DROP TABLE IF EXISTS `cc_cuenta_contable`;
CREATE TABLE `cc_cuenta_contable`  (
  `cta_codigo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cta_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`cta_codigo`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_cuenta_contable
-- ----------------------------
INSERT INTO `cc_cuenta_contable` VALUES ('1', 'ACTIVOS');
INSERT INTO `cc_cuenta_contable` VALUES ('2', 'PASIVOS');
INSERT INTO `cc_cuenta_contable` VALUES ('3', 'PATRIMONIO');
INSERT INTO `cc_cuenta_contable` VALUES ('4', 'SECCION INGRESOS');
INSERT INTO `cc_cuenta_contable` VALUES ('5', 'SECCION COSTOS Y GASTOS');
INSERT INTO `cc_cuenta_contable` VALUES ('6', 'CUENTAS DE CONTROL');

-- ----------------------------
-- Table structure for cc_cuenta_contabledet
-- ----------------------------
DROP TABLE IF EXISTS `cc_cuenta_contabledet`;
CREATE TABLE `cc_cuenta_contabledet`  (
  `ctad_codigo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ctad_nombre_cuenta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ctad_cuenta_padre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `fk_cta_contable` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ctad_estado` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`ctad_codigo`) USING BTREE,
  INDEX `ctad_fk_cta_contable`(`fk_cta_contable`) USING BTREE,
  CONSTRAINT `cc_cuenta_contabledet_ibfk_1` FOREIGN KEY (`fk_cta_contable`) REFERENCES `cc_cuenta_contable` (`cta_codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_cuenta_contabledet
-- ----------------------------
INSERT INTO `cc_cuenta_contabledet` VALUES ('1. ', 'ACTIVO', '', '1', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01', 'ACTIVO CIRCULANTE', '1. ', '1', '1', '2025-10-19 14:16:44', '2025-10-23 13:57:43');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.01', 'CAJA', '1.01', '1', '1', '2025-10-19 14:16:44', '2025-10-23 13:57:20');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.01.02', 'CAJA CHICA DE PRUEBA', '1.01.01', '1', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.02', 'BANCOS', '1.01', '1', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.03', 'CUENTAS POR COBRAR', '1.01', '1', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.03.01', 'CUENTAS POR COBRAR CLIENTES', '1.01.03', '1', '1', '2026-08-16 13:06:45', '2026-08-16 13:55:05');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.03.02', 'CUENTAS POR COBRAR EMPLEADOS', '1.01.03', '1', '1', '2026-08-16 13:55:45', '2026-08-16 13:55:45');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.04', 'INVENTARIOS', '1.01', '1', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.04.01', 'INVENTARIOS DE MATERIA PRIMA', '1.01.04', '1', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.04.01.01', 'INVENTARIO DE MATERIA PRIMA 0%', '1.01.04.01', '1', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.04.01.02', 'INVENTARIO DE MATERIA PRIMA 15%', '1.01.04.01', '1', '1', '2025-10-19 14:16:44', '2025-11-11 00:06:51');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.04.01.03', 'INVENTARIO DE MATERIA PRIMA IVA 5%', '1.01.04.01', '1', '1', '2026-07-10 09:57:47', '2026-07-10 09:57:47');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.04.01.04', 'INVENTARIO DE MATERIA PRIMA IVA 13%', '1.01.04.01', '1', '1', '2026-08-02 13:09:46', '2026-08-02 13:09:46');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.04.02', 'INVENT.PRODC.TERM. Y MERC. EN ALMACEN COMPRADO A TERCEROS', '1.01.04.01', '1', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.05', 'CAJA CHICA', '1.01', '1', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.06', 'ANTICIPOS POR IMPUESTOS CORRIENTES', '1.01', '1', '1', '2026-07-05 17:18:00', '2026-07-05 17:18:00');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.06.01', 'CREDITO TRIBUTARIO A FAVOR DE LA EMPRESA (IVA)', '1.01.06', '1', '1', '2026-07-05 17:19:20', '2026-07-05 17:19:20');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.06.01.01', 'CREDITO TRIBUTARIO IVA', '1.01.06.01', '1', '1', '2026-07-05 17:20:13', '2026-07-05 17:20:13');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.06.01.02', 'IVA COMPRAS 15% BIENES Y SERVICIOS', '1.01.06.01', '1', '1', '2026-07-05 17:20:46', '2026-07-05 17:20:46');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.06.01.03', 'IVA COMPRAS 5% BIENES Y SERVICIOS', '1.01.06.01', '1', '1', '2026-07-05 17:21:21', '2026-07-05 17:21:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.06.01.04', 'IVA COMPRAS 13% BIENES Y SERVICIOS', '1.01.06.01', '1', '1', '2026-08-02 13:10:49', '2026-08-02 13:10:49');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.07', 'SERVICIOS Y OTROS PAGOS ANTICIPADOS', '1.01', '1', '1', '2026-07-06 08:02:26', '2026-07-06 08:02:26');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.07.01', 'SEGUROS Y PAGOS POR ANTICIPADOS', '1.01.07', '1', '1', '2026-07-06 08:02:57', '2026-07-06 08:02:57');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.01.07.02', 'ANTICIPOS A PROVEEDORES', '1.01.07', '1', '1', '2026-07-06 08:03:45', '2026-07-06 08:03:45');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.02', 'ACTIVO FIJO', '1.', '1', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.02.01', 'PROPIEDADES, PLANTA Y EQUIPO', '1.02', '1', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.02.02', 'DEPRECIACIÓN ACUMULADA', '1.02', '1', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.03', 'OTROS ACTIVOS', '1.', '1', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('1.03.01', 'ACTIVOS INTANGIBLES', '1.03', '1', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2. ', 'PASIVO', '', '2', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01', 'PASIVO CIRCULANTE', '2.', '2', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.01', 'CUENTAS POR PAGAR', '2.01', '2', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.01.01', 'CUENTAS Y DOCUMENTOS POR PAGAR LOCALES', '2.01.01', '2', '1', '2026-07-06 09:01:34', '2026-07-06 09:01:34');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.01.01.01', 'CUENTAS Y DCTOS. X PAGAR PROVEEDORES NO RELAC. LOCALES', '2.01.01.01', '2', '1', '2026-07-06 09:02:01', '2026-07-06 09:02:01');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.02', 'IMPUESTOS POR PAGAR', '2.01', '2', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.06', 'IMPUESTOS POR PAGAR', '2.01', '2', '1', '2026-07-05 17:45:50', '2026-07-05 17:45:50');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07', 'OBLIGACIONES CORRIENTES', '2.01', '2', '1', '2026-07-05 17:46:13', '2026-07-05 17:46:13');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01', 'OBLIGACIONES CON LA ADMINISTRACION TRIBUTARIA', '2.01.07', '2', '1', '2026-07-05 17:46:45', '2026-07-05 17:46:45');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01.01', 'RETENCION 1% IR POR PAGAR', '2.01.07.01', '2', '1', '2026-07-05 17:48:49', '2026-07-05 17:48:49');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01.02', 'RETENCION 2.75% IR POR PAGAR', '2.01.07.01', '2', '1', '2026-07-05 17:48:49', '2026-07-05 17:48:49');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01.03', 'RETENCION 8% IR POR PAGAR', '2.01.07.01', '2', '1', '2026-07-05 17:48:49', '2026-07-05 17:48:49');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01.04', 'RETENCION 10% IR POR PAGAR', '2.01.07.01', '2', '1', '2026-07-05 17:48:49', '2026-07-05 17:48:49');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01.05', 'RETENCION 30% IVA COMPRAS BIENES POR PAGAR', '2.01.07.01', '2', '1', '2026-07-05 17:48:49', '2026-07-05 17:48:49');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01.06', 'RETENCION 70% IVA PRESTACION DE SERVICIO POR PAGAR', '2.01.07.01', '2', '1', '2026-07-05 17:48:49', '2026-07-05 17:48:49');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01.07', 'RETENCION 100% IVA POR PAGAR', '2.01.07.01', '2', '1', '2026-07-05 17:48:49', '2026-07-05 17:48:49');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01.12', 'RETENCIONES DE IVA POR PAGAR AL SRI', '2.01.07.01', '2', '1', '2026-07-05 17:48:49', '2026-07-05 17:48:49');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01.13', 'RETENCIONES DE FUENTE POR PAGAR AL SRI', '2.01.07.01', '2', '1', '2026-07-05 17:48:49', '2026-07-05 17:48:49');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01.14', 'RETENCION 1,75% IR POR PAGAR', '2.01.07.01', '2', '1', '2026-07-05 17:48:49', '2026-07-05 17:48:49');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01.15', 'RETENCION 2.75% IR POR PAGAR', '2.01.07.01', '2', '1', '2026-07-05 17:48:49', '2026-07-05 17:48:49');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01.17', 'RETENCION 20% IVA PRESTACION DE SERVICIO POR PAGAR', '2.01.07.01', '2', '1', '2026-07-05 17:48:49', '2026-07-05 17:48:49');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01.18', 'RETENCION 10% IVA COMPRAS BIENES POR PAGAR', '2.01.07.01', '2', '1', '2026-07-05 17:48:49', '2026-07-05 17:48:49');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01.19', 'RETENCIÓN 50% IVA SERVICIOS POR PAGAR', '2.01.07.01', '2', '1', '2026-07-05 18:19:45', '2026-07-05 18:19:45');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01.20', 'RETENCION 3.00% IR POR PAGAR', '2.01.07.01', '2', '1', '2026-07-05 17:48:49', '2026-07-05 17:48:49');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01.21', 'IVA EN VENTAS 15% POR PAGAR (GENERAL)', '2.01.07.01', '2', '1', '2026-08-16 14:02:20', '2026-08-16 14:03:33');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.01.22', 'IVA EN VENTAS POR PAGAR 5% (ESPECIAL)', '2.01.07.01', '2', '1', '2026-08-16 14:02:42', '2026-08-16 14:04:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.07', 'RETENCIÓN LEY DE SOLIDARIDAD', '2.01.07', '2', '1', '2026-07-05 17:48:49', '2026-07-05 17:48:49');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.01.07.07.01', 'RETENCIÓN DEL 3.33% A LA REMUNERACIÓN POR PAGAR', '2.01.07.07', '2', '1', '2026-07-05 17:48:49', '2026-07-05 17:48:49');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.02', 'PASIVO A LARGO PLAZO', '2.', '2', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('2.02.01', 'PRÉSTAMOS A LARGO PLAZO', '2.02', '2', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('3.', 'PATRIMONIO NETO', '', '3', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('3.01', 'CAPITAL SOCIAL', '3.', '3', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('3.02', 'UTILIDADES ACUMULADAS', '3.', '3', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('4.', 'INGRESOS', '', '4', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('4.01', 'INGRESOS DE ACTIVIDADES ORDINARIAS', '4.', '4', '1', '2025-10-19 14:16:44', '2026-07-26 11:20:24');
INSERT INTO `cc_cuenta_contabledet` VALUES ('4.01.01', 'VENTAS', '4.01', '4', '1', '2026-08-16 14:37:58', '2026-08-16 14:37:58');
INSERT INTO `cc_cuenta_contabledet` VALUES ('4.01.01.01', 'INGRESOS POR VENTAS GENERALES', '4.01.01', '4', '1', '2026-08-16 14:38:24', '2026-08-16 14:38:24');
INSERT INTO `cc_cuenta_contabledet` VALUES ('4.01.01.02', 'VENTAS DE PRODUCTOS IVA 15%', '4.01.01', '4', '1', '2026-08-16 14:51:01', '2026-08-16 14:51:01');
INSERT INTO `cc_cuenta_contabledet` VALUES ('4.01.01.03', 'VENTAS DE PRODUCTOS TARIFA 0%', '4.01.01', '4', '1', '2026-08-16 14:51:01', '2026-08-16 14:51:01');
INSERT INTO `cc_cuenta_contabledet` VALUES ('4.01.01.04', 'VENTAS DE SERVICIOS IVA 15%', '4.01.01', '4', '1', '2026-08-16 14:51:01', '2026-08-16 14:51:01');
INSERT INTO `cc_cuenta_contabledet` VALUES ('4.01.01.05', 'VENTAS DE ACTIVOS FIJOS IVA 15%', '4.01.01', '4', '1', '2026-08-16 14:51:01', '2026-08-16 14:51:01');
INSERT INTO `cc_cuenta_contabledet` VALUES ('4.01.05', 'DESCUENTO EN COMPRAS', '4.01', '4', '1', '2026-07-26 11:21:11', '2026-07-26 11:21:11');
INSERT INTO `cc_cuenta_contabledet` VALUES ('4.01.05.01', ' DESCUENTO EN COMPRAS 0%', '4.01.05', '4', '1', '2026-07-26 11:21:50', '2026-07-26 11:21:50');
INSERT INTO `cc_cuenta_contabledet` VALUES ('4.01.05.02', 'DESCUENTO EN COMPRAS 15%', '4.01.05', '4', '1', '2026-07-26 11:22:14', '2026-07-26 11:22:14');
INSERT INTO `cc_cuenta_contabledet` VALUES ('4.01.05.03', 'DESCUENTO EN COMPRAS 5%', '4.01.05', '4', '1', '2026-07-26 11:22:38', '2026-07-26 11:22:38');
INSERT INTO `cc_cuenta_contabledet` VALUES ('4.01.05.04', 'DESCUENTO EN COMPRAS IVA 13%', 'undefined', '4', '1', '2026-08-02 13:08:33', '2026-08-02 13:08:33');
INSERT INTO `cc_cuenta_contabledet` VALUES ('4.02', 'OTROS INGRESOS OPERATIVOS', '4.', '4', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5. ', 'COSTOS Y GASTOS', '', '5', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.01', 'COSTO DE VENTAS', '5.', '5', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.01.01', 'COSTO DE VENTA GENERAL', '5.01', '5', '1', '2026-08-16 14:10:18', '2026-08-16 14:10:18');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02', 'GASTOS OPERATIVOS', '5.', '5', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.01', 'GASTOS DE ADMINISTRACIÓN', '5.02', '5', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02', 'GASTOS EN GENERAL', '5.02', '5', '1', '2025-10-19 14:16:44', '2026-07-05 16:08:31');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.12.04', 'GASTO DE ENERGIA', '5.02.02.12', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.12.05', 'GASTO DE INTERNET', '5.02.02.12', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.12.06', 'GASTO DIRECTV', '5.02.02.12', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13', 'IMPUESTOS, CONTRIBUCIONES Y OTROS', '5.02.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.01', 'ICE', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.02', 'PATENTE MUNICIPAL', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.03', 'PAGOS EN NOTARIAS', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.04', 'PAGOS EN REGISTROS DE LA PROPIEDAD', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.05', 'MATRICULACIÓN DE VEHÍCULOS', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.06', 'TRAMITES EN EL BANCO CENTRAL DEL ECUADOR', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.07', 'CERTIFICACION INEN', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.08', 'PAGO DE PREDIOS', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.09', 'SERVICIOS ADMINISTRATIVOS MUNICIPALES', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.10', 'IMPUESTOS MUNICIPALES', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.11', 'IMPUESTO POR RODAJE', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.12', 'IMPUESTO POR TRANSF DOMINIO DE VEHICULO', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.13', 'CONTRIBUCION A LA SUPERCIAS', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.14', 'CONTRIBUCION A SOLCA', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.15', 'PERMISO DE FUNCIONAMIENTO DEL CUERPO DE BOMBEROS', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.16', 'PERMISO FUNC AGENCIA NAC REGU CONTROL Y VIGILANCIA SANITARIA', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.17', 'PERMISO DE CONSTRUCCION', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.18', 'PERMISO FUNC PARA ESTABLECIMIENTOS DE SALUD', '5.02.02.13', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.13.19', 'IRBPNR', '5.02.02.13', '5', '1', '2026-07-05 17:25:12', '2026-07-05 17:25:12');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14', 'DEPRECIACIONES', '5.02.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.01', 'DEPRECIACIÓN ACUM. EDIFICIOS', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.02', 'DEPRECIACIÓN ACUM. CONSTRUCCIONES EN CURSO', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.03', 'DEPRECIACIÓN ACUM. INSTALACIONES', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.04', 'DEPRECIACIÓN ACUM. MUEBLES Y ENSERES', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.05', 'DEPRECIACIÓN ACUM. MAQUINARIA Y EQUIPO', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.06', 'DEPRECIACIÓN ACUM. NAVES, AREONAVES, BARCAZAS Y SIM.', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.07', 'DEPRECIACIÓN ACUM. EQUIPO DE COMPUTACIÓN', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.08', 'DEPRECIACIÓN ACUM. VEH, EQ. DE TRANSP. Y EQ. CAMI. MOVIL', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.09', 'DEPRECIACIÓN ACUM. OTRAS PROPIEDADES, PLANTA Y EQUIPO', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.10', 'DEPRECIACIÓN ACUM. REPUESTOS Y HERRAMIENTAS', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.11', 'DEPRECIACIÓN ACUM. EQUIPO DE ALARMA Y SEGURIDAD', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.12', 'DEPRECIACIÓN ACUM. MENAJE DE COCINA MAYOR', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.13', 'DEPRECIACIÓN ACUM. MENAJE DE COCINA MENOR', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.14', 'DEPRECIACIÓN ACUM. EQUIPOS E IMPLEMENTOS DE LABORATORIO', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.15', 'DEPRECIACIÓN ACUM. EQUIPO DE AIRE ACONDICIONADO', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.16', 'DEPRECIACIÓN ACUM. EQUIPOS DE OFICINA', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.17', 'DEPRECIACIÓN ACUM. DE EQUIPOS DE CÁMARAS DE FRIÓ', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.14.18', 'DEPRECIACIÓN ACUM. EQUIPOS DE LIMPIEZA', '5.02.02.14', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.15.', 'AMORTIZACIONES', '5.02.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.15.01', 'AMORTIACIONES INTANGIBLES', '5.02.02.15', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16', 'PAGOS POR OTROS SERVICIOS', '5.02.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.01', 'SERVICIOS LEGALES', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.03', 'SERVICIOS DE GUARDIANÍA', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.04', 'SERVICIOS POR ALIMENTACIÓN Y REFRIGERIOS AL PERSONAL - SOCIOS', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.05', 'DIETAS', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.06', 'SERVICIOS DE SISTEMA SOFIA', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.07', 'SERVICIOS POR ALQUILER DE MENAJE DE SALON', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.08', 'SERVICIOS POR ALQUILER DE VEHICULOS', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.09', 'SERVICIOS POR ANALISIS DE ALIMENTOS', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.10', 'SERVICIOS OCASIONALES POR PERSONAS NATURALES', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.11', 'SERVICIOS POR ANALISIS AL PERSONAL', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.12', 'SERVICIOS POR ALQUILER DE TRAJES TIPICOS', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.13', 'SERVICIOS POR RECARGA DE GAS INDUSTRIAL', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.14', 'SERVICIOS POR ADECUACION', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.15', 'SERVICIOS DE LAVANDERIA Y LIMPIEZA', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.16', 'SERVICIOS POR PROCESO DE CALIFICACION DE ADQUISICIONES', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.17', 'SERVICIOS POR INTERNET', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.18', 'SERVICIOS JUDICIALES Y NOTARIALES', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.19', 'SERVICIOS POR REGISTROS DE LA PROPIEDAD', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.20', 'SERVICIOS POR TELEFONIA CELULAR', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.21', 'SERVICIOS POR ASESORÍA TRIBUTARIA, CONTABLE, LEGAL', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.22', 'SERVICIOS POR IMPRENTA Y REPRODUCCIÓN', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.23', 'SERVICIOS POR DERECHOS INTELECTUALES', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.16.24', 'SERVICIOS POR AUDITORIA DE CERTIFICACION BPM', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.17.', 'IVA QUE SE CARGA AL COSTO', '5.02.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.17.01', 'IVA QUE SE CARGA AL COSTO', '5.02.02.17', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.17.02', 'IVA QUE SE CARGA AL GASTO', '5.02.02.17', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.18.', 'SERVICIOS CONTABLES', '5.02.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.02.18.01', 'ASESORIA Y SERVICIOS CONTABLES', '5.02.02.18', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.', 'GASTOS FINANCIEROS', '5.2', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.01', 'GASTO EN INTERESES', '5.02.03', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.01.01', 'GASTO EN INTERESES BANCARIOS BCO PICHINCHA', '5.02.03.01', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.01.02', 'INTERESES POR SOBREGIRO', '5.02.03.01', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.01.03', 'GASTO EN INTERESES BANCARIOS BAN ECUADOR', '5.02.03.01', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.01.04', 'GASTO EN INTERESES BANCARIOS BCO GUAYAQUIL', '5.02.03.01', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.01.05', 'GASTO EN INTERESES BANCARIOS BCO LOJA', '5.02.03.01', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.01.06', 'GASTO EN INTERESES POR SEGURO DE VEHICULOS', '5.02.03.01', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.01.07', 'INTERESES MORA POR SOBREGIRO', '5.02.03.01', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.01.08', 'INTERESES MORA POR SOLCA', '5.02.03.01', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.02', 'GASTO EN COMISIONES BANCARIAS', '5.02.03', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.02.01', 'GASTO EN COMISIONES BANCARIAS', '5.02.03.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.02.02', 'SEGUROS PAGADOS', '5.02.03.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.02.03', 'INTERESES PAGADOS', '5.02.03.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.02.04', 'GASTOS FINANCIEROS', '5.02.03.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.02.05', 'SERVICIOS BANCARIOS', '5.02.03.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.03', 'GASTO POR MULTAS POR PROTESTO DE CHEQUES', '5.02.03', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.03.03.01', 'MULTA POR PROTESTO DE CHEQUES', '5.02.03.03', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04', 'OTROS GASTOS', '5.2', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.01', 'GASTO EN PROVISIONES LOCALES', '5.02.04', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.01.01', 'GASTO PROVISIONES PARA JUBILACIÓN PATRONAL', '5.02.04.01', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.01.02', 'GASTO PROVISIONES PARA DESAHUCIO', '5.02.04.01', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.01.03', 'GASTO PROVISIONES PARA CUENTAS INCOBRABLES', '5.02.04.01', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.02', 'OTRAS PÉRDIDAS EN GASTOS', '5.02.04', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.02.01', 'INTERESES Y MULTAS IESS', '5.02.04.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.02.02', 'INTERESES Y MULTAS SRI', '5.02.04.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.02.03', 'INTERESES Y MULTAS MRL', '5.02.04.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.02.04', 'INTERESES Y MULTAS PTRAS INST. DEL ESTADO', '5.02.04.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.02.05', 'DIFERENCIAS POR CONTABILIZACIONES EN CÁLCULOS A 2 DECIMALES', '5.02.04.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.02.06', 'RETENCIONES ASUMIDAS IVA Y FUENTE', '5.02.04.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.02.07', 'GASTOS POR PERDIDA EN SINIESTROS', '5.02.04.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.03', 'GASTOS POR SERVICIOS EN HOTELES', '5.02.04', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.03.01', 'GASTO POR OTROS SERVICIOS', '5.02.04.03', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.04', 'OTROS GASTOS', '5.02.04', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.04.01', 'GASTOS VARIOS', '5.02.04.04', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.04.02', 'DONACIONES SOCIALES', '5.02.04.04', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.04.03', 'MATERIALES CONSTRUCCION P/CUBIERTA DE INVERNADEROS', '5.02.04.04', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.04.04', 'SERVICIO DE CONFECCION', '5.02.04.04', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.04.05', 'MANO DE OBRA', '5.02.04.04', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.04.06', 'INCEPTICIDAS PROYECTO TAKATAI', '5.02.04.04', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.04.07', 'ADECUACION (BIENES)', '5.02.04.04', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.04.08', 'INSUMOS DE COCINA', '5.02.04.04', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.04.09', 'INSUMOS PARA HABITACIONES', '5.02.04.04', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.04.10', 'MERMAS', '5.02.04.04', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.04.04.11', 'GASTOS POR SERVICIOS COMPLEMENTARIOS', '5.02.04.04', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05', 'GASTOS NO DEDUCIBLES', '5.02', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.01', 'MULTA, MORA E INTERES NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.02', 'TRANSPORTE DE PERSONAL NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.03', 'ENCOMIENDA NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.04', 'OTROS GASTOS NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.05', 'ALIMENTACIÓN NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.06', 'COMBUSTIBLE NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.07', 'RETENCIONES ASUMIDAS NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.08', 'BONIFICACION NAVIDEÑA NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.09', 'IMPUESTOS CONTRIBUCIONES Y OTROS NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.10', 'PERMISOS DE FUNCIONAMIENTO NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.11', 'APORTACIONES AL IEES ASUMIDAS NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.12', 'IMP. RTA. RELACION DEPENDENCIA ASUMIDA NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.13', 'DIFERENCIA POR DECIMALES EN EL SISTEMA SOFIA NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.14', 'SERVICIOS BASICOS NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.15', 'SERVICIO POR CORREO CORPORATIVO NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.16', 'HOSPEDAJE NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.17', 'COBRANZA POR MORA NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.18', 'COMISIONES BANCARIAS NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.19', 'BONIFICACION POR CUMPLIMIENTO DE METAS NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.20', 'OBLIGACIONES TRIBUTARIAS NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.21', 'SERVICIOS POR ANALISIS AL PERSONAL SN', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.22', 'DIETAS DEL DIRECTORIO NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.23', 'PEAJE NS', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.24', 'GASTOS POR REGULACIONES CONTABLES', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.25', 'GASTOS POR REGULARIZACIONES DE INVENTARIOS CLP', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.26', 'GASTOS POR REGULARIZACIONES DE INVENTARIOS FDN', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.27', 'GASTOS POR REGULARIZACIONES DE INVENTARIOS EL COCA', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.28', 'GASTOS POR IMPLEMENTACION DE LOTES CLP', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.29', 'GASTOS POR IMPLEMENTACION DE LOTES FDN', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.02.05.30', 'GASTOS POR IMPLEMENTACION DE LOTES EL COCA', '5.02.05', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.03', 'OTROS GASTOS', '5.', '5', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.03.01', 'GASTOS VARIOS', '5.03', '5', '1', '2026-06-17 10:02:28', '2026-06-17 10:02:28');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.03.02', 'RECARGOS EN COMPRAS', '5.03', '5', '1', '2026-07-05 17:57:13', '2026-07-05 17:57:13');
INSERT INTO `cc_cuenta_contabledet` VALUES ('5.03.03', 'SERVICIOS ADICIONALES EN COMPRAS', '5.03', '5', '1', '2026-07-05 17:57:44', '2026-07-05 17:57:44');
INSERT INTO `cc_cuenta_contabledet` VALUES ('50.2.02.16.02', 'SEMINARIOS CURSOS Y CAPACITACIONES AL PERSONAL', '5.02.02.16', '5', '1', '2026-07-05 16:58:21', '2026-07-05 16:58:21');
INSERT INTO `cc_cuenta_contabledet` VALUES ('6. ', 'CUENTAS DE CONTROL', '', '6', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');
INSERT INTO `cc_cuenta_contabledet` VALUES ('6.01', 'CUENTAS DE CONTROL', '6.', '6', '1', '2025-10-19 14:16:44', '2025-10-19 14:17:03');

-- ----------------------------
-- Table structure for cc_cuenta_contabledet_config
-- ----------------------------
DROP TABLE IF EXISTS `cc_cuenta_contabledet_config`;
CREATE TABLE `cc_cuenta_contabledet_config`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `ctcf_codigo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ctcf_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ctcf_detalle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fk_cuentacontable_det` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ctcf_estado` tinyint(0) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_cuentacotable_det`(`fk_cuentacontable_det`) USING BTREE,
  CONSTRAINT `cc_cuenta_contabledet_config_ibfk_1` FOREIGN KEY (`fk_cuentacontable_det`) REFERENCES `cc_cuenta_contabledet` (`ctad_codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_cuenta_contabledet_config
-- ----------------------------
INSERT INTO `cc_cuenta_contabledet_config` VALUES (1, '010', 'INVENTARIO DE MATERIA PRIMA IVA 0%', 'Para procesos que involucran producto con IVA 0%', '1.01.04.01.01', 1, '2025-10-19 14:17:30', '2025-10-23 13:56:59');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (2, '012', 'AJUSTES DE ENTRADA', 'INVENT.PRODC.TERM. Y MERC. EN ALMACEN COMPRADO A TERCEROS.', '1.01.04.02', 1, '2025-10-19 14:17:30', '2025-10-23 14:01:39');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (3, '011', 'INVENTARIO DE MATERIA PRIMA IVA 15% GENERAL', 'Para procesos que involucran productos con IVA', '1.01.04.01.02', 1, '2025-10-19 14:17:30', '2026-08-16 13:14:53');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (4, '013', 'AJUSTES DE SALIDA', 'INVENT.PRODC.TERM. Y MERC. EN ALMACEN COMPRADO A TERCEROS.', '1.01.04.02', 1, '2025-11-29 13:45:35', '2025-11-29 13:46:18');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (5, '014', 'CUENTA DE GASTOS VARIOS', 'CUENTA DE GASTOS VARIOS,  Configuración de cuenta para extraer la cuenta contable para especificar una cuenta de gasTo por defecto', '5.03.01', 1, '2026-06-17 10:03:51', '2026-06-17 10:03:51');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (6, '015', 'ICE', 'IMPUESTOS, CONTRIBUCIONES Y OTROS 5.2.02.13', '5.02.02.13.01', 1, '2026-07-05 17:09:46', '2026-07-05 17:09:46');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (7, '016', 'IVA COMPRAS 15% BIENES Y SERVICIOS GENERAL', 'CREDITO TRIBUTARIO A FAVOR DE LA EMPRESA (IVA) 1.01.06.01', '1.01.06.01.02', 1, '2026-07-05 17:26:26', '2026-08-16 13:13:31');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (8, '017', 'IVA COMPRAS 5% BIENES Y SERVICIOS ESPECIAL', 'CREDITO TRIBUTARIO A FAVOR DE LA EMPRESA (IVA) 1.01.06.01', '1.01.06.01.03', 1, '2026-07-05 17:27:20', '2026-07-05 19:34:38');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (9, '018', 'IRBPNR', 'IRBPNR', '5.02.02.13.19', 1, '2026-07-05 17:28:06', '2026-07-05 17:28:06');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (10, '019', 'RECARGOS EN COMPRAS', 'RECARGOS EN COMPRAS', '5.03.02', 1, '2026-07-05 17:58:29', '2026-07-05 17:58:29');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (11, '020', 'SERVICIOS ADICIONALES EN COMPRAS', 'SERVICIOS ADICIONALES EN COMPRAS', '5.03.03', 1, '2026-07-05 18:00:00', '2026-07-05 18:00:00');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (12, '021', 'CUENTAS POR PAGAR PROVEEDORES LOCALES', 'CUENTAS Y DCTOS. X PAGAR PROVEEDORES NO RELAC. LOCALES', '2.01.01.01.01', 1, '2026-07-06 08:54:19', '2026-07-06 09:02:44');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (13, '022', 'INVENTARIO DE MATERIA PRIMA IVA 5% ESPECIAL', 'IVA ESPECIAL PARA MATERIALES DE CONSTRUCCIÓN', '1.01.04.01.03', 1, '2026-07-10 10:00:02', '2026-07-10 10:00:02');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (14, '023', 'DESCUENTO EN COMPRAS', 'Para los descuentos en compras en general', '4.01.05', 1, '2026-07-28 09:43:22', '2026-07-28 09:43:22');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (15, '024', 'ANTICIPO A PROVEEDORES', 'PARA REGISTRAR ANTICIPO DE PROVEEDORES YA SEA POR NDC O POR OTRO MOVIMIENTO', '1.01.07.02', 1, '2026-07-28 10:17:44', '2026-07-28 10:17:44');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (16, '025', 'CUENTAS POR COBRAR CLIENTES', 'Cuenta general para registrar CxC de ventas cuando el cliente no tiene cuenta propia', '1.01.03.01', 1, '2026-08-16 14:06:20', '2026-08-16 14:06:20');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (17, '026', 'IVA VENTAS GENERAL', 'Cuenta de IVA por pagar en ventas tarifa general', '2.01.07.01.21', 1, '2026-08-16 14:06:55', '2026-08-16 14:06:55');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (18, '027', 'IVA VENTAS ESPECIAL', 'Cuenta de IVA por pagar en ventas tarifa especial', '2.01.07.01.22', 1, '2026-08-16 14:07:35', '2026-08-16 14:07:35');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (19, '028', 'COSTO DE VENTA', 'Cuenta general para registrar costo de venta', '5.01.01', 1, '2026-08-16 14:10:31', '2026-08-16 14:10:31');
INSERT INTO `cc_cuenta_contabledet_config` VALUES (20, '029', 'INGRESOS POR VENTAS GENERALES', 'Cuenta general de ingresos usada en ventas cuando el producto no tiene configurada una cuenta contable de ventas.', '4.01.01.01', 1, '2026-08-16 14:39:37', '2026-08-16 14:39:37');

-- ----------------------------
-- Table structure for cc_cxc
-- ----------------------------
DROP TABLE IF EXISTS `cc_cxc`;
CREATE TABLE `cc_cxc`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'Cuenta por cobrar generada por venta',
  `fk_venta` int(0) NOT NULL COMMENT 'Venta origen',
  `fk_cliente` int(0) NOT NULL COMMENT 'Cliente deudor',
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa',
  `cxc_total` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Total cuenta por cobrar',
  `cxc_valor_cobrado` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Valor cobrado',
  `cxc_saldo` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Saldo pendiente',
  `cxc_fecha_emision` date NOT NULL COMMENT 'Fecha emision venta',
  `cxc_fecha_vencimiento` date NULL DEFAULT NULL COMMENT 'Fecha vencimiento',
  `cxc_estado` enum('PENDIENTE','PARCIAL','COBRADO','ANULADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'PENDIENTE',
  `cxc_observacion` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Observacion de cartera',
  `cxc_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `cxc_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_cxc_venta`(`fk_venta`) USING BTREE,
  INDEX `idx_cxc_cliente`(`fk_cliente`) USING BTREE,
  INDEX `idx_cxc_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `fk_cxc_cliente` FOREIGN KEY (`fk_cliente`) REFERENCES `cc_clientes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_cxc_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_cxc_venta` FOREIGN KEY (`fk_venta`) REFERENCES `cc_ventas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_cxc
-- ----------------------------

-- ----------------------------
-- Table structure for cc_cxc_cuotas
-- ----------------------------
DROP TABLE IF EXISTS `cc_cxc_cuotas`;
CREATE TABLE `cc_cxc_cuotas`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'Cuota de cuenta por cobrar',
  `fk_cxc` int(0) NOT NULL COMMENT 'Cuenta por cobrar',
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa',
  `cxcc_numero` int(0) NOT NULL COMMENT 'Numero de cuota',
  `cxcc_fecha_vencimiento` date NOT NULL COMMENT 'Fecha vencimiento cuota',
  `cxcc_valor` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Valor cuota',
  `cxcc_cobrado` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Valor cobrado',
  `cxcc_saldo` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Saldo cuota',
  `cxcc_estado` enum('PENDIENTE','PARCIAL','COBRADO','ANULADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'PENDIENTE',
  `cxcc_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `cxcc_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_cxcc_cxc`(`fk_cxc`) USING BTREE,
  INDEX `idx_cxcc_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `fk_cxcc_cxc` FOREIGN KEY (`fk_cxc`) REFERENCES `cc_cxc` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_cxcc_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_cxc_cuotas
-- ----------------------------

-- ----------------------------
-- Table structure for cc_cxp
-- ----------------------------
DROP TABLE IF EXISTS `cc_cxp`;
CREATE TABLE `cc_cxp`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'ID de la cuenta por pagar',
  `fk_compra` int(0) NOT NULL COMMENT 'FK a cc_compras',
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa de la cuenta por pagar',
  `fk_proveedor` int(0) NOT NULL COMMENT 'FK del proveedor',
  `cxp_tipo_transaccion_cod` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Codigo de transaccion',
  `cxp_numero_documento` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Numero de factura',
  `cxp_tipo_pago` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'CONTADO o CREDITO',
  `cxp_num_cuotas` int(0) NULL DEFAULT 1 COMMENT 'Numero de cuotas',
  `cxp_total` decimal(14, 4) NOT NULL COMMENT 'Total deuda (compra - retencion)',
  `cxp_valor_pagado` decimal(14, 4) NULL DEFAULT 0.0000 COMMENT 'Total pagado acumulado',
  `cxp_saldo` decimal(14, 4) NOT NULL COMMENT 'Saldo pendiente',
  `cxp_fecha_ultimo_pago` date NULL DEFAULT NULL COMMENT 'Fecha del ultimo pago realizado',
  `cxp_estado` enum('PENDIENTE','PARCIAL','PAGADO','ANULADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'PENDIENTE' COMMENT 'Estado de la cuenta por pagar',
  `cxp_observacion` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fk_user` int(0) NULL DEFAULT NULL COMMENT 'Usuario que registra',
  `cxp_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `cxp_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_cxp_compra_proyecto`(`fk_compra`, `fk_proyecto`) USING BTREE,
  INDEX `idx_cxp_compra`(`fk_compra`) USING BTREE,
  INDEX `idx_cxp_proveedor`(`fk_proveedor`) USING BTREE,
  INDEX `idx_cxp_estado`(`cxp_estado`) USING BTREE,
  INDEX `cc_cxp_ibfk_3`(`fk_user`) USING BTREE,
  INDEX `idx_cxp_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_cxp_ibfk_1` FOREIGN KEY (`fk_compra`) REFERENCES `cc_compras` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_cxp_ibfk_2` FOREIGN KEY (`fk_proveedor`) REFERENCES `cc_proveedores` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_cxp_ibfk_3` FOREIGN KEY (`fk_user`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_cxp_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_cxp
-- ----------------------------

-- ----------------------------
-- Table structure for cc_cxp_cuotas
-- ----------------------------
DROP TABLE IF EXISTS `cc_cxp_cuotas`;
CREATE TABLE `cc_cxp_cuotas`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'ID de la cuota',
  `fk_cxp` int(0) NOT NULL COMMENT 'FK a cc_cxp',
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa de la cuota CxP',
  `cxpc_numero` int(0) NOT NULL COMMENT 'Numero de cuota',
  `cxpc_fecha_vencimiento` date NOT NULL COMMENT 'Fecha de vencimiento',
  `cxpc_valor` decimal(14, 4) NOT NULL COMMENT 'Valor de la cuota',
  `cxpc_pagado` decimal(14, 4) NULL DEFAULT 0.0000 COMMENT 'Monto pagado',
  `cxpc_saldo` decimal(14, 4) NOT NULL COMMENT 'Saldo pendiente',
  `cxpc_estado` enum('PENDIENTE','PARCIAL','PAGADO','ANULADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'PENDIENTE' COMMENT 'Estado de la cuota',
  `cxpc_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `cxpc_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_cxpc_cxp_numero`(`fk_cxp`, `cxpc_numero`) USING BTREE,
  INDEX `idx_cxpc_cxp`(`fk_cxp`) USING BTREE,
  INDEX `idx_cxpc_estado`(`cxpc_estado`) USING BTREE,
  INDEX `idx_cxpc_proyecto`(`fk_proyecto`) USING BTREE,
  INDEX `idx_cxpc_proyecto_cxp_estado`(`fk_proyecto`, `fk_cxp`, `cxpc_estado`) USING BTREE,
  CONSTRAINT `cc_cxp_cuotas_ibfk_1` FOREIGN KEY (`fk_cxp`) REFERENCES `cc_cxp` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_cxpc_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_cxp_cuotas
-- ----------------------------

-- ----------------------------
-- Table structure for cc_departamento
-- ----------------------------
DROP TABLE IF EXISTS `cc_departamento`;
CREATE TABLE `cc_departamento`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `dep_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `dep_estado` tinyint(1) NULL DEFAULT 1,
  `dep_fechacreacion` date NULL DEFAULT NULL,
  `dep_descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_departamento
-- ----------------------------
INSERT INTO `cc_departamento` VALUES (1, 'ADMINISTRATIVO', 1, '2023-01-30', 'ESTA UBICADO EL PERSONAL ADMINISTRATIVO');

-- ----------------------------
-- Table structure for cc_empleado_bodegas
-- ----------------------------
DROP TABLE IF EXISTS `cc_empleado_bodegas`;
CREATE TABLE `cc_empleado_bodegas`  (
  `fk_empleado` int(0) NULL DEFAULT NULL,
  `fk_bodega` int(0) NULL DEFAULT NULL,
  INDEX `fk_empleado`(`fk_empleado`) USING BTREE,
  INDEX `fk_bodega`(`fk_bodega`) USING BTREE,
  CONSTRAINT `cc_empleado_bodegas_ibfk_1` FOREIGN KEY (`fk_empleado`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_empleado_bodegas_ibfk_2` FOREIGN KEY (`fk_bodega`) REFERENCES `cc_bodegas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_empleado_bodegas
-- ----------------------------
INSERT INTO `cc_empleado_bodegas` VALUES (1, 1);

-- ----------------------------
-- Table structure for cc_empleado_proyecto
-- ----------------------------
DROP TABLE IF EXISTS `cc_empleado_proyecto`;
CREATE TABLE `cc_empleado_proyecto`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'ID de la relacion usuario proyecto',
  `fk_empleado` int(0) NOT NULL COMMENT 'Usuario con acceso al proyecto',
  `fk_proyecto` int(0) NOT NULL COMMENT 'Proyecto al que tiene acceso el usuario',
  `estado` tinyint(0) NOT NULL DEFAULT 1 COMMENT '1 activo, 0 inactivo',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `idx_empleado_proyecto`(`fk_empleado`, `fk_proyecto`) USING BTREE,
  INDEX `idx_empleado_proyecto_empleado`(`fk_empleado`) USING BTREE,
  INDEX `idx_empleado_proyecto_proyecto`(`fk_proyecto`) USING BTREE,
  INDEX `idx_empleado_proyecto_estado`(`estado`) USING BTREE,
  CONSTRAINT `cc_empleado_proyecto_ibfk_1` FOREIGN KEY (`fk_empleado`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_empleado_proyecto_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'Relacion de usuarios con proyectos o subempresas permitidas' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_empleado_proyecto
-- ----------------------------
INSERT INTO `cc_empleado_proyecto` VALUES (1, 1, 1, 1, '2026-08-17 16:47:03', '2026-08-17 16:47:03');

-- ----------------------------
-- Table structure for cc_empleados
-- ----------------------------
DROP TABLE IF EXISTS `cc_empleados`;
CREATE TABLE `cc_empleados`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `emp_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `emp_apellido` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `emp_dni` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `emp_username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `emp_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `emp_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `emp_telefono` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `emp_celular` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `emp_estado` tinyint(1) NULL DEFAULT 1,
  `emp_huella` blob NULL,
  `is_root` tinyint(1) NULL DEFAULT 0,
  `fk_rol` int(0) NULL DEFAULT 0,
  `emp_foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `theme_system` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '#505e9b,#6ce3db',
  `fk_cargo` int(0) NULL DEFAULT NULL,
  `fk_departamento` int(0) NULL DEFAULT NULL,
  `fk_bodega_main` int(0) NULL DEFAULT NULL,
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_cargo`(`fk_cargo`) USING BTREE,
  INDEX `fk_departamento`(`fk_departamento`) USING BTREE,
  INDEX `fk_bodega_main`(`fk_bodega_main`) USING BTREE,
  INDEX `fk_rol`(`fk_rol`) USING BTREE,
  CONSTRAINT `cc_empleados_ibfk_1` FOREIGN KEY (`fk_cargo`) REFERENCES `cc_cargo` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_empleados_ibfk_2` FOREIGN KEY (`fk_departamento`) REFERENCES `cc_departamento` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_empleados_ibfk_3` FOREIGN KEY (`fk_bodega_main`) REFERENCES `cc_bodegas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_empleados_ibfk_4` FOREIGN KEY (`fk_rol`) REFERENCES `cc_roles` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_empleados
-- ----------------------------
INSERT INTO `cc_empleados` VALUES (1, 'ADMIN', 'SISTEMA', '0000000000', 'root', '$2y$10$w4Pq7Kuwhd2fQwrUHq2/J.ncXtPeaXXNZ6pe82uDysPySujZ7ZhhK', '', '', '', 1, NULL, 1, 1, '', '#875653,#4f4df6', 7, 1, 1, '2026-03-22 12:34:21', '2026-08-17 16:47:03');

-- ----------------------------
-- Table structure for cc_empresa
-- ----------------------------
DROP TABLE IF EXISTS `cc_empresa`;
CREATE TABLE `cc_empresa`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `epr_ruc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `epr_razon_social` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `epr_rep_legal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `epr_nombre_comercial` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `epr_direccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `epr_ciudad` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `epr_mision` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `epr_vision` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `epr_objetivos` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `epr_logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `epr_telefono` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `epr_celular` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `epr_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `epr_fecha_creacion` date NULL DEFAULT NULL,
  `epr_pagina_web` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_empresa
-- ----------------------------
INSERT INTO `cc_empresa` VALUES (1, '9999999999999', 'EMPRESA CLIENTE', 'REPRESENTANTE LEGAL', 'EMPRESA CLIENTE', '', '', '', '', '', 'logo.png', '', '', '', '2026-08-17', '');

-- ----------------------------
-- Table structure for cc_empresa_indice
-- ----------------------------
DROP TABLE IF EXISTS `cc_empresa_indice`;
CREATE TABLE `cc_empresa_indice`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `ind_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ind_valor` float(255, 4) NULL DEFAULT NULL,
  `ind_fecha_actualizacion` datetime(0) NULL DEFAULT NULL,
  `ind_descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_empresa_indice
-- ----------------------------
INSERT INTO `cc_empresa_indice` VALUES (1, 'COSTO_INVENTARIO', 0.0000, '2026-08-17 16:47:03', 'Costo total de inventario de la empresa');

-- ----------------------------
-- Table structure for cc_formas_pago
-- ----------------------------
DROP TABLE IF EXISTS `cc_formas_pago`;
CREATE TABLE `cc_formas_pago`  (
  `cod` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fp_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fp_descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fp_estado` tinyint(0) NULL DEFAULT 1,
  PRIMARY KEY (`cod`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_formas_pago
-- ----------------------------
INSERT INTO `cc_formas_pago` VALUES ('01', 'EFECTIVO', 'Pago con dinero en efectivo', 1);
INSERT INTO `cc_formas_pago` VALUES ('02', 'TRANSFERENCIA', 'Pago con transferencia bancaria', 1);
INSERT INTO `cc_formas_pago` VALUES ('03', 'CHEQUE', 'Pago mediante cheque', 1);
INSERT INTO `cc_formas_pago` VALUES ('04', 'TARJETA DE CREDITO', 'Pago mediante tarjeta de credito', 1);
INSERT INTO `cc_formas_pago` VALUES ('05', 'TARJETA DE DEBITO', 'Pago mediante tarjeta de debito', 1);
INSERT INTO `cc_formas_pago` VALUES ('06', 'RETENCION', 'Cuando hacemos uso de la retención para poder pagar', 1);
INSERT INTO `cc_formas_pago` VALUES ('07', 'NOTA DE CREDITO', 'Cuando pagamos con una nota de credito', 1);

-- ----------------------------
-- Table structure for cc_formas_pago_sri
-- ----------------------------
DROP TABLE IF EXISTS `cc_formas_pago_sri`;
CREATE TABLE `cc_formas_pago_sri`  (
  `codigo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fp_nombre_sri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fp_estado` tinyint(0) NULL DEFAULT NULL,
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`codigo`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_formas_pago_sri
-- ----------------------------
INSERT INTO `cc_formas_pago_sri` VALUES ('01', 'SIN UTILIZACION DEL SISTEMA FINANCIERO', 1, '2026-05-24 11:14:59', '2026-05-24 11:14:59');
INSERT INTO `cc_formas_pago_sri` VALUES ('02', 'CHEQUE PROPIO', 0, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('03', 'CHEQUE CERTIFICADO', 0, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('04', 'CHEQUE DE GERENCIA', 0, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('05', 'CHEQUE DEL EXTERIOR', 0, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('06', 'DÉBITO DE CUENTA', 0, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('07', 'TRANSFERENCIA PROPIO BANCO', 0, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('08', 'TRANSFERENCIA OTRO BANCO NACIONAL', 0, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('09', 'TRANSFERENCIA  BANCO EXTERIOR', 0, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('10', 'TARJETA DE CRÉDITO NACIONAL', 0, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('11', 'TARJETA DE CRÉDITO INTERNACIONAL', 0, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('12', 'GIRO', 0, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('13', 'DEPOSITO EN CUENTA (CORRIENTE/AHORROS)', 0, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('14', 'ENDOSO DE INVERSIÓN', 0, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('15', 'COMPENSACIÓN DE DEUDAS', 1, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('16', 'TARJETA DE DEBITO', 1, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('17', 'DINERO ELECTRONICO', 1, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('18', 'TARJETA PREPAGO', 1, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('19', 'TARJETA DE CREDITO', 1, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('20', 'OTROS CON UTILIZACION DEL SISTEMA FINANCIERO', 1, '2026-05-24 11:15:58', '2026-05-24 11:15:58');
INSERT INTO `cc_formas_pago_sri` VALUES ('21', 'ENDOSO DE TITULOS', 1, '2026-05-24 11:15:58', '2026-05-24 11:15:58');

-- ----------------------------
-- Table structure for cc_grupos
-- ----------------------------
DROP TABLE IF EXISTS `cc_grupos`;
CREATE TABLE `cc_grupos`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `gr_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `gr_descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `gr_estado` tinyint(0) NULL DEFAULT NULL,
  `gr_icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `gr_fecha_creacion` date NULL DEFAULT NULL,
  `gr_fecha_actualizacion` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_grupos
-- ----------------------------
INSERT INTO `cc_grupos` VALUES (1, 'ABARROTES', 'PRODUCTOS Y MERCADERIA EN LO QUE COMPETE SOLO ABARROTES .', 1, 'far fa-box', '2025-11-26', '2025-11-26 09:57:20');
INSERT INTO `cc_grupos` VALUES (2, 'EQUIPOS DE COMPUTO', 'TODAS LAS MARCAS EN COMPUTADORAS', 1, 'fas fa-computer', '2024-06-12', '2025-11-10 19:16:59');
INSERT INTO `cc_grupos` VALUES (3, 'CARNICOS', 'PRODUCTOS AL GRUPO CARNICOS', 1, 'far fa-box', '2026-04-13', '2026-04-13 10:45:18');
INSERT INTO `cc_grupos` VALUES (4, 'ACEITES', 'PRODUCTOS AL GRUPO ACEITES', 1, 'far fa-box', '2026-04-23', '2026-04-23 13:49:57');
INSERT INTO `cc_grupos` VALUES (5, 'MANTECAS', 'PRODUCTOS AL GRUPO MANTECAS', 1, 'far fa-box', '2026-04-23', '2026-04-23 13:51:08');
INSERT INTO `cc_grupos` VALUES (6, 'COSTRUCCION', 'PRODUCTOS AL GRUPO COSTRUCCION', 1, 'far fa-box', '2026-04-30', '2026-04-30 16:52:38');
INSERT INTO `cc_grupos` VALUES (7, 'OTROS', 'PRODUCTOS AL GRUPO OTROS', 1, 'far fa-box', '2026-05-01', '2026-05-01 11:49:22');
INSERT INTO `cc_grupos` VALUES (8, 'VARIOS', 'PRODUCTOS AL GRUPO VARIOS', 1, 'far fa-box', '2026-06-20', '2026-06-20 14:34:32');
INSERT INTO `cc_grupos` VALUES (9, 'DESCUENTOS', 'PRODUCTOS AL GRUPO DESCUENTOS', 1, 'far fa-box', '2026-07-26', '2026-07-26 11:35:29');

-- ----------------------------
-- Table structure for cc_impuesto_tarifa
-- ----------------------------
DROP TABLE IF EXISTS `cc_impuesto_tarifa`;
CREATE TABLE `cc_impuesto_tarifa`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `impt_codigo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `impt_porcentage` decimal(10, 2) NULL DEFAULT NULL,
  `impt_detalle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `fk_impuesto` int(0) NULL DEFAULT NULL,
  `impt_fecha_inicio_vigencia` date NULL DEFAULT NULL,
  `impt_fecha_fin_vigencia` date NULL DEFAULT NULL,
  `impt_estado` enum('ACTIVO','HISTORIAL') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `impt_predeterminado` tinyint(0) NULL DEFAULT 0,
  `impt_report_iva` tinyint(0) NULL DEFAULT 0,
  `impt_grupo` enum('GENERAL','ESPECIAL','EXENTO','NO_OBJETO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `impt_fk_impuesto`(`fk_impuesto`) USING BTREE,
  INDEX `idx_impt_impuesto_estado`(`fk_impuesto`, `impt_estado`) USING BTREE,
  INDEX `idx_impt_impuesto_predeterminado`(`fk_impuesto`, `impt_predeterminado`) USING BTREE,
  INDEX `idx_impt_codigo_porcentaje`(`fk_impuesto`, `impt_codigo`, `impt_porcentage`) USING BTREE,
  INDEX `idx_impt_vigencia`(`fk_impuesto`, `impt_fecha_inicio_vigencia`, `impt_fecha_fin_vigencia`) USING BTREE,
  CONSTRAINT `cc_impuesto_tarifa_ibfk_1` FOREIGN KEY (`fk_impuesto`) REFERENCES `cc_impuestos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_impuesto_tarifa
-- ----------------------------
INSERT INTO `cc_impuesto_tarifa` VALUES (1, '0', 0.00, 'TARIFA CERO', 1, NULL, '2050-05-31', 'ACTIVO', 0, 1, NULL);
INSERT INTO `cc_impuesto_tarifa` VALUES (2, '2', 12.00, 'APLICA IVA', 1, NULL, '2025-01-31', 'HISTORIAL', 0, 2, 'GENERAL');
INSERT INTO `cc_impuesto_tarifa` VALUES (3, '7', 0.00, 'EXENTO DE IVA', 1, NULL, '2050-05-31', 'ACTIVO', 0, 0, 'EXENTO');
INSERT INTO `cc_impuesto_tarifa` VALUES (4, '6', 0.00, 'NO OBJETO DE IMPUESTOS', 1, NULL, '2050-05-31', 'ACTIVO', 0, 0, 'NO_OBJETO');
INSERT INTO `cc_impuesto_tarifa` VALUES (5, '3610', 20.00, 'PERFUMES Y AGUAS DE TOCADOR', 2, NULL, '2050-05-31', 'ACTIVO', 0, 0, NULL);
INSERT INTO `cc_impuesto_tarifa` VALUES (6, '2620', 35.00, 'VIDEOJUEGOS', 2, NULL, '2050-05-31', 'ACTIVO', 0, 0, NULL);
INSERT INTO `cc_impuesto_tarifa` VALUES (7, '4', 15.00, 'APLICA IVA', 1, '2025-02-01', NULL, 'ACTIVO', 1, 2, 'GENERAL');
INSERT INTO `cc_impuesto_tarifa` VALUES (8, '5', 5.00, 'APLICA IVA (CONSTRUCCION)', 1, '2025-02-01', NULL, 'ACTIVO', 0, 2, 'ESPECIAL');
INSERT INTO `cc_impuesto_tarifa` VALUES (9, '6', 6.00, 'NUEVO IVA CONSTRUCCION', 1, '2024-08-01', '2024-09-24', 'HISTORIAL', 0, 2, 'ESPECIAL');
INSERT INTO `cc_impuesto_tarifa` VALUES (10, '7', 13.00, 'APLICA IVA', 1, '2026-08-01', '2025-12-20', 'HISTORIAL', 0, 0, 'GENERAL');

-- ----------------------------
-- Table structure for cc_impuesto_tarifa_cuenta_contable
-- ----------------------------
DROP TABLE IF EXISTS `cc_impuesto_tarifa_cuenta_contable`;
CREATE TABLE `cc_impuesto_tarifa_cuenta_contable`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_impuesto_tarifa` int(0) NOT NULL COMMENT 'Tarifa de impuesto específica, activa o histórica',
  `tipo_movimiento` enum('COMPRA','VENTA') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Proceso donde se usa la cuenta',
  `tipo_cuenta` enum('IVA','INVENTARIO','DESCUENTO') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'IVA' COMMENT 'Uso contable de la cuenta',
  `fk_cuentacontable_det` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Cuenta contable asociada a esta tarifa',
  `estado` tinyint(0) NOT NULL DEFAULT 1,
  `created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `comentario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uq_tarifa_tipo_cuenta`(`fk_impuesto_tarifa`, `tipo_movimiento`, `tipo_cuenta`) USING BTREE,
  INDEX `idx_imptcc_cuenta`(`fk_cuentacontable_det`) USING BTREE,
  CONSTRAINT `fk_imptcc_cuenta` FOREIGN KEY (`fk_cuentacontable_det`) REFERENCES `cc_cuenta_contabledet` (`ctad_codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_imptcc_tarifa` FOREIGN KEY (`fk_impuesto_tarifa`) REFERENCES `cc_impuesto_tarifa` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_impuesto_tarifa_cuenta_contable
-- ----------------------------
INSERT INTO `cc_impuesto_tarifa_cuenta_contable` VALUES (1, 10, 'COMPRA', 'IVA', '1.01.06.01.04', 1, '2026-08-02 12:25:36', '2026-08-16 13:50:25', 'PARA EL IVA DE COMPRAS EN IVA 13');
INSERT INTO `cc_impuesto_tarifa_cuenta_contable` VALUES (2, 8, 'COMPRA', 'IVA', '1.01.06.01.03', 1, '2026-08-02 12:25:36', '2026-08-02 16:51:53', 'PARA EL IVA DE COMPRAS EN IVA 5');
INSERT INTO `cc_impuesto_tarifa_cuenta_contable` VALUES (3, 10, 'COMPRA', 'INVENTARIO', '1.01.04.01.04', 1, '2026-08-02 14:37:20', '2026-08-16 13:49:13', 'PARA LOS PRODUCTOS DE INVENTARIO IVA 13');
INSERT INTO `cc_impuesto_tarifa_cuenta_contable` VALUES (4, 10, 'COMPRA', 'DESCUENTO', '4.01.05.04', 1, '2026-08-02 16:40:45', '2026-08-16 13:48:30', 'PARA LAS NDC CON DESCUENTO IVA 13%');

-- ----------------------------
-- Table structure for cc_impuestos
-- ----------------------------
DROP TABLE IF EXISTS `cc_impuestos`;
CREATE TABLE `cc_impuestos`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `imp_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `imp_codigo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_impuestos
-- ----------------------------
INSERT INTO `cc_impuestos` VALUES (1, 'IVA', '2');
INSERT INTO `cc_impuestos` VALUES (2, 'ICE', '3');
INSERT INTO `cc_impuestos` VALUES (3, 'IRBPNR', '5');

-- ----------------------------
-- Table structure for cc_kardex
-- ----------------------------
DROP TABLE IF EXISTS `cc_kardex`;
CREATE TABLE `cc_kardex`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_producto` int(0) NULL DEFAULT NULL,
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa del movimiento kardex',
  `kar_kardex` decimal(15, 3) NULL DEFAULT NULL,
  `kar_kardex_total` decimal(15, 3) NULL DEFAULT NULL,
  `kar_costo_promedio` decimal(15, 4) NULL DEFAULT NULL,
  `kar_costo_ultimo` decimal(15, 4) NULL DEFAULT NULL,
  `kar_total_costo` decimal(15, 4) NULL DEFAULT NULL,
  `kar_documento_id` int(0) NULL DEFAULT NULL COMMENT 'ID documento (ajen_id, salida_id, etc.)',
  `kar_codigo_transaccion` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `kar_fecha` date NULL DEFAULT NULL,
  `kar_hora` time(0) NULL DEFAULT NULL,
  `kar_costoinventario_producto` decimal(15, 4) NULL DEFAULT NULL,
  `kar_costoinventario_total` decimal(15, 4) NULL DEFAULT NULL,
  `kar_estado` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '1',
  `fk_bodega` int(0) NULL DEFAULT NULL,
  `fk_user_id` int(0) NULL DEFAULT NULL,
  `fk_lote` int(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_producto`(`fk_producto`) USING BTREE,
  INDEX `kar_codigo_transaccion`(`kar_codigo_transaccion`) USING BTREE,
  INDEX `fk_bodega`(`fk_bodega`) USING BTREE,
  INDEX `fk_user_id`(`fk_user_id`) USING BTREE,
  INDEX `fk_lote`(`fk_lote`) USING BTREE,
  INDEX `idx_kardex_estado_fecha_producto_id`(`kar_estado`, `kar_fecha`, `fk_producto`, `id`) USING BTREE,
  INDEX `idx_kardex_producto_estado_fecha_id`(`fk_producto`, `kar_estado`, `kar_fecha`, `id`) USING BTREE,
  INDEX `idx_kardex_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_kardex_ibfk_1` FOREIGN KEY (`fk_producto`) REFERENCES `cc_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_kardex_ibfk_2` FOREIGN KEY (`kar_codigo_transaccion`) REFERENCES `cc_transacciones` (`tr_codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_kardex_ibfk_3` FOREIGN KEY (`fk_bodega`) REFERENCES `cc_bodegas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_kardex_ibfk_4` FOREIGN KEY (`fk_user_id`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_kardex_ibfk_5` FOREIGN KEY (`fk_lote`) REFERENCES `cc_lotes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_kardex_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_kardex
-- ----------------------------

-- ----------------------------
-- Table structure for cc_kardex_bodega
-- ----------------------------
DROP TABLE IF EXISTS `cc_kardex_bodega`;
CREATE TABLE `cc_kardex_bodega`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_producto` int(0) NULL DEFAULT NULL,
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa del kardex por bodega',
  `fk_bodega` int(0) NULL DEFAULT NULL,
  `karb_kardex` decimal(15, 3) NULL DEFAULT NULL,
  `karb_kardex_total` decimal(15, 3) NULL DEFAULT NULL,
  `karb_costo_promedio` decimal(15, 4) NULL DEFAULT NULL,
  `karb_costo_ultimo` decimal(15, 4) NULL DEFAULT NULL,
  `karb_documento_id` int(0) NULL DEFAULT NULL,
  `karb_codigo_transaccion` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `karb_fecha` date NULL DEFAULT NULL,
  `karb_hora` time(0) NULL DEFAULT NULL,
  `karb_estado` tinyint(0) NULL DEFAULT 1,
  `fk_lote` int(0) NULL DEFAULT NULL,
  `fk_user_id` int(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_producto`(`fk_producto`) USING BTREE,
  INDEX `fk_bodega`(`fk_bodega`) USING BTREE,
  INDEX `karb_codigo_transaccion`(`karb_codigo_transaccion`) USING BTREE,
  INDEX `fk_lote`(`fk_lote`) USING BTREE,
  INDEX `fk_user_id`(`fk_user_id`) USING BTREE,
  INDEX `idx_karb_bodega_estado_fecha_producto_id`(`fk_bodega`, `karb_estado`, `karb_fecha`, `fk_producto`, `id`) USING BTREE,
  INDEX `idx_karb_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_kardex_bodega_ibfk_1` FOREIGN KEY (`fk_producto`) REFERENCES `cc_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_kardex_bodega_ibfk_2` FOREIGN KEY (`fk_bodega`) REFERENCES `cc_bodegas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_kardex_bodega_ibfk_3` FOREIGN KEY (`karb_codigo_transaccion`) REFERENCES `cc_transacciones` (`tr_codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_kardex_bodega_ibfk_4` FOREIGN KEY (`fk_lote`) REFERENCES `cc_lotes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_kardex_bodega_ibfk_5` FOREIGN KEY (`fk_user_id`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_karb_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_kardex_bodega
-- ----------------------------

-- ----------------------------
-- Table structure for cc_kardex_bodega_lote
-- ----------------------------
DROP TABLE IF EXISTS `cc_kardex_bodega_lote`;
CREATE TABLE `cc_kardex_bodega_lote`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_producto` int(0) NULL DEFAULT NULL,
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa del kardex por bodega y lote',
  `fk_bodega` int(0) NULL DEFAULT NULL,
  `karbl_kardex` decimal(15, 3) NULL DEFAULT NULL,
  `karbl_kardex_total` decimal(15, 3) NULL DEFAULT NULL,
  `karbl_costo_promedio` decimal(15, 4) NULL DEFAULT NULL,
  `karbl_costo_ultimo` decimal(15, 4) NULL DEFAULT NULL,
  `karbl_documento_id` int(0) NULL DEFAULT NULL,
  `karbl_codigo_transaccion` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `karbl_fecha` date NULL DEFAULT NULL,
  `karbl_hora` time(0) NULL DEFAULT NULL,
  `karbl_estado` tinyint(0) NULL DEFAULT 1,
  `fk_lote` int(0) NULL DEFAULT NULL,
  `fk_user_id` int(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_producto`(`fk_producto`) USING BTREE,
  INDEX `fk_bodega`(`fk_bodega`) USING BTREE,
  INDEX `karb_codigo_transaccion`(`karbl_codigo_transaccion`) USING BTREE,
  INDEX `fk_lote`(`fk_lote`) USING BTREE,
  INDEX `fk_user_id`(`fk_user_id`) USING BTREE,
  INDEX `idx_karbl_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_kardex_bodega_lote_ibfk_1` FOREIGN KEY (`fk_producto`) REFERENCES `cc_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_kardex_bodega_lote_ibfk_2` FOREIGN KEY (`fk_bodega`) REFERENCES `cc_bodegas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_kardex_bodega_lote_ibfk_3` FOREIGN KEY (`karbl_codigo_transaccion`) REFERENCES `cc_transacciones` (`tr_codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_kardex_bodega_lote_ibfk_4` FOREIGN KEY (`fk_lote`) REFERENCES `cc_lotes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_kardex_bodega_lote_ibfk_5` FOREIGN KEY (`fk_user_id`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_karbl_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_kardex_bodega_lote
-- ----------------------------

-- ----------------------------
-- Table structure for cc_log
-- ----------------------------
DROP TABLE IF EXISTS `cc_log`;
CREATE TABLE `cc_log`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_user` int(0) NULL DEFAULT NULL,
  `log_detail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `log_fecha` date NULL DEFAULT NULL,
  `log_hora` time(0) NULL DEFAULT NULL,
  `log_dir` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `log_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_user`(`fk_user`) USING BTREE,
  CONSTRAINT `cc_log_ibfk_1` FOREIGN KEY (`fk_user`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_log
-- ----------------------------

-- ----------------------------
-- Table structure for cc_login_system
-- ----------------------------
DROP TABLE IF EXISTS `cc_login_system`;
CREATE TABLE `cc_login_system`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `id_user` int(0) NULL DEFAULT NULL,
  `fecha_login` date NULL DEFAULT NULL,
  `hora_login` time(0) NULL DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_login_system
-- ----------------------------

-- ----------------------------
-- Table structure for cc_lotes
-- ----------------------------
DROP TABLE IF EXISTS `cc_lotes`;
CREATE TABLE `cc_lotes`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `lot_lote` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `lot_fecha_elaboracion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `lot_fecha_caducidad` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `lot_documento_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fk_producto` int(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_producto`(`fk_producto`) USING BTREE,
  CONSTRAINT `cc_lotes_ibfk_1` FOREIGN KEY (`fk_producto`) REFERENCES `cc_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_lotes
-- ----------------------------

-- ----------------------------
-- Table structure for cc_marcas
-- ----------------------------
DROP TABLE IF EXISTS `cc_marcas`;
CREATE TABLE `cc_marcas`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `mrc_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mrc_estado` tinyint(0) NULL DEFAULT NULL,
  `mrc_fecha_creacion` date NULL DEFAULT NULL,
  `mrc_fecha_actualizacion` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 130 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_marcas
-- ----------------------------
INSERT INTO `cc_marcas` VALUES (1, 'LENOVO', 1, '2024-06-11', '2025-11-10 19:20:25');
INSERT INTO `cc_marcas` VALUES (2, 'DELL', 1, '2024-06-11', '2025-11-10 19:20:25');
INSERT INTO `cc_marcas` VALUES (3, 'HP', 1, '2024-06-11', '2025-11-10 19:20:25');
INSERT INTO `cc_marcas` VALUES (4, 'SAMSUNG', 0, '2025-10-23', '2025-11-10 19:20:25');
INSERT INTO `cc_marcas` VALUES (5, 'COCINERO', 1, '2025-11-04', '2025-11-10 19:20:25');
INSERT INTO `cc_marcas` VALUES (6, 'PRONACA', 1, '2025-11-04', '2025-11-10 19:20:25');
INSERT INTO `cc_marcas` VALUES (7, 'ECOLAC', 1, '2025-11-24', '2025-11-24 15:09:44');
INSERT INTO `cc_marcas` VALUES (49, 'KIOSCO', 1, '2025-11-26', '2025-11-25 19:12:48');
INSERT INTO `cc_marcas` VALUES (121, 'SANSUNG', 1, '2026-04-13', '2026-04-13 09:46:38');
INSERT INTO `cc_marcas` VALUES (122, 'TOSHIBA', 1, '2026-04-13', '2026-04-13 09:47:24');
INSERT INTO `cc_marcas` VALUES (123, 'SUPER', 1, '2026-04-13', '2026-04-13 09:53:39');
INSERT INTO `cc_marcas` VALUES (124, 'MSI', 1, '2026-04-13', '2026-04-13 10:14:46');
INSERT INTO `cc_marcas` VALUES (125, 'HORMTS', 1, '2026-04-23', '2026-04-23 15:52:34');
INSERT INTO `cc_marcas` VALUES (126, 'GUAPAN', 1, '2026-04-30', '2026-04-30 16:52:21');
INSERT INTO `cc_marcas` VALUES (127, 'COCA COLA', 1, '2026-05-03', '2026-05-03 12:41:27');
INSERT INTO `cc_marcas` VALUES (128, 'OTROS', 1, '2026-06-21', '2026-06-21 16:36:25');
INSERT INTO `cc_marcas` VALUES (129, 'PLASTIGAMA', 1, '2026-07-10', '2026-07-10 11:07:42');

-- ----------------------------
-- Table structure for cc_mes
-- ----------------------------
DROP TABLE IF EXISTS `cc_mes`;
CREATE TABLE `cc_mes`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `mes_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `mes_nombre_english` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_mes
-- ----------------------------
INSERT INTO `cc_mes` VALUES (1, 'ENERO', 'JANUARY');
INSERT INTO `cc_mes` VALUES (2, 'FEBRERO', 'FEBRUARY');
INSERT INTO `cc_mes` VALUES (3, 'MARZO', 'MARCH');
INSERT INTO `cc_mes` VALUES (4, 'ABRIL', 'APRIL');
INSERT INTO `cc_mes` VALUES (5, 'MAYO', 'MAY');
INSERT INTO `cc_mes` VALUES (6, 'JUNIO', 'JUNE');
INSERT INTO `cc_mes` VALUES (7, 'JULIO', 'JULY');
INSERT INTO `cc_mes` VALUES (8, 'AGOSTO', 'AUGUST');
INSERT INTO `cc_mes` VALUES (9, 'SEPTIEMBRE', 'SEPTEMBER');
INSERT INTO `cc_mes` VALUES (10, 'OCTUBRE', 'OCTOBER');
INSERT INTO `cc_mes` VALUES (11, 'NOVIEMBRE', 'NOVENBER');
INSERT INTO `cc_mes` VALUES (12, 'DICIEMBRE', 'DECEMBER');

-- ----------------------------
-- Table structure for cc_modulos
-- ----------------------------
DROP TABLE IF EXISTS `cc_modulos`;
CREATE TABLE `cc_modulos`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `md_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `md_descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `md_estado` tinyint(0) NULL DEFAULT NULL,
  `md_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `md_tipo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `md_icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `md_orden` int(0) NULL DEFAULT NULL,
  `md_padre` int(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_modulos
-- ----------------------------
INSERT INTO `cc_modulos` VALUES (1, 'COMPRAS', 'MODULO PARA GENERAR TODO PROCESO EN LO QUE COMPETE A COMPRAS ', 1, '/compras/dashboard', 'modulo', 'fad fa-shopping-cart', 2, NULL);
INSERT INTO `cc_modulos` VALUES (2, 'VENTAS', 'MODULO PARA GENERAR TODO PROCESO EN LO QUE COMPETE A VENTAS', 1, '/ventas/dashboard', 'modulo', 'fad fa-cash-register', 3, NULL);
INSERT INTO `cc_modulos` VALUES (3, 'CONTROL DE INVENTARIO', 'MODULO PARA GENERAR REPORTES DE INVENTARIOS Y KARDE POR PRODUCTOS', 1, '/inventarios', 'modulo', 'fad fa-clipboard', 1, NULL);
INSERT INTO `cc_modulos` VALUES (4, 'MANAGAMENT', 'MÓDULO ADMINISTRATIVO DEL SISTEMA', 1, '/admin', 'modulo', 'fad fa-cogs', 5, NULL);
INSERT INTO `cc_modulos` VALUES (5, 'CONTABILIDAD', 'MÓDULO PARA PROCESOS CONTABLES', 1, 'contabilidad/index', 'modulo', 'fad fa-dollar', 4, NULL);
INSERT INTO `cc_modulos` VALUES (6, 'EXISTENCIAS', 'PARA REPORTES DE INVENTARIO', 1, '/inventarios/existencias', 'submodulo', 'fad fa-boxes', 1, 3);
INSERT INTO `cc_modulos` VALUES (7, 'AJUSTES DE ENTRADA', 'PARA AJUSTAR EL INVENTARIO CON INGRESOS', 1, '/ajustesentrada/dashboard', 'submodulo', 'fad fa-sort-amount-up', 5, 3);
INSERT INTO `cc_modulos` VALUES (8, 'AJUSTES DE SALIDA', 'PARA AJUSTAR EL INVENTARIO CON SALIDAS', 1, '/ajustessalida/dashboard', 'submodulo', 'fad fa-sort-amount-down-alt', 4, 3);
INSERT INTO `cc_modulos` VALUES (9, 'TRANSFERENCIAS', 'PARA TRASFERIR PRODUCTOS DE UNA BODEGA A OTRA', 1, '/transferencias/dashboard', 'submodulo', 'fad fa-exchange-alt', 3, 3);
INSERT INTO `cc_modulos` VALUES (12, 'TESTEO21', 'TESTEO CON UPD244', 1, 'INDEX/UU', 'modulo', 'fas fa-save', 10, NULL);
INSERT INTO `cc_modulos` VALUES (13, 'TESTEO SUBM', 'TESTEO DE SUBMxx', 0, 'index/ventas', 'submodulo', 'fas fa-folder', 10, 12);
INSERT INTO `cc_modulos` VALUES (18, 'CONTROL KARDEX', 'En este modulo se dará seguimiento a los productos', 1, '/kardex/kardex', 'submodulo', 'fad fa-clipboard-list', 2, 3);
INSERT INTO `cc_modulos` VALUES (19, 'BIO COMEDOR', 'MODULO DE CONTROL DE MARCACIONES EN LOS COMEDORES', 1, '/biocomedor/dashboard', 'modulo', 'fad fa-utensils', 5, NULL);

-- ----------------------------
-- Table structure for cc_motivos_ajuste
-- ----------------------------
DROP TABLE IF EXISTS `cc_motivos_ajuste`;
CREATE TABLE `cc_motivos_ajuste`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `mot_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `mot_detalle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `mot_tipo` enum('AJUSTES','DESPACHOS','REGULARIZACIONES') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `mot_estado` tinyint(0) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_motivos_ajuste
-- ----------------------------
INSERT INTO `cc_motivos_ajuste` VALUES (1, 'AJUSTE INICIAL', 'PARA AJUSTES DE INVENARIOS INICIALES', 'AJUSTES', 1, '2025-10-07 19:34:51', '2025-10-07 19:45:53');
INSERT INTO `cc_motivos_ajuste` VALUES (2, 'DESPACHO DE MERCADERIA', 'PARA DESPACHOS DE MERCADERIA DE BODEGA', 'DESPACHOS', 1, '2025-10-07 19:46:13', '2026-04-29 15:46:06');
INSERT INTO `cc_motivos_ajuste` VALUES (3, 'REGULARIZACIÓN DE INVENTARIO', 'PARA REGULAR LOS INVENTARIOS', 'REGULARIZACIONES', 1, '2025-10-07 19:48:15', '2025-11-27 12:08:22');

-- ----------------------------
-- Table structure for cc_pagos
-- ----------------------------
DROP TABLE IF EXISTS `cc_pagos`;
CREATE TABLE `cc_pagos`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'ID del pago',
  `fk_proveedor` int(0) NOT NULL COMMENT 'Proveedor al que se paga',
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa del pago',
  `pg_numero_secuencial` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Numero de comprobante de pago es un secuencial',
  `pg_tipo_movimiento` enum('PAGO','NDC_COMPRA') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'PAGO' COMMENT 'PAGO = pago real, NDC_COMPRA = aplicacion de nota de credito de compra',
  `fk_compra_nota_credito` int(0) NULL DEFAULT NULL COMMENT 'FK a cc_compras cuando el movimiento corresponde a una nota de credito de compra',
  `pg_fecha` date NOT NULL COMMENT 'Fecha del pago',
  `fk_forma_pago` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'FK a cc_formas_pago',
  `fk_cuenta_contable` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `fk_banco` int(0) NULL DEFAULT NULL,
  `pg_referencia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Referencia (cheque, transferencia, etc)',
  `pg_numero_transferencia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pg_fecha_transferencia` date NULL DEFAULT NULL,
  `pg_numero_cheque` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pg_fecha_cheque` date NULL DEFAULT NULL,
  `pg_marca_tarjeta` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pg_lote_tarjeta` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pg_autorizacion_tarjeta` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pg_ultimos_digitos` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pg_fecha_voucher` date NULL DEFAULT NULL,
  `pg_valor` decimal(14, 4) NOT NULL COMMENT 'Valor total del pago',
  `pg_estado` enum('ACTIVO','ANULADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'ACTIVO',
  `pg_observacion` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fk_user` int(0) NULL DEFAULT NULL,
  `pg_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `pg_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_pago_proveedor`(`fk_proveedor`) USING BTREE,
  INDEX `idx_pago_fecha`(`pg_fecha`) USING BTREE,
  INDEX `fk_user`(`fk_user`) USING BTREE,
  INDEX `fk_forma_pago`(`fk_forma_pago`) USING BTREE,
  INDEX `idx_pago_cuenta_contable`(`fk_cuenta_contable`) USING BTREE,
  INDEX `idx_pago_banco`(`fk_banco`) USING BTREE,
  INDEX `idx_pago_ndc_compra`(`fk_compra_nota_credito`) USING BTREE,
  INDEX `idx_pago_tipo_ndc_estado`(`pg_tipo_movimiento`, `fk_compra_nota_credito`, `pg_estado`) USING BTREE,
  INDEX `idx_pago_proyecto`(`fk_proyecto`) USING BTREE,
  INDEX `idx_pago_proyecto_tipo_ndc_estado`(`fk_proyecto`, `pg_tipo_movimiento`, `fk_compra_nota_credito`, `pg_estado`) USING BTREE,
  CONSTRAINT `cc_pagos_ibfk_1` FOREIGN KEY (`fk_user`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_pagos_ibfk_2` FOREIGN KEY (`fk_proveedor`) REFERENCES `cc_proveedores` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_pagos_ibfk_3` FOREIGN KEY (`fk_forma_pago`) REFERENCES `cc_formas_pago` (`cod`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_pago_banco` FOREIGN KEY (`fk_banco`) REFERENCES `cc_bancos_list` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pago_cuenta_contable` FOREIGN KEY (`fk_cuenta_contable`) REFERENCES `cc_cuenta_contabledet` (`ctad_codigo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pago_ndc_compra` FOREIGN KEY (`fk_compra_nota_credito`) REFERENCES `cc_compras` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_pago_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_pagos
-- ----------------------------

-- ----------------------------
-- Table structure for cc_pagos_det
-- ----------------------------
DROP TABLE IF EXISTS `cc_pagos_det`;
CREATE TABLE `cc_pagos_det`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'Detalle del pago',
  `fk_pago` int(0) NOT NULL COMMENT 'FK a cc_pagos',
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa del detalle de pago',
  `fk_cxp` int(0) NOT NULL COMMENT 'FK a cc_cxp',
  `fk_cuota` int(0) NULL DEFAULT NULL COMMENT 'FK a cc_cxp_cuotas (opcional)',
  `pgd_valor` decimal(14, 4) NOT NULL COMMENT 'Monto aplicado a la deuda/cuota',
  `pgd_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_pgd_pago`(`fk_pago`) USING BTREE,
  INDEX `idx_pgd_cxp`(`fk_cxp`) USING BTREE,
  INDEX `idx_pgd_cuota`(`fk_cuota`) USING BTREE,
  INDEX `idx_pgd_proyecto`(`fk_proyecto`) USING BTREE,
  INDEX `idx_pgd_proyecto_cxp`(`fk_proyecto`, `fk_cxp`) USING BTREE,
  INDEX `idx_pgd_proyecto_pago`(`fk_proyecto`, `fk_pago`) USING BTREE,
  CONSTRAINT `cc_pagos_det_ibfk_1` FOREIGN KEY (`fk_pago`) REFERENCES `cc_pagos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_pagos_det_ibfk_2` FOREIGN KEY (`fk_cxp`) REFERENCES `cc_cxp` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_pagos_det_ibfk_3` FOREIGN KEY (`fk_cuota`) REFERENCES `cc_cxp_cuotas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_pgd_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_pagos_det
-- ----------------------------

-- ----------------------------
-- Table structure for cc_parametros_irbpnr
-- ----------------------------
DROP TABLE IF EXISTS `cc_parametros_irbpnr`;
CREATE TABLE `cc_parametros_irbpnr`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `param_clave` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `param_valor` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `param_estado` tinyint(0) NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_parametros_irbpnr
-- ----------------------------
INSERT INTO `cc_parametros_irbpnr` VALUES (1, 'IRBPNR_UNITARIO', '0.02', 1);

-- ----------------------------
-- Table structure for cc_parroquia
-- ----------------------------
DROP TABLE IF EXISTS `cc_parroquia`;
CREATE TABLE `cc_parroquia`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `prr_codigo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `prr_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `prr_estado` tinyint(1) NULL DEFAULT NULL,
  `fk_canton` int(0) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_canton`(`fk_canton`) USING BTREE,
  CONSTRAINT `cc_parroquia_ibfk_1` FOREIGN KEY (`fk_canton`) REFERENCES `cc_canton` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1400 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_parroquia
-- ----------------------------
INSERT INTO `cc_parroquia` VALUES (1, '01', 'BELLAVISTA', 1, 1);
INSERT INTO `cc_parroquia` VALUES (2, '02', 'CAÑARIBAMBA', 1, 1);
INSERT INTO `cc_parroquia` VALUES (3, '03', 'EL BATÁN', 1, 1);
INSERT INTO `cc_parroquia` VALUES (4, '04', 'EL SAGRARIO', 1, 1);
INSERT INTO `cc_parroquia` VALUES (5, '05', 'EL VECINO', 1, 1);
INSERT INTO `cc_parroquia` VALUES (6, '06', 'GIL RAMÍREZ DÁVALOS', 1, 1);
INSERT INTO `cc_parroquia` VALUES (7, '07', 'HUAYNACÁPAC', 1, 1);
INSERT INTO `cc_parroquia` VALUES (8, '08', 'MACHÁNGARA', 1, 1);
INSERT INTO `cc_parroquia` VALUES (9, '09', 'MONAY', 1, 1);
INSERT INTO `cc_parroquia` VALUES (10, '10', 'SAN BLAS', 1, 1);
INSERT INTO `cc_parroquia` VALUES (11, '11', 'SAN SEBASTIÁN', 1, 1);
INSERT INTO `cc_parroquia` VALUES (12, '12', 'SUCRE', 1, 1);
INSERT INTO `cc_parroquia` VALUES (13, '13', 'TOTORACOCHA', 1, 1);
INSERT INTO `cc_parroquia` VALUES (14, '14', 'YANUNCAY', 1, 1);
INSERT INTO `cc_parroquia` VALUES (15, '15', 'HERMANO MIGUEL', 1, 1);
INSERT INTO `cc_parroquia` VALUES (16, '50', 'CUENCA', 1, 1);
INSERT INTO `cc_parroquia` VALUES (17, '51', 'BAÑOS', 1, 1);
INSERT INTO `cc_parroquia` VALUES (18, '52', 'CUMBE', 1, 1);
INSERT INTO `cc_parroquia` VALUES (19, '53', 'CHAUCHA', 1, 1);
INSERT INTO `cc_parroquia` VALUES (20, '54', 'CHECA (JIDCAY)', 1, 1);
INSERT INTO `cc_parroquia` VALUES (21, '55', 'CHIQUINTAD', 1, 1);
INSERT INTO `cc_parroquia` VALUES (22, '56', 'LLACAO', 1, 1);
INSERT INTO `cc_parroquia` VALUES (23, '57', 'MOLLETURO', 1, 1);
INSERT INTO `cc_parroquia` VALUES (24, '58', 'NULTI', 1, 1);
INSERT INTO `cc_parroquia` VALUES (25, '59', 'OCTAVIO CORDERO PALACIOS (SANTA ROSA)', 1, 1);
INSERT INTO `cc_parroquia` VALUES (26, '60', 'PACCHA', 1, 1);
INSERT INTO `cc_parroquia` VALUES (27, '61', 'QUINGEO', 1, 1);
INSERT INTO `cc_parroquia` VALUES (28, '62', 'RICAURTE', 1, 1);
INSERT INTO `cc_parroquia` VALUES (29, '63', 'SAN JOAQUÍN', 1, 1);
INSERT INTO `cc_parroquia` VALUES (30, '64', 'SANTA ANA', 1, 1);
INSERT INTO `cc_parroquia` VALUES (31, '65', 'SAYAUSÍ', 1, 1);
INSERT INTO `cc_parroquia` VALUES (32, '66', 'SIDCAY', 1, 1);
INSERT INTO `cc_parroquia` VALUES (33, '67', 'SININCAY', 1, 1);
INSERT INTO `cc_parroquia` VALUES (34, '68', 'TARQUI', 1, 1);
INSERT INTO `cc_parroquia` VALUES (35, '69', 'TURI', 1, 1);
INSERT INTO `cc_parroquia` VALUES (36, '70', 'VALLE', 1, 1);
INSERT INTO `cc_parroquia` VALUES (37, '71', 'VICTORIA DEL PORTETE (IRQUIS)', 1, 1);
INSERT INTO `cc_parroquia` VALUES (38, '50', 'GIRÓN', 1, 2);
INSERT INTO `cc_parroquia` VALUES (39, '51', 'ASUNCIÓN', 1, 2);
INSERT INTO `cc_parroquia` VALUES (40, '52', 'SAN GERARDO', 1, 2);
INSERT INTO `cc_parroquia` VALUES (41, '50', 'GUALACEO', 1, 3);
INSERT INTO `cc_parroquia` VALUES (42, '51', 'CHORDELEG', 1, 3);
INSERT INTO `cc_parroquia` VALUES (43, '52', 'DANIEL CÓRDOVA TORAL (EL ORIENTE)', 1, 3);
INSERT INTO `cc_parroquia` VALUES (44, '53', 'JADÁN', 1, 3);
INSERT INTO `cc_parroquia` VALUES (45, '54', 'MARIANO MORENO', 1, 3);
INSERT INTO `cc_parroquia` VALUES (46, '55', 'PRINCIPAL', 1, 3);
INSERT INTO `cc_parroquia` VALUES (47, '56', 'REMIGIO CRESPO TORAL (GÚLAG)', 1, 3);
INSERT INTO `cc_parroquia` VALUES (48, '57', 'SAN JUAN', 1, 3);
INSERT INTO `cc_parroquia` VALUES (49, '58', 'ZHIDMAD', 1, 3);
INSERT INTO `cc_parroquia` VALUES (50, '59', 'LUIS CORDERO VEGA', 1, 3);
INSERT INTO `cc_parroquia` VALUES (51, '60', 'SIMÓN BOLÍVAR (CAB. EN GAÑANZOL)', 1, 3);
INSERT INTO `cc_parroquia` VALUES (52, '50', 'NABÓN', 1, 4);
INSERT INTO `cc_parroquia` VALUES (53, '51', 'COCHAPATA', 1, 4);
INSERT INTO `cc_parroquia` VALUES (54, '52', 'EL PROGRESO (CAB.EN ZHOTA)', 1, 4);
INSERT INTO `cc_parroquia` VALUES (55, '53', 'LAS NIEVES (CHAYA)', 1, 4);
INSERT INTO `cc_parroquia` VALUES (56, '54', 'OÑA', 1, 4);
INSERT INTO `cc_parroquia` VALUES (57, '50', 'PAUTE', 1, 5);
INSERT INTO `cc_parroquia` VALUES (58, '51', 'AMALUZA', 1, 5);
INSERT INTO `cc_parroquia` VALUES (59, '52', 'BULÁN (JOSÉ VÍCTOR IZQUIERDO)', 1, 5);
INSERT INTO `cc_parroquia` VALUES (60, '53', 'CHICÁN (GUILLERMO ORTEGA)', 1, 5);
INSERT INTO `cc_parroquia` VALUES (61, '54', 'EL CABO', 1, 5);
INSERT INTO `cc_parroquia` VALUES (62, '55', 'GUACHAPALA', 1, 5);
INSERT INTO `cc_parroquia` VALUES (63, '56', 'GUARAINAG', 1, 5);
INSERT INTO `cc_parroquia` VALUES (64, '57', 'PALMAS', 1, 5);
INSERT INTO `cc_parroquia` VALUES (65, '58', 'PAN', 1, 5);
INSERT INTO `cc_parroquia` VALUES (66, '59', 'SAN CRISTÓBAL (CARLOS ORDÓÑEZ LAZO)', 1, 5);
INSERT INTO `cc_parroquia` VALUES (67, '60', 'SEVILLA DE ORO', 1, 5);
INSERT INTO `cc_parroquia` VALUES (68, '61', 'TOMEBAMBA', 1, 5);
INSERT INTO `cc_parroquia` VALUES (69, '62', 'DUG DUG', 1, 5);
INSERT INTO `cc_parroquia` VALUES (70, '50', 'PUCARÁ', 1, 6);
INSERT INTO `cc_parroquia` VALUES (71, '51', 'CAMILO PONCE ENRÍQUEZ (CAB. EN RÍO 7 DE MOLLE', 1, 6);
INSERT INTO `cc_parroquia` VALUES (72, '52', 'SAN RAFAEL DE SHARUG', 1, 6);
INSERT INTO `cc_parroquia` VALUES (73, '50', 'SAN FERNANDO', 1, 7);
INSERT INTO `cc_parroquia` VALUES (74, '51', 'CHUMBLÍN', 1, 7);
INSERT INTO `cc_parroquia` VALUES (75, '50', 'SANTA ISABEL (CHAGUARURCO)', 1, 8);
INSERT INTO `cc_parroquia` VALUES (76, '51', 'ABDÓN CALDERÓN (LA UNIÓN)', 1, 8);
INSERT INTO `cc_parroquia` VALUES (77, '52', 'EL CARMEN DE PIJILÍ', 1, 8);
INSERT INTO `cc_parroquia` VALUES (78, '53', 'ZHAGLLI (SHAGLLI)', 1, 8);
INSERT INTO `cc_parroquia` VALUES (79, '54', 'SAN SALVADOR DE CAÑARIBAMBA', 1, 8);
INSERT INTO `cc_parroquia` VALUES (80, '50', 'SIGSIG', 1, 9);
INSERT INTO `cc_parroquia` VALUES (81, '51', 'CUCHIL (CUTCHIL)', 1, 9);
INSERT INTO `cc_parroquia` VALUES (82, '52', 'GIMA', 1, 9);
INSERT INTO `cc_parroquia` VALUES (83, '53', 'GUEL', 1, 9);
INSERT INTO `cc_parroquia` VALUES (84, '54', 'LUDO', 1, 9);
INSERT INTO `cc_parroquia` VALUES (85, '55', 'SAN BARTOLOMÉ', 1, 9);
INSERT INTO `cc_parroquia` VALUES (86, '56', 'SAN JOSÉ DE RARANGA', 1, 9);
INSERT INTO `cc_parroquia` VALUES (87, '50', 'SAN FELIPE DE OÑA CABECERA CANTONAL', 1, 10);
INSERT INTO `cc_parroquia` VALUES (88, '51', 'SUSUDEL', 1, 10);
INSERT INTO `cc_parroquia` VALUES (89, '50', 'CHORDELEG', 1, 11);
INSERT INTO `cc_parroquia` VALUES (90, '51', 'PRINCIPAL', 1, 11);
INSERT INTO `cc_parroquia` VALUES (91, '52', 'LA UNIÓN', 1, 11);
INSERT INTO `cc_parroquia` VALUES (92, '53', 'LUIS GALARZA ORELLANA (CAB.EN DELEGSOL)', 1, 11);
INSERT INTO `cc_parroquia` VALUES (93, '54', 'SAN MARTÍN DE PUZHIO', 1, 11);
INSERT INTO `cc_parroquia` VALUES (94, '50', 'EL PAN', 1, 12);
INSERT INTO `cc_parroquia` VALUES (95, '51', 'AMALUZA', 1, 12);
INSERT INTO `cc_parroquia` VALUES (96, '52', 'PALMAS', 1, 12);
INSERT INTO `cc_parroquia` VALUES (97, '53', 'SAN VICENTE', 1, 12);
INSERT INTO `cc_parroquia` VALUES (98, '50', 'SEVILLA DE ORO', 1, 13);
INSERT INTO `cc_parroquia` VALUES (99, '51', 'AMALUZA', 1, 13);
INSERT INTO `cc_parroquia` VALUES (100, '52', 'PALMAS', 1, 13);
INSERT INTO `cc_parroquia` VALUES (101, '50', 'GUACHAPALA', 1, 14);
INSERT INTO `cc_parroquia` VALUES (102, '50', 'CAMILO PONCE ENRÍQUEZ', 1, 15);
INSERT INTO `cc_parroquia` VALUES (103, '51', 'EL CARMEN DE PIJILÍ', 1, 15);
INSERT INTO `cc_parroquia` VALUES (104, '01', 'ÁNGEL POLIBIO CHÁVES', 1, 16);
INSERT INTO `cc_parroquia` VALUES (105, '02', 'GABRIEL IGNACIO VEINTIMILLA', 1, 16);
INSERT INTO `cc_parroquia` VALUES (106, '03', 'GUANUJO', 1, 16);
INSERT INTO `cc_parroquia` VALUES (107, '50', 'GUARANDA', 1, 16);
INSERT INTO `cc_parroquia` VALUES (108, '51', 'FACUNDO VELA', 1, 16);
INSERT INTO `cc_parroquia` VALUES (109, '52', 'GUANUJO', 1, 16);
INSERT INTO `cc_parroquia` VALUES (110, '53', 'JULIO E. MORENO (CATANAHUÁN GRANDE)', 1, 16);
INSERT INTO `cc_parroquia` VALUES (111, '54', 'LAS NAVES', 1, 16);
INSERT INTO `cc_parroquia` VALUES (112, '55', 'SALINAS', 1, 16);
INSERT INTO `cc_parroquia` VALUES (113, '56', 'SAN LORENZO', 1, 16);
INSERT INTO `cc_parroquia` VALUES (114, '57', 'SAN SIMÓN (YACOTO)', 1, 16);
INSERT INTO `cc_parroquia` VALUES (115, '58', 'SANTA FÉ (SANTA FÉ)', 1, 16);
INSERT INTO `cc_parroquia` VALUES (116, '59', 'SIMIÁTUG', 1, 16);
INSERT INTO `cc_parroquia` VALUES (117, '60', 'SAN LUIS DE PAMBIL', 1, 16);
INSERT INTO `cc_parroquia` VALUES (118, '50', 'CHILLANES', 1, 17);
INSERT INTO `cc_parroquia` VALUES (119, '51', 'SAN JOSÉ DEL TAMBO (TAMBOPAMBA)', 1, 17);
INSERT INTO `cc_parroquia` VALUES (120, '50', 'SAN JOSÉ DE CHIMBO', 1, 18);
INSERT INTO `cc_parroquia` VALUES (121, '51', 'ASUNCIÓN (ASANCOTO)', 1, 18);
INSERT INTO `cc_parroquia` VALUES (122, '52', 'CALUMA', 1, 18);
INSERT INTO `cc_parroquia` VALUES (123, '53', 'MAGDALENA (CHAPACOTO)', 1, 18);
INSERT INTO `cc_parroquia` VALUES (124, '54', 'SAN SEBASTIÁN', 1, 18);
INSERT INTO `cc_parroquia` VALUES (125, '55', 'TELIMBELA', 1, 18);
INSERT INTO `cc_parroquia` VALUES (126, '50', 'ECHEANDÍA', 1, 19);
INSERT INTO `cc_parroquia` VALUES (127, '50', 'SAN MIGUEL', 1, 20);
INSERT INTO `cc_parroquia` VALUES (128, '51', 'BALSAPAMBA', 1, 20);
INSERT INTO `cc_parroquia` VALUES (129, '52', 'BILOVÁN', 1, 20);
INSERT INTO `cc_parroquia` VALUES (130, '53', 'RÉGULO DE MORA', 1, 20);
INSERT INTO `cc_parroquia` VALUES (131, '54', 'SAN PABLO (SAN PABLO DE ATENAS)', 1, 20);
INSERT INTO `cc_parroquia` VALUES (132, '55', 'SANTIAGO', 1, 20);
INSERT INTO `cc_parroquia` VALUES (133, '56', 'SAN VICENTE', 1, 20);
INSERT INTO `cc_parroquia` VALUES (134, '50', 'CALUMA', 1, 21);
INSERT INTO `cc_parroquia` VALUES (135, '01', 'LAS MERCEDES', 1, 22);
INSERT INTO `cc_parroquia` VALUES (136, '02', 'LAS NAVES', 1, 22);
INSERT INTO `cc_parroquia` VALUES (137, '50', 'LAS NAVES', 1, 22);
INSERT INTO `cc_parroquia` VALUES (138, '01', 'AURELIO BAYAS MARTÍNEZ', 1, 23);
INSERT INTO `cc_parroquia` VALUES (139, '02', 'AZOGUES', 1, 23);
INSERT INTO `cc_parroquia` VALUES (140, '03', 'BORRERO', 1, 23);
INSERT INTO `cc_parroquia` VALUES (141, '04', 'SAN FRANCISCO', 1, 23);
INSERT INTO `cc_parroquia` VALUES (142, '50', 'AZOGUES', 1, 23);
INSERT INTO `cc_parroquia` VALUES (143, '51', 'COJITAMBO', 1, 23);
INSERT INTO `cc_parroquia` VALUES (144, '52', 'DÉLEG', 1, 23);
INSERT INTO `cc_parroquia` VALUES (145, '53', 'GUAPÁN', 1, 23);
INSERT INTO `cc_parroquia` VALUES (146, '54', 'JAVIER LOYOLA (CHUQUIPATA)', 1, 23);
INSERT INTO `cc_parroquia` VALUES (147, '55', 'LUIS CORDERO', 1, 23);
INSERT INTO `cc_parroquia` VALUES (148, '56', 'PINDILIG', 1, 23);
INSERT INTO `cc_parroquia` VALUES (149, '57', 'RIVERA', 1, 23);
INSERT INTO `cc_parroquia` VALUES (150, '58', 'SAN MIGUEL', 1, 23);
INSERT INTO `cc_parroquia` VALUES (151, '59', 'SOLANO', 1, 23);
INSERT INTO `cc_parroquia` VALUES (152, '60', 'TADAY', 1, 23);
INSERT INTO `cc_parroquia` VALUES (153, '50', 'BIBLIÁN', 1, 24);
INSERT INTO `cc_parroquia` VALUES (154, '51', 'NAZÓN (CAB. EN PAMPA DE DOMÍNGUEZ)', 1, 24);
INSERT INTO `cc_parroquia` VALUES (155, '52', 'SAN FRANCISCO DE SAGEO', 1, 24);
INSERT INTO `cc_parroquia` VALUES (156, '53', 'TURUPAMBA', 1, 24);
INSERT INTO `cc_parroquia` VALUES (157, '54', 'JERUSALÉN', 1, 24);
INSERT INTO `cc_parroquia` VALUES (158, '50', 'CAÑAR', 1, 25);
INSERT INTO `cc_parroquia` VALUES (159, '51', 'CHONTAMARCA', 1, 25);
INSERT INTO `cc_parroquia` VALUES (160, '52', 'CHOROCOPTE', 1, 25);
INSERT INTO `cc_parroquia` VALUES (161, '53', 'GENERAL MORALES (SOCARTE)', 1, 25);
INSERT INTO `cc_parroquia` VALUES (162, '54', 'GUALLETURO', 1, 25);
INSERT INTO `cc_parroquia` VALUES (163, '55', 'HONORATO VÁSQUEZ (TAMBO VIEJO)', 1, 25);
INSERT INTO `cc_parroquia` VALUES (164, '56', 'INGAPIRCA', 1, 25);
INSERT INTO `cc_parroquia` VALUES (165, '57', 'JUNCAL', 1, 25);
INSERT INTO `cc_parroquia` VALUES (166, '58', 'SAN ANTONIO', 1, 25);
INSERT INTO `cc_parroquia` VALUES (167, '59', 'SUSCAL', 1, 25);
INSERT INTO `cc_parroquia` VALUES (168, '60', 'TAMBO', 1, 25);
INSERT INTO `cc_parroquia` VALUES (169, '61', 'ZHUD', 1, 25);
INSERT INTO `cc_parroquia` VALUES (170, '62', 'VENTURA', 1, 25);
INSERT INTO `cc_parroquia` VALUES (171, '63', 'DUCUR', 1, 25);
INSERT INTO `cc_parroquia` VALUES (172, '50', 'LA TRONCAL', 1, 26);
INSERT INTO `cc_parroquia` VALUES (173, '51', 'MANUEL J. CALLE', 1, 26);
INSERT INTO `cc_parroquia` VALUES (174, '52', 'PANCHO NEGRO', 1, 26);
INSERT INTO `cc_parroquia` VALUES (175, '50', 'EL TAMBO', 1, 27);
INSERT INTO `cc_parroquia` VALUES (176, '50', 'DÉLEG', 1, 28);
INSERT INTO `cc_parroquia` VALUES (177, '51', 'SOLANO', 1, 28);
INSERT INTO `cc_parroquia` VALUES (178, '50', 'SUSCAL', 1, 29);
INSERT INTO `cc_parroquia` VALUES (179, '01', 'GONZÁLEZ SUÁREZ', 1, 30);
INSERT INTO `cc_parroquia` VALUES (180, '02', 'TULCÁN', 1, 30);
INSERT INTO `cc_parroquia` VALUES (181, '50', 'TULCÁN', 1, 30);
INSERT INTO `cc_parroquia` VALUES (182, '51', 'EL CARMELO (EL PUN)', 1, 30);
INSERT INTO `cc_parroquia` VALUES (183, '52', 'HUACA', 1, 30);
INSERT INTO `cc_parroquia` VALUES (184, '53', 'JULIO ANDRADE (OREJUELA)', 1, 30);
INSERT INTO `cc_parroquia` VALUES (185, '54', 'MALDONADO', 1, 30);
INSERT INTO `cc_parroquia` VALUES (186, '55', 'PIOTER', 1, 30);
INSERT INTO `cc_parroquia` VALUES (187, '56', 'TOBAR DONOSO (LA BOCANA DE CAMUMBÍ)', 1, 30);
INSERT INTO `cc_parroquia` VALUES (188, '57', 'TUFIÑO', 1, 30);
INSERT INTO `cc_parroquia` VALUES (189, '58', 'URBINA (TAYA)', 1, 30);
INSERT INTO `cc_parroquia` VALUES (190, '59', 'EL CHICAL', 1, 30);
INSERT INTO `cc_parroquia` VALUES (191, '60', 'MARISCAL SUCRE', 1, 30);
INSERT INTO `cc_parroquia` VALUES (192, '61', 'SANTA MARTHA DE CUBA', 1, 30);
INSERT INTO `cc_parroquia` VALUES (193, '50', 'BOLÍVAR', 1, 31);
INSERT INTO `cc_parroquia` VALUES (194, '51', 'GARCÍA MORENO', 1, 31);
INSERT INTO `cc_parroquia` VALUES (195, '52', 'LOS ANDES', 1, 31);
INSERT INTO `cc_parroquia` VALUES (196, '53', 'MONTE OLIVO', 1, 31);
INSERT INTO `cc_parroquia` VALUES (197, '54', 'SAN VICENTE DE PUSIR', 1, 31);
INSERT INTO `cc_parroquia` VALUES (198, '55', 'SAN RAFAEL', 1, 31);
INSERT INTO `cc_parroquia` VALUES (199, '01', 'EL ÁNGEL', 1, 32);
INSERT INTO `cc_parroquia` VALUES (200, '02', '27 DE SEPTIEMBRE', 1, 32);
INSERT INTO `cc_parroquia` VALUES (201, '50', 'EL ANGEL', 1, 32);
INSERT INTO `cc_parroquia` VALUES (202, '51', 'EL GOALTAL', 1, 32);
INSERT INTO `cc_parroquia` VALUES (203, '52', 'LA LIBERTAD (ALIZO)', 1, 32);
INSERT INTO `cc_parroquia` VALUES (204, '53', 'SAN ISIDRO', 1, 32);
INSERT INTO `cc_parroquia` VALUES (205, '50', 'MIRA (CHONTAHUASI)', 1, 33);
INSERT INTO `cc_parroquia` VALUES (206, '51', 'CONCEPCIÓN', 1, 33);
INSERT INTO `cc_parroquia` VALUES (207, '52', 'JIJÓN Y CAAMAÑO (CAB. EN RÍO BLANCO)', 1, 33);
INSERT INTO `cc_parroquia` VALUES (208, '53', 'JUAN MONTALVO (SAN IGNACIO DE QUIL)', 1, 33);
INSERT INTO `cc_parroquia` VALUES (209, '01', 'GONZÁLEZ SUÁREZ', 1, 34);
INSERT INTO `cc_parroquia` VALUES (210, '02', 'SAN JOSÉ', 1, 34);
INSERT INTO `cc_parroquia` VALUES (211, '50', 'SAN GABRIEL', 1, 34);
INSERT INTO `cc_parroquia` VALUES (212, '51', 'CRISTÓBAL COLÓN', 1, 34);
INSERT INTO `cc_parroquia` VALUES (213, '52', 'CHITÁN DE NAVARRETE', 1, 34);
INSERT INTO `cc_parroquia` VALUES (214, '53', 'FERNÁNDEZ SALVADOR', 1, 34);
INSERT INTO `cc_parroquia` VALUES (215, '54', 'LA PAZ', 1, 34);
INSERT INTO `cc_parroquia` VALUES (216, '55', 'PIARTAL', 1, 34);
INSERT INTO `cc_parroquia` VALUES (217, '50', 'HUACA', 1, 35);
INSERT INTO `cc_parroquia` VALUES (218, '51', 'MARISCAL SUCRE', 1, 35);
INSERT INTO `cc_parroquia` VALUES (219, '01', 'ELOY ALFARO (SAN FELIPE)', 1, 36);
INSERT INTO `cc_parroquia` VALUES (220, '02', 'IGNACIO FLORES (PARQUE FLORES)', 1, 36);
INSERT INTO `cc_parroquia` VALUES (221, '03', 'JUAN MONTALVO (SAN SEBASTIÁN)', 1, 36);
INSERT INTO `cc_parroquia` VALUES (222, '04', 'LA MATRIZ', 1, 36);
INSERT INTO `cc_parroquia` VALUES (223, '05', 'SAN BUENAVENTURA', 1, 36);
INSERT INTO `cc_parroquia` VALUES (224, '50', 'LATACUNGA', 1, 36);
INSERT INTO `cc_parroquia` VALUES (225, '51', 'ALAQUES (ALÁQUEZ)', 1, 36);
INSERT INTO `cc_parroquia` VALUES (226, '52', 'BELISARIO QUEVEDO (GUANAILÍN)', 1, 36);
INSERT INTO `cc_parroquia` VALUES (227, '53', 'GUAITACAMA (GUAYTACAMA)', 1, 36);
INSERT INTO `cc_parroquia` VALUES (228, '54', 'JOSEGUANGO BAJO', 1, 36);
INSERT INTO `cc_parroquia` VALUES (229, '55', 'LAS PAMPAS', 1, 36);
INSERT INTO `cc_parroquia` VALUES (230, '56', 'MULALÓ', 1, 36);
INSERT INTO `cc_parroquia` VALUES (231, '57', '11 DE NOVIEMBRE (ILINCHISI)', 1, 36);
INSERT INTO `cc_parroquia` VALUES (232, '58', 'POALÓ', 1, 36);
INSERT INTO `cc_parroquia` VALUES (233, '59', 'SAN JUAN DE PASTOCALLE', 1, 36);
INSERT INTO `cc_parroquia` VALUES (234, '60', 'SIGCHOS', 1, 36);
INSERT INTO `cc_parroquia` VALUES (235, '61', 'TANICUCHÍ', 1, 36);
INSERT INTO `cc_parroquia` VALUES (236, '62', 'TOACASO', 1, 36);
INSERT INTO `cc_parroquia` VALUES (237, '63', 'PALO QUEMADO', 1, 36);
INSERT INTO `cc_parroquia` VALUES (238, '01', 'EL CARMEN', 1, 37);
INSERT INTO `cc_parroquia` VALUES (239, '02', 'LA MANÁ', 1, 37);
INSERT INTO `cc_parroquia` VALUES (240, '03', 'EL TRIUNFO', 1, 37);
INSERT INTO `cc_parroquia` VALUES (241, '50', 'LA MANÁ', 1, 37);
INSERT INTO `cc_parroquia` VALUES (242, '51', 'GUASAGANDA (CAB.EN GUASAGANDA', 1, 37);
INSERT INTO `cc_parroquia` VALUES (243, '52', 'PUCAYACU', 1, 37);
INSERT INTO `cc_parroquia` VALUES (244, '50', 'EL CORAZÓN', 1, 38);
INSERT INTO `cc_parroquia` VALUES (245, '51', 'MORASPUNGO', 1, 38);
INSERT INTO `cc_parroquia` VALUES (246, '52', 'PINLLOPATA', 1, 38);
INSERT INTO `cc_parroquia` VALUES (247, '53', 'RAMÓN CAMPAÑA', 1, 38);
INSERT INTO `cc_parroquia` VALUES (248, '50', 'PUJILÍ', 1, 39);
INSERT INTO `cc_parroquia` VALUES (249, '51', 'ANGAMARCA', 1, 39);
INSERT INTO `cc_parroquia` VALUES (250, '52', 'CHUCCHILÁN (CHUGCHILÁN)', 1, 39);
INSERT INTO `cc_parroquia` VALUES (251, '53', 'GUANGAJE', 1, 39);
INSERT INTO `cc_parroquia` VALUES (252, '54', 'ISINLIBÍ (ISINLIVÍ)', 1, 39);
INSERT INTO `cc_parroquia` VALUES (253, '55', 'LA VICTORIA', 1, 39);
INSERT INTO `cc_parroquia` VALUES (254, '56', 'PILALÓ', 1, 39);
INSERT INTO `cc_parroquia` VALUES (255, '57', 'TINGO', 1, 39);
INSERT INTO `cc_parroquia` VALUES (256, '58', 'ZUMBAHUA', 1, 39);
INSERT INTO `cc_parroquia` VALUES (257, '50', 'SAN MIGUEL', 1, 40);
INSERT INTO `cc_parroquia` VALUES (258, '51', 'ANTONIO JOSÉ HOLGUÍN (SANTA LUCÍA)', 1, 40);
INSERT INTO `cc_parroquia` VALUES (259, '52', 'CUSUBAMBA', 1, 40);
INSERT INTO `cc_parroquia` VALUES (260, '53', 'MULALILLO', 1, 40);
INSERT INTO `cc_parroquia` VALUES (261, '54', 'MULLIQUINDIL (SANTA ANA)', 1, 40);
INSERT INTO `cc_parroquia` VALUES (262, '55', 'PANSALEO', 1, 40);
INSERT INTO `cc_parroquia` VALUES (263, '50', 'SAQUISILÍ', 1, 41);
INSERT INTO `cc_parroquia` VALUES (264, '51', 'CANCHAGUA', 1, 41);
INSERT INTO `cc_parroquia` VALUES (265, '52', 'CHANTILÍN', 1, 41);
INSERT INTO `cc_parroquia` VALUES (266, '53', 'COCHAPAMBA', 1, 41);
INSERT INTO `cc_parroquia` VALUES (267, '50', 'SIGCHOS', 1, 42);
INSERT INTO `cc_parroquia` VALUES (268, '51', 'CHUGCHILLÁN', 1, 42);
INSERT INTO `cc_parroquia` VALUES (269, '52', 'ISINLIVÍ', 1, 42);
INSERT INTO `cc_parroquia` VALUES (270, '53', 'LAS PAMPAS', 1, 42);
INSERT INTO `cc_parroquia` VALUES (271, '54', 'PALO QUEMADO', 1, 42);
INSERT INTO `cc_parroquia` VALUES (272, '01', 'LIZARZABURU', 1, 43);
INSERT INTO `cc_parroquia` VALUES (273, '02', 'MALDONADO', 1, 43);
INSERT INTO `cc_parroquia` VALUES (274, '03', 'VELASCO', 1, 43);
INSERT INTO `cc_parroquia` VALUES (275, '04', 'VELOZ', 1, 43);
INSERT INTO `cc_parroquia` VALUES (276, '05', 'YARUQUÍES', 1, 43);
INSERT INTO `cc_parroquia` VALUES (277, '50', 'RIOBAMBA', 1, 43);
INSERT INTO `cc_parroquia` VALUES (278, '51', 'CACHA (CAB. EN MACHÁNGARA)', 1, 43);
INSERT INTO `cc_parroquia` VALUES (279, '52', 'CALPI', 1, 43);
INSERT INTO `cc_parroquia` VALUES (280, '53', 'CUBIJÍES', 1, 43);
INSERT INTO `cc_parroquia` VALUES (281, '54', 'FLORES', 1, 43);
INSERT INTO `cc_parroquia` VALUES (282, '55', 'LICÁN', 1, 43);
INSERT INTO `cc_parroquia` VALUES (283, '56', 'LICTO', 1, 43);
INSERT INTO `cc_parroquia` VALUES (284, '57', 'PUNGALÁ', 1, 43);
INSERT INTO `cc_parroquia` VALUES (285, '58', 'PUNÍN', 1, 43);
INSERT INTO `cc_parroquia` VALUES (286, '59', 'QUIMIAG', 1, 43);
INSERT INTO `cc_parroquia` VALUES (287, '60', 'SAN JUAN', 1, 43);
INSERT INTO `cc_parroquia` VALUES (288, '61', 'SAN LUIS', 1, 43);
INSERT INTO `cc_parroquia` VALUES (289, '50', 'ALAUSÍ', 1, 44);
INSERT INTO `cc_parroquia` VALUES (290, '51', 'ACHUPALLAS', 1, 44);
INSERT INTO `cc_parroquia` VALUES (291, '52', 'CUMANDÁ', 1, 44);
INSERT INTO `cc_parroquia` VALUES (292, '53', 'GUASUNTOS', 1, 44);
INSERT INTO `cc_parroquia` VALUES (293, '54', 'HUIGRA', 1, 44);
INSERT INTO `cc_parroquia` VALUES (294, '55', 'MULTITUD', 1, 44);
INSERT INTO `cc_parroquia` VALUES (295, '56', 'PISTISHÍ (NARIZ DEL DIABLO)', 1, 44);
INSERT INTO `cc_parroquia` VALUES (296, '57', 'PUMALLACTA', 1, 44);
INSERT INTO `cc_parroquia` VALUES (297, '58', 'SEVILLA', 1, 44);
INSERT INTO `cc_parroquia` VALUES (298, '59', 'SIBAMBE', 1, 44);
INSERT INTO `cc_parroquia` VALUES (299, '60', 'TIXÁN', 1, 44);
INSERT INTO `cc_parroquia` VALUES (300, '01', 'CAJABAMBA', 1, 45);
INSERT INTO `cc_parroquia` VALUES (301, '02', 'SICALPA', 1, 45);
INSERT INTO `cc_parroquia` VALUES (302, '50', 'VILLA LA UNIÓN (CAJABAMBA)', 1, 45);
INSERT INTO `cc_parroquia` VALUES (303, '51', 'CAÑI', 1, 45);
INSERT INTO `cc_parroquia` VALUES (304, '52', 'COLUMBE', 1, 45);
INSERT INTO `cc_parroquia` VALUES (305, '53', 'JUAN DE VELASCO (PANGOR)', 1, 45);
INSERT INTO `cc_parroquia` VALUES (306, '54', 'SANTIAGO DE QUITO (CAB. EN SAN ANTONIO DE QUI', 1, 45);
INSERT INTO `cc_parroquia` VALUES (307, '50', 'CHAMBO', 1, 46);
INSERT INTO `cc_parroquia` VALUES (308, '50', 'CHUNCHI', 1, 47);
INSERT INTO `cc_parroquia` VALUES (309, '51', 'CAPZOL', 1, 47);
INSERT INTO `cc_parroquia` VALUES (310, '52', 'COMPUD', 1, 47);
INSERT INTO `cc_parroquia` VALUES (311, '53', 'GONZOL', 1, 47);
INSERT INTO `cc_parroquia` VALUES (312, '54', 'LLAGOS', 1, 47);
INSERT INTO `cc_parroquia` VALUES (313, '50', 'GUAMOTE', 1, 48);
INSERT INTO `cc_parroquia` VALUES (314, '51', 'CEBADAS', 1, 48);
INSERT INTO `cc_parroquia` VALUES (315, '52', 'PALMIRA', 1, 48);
INSERT INTO `cc_parroquia` VALUES (316, '01', 'EL ROSARIO', 1, 49);
INSERT INTO `cc_parroquia` VALUES (317, '02', 'LA MATRIZ', 1, 49);
INSERT INTO `cc_parroquia` VALUES (318, '50', 'GUANO', 1, 49);
INSERT INTO `cc_parroquia` VALUES (319, '51', 'GUANANDO', 1, 49);
INSERT INTO `cc_parroquia` VALUES (320, '52', 'ILAPO', 1, 49);
INSERT INTO `cc_parroquia` VALUES (321, '53', 'LA PROVIDENCIA', 1, 49);
INSERT INTO `cc_parroquia` VALUES (322, '54', 'SAN ANDRÉS', 1, 49);
INSERT INTO `cc_parroquia` VALUES (323, '55', 'SAN GERARDO DE PACAICAGUÁN', 1, 49);
INSERT INTO `cc_parroquia` VALUES (324, '56', 'SAN ISIDRO DE PATULÚ', 1, 49);
INSERT INTO `cc_parroquia` VALUES (325, '57', 'SAN JOSÉ DEL CHAZO', 1, 49);
INSERT INTO `cc_parroquia` VALUES (326, '58', 'SANTA FÉ DE GALÁN', 1, 49);
INSERT INTO `cc_parroquia` VALUES (327, '59', 'VALPARAÍSO', 1, 49);
INSERT INTO `cc_parroquia` VALUES (328, '50', 'PALLATANGA', 1, 50);
INSERT INTO `cc_parroquia` VALUES (329, '50', 'PENIPE', 1, 51);
INSERT INTO `cc_parroquia` VALUES (330, '51', 'EL ALTAR', 1, 51);
INSERT INTO `cc_parroquia` VALUES (331, '52', 'MATUS', 1, 51);
INSERT INTO `cc_parroquia` VALUES (332, '53', 'PUELA', 1, 51);
INSERT INTO `cc_parroquia` VALUES (333, '54', 'SAN ANTONIO DE BAYUSHIG', 1, 51);
INSERT INTO `cc_parroquia` VALUES (334, '55', 'LA CANDELARIA', 1, 51);
INSERT INTO `cc_parroquia` VALUES (335, '56', 'BILBAO (CAB.EN QUILLUYACU)', 1, 51);
INSERT INTO `cc_parroquia` VALUES (336, '50', 'CUMANDÁ', 1, 52);
INSERT INTO `cc_parroquia` VALUES (337, '01', 'LA PROVIDENCIA', 1, 53);
INSERT INTO `cc_parroquia` VALUES (338, '02', 'MACHALA', 1, 53);
INSERT INTO `cc_parroquia` VALUES (339, '03', 'PUERTO BOLÍVAR', 1, 53);
INSERT INTO `cc_parroquia` VALUES (340, '04', 'NUEVE DE MAYO', 1, 53);
INSERT INTO `cc_parroquia` VALUES (341, '05', 'EL CAMBIO', 1, 53);
INSERT INTO `cc_parroquia` VALUES (342, '50', 'MACHALA', 1, 53);
INSERT INTO `cc_parroquia` VALUES (343, '51', 'EL CAMBIO', 1, 53);
INSERT INTO `cc_parroquia` VALUES (344, '52', 'EL RETIRO', 1, 53);
INSERT INTO `cc_parroquia` VALUES (345, '50', 'ARENILLAS', 1, 54);
INSERT INTO `cc_parroquia` VALUES (346, '51', 'CHACRAS', 1, 54);
INSERT INTO `cc_parroquia` VALUES (347, '52', 'LA LIBERTAD', 1, 54);
INSERT INTO `cc_parroquia` VALUES (348, '53', 'LAS LAJAS (CAB. EN LA VICTORIA)', 1, 54);
INSERT INTO `cc_parroquia` VALUES (349, '54', 'PALMALES', 1, 54);
INSERT INTO `cc_parroquia` VALUES (350, '55', 'CARCABÓN', 1, 54);
INSERT INTO `cc_parroquia` VALUES (351, '50', 'PACCHA', 1, 55);
INSERT INTO `cc_parroquia` VALUES (352, '51', 'AYAPAMBA', 1, 55);
INSERT INTO `cc_parroquia` VALUES (353, '52', 'CORDONCILLO', 1, 55);
INSERT INTO `cc_parroquia` VALUES (354, '53', 'MILAGRO', 1, 55);
INSERT INTO `cc_parroquia` VALUES (355, '54', 'SAN JOSÉ', 1, 55);
INSERT INTO `cc_parroquia` VALUES (356, '55', 'SAN JUAN DE CERRO AZUL', 1, 55);
INSERT INTO `cc_parroquia` VALUES (357, '50', 'BALSAS', 1, 56);
INSERT INTO `cc_parroquia` VALUES (358, '51', 'BELLAMARÍA', 1, 56);
INSERT INTO `cc_parroquia` VALUES (359, '50', 'CHILLA', 1, 57);
INSERT INTO `cc_parroquia` VALUES (360, '50', 'EL GUABO', 1, 58);
INSERT INTO `cc_parroquia` VALUES (361, '51', 'BARBONES (SUCRE)', 1, 58);
INSERT INTO `cc_parroquia` VALUES (362, '52', 'LA IBERIA', 1, 58);
INSERT INTO `cc_parroquia` VALUES (363, '53', 'TENDALES (CAB.EN PUERTO TENDALES)', 1, 58);
INSERT INTO `cc_parroquia` VALUES (364, '54', 'RÍO BONITO', 1, 58);
INSERT INTO `cc_parroquia` VALUES (365, '01', 'ECUADOR', 1, 59);
INSERT INTO `cc_parroquia` VALUES (366, '02', 'EL PARAÍSO', 1, 59);
INSERT INTO `cc_parroquia` VALUES (367, '03', 'HUALTACO', 1, 59);
INSERT INTO `cc_parroquia` VALUES (368, '04', 'MILTON REYES', 1, 59);
INSERT INTO `cc_parroquia` VALUES (369, '05', 'UNIÓN LOJANA', 1, 59);
INSERT INTO `cc_parroquia` VALUES (370, '50', 'HUAQUILLAS', 1, 59);
INSERT INTO `cc_parroquia` VALUES (371, '50', 'MARCABELÍ', 1, 60);
INSERT INTO `cc_parroquia` VALUES (372, '51', 'EL INGENIO', 1, 60);
INSERT INTO `cc_parroquia` VALUES (373, '01', 'BOLÍVAR', 1, 61);
INSERT INTO `cc_parroquia` VALUES (374, '02', 'LOMA DE FRANCO', 1, 61);
INSERT INTO `cc_parroquia` VALUES (375, '03', 'OCHOA LEÓN (MATRIZ)', 1, 61);
INSERT INTO `cc_parroquia` VALUES (376, '04', 'TRES CERRITOS', 1, 61);
INSERT INTO `cc_parroquia` VALUES (377, '50', 'PASAJE', 1, 61);
INSERT INTO `cc_parroquia` VALUES (378, '51', 'BUENAVISTA', 1, 61);
INSERT INTO `cc_parroquia` VALUES (379, '52', 'CASACAY', 1, 61);
INSERT INTO `cc_parroquia` VALUES (380, '53', 'LA PEAÑA', 1, 61);
INSERT INTO `cc_parroquia` VALUES (381, '54', 'PROGRESO', 1, 61);
INSERT INTO `cc_parroquia` VALUES (382, '55', 'UZHCURRUMI', 1, 61);
INSERT INTO `cc_parroquia` VALUES (383, '56', 'CAÑAQUEMADA', 1, 61);
INSERT INTO `cc_parroquia` VALUES (384, '01', 'LA MATRIZ', 1, 62);
INSERT INTO `cc_parroquia` VALUES (385, '02', 'LA SUSAYA', 1, 62);
INSERT INTO `cc_parroquia` VALUES (386, '03', 'PIÑAS GRANDE', 1, 62);
INSERT INTO `cc_parroquia` VALUES (387, '50', 'PIÑAS', 1, 62);
INSERT INTO `cc_parroquia` VALUES (388, '51', 'CAPIRO (CAB. EN LA CAPILLA DE CAPIRO)', 1, 62);
INSERT INTO `cc_parroquia` VALUES (389, '52', 'LA BOCANA', 1, 62);
INSERT INTO `cc_parroquia` VALUES (390, '53', 'MOROMORO (CAB. EN EL VADO)', 1, 62);
INSERT INTO `cc_parroquia` VALUES (391, '54', 'PIEDRAS', 1, 62);
INSERT INTO `cc_parroquia` VALUES (392, '55', 'SAN ROQUE (AMBROSIO MALDONADO)', 1, 62);
INSERT INTO `cc_parroquia` VALUES (393, '56', 'SARACAY', 1, 62);
INSERT INTO `cc_parroquia` VALUES (394, '50', 'PORTOVELO', 1, 63);
INSERT INTO `cc_parroquia` VALUES (395, '51', 'CURTINCAPA', 1, 63);
INSERT INTO `cc_parroquia` VALUES (396, '52', 'MORALES', 1, 63);
INSERT INTO `cc_parroquia` VALUES (397, '53', 'SALATÍ', 1, 63);
INSERT INTO `cc_parroquia` VALUES (398, '01', 'SANTA ROSA', 1, 64);
INSERT INTO `cc_parroquia` VALUES (399, '02', 'PUERTO JELÍ', 1, 64);
INSERT INTO `cc_parroquia` VALUES (400, '03', 'BALNEARIO JAMBELÍ (SATÉLITE)', 1, 64);
INSERT INTO `cc_parroquia` VALUES (401, '04', 'JUMÓN (SATÉLITE)', 1, 64);
INSERT INTO `cc_parroquia` VALUES (402, '05', 'NUEVO SANTA ROSA', 1, 64);
INSERT INTO `cc_parroquia` VALUES (403, '50', 'SANTA ROSA', 1, 64);
INSERT INTO `cc_parroquia` VALUES (404, '51', 'BELLAVISTA', 1, 64);
INSERT INTO `cc_parroquia` VALUES (405, '52', 'JAMBELÍ', 1, 64);
INSERT INTO `cc_parroquia` VALUES (406, '53', 'LA AVANZADA', 1, 64);
INSERT INTO `cc_parroquia` VALUES (407, '54', 'SAN ANTONIO', 1, 64);
INSERT INTO `cc_parroquia` VALUES (408, '55', 'TORATA', 1, 64);
INSERT INTO `cc_parroquia` VALUES (409, '56', 'VICTORIA', 1, 64);
INSERT INTO `cc_parroquia` VALUES (410, '57', 'BELLAMARÍA', 1, 64);
INSERT INTO `cc_parroquia` VALUES (411, '50', 'ZARUMA', 1, 65);
INSERT INTO `cc_parroquia` VALUES (412, '51', 'ABAÑÍN', 1, 65);
INSERT INTO `cc_parroquia` VALUES (413, '52', 'ARCAPAMBA', 1, 65);
INSERT INTO `cc_parroquia` VALUES (414, '53', 'GUANAZÁN', 1, 65);
INSERT INTO `cc_parroquia` VALUES (415, '54', 'GUIZHAGUIÑA', 1, 65);
INSERT INTO `cc_parroquia` VALUES (416, '55', 'HUERTAS', 1, 65);
INSERT INTO `cc_parroquia` VALUES (417, '56', 'MALVAS', 1, 65);
INSERT INTO `cc_parroquia` VALUES (418, '57', 'MULUNCAY GRANDE', 1, 65);
INSERT INTO `cc_parroquia` VALUES (419, '58', 'SINSAO', 1, 65);
INSERT INTO `cc_parroquia` VALUES (420, '59', 'SALVIAS', 1, 65);
INSERT INTO `cc_parroquia` VALUES (421, '01', 'LA VICTORIA', 1, 66);
INSERT INTO `cc_parroquia` VALUES (422, '02', 'PLATANILLOS', 1, 66);
INSERT INTO `cc_parroquia` VALUES (423, '03', 'VALLE HERMOSO', 1, 66);
INSERT INTO `cc_parroquia` VALUES (424, '50', 'LA VICTORIA', 1, 66);
INSERT INTO `cc_parroquia` VALUES (425, '51', 'LA LIBERTAD', 1, 66);
INSERT INTO `cc_parroquia` VALUES (426, '52', 'EL PARAÍSO', 1, 66);
INSERT INTO `cc_parroquia` VALUES (427, '53', 'SAN ISIDRO', 1, 66);
INSERT INTO `cc_parroquia` VALUES (428, '01', 'BARTOLOMÉ RUIZ (CÉSAR FRANCO CARRIÓN)', 1, 67);
INSERT INTO `cc_parroquia` VALUES (429, '02', '5 DE AGOSTO', 1, 67);
INSERT INTO `cc_parroquia` VALUES (430, '03', 'ESMERALDAS', 1, 67);
INSERT INTO `cc_parroquia` VALUES (431, '04', 'LUIS TELLO (LAS PALMAS)', 1, 67);
INSERT INTO `cc_parroquia` VALUES (432, '05', 'SIMÓN PLATA TORRES', 1, 67);
INSERT INTO `cc_parroquia` VALUES (433, '50', 'ESMERALDAS', 1, 67);
INSERT INTO `cc_parroquia` VALUES (434, '51', 'ATACAMES', 1, 67);
INSERT INTO `cc_parroquia` VALUES (435, '52', 'CAMARONES (CAB. EN SAN VICENTE)', 1, 67);
INSERT INTO `cc_parroquia` VALUES (436, '53', 'CRNEL. CARLOS CONCHA TORRES (CAB.EN HUELE)', 1, 67);
INSERT INTO `cc_parroquia` VALUES (437, '54', 'CHINCA', 1, 67);
INSERT INTO `cc_parroquia` VALUES (438, '55', 'CHONTADURO', 1, 67);
INSERT INTO `cc_parroquia` VALUES (439, '56', 'CHUMUNDÉ', 1, 67);
INSERT INTO `cc_parroquia` VALUES (440, '57', 'LAGARTO', 1, 67);
INSERT INTO `cc_parroquia` VALUES (441, '58', 'LA UNIÓN', 1, 67);
INSERT INTO `cc_parroquia` VALUES (442, '59', 'MAJUA', 1, 67);
INSERT INTO `cc_parroquia` VALUES (443, '60', 'MONTALVO (CAB. EN HORQUETA)', 1, 67);
INSERT INTO `cc_parroquia` VALUES (444, '61', 'RÍO VERDE', 1, 67);
INSERT INTO `cc_parroquia` VALUES (445, '62', 'ROCAFUERTE', 1, 67);
INSERT INTO `cc_parroquia` VALUES (446, '63', 'SAN MATEO', 1, 67);
INSERT INTO `cc_parroquia` VALUES (447, '64', 'SÚA (CAB. EN LA BOCANA)', 1, 67);
INSERT INTO `cc_parroquia` VALUES (448, '65', 'TABIAZO', 1, 67);
INSERT INTO `cc_parroquia` VALUES (449, '66', 'TACHINA', 1, 67);
INSERT INTO `cc_parroquia` VALUES (450, '67', 'TONCHIGÜE', 1, 67);
INSERT INTO `cc_parroquia` VALUES (451, '68', 'VUELTA LARGA', 1, 67);
INSERT INTO `cc_parroquia` VALUES (452, '50', 'VALDEZ (LIMONES)', 1, 68);
INSERT INTO `cc_parroquia` VALUES (453, '51', 'ANCHAYACU', 1, 68);
INSERT INTO `cc_parroquia` VALUES (454, '52', 'ATAHUALPA (CAB. EN CAMARONES)', 1, 68);
INSERT INTO `cc_parroquia` VALUES (455, '53', 'BORBÓN', 1, 68);
INSERT INTO `cc_parroquia` VALUES (456, '54', 'LA TOLA', 1, 68);
INSERT INTO `cc_parroquia` VALUES (457, '55', 'LUIS VARGAS TORRES (CAB. EN PLAYA DE ORO)', 1, 68);
INSERT INTO `cc_parroquia` VALUES (458, '56', 'MALDONADO', 1, 68);
INSERT INTO `cc_parroquia` VALUES (459, '57', 'PAMPANAL DE BOLÍVAR', 1, 68);
INSERT INTO `cc_parroquia` VALUES (460, '58', 'SAN FRANCISCO DE ONZOLE', 1, 68);
INSERT INTO `cc_parroquia` VALUES (461, '59', 'SANTO DOMINGO DE ONZOLE', 1, 68);
INSERT INTO `cc_parroquia` VALUES (462, '60', 'SELVA ALEGRE', 1, 68);
INSERT INTO `cc_parroquia` VALUES (463, '61', 'TELEMBÍ', 1, 68);
INSERT INTO `cc_parroquia` VALUES (464, '62', 'COLÓN ELOY DEL MARÍA', 1, 68);
INSERT INTO `cc_parroquia` VALUES (465, '63', 'SAN JOSÉ DE CAYAPAS', 1, 68);
INSERT INTO `cc_parroquia` VALUES (466, '64', 'TIMBIRÉ', 1, 68);
INSERT INTO `cc_parroquia` VALUES (467, '50', 'MUISNE', 1, 69);
INSERT INTO `cc_parroquia` VALUES (468, '51', 'BOLÍVAR', 1, 69);
INSERT INTO `cc_parroquia` VALUES (469, '52', 'DAULE', 1, 69);
INSERT INTO `cc_parroquia` VALUES (470, '53', 'GALERA', 1, 69);
INSERT INTO `cc_parroquia` VALUES (471, '54', 'QUINGUE (OLMEDO PERDOMO FRANCO)', 1, 69);
INSERT INTO `cc_parroquia` VALUES (472, '55', 'SALIMA', 1, 69);
INSERT INTO `cc_parroquia` VALUES (473, '56', 'SAN FRANCISCO', 1, 69);
INSERT INTO `cc_parroquia` VALUES (474, '57', 'SAN GREGORIO', 1, 69);
INSERT INTO `cc_parroquia` VALUES (475, '58', 'SAN JOSÉ DE CHAMANGA (CAB.EN CHAMANGA)', 1, 69);
INSERT INTO `cc_parroquia` VALUES (476, '50', 'ROSA ZÁRATE (QUININDÉ)', 1, 70);
INSERT INTO `cc_parroquia` VALUES (477, '51', 'CUBE', 1, 70);
INSERT INTO `cc_parroquia` VALUES (478, '52', 'CHURA (CHANCAMA) (CAB. EN EL YERBERO)', 1, 70);
INSERT INTO `cc_parroquia` VALUES (479, '53', 'MALIMPIA', 1, 70);
INSERT INTO `cc_parroquia` VALUES (480, '54', 'VICHE', 1, 70);
INSERT INTO `cc_parroquia` VALUES (481, '55', 'LA UNIÓN', 1, 70);
INSERT INTO `cc_parroquia` VALUES (482, '50', 'SAN LORENZO', 1, 71);
INSERT INTO `cc_parroquia` VALUES (483, '51', 'ALTO TAMBO (CAB. EN GUADUAL)', 1, 71);
INSERT INTO `cc_parroquia` VALUES (484, '52', 'ANCÓN (PICHANGAL) (CAB. EN PALMA REAL)', 1, 71);
INSERT INTO `cc_parroquia` VALUES (485, '53', 'CALDERÓN', 1, 71);
INSERT INTO `cc_parroquia` VALUES (486, '54', 'CARONDELET', 1, 71);
INSERT INTO `cc_parroquia` VALUES (487, '55', '5 DE JUNIO (CAB. EN UIMBI)', 1, 71);
INSERT INTO `cc_parroquia` VALUES (488, '56', 'CONCEPCIÓN', 1, 71);
INSERT INTO `cc_parroquia` VALUES (489, '57', 'MATAJE (CAB. EN SANTANDER)', 1, 71);
INSERT INTO `cc_parroquia` VALUES (490, '58', 'SAN JAVIER DE CACHAVÍ (CAB. EN SAN JAVIER)', 1, 71);
INSERT INTO `cc_parroquia` VALUES (491, '59', 'SANTA RITA', 1, 71);
INSERT INTO `cc_parroquia` VALUES (492, '60', 'TAMBILLO', 1, 71);
INSERT INTO `cc_parroquia` VALUES (493, '61', 'TULULBÍ (CAB. EN RICAURTE)', 1, 71);
INSERT INTO `cc_parroquia` VALUES (494, '62', 'URBINA', 1, 71);
INSERT INTO `cc_parroquia` VALUES (495, '50', 'ATACAMES', 1, 72);
INSERT INTO `cc_parroquia` VALUES (496, '51', 'LA UNIÓN', 1, 72);
INSERT INTO `cc_parroquia` VALUES (497, '52', 'SÚA (CAB. EN LA BOCANA)', 1, 72);
INSERT INTO `cc_parroquia` VALUES (498, '53', 'TONCHIGÜE', 1, 72);
INSERT INTO `cc_parroquia` VALUES (499, '54', 'TONSUPA', 1, 72);
INSERT INTO `cc_parroquia` VALUES (500, '50', 'RIOVERDE', 1, 73);
INSERT INTO `cc_parroquia` VALUES (501, '51', 'CHONTADURO', 1, 73);
INSERT INTO `cc_parroquia` VALUES (502, '52', 'CHUMUNDÉ', 1, 73);
INSERT INTO `cc_parroquia` VALUES (503, '53', 'LAGARTO', 1, 73);
INSERT INTO `cc_parroquia` VALUES (504, '54', 'MONTALVO (CAB. EN HORQUETA)', 1, 73);
INSERT INTO `cc_parroquia` VALUES (505, '55', 'ROCAFUERTE', 1, 73);
INSERT INTO `cc_parroquia` VALUES (506, '50', 'LA CONCORDIA', 1, 74);
INSERT INTO `cc_parroquia` VALUES (507, '51', 'MONTERREY', 1, 74);
INSERT INTO `cc_parroquia` VALUES (508, '52', 'LA VILLEGAS', 1, 74);
INSERT INTO `cc_parroquia` VALUES (509, '53', 'PLAN PILOTO', 1, 74);
INSERT INTO `cc_parroquia` VALUES (510, '01', 'AYACUCHO', 1, 75);
INSERT INTO `cc_parroquia` VALUES (511, '02', 'BOLÍVAR (SAGRARIO)', 1, 75);
INSERT INTO `cc_parroquia` VALUES (512, '03', 'CARBO (CONCEPCIÓN)', 1, 75);
INSERT INTO `cc_parroquia` VALUES (513, '04', 'FEBRES CORDERO', 1, 75);
INSERT INTO `cc_parroquia` VALUES (514, '05', 'GARCÍA MORENO', 1, 75);
INSERT INTO `cc_parroquia` VALUES (515, '06', 'LETAMENDI', 1, 75);
INSERT INTO `cc_parroquia` VALUES (516, '07', 'NUEVE DE OCTUBRE', 1, 75);
INSERT INTO `cc_parroquia` VALUES (517, '08', 'OLMEDO (SAN ALEJO)', 1, 75);
INSERT INTO `cc_parroquia` VALUES (518, '09', 'ROCA', 1, 75);
INSERT INTO `cc_parroquia` VALUES (519, '10', 'ROCAFUERTE', 1, 75);
INSERT INTO `cc_parroquia` VALUES (520, '11', 'SUCRE', 1, 75);
INSERT INTO `cc_parroquia` VALUES (521, '12', 'TARQUI', 1, 75);
INSERT INTO `cc_parroquia` VALUES (522, '13', 'URDANETA', 1, 75);
INSERT INTO `cc_parroquia` VALUES (523, '14', 'XIMENA', 1, 75);
INSERT INTO `cc_parroquia` VALUES (524, '15', 'PASCUALES', 1, 75);
INSERT INTO `cc_parroquia` VALUES (525, '50', 'GUAYAQUIL', 1, 75);
INSERT INTO `cc_parroquia` VALUES (526, '51', 'CHONGÓN', 1, 75);
INSERT INTO `cc_parroquia` VALUES (527, '52', 'JUAN GÓMEZ RENDÓN (PROGRESO)', 1, 75);
INSERT INTO `cc_parroquia` VALUES (528, '53', 'MORRO', 1, 75);
INSERT INTO `cc_parroquia` VALUES (529, '54', 'PASCUALES', 1, 75);
INSERT INTO `cc_parroquia` VALUES (530, '55', 'PLAYAS (GRAL. VILLAMIL)', 1, 75);
INSERT INTO `cc_parroquia` VALUES (531, '56', 'POSORJA', 1, 75);
INSERT INTO `cc_parroquia` VALUES (532, '57', 'PUNÁ', 1, 75);
INSERT INTO `cc_parroquia` VALUES (533, '58', 'TENGUEL', 1, 75);
INSERT INTO `cc_parroquia` VALUES (534, '50', 'ALFREDO BAQUERIZO MORENO (JUJÁN)', 1, 76);
INSERT INTO `cc_parroquia` VALUES (535, '50', 'BALAO', 1, 77);
INSERT INTO `cc_parroquia` VALUES (536, '50', 'BALZAR', 1, 78);
INSERT INTO `cc_parroquia` VALUES (537, '50', 'COLIMES', 1, 79);
INSERT INTO `cc_parroquia` VALUES (538, '51', 'SAN JACINTO', 1, 79);
INSERT INTO `cc_parroquia` VALUES (539, '01', 'DAULE', 1, 80);
INSERT INTO `cc_parroquia` VALUES (540, '02', 'LA AURORA (SATÉLITE)', 1, 80);
INSERT INTO `cc_parroquia` VALUES (541, '03', 'BANIFE', 1, 80);
INSERT INTO `cc_parroquia` VALUES (542, '04', 'EMILIANO CAICEDO MARCOS', 1, 80);
INSERT INTO `cc_parroquia` VALUES (543, '05', 'MAGRO', 1, 80);
INSERT INTO `cc_parroquia` VALUES (544, '06', 'PADRE JUAN BAUTISTA AGUIRRE', 1, 80);
INSERT INTO `cc_parroquia` VALUES (545, '07', 'SANTA CLARA', 1, 80);
INSERT INTO `cc_parroquia` VALUES (546, '08', 'VICENTE PIEDRAHITA', 1, 80);
INSERT INTO `cc_parroquia` VALUES (547, '50', 'DAULE', 1, 80);
INSERT INTO `cc_parroquia` VALUES (548, '51', 'ISIDRO AYORA (SOLEDAD)', 1, 80);
INSERT INTO `cc_parroquia` VALUES (549, '52', 'JUAN BAUTISTA AGUIRRE (LOS TINTOS)', 1, 80);
INSERT INTO `cc_parroquia` VALUES (550, '53', 'LAUREL', 1, 80);
INSERT INTO `cc_parroquia` VALUES (551, '54', 'LIMONAL', 1, 80);
INSERT INTO `cc_parroquia` VALUES (552, '55', 'LOMAS DE SARGENTILLO', 1, 80);
INSERT INTO `cc_parroquia` VALUES (553, '56', 'LOS LOJAS (ENRIQUE BAQUERIZO MORENO)', 1, 80);
INSERT INTO `cc_parroquia` VALUES (554, '57', 'PIEDRAHITA (NOBOL)', 1, 80);
INSERT INTO `cc_parroquia` VALUES (555, '01', 'ELOY ALFARO (DURÁN)', 1, 81);
INSERT INTO `cc_parroquia` VALUES (556, '02', 'EL RECREO', 1, 81);
INSERT INTO `cc_parroquia` VALUES (557, '50', 'ELOY ALFARO (DURÁN)', 1, 81);
INSERT INTO `cc_parroquia` VALUES (558, '50', 'VELASCO IBARRA (EL EMPALME)', 1, 82);
INSERT INTO `cc_parroquia` VALUES (559, '51', 'GUAYAS (PUEBLO NUEVO)', 1, 82);
INSERT INTO `cc_parroquia` VALUES (560, '52', 'EL ROSARIO', 1, 82);
INSERT INTO `cc_parroquia` VALUES (561, '50', 'EL TRIUNFO', 1, 83);
INSERT INTO `cc_parroquia` VALUES (562, '50', 'MILAGRO', 1, 84);
INSERT INTO `cc_parroquia` VALUES (563, '51', 'CHOBO', 1, 84);
INSERT INTO `cc_parroquia` VALUES (564, '52', 'GENERAL ELIZALDE (BUCAY)', 1, 84);
INSERT INTO `cc_parroquia` VALUES (565, '53', 'MARISCAL SUCRE (HUAQUES)', 1, 84);
INSERT INTO `cc_parroquia` VALUES (566, '54', 'ROBERTO ASTUDILLO (CAB. EN CRUCE DE VENECIA)', 1, 84);
INSERT INTO `cc_parroquia` VALUES (567, '50', 'NARANJAL', 1, 85);
INSERT INTO `cc_parroquia` VALUES (568, '51', 'JESÚS MARÍA', 1, 85);
INSERT INTO `cc_parroquia` VALUES (569, '52', 'SAN CARLOS', 1, 85);
INSERT INTO `cc_parroquia` VALUES (570, '53', 'SANTA ROSA DE FLANDES', 1, 85);
INSERT INTO `cc_parroquia` VALUES (571, '54', 'TAURA', 1, 85);
INSERT INTO `cc_parroquia` VALUES (572, '50', 'NARANJITO', 1, 86);
INSERT INTO `cc_parroquia` VALUES (573, '50', 'PALESTINA', 1, 87);
INSERT INTO `cc_parroquia` VALUES (574, '50', 'PEDRO CARBO', 1, 88);
INSERT INTO `cc_parroquia` VALUES (575, '51', 'VALLE DE LA VIRGEN', 1, 88);
INSERT INTO `cc_parroquia` VALUES (576, '52', 'SABANILLA', 1, 88);
INSERT INTO `cc_parroquia` VALUES (577, '01', 'SAMBORONDÓN', 1, 89);
INSERT INTO `cc_parroquia` VALUES (578, '02', 'LA PUNTILLA (SATÉLITE)', 1, 89);
INSERT INTO `cc_parroquia` VALUES (579, '50', 'SAMBORONDÓN', 1, 89);
INSERT INTO `cc_parroquia` VALUES (580, '51', 'TARIFA', 1, 89);
INSERT INTO `cc_parroquia` VALUES (581, '50', 'SANTA LUCÍA', 1, 90);
INSERT INTO `cc_parroquia` VALUES (582, '01', 'BOCANA', 1, 91);
INSERT INTO `cc_parroquia` VALUES (583, '02', 'CANDILEJOS', 1, 91);
INSERT INTO `cc_parroquia` VALUES (584, '03', 'CENTRAL', 1, 91);
INSERT INTO `cc_parroquia` VALUES (585, '04', 'PARAÍSO', 1, 91);
INSERT INTO `cc_parroquia` VALUES (586, '05', 'SAN MATEO', 1, 91);
INSERT INTO `cc_parroquia` VALUES (587, '50', 'EL SALITRE (LAS RAMAS)', 1, 91);
INSERT INTO `cc_parroquia` VALUES (588, '51', 'GRAL. VERNAZA (DOS ESTEROS)', 1, 91);
INSERT INTO `cc_parroquia` VALUES (589, '52', 'LA VICTORIA (ÑAUZA)', 1, 91);
INSERT INTO `cc_parroquia` VALUES (590, '53', 'JUNQUILLAL', 1, 91);
INSERT INTO `cc_parroquia` VALUES (591, '50', 'SAN JACINTO DE YAGUACHI', 1, 92);
INSERT INTO `cc_parroquia` VALUES (592, '51', 'CRNEL. LORENZO DE GARAICOA (PEDREGAL)', 1, 92);
INSERT INTO `cc_parroquia` VALUES (593, '52', 'CRNEL. MARCELINO MARIDUEÑA (SAN CARLOS)', 1, 92);
INSERT INTO `cc_parroquia` VALUES (594, '53', 'GRAL. PEDRO J. MONTERO (BOLICHE)', 1, 92);
INSERT INTO `cc_parroquia` VALUES (595, '54', 'SIMÓN BOLÍVAR', 1, 92);
INSERT INTO `cc_parroquia` VALUES (596, '55', 'YAGUACHI VIEJO (CONE)', 1, 92);
INSERT INTO `cc_parroquia` VALUES (597, '56', 'VIRGEN DE FÁTIMA', 1, 92);
INSERT INTO `cc_parroquia` VALUES (598, '50', 'GENERAL VILLAMIL (PLAYAS)', 1, 93);
INSERT INTO `cc_parroquia` VALUES (599, '50', 'SIMÓN BOLÍVAR', 1, 94);
INSERT INTO `cc_parroquia` VALUES (600, '51', 'CRNEL.LORENZO DE GARAICOA (PEDREGAL)', 1, 94);
INSERT INTO `cc_parroquia` VALUES (601, '50', 'CORONEL MARCELINO MARIDUEÑA (SAN CARLOS)', 1, 95);
INSERT INTO `cc_parroquia` VALUES (602, '50', 'LOMAS DE SARGENTILLO', 1, 96);
INSERT INTO `cc_parroquia` VALUES (603, '51', 'ISIDRO AYORA (SOLEDAD)', 1, 96);
INSERT INTO `cc_parroquia` VALUES (604, '50', 'NARCISA DE JESÚS', 1, 97);
INSERT INTO `cc_parroquia` VALUES (605, '50', 'GENERAL ANTONIO ELIZALDE (BUCAY)', 1, 98);
INSERT INTO `cc_parroquia` VALUES (606, '50', 'ISIDRO AYORA', 1, 99);
INSERT INTO `cc_parroquia` VALUES (607, '01', 'CARANQUI', 1, 100);
INSERT INTO `cc_parroquia` VALUES (608, '02', 'GUAYAQUIL DE ALPACHACA', 1, 100);
INSERT INTO `cc_parroquia` VALUES (609, '03', 'SAGRARIO', 1, 100);
INSERT INTO `cc_parroquia` VALUES (610, '04', 'SAN FRANCISCO', 1, 100);
INSERT INTO `cc_parroquia` VALUES (611, '05', 'LA DOLOROSA DEL PRIORATO', 1, 100);
INSERT INTO `cc_parroquia` VALUES (612, '50', 'SAN MIGUEL DE IBARRA', 1, 100);
INSERT INTO `cc_parroquia` VALUES (613, '51', 'AMBUQUÍ', 1, 100);
INSERT INTO `cc_parroquia` VALUES (614, '52', 'ANGOCHAGUA', 1, 100);
INSERT INTO `cc_parroquia` VALUES (615, '53', 'CAROLINA', 1, 100);
INSERT INTO `cc_parroquia` VALUES (616, '54', 'LA ESPERANZA', 1, 100);
INSERT INTO `cc_parroquia` VALUES (617, '55', 'LITA', 1, 100);
INSERT INTO `cc_parroquia` VALUES (618, '56', 'SALINAS', 1, 100);
INSERT INTO `cc_parroquia` VALUES (619, '57', 'SAN ANTONIO', 1, 100);
INSERT INTO `cc_parroquia` VALUES (620, '01', 'ANDRADE MARÍN (LOURDES)', 1, 101);
INSERT INTO `cc_parroquia` VALUES (621, '02', 'ATUNTAQUI', 1, 101);
INSERT INTO `cc_parroquia` VALUES (622, '50', 'ATUNTAQUI', 1, 101);
INSERT INTO `cc_parroquia` VALUES (623, '51', 'IMBAYA (SAN LUIS DE COBUENDO)', 1, 101);
INSERT INTO `cc_parroquia` VALUES (624, '52', 'SAN FRANCISCO DE NATABUELA', 1, 101);
INSERT INTO `cc_parroquia` VALUES (625, '53', 'SAN JOSÉ DE CHALTURA', 1, 101);
INSERT INTO `cc_parroquia` VALUES (626, '54', 'SAN ROQUE', 1, 101);
INSERT INTO `cc_parroquia` VALUES (627, '01', 'SAGRARIO', 1, 102);
INSERT INTO `cc_parroquia` VALUES (628, '02', 'SAN FRANCISCO', 1, 102);
INSERT INTO `cc_parroquia` VALUES (629, '50', 'COTACACHI', 1, 102);
INSERT INTO `cc_parroquia` VALUES (630, '51', 'APUELA', 1, 102);
INSERT INTO `cc_parroquia` VALUES (631, '52', 'GARCÍA MORENO (LLURIMAGUA)', 1, 102);
INSERT INTO `cc_parroquia` VALUES (632, '53', 'IMANTAG', 1, 102);
INSERT INTO `cc_parroquia` VALUES (633, '54', 'PEÑAHERRERA', 1, 102);
INSERT INTO `cc_parroquia` VALUES (634, '55', 'PLAZA GUTIÉRREZ (CALVARIO)', 1, 102);
INSERT INTO `cc_parroquia` VALUES (635, '56', 'QUIROGA', 1, 102);
INSERT INTO `cc_parroquia` VALUES (636, '57', '6 DE JULIO DE CUELLAJE (CAB. EN CUELLAJE)', 1, 102);
INSERT INTO `cc_parroquia` VALUES (637, '58', 'VACAS GALINDO (EL CHURO) (CAB.EN SAN MIGUEL A', 1, 102);
INSERT INTO `cc_parroquia` VALUES (638, '01', 'JORDÁN', 1, 103);
INSERT INTO `cc_parroquia` VALUES (639, '02', 'SAN LUIS', 1, 103);
INSERT INTO `cc_parroquia` VALUES (640, '50', 'OTAVALO', 1, 103);
INSERT INTO `cc_parroquia` VALUES (641, '51', 'DR. MIGUEL EGAS CABEZAS (PEGUCHE)', 1, 103);
INSERT INTO `cc_parroquia` VALUES (642, '52', 'EUGENIO ESPEJO (CALPAQUÍ)', 1, 103);
INSERT INTO `cc_parroquia` VALUES (643, '53', 'GONZÁLEZ SUÁREZ', 1, 103);
INSERT INTO `cc_parroquia` VALUES (644, '54', 'PATAQUÍ', 1, 103);
INSERT INTO `cc_parroquia` VALUES (645, '55', 'SAN JOSÉ DE QUICHINCHE', 1, 103);
INSERT INTO `cc_parroquia` VALUES (646, '56', 'SAN JUAN DE ILUMÁN', 1, 103);
INSERT INTO `cc_parroquia` VALUES (647, '57', 'SAN PABLO', 1, 103);
INSERT INTO `cc_parroquia` VALUES (648, '58', 'SAN RAFAEL', 1, 103);
INSERT INTO `cc_parroquia` VALUES (649, '59', 'SELVA ALEGRE (CAB.EN SAN MIGUEL DE PAMPLONA)', 1, 103);
INSERT INTO `cc_parroquia` VALUES (650, '50', 'PIMAMPIRO', 1, 104);
INSERT INTO `cc_parroquia` VALUES (651, '51', 'CHUGÁ', 1, 104);
INSERT INTO `cc_parroquia` VALUES (652, '52', 'MARIANO ACOSTA', 1, 104);
INSERT INTO `cc_parroquia` VALUES (653, '53', 'SAN FRANCISCO DE SIGSIPAMBA', 1, 104);
INSERT INTO `cc_parroquia` VALUES (654, '50', 'URCUQUÍ CABECERA CANTONAL', 1, 105);
INSERT INTO `cc_parroquia` VALUES (655, '51', 'CAHUASQUÍ', 1, 105);
INSERT INTO `cc_parroquia` VALUES (656, '52', 'LA MERCED DE BUENOS AIRES', 1, 105);
INSERT INTO `cc_parroquia` VALUES (657, '53', 'PABLO ARENAS', 1, 105);
INSERT INTO `cc_parroquia` VALUES (658, '54', 'SAN BLAS', 1, 105);
INSERT INTO `cc_parroquia` VALUES (659, '55', 'TUMBABIRO', 1, 105);
INSERT INTO `cc_parroquia` VALUES (660, '01', 'EL SAGRARIO', 1, 106);
INSERT INTO `cc_parroquia` VALUES (661, '02', 'SAN SEBASTIÁN', 1, 106);
INSERT INTO `cc_parroquia` VALUES (662, '03', 'SUCRE', 1, 106);
INSERT INTO `cc_parroquia` VALUES (663, '04', 'VALLE', 1, 106);
INSERT INTO `cc_parroquia` VALUES (664, '50', 'LOJA', 1, 106);
INSERT INTO `cc_parroquia` VALUES (665, '51', 'CHANTACO', 1, 106);
INSERT INTO `cc_parroquia` VALUES (666, '52', 'CHUQUIRIBAMBA', 1, 106);
INSERT INTO `cc_parroquia` VALUES (667, '53', 'EL CISNE', 1, 106);
INSERT INTO `cc_parroquia` VALUES (668, '54', 'GUALEL', 1, 106);
INSERT INTO `cc_parroquia` VALUES (669, '55', 'JIMBILLA', 1, 106);
INSERT INTO `cc_parroquia` VALUES (670, '56', 'MALACATOS (VALLADOLID)', 1, 106);
INSERT INTO `cc_parroquia` VALUES (671, '57', 'SAN LUCAS', 1, 106);
INSERT INTO `cc_parroquia` VALUES (672, '58', 'SAN PEDRO DE VILCABAMBA', 1, 106);
INSERT INTO `cc_parroquia` VALUES (673, '59', 'SANTIAGO', 1, 106);
INSERT INTO `cc_parroquia` VALUES (674, '60', 'TAQUIL (MIGUEL RIOFRÍO)', 1, 106);
INSERT INTO `cc_parroquia` VALUES (675, '61', 'VILCABAMBA (VICTORIA)', 1, 106);
INSERT INTO `cc_parroquia` VALUES (676, '62', 'YANGANA (ARSENIO CASTILLO)', 1, 106);
INSERT INTO `cc_parroquia` VALUES (677, '63', 'QUINARA', 1, 106);
INSERT INTO `cc_parroquia` VALUES (678, '01', 'CARIAMANGA', 1, 107);
INSERT INTO `cc_parroquia` VALUES (679, '02', 'CHILE', 1, 107);
INSERT INTO `cc_parroquia` VALUES (680, '03', 'SAN VICENTE', 1, 107);
INSERT INTO `cc_parroquia` VALUES (681, '50', 'CARIAMANGA', 1, 107);
INSERT INTO `cc_parroquia` VALUES (682, '51', 'COLAISACA', 1, 107);
INSERT INTO `cc_parroquia` VALUES (683, '52', 'EL LUCERO', 1, 107);
INSERT INTO `cc_parroquia` VALUES (684, '53', 'UTUANA', 1, 107);
INSERT INTO `cc_parroquia` VALUES (685, '54', 'SANGUILLÍN', 1, 107);
INSERT INTO `cc_parroquia` VALUES (686, '01', 'CATAMAYO', 1, 108);
INSERT INTO `cc_parroquia` VALUES (687, '02', 'SAN JOSÉ', 1, 108);
INSERT INTO `cc_parroquia` VALUES (688, '50', 'CATAMAYO (LA TOMA)', 1, 108);
INSERT INTO `cc_parroquia` VALUES (689, '51', 'EL TAMBO', 1, 108);
INSERT INTO `cc_parroquia` VALUES (690, '52', 'GUAYQUICHUMA', 1, 108);
INSERT INTO `cc_parroquia` VALUES (691, '53', 'SAN PEDRO DE LA BENDITA', 1, 108);
INSERT INTO `cc_parroquia` VALUES (692, '54', 'ZAMBI', 1, 108);
INSERT INTO `cc_parroquia` VALUES (693, '50', 'CELICA', 1, 109);
INSERT INTO `cc_parroquia` VALUES (694, '51', 'CRUZPAMBA (CAB. EN CARLOS BUSTAMANTE)', 1, 109);
INSERT INTO `cc_parroquia` VALUES (695, '52', 'CHAQUINAL', 1, 109);
INSERT INTO `cc_parroquia` VALUES (696, '53', '12 DE DICIEMBRE (CAB. EN ACHIOTES)', 1, 109);
INSERT INTO `cc_parroquia` VALUES (697, '54', 'PINDAL (FEDERICO PÁEZ)', 1, 109);
INSERT INTO `cc_parroquia` VALUES (698, '55', 'POZUL (SAN JUAN DE POZUL)', 1, 109);
INSERT INTO `cc_parroquia` VALUES (699, '56', 'SABANILLA', 1, 109);
INSERT INTO `cc_parroquia` VALUES (700, '57', 'TNTE. MAXIMILIANO RODRÍGUEZ LOAIZA', 1, 109);
INSERT INTO `cc_parroquia` VALUES (701, '50', 'CHAGUARPAMBA', 1, 110);
INSERT INTO `cc_parroquia` VALUES (702, '51', 'BUENAVISTA', 1, 110);
INSERT INTO `cc_parroquia` VALUES (703, '52', 'EL ROSARIO', 1, 110);
INSERT INTO `cc_parroquia` VALUES (704, '53', 'SANTA RUFINA', 1, 110);
INSERT INTO `cc_parroquia` VALUES (705, '54', 'AMARILLOS', 1, 110);
INSERT INTO `cc_parroquia` VALUES (706, '50', 'AMALUZA', 1, 111);
INSERT INTO `cc_parroquia` VALUES (707, '51', 'BELLAVISTA', 1, 111);
INSERT INTO `cc_parroquia` VALUES (708, '52', 'JIMBURA', 1, 111);
INSERT INTO `cc_parroquia` VALUES (709, '53', 'SANTA TERESITA', 1, 111);
INSERT INTO `cc_parroquia` VALUES (710, '54', '27 DE ABRIL (CAB. EN LA NARANJA)', 1, 111);
INSERT INTO `cc_parroquia` VALUES (711, '55', 'EL INGENIO', 1, 111);
INSERT INTO `cc_parroquia` VALUES (712, '56', 'EL AIRO', 1, 111);
INSERT INTO `cc_parroquia` VALUES (713, '50', 'GONZANAMÁ', 1, 112);
INSERT INTO `cc_parroquia` VALUES (714, '51', 'CHANGAIMINA (LA LIBERTAD)', 1, 112);
INSERT INTO `cc_parroquia` VALUES (715, '52', 'FUNDOCHAMBA', 1, 112);
INSERT INTO `cc_parroquia` VALUES (716, '53', 'NAMBACOLA', 1, 112);
INSERT INTO `cc_parroquia` VALUES (717, '54', 'PURUNUMA (EGUIGUREN)', 1, 112);
INSERT INTO `cc_parroquia` VALUES (718, '55', 'QUILANGA (LA PAZ)', 1, 112);
INSERT INTO `cc_parroquia` VALUES (719, '56', 'SACAPALCA', 1, 112);
INSERT INTO `cc_parroquia` VALUES (720, '57', 'SAN ANTONIO DE LAS ARADAS (CAB. EN LAS ARADAS', 1, 112);
INSERT INTO `cc_parroquia` VALUES (721, '01', 'GENERAL ELOY ALFARO (SAN SEBASTIÁN)', 1, 113);
INSERT INTO `cc_parroquia` VALUES (722, '02', 'MACARÁ (MANUEL ENRIQUE RENGEL SUQUILANDA)', 1, 113);
INSERT INTO `cc_parroquia` VALUES (723, '50', 'MACARÁ', 1, 113);
INSERT INTO `cc_parroquia` VALUES (724, '51', 'LARAMA', 1, 113);
INSERT INTO `cc_parroquia` VALUES (725, '52', 'LA VICTORIA', 1, 113);
INSERT INTO `cc_parroquia` VALUES (726, '53', 'SABIANGO (LA CAPILLA)', 1, 113);
INSERT INTO `cc_parroquia` VALUES (727, '01', 'CATACOCHA', 1, 114);
INSERT INTO `cc_parroquia` VALUES (728, '02', 'LOURDES', 1, 114);
INSERT INTO `cc_parroquia` VALUES (729, '50', 'CATACOCHA', 1, 114);
INSERT INTO `cc_parroquia` VALUES (730, '51', 'CANGONAMÁ', 1, 114);
INSERT INTO `cc_parroquia` VALUES (731, '52', 'GUACHANAMÁ', 1, 114);
INSERT INTO `cc_parroquia` VALUES (732, '53', 'LA TINGUE', 1, 114);
INSERT INTO `cc_parroquia` VALUES (733, '54', 'LAURO GUERRERO', 1, 114);
INSERT INTO `cc_parroquia` VALUES (734, '55', 'OLMEDO (SANTA BÁRBARA)', 1, 114);
INSERT INTO `cc_parroquia` VALUES (735, '56', 'ORIANGA', 1, 114);
INSERT INTO `cc_parroquia` VALUES (736, '57', 'SAN ANTONIO', 1, 114);
INSERT INTO `cc_parroquia` VALUES (737, '58', 'CASANGA', 1, 114);
INSERT INTO `cc_parroquia` VALUES (738, '59', 'YAMANA', 1, 114);
INSERT INTO `cc_parroquia` VALUES (739, '50', 'ALAMOR', 1, 115);
INSERT INTO `cc_parroquia` VALUES (740, '51', 'CIANO', 1, 115);
INSERT INTO `cc_parroquia` VALUES (741, '52', 'EL ARENAL', 1, 115);
INSERT INTO `cc_parroquia` VALUES (742, '53', 'EL LIMO (MARIANA DE JESÚS)', 1, 115);
INSERT INTO `cc_parroquia` VALUES (743, '54', 'MERCADILLO', 1, 115);
INSERT INTO `cc_parroquia` VALUES (744, '55', 'VICENTINO', 1, 115);
INSERT INTO `cc_parroquia` VALUES (745, '50', 'SARAGURO', 1, 116);
INSERT INTO `cc_parroquia` VALUES (746, '51', 'EL PARAÍSO DE CELÉN', 1, 116);
INSERT INTO `cc_parroquia` VALUES (747, '52', 'EL TABLÓN', 1, 116);
INSERT INTO `cc_parroquia` VALUES (748, '53', 'LLUZHAPA', 1, 116);
INSERT INTO `cc_parroquia` VALUES (749, '54', 'MANÚ', 1, 116);
INSERT INTO `cc_parroquia` VALUES (750, '55', 'SAN ANTONIO DE QUMBE (CUMBE)', 1, 116);
INSERT INTO `cc_parroquia` VALUES (751, '56', 'SAN PABLO DE TENTA', 1, 116);
INSERT INTO `cc_parroquia` VALUES (752, '57', 'SAN SEBASTIÁN DE YÚLUC', 1, 116);
INSERT INTO `cc_parroquia` VALUES (753, '58', 'SELVA ALEGRE', 1, 116);
INSERT INTO `cc_parroquia` VALUES (754, '59', 'URDANETA (PAQUISHAPA)', 1, 116);
INSERT INTO `cc_parroquia` VALUES (755, '60', 'SUMAYPAMBA', 1, 116);
INSERT INTO `cc_parroquia` VALUES (756, '50', 'SOZORANGA', 1, 117);
INSERT INTO `cc_parroquia` VALUES (757, '51', 'NUEVA FÁTIMA', 1, 117);
INSERT INTO `cc_parroquia` VALUES (758, '52', 'TACAMOROS', 1, 117);
INSERT INTO `cc_parroquia` VALUES (759, '50', 'ZAPOTILLO', 1, 118);
INSERT INTO `cc_parroquia` VALUES (760, '51', 'MANGAHURCO (CAZADEROS)', 1, 118);
INSERT INTO `cc_parroquia` VALUES (761, '52', 'GARZAREAL', 1, 118);
INSERT INTO `cc_parroquia` VALUES (762, '53', 'LIMONES', 1, 118);
INSERT INTO `cc_parroquia` VALUES (763, '54', 'PALETILLAS', 1, 118);
INSERT INTO `cc_parroquia` VALUES (764, '55', 'BOLASPAMBA', 1, 118);
INSERT INTO `cc_parroquia` VALUES (765, '50', 'PINDAL', 1, 119);
INSERT INTO `cc_parroquia` VALUES (766, '51', 'CHAQUINAL', 1, 119);
INSERT INTO `cc_parroquia` VALUES (767, '52', '12 DE DICIEMBRE (CAB.EN ACHIOTES)', 1, 119);
INSERT INTO `cc_parroquia` VALUES (768, '53', 'MILAGROS', 1, 119);
INSERT INTO `cc_parroquia` VALUES (769, '50', 'QUILANGA', 1, 120);
INSERT INTO `cc_parroquia` VALUES (770, '51', 'FUNDOCHAMBA', 1, 120);
INSERT INTO `cc_parroquia` VALUES (771, '52', 'SAN ANTONIO DE LAS ARADAS (CAB. EN LAS ARADAS', 1, 120);
INSERT INTO `cc_parroquia` VALUES (772, '50', 'OLMEDO', 1, 121);
INSERT INTO `cc_parroquia` VALUES (773, '51', 'LA TINGUE', 1, 121);
INSERT INTO `cc_parroquia` VALUES (774, '01', 'CLEMENTE BAQUERIZO', 1, 122);
INSERT INTO `cc_parroquia` VALUES (775, '02', 'DR. CAMILO PONCE', 1, 122);
INSERT INTO `cc_parroquia` VALUES (776, '03', 'BARREIRO', 1, 122);
INSERT INTO `cc_parroquia` VALUES (777, '04', 'EL SALTO', 1, 122);
INSERT INTO `cc_parroquia` VALUES (778, '50', 'BABAHOYO', 1, 122);
INSERT INTO `cc_parroquia` VALUES (779, '51', 'BARREIRO (SANTA RITA)', 1, 122);
INSERT INTO `cc_parroquia` VALUES (780, '52', 'CARACOL', 1, 122);
INSERT INTO `cc_parroquia` VALUES (781, '53', 'FEBRES CORDERO (LAS JUNTAS)', 1, 122);
INSERT INTO `cc_parroquia` VALUES (782, '54', 'PIMOCHA', 1, 122);
INSERT INTO `cc_parroquia` VALUES (783, '55', 'LA UNIÓN', 1, 122);
INSERT INTO `cc_parroquia` VALUES (784, '50', 'BABA', 1, 123);
INSERT INTO `cc_parroquia` VALUES (785, '51', 'GUARE', 1, 123);
INSERT INTO `cc_parroquia` VALUES (786, '52', 'ISLA DE BEJUCAL', 1, 123);
INSERT INTO `cc_parroquia` VALUES (787, '50', 'MONTALVO', 1, 124);
INSERT INTO `cc_parroquia` VALUES (788, '50', 'PUEBLOVIEJO', 1, 125);
INSERT INTO `cc_parroquia` VALUES (789, '51', 'PUERTO PECHICHE', 1, 125);
INSERT INTO `cc_parroquia` VALUES (790, '52', 'SAN JUAN', 1, 125);
INSERT INTO `cc_parroquia` VALUES (791, '01', 'QUEVEDO', 1, 126);
INSERT INTO `cc_parroquia` VALUES (792, '02', 'SAN CAMILO', 1, 126);
INSERT INTO `cc_parroquia` VALUES (793, '03', 'SAN JOSÉ', 1, 126);
INSERT INTO `cc_parroquia` VALUES (794, '04', 'GUAYACÁN', 1, 126);
INSERT INTO `cc_parroquia` VALUES (795, '05', 'NICOLÁS INFANTE DÍAZ', 1, 126);
INSERT INTO `cc_parroquia` VALUES (796, '06', 'SAN CRISTÓBAL', 1, 126);
INSERT INTO `cc_parroquia` VALUES (797, '07', 'SIETE DE OCTUBRE', 1, 126);
INSERT INTO `cc_parroquia` VALUES (798, '08', '24 DE MAYO', 1, 126);
INSERT INTO `cc_parroquia` VALUES (799, '09', 'VENUS DEL RÍO QUEVEDO', 1, 126);
INSERT INTO `cc_parroquia` VALUES (800, '10', 'VIVA ALFARO', 1, 126);
INSERT INTO `cc_parroquia` VALUES (801, '50', 'QUEVEDO', 1, 126);
INSERT INTO `cc_parroquia` VALUES (802, '51', 'BUENA FÉ', 1, 126);
INSERT INTO `cc_parroquia` VALUES (803, '52', 'MOCACHE', 1, 126);
INSERT INTO `cc_parroquia` VALUES (804, '53', 'SAN CARLOS', 1, 126);
INSERT INTO `cc_parroquia` VALUES (805, '54', 'VALENCIA', 1, 126);
INSERT INTO `cc_parroquia` VALUES (806, '55', 'LA ESPERANZA', 1, 126);
INSERT INTO `cc_parroquia` VALUES (807, '50', 'CATARAMA', 1, 127);
INSERT INTO `cc_parroquia` VALUES (808, '51', 'RICAURTE', 1, 127);
INSERT INTO `cc_parroquia` VALUES (809, '01', '10 DE NOVIEMBRE', 1, 128);
INSERT INTO `cc_parroquia` VALUES (810, '50', 'VENTANAS', 1, 128);
INSERT INTO `cc_parroquia` VALUES (811, '51', 'QUINSALOMA', 1, 128);
INSERT INTO `cc_parroquia` VALUES (812, '52', 'ZAPOTAL', 1, 128);
INSERT INTO `cc_parroquia` VALUES (813, '53', 'CHACARITA', 1, 128);
INSERT INTO `cc_parroquia` VALUES (814, '54', 'LOS ÁNGELES', 1, 128);
INSERT INTO `cc_parroquia` VALUES (815, '50', 'VINCES', 1, 129);
INSERT INTO `cc_parroquia` VALUES (816, '51', 'ANTONIO SOTOMAYOR (CAB. EN PLAYAS DE VINCES)', 1, 129);
INSERT INTO `cc_parroquia` VALUES (817, '52', 'PALENQUE', 1, 129);
INSERT INTO `cc_parroquia` VALUES (818, '50', 'PALENQUE', 1, 130);
INSERT INTO `cc_parroquia` VALUES (819, '01', 'SAN JACINTO DE BUENA FÉ', 1, 131);
INSERT INTO `cc_parroquia` VALUES (820, '02', '7 DE AGOSTO', 1, 131);
INSERT INTO `cc_parroquia` VALUES (821, '03', '11 DE OCTUBRE', 1, 131);
INSERT INTO `cc_parroquia` VALUES (822, '50', 'SAN JACINTO DE BUENA FÉ', 1, 131);
INSERT INTO `cc_parroquia` VALUES (823, '51', 'PATRICIA PILAR', 1, 131);
INSERT INTO `cc_parroquia` VALUES (824, '50', 'VALENCIA', 1, 132);
INSERT INTO `cc_parroquia` VALUES (825, '50', 'MOCACHE', 1, 133);
INSERT INTO `cc_parroquia` VALUES (826, '50', 'QUINSALOMA', 1, 134);
INSERT INTO `cc_parroquia` VALUES (827, '01', 'PORTOVIEJO', 1, 135);
INSERT INTO `cc_parroquia` VALUES (828, '02', '12 DE MARZO', 1, 135);
INSERT INTO `cc_parroquia` VALUES (829, '03', 'COLÓN', 1, 135);
INSERT INTO `cc_parroquia` VALUES (830, '04', 'PICOAZÁ', 1, 135);
INSERT INTO `cc_parroquia` VALUES (831, '05', 'SAN PABLO', 1, 135);
INSERT INTO `cc_parroquia` VALUES (832, '06', 'ANDRÉS DE VERA', 1, 135);
INSERT INTO `cc_parroquia` VALUES (833, '07', 'FRANCISCO PACHECO', 1, 135);
INSERT INTO `cc_parroquia` VALUES (834, '08', '18 DE OCTUBRE', 1, 135);
INSERT INTO `cc_parroquia` VALUES (835, '09', 'SIMÓN BOLÍVAR', 1, 135);
INSERT INTO `cc_parroquia` VALUES (836, '50', 'PORTOVIEJO', 1, 135);
INSERT INTO `cc_parroquia` VALUES (837, '51', 'ABDÓN CALDERÓN (SAN FRANCISCO)', 1, 135);
INSERT INTO `cc_parroquia` VALUES (838, '52', 'ALHAJUELA (BAJO GRANDE)', 1, 135);
INSERT INTO `cc_parroquia` VALUES (839, '53', 'CRUCITA', 1, 135);
INSERT INTO `cc_parroquia` VALUES (840, '54', 'PUEBLO NUEVO', 1, 135);
INSERT INTO `cc_parroquia` VALUES (841, '55', 'RIOCHICO (RÍO CHICO)', 1, 135);
INSERT INTO `cc_parroquia` VALUES (842, '56', 'SAN PLÁCIDO', 1, 135);
INSERT INTO `cc_parroquia` VALUES (843, '57', 'CHIRIJOS', 1, 135);
INSERT INTO `cc_parroquia` VALUES (844, '50', 'CALCETA', 1, 31);
INSERT INTO `cc_parroquia` VALUES (845, '51', 'MEMBRILLO', 1, 31);
INSERT INTO `cc_parroquia` VALUES (846, '52', 'QUIROGA', 1, 31);
INSERT INTO `cc_parroquia` VALUES (847, '01', 'CHONE', 1, 137);
INSERT INTO `cc_parroquia` VALUES (848, '02', 'SANTA RITA', 1, 137);
INSERT INTO `cc_parroquia` VALUES (849, '50', 'CHONE', 1, 137);
INSERT INTO `cc_parroquia` VALUES (850, '51', 'BOYACÁ', 1, 137);
INSERT INTO `cc_parroquia` VALUES (851, '52', 'CANUTO', 1, 137);
INSERT INTO `cc_parroquia` VALUES (852, '53', 'CONVENTO', 1, 137);
INSERT INTO `cc_parroquia` VALUES (853, '54', 'CHIBUNGA', 1, 137);
INSERT INTO `cc_parroquia` VALUES (854, '55', 'ELOY ALFARO', 1, 137);
INSERT INTO `cc_parroquia` VALUES (855, '56', 'RICAURTE', 1, 137);
INSERT INTO `cc_parroquia` VALUES (856, '57', 'SAN ANTONIO', 1, 137);
INSERT INTO `cc_parroquia` VALUES (857, '01', 'EL CARMEN', 1, 138);
INSERT INTO `cc_parroquia` VALUES (858, '02', '4 DE DICIEMBRE', 1, 138);
INSERT INTO `cc_parroquia` VALUES (859, '50', 'EL CARMEN', 1, 138);
INSERT INTO `cc_parroquia` VALUES (860, '51', 'WILFRIDO LOOR MOREIRA (MAICITO)', 1, 138);
INSERT INTO `cc_parroquia` VALUES (861, '52', 'SAN PEDRO DE SUMA', 1, 138);
INSERT INTO `cc_parroquia` VALUES (862, '50', 'FLAVIO ALFARO', 1, 139);
INSERT INTO `cc_parroquia` VALUES (863, '51', 'SAN FRANCISCO DE NOVILLO (CAB. EN', 1, 139);
INSERT INTO `cc_parroquia` VALUES (864, '52', 'ZAPALLO', 1, 139);
INSERT INTO `cc_parroquia` VALUES (865, '01', 'DR. MIGUEL MORÁN LUCIO', 1, 140);
INSERT INTO `cc_parroquia` VALUES (866, '02', 'MANUEL INOCENCIO PARRALES Y GUALE', 1, 140);
INSERT INTO `cc_parroquia` VALUES (867, '03', 'SAN LORENZO DE JIPIJAPA', 1, 140);
INSERT INTO `cc_parroquia` VALUES (868, '50', 'JIPIJAPA', 1, 140);
INSERT INTO `cc_parroquia` VALUES (869, '51', 'AMÉRICA', 1, 140);
INSERT INTO `cc_parroquia` VALUES (870, '52', 'EL ANEGADO (CAB. EN ELOY ALFARO)', 1, 140);
INSERT INTO `cc_parroquia` VALUES (871, '53', 'JULCUY', 1, 140);
INSERT INTO `cc_parroquia` VALUES (872, '54', 'LA UNIÓN', 1, 140);
INSERT INTO `cc_parroquia` VALUES (873, '55', 'MACHALILLA', 1, 140);
INSERT INTO `cc_parroquia` VALUES (874, '56', 'MEMBRILLAL', 1, 140);
INSERT INTO `cc_parroquia` VALUES (875, '57', 'PEDRO PABLO GÓMEZ', 1, 140);
INSERT INTO `cc_parroquia` VALUES (876, '58', 'PUERTO DE CAYO', 1, 140);
INSERT INTO `cc_parroquia` VALUES (877, '59', 'PUERTO LÓPEZ', 1, 140);
INSERT INTO `cc_parroquia` VALUES (878, '50', 'JUNÍN', 1, 141);
INSERT INTO `cc_parroquia` VALUES (879, '01', 'LOS ESTEROS', 1, 142);
INSERT INTO `cc_parroquia` VALUES (880, '02', 'MANTA', 1, 142);
INSERT INTO `cc_parroquia` VALUES (881, '03', 'SAN MATEO', 1, 142);
INSERT INTO `cc_parroquia` VALUES (882, '04', 'TARQUI', 1, 142);
INSERT INTO `cc_parroquia` VALUES (883, '05', 'ELOY ALFARO', 1, 142);
INSERT INTO `cc_parroquia` VALUES (884, '50', 'MANTA', 1, 142);
INSERT INTO `cc_parroquia` VALUES (885, '51', 'SAN LORENZO', 1, 142);
INSERT INTO `cc_parroquia` VALUES (886, '52', 'SANTA MARIANITA (BOCA DE PACOCHE)', 1, 142);
INSERT INTO `cc_parroquia` VALUES (887, '01', 'ANIBAL SAN ANDRÉS', 1, 143);
INSERT INTO `cc_parroquia` VALUES (888, '02', 'MONTECRISTI', 1, 143);
INSERT INTO `cc_parroquia` VALUES (889, '03', 'EL COLORADO', 1, 143);
INSERT INTO `cc_parroquia` VALUES (890, '04', 'GENERAL ELOY ALFARO', 1, 143);
INSERT INTO `cc_parroquia` VALUES (891, '05', 'LEONIDAS PROAÑO', 1, 143);
INSERT INTO `cc_parroquia` VALUES (892, '50', 'MONTECRISTI', 1, 143);
INSERT INTO `cc_parroquia` VALUES (893, '51', 'JARAMIJÓ', 1, 143);
INSERT INTO `cc_parroquia` VALUES (894, '52', 'LA PILA', 1, 143);
INSERT INTO `cc_parroquia` VALUES (895, '50', 'PAJÁN', 1, 144);
INSERT INTO `cc_parroquia` VALUES (896, '51', 'CAMPOZANO (LA PALMA DE PAJÁN)', 1, 144);
INSERT INTO `cc_parroquia` VALUES (897, '52', 'CASCOL', 1, 144);
INSERT INTO `cc_parroquia` VALUES (898, '53', 'GUALE', 1, 144);
INSERT INTO `cc_parroquia` VALUES (899, '54', 'LASCANO', 1, 144);
INSERT INTO `cc_parroquia` VALUES (900, '50', 'PICHINCHA', 1, 145);
INSERT INTO `cc_parroquia` VALUES (901, '51', 'BARRAGANETE', 1, 145);
INSERT INTO `cc_parroquia` VALUES (902, '52', 'SAN SEBASTIÁN', 1, 145);
INSERT INTO `cc_parroquia` VALUES (903, '50', 'ROCAFUERTE', 1, 146);
INSERT INTO `cc_parroquia` VALUES (904, '01', 'SANTA ANA', 1, 147);
INSERT INTO `cc_parroquia` VALUES (905, '02', 'LODANA', 1, 147);
INSERT INTO `cc_parroquia` VALUES (906, '50', 'SANTA ANA DE VUELTA LARGA', 1, 147);
INSERT INTO `cc_parroquia` VALUES (907, '51', 'AYACUCHO', 1, 147);
INSERT INTO `cc_parroquia` VALUES (908, '52', 'HONORATO VÁSQUEZ (CAB. EN VÁSQUEZ)', 1, 147);
INSERT INTO `cc_parroquia` VALUES (909, '53', 'LA UNIÓN', 1, 147);
INSERT INTO `cc_parroquia` VALUES (910, '54', 'OLMEDO', 1, 147);
INSERT INTO `cc_parroquia` VALUES (911, '55', 'SAN PABLO (CAB. EN PUEBLO NUEVO)', 1, 147);
INSERT INTO `cc_parroquia` VALUES (912, '01', 'BAHÍA DE CARÁQUEZ', 1, 148);
INSERT INTO `cc_parroquia` VALUES (913, '02', 'LEONIDAS PLAZA GUTIÉRREZ', 1, 148);
INSERT INTO `cc_parroquia` VALUES (914, '50', 'BAHÍA DE CARÁQUEZ', 1, 148);
INSERT INTO `cc_parroquia` VALUES (915, '51', 'CANOA', 1, 148);
INSERT INTO `cc_parroquia` VALUES (916, '52', 'COJIMÍES', 1, 148);
INSERT INTO `cc_parroquia` VALUES (917, '53', 'CHARAPOTÓ', 1, 148);
INSERT INTO `cc_parroquia` VALUES (918, '54', '10 DE AGOSTO', 1, 148);
INSERT INTO `cc_parroquia` VALUES (919, '55', 'JAMA', 1, 148);
INSERT INTO `cc_parroquia` VALUES (920, '56', 'PEDERNALES', 1, 148);
INSERT INTO `cc_parroquia` VALUES (921, '57', 'SAN ISIDRO', 1, 148);
INSERT INTO `cc_parroquia` VALUES (922, '58', 'SAN VICENTE', 1, 148);
INSERT INTO `cc_parroquia` VALUES (923, '50', 'TOSAGUA', 1, 149);
INSERT INTO `cc_parroquia` VALUES (924, '51', 'BACHILLERO', 1, 149);
INSERT INTO `cc_parroquia` VALUES (925, '52', 'ANGEL PEDRO GILER (LA ESTANCILLA)', 1, 149);
INSERT INTO `cc_parroquia` VALUES (926, '50', 'SUCRE', 1, 150);
INSERT INTO `cc_parroquia` VALUES (927, '51', 'BELLAVISTA', 1, 150);
INSERT INTO `cc_parroquia` VALUES (928, '52', 'NOBOA', 1, 150);
INSERT INTO `cc_parroquia` VALUES (929, '53', 'ARQ. SIXTO DURÁN BALLÉN', 1, 150);
INSERT INTO `cc_parroquia` VALUES (930, '50', 'PEDERNALES', 1, 151);
INSERT INTO `cc_parroquia` VALUES (931, '51', 'COJIMÍES', 1, 151);
INSERT INTO `cc_parroquia` VALUES (932, '52', '10 DE AGOSTO', 1, 151);
INSERT INTO `cc_parroquia` VALUES (933, '53', 'ATAHUALPA', 1, 151);
INSERT INTO `cc_parroquia` VALUES (934, '50', 'OLMEDO', 1, 121);
INSERT INTO `cc_parroquia` VALUES (935, '50', 'PUERTO LÓPEZ', 1, 153);
INSERT INTO `cc_parroquia` VALUES (936, '51', 'MACHALILLA', 1, 153);
INSERT INTO `cc_parroquia` VALUES (937, '52', 'SALANGO', 1, 153);
INSERT INTO `cc_parroquia` VALUES (938, '50', 'JAMA', 1, 154);
INSERT INTO `cc_parroquia` VALUES (939, '50', 'JARAMIJÓ', 1, 155);
INSERT INTO `cc_parroquia` VALUES (940, '50', 'SAN VICENTE', 1, 156);
INSERT INTO `cc_parroquia` VALUES (941, '51', 'CANOA', 1, 156);
INSERT INTO `cc_parroquia` VALUES (942, '50', 'MACAS', 1, 157);
INSERT INTO `cc_parroquia` VALUES (943, '51', 'ALSHI (CAB. EN 9 DE OCTUBRE)', 1, 157);
INSERT INTO `cc_parroquia` VALUES (944, '52', 'CHIGUAZA', 1, 157);
INSERT INTO `cc_parroquia` VALUES (945, '53', 'GENERAL PROAÑO', 1, 157);
INSERT INTO `cc_parroquia` VALUES (946, '54', 'HUASAGA (CAB.EN WAMPUIK)', 1, 157);
INSERT INTO `cc_parroquia` VALUES (947, '55', 'MACUMA', 1, 157);
INSERT INTO `cc_parroquia` VALUES (948, '56', 'SAN ISIDRO', 1, 157);
INSERT INTO `cc_parroquia` VALUES (949, '57', 'SEVILLA DON BOSCO', 1, 157);
INSERT INTO `cc_parroquia` VALUES (950, '58', 'SINAÍ', 1, 157);
INSERT INTO `cc_parroquia` VALUES (951, '59', 'TAISHA', 1, 157);
INSERT INTO `cc_parroquia` VALUES (952, '60', 'ZUÑA (ZÚÑAC)', 1, 157);
INSERT INTO `cc_parroquia` VALUES (953, '61', 'TUUTINENTZA', 1, 157);
INSERT INTO `cc_parroquia` VALUES (954, '62', 'CUCHAENTZA', 1, 157);
INSERT INTO `cc_parroquia` VALUES (955, '63', 'SAN JOSÉ DE MORONA', 1, 157);
INSERT INTO `cc_parroquia` VALUES (956, '64', 'RÍO BLANCO', 1, 157);
INSERT INTO `cc_parroquia` VALUES (957, '01', 'GUALAQUIZA', 1, 158);
INSERT INTO `cc_parroquia` VALUES (958, '02', 'MERCEDES MOLINA', 1, 158);
INSERT INTO `cc_parroquia` VALUES (959, '50', 'GUALAQUIZA', 1, 158);
INSERT INTO `cc_parroquia` VALUES (960, '51', 'AMAZONAS (ROSARIO DE CUYES)', 1, 158);
INSERT INTO `cc_parroquia` VALUES (961, '52', 'BERMEJOS', 1, 158);
INSERT INTO `cc_parroquia` VALUES (962, '53', 'BOMBOIZA', 1, 158);
INSERT INTO `cc_parroquia` VALUES (963, '54', 'CHIGÜINDA', 1, 158);
INSERT INTO `cc_parroquia` VALUES (964, '55', 'EL ROSARIO', 1, 158);
INSERT INTO `cc_parroquia` VALUES (965, '56', 'NUEVA TARQUI', 1, 158);
INSERT INTO `cc_parroquia` VALUES (966, '57', 'SAN MIGUEL DE CUYES', 1, 158);
INSERT INTO `cc_parroquia` VALUES (967, '58', 'EL IDEAL', 1, 158);
INSERT INTO `cc_parroquia` VALUES (968, '50', 'GENERAL LEONIDAS PLAZA GUTIÉRREZ (LIMÓN)', 1, 159);
INSERT INTO `cc_parroquia` VALUES (969, '51', 'INDANZA', 1, 159);
INSERT INTO `cc_parroquia` VALUES (970, '52', 'PAN DE AZÚCAR', 1, 159);
INSERT INTO `cc_parroquia` VALUES (971, '53', 'SAN ANTONIO (CAB. EN SAN ANTONIO CENTRO', 1, 159);
INSERT INTO `cc_parroquia` VALUES (972, '54', 'SAN CARLOS DE LIMÓN (SAN CARLOS DEL', 1, 159);
INSERT INTO `cc_parroquia` VALUES (973, '55', 'SAN JUAN BOSCO', 1, 159);
INSERT INTO `cc_parroquia` VALUES (974, '56', 'SAN MIGUEL DE CONCHAY', 1, 159);
INSERT INTO `cc_parroquia` VALUES (975, '57', 'SANTA SUSANA DE CHIVIAZA (CAB. EN CHIVIAZA)', 1, 159);
INSERT INTO `cc_parroquia` VALUES (976, '58', 'YUNGANZA (CAB. EN EL ROSARIO)', 1, 159);
INSERT INTO `cc_parroquia` VALUES (977, '50', 'PALORA (METZERA)', 1, 160);
INSERT INTO `cc_parroquia` VALUES (978, '51', 'ARAPICOS', 1, 160);
INSERT INTO `cc_parroquia` VALUES (979, '52', 'CUMANDÁ (CAB. EN COLONIA AGRÍCOLA SEVILLA DEL', 1, 160);
INSERT INTO `cc_parroquia` VALUES (980, '53', 'HUAMBOYA', 1, 160);
INSERT INTO `cc_parroquia` VALUES (981, '54', 'SANGAY (CAB. EN NAYAMANACA)', 1, 160);
INSERT INTO `cc_parroquia` VALUES (982, '50', 'SANTIAGO DE MÉNDEZ', 1, 161);
INSERT INTO `cc_parroquia` VALUES (983, '51', 'COPAL', 1, 161);
INSERT INTO `cc_parroquia` VALUES (984, '52', 'CHUPIANZA', 1, 161);
INSERT INTO `cc_parroquia` VALUES (985, '53', 'PATUCA', 1, 161);
INSERT INTO `cc_parroquia` VALUES (986, '54', 'SAN LUIS DE EL ACHO (CAB. EN EL ACHO)', 1, 161);
INSERT INTO `cc_parroquia` VALUES (987, '55', 'SANTIAGO', 1, 161);
INSERT INTO `cc_parroquia` VALUES (988, '56', 'TAYUZA', 1, 161);
INSERT INTO `cc_parroquia` VALUES (989, '57', 'SAN FRANCISCO DE CHINIMBIMI', 1, 161);
INSERT INTO `cc_parroquia` VALUES (990, '50', 'SUCÚA', 1, 162);
INSERT INTO `cc_parroquia` VALUES (991, '51', 'ASUNCIÓN', 1, 162);
INSERT INTO `cc_parroquia` VALUES (992, '52', 'HUAMBI', 1, 162);
INSERT INTO `cc_parroquia` VALUES (993, '53', 'LOGROÑO', 1, 162);
INSERT INTO `cc_parroquia` VALUES (994, '54', 'YAUPI', 1, 162);
INSERT INTO `cc_parroquia` VALUES (995, '55', 'SANTA MARIANITA DE JESÚS', 1, 162);
INSERT INTO `cc_parroquia` VALUES (996, '50', 'HUAMBOYA', 1, 163);
INSERT INTO `cc_parroquia` VALUES (997, '51', 'CHIGUAZA', 1, 163);
INSERT INTO `cc_parroquia` VALUES (998, '52', 'PABLO SEXTO', 1, 163);
INSERT INTO `cc_parroquia` VALUES (999, '50', 'SAN JUAN BOSCO', 1, 164);
INSERT INTO `cc_parroquia` VALUES (1000, '51', 'PAN DE AZÚCAR', 1, 164);
INSERT INTO `cc_parroquia` VALUES (1001, '52', 'SAN CARLOS DE LIMÓN', 1, 164);
INSERT INTO `cc_parroquia` VALUES (1002, '53', 'SAN JACINTO DE WAKAMBEIS', 1, 164);
INSERT INTO `cc_parroquia` VALUES (1003, '54', 'SANTIAGO DE PANANZA', 1, 164);
INSERT INTO `cc_parroquia` VALUES (1004, '50', 'TAISHA', 1, 165);
INSERT INTO `cc_parroquia` VALUES (1005, '51', 'HUASAGA (CAB. EN WAMPUIK)', 1, 165);
INSERT INTO `cc_parroquia` VALUES (1006, '52', 'MACUMA', 1, 165);
INSERT INTO `cc_parroquia` VALUES (1007, '53', 'TUUTINENTZA', 1, 165);
INSERT INTO `cc_parroquia` VALUES (1008, '54', 'PUMPUENTSA', 1, 165);
INSERT INTO `cc_parroquia` VALUES (1009, '50', 'LOGROÑO', 1, 166);
INSERT INTO `cc_parroquia` VALUES (1010, '51', 'YAUPI', 1, 166);
INSERT INTO `cc_parroquia` VALUES (1011, '52', 'SHIMPIS', 1, 166);
INSERT INTO `cc_parroquia` VALUES (1012, '50', 'PABLO SEXTO', 1, 167);
INSERT INTO `cc_parroquia` VALUES (1013, '50', 'SANTIAGO', 1, 168);
INSERT INTO `cc_parroquia` VALUES (1014, '51', 'SAN JOSÉ DE MORONA', 1, 168);
INSERT INTO `cc_parroquia` VALUES (1015, '50', 'TENA', 1, 169);
INSERT INTO `cc_parroquia` VALUES (1016, '51', 'AHUANO', 1, 169);
INSERT INTO `cc_parroquia` VALUES (1017, '52', 'CARLOS JULIO AROSEMENA TOLA (ZATZA-YACU)', 1, 169);
INSERT INTO `cc_parroquia` VALUES (1018, '53', 'CHONTAPUNTA', 1, 169);
INSERT INTO `cc_parroquia` VALUES (1019, '54', 'PANO', 1, 169);
INSERT INTO `cc_parroquia` VALUES (1020, '55', 'PUERTO MISAHUALLI', 1, 169);
INSERT INTO `cc_parroquia` VALUES (1021, '56', 'PUERTO NAPO', 1, 169);
INSERT INTO `cc_parroquia` VALUES (1022, '57', 'TÁLAG', 1, 169);
INSERT INTO `cc_parroquia` VALUES (1023, '58', 'SAN JUAN DE MUYUNA', 1, 169);
INSERT INTO `cc_parroquia` VALUES (1024, '50', 'ARCHIDONA', 1, 170);
INSERT INTO `cc_parroquia` VALUES (1025, '51', 'AVILA', 1, 170);
INSERT INTO `cc_parroquia` VALUES (1026, '52', 'COTUNDO', 1, 170);
INSERT INTO `cc_parroquia` VALUES (1027, '53', 'LORETO', 1, 170);
INSERT INTO `cc_parroquia` VALUES (1028, '54', 'SAN PABLO DE USHPAYACU', 1, 170);
INSERT INTO `cc_parroquia` VALUES (1029, '55', 'PUERTO MURIALDO', 1, 170);
INSERT INTO `cc_parroquia` VALUES (1030, '50', 'EL CHACO', 1, 171);
INSERT INTO `cc_parroquia` VALUES (1031, '51', 'GONZALO DíAZ DE PINEDA (EL BOMBÓN)', 1, 171);
INSERT INTO `cc_parroquia` VALUES (1032, '52', 'LINARES', 1, 171);
INSERT INTO `cc_parroquia` VALUES (1033, '53', 'OYACACHI', 1, 171);
INSERT INTO `cc_parroquia` VALUES (1034, '54', 'SANTA ROSA', 1, 171);
INSERT INTO `cc_parroquia` VALUES (1035, '55', 'SARDINAS', 1, 171);
INSERT INTO `cc_parroquia` VALUES (1036, '50', 'BAEZA', 1, 172);
INSERT INTO `cc_parroquia` VALUES (1037, '51', 'COSANGA', 1, 172);
INSERT INTO `cc_parroquia` VALUES (1038, '52', 'CUYUJA', 1, 172);
INSERT INTO `cc_parroquia` VALUES (1039, '53', 'PAPALLACTA', 1, 172);
INSERT INTO `cc_parroquia` VALUES (1040, '54', 'SAN FRANCISCO DE BORJA (VIRGILIO DÁVILA)', 1, 172);
INSERT INTO `cc_parroquia` VALUES (1041, '55', 'SAN JOSÉ DEL PAYAMINO', 1, 172);
INSERT INTO `cc_parroquia` VALUES (1042, '56', 'SUMACO', 1, 172);
INSERT INTO `cc_parroquia` VALUES (1043, '50', 'CARLOS JULIO AROSEMENA TOLA', 1, 173);
INSERT INTO `cc_parroquia` VALUES (1044, '50', 'PUYO', 1, 174);
INSERT INTO `cc_parroquia` VALUES (1045, '51', 'ARAJUNO', 1, 174);
INSERT INTO `cc_parroquia` VALUES (1046, '52', 'CANELOS', 1, 174);
INSERT INTO `cc_parroquia` VALUES (1047, '53', 'CURARAY', 1, 174);
INSERT INTO `cc_parroquia` VALUES (1048, '54', 'DIEZ DE AGOSTO', 1, 174);
INSERT INTO `cc_parroquia` VALUES (1049, '55', 'FÁTIMA', 1, 174);
INSERT INTO `cc_parroquia` VALUES (1050, '56', 'MONTALVO (ANDOAS)', 1, 174);
INSERT INTO `cc_parroquia` VALUES (1051, '57', 'POMONA', 1, 174);
INSERT INTO `cc_parroquia` VALUES (1052, '58', 'RÍO CORRIENTES', 1, 174);
INSERT INTO `cc_parroquia` VALUES (1053, '59', 'RÍO TIGRE', 1, 174);
INSERT INTO `cc_parroquia` VALUES (1054, '60', 'SANTA CLARA', 1, 174);
INSERT INTO `cc_parroquia` VALUES (1055, '61', 'SARAYACU', 1, 174);
INSERT INTO `cc_parroquia` VALUES (1056, '62', 'SIMÓN BOLÍVAR (CAB. EN MUSHULLACTA)', 1, 174);
INSERT INTO `cc_parroquia` VALUES (1057, '63', 'TARQUI', 1, 174);
INSERT INTO `cc_parroquia` VALUES (1058, '64', 'TENIENTE HUGO ORTIZ', 1, 174);
INSERT INTO `cc_parroquia` VALUES (1059, '65', 'VERACRUZ (INDILLAMA) (CAB. EN INDILLAMA)', 1, 174);
INSERT INTO `cc_parroquia` VALUES (1060, '66', 'EL TRIUNFO', 1, 174);
INSERT INTO `cc_parroquia` VALUES (1061, '50', 'MERA', 1, 175);
INSERT INTO `cc_parroquia` VALUES (1062, '51', 'MADRE TIERRA', 1, 175);
INSERT INTO `cc_parroquia` VALUES (1063, '52', 'SHELL', 1, 175);
INSERT INTO `cc_parroquia` VALUES (1064, '50', 'SANTA CLARA', 1, 176);
INSERT INTO `cc_parroquia` VALUES (1065, '51', 'SAN JOSÉ', 1, 176);
INSERT INTO `cc_parroquia` VALUES (1066, '50', 'ARAJUNO', 1, 177);
INSERT INTO `cc_parroquia` VALUES (1067, '51', 'CURARAY', 1, 177);
INSERT INTO `cc_parroquia` VALUES (1068, '01', 'BELISARIO QUEVEDO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1069, '02', 'CARCELÉN', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1070, '03', 'CENTRO HISTÓRICO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1071, '04', 'COCHAPAMBA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1072, '05', 'COMITÉ DEL PUEBLO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1073, '06', 'COTOCOLLAO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1074, '07', 'CHILIBULO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1075, '08', 'CHILLOGALLO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1076, '09', 'CHIMBACALLE', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1077, '10', 'EL CONDADO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1078, '11', 'GUAMANÍ', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1079, '12', 'IÑAQUITO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1080, '13', 'ITCHIMBÍA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1081, '14', 'JIPIJAPA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1082, '15', 'KENNEDY', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1083, '16', 'LA ARGELIA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1084, '17', 'LA CONCEPCIÓN', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1085, '18', 'LA ECUATORIANA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1086, '19', 'LA FERROVIARIA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1087, '20', 'LA LIBERTAD', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1088, '21', 'LA MAGDALENA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1089, '22', 'LA MENA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1090, '23', 'MARISCAL SUCRE', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1091, '24', 'PONCEANO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1092, '25', 'PUENGASÍ', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1093, '26', 'QUITUMBE', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1094, '27', 'RUMIPAMBA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1095, '28', 'SAN BARTOLO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1096, '29', 'SAN ISIDRO DEL INCA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1097, '30', 'SAN JUAN', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1098, '31', 'SOLANDA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1099, '32', 'TURUBAMBA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1100, '50', 'QUITO DISTRITO METROPOLITANO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1101, '51', 'ALANGASÍ', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1102, '52', 'AMAGUAÑA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1103, '53', 'ATAHUALPA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1104, '54', 'CALACALÍ', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1105, '55', 'CALDERÓN', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1106, '56', 'CONOCOTO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1107, '57', 'CUMBAYÁ', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1108, '58', 'CHAVEZPAMBA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1109, '59', 'CHECA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1110, '60', 'EL QUINCHE', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1111, '61', 'GUALEA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1112, '62', 'GUANGOPOLO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1113, '63', 'GUAYLLABAMBA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1114, '64', 'LA MERCED', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1115, '65', 'LLANO CHICO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1116, '66', 'LLOA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1117, '67', 'MINDO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1118, '68', 'NANEGAL', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1119, '69', 'NANEGALITO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1120, '70', 'NAYÓN', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1121, '71', 'NONO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1122, '72', 'PACTO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1123, '73', 'PEDRO VICENTE MALDONADO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1124, '74', 'PERUCHO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1125, '75', 'PIFO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1126, '76', 'PÍNTAG', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1127, '77', 'POMASQUI', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1128, '78', 'PUÉLLARO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1129, '79', 'PUEMBO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1130, '80', 'SAN ANTONIO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1131, '81', 'SAN JOSÉ DE MINAS', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1132, '82', 'SAN MIGUEL DE LOS BANCOS', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1133, '83', 'TABABELA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1134, '84', 'TUMBACO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1135, '85', 'YARUQUÍ', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1136, '86', 'ZAMBIZA', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1137, '87', 'PUERTO QUITO', 1, 178);
INSERT INTO `cc_parroquia` VALUES (1138, '01', 'AYORA', 1, 179);
INSERT INTO `cc_parroquia` VALUES (1139, '02', 'CAYAMBE', 1, 179);
INSERT INTO `cc_parroquia` VALUES (1140, '03', 'JUAN MONTALVO', 1, 179);
INSERT INTO `cc_parroquia` VALUES (1141, '50', 'CAYAMBE', 1, 179);
INSERT INTO `cc_parroquia` VALUES (1142, '51', 'ASCÁZUBI', 1, 179);
INSERT INTO `cc_parroquia` VALUES (1143, '52', 'CANGAHUA', 1, 179);
INSERT INTO `cc_parroquia` VALUES (1144, '53', 'OLMEDO (PESILLO)', 1, 179);
INSERT INTO `cc_parroquia` VALUES (1145, '54', 'OTÓN', 1, 179);
INSERT INTO `cc_parroquia` VALUES (1146, '55', 'SANTA ROSA DE CUZUBAMBA', 1, 179);
INSERT INTO `cc_parroquia` VALUES (1147, '50', 'MACHACHI', 1, 180);
INSERT INTO `cc_parroquia` VALUES (1148, '51', 'ALÓAG', 1, 180);
INSERT INTO `cc_parroquia` VALUES (1149, '52', 'ALOASÍ', 1, 180);
INSERT INTO `cc_parroquia` VALUES (1150, '53', 'CUTUGLAHUA', 1, 180);
INSERT INTO `cc_parroquia` VALUES (1151, '54', 'EL CHAUPI', 1, 180);
INSERT INTO `cc_parroquia` VALUES (1152, '55', 'MANUEL CORNEJO ASTORGA (TANDAPI)', 1, 180);
INSERT INTO `cc_parroquia` VALUES (1153, '56', 'TAMBILLO', 1, 180);
INSERT INTO `cc_parroquia` VALUES (1154, '57', 'UYUMBICHO', 1, 180);
INSERT INTO `cc_parroquia` VALUES (1155, '50', 'TABACUNDO', 1, 181);
INSERT INTO `cc_parroquia` VALUES (1156, '51', 'LA ESPERANZA', 1, 181);
INSERT INTO `cc_parroquia` VALUES (1157, '52', 'MALCHINGUÍ', 1, 181);
INSERT INTO `cc_parroquia` VALUES (1158, '53', 'TOCACHI', 1, 181);
INSERT INTO `cc_parroquia` VALUES (1159, '54', 'TUPIGACHI', 1, 181);
INSERT INTO `cc_parroquia` VALUES (1160, '01', 'SANGOLQUÍ', 1, 182);
INSERT INTO `cc_parroquia` VALUES (1161, '02', 'SAN PEDRO DE TABOADA', 1, 182);
INSERT INTO `cc_parroquia` VALUES (1162, '03', 'SAN RAFAEL', 1, 182);
INSERT INTO `cc_parroquia` VALUES (1163, '50', 'SANGOLQUI', 1, 182);
INSERT INTO `cc_parroquia` VALUES (1164, '51', 'COTOGCHOA', 1, 182);
INSERT INTO `cc_parroquia` VALUES (1165, '52', 'RUMIPAMBA', 1, 182);
INSERT INTO `cc_parroquia` VALUES (1166, '50', 'SAN MIGUEL DE LOS BANCOS', 1, 183);
INSERT INTO `cc_parroquia` VALUES (1167, '51', 'MINDO', 1, 183);
INSERT INTO `cc_parroquia` VALUES (1168, '52', 'PEDRO VICENTE MALDONADO', 1, 183);
INSERT INTO `cc_parroquia` VALUES (1169, '53', 'PUERTO QUITO', 1, 183);
INSERT INTO `cc_parroquia` VALUES (1170, '50', 'PEDRO VICENTE MALDONADO', 1, 184);
INSERT INTO `cc_parroquia` VALUES (1171, '50', 'PUERTO QUITO', 1, 185);
INSERT INTO `cc_parroquia` VALUES (1172, '01', 'ATOCHA – FICOA', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1173, '02', 'CELIANO MONGE', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1174, '03', 'HUACHI CHICO', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1175, '04', 'HUACHI LORETO', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1176, '05', 'LA MERCED', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1177, '06', 'LA PENÍNSULA', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1178, '07', 'MATRIZ', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1179, '08', 'PISHILATA', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1180, '09', 'SAN FRANCISCO', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1181, '50', 'AMBATO', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1182, '51', 'AMBATILLO', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1183, '52', 'ATAHUALPA (CHISALATA)', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1184, '53', 'AUGUSTO N. MARTÍNEZ (MUNDUGLEO)', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1185, '54', 'CONSTANTINO FERNÁNDEZ (CAB. EN CULLITAHUA)', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1186, '55', 'HUACHI GRANDE', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1187, '56', 'IZAMBA', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1188, '57', 'JUAN BENIGNO VELA', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1189, '58', 'MONTALVO', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1190, '59', 'PASA', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1191, '60', 'PICAIGUA', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1192, '61', 'PILAGÜÍN (PILAHÜÍN)', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1193, '62', 'QUISAPINCHA (QUIZAPINCHA)', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1194, '63', 'SAN BARTOLOMÉ DE PINLLOG', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1195, '64', 'SAN FERNANDO (PASA SAN FERNANDO)', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1196, '65', 'SANTA ROSA', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1197, '66', 'TOTORAS', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1198, '67', 'CUNCHIBAMBA', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1199, '68', 'UNAMUNCHO', 1, 186);
INSERT INTO `cc_parroquia` VALUES (1200, '50', 'BAÑOS DE AGUA SANTA', 1, 187);
INSERT INTO `cc_parroquia` VALUES (1201, '51', 'LLIGUA', 1, 187);
INSERT INTO `cc_parroquia` VALUES (1202, '52', 'RÍO NEGRO', 1, 187);
INSERT INTO `cc_parroquia` VALUES (1203, '53', 'RÍO VERDE', 1, 187);
INSERT INTO `cc_parroquia` VALUES (1204, '54', 'ULBA', 1, 187);
INSERT INTO `cc_parroquia` VALUES (1205, '50', 'CEVALLOS', 1, 188);
INSERT INTO `cc_parroquia` VALUES (1206, '50', 'MOCHA', 1, 189);
INSERT INTO `cc_parroquia` VALUES (1207, '51', 'PINGUILÍ', 1, 189);
INSERT INTO `cc_parroquia` VALUES (1208, '50', 'PATATE', 1, 190);
INSERT INTO `cc_parroquia` VALUES (1209, '51', 'EL TRIUNFO', 1, 190);
INSERT INTO `cc_parroquia` VALUES (1210, '52', 'LOS ANDES (CAB. EN POATUG)', 1, 190);
INSERT INTO `cc_parroquia` VALUES (1211, '53', 'SUCRE (CAB. EN SUCRE-PATATE URCU)', 1, 190);
INSERT INTO `cc_parroquia` VALUES (1212, '50', 'QUERO', 1, 191);
INSERT INTO `cc_parroquia` VALUES (1213, '51', 'RUMIPAMBA', 1, 191);
INSERT INTO `cc_parroquia` VALUES (1214, '52', 'YANAYACU - MOCHAPATA (CAB. EN YANAYACU)', 1, 191);
INSERT INTO `cc_parroquia` VALUES (1215, '01', 'PELILEO', 1, 192);
INSERT INTO `cc_parroquia` VALUES (1216, '02', 'PELILEO GRANDE', 1, 192);
INSERT INTO `cc_parroquia` VALUES (1217, '50', 'PELILEO', 1, 192);
INSERT INTO `cc_parroquia` VALUES (1218, '51', 'BENÍTEZ (PACHANLICA)', 1, 192);
INSERT INTO `cc_parroquia` VALUES (1219, '52', 'BOLÍVAR', 1, 192);
INSERT INTO `cc_parroquia` VALUES (1220, '53', 'COTALÓ', 1, 192);
INSERT INTO `cc_parroquia` VALUES (1221, '54', 'CHIQUICHA (CAB. EN CHIQUICHA GRANDE)', 1, 192);
INSERT INTO `cc_parroquia` VALUES (1222, '55', 'EL ROSARIO (RUMICHACA)', 1, 192);
INSERT INTO `cc_parroquia` VALUES (1223, '56', 'GARCÍA MORENO (CHUMAQUI)', 1, 192);
INSERT INTO `cc_parroquia` VALUES (1224, '57', 'GUAMBALÓ (HUAMBALÓ)', 1, 192);
INSERT INTO `cc_parroquia` VALUES (1225, '58', 'SALASACA', 1, 192);
INSERT INTO `cc_parroquia` VALUES (1226, '01', 'CIUDAD NUEVA', 1, 193);
INSERT INTO `cc_parroquia` VALUES (1227, '02', 'PÍLLARO', 1, 193);
INSERT INTO `cc_parroquia` VALUES (1228, '50', 'PÍLLARO', 1, 193);
INSERT INTO `cc_parroquia` VALUES (1229, '51', 'BAQUERIZO MORENO', 1, 193);
INSERT INTO `cc_parroquia` VALUES (1230, '52', 'EMILIO MARÍA TERÁN (RUMIPAMBA)', 1, 193);
INSERT INTO `cc_parroquia` VALUES (1231, '53', 'MARCOS ESPINEL (CHACATA)', 1, 193);
INSERT INTO `cc_parroquia` VALUES (1232, '54', 'PRESIDENTE URBINA (CHAGRAPAMBA -PATZUCUL)', 1, 193);
INSERT INTO `cc_parroquia` VALUES (1233, '55', 'SAN ANDRÉS', 1, 193);
INSERT INTO `cc_parroquia` VALUES (1234, '56', 'SAN JOSÉ DE POALÓ', 1, 193);
INSERT INTO `cc_parroquia` VALUES (1235, '57', 'SAN MIGUELITO', 1, 193);
INSERT INTO `cc_parroquia` VALUES (1236, '50', 'TISALEO', 1, 194);
INSERT INTO `cc_parroquia` VALUES (1237, '51', 'QUINCHICOTO', 1, 194);
INSERT INTO `cc_parroquia` VALUES (1238, '01', 'EL LIMÓN', 1, 195);
INSERT INTO `cc_parroquia` VALUES (1239, '02', 'ZAMORA', 1, 195);
INSERT INTO `cc_parroquia` VALUES (1240, '50', 'ZAMORA', 1, 195);
INSERT INTO `cc_parroquia` VALUES (1241, '51', 'CUMBARATZA', 1, 195);
INSERT INTO `cc_parroquia` VALUES (1242, '52', 'GUADALUPE', 1, 195);
INSERT INTO `cc_parroquia` VALUES (1243, '53', 'IMBANA (LA VICTORIA DE IMBANA)', 1, 195);
INSERT INTO `cc_parroquia` VALUES (1244, '54', 'PAQUISHA', 1, 195);
INSERT INTO `cc_parroquia` VALUES (1245, '55', 'SABANILLA', 1, 195);
INSERT INTO `cc_parroquia` VALUES (1246, '56', 'TIMBARA', 1, 195);
INSERT INTO `cc_parroquia` VALUES (1247, '57', 'ZUMBI', 1, 195);
INSERT INTO `cc_parroquia` VALUES (1248, '58', 'SAN CARLOS DE LAS MINAS', 1, 195);
INSERT INTO `cc_parroquia` VALUES (1249, '50', 'ZUMBA', 1, 196);
INSERT INTO `cc_parroquia` VALUES (1250, '51', 'CHITO', 1, 196);
INSERT INTO `cc_parroquia` VALUES (1251, '52', 'EL CHORRO', 1, 196);
INSERT INTO `cc_parroquia` VALUES (1252, '53', 'EL PORVENIR DEL CARMEN', 1, 196);
INSERT INTO `cc_parroquia` VALUES (1253, '54', 'LA CHONTA', 1, 196);
INSERT INTO `cc_parroquia` VALUES (1254, '55', 'PALANDA', 1, 196);
INSERT INTO `cc_parroquia` VALUES (1255, '56', 'PUCAPAMBA', 1, 196);
INSERT INTO `cc_parroquia` VALUES (1256, '57', 'SAN FRANCISCO DEL VERGEL', 1, 196);
INSERT INTO `cc_parroquia` VALUES (1257, '58', 'VALLADOLID', 1, 196);
INSERT INTO `cc_parroquia` VALUES (1258, '59', 'SAN ANDRÉS', 1, 196);
INSERT INTO `cc_parroquia` VALUES (1259, '50', 'GUAYZIMI', 1, 197);
INSERT INTO `cc_parroquia` VALUES (1260, '51', 'ZURMI', 1, 197);
INSERT INTO `cc_parroquia` VALUES (1261, '52', 'NUEVO PARAÍSO', 1, 197);
INSERT INTO `cc_parroquia` VALUES (1262, '50', '28 DE MAYO (SAN JOSÉ DE YACUAMBI)', 1, 198);
INSERT INTO `cc_parroquia` VALUES (1263, '51', 'LA PAZ', 1, 198);
INSERT INTO `cc_parroquia` VALUES (1264, '52', 'TUTUPALI', 1, 198);
INSERT INTO `cc_parroquia` VALUES (1265, '50', 'YANTZAZA (YANZATZA)', 1, 199);
INSERT INTO `cc_parroquia` VALUES (1266, '51', 'CHICAÑA', 1, 199);
INSERT INTO `cc_parroquia` VALUES (1267, '52', 'EL PANGUI', 1, 199);
INSERT INTO `cc_parroquia` VALUES (1268, '53', 'LOS ENCUENTROS', 1, 199);
INSERT INTO `cc_parroquia` VALUES (1269, '50', 'EL PANGUI', 1, 200);
INSERT INTO `cc_parroquia` VALUES (1270, '51', 'EL GUISME', 1, 200);
INSERT INTO `cc_parroquia` VALUES (1271, '52', 'PACHICUTZA', 1, 200);
INSERT INTO `cc_parroquia` VALUES (1272, '53', 'TUNDAYME', 1, 200);
INSERT INTO `cc_parroquia` VALUES (1273, '50', 'ZUMBI', 1, 201);
INSERT INTO `cc_parroquia` VALUES (1274, '51', 'PAQUISHA', 1, 201);
INSERT INTO `cc_parroquia` VALUES (1275, '52', 'TRIUNFO-DORADO', 1, 201);
INSERT INTO `cc_parroquia` VALUES (1276, '53', 'PANGUINTZA', 1, 201);
INSERT INTO `cc_parroquia` VALUES (1277, '50', 'PALANDA', 1, 202);
INSERT INTO `cc_parroquia` VALUES (1278, '51', 'EL PORVENIR DEL CARMEN', 1, 202);
INSERT INTO `cc_parroquia` VALUES (1279, '52', 'SAN FRANCISCO DEL VERGEL', 1, 202);
INSERT INTO `cc_parroquia` VALUES (1280, '53', 'VALLADOLID', 1, 202);
INSERT INTO `cc_parroquia` VALUES (1281, '54', 'LA CANELA', 1, 202);
INSERT INTO `cc_parroquia` VALUES (1282, '50', 'PAQUISHA', 1, 203);
INSERT INTO `cc_parroquia` VALUES (1283, '51', 'BELLAVISTA', 1, 203);
INSERT INTO `cc_parroquia` VALUES (1284, '52', 'NUEVO QUITO', 1, 203);
INSERT INTO `cc_parroquia` VALUES (1285, '50', 'PUERTO BAQUERIZO MORENO', 1, 204);
INSERT INTO `cc_parroquia` VALUES (1286, '51', 'EL PROGRESO', 1, 204);
INSERT INTO `cc_parroquia` VALUES (1287, '52', 'ISLA SANTA MARÍA (FLOREANA) (CAB. EN PTO. VEL', 1, 204);
INSERT INTO `cc_parroquia` VALUES (1288, '50', 'PUERTO VILLAMIL', 1, 205);
INSERT INTO `cc_parroquia` VALUES (1289, '51', 'TOMÁS DE BERLANGA (SANTO TOMÁS)', 1, 205);
INSERT INTO `cc_parroquia` VALUES (1290, '50', 'PUERTO AYORA', 1, 206);
INSERT INTO `cc_parroquia` VALUES (1291, '51', 'BELLAVISTA', 1, 206);
INSERT INTO `cc_parroquia` VALUES (1292, '52', 'SANTA ROSA (INCLUYE LA ISLA BALTRA)', 1, 206);
INSERT INTO `cc_parroquia` VALUES (1293, '50', 'NUEVA LOJA', 1, 207);
INSERT INTO `cc_parroquia` VALUES (1294, '51', 'CUYABENO', 1, 207);
INSERT INTO `cc_parroquia` VALUES (1295, '52', 'DURENO', 1, 207);
INSERT INTO `cc_parroquia` VALUES (1296, '53', 'GENERAL FARFÁN', 1, 207);
INSERT INTO `cc_parroquia` VALUES (1297, '54', 'TARAPOA', 1, 207);
INSERT INTO `cc_parroquia` VALUES (1298, '55', 'EL ENO', 1, 207);
INSERT INTO `cc_parroquia` VALUES (1299, '56', 'PACAYACU', 1, 207);
INSERT INTO `cc_parroquia` VALUES (1300, '57', 'JAMBELÍ', 1, 207);
INSERT INTO `cc_parroquia` VALUES (1301, '58', 'SANTA CECILIA', 1, 207);
INSERT INTO `cc_parroquia` VALUES (1302, '59', 'AGUAS NEGRAS', 1, 207);
INSERT INTO `cc_parroquia` VALUES (1303, '50', 'EL DORADO DE CASCALES', 1, 208);
INSERT INTO `cc_parroquia` VALUES (1304, '51', 'EL REVENTADOR', 1, 208);
INSERT INTO `cc_parroquia` VALUES (1305, '52', 'GONZALO PIZARRO', 1, 208);
INSERT INTO `cc_parroquia` VALUES (1306, '53', 'LUMBAQUÍ', 1, 208);
INSERT INTO `cc_parroquia` VALUES (1307, '54', 'PUERTO LIBRE', 1, 208);
INSERT INTO `cc_parroquia` VALUES (1308, '55', 'SANTA ROSA DE SUCUMBÍOS', 1, 208);
INSERT INTO `cc_parroquia` VALUES (1309, '50', 'PUERTO EL CARMEN DEL PUTUMAYO', 1, 209);
INSERT INTO `cc_parroquia` VALUES (1310, '51', 'PALMA ROJA', 1, 209);
INSERT INTO `cc_parroquia` VALUES (1311, '52', 'PUERTO BOLÍVAR (PUERTO MONTÚFAR)', 1, 209);
INSERT INTO `cc_parroquia` VALUES (1312, '53', 'PUERTO RODRÍGUEZ', 1, 209);
INSERT INTO `cc_parroquia` VALUES (1313, '54', 'SANTA ELENA', 1, 209);
INSERT INTO `cc_parroquia` VALUES (1314, '50', 'SHUSHUFINDI', 1, 210);
INSERT INTO `cc_parroquia` VALUES (1315, '51', 'LIMONCOCHA', 1, 210);
INSERT INTO `cc_parroquia` VALUES (1316, '52', 'PAÑACOCHA', 1, 210);
INSERT INTO `cc_parroquia` VALUES (1317, '53', 'SAN ROQUE (CAB. EN SAN VICENTE)', 1, 210);
INSERT INTO `cc_parroquia` VALUES (1318, '54', 'SAN PEDRO DE LOS COFANES', 1, 210);
INSERT INTO `cc_parroquia` VALUES (1319, '55', 'SIETE DE JULIO', 1, 210);
INSERT INTO `cc_parroquia` VALUES (1320, '50', 'LA BONITA', 1, 211);
INSERT INTO `cc_parroquia` VALUES (1321, '51', 'EL PLAYÓN DE SAN FRANCISCO', 1, 211);
INSERT INTO `cc_parroquia` VALUES (1322, '52', 'LA SOFÍA', 1, 211);
INSERT INTO `cc_parroquia` VALUES (1323, '53', 'ROSA FLORIDA', 1, 211);
INSERT INTO `cc_parroquia` VALUES (1324, '54', 'SANTA BÁRBARA', 1, 211);
INSERT INTO `cc_parroquia` VALUES (1325, '50', 'EL DORADO DE CASCALES', 1, 212);
INSERT INTO `cc_parroquia` VALUES (1326, '51', 'SANTA ROSA DE SUCUMBÍOS', 1, 212);
INSERT INTO `cc_parroquia` VALUES (1327, '52', 'SEVILLA', 1, 212);
INSERT INTO `cc_parroquia` VALUES (1328, '50', 'TARAPOA', 1, 213);
INSERT INTO `cc_parroquia` VALUES (1329, '51', 'CUYABENO', 1, 213);
INSERT INTO `cc_parroquia` VALUES (1330, '52', 'AGUAS NEGRAS', 1, 213);
INSERT INTO `cc_parroquia` VALUES (1331, '50', 'PUERTO FRANCISCO DE ORELLANA (EL COCA)', 1, 214);
INSERT INTO `cc_parroquia` VALUES (1332, '51', 'DAYUMA', 1, 214);
INSERT INTO `cc_parroquia` VALUES (1333, '52', 'TARACOA (NUEVA ESPERANZA: YUCA)', 1, 214);
INSERT INTO `cc_parroquia` VALUES (1334, '53', 'ALEJANDRO LABAKA', 1, 214);
INSERT INTO `cc_parroquia` VALUES (1335, '54', 'EL DORADO', 1, 214);
INSERT INTO `cc_parroquia` VALUES (1336, '55', 'EL EDÉN', 1, 214);
INSERT INTO `cc_parroquia` VALUES (1337, '56', 'GARCÍA MORENO', 1, 214);
INSERT INTO `cc_parroquia` VALUES (1338, '57', 'INÉS ARANGO (CAB. EN WESTERN)', 1, 214);
INSERT INTO `cc_parroquia` VALUES (1339, '58', 'LA BELLEZA', 1, 214);
INSERT INTO `cc_parroquia` VALUES (1340, '59', 'NUEVO PARAÍSO (CAB. EN UNIÓN', 1, 214);
INSERT INTO `cc_parroquia` VALUES (1341, '60', 'SAN JOSÉ DE GUAYUSA', 1, 214);
INSERT INTO `cc_parroquia` VALUES (1342, '61', 'SAN LUIS DE ARMENIA', 1, 214);
INSERT INTO `cc_parroquia` VALUES (1343, '01', 'TIPITINI', 1, 215);
INSERT INTO `cc_parroquia` VALUES (1344, '50', 'NUEVO ROCAFUERTE', 1, 215);
INSERT INTO `cc_parroquia` VALUES (1345, '51', 'CAPITÁN AUGUSTO RIVADENEYRA', 1, 215);
INSERT INTO `cc_parroquia` VALUES (1346, '52', 'CONONACO', 1, 215);
INSERT INTO `cc_parroquia` VALUES (1347, '53', 'SANTA MARÍA DE HUIRIRIMA', 1, 215);
INSERT INTO `cc_parroquia` VALUES (1348, '54', 'TIPUTINI', 1, 215);
INSERT INTO `cc_parroquia` VALUES (1349, '55', 'YASUNÍ', 1, 215);
INSERT INTO `cc_parroquia` VALUES (1350, '50', 'LA JOYA DE LOS SACHAS', 1, 216);
INSERT INTO `cc_parroquia` VALUES (1351, '51', 'ENOKANQUI', 1, 216);
INSERT INTO `cc_parroquia` VALUES (1352, '52', 'POMPEYA', 1, 216);
INSERT INTO `cc_parroquia` VALUES (1353, '53', 'SAN CARLOS', 1, 216);
INSERT INTO `cc_parroquia` VALUES (1354, '54', 'SAN SEBASTIÁN DEL COCA', 1, 216);
INSERT INTO `cc_parroquia` VALUES (1355, '55', 'LAGO SAN PEDRO', 1, 216);
INSERT INTO `cc_parroquia` VALUES (1356, '56', 'RUMIPAMBA', 1, 216);
INSERT INTO `cc_parroquia` VALUES (1357, '57', 'TRES DE NOVIEMBRE', 1, 216);
INSERT INTO `cc_parroquia` VALUES (1358, '58', 'UNIÓN MILAGREÑA', 1, 216);
INSERT INTO `cc_parroquia` VALUES (1359, '50', 'LORETO', 1, 217);
INSERT INTO `cc_parroquia` VALUES (1360, '51', 'AVILA (CAB. EN HUIRUNO)', 1, 217);
INSERT INTO `cc_parroquia` VALUES (1361, '52', 'PUERTO MURIALDO', 1, 217);
INSERT INTO `cc_parroquia` VALUES (1362, '53', 'SAN JOSÉ DE PAYAMINO', 1, 217);
INSERT INTO `cc_parroquia` VALUES (1363, '54', 'SAN JOSÉ DE DAHUANO', 1, 217);
INSERT INTO `cc_parroquia` VALUES (1364, '55', 'SAN VICENTE DE HUATICOCHA', 1, 217);
INSERT INTO `cc_parroquia` VALUES (1365, '01', 'ABRAHAM CALAZACÓN', 1, 218);
INSERT INTO `cc_parroquia` VALUES (1366, '02', 'BOMBOLÍ', 1, 218);
INSERT INTO `cc_parroquia` VALUES (1367, '03', 'CHIGUILPE', 1, 218);
INSERT INTO `cc_parroquia` VALUES (1368, '04', 'RÍO TOACHI', 1, 218);
INSERT INTO `cc_parroquia` VALUES (1369, '05', 'RÍO VERDE', 1, 218);
INSERT INTO `cc_parroquia` VALUES (1370, '06', 'SANTO DOMINGO DE LOS COLORADOS', 1, 218);
INSERT INTO `cc_parroquia` VALUES (1371, '07', 'ZARACAY', 1, 218);
INSERT INTO `cc_parroquia` VALUES (1372, '50', 'SANTO DOMINGO DE LOS COLORADOS', 1, 218);
INSERT INTO `cc_parroquia` VALUES (1373, '51', 'ALLURIQUÍN', 1, 218);
INSERT INTO `cc_parroquia` VALUES (1374, '52', 'PUERTO LIMÓN', 1, 218);
INSERT INTO `cc_parroquia` VALUES (1375, '53', 'LUZ DE AMÉRICA', 1, 218);
INSERT INTO `cc_parroquia` VALUES (1376, '54', 'SAN JACINTO DEL BÚA', 1, 218);
INSERT INTO `cc_parroquia` VALUES (1377, '55', 'VALLE HERMOSO', 1, 218);
INSERT INTO `cc_parroquia` VALUES (1378, '56', 'EL ESFUERZO', 1, 218);
INSERT INTO `cc_parroquia` VALUES (1379, '57', 'SANTA MARÍA DEL TOACHI', 1, 218);
INSERT INTO `cc_parroquia` VALUES (1380, '01', 'BALLENITA', 1, 219);
INSERT INTO `cc_parroquia` VALUES (1381, '02', 'SANTA ELENA', 1, 219);
INSERT INTO `cc_parroquia` VALUES (1382, '50', 'SANTA ELENA', 1, 219);
INSERT INTO `cc_parroquia` VALUES (1383, '51', 'ATAHUALPA', 1, 219);
INSERT INTO `cc_parroquia` VALUES (1384, '52', 'COLONCHE', 1, 219);
INSERT INTO `cc_parroquia` VALUES (1385, '53', 'CHANDUY', 1, 219);
INSERT INTO `cc_parroquia` VALUES (1386, '54', 'MANGLARALTO', 1, 219);
INSERT INTO `cc_parroquia` VALUES (1387, '55', 'SIMÓN BOLÍVAR (JULIO MORENO)', 1, 219);
INSERT INTO `cc_parroquia` VALUES (1388, '56', 'SAN JOSÉ DE ANCÓN', 1, 219);
INSERT INTO `cc_parroquia` VALUES (1389, '50', 'LA LIBERTAD', 1, 220);
INSERT INTO `cc_parroquia` VALUES (1390, '01', 'CARLOS ESPINOZA LARREA', 1, 221);
INSERT INTO `cc_parroquia` VALUES (1391, '02', 'GRAL. ALBERTO ENRÍQUEZ GALLO', 1, 221);
INSERT INTO `cc_parroquia` VALUES (1392, '03', 'VICENTE ROCAFUERTE', 1, 221);
INSERT INTO `cc_parroquia` VALUES (1393, '04', 'SANTA ROSA', 1, 221);
INSERT INTO `cc_parroquia` VALUES (1394, '50', 'SALINAS', 1, 221);
INSERT INTO `cc_parroquia` VALUES (1395, '51', 'ANCONCITO', 1, 221);
INSERT INTO `cc_parroquia` VALUES (1396, '52', 'JOSÉ LUIS TAMAYO (MUEY)', 1, 221);
INSERT INTO `cc_parroquia` VALUES (1397, '51', 'LAS GOLONDRINAS', 1, 222);
INSERT INTO `cc_parroquia` VALUES (1398, '51', 'MANGA DEL CURA', 1, 223);
INSERT INTO `cc_parroquia` VALUES (1399, '51', 'EL PIEDRERO', 1, 225);

-- ----------------------------
-- Table structure for cc_periodos_contables
-- ----------------------------
DROP TABLE IF EXISTS `cc_periodos_contables`;
CREATE TABLE `cc_periodos_contables`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `pc_anio` int(0) NOT NULL,
  `pc_mes` int(0) NOT NULL,
  `pc_fecha_inicio` date NOT NULL,
  `pc_fecha_fin` date NOT NULL,
  `pc_estado` enum('ABIERTO','CERRADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'ABIERTO',
  `pc_valor` int(0) NULL DEFAULT NULL COMMENT 'ES UN AUTOINCREMENT QUE CUENTA LOS ASIENTOS, EMPIEZA EN 1',
  `pc_created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_periodos_contables
-- ----------------------------
INSERT INTO `cc_periodos_contables` VALUES (1, 2026, 8, '2026-08-01', '2026-08-31', 'ABIERTO', 0, '2026-08-17 16:55:52');

-- ----------------------------
-- Table structure for cc_producto_impuestotarifa
-- ----------------------------
DROP TABLE IF EXISTS `cc_producto_impuestotarifa`;
CREATE TABLE `cc_producto_impuestotarifa`  (
  `fk_producto` int(0) NULL DEFAULT NULL,
  `fk_impuestotarifa` int(0) NULL DEFAULT NULL,
  `fk_impuesto` int(0) NULL DEFAULT NULL,
  INDEX `fk_producto`(`fk_producto`) USING BTREE,
  INDEX `fk_impuestotarifa`(`fk_impuestotarifa`) USING BTREE,
  INDEX `fk_impuesto`(`fk_impuesto`) USING BTREE,
  INDEX `idx_producto_impuesto_producto`(`fk_producto`) USING BTREE,
  INDEX `idx_producto_impuesto_tarifa`(`fk_impuestotarifa`) USING BTREE,
  CONSTRAINT `cc_producto_impuestotarifa_ibfk_1` FOREIGN KEY (`fk_producto`) REFERENCES `cc_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_producto_impuestotarifa_ibfk_3` FOREIGN KEY (`fk_impuestotarifa`) REFERENCES `cc_impuesto_tarifa` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_producto_impuestotarifa_ibfk_4` FOREIGN KEY (`fk_impuesto`) REFERENCES `cc_impuestos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_producto_impuestotarifa
-- ----------------------------

-- ----------------------------
-- Table structure for cc_producto_precios
-- ----------------------------
DROP TABLE IF EXISTS `cc_producto_precios`;
CREATE TABLE `cc_producto_precios`  (
  `fk_tipo_precio` int(0) NULL DEFAULT NULL,
  `fk_producto` int(0) NULL DEFAULT NULL,
  `pp_valor` decimal(15, 4) NULL DEFAULT NULL,
  INDEX `pp_fk_tipo_precio`(`fk_tipo_precio`) USING BTREE,
  INDEX `pp_fk_producto`(`fk_producto`) USING BTREE,
  CONSTRAINT `cc_producto_precios_ibfk_1` FOREIGN KEY (`fk_tipo_precio`) REFERENCES `cc_tipo_precios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_producto_precios_ibfk_2` FOREIGN KEY (`fk_producto`) REFERENCES `cc_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_producto_precios
-- ----------------------------

-- ----------------------------
-- Table structure for cc_producto_proveedor
-- ----------------------------
DROP TABLE IF EXISTS `cc_producto_proveedor`;
CREATE TABLE `cc_producto_proveedor`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_producto` int(0) NULL DEFAULT NULL,
  `fk_proveedor` int(0) NULL DEFAULT NULL,
  `codigo_proveedor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_proveedor`(`fk_proveedor`) USING BTREE,
  INDEX `fk_producto`(`fk_producto`) USING BTREE,
  CONSTRAINT `cc_producto_proveedor_ibfk_1` FOREIGN KEY (`fk_proveedor`) REFERENCES `cc_proveedores` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_producto_proveedor_ibfk_2` FOREIGN KEY (`fk_producto`) REFERENCES `cc_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_producto_proveedor
-- ----------------------------

-- ----------------------------
-- Table structure for cc_productos
-- ----------------------------
DROP TABLE IF EXISTS `cc_productos`;
CREATE TABLE `cc_productos`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `prod_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `prod_codigo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `prod_codigobarras` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `prod_codigobarras2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `prod_codigobarras3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `prod_imagen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `prod_detalle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `prod_existenciaminima` int(0) NULL DEFAULT NULL,
  `prod_existenciamaxima` int(0) NULL DEFAULT NULL,
  `prod_stockactual` decimal(15, 3) NULL DEFAULT NULL,
  `prod_costopromedio` decimal(15, 4) NULL DEFAULT NULL,
  `prod_costoultimo` decimal(15, 4) NULL DEFAULT NULL,
  `prod_costoalto` decimal(15, 4) NULL DEFAULT NULL,
  `prod_venta` tinyint(0) NULL DEFAULT NULL,
  `prod_compra` tinyint(0) NULL DEFAULT NULL,
  `prod_fechacreacion` date NULL DEFAULT NULL,
  `prod_fechaactualizacion` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `prod_isservicio` tinyint(0) NULL DEFAULT NULL,
  `prod_isgasto` tinyint(0) NULL DEFAULT NULL,
  `prod_valormedida` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `fk_unidadmedida` int(0) NULL DEFAULT NULL,
  `fk_subgrupo` int(0) NULL DEFAULT NULL,
  `fk_marca` int(0) NULL DEFAULT NULL,
  `fk_tipoproducto` int(0) NULL DEFAULT NULL,
  `prod_ivaporcentage` float(255, 2) NULL DEFAULT NULL,
  `prod_iceporcentage` float(255, 2) NULL DEFAULT NULL,
  `prod_tiene_ice` tinyint(0) NULL DEFAULT NULL,
  `prod_pvppromo` float(255, 6) NULL DEFAULT NULL,
  `prod_ispromo` tinyint(0) NULL DEFAULT NULL,
  `prod_costoinventario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `prod_especificaciones` tinyint(0) NULL DEFAULT NULL,
  `fk_cuentacontableventas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `fk_cuentacontablecompras` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `prod_estado` tinyint(0) NULL DEFAULT NULL,
  `prod_issuperproducto` tinyint(0) NULL DEFAULT NULL,
  `prod_ctrllote` tinyint(0) NULL DEFAULT NULL,
  `prod_facturar_ennegativo` tinyint(0) NULL DEFAULT NULL,
  `prod_facturar_precio_inferiorcosto` tinyint(0) NULL DEFAULT NULL,
  `prod_tiene_irbpnr` tinyint(0) NULL DEFAULT NULL,
  `prod_valor_irbpnr` decimal(15, 4) NULL DEFAULT NULL,
  `fk_user_id` int(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `prod_codigo`(`prod_codigo`) USING BTREE,
  UNIQUE INDEX `prod_nombre`(`prod_nombre`) USING BTREE,
  INDEX `fk_unidadmedida`(`fk_unidadmedida`) USING BTREE,
  INDEX `fk_subgrupo`(`fk_subgrupo`) USING BTREE,
  INDEX `fk_marca`(`fk_marca`) USING BTREE,
  INDEX `fk_tipoproducto`(`fk_tipoproducto`) USING BTREE,
  INDEX `fk_cuentacontableventas`(`fk_cuentacontableventas`) USING BTREE,
  INDEX `fk_cuentacontablecompras`(`fk_cuentacontablecompras`) USING BTREE,
  INDEX `idx_producto_codigo`(`prod_codigo`) USING BTREE,
  INDEX `idx_producto_codigobarras`(`prod_codigobarras`) USING BTREE,
  INDEX `idx_producto_subgrupo`(`fk_subgrupo`) USING BTREE,
  INDEX `idx_producto_marca`(`fk_marca`) USING BTREE,
  INDEX `fk_user_id`(`fk_user_id`) USING BTREE,
  CONSTRAINT `cc_productos_ibfk_1` FOREIGN KEY (`fk_unidadmedida`) REFERENCES `cc_unidades_medida` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_productos_ibfk_2` FOREIGN KEY (`fk_subgrupo`) REFERENCES `cc_subgrupos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_productos_ibfk_3` FOREIGN KEY (`fk_marca`) REFERENCES `cc_marcas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_productos_ibfk_4` FOREIGN KEY (`fk_tipoproducto`) REFERENCES `cc_tipo_producto` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_productos_ibfk_5` FOREIGN KEY (`fk_cuentacontableventas`) REFERENCES `cc_cuenta_contabledet` (`ctad_codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_productos_ibfk_6` FOREIGN KEY (`fk_cuentacontablecompras`) REFERENCES `cc_cuenta_contabledet` (`ctad_codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_productos_ibfk_7` FOREIGN KEY (`fk_user_id`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_productos
-- ----------------------------

-- ----------------------------
-- Table structure for cc_proveedor_banco
-- ----------------------------
DROP TABLE IF EXISTS `cc_proveedor_banco`;
CREATE TABLE `cc_proveedor_banco`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_proveedor` int(0) NULL DEFAULT NULL,
  `fk_banco` int(0) NULL DEFAULT NULL,
  `fk_tipo_cuenta` int(0) NULL DEFAULT NULL,
  `numero_cuenta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_proveedor`(`fk_proveedor`) USING BTREE,
  INDEX `fk_banco`(`fk_banco`) USING BTREE,
  INDEX `fk_tipo_cuenta`(`fk_tipo_cuenta`) USING BTREE,
  CONSTRAINT `cc_proveedor_banco_ibfk_1` FOREIGN KEY (`fk_proveedor`) REFERENCES `cc_proveedores` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_proveedor_banco_ibfk_2` FOREIGN KEY (`fk_banco`) REFERENCES `cc_bancos_list` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_proveedor_banco_ibfk_3` FOREIGN KEY (`fk_tipo_cuenta`) REFERENCES `cc_banco_tipo_cuenta` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_proveedor_banco
-- ----------------------------

-- ----------------------------
-- Table structure for cc_proveedor_retencion
-- ----------------------------
DROP TABLE IF EXISTS `cc_proveedor_retencion`;
CREATE TABLE `cc_proveedor_retencion`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_proveedor` int(0) NULL DEFAULT NULL,
  `fk_retencion` int(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_proveedor`(`fk_proveedor`) USING BTREE,
  INDEX `fk_retencion`(`fk_retencion`) USING BTREE,
  CONSTRAINT `cc_proveedor_retencion_ibfk_1` FOREIGN KEY (`fk_proveedor`) REFERENCES `cc_proveedores` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_proveedor_retencion_ibfk_2` FOREIGN KEY (`fk_retencion`) REFERENCES `cc_retencion_sri` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_proveedor_retencion
-- ----------------------------

-- ----------------------------
-- Table structure for cc_proveedores
-- ----------------------------
DROP TABLE IF EXISTS `cc_proveedores`;
CREATE TABLE `cc_proveedores`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `prov_nombres` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `prov_apellidos` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fk_tipo_documento` int(0) NULL DEFAULT NULL COMMENT 'CEDULA,RUC,PASAPORTE',
  `prov_ruc` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Puede ser cédula, ruc',
  `prov_razon_social` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `prov_telefono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `prov_celular` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `prov_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `prov_direccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fk_parroquia` int(0) NULL DEFAULT NULL,
  `prov_estado` tinyint(1) NULL DEFAULT NULL,
  `prov_fecha_creacion` datetime(0) NULL DEFAULT NULL,
  `prov_fecha_actualizacion` datetime(0) NULL DEFAULT NULL,
  `fk_tipo_sujeto` int(0) NULL DEFAULT NULL COMMENT 'NATURAL, JURIDICA, EXTRANGERA',
  `prov_comentarios` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `prov_dias_credito` int(0) NULL DEFAULT NULL,
  `fk_codigo_cuenta_contable` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `fk_sector` int(0) NULL DEFAULT NULL,
  `fk_proyecto` int(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_tipo_documento`(`fk_tipo_documento`) USING BTREE,
  INDEX `fk_parroquia`(`fk_parroquia`) USING BTREE,
  INDEX `fk_tipo_sujeto`(`fk_tipo_sujeto`) USING BTREE,
  INDEX `fk_sector`(`fk_sector`) USING BTREE,
  INDEX `fk_codigo_cuenta_contable`(`fk_codigo_cuenta_contable`) USING BTREE,
  CONSTRAINT `cc_proveedores_ibfk_1` FOREIGN KEY (`fk_tipo_documento`) REFERENCES `cc_tipo_documento` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_proveedores_ibfk_2` FOREIGN KEY (`fk_parroquia`) REFERENCES `cc_parroquia` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_proveedores_ibfk_3` FOREIGN KEY (`fk_tipo_sujeto`) REFERENCES `cc_tipo_sujetos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_proveedores_ibfk_4` FOREIGN KEY (`fk_sector`) REFERENCES `cc_sectores` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_proveedores_ibfk_5` FOREIGN KEY (`fk_codigo_cuenta_contable`) REFERENCES `cc_cuenta_contabledet` (`ctad_codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_proveedores
-- ----------------------------
INSERT INTO `cc_proveedores` VALUES (1, 'PROVEEDOR', 'PRUEBA', 1, '9999999999999', 'PROVEEDOR PRUEBA', '', '', '', '', NULL, 1, '2026-08-17 16:50:25', '2026-08-17 16:50:25', 2, 'Proveedor base de prueba', 0, NULL, NULL, 1);

-- ----------------------------
-- Table structure for cc_provincia
-- ----------------------------
DROP TABLE IF EXISTS `cc_provincia`;
CREATE TABLE `cc_provincia`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `prv_codigo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `prv_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `prv_estado` tinyint(1) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 26 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_provincia
-- ----------------------------
INSERT INTO `cc_provincia` VALUES (1, '01', 'AZUAY', 1);
INSERT INTO `cc_provincia` VALUES (2, '02', 'BOLIVAR', 1);
INSERT INTO `cc_provincia` VALUES (3, '03', 'CAÑAR', 1);
INSERT INTO `cc_provincia` VALUES (4, '04', 'CARCHI', 1);
INSERT INTO `cc_provincia` VALUES (5, '05', 'COTOPAXI', 1);
INSERT INTO `cc_provincia` VALUES (6, '06', 'CHIMBORAZO', 1);
INSERT INTO `cc_provincia` VALUES (7, '07', 'EL ORO', 1);
INSERT INTO `cc_provincia` VALUES (8, '08', 'ESMERALDAS', 1);
INSERT INTO `cc_provincia` VALUES (9, '09', 'GUAYAS', 1);
INSERT INTO `cc_provincia` VALUES (10, '10', 'IMBABURA', 1);
INSERT INTO `cc_provincia` VALUES (11, '11', 'LOJA', 1);
INSERT INTO `cc_provincia` VALUES (12, '12', 'LOS RIOS', 1);
INSERT INTO `cc_provincia` VALUES (13, '13', 'MANABI', 1);
INSERT INTO `cc_provincia` VALUES (14, '14', 'MORONA SANTIAGO', 1);
INSERT INTO `cc_provincia` VALUES (15, '15', 'NAPO', 1);
INSERT INTO `cc_provincia` VALUES (16, '16', 'PASTAZA', 1);
INSERT INTO `cc_provincia` VALUES (17, '17', 'PICHINCHA', 1);
INSERT INTO `cc_provincia` VALUES (18, '18', 'TUNGURAHUA', 1);
INSERT INTO `cc_provincia` VALUES (19, '19', 'ZAMORA CHINCHIPE', 1);
INSERT INTO `cc_provincia` VALUES (20, '20', 'GALAPAGOS', 1);
INSERT INTO `cc_provincia` VALUES (21, '21', 'SUCUMBIOS', 1);
INSERT INTO `cc_provincia` VALUES (22, '22', 'ORELLANA', 1);
INSERT INTO `cc_provincia` VALUES (23, '23', 'SANTO DOMINGO DE LOS TSACHILAS', 1);
INSERT INTO `cc_provincia` VALUES (24, '24', 'SANTA ELENA', 1);
INSERT INTO `cc_provincia` VALUES (25, '90', 'ZONAS NO DELIMITADAS', 1);

-- ----------------------------
-- Table structure for cc_proyectos
-- ----------------------------
DROP TABLE IF EXISTS `cc_proyectos`;
CREATE TABLE `cc_proyectos`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'ID del proyecto o subempresa',
  `proy_codigo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Codigo interno del proyecto',
  `proy_nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Nombre del proyecto o subempresa',
  `proy_ruc` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'RUC del proyecto o subempresa si aplica',
  `proy_direccion` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Direccion del proyecto',
  `proy_telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Telefono del proyecto',
  `proy_email` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Email del proyecto',
  `proy_estado` tinyint(0) NOT NULL DEFAULT 1 COMMENT '1 activo, 0 inactivo',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `idx_proy_codigo`(`proy_codigo`) USING BTREE,
  INDEX `idx_proy_estado`(`proy_estado`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'Proyectos o subempresas disponibles para segmentar la operacion' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_proyectos
-- ----------------------------
INSERT INTO `cc_proyectos` VALUES (1, 'PRY-001', 'PROYECTO PRINCIPAL', NULL, NULL, NULL, NULL, 1, '2026-07-28 11:26:03', '2026-07-28 11:26:03');

-- ----------------------------
-- Table structure for cc_puntos_venta
-- ----------------------------
DROP TABLE IF EXISTS `cc_puntos_venta`;
CREATE TABLE `cc_puntos_venta`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto al que pertenece este punto de venta/emisión',
  `fk_comprobante` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pv_establecimiento` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pv_emision` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pv_auth_sri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pv_fecha_vence_auth` date NOT NULL,
  `pv_sec_inicial` int(0) NOT NULL,
  `pv_sec_actual` int(0) NOT NULL DEFAULT 1,
  `pv_sec_final` int(0) NOT NULL,
  `pv_is_electronica` tinyint(0) NULL DEFAULT NULL,
  `pv_fk_bodega` int(0) NOT NULL,
  `pv_estado` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `pv_fecha_creacionpunto` date NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pv_fk_bodega`(`pv_fk_bodega`) USING BTREE,
  INDEX `pv_fk_comprobante`(`fk_comprobante`) USING BTREE,
  INDEX `idx_puntos_venta_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_puntos_venta_ibfk_1` FOREIGN KEY (`pv_fk_bodega`) REFERENCES `cc_bodegas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_puntos_venta_ibfk_2` FOREIGN KEY (`fk_comprobante`) REFERENCES `cc_tipos_comprobante` (`comp_codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_puntos_venta_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_puntos_venta
-- ----------------------------

-- ----------------------------
-- Table structure for cc_puntoventa_empleado
-- ----------------------------
DROP TABLE IF EXISTS `cc_puntoventa_empleado`;
CREATE TABLE `cc_puntoventa_empleado`  (
  `fk_punto_venta` int(0) NULL DEFAULT NULL,
  `fk_empleado` int(0) NULL DEFAULT NULL,
  `pvemp_fecha_registro` date NULL DEFAULT NULL,
  INDEX `fk_punto_venta`(`fk_punto_venta`) USING BTREE,
  INDEX `fk_empleado`(`fk_empleado`) USING BTREE,
  CONSTRAINT `cc_puntoventa_empleado_ibfk_1` FOREIGN KEY (`fk_punto_venta`) REFERENCES `cc_puntos_venta` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_puntoventa_empleado_ibfk_2` FOREIGN KEY (`fk_empleado`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_puntoventa_empleado
-- ----------------------------

-- ----------------------------
-- Table structure for cc_reserva_inventario
-- ----------------------------
DROP TABLE IF EXISTS `cc_reserva_inventario`;
CREATE TABLE `cc_reserva_inventario`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/empresa al que pertenece la reserva de inventario',
  `fk_producto` int(0) NOT NULL,
  `fk_bodega` int(0) NOT NULL,
  `fk_lote` int(0) NULL DEFAULT NULL,
  `res_codigo_transaccion` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `res_documento_id` int(0) NOT NULL,
  `res_cantidad` decimal(18, 6) NOT NULL DEFAULT 0.000000,
  `res_estado` enum('ACTIVA','LIBERADA','CONSUMIDA') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'ACTIVA',
  `res_fecha` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `res_observacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fk_user_id` int(0) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_producto_bodega`(`fk_producto`, `fk_bodega`) USING BTREE,
  INDEX `idx_estado`(`res_estado`) USING BTREE,
  INDEX `fk_bodega`(`fk_bodega`) USING BTREE,
  INDEX `fk_user_id`(`fk_user_id`) USING BTREE,
  INDEX `idx_origen`(`res_codigo_transaccion`, `res_documento_id`) USING BTREE,
  INDEX `fk_lote`(`fk_lote`) USING BTREE,
  INDEX `idx_reserva_inventario_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_reserva_inventario_ibfk_1` FOREIGN KEY (`fk_producto`) REFERENCES `cc_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_reserva_inventario_ibfk_2` FOREIGN KEY (`fk_bodega`) REFERENCES `cc_bodegas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_reserva_inventario_ibfk_3` FOREIGN KEY (`res_codigo_transaccion`) REFERENCES `cc_transacciones` (`tr_codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_reserva_inventario_ibfk_4` FOREIGN KEY (`fk_user_id`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_reserva_inventario_ibfk_5` FOREIGN KEY (`fk_lote`) REFERENCES `cc_lotes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_reserva_inventario
-- ----------------------------

-- ----------------------------
-- Table structure for cc_retencion
-- ----------------------------
DROP TABLE IF EXISTS `cc_retencion`;
CREATE TABLE `cc_retencion`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `ret_secuencial` int(0) NULL DEFAULT NULL,
  `ret_documento_id` int(0) NOT NULL,
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa de la retencion',
  `ret_tipo_transaccion_cod` int(0) NULL DEFAULT NULL,
  `ret_numero_comprobante` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ret_numero_emision` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ret_numero_establecimiento` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ret_autorizacion_sri` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ret_fecha_emision` date NOT NULL,
  `ret_fecha_registro` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `ret_clave_acceso` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ret_estado_sri` enum('PENDIENTE','ENVIADO','AUTORIZADO','RECHAZADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'PENDIENTE' COMMENT 'PENDIENTE,ENVIADO,AUTORIZADO,RECHAZADO',
  `ret_mensaje_sri` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `ret_fecha_autorizacion` datetime(0) NULL DEFAULT NULL,
  `ret_ambiente_sri` enum('PRUEBAS','PRODUCCION') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'PRUEBAS / PRODUCCION',
  `ret_xml_enviado` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `ret_xml_autorizado` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `ret_total_retenido` decimal(15, 2) NULL DEFAULT 0.00,
  `ret_estado` tinyint(0) NULL DEFAULT 1 COMMENT '1 ACTIVO, 0 ANULADO',
  `fk_user` int(0) NULL DEFAULT NULL,
  `ret_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `ret_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `ret_clave_acceso`(`ret_clave_acceso`) USING BTREE,
  INDEX `idx_ret_fk_compra`(`ret_documento_id`) USING BTREE,
  INDEX `idx_ret_estado_sri`(`ret_estado_sri`) USING BTREE,
  INDEX `idx_ret_fecha_emision`(`ret_fecha_emision`) USING BTREE,
  INDEX `idx_ret_fk_user`(`fk_user`) USING BTREE,
  INDEX `idx_ret_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `fk_ret_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_ret_user` FOREIGN KEY (`fk_user`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_retencion
-- ----------------------------

-- ----------------------------
-- Table structure for cc_retencion_det
-- ----------------------------
DROP TABLE IF EXISTS `cc_retencion_det`;
CREATE TABLE `cc_retencion_det`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_retencion` int(0) NOT NULL,
  `retd_tipo_retencion` enum('RENTA','IVA') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'IVA / RENTA',
  `retd_codigo_sri` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `retd_porcentaje` decimal(5, 2) NOT NULL,
  `retd_base_imponible` decimal(12, 2) NOT NULL,
  `retd_valor_retenido` decimal(12, 2) NOT NULL,
  `retd_descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fk_sri_retencion` int(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_retencion`(`fk_retencion`) USING BTREE,
  INDEX `cc_retencion_det_ibfk_2`(`fk_sri_retencion`) USING BTREE,
  CONSTRAINT `cc_retencion_det_ibfk_1` FOREIGN KEY (`fk_retencion`) REFERENCES `cc_retencion` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_retencion_det_ibfk_2` FOREIGN KEY (`fk_sri_retencion`) REFERENCES `cc_retencion_sri` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_retencion_det
-- ----------------------------

-- ----------------------------
-- Table structure for cc_retencion_sri
-- ----------------------------
DROP TABLE IF EXISTS `cc_retencion_sri`;
CREATE TABLE `cc_retencion_sri`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `ret_codigo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ret_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ret_porcentaje` decimal(65, 2) NULL DEFAULT NULL,
  `ret_cta_compras` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ret_cta_ventas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ret_impuesto` enum('RENTA','IVA') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'RENTA',
  `ret_impuesto_detalle` enum('RENTA','IVA_BIENES','IVA_SERVICIOS') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ret_val_compra` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ret_val_venta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `ret_estado` tinyint(0) NULL DEFAULT 1,
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 80 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_retencion_sri
-- ----------------------------
INSERT INTO `cc_retencion_sri` VALUES (1, '303', 'HONORARIOS PROFESIONALES Y DIETAS', 10.00, '2.01.07.01.04', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-07-05 17:53:16');
INSERT INTO `cc_retencion_sri` VALUES (2, '304', 'SERVICIOS PREDOMINA EL INTELECTO NO RELACIONADOS CON TITULO PROFESIONAL', 10.00, '2.01.07.01.03', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:19');
INSERT INTO `cc_retencion_sri` VALUES (3, '307', 'SERVICIOS PREDOMINA LA MANO DE OBRA', 2.00, '2.01.07.01.02', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:20');
INSERT INTO `cc_retencion_sri` VALUES (4, '308', 'UTILIZACION O APROVECHAMIENTO DE LA IMAGEN O RENOMBRE ', 10.00, '2.01.07.01.04', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:20');
INSERT INTO `cc_retencion_sri` VALUES (5, '309', 'SERVICIOS PUBLICIDAD Y COMUNICACION', 2.75, '2.01.07.01.01', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:21');
INSERT INTO `cc_retencion_sri` VALUES (6, '310', 'Servicio transporte privado de pasajeros o servicio pblico o privado de carga', 1.00, '2.01.07.01.01', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:22');
INSERT INTO `cc_retencion_sri` VALUES (7, '312', 'Transferencia de bienes muebles de naturaleza corporal', 1.75, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:22');
INSERT INTO `cc_retencion_sri` VALUES (8, '319', 'Arrendamiento mercantil', 2.00, '2.01.07.01.01', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:23');
INSERT INTO `cc_retencion_sri` VALUES (9, '320', 'Arrendamiento bienes inmuebles', 10.00, '2.01.07.01.03', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:24');
INSERT INTO `cc_retencion_sri` VALUES (10, '322', 'Seguros y reaseguros (primas y cesiones)', 1.00, '2.01.07.01.01', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:24');
INSERT INTO `cc_retencion_sri` VALUES (11, '323', 'Por rendimientos financieros pagados a naturales y sociedades  (No a IFIs)', 2.00, '2.01.07.01.02', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:25');
INSERT INTO `cc_retencion_sri` VALUES (12, '323A', 'Por RF: depósitos Cta. Corriente', 2.00, '2.01.07.01.02', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:26');
INSERT INTO `cc_retencion_sri` VALUES (13, '323B1', 'Por RF:  depósitos Cta. Ahorros Sociedades', 2.00, '2.01.07.01.02', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:26');
INSERT INTO `cc_retencion_sri` VALUES (14, '323C', 'Por rendimientos financieros:  depÃ³sitos en cuentas exentas', 0.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:27');
INSERT INTO `cc_retencion_sri` VALUES (15, '323D', 'Por rendimientos financieros: compra, cancelaciÃ³n o redenciÃ³n de mini bemÂ´s y bemÂ´s', 0.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:27');
INSERT INTO `cc_retencion_sri` VALUES (16, '323E', 'Por RF: deposito a plazo', 2.00, '2.01.07.01.02', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:28');
INSERT INTO `cc_retencion_sri` VALUES (17, '323F', 'Por rendimientos financieros: operaciones de reporto - repos', 2.00, '2.01.07.01.02', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:29');
INSERT INTO `cc_retencion_sri` VALUES (18, '323G', 'Por RF: inversiones (captaciones)', 2.00, '2.01.07.01.02', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:29');
INSERT INTO `cc_retencion_sri` VALUES (19, '323H', 'Por RF: obligaciones', 2.00, '2.01.07.01.02', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:30');
INSERT INTO `cc_retencion_sri` VALUES (20, '323I', 'Por RF: bonos convertible en acciones', 2.00, '2.01.07.01.02', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:31');
INSERT INTO `cc_retencion_sri` VALUES (21, '323J', 'Por RF: bonos de organismos y gobiernos extranjeros', 2.00, '2.01.07.01.02', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:31');
INSERT INTO `cc_retencion_sri` VALUES (22, '323K', 'Por RF: entre IFI\'s', 2.00, '2.01.07.01.02', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:32');
INSERT INTO `cc_retencion_sri` VALUES (23, '325', 'Anticipo Dividendos', 22.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:33');
INSERT INTO `cc_retencion_sri` VALUES (24, '327', 'Dividendos distribuidos a personas naturales residentes', 1.75, '2.01.07.01.01', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:33');
INSERT INTO `cc_retencion_sri` VALUES (25, '328', 'Dividendos distribuidos a sociedades residentes', 0.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:34');
INSERT INTO `cc_retencion_sri` VALUES (26, '332', 'Otras compras de bienes y servicios no sujetas a retencion', 0.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:34');
INSERT INTO `cc_retencion_sri` VALUES (27, '334', 'Enajenacion de derechos representativos de capital y otros derecho no cotizados en bolsa ecuatoriana', 1.00, '2.01.07.01.01', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:35');
INSERT INTO `cc_retencion_sri` VALUES (28, '343A', 'Por energia electrica', 1.00, '2.01.07.01.01', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:36');
INSERT INTO `cc_retencion_sri` VALUES (29, '341', 'Impuesto Unico a la exportacion de banano de produccion propia-componente 2', 1.00, '2.01.07.01.01', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:36');
INSERT INTO `cc_retencion_sri` VALUES (30, '343', 'Otras retenciones aplicables el 1%', 1.00, '2.01.07.01.01', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:42');
INSERT INTO `cc_retencion_sri` VALUES (31, '345', 'Otras retenciones aplicables el 8%', 8.00, '2.01.07.01.03', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:43');
INSERT INTO `cc_retencion_sri` VALUES (32, '346', 'Dividendos sociedades en paraÃ­sos fiscales', 1.75, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:43');
INSERT INTO `cc_retencion_sri` VALUES (33, '347', 'Dividendos anticipados', 22.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:44');
INSERT INTO `cc_retencion_sri` VALUES (34, '348', 'Compra local de banano a productor', 1.00, '2.01.07.01.02', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:45');
INSERT INTO `cc_retencion_sri` VALUES (35, '349', 'Impuesto a la actividad bananera productor-exportador', 2.00, '2.01.07.01.02', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:45');
INSERT INTO `cc_retencion_sri` VALUES (36, '500', 'Pago al exterior - Rentas Inmobiliarias', 25.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:46');
INSERT INTO `cc_retencion_sri` VALUES (37, '501', 'Pago al exterior - Beneficios Empresariales', 25.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:46');
INSERT INTO `cc_retencion_sri` VALUES (38, '502', 'Pago al exterior - Servicios Empresariales', 0.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:47');
INSERT INTO `cc_retencion_sri` VALUES (39, '503', 'Pago al exterior - NavegaciÃ³n MarÃ­tima y/o aÃ©rea', 25.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:48');
INSERT INTO `cc_retencion_sri` VALUES (40, '504A', 'Pago al exterior - Dividendos a Sociedades', 1.75, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:48');
INSERT INTO `cc_retencion_sri` VALUES (41, '505', 'Pago al exterior - Intereses', 25.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:49');
INSERT INTO `cc_retencion_sri` VALUES (42, '506', 'Pago al exterior - Intereses por Finaciamiento de proveedores externos', 0.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:50');
INSERT INTO `cc_retencion_sri` VALUES (43, '507', 'Pago al exterior - Intereses de crÃ©ditos externos', 0.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:50');
INSERT INTO `cc_retencion_sri` VALUES (44, '508', 'Pago al exterior - CrÃ©ditos de IFI\'s organismos y gobierno a gobierno', 0.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:51');
INSERT INTO `cc_retencion_sri` VALUES (45, '509', 'Pago al exterior - Cánones, derechos de autor, marcas, patentes y similares', 25.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:52');
INSERT INTO `cc_retencion_sri` VALUES (46, '510', 'Pago al exterior - Ganancias de capital', 5.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:52');
INSERT INTO `cc_retencion_sri` VALUES (47, '511', 'Pago al exterior - Servicios profesionales independientes', 25.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:53');
INSERT INTO `cc_retencion_sri` VALUES (48, '512', 'Pago al exterior - Servicios profesionales dependientes', 25.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:53');
INSERT INTO `cc_retencion_sri` VALUES (49, '513', 'Pago al exterior - Artistas ', 25.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:54');
INSERT INTO `cc_retencion_sri` VALUES (50, '514', 'Pago al exterior - Participacion de consejeros', 25.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:55');
INSERT INTO `cc_retencion_sri` VALUES (51, '515', 'Pago al exterior - Entretenimiento Publico', 25.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:01:23');
INSERT INTO `cc_retencion_sri` VALUES (52, '516', 'Pago al exterior - Pensiones', 25.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:01:30');
INSERT INTO `cc_retencion_sri` VALUES (53, '517', 'Pago al exterior - Reembolso de Gastos', 25.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:01:31');
INSERT INTO `cc_retencion_sri` VALUES (54, '518', 'Pago al exterior - Funciones Publicas', 25.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:01:44');
INSERT INTO `cc_retencion_sri` VALUES (55, '519', 'Pago al exterior - Estudiantes', 25.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:01:45');
INSERT INTO `cc_retencion_sri` VALUES (56, '520', 'Pago al exterior - Por otros conceptos ', 0.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:01:48');
INSERT INTO `cc_retencion_sri` VALUES (57, '1', 'Retención de IVA Bienes', 30.00, '2.01.07.01.05', '1.01.05.01.04', 'IVA', 'IVA_BIENES', 'iva', 'iva', 1, '2026-05-23 10:03:20', '2026-05-23 11:03:06');
INSERT INTO `cc_retencion_sri` VALUES (58, '2', 'Retención de IVA Servicios', 70.00, '2.01.07.01.06', '1.01.05.01.05', 'IVA', 'IVA_SERVICIOS', 'iva', 'iva', 1, '2026-05-23 10:03:20', '2026-05-23 11:03:11');
INSERT INTO `cc_retencion_sri` VALUES (59, '3', 'Retencion de IVA total (Arriendos, honorarios, etc)', 100.00, '2.01.07.01.07', '1.01.05.01.06', 'IVA', 'IVA_SERVICIOS', 'iva', 'iva', 1, '2026-05-23 10:03:20', '2026-05-23 11:17:37');
INSERT INTO `cc_retencion_sri` VALUES (60, '340B', 'Por actividades de construccion de obra material inmueble,urbanizacion,lotizacion o actividades similares', 1.00, '2.01.07.01.01', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:01:50');
INSERT INTO `cc_retencion_sri` VALUES (61, '332A', 'Por la enajenacion ocasional de acciones y participaciones y titulos valores', 0.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:01:51');
INSERT INTO `cc_retencion_sri` VALUES (62, '332B', 'Compra de Bienes Inmuebles', 0.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:01:52');
INSERT INTO `cc_retencion_sri` VALUES (63, '332C', 'Transporte publico de pasajeros', 0.00, '2.01.07.01.14', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:01:53');
INSERT INTO `cc_retencion_sri` VALUES (64, '304A ', 'Comisiones y demás pagos por servicios predomina intelecto no relacionados con el título profesional', 10.00, '2.01.07.01.03', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:01:54');
INSERT INTO `cc_retencion_sri` VALUES (65, '304B', 'Pagos a notarios y registradores de la propiedad y mercantil por sus actividades ejercidas como tales', 10.00, '2.01.07.01.03', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:01:56');
INSERT INTO `cc_retencion_sri` VALUES (66, '304C', 'Pagos a deportistas, entrenadores,arbitros,miembros del cuerpo tecnico por sus actividades ejercidas como tales', 8.00, '2.01.07.01.03', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:01:57');
INSERT INTO `cc_retencion_sri` VALUES (67, '304D', 'Pagos a artistas por sus actividades ejercidas como tales', 8.00, '2.01.07.01.03', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:00');
INSERT INTO `cc_retencion_sri` VALUES (68, '304E', 'Honorarios y demas pagos por servicios de docencia ', 10.00, '2.01.07.01.03', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:01');
INSERT INTO `cc_retencion_sri` VALUES (69, '311', 'Por pagos a traves de liquidacion de compra (nivel cultural o rusticidad)', 2.00, '2.01.07.01.02', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:02');
INSERT INTO `cc_retencion_sri` VALUES (70, '3440', 'Otras retenciones aplicables el 2.75%', 2.75, '2.01.07.01.02', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:03');
INSERT INTO `cc_retencion_sri` VALUES (71, '9', 'Retencion IVA Bienes', 10.00, '2.01.07.01.18', NULL, 'IVA', 'IVA_BIENES', 'iva', 'iva', 1, '2026-05-23 10:03:20', '2026-05-23 11:03:40');
INSERT INTO `cc_retencion_sri` VALUES (72, '10', 'Retencion IVA servicios', 20.00, '2.01.07.01.17', '1.01.05.01.07', 'IVA', 'IVA_SERVICIOS', 'iva', 'iva', 1, '2026-05-23 10:03:20', '2026-05-23 11:03:43');
INSERT INTO `cc_retencion_sri` VALUES (73, '8', 'Retención IVA', 0.00, '2.01.07.01.12', NULL, 'IVA', 'IVA_BIENES', 'iva', 'iva', 1, '2026-05-23 10:03:20', '2026-07-05 18:18:12');
INSERT INTO `cc_retencion_sri` VALUES (74, '4', 'RETENCIÓN DE IVA SERVICIOS EXPORTADORES', 50.00, '2.01.07.01.19', NULL, 'IVA', 'IVA_SERVICIOS', 'iva', 'iva', 1, '2026-05-23 10:03:20', '2026-07-05 18:24:29');
INSERT INTO `cc_retencion_sri` VALUES (75, '344A', 'Pago local tarjeta de crédito reportada por la Emisora de tarjeta de crédito, solo recap', 2.00, '2.01.07.01.02', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:05');
INSERT INTO `cc_retencion_sri` VALUES (76, '312A', 'Para productos agricolas', 1.00, '2.01.07.01.01', '1.01.05.03.02', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:06');
INSERT INTO `cc_retencion_sri` VALUES (77, '303A', 'Servicios profesionales prestados por sociedades residentes', 3.00, '2.01.07.01.20', NULL, 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:07');
INSERT INTO `cc_retencion_sri` VALUES (78, '312C', 'Compras al comercializador: de bienes de origen bioacuático, forestal y los descritos  el art.27.1 de lrti', 1.75, '2.01.07.01.14', NULL, 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 1, '2026-05-23 10:03:20', '2026-05-23 11:02:08');
INSERT INTO `cc_retencion_sri` VALUES (79, '5011', 'RETENCION PRUEBA', 5.00, '2.01.01.02', '1.01.01.03', 'RENTA', 'RENTA', 'subtotalNeto', 'subtotalNeto', 0, '2026-05-23 10:03:20', '2026-07-05 18:05:39');

-- ----------------------------
-- Table structure for cc_roles
-- ----------------------------
DROP TABLE IF EXISTS `cc_roles`;
CREATE TABLE `cc_roles`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `rol_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `rol_estado` tinyint(0) NULL DEFAULT NULL,
  `rol_fecha_creacion` date NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_roles
-- ----------------------------
INSERT INTO `cc_roles` VALUES (1, 'ADMIN', 1, '2023-12-20');
INSERT INTO `cc_roles` VALUES (2, 'COMPRADOR', 1, '2023-12-20');
INSERT INTO `cc_roles` VALUES (3, 'VENDEDOR', 1, '2023-12-27');
INSERT INTO `cc_roles` VALUES (6, 'TESTER0', 0, '2024-04-14');

-- ----------------------------
-- Table structure for cc_roles_accion
-- ----------------------------
DROP TABLE IF EXISTS `cc_roles_accion`;
CREATE TABLE `cc_roles_accion`  (
  `fk_rol` int(0) NULL DEFAULT NULL,
  `fk_accion` int(0) NULL DEFAULT NULL,
  `ra_fecha` date NULL DEFAULT NULL,
  INDEX `fk_rol`(`fk_rol`) USING BTREE,
  INDEX `fk_accion`(`fk_accion`) USING BTREE,
  CONSTRAINT `cc_roles_accion_ibfk_1` FOREIGN KEY (`fk_rol`) REFERENCES `cc_roles` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_roles_accion_ibfk_2` FOREIGN KEY (`fk_accion`) REFERENCES `cc_acciones` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_roles_accion
-- ----------------------------
INSERT INTO `cc_roles_accion` VALUES (3, 4, '2024-01-24');
INSERT INTO `cc_roles_accion` VALUES (6, 1, '2024-03-20');
INSERT INTO `cc_roles_accion` VALUES (6, 2, '2024-03-20');
INSERT INTO `cc_roles_accion` VALUES (6, 3, '2024-03-20');
INSERT INTO `cc_roles_accion` VALUES (6, 4, '2024-03-20');
INSERT INTO `cc_roles_accion` VALUES (6, 5, '2024-03-20');
INSERT INTO `cc_roles_accion` VALUES (2, 1, '2026-03-23');
INSERT INTO `cc_roles_accion` VALUES (2, 2, '2026-03-23');
INSERT INTO `cc_roles_accion` VALUES (2, 4, '2026-03-23');
INSERT INTO `cc_roles_accion` VALUES (2, 8, '2026-03-23');
INSERT INTO `cc_roles_accion` VALUES (1, 1, '2026-08-09');
INSERT INTO `cc_roles_accion` VALUES (1, 2, '2026-08-09');
INSERT INTO `cc_roles_accion` VALUES (1, 3, '2026-08-09');
INSERT INTO `cc_roles_accion` VALUES (1, 4, '2026-08-09');
INSERT INTO `cc_roles_accion` VALUES (1, 5, '2026-08-09');
INSERT INTO `cc_roles_accion` VALUES (1, 8, '2026-08-09');
INSERT INTO `cc_roles_accion` VALUES (1, 13, '2026-08-09');

-- ----------------------------
-- Table structure for cc_roles_modulos
-- ----------------------------
DROP TABLE IF EXISTS `cc_roles_modulos`;
CREATE TABLE `cc_roles_modulos`  (
  `fk_modulo` int(0) NULL DEFAULT NULL,
  `fk_rol` int(0) NULL DEFAULT NULL,
  `rm_fecha` date NULL DEFAULT NULL,
  INDEX `fk_modulo`(`fk_modulo`) USING BTREE,
  INDEX `fk_rol`(`fk_rol`) USING BTREE,
  CONSTRAINT `cc_roles_modulos_ibfk_1` FOREIGN KEY (`fk_modulo`) REFERENCES `cc_modulos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_roles_modulos_ibfk_2` FOREIGN KEY (`fk_rol`) REFERENCES `cc_roles` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_roles_modulos
-- ----------------------------
INSERT INTO `cc_roles_modulos` VALUES (2, 3, '2024-01-24');
INSERT INTO `cc_roles_modulos` VALUES (6, 3, '2024-01-24');
INSERT INTO `cc_roles_modulos` VALUES (1, 6, '2024-03-20');
INSERT INTO `cc_roles_modulos` VALUES (2, 6, '2024-03-20');
INSERT INTO `cc_roles_modulos` VALUES (3, 6, '2024-03-20');
INSERT INTO `cc_roles_modulos` VALUES (4, 6, '2024-03-20');
INSERT INTO `cc_roles_modulos` VALUES (5, 6, '2024-03-20');
INSERT INTO `cc_roles_modulos` VALUES (12, 6, '2024-03-20');
INSERT INTO `cc_roles_modulos` VALUES (6, 6, '2024-03-20');
INSERT INTO `cc_roles_modulos` VALUES (7, 6, '2024-03-20');
INSERT INTO `cc_roles_modulos` VALUES (8, 6, '2024-03-20');
INSERT INTO `cc_roles_modulos` VALUES (9, 6, '2024-03-20');
INSERT INTO `cc_roles_modulos` VALUES (1, 2, '2026-03-23');
INSERT INTO `cc_roles_modulos` VALUES (3, 2, '2026-03-23');
INSERT INTO `cc_roles_modulos` VALUES (4, 2, '2026-03-23');
INSERT INTO `cc_roles_modulos` VALUES (6, 2, '2026-03-23');
INSERT INTO `cc_roles_modulos` VALUES (8, 2, '2026-03-23');
INSERT INTO `cc_roles_modulos` VALUES (9, 2, '2026-03-23');
INSERT INTO `cc_roles_modulos` VALUES (1, 1, '2026-08-09');
INSERT INTO `cc_roles_modulos` VALUES (2, 1, '2026-08-09');
INSERT INTO `cc_roles_modulos` VALUES (3, 1, '2026-08-09');
INSERT INTO `cc_roles_modulos` VALUES (4, 1, '2026-08-09');
INSERT INTO `cc_roles_modulos` VALUES (5, 1, '2026-08-09');
INSERT INTO `cc_roles_modulos` VALUES (7, 1, '2026-08-09');
INSERT INTO `cc_roles_modulos` VALUES (8, 1, '2026-08-09');
INSERT INTO `cc_roles_modulos` VALUES (9, 1, '2026-08-09');
INSERT INTO `cc_roles_modulos` VALUES (12, 1, '2026-08-09');

-- ----------------------------
-- Table structure for cc_sectores
-- ----------------------------
DROP TABLE IF EXISTS `cc_sectores`;
CREATE TABLE `cc_sectores`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `sec_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `sec_descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `sec_estado` tinyint(0) NULL DEFAULT NULL,
  `fk_anillo` int(0) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_anillo`(`fk_anillo`) USING BTREE,
  CONSTRAINT `cc_sectores_ibfk_1` FOREIGN KEY (`fk_anillo`) REFERENCES `cc_anillo` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_sectores
-- ----------------------------
INSERT INTO `cc_sectores` VALUES (1, 'LOS ENCUENTROS', 'SOLO LOS ENCUENTROS CENTRO', 1, 1, '2025-10-07 15:26:54', '2025-10-07 15:28:03');
INSERT INTO `cc_sectores` VALUES (2, 'YANTZAZA CENTRO', 'PARA PROVEEDORES DEL ANILLO 2', 1, 2, '2025-10-07 15:50:54', '2026-08-02 08:30:33');

-- ----------------------------
-- Table structure for cc_servicios
-- ----------------------------
DROP TABLE IF EXISTS `cc_servicios`;
CREATE TABLE `cc_servicios`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `serv_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `serv_descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `serv_abreviatura` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `serv_color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `serv_estado` tinyint(0) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_servicios
-- ----------------------------
INSERT INTO `cc_servicios` VALUES (1, 'OTROS', 'OTROS SERVICIOS', 'OTR', '#5656D', 1, '2025-11-29 10:44:33', '2025-11-29 10:44:33');

-- ----------------------------
-- Table structure for cc_settings
-- ----------------------------
DROP TABLE IF EXISTS `cc_settings`;
CREATE TABLE `cc_settings`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `st_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `st_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `st_detalle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_settings
-- ----------------------------
INSERT INTO `cc_settings` VALUES (1, 'IVA', '15', 'IVA ACTUAL');
INSERT INTO `cc_settings` VALUES (2, 'SYSTEM_NAME', 'CCFACT', 'NOMBRE DEL SISTEMA');
INSERT INTO `cc_settings` VALUES (3, 'IVA2', '12', 'IVA TEMPORAL');
INSERT INTO `cc_settings` VALUES (4, 'EMAIL_TEST', 'soporteccfact@ccfact.com', 'Email usado para pruebas de envio de correos');
INSERT INTO `cc_settings` VALUES (5, 'SYSTEM_DEVELOP', '0', '0=LOCAL, 1=QA/PRUEBAS, 2=PRODUCCION');
INSERT INTO `cc_settings` VALUES (8, 'ABREVIATURA_AUTO_COD', 'CCF-', '');
INSERT INTO `cc_settings` VALUES (9, 'PERMITIR_ITEMS_DUPLICADOS', 'true', 'SIRVE PARA QUE EN LOS CART PERMITA O NO CARGAR ITEMS DUPLICADOS, ESTO SE MANEJA MAS CUANDO HAY CONTROL DE LOTES');
INSERT INTO `cc_settings` VALUES (10, 'VALOR_MAXIMO_ANEXO_ATS_SRI', '500', 'Cuando la factura sea mayor o igual a este valor hay que declarar la forma de pago ats');
INSERT INTO `cc_settings` VALUES (11, 'SRI_URL_AUTORIZACION', 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl', 'Link para poder importar facturas del SRI');
INSERT INTO `cc_settings` VALUES (12, 'TOLERANCIA_MARCACION_MINUTOS', '5', 'Minutos para considerar una marcacion como repetida');
INSERT INTO `cc_settings` VALUES (13, 'PERMITIR_MULTIPLE_CONSUMO_SERVICIO', '1', 'Permite mas de un consumo valido por servicio en el mismo dia');

-- ----------------------------
-- Table structure for cc_stock_bodega
-- ----------------------------
DROP TABLE IF EXISTS `cc_stock_bodega`;
CREATE TABLE `cc_stock_bodega`  (
  `fk_bodega` int(0) NULL DEFAULT NULL,
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/empresa al que pertenece el saldo de stock por bodega',
  `fk_producto` int(0) NULL DEFAULT NULL,
  `stb_stock` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `stb_created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `stb_updated_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  INDEX `fk_bodega`(`fk_bodega`) USING BTREE,
  INDEX `fk_producto`(`fk_producto`) USING BTREE,
  INDEX `idx_stock_producto_bodega`(`fk_producto`, `fk_bodega`) USING BTREE,
  INDEX `idx_stock_bodega`(`fk_bodega`) USING BTREE,
  INDEX `idx_stock_producto`(`fk_producto`) USING BTREE,
  INDEX `idx_stock_stock`(`stb_stock`) USING BTREE,
  INDEX `idx_stock_bodega_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_stock_bodega_ibfk_1` FOREIGN KEY (`fk_bodega`) REFERENCES `cc_bodegas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_stock_bodega_ibfk_2` FOREIGN KEY (`fk_producto`) REFERENCES `cc_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_stock_bodega
-- ----------------------------

-- ----------------------------
-- Table structure for cc_stock_bodega_lote
-- ----------------------------
DROP TABLE IF EXISTS `cc_stock_bodega_lote`;
CREATE TABLE `cc_stock_bodega_lote`  (
  `fk_bodega` int(0) NULL DEFAULT NULL,
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/empresa al que pertenece el saldo de stock por bodega y lote',
  `fk_producto` int(0) NULL DEFAULT NULL,
  `stbl_stock` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fk_lote` int(0) NULL DEFAULT NULL,
  `stbl_created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `stbl_updated_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  INDEX `fk_bodega`(`fk_bodega`) USING BTREE,
  INDEX `fk_producto`(`fk_producto`) USING BTREE,
  INDEX `fk_lote`(`fk_lote`) USING BTREE,
  INDEX `idx_stock_bodega_lote_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_stock_bodega_lote_ibfk_1` FOREIGN KEY (`fk_bodega`) REFERENCES `cc_bodegas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_stock_bodega_lote_ibfk_2` FOREIGN KEY (`fk_producto`) REFERENCES `cc_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_stock_bodega_lote_ibfk_3` FOREIGN KEY (`fk_lote`) REFERENCES `cc_lotes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_stock_bodega_lote
-- ----------------------------

-- ----------------------------
-- Table structure for cc_subgrupos
-- ----------------------------
DROP TABLE IF EXISTS `cc_subgrupos`;
CREATE TABLE `cc_subgrupos`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `sgr_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `sgr_detalle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `sgr_fecha_creacion` date NULL DEFAULT NULL,
  `sgr_icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `sgr_estado` tinyint(0) NULL DEFAULT NULL,
  `fk_grupo` int(0) NULL DEFAULT NULL,
  `sgr_fecha_actualizacion` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sgr_fk_grupo`(`fk_grupo`) USING BTREE,
  INDEX `idx_subgrupo_grupo`(`fk_grupo`) USING BTREE,
  CONSTRAINT `cc_subgrupos_ibfk_1` FOREIGN KEY (`fk_grupo`) REFERENCES `cc_grupos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_subgrupos
-- ----------------------------
INSERT INTO `cc_subgrupos` VALUES (1, 'CEREALES', 'TODOS LOS CEREALES DE LA BODEGA', '2024-06-12', 'fas fa-folder', 1, 1, '2025-11-10 19:18:13');
INSERT INTO `cc_subgrupos` VALUES (2, 'COMPUTADORAS', 'SOLO COMPUTADORAS', '2024-06-12', 'fas fa-box', 1, 2, '2025-11-10 19:18:13');
INSERT INTO `cc_subgrupos` VALUES (3, 'LACTEOS', 'PRODUCTOS EN SUBGRUPO LACTEOS', '2025-11-24', NULL, 1, 1, '2025-11-24 15:09:44');
INSERT INTO `cc_subgrupos` VALUES (4, 'ACEITITO', 'PRODUCTOS AL SUBGRUPO ACEITITO', '2026-04-23', NULL, 1, 4, '2026-04-23 15:46:51');
INSERT INTO `cc_subgrupos` VALUES (5, 'MANTEQUITA', 'PRODUCTOS AL SUBGRUPO MANTEQUITA', '2026-04-23', NULL, 1, 5, '2026-04-23 15:49:51');
INSERT INTO `cc_subgrupos` VALUES (9, 'HORMIGAS', 'PRODUCTOS AL SUBGRUPO HORMIGAS', '2026-04-23', NULL, 1, 3, '2026-04-23 15:53:27');
INSERT INTO `cc_subgrupos` VALUES (10, 'CERDO', 'PRODUCTOS EN SUBGRUPO CERDO', '2026-04-23', NULL, 1, 3, '2026-04-23 16:57:23');
INSERT INTO `cc_subgrupos` VALUES (11, 'SACOS', 'PRODUCTOS AL SUBGRUPO SACOS', '2026-04-30', NULL, 1, 6, '2026-04-30 16:52:51');
INSERT INTO `cc_subgrupos` VALUES (12, 'AZUCARES', 'PRODUCTOS AL SUBGRUPO AZUCARES', '2026-04-30', NULL, 1, 1, '2026-04-30 17:16:33');
INSERT INTO `cc_subgrupos` VALUES (13, 'ELECTRICIDAD', 'PRODUCTOS AL SUBGRUPO ELECTRICIDAD', '2026-05-01', NULL, 1, 7, '2026-05-01 11:49:35');
INSERT INTO `cc_subgrupos` VALUES (14, 'GASEOSAS', 'PRODUCTOS AL SUBGRUPO GASEOSAS', '2026-05-03', NULL, 1, 1, '2026-05-03 12:41:51');
INSERT INTO `cc_subgrupos` VALUES (15, 'EPA', 'PRODUCTOS AL SUBGRUPO EPA', '2026-06-20', NULL, 1, 8, '2026-06-20 14:34:46');
INSERT INTO `cc_subgrupos` VALUES (16, 'OTROS', 'PRODUCTOS AL SUBGRUPO OTROS', '2026-06-20', NULL, 1, 8, '2026-06-20 16:01:55');
INSERT INTO `cc_subgrupos` VALUES (17, 'PVC', 'PRODUCTOS AL SUBGRUPO PVC', '2026-07-10', NULL, 1, 6, '2026-07-10 11:08:01');
INSERT INTO `cc_subgrupos` VALUES (18, 'DESCUENTOS NDC', 'PRODUCTOS AL SUBGRUPO DESCUENTOS NDC', '2026-07-26', NULL, 1, 9, '2026-07-26 11:37:09');

-- ----------------------------
-- Table structure for cc_sustentos
-- ----------------------------
DROP TABLE IF EXISTS `cc_sustentos`;
CREATE TABLE `cc_sustentos`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `sus_codigo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sus_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sus_tipo_comprobante` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sus_estado` tinyint(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `cod_sustento`(`sus_codigo`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_sustentos
-- ----------------------------
INSERT INTO `cc_sustentos` VALUES (1, '00', 'CASOS ESPECIALES CUYO SUSTENTO NO APLICA EN LAS OPCIONES ANTERIORES', '01, 02,04, 05, 42', 1);
INSERT INTO `cc_sustentos` VALUES (2, '01', 'CREDITO TRIBUTARIO PARA DECLARACION DE IVA (SERVICIOS Y BIENES DISTINTOS DE INVENTARIOS Y ACTIVOS FIJOS)', '01, 03, 04, 05, 11, 12, 21, 41, 43, 47, 48', 1);
INSERT INTO `cc_sustentos` VALUES (3, '02', 'COSTO O GASTO PARA DECLARACION DE IR (SERVICIOS Y BIENES DISTINTOS DE INVENTARIOS Y ACTIVOS FIJOS)', '01,0 2, 03, 04, 05, 11, 12, 15, 19, 20, 21, 41, 43, 47, 48', 1);
INSERT INTO `cc_sustentos` VALUES (4, '03', 'ACTIVO FIJO - CREDITO TRIBUTARIO PARA DECLARACIÓN DE IVA', '01, 03,0 4, 05, 41, 47, 48', 1);
INSERT INTO `cc_sustentos` VALUES (5, '04', 'ACTIVO FIJO - COSTO O GASTO PARA DECLARACION DE IR', '01, 02, 03, 04, 05, 15, 41, 47, 48', 1);
INSERT INTO `cc_sustentos` VALUES (6, '05', 'LIQUIDACION GASTOS DE VIAJE, HOSPEDAJE Y ALIMENTACION GASTOS IR (A NOMBRE DE EMPLEADOS Y NO DE LA EMPRESA)', '01, 02, 03, 04, 05, 11, 15, 41', 1);
INSERT INTO `cc_sustentos` VALUES (7, '06', 'INVENTARIO - CREDITO TRIBUTARIO PARA DECLARACION DE IVA', '01,0 3, 04, 05, 41, 43, 47, 48', 1);
INSERT INTO `cc_sustentos` VALUES (8, '07', 'INVENTARIO - COSTO O GASTO PARA DECLARACION DE IR', '01, 02, 03, 04, 05, 15, 41, 43, 47, 48', 1);
INSERT INTO `cc_sustentos` VALUES (9, '08', 'VALOR PAGADO PARA SOLICITAR REEMBOLSO DE GASTO (INTERMEDIARIO)', '01, 02, 03, 04, 05, 21', 1);
INSERT INTO `cc_sustentos` VALUES (10, '09', 'REEMBOLSO POR SINIESTROS', '45, 04, 05', 1);
INSERT INTO `cc_sustentos` VALUES (11, '10', 'DISTRIBUCION DE DIVIDENDOS, BENEFICIOS O UTILIDADES', '19', 1);
INSERT INTO `cc_sustentos` VALUES (12, '00023', 'SUSTENTO TEST NUEVOX', '01,02,03', 0);

-- ----------------------------
-- Table structure for cc_tipo_compra
-- ----------------------------
DROP TABLE IF EXISTS `cc_tipo_compra`;
CREATE TABLE `cc_tipo_compra`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'ID del tipo de compra',
  `tc_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Nombre del tipo de compra',
  `tc_descripcion` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Descripcion del tipo de compra',
  `tc_codigo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Codigo interno (INV, GAS, ACT)',
  `tc_estado` tinyint(0) NULL DEFAULT 1 COMMENT '1 ACTIVO, 0 INACTIVO',
  `tc_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `tc_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_tipo_compra
-- ----------------------------
INSERT INTO `cc_tipo_compra` VALUES (1, 'ADQUISICIONES Y PAGOS (EXCLUYE ACTIVOS FIJOS) GRAVADOS TARIFA DIFERENTE DE CERO (CON DERECHO A CRÉDITO TRIBUTARIO)', NULL, '500', 1, '2026-05-02 15:13:15', '2026-05-22 16:00:44');
INSERT INTO `cc_tipo_compra` VALUES (2, 'ADQUISICIONES LOCALES DE ACTIVOS FIJOS GRAVADOS TARIFA DIFERENTE DE CERO (CON DERECHO A CRÉDITO TRIBUTARIO)', NULL, '501', 1, '2026-05-02 15:13:30', '2026-05-22 16:00:44');
INSERT INTO `cc_tipo_compra` VALUES (3, 'OTRAS ADQUISICIONES Y PAGOS GRAVADOS TARIFA DIFERENTE DE CERO (SIN DERECHO A CRÉDITO TRIBUTARIO)', NULL, '502', 1, '2026-05-02 15:13:48', '2026-05-22 16:00:44');
INSERT INTO `cc_tipo_compra` VALUES (4, 'IMPORTACIONES DE SERVICIOS GRAVADOS TARIFA DIFERENTE DE CERO', NULL, '503', 1, '2026-05-02 15:14:37', '2026-05-22 16:00:44');
INSERT INTO `cc_tipo_compra` VALUES (5, 'IMPORTACIONES DE BIENES (EXCLUYE ACTIVOS FIJOS) GRAVADOS TARIFA DIFERENTE DE CERO', NULL, '504', 1, '2026-05-22 16:01:38', '2026-05-22 16:01:38');
INSERT INTO `cc_tipo_compra` VALUES (6, 'IMPORTACIONES DE ACTIVOS FIJOS GRAVADOS TARIFA DIFERENTE DE CERO', NULL, '505', 1, '2026-05-22 16:01:38', '2026-05-22 16:01:38');
INSERT INTO `cc_tipo_compra` VALUES (7, 'IMPORTACIONES DE BIENES (INCLUYE ACTIVOS FIJOS) GRAVADOS TARIFA 0% ', NULL, '506', 1, '2026-05-22 16:01:38', '2026-05-22 16:01:38');
INSERT INTO `cc_tipo_compra` VALUES (8, 'ADQUISICIONES Y PAGOS (INCLUYE ACTIVOS FIJOS) GRAVADOS TARIFA 0%', NULL, '507', 1, '2026-05-22 16:01:38', '2026-05-22 16:01:38');
INSERT INTO `cc_tipo_compra` VALUES (9, 'ADQUISICIONES REALIZADAS A CONTRIBUYENTES RISE', NULL, '508', 1, '2026-05-22 16:01:38', '2026-05-22 16:01:38');
INSERT INTO `cc_tipo_compra` VALUES (10, 'ADQUISICIONES NO OBJETO DE IVA', NULL, '531', 1, '2026-05-22 16:01:38', '2026-05-22 16:01:38');
INSERT INTO `cc_tipo_compra` VALUES (11, 'ADQUISICIONES EXENTAS DE PAGO IVA', NULL, '532', 1, '2026-05-22 16:01:38', '2026-05-22 16:01:38');
INSERT INTO `cc_tipo_compra` VALUES (12, 'PAGOS NETOS POR REEMBOLSO COMO INTERMEDIARIO ', NULL, '535', 1, '2026-05-22 16:01:38', '2026-05-22 16:01:38');

-- ----------------------------
-- Table structure for cc_tipo_documento
-- ----------------------------
DROP TABLE IF EXISTS `cc_tipo_documento`;
CREATE TABLE `cc_tipo_documento`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `doc_codigo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `doc_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `doc_descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_tipo_documento
-- ----------------------------
INSERT INTO `cc_tipo_documento` VALUES (1, '04', 'RUC', 'DOCUMENTO PERSONAS JURIDICAS');
INSERT INTO `cc_tipo_documento` VALUES (2, '05', 'CEDULA', 'DOCUMENTO DE IDENTIDAD ECIATORIANA');
INSERT INTO `cc_tipo_documento` VALUES (3, '06', 'PASAPORTE', 'DOCUMENTO PARA GENTE EXTANGERA');

-- ----------------------------
-- Table structure for cc_tipo_precios
-- ----------------------------
DROP TABLE IF EXISTS `cc_tipo_precios`;
CREATE TABLE `cc_tipo_precios`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `tpc_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `tpc_descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `tpc_estado` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `tpc_fecha_creacion` date NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_tipo_precios
-- ----------------------------
INSERT INTO `cc_tipo_precios` VALUES (1, 'pA', 'PRECIO PARA CLIENTES FINALES', '1', '2024-07-04');
INSERT INTO `cc_tipo_precios` VALUES (2, 'pB', 'PRECIO PARA CLIENTES FINALES APLICANDOLE DESCUENTOS', '1', '2024-07-04');

-- ----------------------------
-- Table structure for cc_tipo_producto
-- ----------------------------
DROP TABLE IF EXISTS `cc_tipo_producto`;
CREATE TABLE `cc_tipo_producto`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `tp_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `tp_descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `tp_fecha_creacion` date NULL DEFAULT NULL,
  `tp_estado` tinyint(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_tipo_producto
-- ----------------------------
INSERT INTO `cc_tipo_producto` VALUES (1, 'COMPRA/VENTA', 'SE DEFINEN DE ESTE TIPO LOS PRODUCTOS CUYA REPOSICIÓN DEBE SER MÁS O MENOS AUTOMATIZADA, SEGÚN REGLAS DEFINIDAS EN EL SISTEMA. CREAN MOVIMIENTOS DE ALMACÉN Y SE CONTROLA SU STOCK.', '2024-06-11', 1);
INSERT INTO `cc_tipo_producto` VALUES (2, 'ACTIVO/FIJO', 'SE PUEDEN RECIBIR, DISTRIBUIR O FABRICAR. SIN EMBARGO, SU NIVEL DE EXISTENCIAS NO ESTÁ GESTIONADO POR EL SISTEMA. EN EL ERP SE DA POR SUPUESTO QUE HAY UN NIVEL SUFICIENTE DE EXISTENCIAS EN TODO MOMENTO. NO GENERAN MOVIMIENTOS DE ENTRADA Y SALIDA DE ALMACÉ', '2024-04-16', 1);
INSERT INTO `cc_tipo_producto` VALUES (3, 'SERVICIOS', 'SIN CONTROL DE STOCK. NO APARECEN EN LAS DIVERSAS OPERACIONES DE ALMACÉN. POR EJEMPLO, UN SERVICIO DE CONSULTORÍA. PUEDEN GENERAR COMPRAS (SI ES UN SERVICIO QUE SE COMPRA: SUBCONTRATACIÓN) O PUEDE GENERAR TAREAS (SI ES UN SERVICIO QUE SE PRODUCE).', '2024-04-16', 1);
INSERT INTO `cc_tipo_producto` VALUES (4, 'PRODUCCION', 'SIN CONTROL DE STOCK. SE UTILIZA CUANDO SE CREA UN SUPERPRODUCTO QUE TIENE ASOCIADO VARIOS SUBPRODUCTOS Y SE UTILIZA ESPECÍFICAMENTE EN UN AJUSTE DE PRODUCCIÓN.', '2024-06-11', 1);
INSERT INTO `cc_tipo_producto` VALUES (5, 'TP_DEMO', 'DEMO', '2024-06-11', 0);

-- ----------------------------
-- Table structure for cc_tipo_sujetos
-- ----------------------------
DROP TABLE IF EXISTS `cc_tipo_sujetos`;
CREATE TABLE `cc_tipo_sujetos`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `tps_descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tps_estado` tinyint(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_tipo_sujetos
-- ----------------------------
INSERT INTO `cc_tipo_sujetos` VALUES (1, 'NATURAL', 1);
INSERT INTO `cc_tipo_sujetos` VALUES (2, 'JURIDICA PRIVADA', 1);
INSERT INTO `cc_tipo_sujetos` VALUES (3, 'JURIDICA PUBLICA', 1);
INSERT INTO `cc_tipo_sujetos` VALUES (4, 'EXTRANGERO', 1);

-- ----------------------------
-- Table structure for cc_tipo_venta
-- ----------------------------
DROP TABLE IF EXISTS `cc_tipo_venta`;
CREATE TABLE `cc_tipo_venta`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `tv_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tv_descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `tv_codigo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tv_estado` tinyint(1) NOT NULL DEFAULT 1,
  `tv_created_at` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `tv_updated_at` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_tipo_venta
-- ----------------------------
INSERT INTO `cc_tipo_venta` VALUES (1, 'VENTAS LOCALES (EXCLUYE ACTIVOS FIJOS) GRAVADAS TARIFA DIFERENTE DE CERO', NULL, '401', 1, '2026-08-10 11:00:36', '2026-08-10 11:00:36');
INSERT INTO `cc_tipo_venta` VALUES (2, 'VENTAS DE ACTIVOS FIJOS GRAVADAS TARIFA DIFERENTE DE CERO', NULL, '402', 1, '2026-08-10 11:00:36', '2026-08-10 11:00:36');
INSERT INTO `cc_tipo_venta` VALUES (3, 'VENTAS LOCALES (EXCLUYE ACTIVOS FIJOS) GRAVADAS TARIFA 0% QUE NO DAN DERECHO A CREDITO TRIBUTARIO', NULL, '403', 1, '2026-08-10 11:00:36', '2026-08-10 11:00:36');
INSERT INTO `cc_tipo_venta` VALUES (4, 'VENTAS DE ACTIVOS FIJOS GRAVADAS TARIFA 0% QUE NO DAN DERECHO A CREDITO TRIBUTARIO', NULL, '404', 1, '2026-08-10 11:00:36', '2026-08-10 11:00:36');
INSERT INTO `cc_tipo_venta` VALUES (5, 'VENTAS LOCALES (EXCLUYE ACTIVOS FIJOS) GRAVADAS TARIFA 0% QUE DAN DERECHO A CREDITO TRIBUTARIO', NULL, '405', 1, '2026-08-10 11:00:36', '2026-08-10 11:00:36');
INSERT INTO `cc_tipo_venta` VALUES (6, 'VENTAS DE ACTIVOS FIJOS GRAVADAS TARIFA 0% QUE DAN DERECHO A CREDITO TRIBUTARIO', NULL, '406', 1, '2026-08-10 11:00:36', '2026-08-10 11:00:36');
INSERT INTO `cc_tipo_venta` VALUES (7, 'EXPORTACIONES DE BIENES', NULL, '407', 1, '2026-08-10 11:00:36', '2026-08-10 11:00:36');
INSERT INTO `cc_tipo_venta` VALUES (8, 'EXPORTACIONES DE SERVICIOS', NULL, '408', 1, '2026-08-10 11:00:36', '2026-08-10 11:00:36');
INSERT INTO `cc_tipo_venta` VALUES (9, 'TRANSFERENCIAS NO OBJETO O EXENTAS DE IVA', NULL, '431', 1, '2026-08-10 11:00:36', '2026-08-10 11:00:36');
INSERT INTO `cc_tipo_venta` VALUES (10, 'INGRESOS POR REEMBOLSO COMO INTERMEDIARIO / VALORES FACTURADOS POR OPERADOR DE TRANSPORTE', NULL, '434', 1, '2026-08-10 11:00:36', '2026-08-10 11:00:36');

-- ----------------------------
-- Table structure for cc_tipos_comprobante
-- ----------------------------
DROP TABLE IF EXISTS `cc_tipos_comprobante`;
CREATE TABLE `cc_tipos_comprobante`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `comp_codigo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `comp_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `comp_estado` tinyint(0) NULL DEFAULT NULL,
  `comp_sustento_tributario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `comp_codigo`(`comp_codigo`) USING BTREE,
  INDEX `id`(`id`, `comp_codigo`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 24 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_tipos_comprobante
-- ----------------------------
INSERT INTO `cc_tipos_comprobante` VALUES (1, '01', 'FACTURA', 1, '01, 02, 03, 04, 05, 06, 07, 08, 09, 00');
INSERT INTO `cc_tipos_comprobante` VALUES (2, '02', 'NOTA O BOLETA DE VENTA', 1, '02, 04, 05, 07, 08, 09, 00');
INSERT INTO `cc_tipos_comprobante` VALUES (3, '03', 'LIQUIDACION DE COMPRA DE BIENES O PRESTACIÓN DE SERVICIOS', 1, '01, 02, 03, 04, 05, 06, 07, 08');
INSERT INTO `cc_tipos_comprobante` VALUES (4, '04', 'NOTA DE CREDITO', 1, '01, 02, 03, 04, 05, 06, 07, 08, 09, 00');
INSERT INTO `cc_tipos_comprobante` VALUES (5, '05', 'NOTA DE DEBITO', 1, '01, 02, 03, 04, 05, 06, 07, 08, 09, 00');
INSERT INTO `cc_tipos_comprobante` VALUES (6, '06', 'GUIAS DE REMISION', 1, '');
INSERT INTO `cc_tipos_comprobante` VALUES (7, '07', 'COMPROBANTE DE RETENCION', 1, '');
INSERT INTO `cc_tipos_comprobante` VALUES (8, '08', 'TIQUETES O VALES EMITIDOS POR MAQUINAS REGISTRADORAS', 1, '');
INSERT INTO `cc_tipos_comprobante` VALUES (9, '12', 'DOCUMENTOS EMITIDOS POR INSTITUCIONES FINANCIERAS', 1, '01, 02, 05');
INSERT INTO `cc_tipos_comprobante` VALUES (10, '15', 'COMPROBANTE DE VENTA EMITIDO EN EL EXTERIOR', 1, '02, 04, 05, 07');
INSERT INTO `cc_tipos_comprobante` VALUES (11, '16', 'FORMULARIO UNICO DE EXPORTACION (FUE) O DECLARACION ADUANERA UNICA (DAU) O DE', 1, '');
INSERT INTO `cc_tipos_comprobante` VALUES (12, '18', 'DOCUMENTOS AUTORIZADOS UTILIZADOS EN VENTAS EXCEPTO N/C N/D', 1, '');
INSERT INTO `cc_tipos_comprobante` VALUES (13, '19', 'COMPROBANTES DE PAGO DE CUOTAS O APORTES', 1, '02, 10');
INSERT INTO `cc_tipos_comprobante` VALUES (14, '20', 'DOCUMENTOS POR SERVICIOS ADMINISTRATIVOS EMITIDOS POR INST. DEL ESTADO', 1, '02');
INSERT INTO `cc_tipos_comprobante` VALUES (15, '22', 'RECAP', 1, '');
INSERT INTO `cc_tipos_comprobante` VALUES (16, '41', 'COMPROBANTE DE VENTA EMITIDO POR REEMBOLSO', 1, '01, 02, 03, 04, 05, 06, 07');
INSERT INTO `cc_tipos_comprobante` VALUES (17, '42', 'DOCUMENTO AGENTE DE RETENCIÓN PRESUNTIVA', 1, '00');
INSERT INTO `cc_tipos_comprobante` VALUES (18, '43', 'LIQUIDACION PARA EXPLOTACION Y EXPLORACION DE HIDROCARBUROS', 1, '01, 02, 06, 07');
INSERT INTO `cc_tipos_comprobante` VALUES (19, '44', 'COMPROBANTE DE CONTRIBUCIONES Y APORTES', 1, '');
INSERT INTO `cc_tipos_comprobante` VALUES (20, '45', 'LIQUIDACION POR RECLAMOS DE ASEGURADORAS', 1, '09');
INSERT INTO `cc_tipos_comprobante` VALUES (21, '47', 'NOTA DE CREDITO POR REEMBOLSO EMITIDA POR INTERMEDIARIO', 1, '01, 02, 03, 04, 06, 07');
INSERT INTO `cc_tipos_comprobante` VALUES (22, '48', 'NOTA DE DEBITO POR REEMBOLSO EMITIDA POR INTERMEDIARIO', 1, '01, 02, 03, 04, 06, 07');
INSERT INTO `cc_tipos_comprobante` VALUES (23, '49', 'PROVEEDOR DIRECTO DE EXPORTADOR BAJO RÉGIMEN ESPECIAL', 1, '');

-- ----------------------------
-- Table structure for cc_transacciones
-- ----------------------------
DROP TABLE IF EXISTS `cc_transacciones`;
CREATE TABLE `cc_transacciones`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `tr_codigo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tr_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tr_descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `unique_cod`(`tr_codigo`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 47 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_transacciones
-- ----------------------------
INSERT INTO `cc_transacciones` VALUES (1, '01', 'VENTA', 'SIRVE PARA REGISTRAR LOS PROCESOS DE VENTAS');
INSERT INTO `cc_transacciones` VALUES (2, '02', 'COMPRA', 'SIRVE PARA REGISTRAR LOS PROCESOS DE COMPRAS');
INSERT INTO `cc_transacciones` VALUES (3, '03', 'RECIBO ANTICIPO', 'CUANDO SE RECIBE UN ANTICIPO DEL CLIENTE');
INSERT INTO `cc_transacciones` VALUES (4, '04', 'CONCILIACION ANTICIPO X TRANSFERENCIA', '');
INSERT INTO `cc_transacciones` VALUES (5, '05', 'COBRO CXC', 'POR COBRO DE LAS CUENTAS X COBRAR');
INSERT INTO `cc_transacciones` VALUES (6, '06', 'CONCILIACION CHEQUES CUSTODIO', 'CUANDO SE REALIZA LA CONCILIACION DE LOS CHEQUES EN CUSTODIO');
INSERT INTO `cc_transacciones` VALUES (7, '07', 'CONCILIACION ANTICIPOS CON TARJ CREDITO', 'CONCILIACION DE TARJETAS DE CREDITO POR RECIBO DE ANTICIPOS');
INSERT INTO `cc_transacciones` VALUES (8, '08', 'ANULACION FACTURA VENTA', 'CUANDO SE ANULA UNA FACTURA DE VENTA');
INSERT INTO `cc_transacciones` VALUES (9, '09', 'ANULACION FACTURA COMPRA', 'CUANDO SE ANULA UNA FACTURA DE COMPRA');
INSERT INTO `cc_transacciones` VALUES (10, '10', 'PREFACTURA SERVICIO TECNICO', 'CUANDO SE DEJA FACTURAS EN PENDIENTE DESDE SERVICIO TECNICO');
INSERT INTO `cc_transacciones` VALUES (11, '11', 'NOTAS CREDITO COMPRA', '');
INSERT INTO `cc_transacciones` VALUES (12, '12', 'RETENCION COMPRA', '');
INSERT INTO `cc_transacciones` VALUES (13, '13', 'ANTICIPO A PROVEEDOR', '');
INSERT INTO `cc_transacciones` VALUES (14, '14', 'DEPOSITO', '');
INSERT INTO `cc_transacciones` VALUES (15, '15', 'CUENTA BANCARIA', 'CREACION DE NUEVA CUENTA BANCARIA');
INSERT INTO `cc_transacciones` VALUES (16, '16', 'ANTICIPO CLIENTE', '');
INSERT INTO `cc_transacciones` VALUES (17, '17', 'TRANSFERENCIA BODEGAS', '');
INSERT INTO `cc_transacciones` VALUES (18, '18', 'RETENCION VENTA', '');
INSERT INTO `cc_transacciones` VALUES (19, '19', 'PAGO  CXP', 'CUANDO ES UN PAGO DE CUENTAS');
INSERT INTO `cc_transacciones` VALUES (20, '20', 'NOTA CREDITO VENTA', '');
INSERT INTO `cc_transacciones` VALUES (21, '21', 'COMPROBANTE DE PAGO', '');
INSERT INTO `cc_transacciones` VALUES (22, '22', 'COSTO VENTA', 'REGISTRA EL ASIENTO CONTABLE DEL COSTO DE VENTA');
INSERT INTO `cc_transacciones` VALUES (23, '23', 'NUEVO COMPROBANTE INGRESO', 'CUANDO SE CREA UN NUEVO COMPROBANTE DE INGRESO SIN NINGUNA OTRA TRANSACCION LIGADA');
INSERT INTO `cc_transacciones` VALUES (24, '24', 'NUEVO COMPROBANTE EGRESO', 'CUANDO SE CREA UN NUEVO COMPROBANTE DE EGRESO SIN NINGUNA OTRA TRANSACCION LIGADA');
INSERT INTO `cc_transacciones` VALUES (25, '25', 'REGISTRO CHEQUES GIRADOS', 'LOS CHEQUES GIRADOS ACTUALIZAN LA CUENTA BANCARIA');
INSERT INTO `cc_transacciones` VALUES (26, '26', 'ANULACION COBRO CXC', 'CUANDO SE ANULA EL COBRO DE UNA CXC');
INSERT INTO `cc_transacciones` VALUES (27, '27', 'DEPOSITOS REALIZADOS', 'DESDE MODULO BANCOS');
INSERT INTO `cc_transacciones` VALUES (28, '28', 'DEPOSITOS RECIBIDOS', '');
INSERT INTO `cc_transacciones` VALUES (29, '29', 'DEVOLUCIONES RETENCION VENTA', '');
INSERT INTO `cc_transacciones` VALUES (30, '30', 'PAGOS CXP CUOTAS', 'SE DEFINIÓ QUE ESTE COD VA A SER PARA REFERENCIA LSO PAGOS DE LAS CUOTAS DE CXP CON EL PRICESO QUE INVOLUCRA A LAS NUEVAS TABLAS');
INSERT INTO `cc_transacciones` VALUES (31, '31', 'ANULACION NOTA DE CREDITO', 'CUANDO SE ANULA UNA NOTA DE CRÉDITO.');
INSERT INTO `cc_transacciones` VALUES (32, '32', 'PAGO DE ROLES', 'PAGO DE ROLES DE EMPLEADOS');
INSERT INTO `cc_transacciones` VALUES (33, '33', 'ANTICIPO EMPLEADO', '');
INSERT INTO `cc_transacciones` VALUES (34, '34', 'CIERRE DEL PERIODO', 'SIRVE CUANDO SE CIERRAN LOS PERIODOS');
INSERT INTO `cc_transacciones` VALUES (35, '35', 'APERTURA DEL PERIODO', '');
INSERT INTO `cc_transacciones` VALUES (36, '36', 'ANULACION NC COMPRA', '');
INSERT INTO `cc_transacciones` VALUES (37, '37', 'ANULACION NC VENTA', '');
INSERT INTO `cc_transacciones` VALUES (38, '38', 'AJUSTE INVENTARIO SALIDA', '');
INSERT INTO `cc_transacciones` VALUES (39, '39', 'AJUSTE INVENTARIO ENTRADA', '');
INSERT INTO `cc_transacciones` VALUES (40, '40', 'ANULACIÓN AJUSTE SALIDA', 'NOS SIRVE PARA LA ANULACIÓN DE LOS AJUSTES DE SALIDA');
INSERT INTO `cc_transacciones` VALUES (41, '41', 'ANULACIÓN AJUSTE ENTRADA', 'NOS SIRVE PARA IDENTIFICAR LAS ANULACIONES DE AJUSTES DE ENTRADA');
INSERT INTO `cc_transacciones` VALUES (42, '42', 'DESPACHO DE INVENTARIO', 'TODOS LAS SALIDAS QUE NO SEAN AJUSTES');
INSERT INTO `cc_transacciones` VALUES (43, '43', 'ANULACIÓN DE DESPACHO', 'ANULACIONES DE LOS DESPACHOS POR X ERRORES');
INSERT INTO `cc_transacciones` VALUES (44, '44', 'ANULACION TRANSFERENCIA DE PRODUCTOS', 'CÓDIGO PARA LA ANULACIÓN DE TRANSFERENCIA EN PRODUCTOS DE BODGA');
INSERT INTO `cc_transacciones` VALUES (46, '0001', 'TRANS_TEST', 'TRANSACCION DE TESTEO 2');

-- ----------------------------
-- Table structure for cc_transferencia_bodega
-- ----------------------------
DROP TABLE IF EXISTS `cc_transferencia_bodega`;
CREATE TABLE `cc_transferencia_bodega`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa de la transferencia',
  `trb_secuencial` int(0) NOT NULL,
  `fk_bodega_origen` int(0) NOT NULL,
  `fk_bodega_destino` int(0) NOT NULL,
  `fk_centro_costo` int(0) NULL DEFAULT NULL,
  `trb_estado` tinyint(0) NOT NULL COMMENT '\r\n	1 = BORRADOR \r\n	2 = POR CONFIRMAR \r\n	3 = CONFIRMADA  \r\n	-1 = ANULADA\r\n0 = RECHAZADA\r\n\r\n	',
  `trb_observaciones` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `trb_motivo_anulacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `trb_total_items` int(0) NULL DEFAULT NULL,
  `trb_totaliva` decimal(15, 4) NULL DEFAULT 0.0000,
  `trb_totalcartiva` decimal(15, 4) NULL DEFAULT 0.0000,
  `trb_total` decimal(15, 4) NULL DEFAULT NULL,
  `trb_fecha` date NOT NULL,
  `trb_fecha_confirmacion` datetime(0) NULL DEFAULT NULL,
  `trb_fecha_anulacion` datetime(0) NULL DEFAULT NULL,
  `fk_user_crea` int(0) NOT NULL,
  `fk_user_confirma` int(0) NULL DEFAULT NULL,
  `fk_user_anula` int(0) NULL DEFAULT NULL,
  `trb_items_duplicados` enum('true','false') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `trb_fecha_creacion` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `trb_fecha_actualizacion` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_bodega_origen`(`fk_bodega_origen`) USING BTREE,
  INDEX `idx_bodega_destino`(`fk_bodega_destino`) USING BTREE,
  INDEX `idx_estado`(`trb_estado`) USING BTREE,
  INDEX `idx_centro_costos`(`fk_centro_costo`) USING BTREE,
  INDEX `fk_user_crea`(`fk_user_crea`) USING BTREE,
  INDEX `fk_user_confirma`(`fk_user_confirma`) USING BTREE,
  INDEX `fk_user_anula`(`fk_user_anula`) USING BTREE,
  INDEX `idx_trb_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `cc_transferencia_bodega_ibfk_1` FOREIGN KEY (`fk_bodega_origen`) REFERENCES `cc_bodegas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_transferencia_bodega_ibfk_2` FOREIGN KEY (`fk_bodega_destino`) REFERENCES `cc_bodegas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_transferencia_bodega_ibfk_3` FOREIGN KEY (`fk_centro_costo`) REFERENCES `cc_centroscosto` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_transferencia_bodega_ibfk_4` FOREIGN KEY (`fk_user_crea`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_transferencia_bodega_ibfk_5` FOREIGN KEY (`fk_user_confirma`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_transferencia_bodega_ibfk_6` FOREIGN KEY (`fk_user_anula`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_trb_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_transferencia_bodega
-- ----------------------------

-- ----------------------------
-- Table structure for cc_transferencia_bodega_det
-- ----------------------------
DROP TABLE IF EXISTS `cc_transferencia_bodega_det`;
CREATE TABLE `cc_transferencia_bodega_det`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `fk_transferencia_bodega` int(0) NOT NULL,
  `fk_producto` int(0) NOT NULL,
  `fk_lote` int(0) NULL DEFAULT NULL,
  `trbd_itemcantidad` decimal(15, 3) NOT NULL,
  `trbd_itemcosto` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `trbd_itemcostoxcantidad` decimal(15, 4) NOT NULL DEFAULT 0.0000 COMMENT 'cantidad * precio',
  `trbd_estado` tinyint(0) NOT NULL DEFAULT 1 COMMENT '\r\n	1 = ACTIVO\r\n	0 = ELIMINADO\r\n	',
  `trbd_observaciones` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_transfer`(`fk_transferencia_bodega`) USING BTREE,
  INDEX `idx_producto`(`fk_producto`) USING BTREE,
  INDEX `idx_lote`(`fk_lote`) USING BTREE,
  CONSTRAINT `cc_transferencia_bodega_det_ibfk_1` FOREIGN KEY (`fk_producto`) REFERENCES `cc_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cc_transferencia_bodega_det_ibfk_2` FOREIGN KEY (`fk_lote`) REFERENCES `cc_lotes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_trb_det_cab` FOREIGN KEY (`fk_transferencia_bodega`) REFERENCES `cc_transferencia_bodega` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_transferencia_bodega_det
-- ----------------------------

-- ----------------------------
-- Table structure for cc_unidades_medida
-- ----------------------------
DROP TABLE IF EXISTS `cc_unidades_medida`;
CREATE TABLE `cc_unidades_medida`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `um_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `um_estado` tinyint(0) NULL DEFAULT NULL,
  `um_fecha_creacion` date NULL DEFAULT NULL,
  `um_nombre_corto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `um_fecha_actualizacion` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 53 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_unidades_medida
-- ----------------------------
INSERT INTO `cc_unidades_medida` VALUES (1, 'KILOGRAMO', 1, '2024-04-16', 'KG', '2025-11-10 19:21:46');
INSERT INTO `cc_unidades_medida` VALUES (2, 'GRAMO', 1, '2024-04-17', 'GR', '2025-11-10 19:21:46');
INSERT INTO `cc_unidades_medida` VALUES (3, 'LIBRA', 1, '2024-04-17', 'LB', '2025-11-10 19:21:46');
INSERT INTO `cc_unidades_medida` VALUES (4, 'LITROS', 1, '2024-04-17', 'LT', '2025-11-10 19:21:46');
INSERT INTO `cc_unidades_medida` VALUES (5, 'MIILITROS', 1, '2024-04-17', 'ML', '2025-11-10 19:21:46');
INSERT INTO `cc_unidades_medida` VALUES (6, 'DISPLEY', 1, '2024-04-17', 'DISPLEY', '2025-11-10 19:21:46');
INSERT INTO `cc_unidades_medida` VALUES (7, 'CAJA', 1, '2024-04-17', 'CJ', '2025-11-10 19:21:46');
INSERT INTO `cc_unidades_medida` VALUES (8, 'QUINTAL', 1, '2024-06-11', 'QQ', '2025-11-10 19:21:46');
INSERT INTO `cc_unidades_medida` VALUES (9, 'UNIDAD', 1, '2024-07-09', 'UNI', '2025-11-10 19:21:46');
INSERT INTO `cc_unidades_medida` VALUES (51, 'GALON', 1, '2025-11-26', 'GAL', '2025-11-25 19:12:48');
INSERT INTO `cc_unidades_medida` VALUES (52, 'KG', 1, '2026-04-23', 'KG', '2026-04-23 16:57:23');

-- ----------------------------
-- Table structure for cc_ventas
-- ----------------------------
DROP TABLE IF EXISTS `cc_ventas`;
CREATE TABLE `cc_ventas`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'ID de la venta',
  `ven_secuencial` int(0) NULL DEFAULT NULL COMMENT 'Secuencial interno de venta por proyecto',
  `fk_cliente` int(0) NOT NULL COMMENT 'Cliente al que se emite la venta',
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa del sistema',
  `fk_punto_venta` int(0) NULL DEFAULT NULL COMMENT 'Punto de venta/emision utilizado',
  `ven_numero_establecimiento` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Establecimiento del comprobante',
  `ven_numero_emision` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Punto de emision del comprobante',
  `ven_numero_comprobante` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Numero secuencial del comprobante',
  `ven_autorizacion_sri` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Autorizacion SRI',
  `ven_fecha_vencimiento_autorizacion` date NULL DEFAULT NULL COMMENT 'Fecha vencimiento autorizacion',
  `ven_tipo_comprobante_cod` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'Codigo SRI del comprobante',
  `ven_fecha_emision` date NOT NULL COMMENT 'Fecha de emision',
  `ven_fecha_registro` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Fecha registro sistema',
  `fk_bodega` int(0) NULL DEFAULT NULL COMMENT 'Bodega desde donde sale inventario',
  `fk_centro_costo` int(0) NULL DEFAULT NULL COMMENT 'Centro de costo global',
  `fk_tipo_venta` int(0) NULL DEFAULT NULL COMMENT 'Tipo de venta SRI / formulario tributario',
  `ven_subtotal_bruto` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Subtotal antes de descuentos',
  `ven_descuento_items` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Descuento por items',
  `ven_descuento_global` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Descuento global',
  `ven_descuento_valor` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Descuento total',
  `ven_subtotal_neto` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Subtotal luego de descuentos',
  `ven_totaliva` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Total IVA',
  `ven_totalice` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Total ICE',
  `ven_totalirbpnr` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Total IRBPNR',
  `ven_recargo` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Recargos de venta',
  `ven_servicios_adicionales` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Servicios adicionales',
  `ven_total` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Total final venta',
  `ven_tarifacero_bruto` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Base tarifa 0 bruta',
  `ven_tarifacero_neto` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Base tarifa 0 neta',
  `ven_tarifaiva_bruto` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Base IVA bruta',
  `ven_tarifaiva_neto` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Base IVA neta',
  `ven_base_iva` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Base imponible IVA',
  `ven_total_excento_impuestos` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Base exenta IVA',
  `ven_total_no_objeto_impuestos` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Base no objeto IVA',
  `cod_forma_pago` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Forma de pago principal',
  `ven_tipo_pago` enum('CONTADO','CREDITO') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Tipo de pago',
  `ven_dias_credito` int(0) NULL DEFAULT NULL COMMENT 'Dias de credito',
  `ven_num_cuotas` int(0) NULL DEFAULT NULL COMMENT 'Numero de cuotas',
  `ven_items_duplicados` enum('true','false') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'false' COMMENT 'Permite items duplicados',
  `ven_estado` enum('BORRADOR','ARCHIVADO','ANULADA_EN_PENDIENTE','ANULADA_EN_ARCHIVADA') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Estado de la venta',
  `ven_observacion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT 'Observaciones generales',
  `fk_user` int(0) NULL DEFAULT NULL COMMENT 'Usuario registra',
  `ven_fecha_archivada` datetime(0) NULL DEFAULT NULL COMMENT 'Fecha en que se archivo',
  `ven_fecha_anulacion` datetime(0) NULL DEFAULT NULL COMMENT 'Fecha anulacion',
  `ven_motivo_anulacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Motivo anulacion',
  `fk_user_anulacion` int(0) NULL DEFAULT NULL COMMENT 'Usuario anulacion',
  `ven_autorizado_sri` tinyint(0) NULL DEFAULT NULL COMMENT '1 autorizado SRI, 0 no autorizado',
  `ven_mensaje_sri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Mensaje SRI',
  `ven_codigo_factura_electronica` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Codigo documento electronico',
  `ven_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `ven_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_venta_cliente`(`fk_cliente`) USING BTREE,
  INDEX `idx_venta_proyecto`(`fk_proyecto`) USING BTREE,
  INDEX `idx_venta_fecha`(`ven_fecha_emision`) USING BTREE,
  INDEX `idx_venta_estado`(`ven_estado`) USING BTREE,
  INDEX `fk_venta_bodega`(`fk_bodega`) USING BTREE,
  INDEX `fk_venta_centro`(`fk_centro_costo`) USING BTREE,
  INDEX `fk_venta_user`(`fk_user`) USING BTREE,
  INDEX `fk_venta_punto`(`fk_punto_venta`) USING BTREE,
  INDEX `idx_venta_tipo_venta`(`fk_tipo_venta`) USING BTREE,
  CONSTRAINT `fk_venta_bodega` FOREIGN KEY (`fk_bodega`) REFERENCES `cc_bodegas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_venta_centro` FOREIGN KEY (`fk_centro_costo`) REFERENCES `cc_centroscosto` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_venta_cliente` FOREIGN KEY (`fk_cliente`) REFERENCES `cc_clientes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_venta_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_venta_punto` FOREIGN KEY (`fk_punto_venta`) REFERENCES `cc_puntos_venta` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_venta_tipo_venta` FOREIGN KEY (`fk_tipo_venta`) REFERENCES `cc_tipo_venta` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_venta_user` FOREIGN KEY (`fk_user`) REFERENCES `cc_empleados` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_ventas
-- ----------------------------

-- ----------------------------
-- Table structure for cc_ventas_ats_formas_pago
-- ----------------------------
DROP TABLE IF EXISTS `cc_ventas_ats_formas_pago`;
CREATE TABLE `cc_ventas_ats_formas_pago`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'ID forma de pago ATS de venta',
  `fk_venta` int(0) NOT NULL COMMENT 'Venta relacionada',
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa',
  `fk_forma_pago_ats` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Codigo forma de pago ATS SRI',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uq_venta_forma_pago_ats`(`fk_venta`, `fk_forma_pago_ats`) USING BTREE,
  INDEX `idx_vats_venta`(`fk_venta`) USING BTREE,
  INDEX `idx_vats_proyecto`(`fk_proyecto`) USING BTREE,
  INDEX `idx_vats_forma_pago`(`fk_forma_pago_ats`) USING BTREE,
  CONSTRAINT `fk_vats_forma_pago` FOREIGN KEY (`fk_forma_pago_ats`) REFERENCES `cc_formas_pago_sri` (`codigo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_vats_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_vats_venta` FOREIGN KEY (`fk_venta`) REFERENCES `cc_ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'Formas de pago ATS asociadas a ventas' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_ventas_ats_formas_pago
-- ----------------------------

-- ----------------------------
-- Table structure for cc_ventas_bases_impuesto
-- ----------------------------
DROP TABLE IF EXISTS `cc_ventas_bases_impuesto`;
CREATE TABLE `cc_ventas_bases_impuesto`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'ID base impuesto venta',
  `fk_venta` int(0) NOT NULL COMMENT 'Venta relacionada',
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa',
  `fk_impuesto_tarifa` int(0) NULL DEFAULT NULL COMMENT 'Tarifa de impuesto aplicada',
  `imp_codigo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Codigo SRI impuesto',
  `imp_detalle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Detalle tarifa',
  `imp_porcentaje` decimal(10, 4) NOT NULL DEFAULT 0.0000 COMMENT 'Porcentaje impuesto',
  `imp_subtotal_bruto` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Subtotal bruto base',
  `imp_subtotal_neto` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Base neta',
  `imp_valor` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Valor impuesto',
  `imp_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_vbi_venta`(`fk_venta`) USING BTREE,
  INDEX `idx_vbi_proyecto`(`fk_proyecto`) USING BTREE,
  CONSTRAINT `fk_vbi_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_vbi_venta` FOREIGN KEY (`fk_venta`) REFERENCES `cc_ventas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_ventas_bases_impuesto
-- ----------------------------

-- ----------------------------
-- Table structure for cc_ventas_det
-- ----------------------------
DROP TABLE IF EXISTS `cc_ventas_det`;
CREATE TABLE `cc_ventas_det`  (
  `id` int(0) NOT NULL AUTO_INCREMENT COMMENT 'ID detalle venta',
  `fk_venta` int(0) NOT NULL COMMENT 'Venta cabecera',
  `fk_proyecto` int(0) NOT NULL DEFAULT 1 COMMENT 'Proyecto/subempresa',
  `fk_producto` int(0) NOT NULL COMMENT 'Producto vendido',
  `fk_bodega` int(0) NULL DEFAULT NULL COMMENT 'Bodega de salida',
  `fk_lote` int(0) NULL DEFAULT NULL COMMENT 'Lote vendido si aplica',
  `vend_cantidad` decimal(15, 6) NOT NULL COMMENT 'Cantidad vendida',
  `vend_precio_bruto` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Precio antes descuento',
  `vend_descuento_valor` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Descuento valor',
  `vend_descuento_porcentaje` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Descuento porcentaje',
  `vend_precio_neto` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Precio neto unitario',
  `vend_total_neto` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Subtotal neto',
  `fk_impuesto_tarifa` int(0) NULL DEFAULT NULL COMMENT 'Tarifa IVA aplicada',
  `vend_impt_codigo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Codigo tarifa impuesto',
  `vend_impt_porcentaje` decimal(10, 4) NOT NULL DEFAULT 0.0000 COMMENT 'Porcentaje IVA',
  `vend_iva_valor` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'IVA unitario',
  `vend_total_iva_valor` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'IVA total',
  `vend_total` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Total item',
  `vend_costo_unitario` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Costo promedio/ultimo usado',
  `vend_costo_total` decimal(15, 6) NOT NULL DEFAULT 0.000000 COMMENT 'Costo total de venta',
  `vend_cta_venta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Cuenta contable ingreso',
  `vend_cta_inventario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Cuenta inventario',
  `vend_cta_costo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Cuenta costo venta',
  `vend_centro_costo` int(0) NULL DEFAULT NULL COMMENT 'Centro de costo del item',
  `vend_lote` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'Codigo lote snapshot',
  `vend_fecha_elaboracion` date NULL DEFAULT NULL COMMENT 'Fecha elaboracion snapshot',
  `vend_fecha_caducidad` date NULL DEFAULT NULL COMMENT 'Fecha caducidad snapshot',
  `vend_estado` tinyint(0) NOT NULL DEFAULT 1 COMMENT '1 activo, 0 anulado',
  `vend_created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `vend_updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_vend_venta`(`fk_venta`) USING BTREE,
  INDEX `idx_vend_producto`(`fk_producto`) USING BTREE,
  INDEX `idx_vend_proyecto`(`fk_proyecto`) USING BTREE,
  INDEX `fk_vend_bodega`(`fk_bodega`) USING BTREE,
  INDEX `fk_vend_lote`(`fk_lote`) USING BTREE,
  CONSTRAINT `fk_vend_bodega` FOREIGN KEY (`fk_bodega`) REFERENCES `cc_bodegas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_vend_lote` FOREIGN KEY (`fk_lote`) REFERENCES `cc_lotes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_vend_producto` FOREIGN KEY (`fk_producto`) REFERENCES `cc_productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_vend_proyecto` FOREIGN KEY (`fk_proyecto`) REFERENCES `cc_proyectos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_vend_venta` FOREIGN KEY (`fk_venta`) REFERENCES `cc_ventas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cc_ventas_det
-- ----------------------------

-- ----------------------------
-- Table structure for session
-- ----------------------------
DROP TABLE IF EXISTS `session`;
CREATE TABLE `session`  (
  `id` varchar(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `timestamp` int(0) NOT NULL,
  `data` mediumblob NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of session
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
