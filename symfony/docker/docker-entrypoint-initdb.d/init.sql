CREATE SEQUENCE holding_id_seq START 1;
ALTER TABLE holding ALTER COLUMN id SET DEFAULT nextval('holding_id_seq');
CREATE SEQUENCE asset_prices_id_seq START 1;
ALTER TABLE asset_prices ALTER COLUMN id SET DEFAULT nextval('asset_prices_id_seq');
CREATE SEQUENCE account_asset_return_id_seq START 1;
ALTER TABLE account_asset_return ALTER COLUMN id SET DEFAULT nextval('account_asset_return_id_seq');