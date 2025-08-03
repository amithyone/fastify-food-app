<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use App\Services\PaymentCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentSettingController extends Controller
{
    protected $paymentCalculationService;

    public function __construct(PaymentCalculationService $paymentCalculationService)
    {
        $this->paymentCalculationService = $paymentCalculationService;
    }

    /**
     * Display a listing of payment settings
     */
    public function index()
    {
        $settings = PaymentSetting::orderBy('key')->get();
        
        return view('admin.payment-settings.index', compact('settings'));
    }

    /**
     * Show the form for creating a new payment setting
     */
    public function create()
    {
        return view('admin.payment-settings.create');
    }

    /**
     * Store a newly created payment setting
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|unique:payment_settings,key',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
            'status' => 'required|in:active,inactive',
            'conditions' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        PaymentSetting::create($request->all());

        return redirect()->route('admin.payment-settings.index')
            ->with('success', 'Payment setting created successfully.');
    }

    /**
     * Show the form for editing the specified payment setting
     */
    public function edit(PaymentSetting $paymentSetting)
    {
        return view('admin.payment-settings.edit', compact('paymentSetting'));
    }

    /**
     * Update the specified payment setting
     */
    public function update(Request $request, PaymentSetting $paymentSetting)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
            'status' => 'required|in:active,inactive',
            'conditions' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $paymentSetting->update($request->all());

        return redirect()->route('admin.payment-settings.index')
            ->with('success', 'Payment setting updated successfully.');
    }

    /**
     * Remove the specified payment setting
     */
    public function destroy(PaymentSetting $paymentSetting)
    {
        $paymentSetting->delete();

        return redirect()->route('admin.payment-settings.index')
            ->with('success', 'Payment setting deleted successfully.');
    }

    /**
     * Toggle payment setting status
     */
    public function toggleStatus(PaymentSetting $paymentSetting)
    {
        $paymentSetting->update([
            'status' => $paymentSetting->status === 'active' ? 'inactive' : 'active'
        ]);

        return back()->with('success', 'Payment setting status updated successfully.');
    }

    /**
     * Test payment calculation
     */
    public function testCalculation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subtotal' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $orderData = [
            'subtotal' => $request->subtotal,
            'payment_method' => $request->payment_method
        ];

        $charges = $this->paymentCalculationService->calculateOrderCharges(
            $request->subtotal, 
            $orderData
        );

        return response()->json([
            'success' => true,
            'charges' => $charges,
            'formatted_breakdown' => $this->paymentCalculationService->getFormattedBreakdown($charges['breakdown'])
        ]);
    }

    /**
     * Get payment settings for API
     */
    public function apiIndex()
    {
        $settings = PaymentSetting::active()->get();
        
        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Update payment setting via API
     */
    public function apiUpdate(Request $request, $key)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $setting = $this->paymentCalculationService->updateSetting($key, $request->all());

        return response()->json([
            'success' => true,
            'data' => $setting,
            'message' => 'Payment setting updated successfully.'
        ]);
    }
}
