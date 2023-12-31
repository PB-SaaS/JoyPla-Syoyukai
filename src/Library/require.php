<?php
require_once('Library/gs1128-decoder/ApplicationIdentifiers.php');
require_once('Library/gs1128-decoder/Barcode.php');
require_once('Library/gs1128-decoder/Contracts/Identifier.php');
require_once('Library/gs1128-decoder/Contracts/Identifiers/VariableLength.php');
require_once('Library/gs1128-decoder/Contracts/Identifiers/WithDecimals.php');
require_once('Library/gs1128-decoder/Decoder.php');
require_once('Library/gs1128-decoder/Exceptions/InvalidBarcode.php');
require_once('Library/gs1128-decoder/Exceptions/InvalidDecimalsException.php');
require_once('Library/gs1128-decoder/Exceptions/MissingIdentifier.php');
require_once('Library/gs1128-decoder/IdentifierCollection.php');
require_once('Library/gs1128-decoder/Identifiers/Abstracts/1Identifier.php');
require_once('Library/gs1128-decoder/Identifiers/Abstracts/DateIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/Abstracts/FloatIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/BatchLotIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/BestBeforeDateIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/CompanyInternalInformationIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/ContentIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/DueDateIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/ExpirationDateIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/ExpirationDateAndTimeIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/GTINIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/NetWeightKgIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/OriginIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/PackagingDateIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/PriceIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/PricePerUnitIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/ProductionDateIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/ProductionTimeIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/SSCCIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/SellByDateIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/SerialNumberIdentifier.php');
require_once('Library/gs1128-decoder/Identifiers/VariantIdentifier.php');
