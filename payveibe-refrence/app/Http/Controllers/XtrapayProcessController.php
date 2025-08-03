<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\WebhookService;

class XtrapayProcessController extends Controller
{
    public function ipn(Request $request)
    {
        // Retrieve JSON payload
        $payload = $request->json()->all();

        // Ensure required fields exist
        if (!isset($payload['data']) || !isset($payload['hash'])) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        // Retrieve access key securely
        $accessKey = env('XTRAPAY_ACCESS_KEY', 'your_default_access_key');

        // Compute expected hash
        $computedHash = hash_hmac('sha256', json_encode($payload['data']), $accessKey);
        
        // Verify hash

        if (!hash_equals($computedHash, $payload['hash'])) {
            return $this->updateDepositInfo($payload['data']['reference'] ?? null, 'Invalid Authentication');
        }

        // Extract transaction details
        $data = $payload['data'];
        $reference = $data['reference'] ?? null;
        $amountReceived = $data['amount'] ?? 0;
        $status = strtolower($data['status'] ?? 'pending'); // Normalize status

        // Define valid statuses
        $validStatuses = ['pending', 'successful', 'failed', 'reversed'];

        if (!in_array($status, $validStatuses)) {
            return $this->updateDepositInfo($reference, "Invalid status received: {$status}");
        }

        // Find deposit transaction with row locking
        $deposit = Transaction::where('ref_id', $reference)->lockForUpdate()->first();

        if (!$deposit) {
            return $this->updateDepositInfo($reference, 'Deposit not found');
        }

        // Prevent multiple processing of successful transactions
        if ($deposit->status == 2 && $status == 'successful') {
            return response()->json(['message' => 'Transaction already processed'], 200);
        }
        $mismatch = false;

        // Validate received amount against the expected deposit amount
        if ((float) $amountReceived < (float) $deposit->final_amount) {
            $this->updateDepositInfo($reference, "Amount mismatch: Expected {$deposit->final_amount}, received {$amountReceived}", $data);
            $mismatch = true;
            $deposit->final_amount = $amountReceived;
            $deposit->charge = 100 + (round($amountReceived, 2) * 0.015);
            if ($amountReceived >= 10000) {
                $deposit->charge = 100 + (round($amountReceived, 2) * 0.02);
            }
            $deposit->amount = $deposit->final_amount - $deposit->charge;
            $deposit->save();
            
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            if ($status === 'successful') {
                // Lock user record to prevent race conditions
                $user = User::where('id', $deposit->user_id)->lockForUpdate()->first();

                if ($user) {
                    // Update user balance
                    $user->increment('wallet', $deposit->amount);
                }

                // Mark deposit as successful
                $deposit->update(['status' => 2]);
                
                // Send webhook for successful transaction
                WebhookService::sendSuccessfulTransaction($deposit, $user);
                
                if (!$mismatch) {
                    $this->updateDepositInfo($reference, 'Transaction successful', $data);
                }
                
                        // Check for suspicious name in transaction
                $needle = 'PROMISE JAMES CHUKWUMA';
                $found = false;
        
                // Ensure 'detail' and 'extra_data' are structured correctly
                $extraData = $deposit->fresh()->detail['extra_data'] ?? [];
        
                foreach ($extraData as $key => $value) {
                    if (is_string($value) && stripos($value, $needle) != false) {
                        $found = true;
                        break;
                    }
                }
        
                if ($found && $user) {
                    $user->disabled = 1;
                    $user->save(); // cleaner than update() for single-field change
                }


            } elseif ($status === 'failed' || $status === 'reversed') {
                // Mark deposit as failed/reversed
                $deposit->update(['status' => 1]);
                
                // Send webhook for failed transaction
                WebhookService::sendFailedTransaction($deposit, $user, "Transaction {$status}");
                
                $this->updateDepositInfo($reference, "Transaction marked as {$status}", $data);
            }

            // Commit transaction
            DB::commit();

            return response()->json(['message' => 'Transaction Processed successfully'], 200);
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            return $this->updateDepositInfo($reference, 'Database error: ' . $e->getMessage());
        }
    }

    /**
     * Update the deposit detail JSON with additional info.
     *
     * @param string|null $reference
     * @param string $message
     * @param array|null $extraData
     * @return \Illuminate\Http\JsonResponse
     */
    private function updateDepositInfo($reference, $message, $extraData = null)
    {
        if (!$reference) {
            return response()->json(['error' => 'Transaction reference missing'], 400);
        }

        $deposit = Transaction::where('ref_id', $reference)->lockForUpdate()->first();

        if (!$deposit) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        // Decode existing details
        $detail = $deposit->detail;

        // Add new info
        $detail['info'] = $message;

        if ($extraData) {
            $detail['extra_data'] = $extraData;
        }

        // Update deposit record
        $deposit->update(['detail' => $detail]);

        return response()->json(['error' => $message], 400);
    }

    public function checkTransaction(Request $request, $reference)
    {
        #$reference = $request->query('reference');

        // Validate input
        if (!$reference) {
            return response()->json(['error' => 'Reference number is required'], 400);
        }

        // XtraPay API URL
        $url = "https://mobile.xtrapay.ng/api/faddedsocials/requeryTransaction/{$reference}";
        $accessKey = env('XTRAPAY_ACCESS_KEY', 'your_default_access_key');

        $response = Http::withToken($accessKey)->get($url);

        // Decode response
        $data = $response->json();

        // Check if request was successful
        if ($data['status'] == 'Successful' && isset($data['data']['payload'])) {
            $payload = $data['data']['payload'];

            // Send payload to internal endpoint
            $internalResponse = Http::post('https://faddedsms.com/api/ipn/xtrapay', $payload);

            // Return the response from the internal endpoint
            return response()->json($internalResponse->json(), $internalResponse->status());
        }

        // If request failed, return the error response
        return response()->json($data, $response->status());
    }

    public function requery($reference)
    {
        // Define the API endpoint
        $url = "https://faddedsms.com/api/ipn/xtrapay/requery/{$reference}";

        try {
            // Make a GET request with Bearer token
            $response = Http::get($url);

            // Decode response
            $data = $response->json();

            // Check if request was successful (HTTP 200)
            if ($response->successful() && isset($data['data']['response']['message'])) {
                $message = $data['data']['response']['message'];


                // Redirect back with success message
                return back()->with('message', $message);
            } else {
                // Handle error message from response
                $error = $data['message'] ?? 'Something went wrong. Please try again.';
                return back()->with('error', $error);
            }

        } catch (\Exception $e) {
            // Catch request failures (network errors, timeouts, etc.)
            return back()->with('error', 'Unable to process request.');
        }
    }
}
