<?php
require_once('JoyPla/config.php');
require_once('JoyPla/JoyPlaApplication.php');
require_once('JoyPla/Application/Interactors/Api/Barcode/BarcodeOrderSearchInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Barcode/BarcodeSearchInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Consumption/ConsumptionDeleteInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Consumption/ConsumptionRegisterInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Consumption/ConsumptionShowInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Distributor/DistributorShowInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Division/DivisionShowInteractor.php');
require_once('JoyPla/Application/Interactors/Api/InHospitalItem/InHospitalItemShowInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Order/FixedQuantityOrderInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Order/OrderRegisterInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Order/OrderRevisedInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Order/OrderShowInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Order/OrderUnReceivedShowInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Order/OrderUnapprovedApprovalInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Order/OrderUnapprovedDeleteInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Order/OrderUnapprovedItemDeleteInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Order/OrderUnapprovedUpdateInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Received/ReceivedRegisterByOrderSlipInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Received/ReceivedRegisterInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Received/ReceivedReturnRegisterInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Received/ReceivedShowInteractor.php');
require_once('JoyPla/Application/Interactors/Api/Return/ReturnShowInteractor.php');
require_once('JoyPla/Application/Interactors/Web/Consumption/ConsumptionIndexInteractor.php');
require_once('JoyPla/Application/Interactors/Web/Order/OrderIndexInteractor.php');
require_once('JoyPla/Application/Interactors/Web/Received/OrderReceivedSlipIndexInteractor.php');
require_once('JoyPla/Application/Interactors/Web/Received/ReceivedIndexInteractor.php');
require_once('JoyPla/Application/Interactors/Web/Received/ReceivedLabelInteractor.php');
require_once('JoyPla/Enterprise/Traits/ValueObjectTrait.php');
require_once('JoyPla/Enterprise/Models/Consumption.php');
require_once('JoyPla/Enterprise/Models/ConsumptionItem.php');
require_once('JoyPla/Enterprise/Models/Distributor.php');
require_once('JoyPla/Enterprise/Models/Division.php');
require_once('JoyPla/Enterprise/Models/Hospital.php');
require_once('JoyPla/Enterprise/Models/InHospitalItem.php');
require_once('JoyPla/Enterprise/Models/InventoryCalculation.php');
require_once('JoyPla/Enterprise/Models/Item.php');
require_once('JoyPla/Enterprise/Models/Lot.php');
require_once('JoyPla/Enterprise/Models/Order.php');
require_once('JoyPla/Enterprise/Models/OrderItem.php');
require_once('JoyPla/Enterprise/Models/Quantity.php');
require_once('JoyPla/Enterprise/Models/Received.php');
require_once('JoyPla/Enterprise/Models/ReceivedItem.php');
require_once('JoyPla/Enterprise/Models/Redemption.php');
require_once('JoyPla/Enterprise/Models/ReturnData.php');
require_once('JoyPla/Enterprise/Models/ReturnItem.php');
require_once('JoyPla/Enterprise/Models/Stock.php');
require_once('JoyPla/Enterprise/CommonModels/GatePermissionModel.php');
require_once('JoyPla/Enterprise/CommonModels/ReceivedLabelModel.php');
require_once('JoyPla/Enterprise/ValueObject/ValueObject.php');
require_once('JoyPla/InterfaceAdapters/Controllers/Api/BarcodeController.php');
require_once('JoyPla/InterfaceAdapters/Controllers/Api/ConsumptionController.php');
require_once('JoyPla/InterfaceAdapters/Controllers/Api/DistributorController.php');
require_once('JoyPla/InterfaceAdapters/Controllers/Api/DivisionController.php');
require_once('JoyPla/InterfaceAdapters/Controllers/Api/InHospitalItemController.php');
require_once('JoyPla/InterfaceAdapters/Controllers/Api/OrderController.php');
require_once('JoyPla/InterfaceAdapters/Controllers/Api/ReceivedController.php');
require_once('JoyPla/InterfaceAdapters/Controllers/Api/ReturnController.php');
require_once('JoyPla/InterfaceAdapters/Controllers/Api/StockController.php');
require_once('JoyPla/InterfaceAdapters/Controllers/Api/StocktakingController.php');
require_once('JoyPla/InterfaceAdapters/Controllers/Web/AgreeFormController.php');
require_once('JoyPla/InterfaceAdapters/Controllers/Web/ConsumptionController.php');
require_once('JoyPla/InterfaceAdapters/Controllers/Web/OrderController.php');
require_once('JoyPla/InterfaceAdapters/Controllers/Web/ReceivedController.php');
require_once('JoyPla/InterfaceAdapters/Controllers/Web/ReturnController.php');
require_once('JoyPla/InterfaceAdapters/Controllers/Web/StocktakingController.php');
require_once('JoyPla/InterfaceAdapters/Controllers/Web/TopController.php');
require_once('JoyPla/InterfaceAdapters/GateWays/Middleware/PersonalInformationConsentMiddleware.php');
require_once('JoyPla/InterfaceAdapters/GateWays/Middleware/UnorderDataExistMiddleware.php');
require_once('JoyPla/InterfaceAdapters/GateWays/Repository/BarcodeRepository.php');
require_once('JoyPla/InterfaceAdapters/GateWays/Repository/ConsumptionRepository.php');
require_once('JoyPla/InterfaceAdapters/GateWays/Repository/DistributorRepository.php');
require_once('JoyPla/InterfaceAdapters/GateWays/Repository/DivisionRepository.php');
require_once('JoyPla/InterfaceAdapters/GateWays/Repository/HospitalRepository.php');
require_once('JoyPla/InterfaceAdapters/GateWays/Repository/InHospitalItemRepository.php');
require_once('JoyPla/InterfaceAdapters/GateWays/Repository/InventoryCalculationRepository.php');
require_once('JoyPla/InterfaceAdapters/GateWays/Repository/OrderRepository.php');
require_once('JoyPla/InterfaceAdapters/GateWays/Repository/ReceivedRepository.php');
require_once('JoyPla/InterfaceAdapters/GateWays/Repository/ReturnRepository.php');
require_once('JoyPla/InterfaceAdapters/GateWays/Repository/StockRepository.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Barcode/BarcodeOrderSearchPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Barcode/BarcodeSearchPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Consumption/ConsumptionDeletePresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Consumption/ConsumptionRegisterPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Consumption/ConsumptionShowPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Distributor/DistributorShowPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Division/DivisionShowPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/InHospitalItem/InHospitalItemShowPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Order/FixedQuantityOrderPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Order/OrderRegisterPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Order/OrderRevisedPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Order/OrderShowPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Order/OrderUnReceivedShowPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Order/OrderUnapprovedApprovalPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Order/OrderUnapprovedDeletePresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Order/OrderUnapprovedItemDeletePresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Order/OrderUnapprovedShowPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Order/OrderUnapprovedUpdatePresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Received/ReceivedRegisterByOrderSlipPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Received/ReceivedRegisterPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Received/ReceivedReturnRegisterPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Received/ReceivedShowPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Api/Return/ReturnShowPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Web/Consumption/ConsumptionIndexPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Web/Consumption/ConsumptionPrintPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Web/Order/OrderIndexPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Web/Order/OrderPrintPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Web/Order/UnapprovedOrderIndexPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Web/Received/OrderReceivedSlipIndexPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Web/Received/ReceivedIndexPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Web/Received/ReceivedLabelPresenter.php');
require_once('JoyPla/InterfaceAdapters/Presenters/Web/Received/ReceivedLabelSettingPresenter.php');
require_once('JoyPla/Enterprise/SpiralDb/CardView.php');
require_once('JoyPla/Enterprise/SpiralDb/Consumption.php');
require_once('JoyPla/Enterprise/SpiralDb/ConsumptionItem.php');
require_once('JoyPla/Enterprise/SpiralDb/ConsumptionItemView.php');
require_once('JoyPla/Enterprise/SpiralDb/ConsumptionView.php');
require_once('JoyPla/Enterprise/SpiralDb/Distributor.php');
require_once('JoyPla/Enterprise/SpiralDb/DistributorAffiliationView.php');
require_once('JoyPla/Enterprise/SpiralDb/Division.php');
require_once('JoyPla/Enterprise/SpiralDb/Hospital.php');
require_once('JoyPla/Enterprise/SpiralDb/HospitalUser.php');
require_once('JoyPla/Enterprise/SpiralDb/InHospitalItem.php');
require_once('JoyPla/Enterprise/SpiralDb/InHospitalItemView.php');
require_once('JoyPla/Enterprise/SpiralDb/InventoryAdjustmentTransaction.php');
require_once('JoyPla/Enterprise/SpiralDb/Item.php');
require_once('JoyPla/Enterprise/SpiralDb/Order.php');
require_once('JoyPla/Enterprise/SpiralDb/OrderItem.php');
require_once('JoyPla/Enterprise/SpiralDb/OrderItemView.php');
require_once('JoyPla/Enterprise/SpiralDb/OrderView.php');
require_once('JoyPla/Enterprise/SpiralDb/PayoutItem.php');
require_once('JoyPla/Enterprise/SpiralDb/Price.php');
require_once('JoyPla/Enterprise/SpiralDb/Received.php');
require_once('JoyPla/Enterprise/SpiralDb/ReceivedItem.php');
require_once('JoyPla/Enterprise/SpiralDb/ReceivedItemView.php');
require_once('JoyPla/Enterprise/SpiralDb/ReceivedView.php');
require_once('JoyPla/Enterprise/SpiralDb/ReturnHistory.php');
require_once('JoyPla/Enterprise/SpiralDb/ReturnItem.php');
require_once('JoyPla/Enterprise/SpiralDb/ReturnItemView.php');
require_once('JoyPla/Enterprise/SpiralDb/ReturnView.php');
require_once('JoyPla/Enterprise/SpiralDb/Stock.php');
require_once('JoyPla/Enterprise/SpiralDb/StockView.php');
require_once('JoyPla/Enterprise/SpiralDb/Tenant.php');