<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public Route
    // Site routes
Route::get('/siteNewsView', [\App\Http\Controllers\NewsController::class, 'viewSite']);
Route::post('/siteComplainAdd', [\App\Http\Controllers\ComplainController::class, 'store']);
Route::get('/siteComplainsView', [\App\Http\Controllers\ComplainController::class, 'siteIndex']);
Route::get('/siteComplainActionsView', [\App\Http\Controllers\ComplainActionController::class, 'siteIndex']);

// Public gallery and downloads routes
Route::get('/gallery', [\App\Http\Controllers\GalleryController::class, 'index']);
Route::get('/gallery/{id}', [\App\Http\Controllers\GalleryController::class, 'show']);
Route::get('/downloadReport', [\App\Http\Controllers\DownloadCommitteeReportController::class, 'index']);
Route::get('/downloadReport/{id}', [\App\Http\Controllers\DownloadCommitteeReportController::class, 'show']);
Route::get('/downloadActs', [\App\Http\Controllers\DownloadActsController::class, 'index']);
Route::get('/downloadActs/{id}', [\App\Http\Controllers\DownloadActsController::class, 'show']);
Route::get('/downloadApplications', [\App\Http\Controllers\DownloadApplicationController::class, 'index']);
Route::get('/downloadApplications/{id}', [\App\Http\Controllers\DownloadApplicationController::class, 'show']);

    // Authentication-related routes
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/activate', [\App\Http\Controllers\AuthController::class, 'activate']);

    // Public directory routes
Route::get('/officers/directory', [\App\Http\Controllers\OfficerController::class, 'directory']);
Route::get('/members/directory', [\App\Http\Controllers\MemberController::class, 'directory']);

// Protected Routes with Sanctum Middleware
Route::middleware('auth:sanctum')->group(function () {

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('/member', \App\Http\Controllers\MemberController::class);
        Route::apiResource('/division', \App\Http\Controllers\DivisionController::class);
        Route::apiResource('/memberParty', \App\Http\Controllers\MemberPartyController::class);
        Route::apiResource('/memberPosition', \App\Http\Controllers\MemberPositionController::class);

        Route::apiResource('/officer', \App\Http\Controllers\OfficerController::class);
        Route::apiResource('/supplier', \App\Http\Controllers\SupplierController::class);
        
        // Hall Reservation Management Routes (OfficerHallReserve)
        Route::apiResource('/hall', \App\Http\Controllers\HallController::class);
        Route::get('/halls/statistics', [\App\Http\Controllers\HallController::class, 'getStatistics']);
        Route::get('/halls/availability', [\App\Http\Controllers\HallController::class, 'getAvailability']);
        
        // Hall Reservation Payment Routes
        Route::post('/hall-reservations/{id}/payments/online', [\App\Http\Controllers\HallReservationController::class, 'processOnlinePayment']);
        Route::post('/hall-reservations/{id}/payments', [\App\Http\Controllers\HallReservationController::class, 'addPayment']);
        
        // Facility Management
        Route::get('/facilities', [\App\Http\Controllers\HallController::class, 'getFacilities']);
        Route::post('/facilities', [\App\Http\Controllers\HallController::class, 'addFacility']);
        Route::put('/facilities/{id}', [\App\Http\Controllers\HallController::class, 'updateFacility']);
        Route::delete('/facilities/{id}', [\App\Http\Controllers\HallController::class, 'deleteFacility']);
        
        // Hall-Facility Management
        Route::post('/halls/{hallId}/facilities', [\App\Http\Controllers\HallController::class, 'addFacilityToHall']);
        Route::delete('/halls/{hallId}/facilities/{facilityId}', [\App\Http\Controllers\HallController::class, 'removeFacilityFromHall']);
        
        // Rate Management
        Route::post('/hall-rates', [\App\Http\Controllers\HallController::class, 'addHallRate']);
        Route::put('/hall-rates/{id}', [\App\Http\Controllers\HallController::class, 'updateHallRate']);
        Route::delete('/hall-rates/{id}', [\App\Http\Controllers\HallController::class, 'deleteHallRate']);
        
        // Reservation Management
        Route::get('/reservations', [\App\Http\Controllers\HallController::class, 'getReservations']);
        Route::get('/reservations/{id}', [\App\Http\Controllers\HallController::class, 'getReservation']);
        Route::put('/reservations/{id}/status', [\App\Http\Controllers\HallController::class, 'updateReservationStatus']);
        Route::post('/reservations/{id}/cancel', [\App\Http\Controllers\HallController::class, 'cancelReservation']);
        
        // Payment Management
        Route::post('/payments', [\App\Http\Controllers\HallController::class, 'addPayment']);
        Route::put('/payments/{id}/status', [\App\Http\Controllers\HallController::class, 'updatePaymentStatus']);
        Route::get('/reservations/{id}/payments', [\App\Http\Controllers\HallController::class, 'getReservationPayments']);
        
        // Water Bill Management Routes (OfficerWaterBill)
        Route::get('/water-schemes', [\App\Http\Controllers\WaterBillController::class, 'getWaterSchemes']);
        Route::post('/water-schemes', [\App\Http\Controllers\WaterBillController::class, 'addWaterScheme']);
        Route::get('/water-schemes/{id}', [\App\Http\Controllers\WaterBillController::class, 'getWaterScheme']);
        Route::put('/water-schemes/{id}', [\App\Http\Controllers\WaterBillController::class, 'updateWaterScheme']);
        Route::delete('/water-schemes/{id}', [\App\Http\Controllers\WaterBillController::class, 'deleteWaterScheme']);
        
        // Water Customer Management
        Route::get('/water-customers', [\App\Http\Controllers\WaterBillController::class, 'getWaterCustomers']);
        Route::post('/water-customers', [\App\Http\Controllers\WaterBillController::class, 'addWaterCustomer']);
        Route::get('/water-customers/{id}', [\App\Http\Controllers\WaterBillController::class, 'getWaterCustomer']);
        Route::get('/water-customers/account/{accountNo}', [\App\Http\Controllers\WaterBillController::class, 'getWaterCustomerByAccount']);
        Route::put('/water-customers/{id}', [\App\Http\Controllers\WaterBillController::class, 'updateWaterCustomer']);
        Route::delete('/water-customers/{id}', [\App\Http\Controllers\WaterBillController::class, 'deleteWaterCustomer']);
        
        // Meter Reader Management
        Route::post('/meter-readers', [\App\Http\Controllers\WaterBillController::class, 'addMeterReader']);
        Route::get('/water-schemes/{schemeId}/meter-readers', [\App\Http\Controllers\WaterBillController::class, 'getMeterReadersByScheme']);
        
        // Water Bill Management
        Route::apiResource('/water-bills', \App\Http\Controllers\WaterBillController::class);
        Route::get('/water-bills/customer/{customerId}', [\App\Http\Controllers\WaterBillController::class, 'getCustomerBills']);
        Route::get('/water-bills/account/{accountNo}', [\App\Http\Controllers\WaterBillController::class, 'getBillsByAccount']);
        Route::put('/water-bills/{id}/status', [\App\Http\Controllers\WaterBillController::class, 'updateBillStatus']);
        
        // Water Payment Management
        Route::post('/water-payments', [\App\Http\Controllers\WaterBillController::class, 'addWaterPayment']);
        Route::get('/water-bills/{billId}/payments', [\App\Http\Controllers\WaterBillController::class, 'getBillPayments']);
        
        // Water Bill Statistics
        Route::get('/water-bills/statistics', [\App\Http\Controllers\WaterBillController::class, 'getStatistics']);
        Route::get('/water-payments/summary', [\App\Http\Controllers\WaterBillController::class, 'getPaymentSummary']);
        Route::get('/suppliers/category/{category}', [\App\Http\Controllers\SupplierController::class, 'getByCategory']);
        Route::post('/suppliers/{id}/toggle-status', [\App\Http\Controllers\SupplierController::class, 'toggleStatus']);
        Route::get('/officerServices', [\App\Http\Controllers\OfficerServiceController::class, 'index']);
        Route::get('/officerLevels', [\App\Http\Controllers\OfficerLevelController::class, 'index']);
        Route::get('/officerGrades/{serviceId}', [\App\Http\Controllers\OfficerGradeController::class, 'getGradesByService']);
        Route::get('/officerPositions/{serviceId}', [\App\Http\Controllers\OfficerPositionController::class, 'getPositionsByService']);
        Route::get('/officerDuties/{positionId}', [\App\Http\Controllers\OfficerSubjectController::class, 'getDutiesByPosition']);
        Route::apiResource('/officerPosition', \App\Http\Controllers\OfficerPositionController::class);
        Route::apiResource('/officerSubject', \App\Http\Controllers\OfficerSubjectController::class);

    });

    // All users routes
    Route::middleware('role:admin|officer|member')->group(function () {
        Route::apiResource('/complains', \App\Http\Controllers\ComplainController::class);
        Route::apiResource('/complainActions', \App\Http\Controllers\ComplainActionController::class);
        Route::get('/newsCount', [\App\Http\Controllers\NewsController::class, 'count']);
        Route::get('/countDownload', [\App\Http\Controllers\DownloadActsController::class, 'count']);
        Route::get('/countGallery', [\App\Http\Controllers\GalleryImageController::class, 'count']);
        Route::get('/countProject', [\App\Http\Controllers\ProjectController::class, 'count']);
        Route::get('/countMember', [\App\Http\Controllers\MemberController::class, 'count']);
        Route::get('/complaincount', [\App\Http\Controllers\ComplainController::class, 'getCount']);
        Route::get('/countOfficer', [\App\Http\Controllers\OfficerController::class, 'count']);
        Route::get('/countSupplier', [\App\Http\Controllers\SupplierController::class, 'count']);
        
        
        // Public access to supporting data for directory views
        Route::get('/officerServices', [\App\Http\Controllers\OfficerServiceController::class, 'index']);
        Route::get('/officerLevels', [\App\Http\Controllers\OfficerLevelController::class, 'index']);
        Route::get('/officerGrades/{serviceId}', [\App\Http\Controllers\OfficerGradeController::class, 'getGradesByService']);
        Route::get('/officerPositions/{serviceId}', [\App\Http\Controllers\OfficerPositionController::class, 'getPositionsByService']);
        Route::get('/officerDuties/{positionId}', [\App\Http\Controllers\OfficerSubjectController::class, 'getDutiesByPosition']);
        Route::get('/divisions', [\App\Http\Controllers\DivisionController::class, 'index']);
        Route::get('/memberParties', [\App\Http\Controllers\MemberPartyController::class, 'index']);
        Route::get('/memberPositions', [\App\Http\Controllers\MemberPositionController::class, 'index']);
    
    // Hall Reservation Customer Routes
    Route::post('/hall-customers', [\App\Http\Controllers\HallReservationController::class, 'registerCustomer']);
    Route::get('/hall-customers/{id}', [\App\Http\Controllers\HallReservationController::class, 'getCustomer']);
    Route::put('/hall-customers/{id}', [\App\Http\Controllers\HallReservationController::class, 'updateCustomer']);
    
    // Hall Availability and Booking
    Route::get('/halls', [\App\Http\Controllers\HallReservationController::class, 'getHalls']);
    Route::get('/halls/{id}', [\App\Http\Controllers\HallReservationController::class, 'getHall']);
    Route::get('/halls/availability', [\App\Http\Controllers\HallReservationController::class, 'getAvailableHalls']);
    
    // Customer Reservations
    Route::post('/reservations', [\App\Http\Controllers\HallReservationController::class, 'createReservation']);
    Route::get('/customers/{customerId}/reservations', [\App\Http\Controllers\HallReservationController::class, 'getCustomerReservations']);
    Route::get('/reservations/{id}', [\App\Http\Controllers\HallReservationController::class, 'getReservation']);
    
    // Customer Payments
    Route::post('/reservation-payments', [\App\Http\Controllers\HallReservationController::class, 'addPayment']);
    Route::get('/reservations/{id}/payments', [\App\Http\Controllers\HallReservationController::class, 'getReservationPayments']);
    
    // Water Bill Customer Routes
    Route::post('/water-bills/check', [\App\Http\Controllers\WaterCustomerController::class, 'checkBillDetails']);
    Route::get('/water-bills/account/{accountNo}', [\App\Http\Controllers\WaterCustomerController::class, 'getCustomerBills']);
    Route::get('/water-bills/{billId}', [\App\Http\Controllers\WaterCustomerController::class, 'getBillDetails']);
    Route::post('/water-bills/online-payment', [\App\Http\Controllers\WaterCustomerController::class, 'makeOnlinePayment']);
    Route::get('/water-bills/payment-history', [\App\Http\Controllers\WaterCustomerController::class, 'getPaymentHistory']);
    Route::get('/water-payments/{paymentId}/receipt', [\App\Http\Controllers\WaterCustomerController::class, 'getPaymentReceipt']);
    });

    // Meter Reader Routes
    Route::middleware('role:meterReader')->group(function () {
        Route::post('/meter-readings', [\App\Http\Controllers\MeterReaderController::class, 'addMeterReading']);
        Route::put('/meter-readings/{id}', [\App\Http\Controllers\MeterReaderController::class, 'updateMeterReading']);
        Route::get('/meter-readings/customer/{customerId}', [\App\Http\Controllers\MeterReaderController::class, 'getCustomerMeterReadings']);
        Route::get('/meter-readings', [\App\Http\Controllers\MeterReaderController::class, 'getAllMeterReadings']);
        Route::get('/meter-readings/date-range', [\App\Http\Controllers\MeterReaderController::class, 'getMeterReadingsByDateRange']);
        Route::get('/meter-readings/statistics', [\App\Http\Controllers\MeterReaderController::class, 'getMeterReadingStatistics']);
    });

     // Officer and Admin routes
    Route::middleware('role:officer|admin')->group(function () {
        Route::apiResource('/news', \App\Http\Controllers\NewsController::class);
        // Modified resource routes to exclude GET methods that are now public
        Route::post('/downloadActs', [\App\Http\Controllers\DownloadActsController::class, 'store']);
        Route::put('/downloadActs/{id}', [\App\Http\Controllers\DownloadActsController::class, 'update']);
        Route::delete('/downloadActs/{id}', [\App\Http\Controllers\DownloadActsController::class, 'destroy']);
        
        Route::post('/downloadReport', [\App\Http\Controllers\DownloadCommitteeReportController::class, 'store']);
        Route::put('/downloadReport/{id}', [\App\Http\Controllers\DownloadCommitteeReportController::class, 'update']);
        Route::delete('/downloadReport/{id}', [\App\Http\Controllers\DownloadCommitteeReportController::class, 'destroy']);

        // Applications management (POST/PUT/DELETE are protected, GET is public above)
        Route::post('/downloadApplications', [\App\Http\Controllers\DownloadApplicationController::class, 'store']);
        Route::put('/downloadApplications/{id}', [\App\Http\Controllers\DownloadApplicationController::class, 'update']);
        Route::delete('/downloadApplications/{id}', [\App\Http\Controllers\DownloadApplicationController::class, 'destroy']);
        
        Route::post('/gallery', [\App\Http\Controllers\GalleryController::class, 'store']);
        Route::put('/gallery/{id}', [\App\Http\Controllers\GalleryController::class, 'update']);
        Route::delete('/gallery/{id}', [\App\Http\Controllers\GalleryController::class, 'destroy']);
        
        // Gallery Image Management Routes
        Route::delete('/gallery-images/{id}', [\App\Http\Controllers\GalleryImageController::class, 'destroy']);
        Route::post('/gallery-images/delete-multiple', [\App\Http\Controllers\GalleryImageController::class, 'deleteMultiple']);
        Route::post('/gallery-images/update-order', [\App\Http\Controllers\GalleryImageController::class, 'updateOrder']);
        Route::apiResource('/project', \App\Http\Controllers\ProjectController::class);
        Route::apiResource('/watersup', \App\Http\Controllers\WaterSupplyController::class);
        Route::apiResource('/addComplain', \App\Http\Controllers\ComplainController::class);

        Route::apiResource('/addTax', \App\Http\Controllers\TaxController::class);
    });

    // Tax System Routes
    Route::middleware('role:officerTax|admin')->group(function () {
        // Tax Payee Management
        Route::apiResource('/tax-payees', \App\Http\Controllers\TaxPayeeController::class);
        Route::get('/tax-payees/search/nic', [\App\Http\Controllers\TaxPayeeController::class, 'searchByNic']);
        
        // Tax Property Management
        Route::apiResource('/tax-properties', \App\Http\Controllers\TaxPropertyController::class);
        Route::get('/tax-properties/payee/{payeeId}', [\App\Http\Controllers\TaxPropertyController::class, 'getByPayee']);
        Route::get('/tax-properties/types', [\App\Http\Controllers\TaxPropertyController::class, 'getPropertyTypes']);
        
        // Tax Assessment Management
        Route::apiResource('/tax-assessments', \App\Http\Controllers\TaxAssessmentController::class);
        Route::get('/tax-assessments/payee/{payeeId}', [\App\Http\Controllers\TaxAssessmentController::class, 'getByPayee']);
        Route::put('/tax-assessments/{id}/mark-overdue', [\App\Http\Controllers\TaxAssessmentController::class, 'markOverdue']);
        
        // Tax Payment Management
        Route::apiResource('/tax-payments', \App\Http\Controllers\TaxPaymentController::class);
        Route::post('/tax-payments/cash/{assessmentId}', [\App\Http\Controllers\TaxPaymentController::class, 'store']);
        
        // Penalty Notices
        Route::apiResource('/penalty-notices', \App\Http\Controllers\TaxPenaltyNoticeController::class);
        Route::post('/penalty-notices/assessment/{assessmentId}', [\App\Http\Controllers\TaxPenaltyNoticeController::class, 'issueForAssessment']);
        Route::put('/penalty-notices/{id}/resolve', [\App\Http\Controllers\TaxPenaltyNoticeController::class, 'resolve']);
        
        // Property Prohibition Orders
        Route::apiResource('/prohibition-orders', \App\Http\Controllers\PropertyProhibitionOrderController::class);
        Route::post('/prohibition-orders/property/{propertyId}', [\App\Http\Controllers\PropertyProhibitionOrderController::class, 'issueForProperty']);
        Route::put('/prohibition-orders/{id}/revoke', [\App\Http\Controllers\PropertyProhibitionOrderController::class, 'revoke']);
        Route::get('/prohibition-orders/active', [\App\Http\Controllers\PropertyProhibitionOrderController::class, 'getActive']);
    });

    // Tax Customer Routes
    Route::middleware('role:customerTax')->group(function () {
        Route::get('/tax-payees/search/nic', [\App\Http\Controllers\TaxPayeeController::class, 'searchByNic']);
        Route::get('/tax-assessments/payee/{payeeId}', [\App\Http\Controllers\TaxAssessmentController::class, 'getByPayee']);
        Route::post('/tax-payments/online/{assessmentId}', [\App\Http\Controllers\TaxPaymentController::class, 'processOnlinePayment']);
        Route::get('/tax-payments/history', [\App\Http\Controllers\TaxPaymentController::class, 'index']);
    });

    // Unified Payment System Routes
    Route::post('/payments/online', [\App\Http\Controllers\UnifiedPaymentController::class, 'processOnlinePayment']);
    Route::get('/payments/{paymentType}/{paymentId}/receipt', [\App\Http\Controllers\UnifiedPaymentController::class, 'getReceipt']);
    Route::get('/payments/{paymentType}/{paymentId}/receipt/download', [\App\Http\Controllers\UnifiedPaymentController::class, 'downloadReceipt']);
    Route::get('/payments/status/{orderId}', [\App\Http\Controllers\UnifiedPaymentController::class, 'getPaymentStatus']);

    // PayHere Webhook (Public route for payment callbacks)
    Route::post('/payhere/callback', [\App\Http\Controllers\UnifiedPaymentController::class, 'handleCallback']);

    // SMS Notification Routes
    Route::middleware('role:admin|officerTax|officerWaterBill|officerHallReserve')->group(function () {
        Route::post('/sms/test', [\App\Http\Controllers\SmsNotificationController::class, 'sendTestSms']);
        Route::post('/sms/payment-confirmation', [\App\Http\Controllers\SmsNotificationController::class, 'sendPaymentConfirmation']);
        Route::post('/sms/service-reminder', [\App\Http\Controllers\SmsNotificationController::class, 'sendServiceReminder']);
        Route::post('/sms/overdue-notice', [\App\Http\Controllers\SmsNotificationController::class, 'sendOverdueNotice']);
        Route::post('/sms/reservation-confirmation', [\App\Http\Controllers\SmsNotificationController::class, 'sendReservationConfirmation']);
        Route::post('/sms/tax-assessment', [\App\Http\Controllers\SmsNotificationController::class, 'sendTaxAssessment']);
        Route::post('/sms/water-bill', [\App\Http\Controllers\SmsNotificationController::class, 'sendWaterBill']);
        Route::post('/sms/custom', [\App\Http\Controllers\SmsNotificationController::class, 'sendCustomSms']);
        Route::get('/sms/status/{messageSid}', [\App\Http\Controllers\SmsNotificationController::class, 'getDeliveryStatus']);
        Route::get('/sms/account-info', [\App\Http\Controllers\SmsNotificationController::class, 'getAccountInfo']);
    });

    // Get authenticated user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
