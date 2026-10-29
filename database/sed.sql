-- =====================================================================
-- HUANTA CERCA - Datos de DEMOSTRACIÓN
-- Archivo: database/seed.sql
-- Todo lo marcado [DEMO] es ficticio y sirve solo para pruebas.
-- No incluye usuarios: el administrador se crea aparte, con contraseña cifrada.
-- =====================================================================

USE huanta_cerca;

INSERT INTO categorias (id, nombre, descripcion, estado) VALUES
  (1, 'Restaurantes y comida',  'Restaurantes, cevicherías y venta de alimentos', 1),
  (2, 'Salud y bienestar',      'Farmacias, consultorios y centros de salud',     1),
  (3, 'Educación',              'Academias, institutos y capacitación',           1),
  (4, 'Tecnología y reparaciones', 'Reparación de equipos y servicios técnicos',  1),
  (5, 'Impresión y papelería',  'Imprentas, fotocopias y útiles',                 1),
  (6, 'Hogar y construcción',   'Ferreterías y materiales',                       1),
  (7, '[DEMO] Categoría inactiva', 'Sirve para probar que una categoría inactiva oculta sus negocios', 0);

INSERT INTO negocios (categoria_id, nombre, descripcion, direccion, telefono, horario, estado) VALUES
  (1, '[DEMO] Restaurante Sabor Ejemplo',
      'Negocio ficticio de demostración. Menú del día y platos a la carta.',
      'Dirección de ejemplo 101', '900000001', 'Lun a Dom: 8:00 a 20:00', 1),
  (1, '[DEMO] Cevichería Ejemplo',
      'Negocio ficticio de demostración. Pescados y mariscos.',
      'Dirección de ejemplo 102', '900000002', 'Mar a Dom: 10:00 a 17:00', 1),
  (2, '[DEMO] Farmacia Ejemplo',
      'Negocio ficticio de demostración. Medicamentos y productos de cuidado personal.',
      'Dirección de ejemplo 201', '900000003', 'Lun a Sáb: 8:00 a 21:00', 1),
  (2, '[DEMO] Consultorio Ejemplo (sin teléfono)',
      'Negocio ficticio sin teléfono: prueba que el botón de WhatsApp no aparece.',
      'Dirección de ejemplo 202', NULL, 'Lun a Vie: 9:00 a 13:00', 1),
  (3, '[DEMO] Academia Ejemplo',
      'Negocio ficticio de demostración. Reforzamiento y cursos cortos.',
      'Dirección de ejemplo 301', '900000004', 'Lun a Vie: 15:00 a 20:00', 1),
  (4, '[DEMO] Reparación de Celulares Ejemplo',
      'Negocio ficticio de demostración. Cambio de pantallas y baterías.',
      'Dirección de ejemplo 401', '900000005', 'Lun a Sáb: 9:00 a 19:00', 1),
  (5, '[DEMO] Imprenta Ejemplo',
      'Negocio ficticio de demostración. Tarjetas, banners y fotocopias.',
      'Dirección de ejemplo 501', '900000006', 'Lun a Sáb: 8:30 a 18:30', 1),
  (6, '[DEMO] Ferretería Ejemplo (inactiva)',
      'Negocio ficticio INACTIVO: prueba que un negocio desactivado no se muestra al público.',
      'Dirección de ejemplo 601', '900000007', 'Lun a Sáb: 8:00 a 18:00', 0),
  (7, '[DEMO] Negocio en categoría inactiva',
      'Negocio activo, pero su categoría está inactiva: no debe mostrarse al público.',
      'Dirección de ejemplo 701', '900000008', 'Lun a Vie: 9:00 a 17:00', 1);