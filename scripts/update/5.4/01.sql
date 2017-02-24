ALTER TABLE steam_replays
ADD COLUMN uploaded_to_s3 smallint NOT NULL DEFAULT 0;

INSERT INTO cms_configuration_parameters(module_id, parameter_name, sort_order, parameter_data_type_id, display_name, has_value_list)
VALUES (2, 'aws_s3_write_key', 20, 1, 'AWS S3 Write Key', 0);

INSERT INTO cms_configuration_parameters(module_id, parameter_name, sort_order, parameter_data_type_id, display_name, has_value_list)
VALUES (2, 'aws_s3_write_secret', 21, 1, 'AWS S3 Write Secret', 0); 

INSERT INTO cms_configuration_parameters(module_id, parameter_name, sort_order, parameter_data_type_id, display_name, has_value_list)
VALUES (2, 'aws_s3_region', 22, 1, 'AWS S3 Region', 0);

INSERT INTO cms_configuration_parameters(module_id, parameter_name, sort_order, parameter_data_type_id, display_name, has_value_list)
VALUES (2, 'aws_s3_version', 23, 1, 'AWS S3 Version', 0); 