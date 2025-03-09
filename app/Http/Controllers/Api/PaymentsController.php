<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentsStoreRequest;
use App\Http\Requests\PaymentsUpdateRequest;
use App\Http\Response\ApiResponse;
use App\Interfaces\PaymentServiceInterface;
use App\Models\Payments;

class PaymentsController extends Controller
{
    protected $paymentsService;

    public function __construct(PaymentServiceInterface $paymentsService)
    {
        $this->paymentsService = $paymentsService;
    }

    public function index()
    {
        return $this->paymentsService->all();
    }

    public function store(PaymentsStoreRequest $request)
    {
        $payments = $this->paymentsService->store($request->validated());

        return ApiResponse::created($payments, 'Payment created successfully!');
    }

    public function show(Payments $payments)
    {
        return $this->paymentsService->find($payments);
    }

    public function update(PaymentsUpdateRequest $request, Payments $payments)
    {
        $payments = $this->paymentsService->update($request->validated(), $payments);

        return ApiResponse::created($payments, 'Payment updated successfully!');
    }

    public function destroy(Payments $payments)
    {
        $payments = $this->paymentsService->delete($payments);

        return ApiResponse::created($payments, 'Payment deleted successfully!');
    }
}
