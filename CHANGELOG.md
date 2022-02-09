# Release Notes

## [v1.0.14 (2022-02-09)] (https://github.com/Pandrome/Datagrid/compare/1.0.13...1.0.14)

### Added
- Support for hasnot operator

## [v1.0.13 (2022-01-13)] (https://github.com/Pandrome/Datagrid/compare/1.0.12...1.0.13)

### Fixed
- Tag issue

## [v1.0.12 (2022-01-13)] (https://github.com/Pandrome/Datagrid/compare/1.0.11...1.0.12)

### Added
- Beter support for relational locked filters

## [v1.0.11 (2020-11-15)] (https://github.com/Pandrome/Datagrid/compare/1.0.10...1.0.11)

### Added
- Added keys for all loops in datagrid component
- Replaced flatpickr with a bootstrap popover with two input fields of type date.

## [v1.0.10 (2020-11-03)] (https://github.com/Pandrome/Datagrid/compare/1.0.9...1.0.10)

### Fixed
- Fixed version number

## [v1.0.9 (2020-11-03)] (https://github.com/Pandrome/Datagrid/compare/1.0.8...1.0.9)

### Added
- Added support for locked filters. In your datagrid add the protected variable $lockedFilters if you want to use preset filters. 
  Set a column name as the key and within you can use the key "value" which can contain a string or array 
  and you can use the key operator. Here you can give the operator to be used in the query. Usable operators can be found in
  laravel/framework/src/Illuminate/Database/Query/Builder.php. When using these operators a value is mandatory.
  If you need to filter for null or not null use the operator "null" or "notnull" a value is not needed then.
  Default operators are "in" for an array, otherwise "=".
  E.g.: In your class that extends AGrid add protected $lockedFilters = [ 'status' => [ 'operator' => '!=', 'value' => 'new' ] ];

## [v1.0.8 (2020-10-25)] (https://github.com/Pandrome/Datagrid/compare/1.0.7...1.0.8)

### Fixed
- Text field column now type casts to string if null

## [v1.0.7 (2020-10-07)] (https://github.com/Pandrome/Datagrid/compare/1.0.6...1.0.7)

### Added
- Support for button arguments by adding args.

## [v1.0.6 (2020-08-07)] (https://github.com/Pandrome/Datagrid/compare/1.0.5...1.0.6)

### Added
- AGrid now supports a default sort column

## [v1.0.5 (2020-07-17)] (https://github.com/Pandrome/Datagrid/compare/1.0.4...1.0.5)

### Updated
- Now automatically publishes when installing or updating with composer
- The filter dropdowns will now sort it's values alphabetically

### Fixed
- Replacing values for buttons now works as intended.

## [v1.0.4 (2020-07-15)] (https://github.com/Pandrome/Datagrid/compare/1.0.3...1.0.4)

### Updated
- Removed target _blank from links

## [v1.0.3 (2020-07-15)] (https://github.com/Pandrome/Datagrid/compare/1.0.2...1.0.3)

### Added
- Added support for using buttons as links by adding isLink = true to the button options