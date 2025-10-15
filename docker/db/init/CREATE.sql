-- =========================================
-- USERS
-- =========================================
CREATE TABLE users (
  id                 SERIAL PRIMARY KEY,
  username           TEXT NOT NULL UNIQUE,
  password	     TEXT NOT NULL,
  active             BOOLEAN NOT NULL DEFAULT true,
  role               TEXT NOT NULL,              -- admin, operator, viewer, etc.
  is_superuser       BOOLEAN NOT NULL DEFAULT false,


  first_name         TEXT NOT NULL,
  last_name          TEXT NOT NULL,
  document_type      TEXT,                       -- DNI, CI, PASSPORT, etc.
  document_number    TEXT,
  CONSTRAINT uq_users_document UNIQUE (document_type, document_number),

  address_line1      TEXT NOT NULL,
  address_line2      TEXT NOT NULL,
  city               TEXT NOT NULL,
  state_province     TEXT NOT NULL,
  postal_code        TEXT NOT NULL,
  country            TEXT NOT NULL,
  email              TEXT NOT NULL,
  phone              TEXT NOT NULL,

  organization       TEXT,                       -- universidad, Facultad, empresa.... etc.
  department         TEXT,                       -- cátedra / área / laboratorio
  position_title     TEXT,                       -- cargo
  
  created_at         TIMESTAMPTZ NOT NULL DEFAULT now(),
  created_by_user_id INTEGER REFERENCES users(id),
  updated_at         TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_by_user_id INTEGER REFERENCES users(id),
  last_login_at      TIMESTAMPTZ,

  CONSTRAINT ck_users_email_has_at CHECK (position('@' in email) > 1)
);

CREATE INDEX idx_users_active        ON users (active);


-- =========================================
-- units (catálogo maestro de unidades según estándar UCUM https://ucum.org/ )
-- =========================================
CREATE TABLE units (
  id             SERIAL PRIMARY KEY,          -- numeric ID for FK references
  ucum_code      TEXT UNIQUE NOT NULL,        -- UCUM code (e.g. 'Cel', 'uS/cm', 'Hz', '1')
  uncefact_code  TEXT,                        -- optional UN/CEFACT code
  display        TEXT NOT NULL                -- human-friendly label (°C, µS/cm, etc.)
);



-- =========================================
-- magnitudes (catálogo maestro de magnitudes + unidad + códigos...)
-- =========================================
CREATE TABLE magnitudes (
  id               SERIAL PRIMARY KEY,
  group_name       TEXT NOT NULL,             -- Water / Weather / Radio / Electricity / Motion
  name_en          TEXT NOT NULL,             -- e.g. "Electrical Conductivity"
  abbreviation     TEXT NOT NULL,             -- e.g. "EC"
  unit_id          INT NOT NULL REFERENCES units(id), -- preferred UCUM unit
  wqx_code         TEXT,
  wmo_code         TEXT,
  iso_ieee_code    TEXT,
  decimals         SMALLINT CHECK (decimals BETWEEN 0 AND 6),
  allow_negative   BOOLEAN NOT NULL DEFAULT false,
  min_value        DOUBLE PRECISION,
  max_value        DOUBLE PRECISION,
  UNIQUE (name_en, unit_id)
);

-- =========================================
-- SENSORS (definición de cada equipo de medicion)
-- =========================================
CREATE TABLE sensors (
  id              SERIAL PRIMARY KEY,
  name            TEXT NOT NULL,                    -- alias del sensor
  manufacturer    TEXT,
  model           TEXT,
  serial_number   TEXT,
  sensor_type     TEXT,                             -- p.ej. multiparameter, thermometer, anemometer...
  installed_at    TIMESTAMPTZ,
  active          BOOLEAN NOT NULL DEFAULT true,
  notes           TEXT,
  UNIQUE (manufacturer, model, serial_number)
);

-- =========================================
-- SENSORS x MAGNITUDES (capacidades y límites por canal)
-- relación N:M entre sensors y magnitudes
-- =========================================
CREATE TABLE sensor_magnitudes (
  id              SERIAL PRIMARY KEY,
  sensor_id       INT NOT NULL REFERENCES sensors(id) ON DELETE CASCADE,
  magnitude_id    INT NOT NULL REFERENCES magnitudes(id) ON DELETE CASCADE,
  value_min       DOUBLE PRECISION,                 -- rango operativo del sensor para esa magnitud
  value_max       DOUBLE PRECISION,
  resolution      DOUBLE PRECISION,                 -- resolución nominal
  accuracy        TEXT,                             -- ej: "±0.1 °C", "±2% of reading"
  calibrated_at   DATE,                             -- última calibración
  channel_name    TEXT,                             -- opcional: "CH1", "pH", etc.
  notes           TEXT,
  CHECK (value_min IS NULL OR value_max IS NULL OR value_min < value_max)
);

-- Índices útiles
CREATE INDEX idx_sensor_magnitudes_sensor ON sensor_magnitudes (sensor_id);
CREATE INDEX idx_sensor_magnitudes_magnitude ON sensor_magnitudes (magnitude_id);

-- =========================================
-- LOCATIONS
-- =========================================
CREATE TABLE locations (
  id              SERIAL PRIMARY KEY,
  name            TEXT NOT NULL UNIQUE,
  description     TEXT,
  lat_dd          DOUBLE PRECISION,        -- latitude (decimal degrees)
  lon_dd          DOUBLE PRECISION,        -- longitude (decimal degrees)
  altitude_m      DOUBLE PRECISION,        -- meters
  address         TEXT,
  active          BOOLEAN NOT NULL DEFAULT true,
  created_at      TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- =========================================
-- LOCATION x MAGNITUDES
-- =========================================
CREATE TABLE location_magnitudes (
  id              SERIAL PRIMARY KEY,
  location_id     INT NOT NULL REFERENCES locations(id) ON DELETE CASCADE,
  magnitude_id    INT NOT NULL REFERENCES magnitudes(id) ON DELETE CASCADE,
  min_acceptable  DOUBLE PRECISION,             -- soft alarms for this site
  max_acceptable  DOUBLE PRECISION,
  alert_low       DOUBLE PRECISION,             -- Alerts for this site
  alert_high      DOUBLE PRECISION,
  sampling_plan   TEXT,                         -- e.g., "hourly", "daily at 08:00", "event-based"
  required        BOOLEAN NOT NULL DEFAULT true,
  notes           TEXT,
  CHECK (min_acceptable IS NULL OR max_acceptable IS NULL OR min_acceptable < max_acceptable)
);


-- =========================================
-- measurements -> MEDICIONES: un registro por toma de muestra
-- =========================================
CREATE TABLE measurements (
  id                SERIAL PRIMARY KEY,
  sensor_id         INT REFERENCES sensors(id),         -- sensor que originó la lectura
  location_id       INT REFERENCES locations(id) NOT NULL,
  entered_by_id        INT REFERENCES users(id),           -- quién cargó el registro
  sampled_by_id        INT REFERENCES users(id),           -- quién tomó la muestra
  registered_at     TIMESTAMPTZ NOT NULL DEFAULT now(), -- timestamp de carga/ingesta
  sampled_at        TIMESTAMPTZ NOT NULL,               -- fecha/hora de la toma
  status            TEXT NOT NULL DEFAULT 'received',
  source            TEXT NOT NULL DEFAULT 'manual',     -- 'manual','device','import','api'
  batch_id          TEXT,                               -- para agrupar cargas
  comments          TEXT
);

CREATE INDEX idx_measurements_sampled_at ON measurements(sampled_at);
CREATE INDEX idx_measurements_location   ON measurements(location_id);
CREATE INDEX idx_measurements_sensor     ON measurements(sensor_id);
CREATE INDEX idx_measurements_status     ON measurements(status);

-- =========================================
-- measured_values -> VALORES MEDIDOS: 1..N por medición
-- =========================================
CREATE TABLE measured_values (
  id                        SERIAL PRIMARY KEY,
  measurement_id            INT NOT NULL REFERENCES measurements(id) ON DELETE CASCADE,
  magnitude_id              INT NOT NULL REFERENCES magnitudes(id),  -- qué magnitud
  value_numeric             DOUBLE PRECISION NOT NULL,                  -- valor medido
  qc_flag                   TEXT,                                    -- banderas QC (ej: 'suspect', 'outlier', etc.)
  status                    TEXT NOT NULL DEFAULT 'received',
  taken_at                  TIMESTAMPTZ,                             -- si cada valor tiene su propio timestamp
  comments                  TEXT,

  -- ====== Snapshot de configuración al momento de la toma ======
  snapshot_min_acceptable   DOUBLE PRECISION,
  snapshot_max_acceptable   DOUBLE PRECISION,
  snapshot_alert_low        DOUBLE PRECISION,
  snapshot_alert_high       DOUBLE PRECISION,
  snapshot_allow_negative   BOOLEAN
);

CREATE INDEX idx_measured_values_meas   ON measured_values(measurement_id);
CREATE INDEX idx_measured_values_mag    ON measured_values(magnitude_id);
CREATE INDEX idx_measured_values_taken  ON measured_values(taken_at);
CREATE INDEX idx_measured_values_status ON measured_values(status);

-- Evitar duplicados de magnitud por medición (una fila por magnitud)
CREATE UNIQUE INDEX uq_measured_values_meas_mag
  ON measured_values(measurement_id, magnitude_id);


-- =========================================
-- USERS_LOCATIONS_ROLES
-- =========================================
CREATE TABLE user_location_roles (
  id               SERIAL PRIMARY KEY,
  user_id          INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  location_id      INT REFERENCES locations(id) ON DELETE CASCADE,  -- NULL = todas las ubicaciones
  
  -- Flags por rol
  is_system_admin  BOOLEAN NOT NULL DEFAULT false,
  is_location_admin BOOLEAN NOT NULL DEFAULT false,
  is_data_entry    BOOLEAN NOT NULL DEFAULT false,
  is_viewer        BOOLEAN NOT NULL DEFAULT false,
  active           BOOLEAN NOT NULL DEFAULT true,
  assigned_at      TIMESTAMPTZ NOT NULL DEFAULT now(),
  assigned_by_user INT REFERENCES users(id),
  notes            TEXT,

  -- Coherencia de alcance:
  -- - system_admin debe ser global (location_id NULL)
  -- - roles de ubicación NO pueden ser globales
  CONSTRAINT ck_scope_consistency
    CHECK (
      (is_system_admin = true AND location_id IS NULL)
      OR
      (is_system_admin = false AND location_id IS NOT NULL)
    )
);

-- Índices útiles
CREATE INDEX idx_ulr_user_active        ON user_location_roles (user_id) WHERE active;
CREATE INDEX idx_ulr_location_active    ON user_location_roles (location_id) WHERE active AND location_id IS NOT NULL;
CREATE INDEX idx_ulr_admins             ON user_location_roles (is_system_admin, is_location_admin) WHERE active;



-- =========================================
-- location_sensors -> Sensors x Location
-- =========================================
CREATE TABLE location_sensors (
  id          SERIAL PRIMARY KEY,
  location_id INT NOT NULL REFERENCES locations(id) ON DELETE CASCADE,
  sensor_id   INT NOT NULL REFERENCES sensors(id)   ON DELETE CASCADE,
  active      BOOLEAN NOT NULL DEFAULT true,
  notes       TEXT
);
