composer show > rector.log
vendor/bin/rector list-rules --output-format json extend > rector_rules_out.json
vendor/bin/rector process --dry-run extend