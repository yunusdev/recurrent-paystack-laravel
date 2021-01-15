<?php

namespace App\Http\Controllers;

use App\Card;
use App\Http\Services\PaystackService;
use App\User;
use App\WalletHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Reference;
use phpDocumentor\Reflection\Types\Integer;

class PaymentController extends Controller
{
    //
    private $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
        $this->middleware('auth');

    }

    public function index()
    {

        $data['paystack_pb_key'] = env('PAYSTACK_PUBLIC_KEY');
        $data['wallet_histories'] = auth()->user()->walletHistories;
        return view('home')->with($data);

    }

    public function pay(Request $request, $reference){

        $user = auth()->user();

        $userCard = auth()->user()->card;

        if (!$userCard){

            $res =  $this->paystackService->verifyTransaction($reference);

            $card = null;

            if($res['status'] == true && strtolower($res['data']['status']) == 'success' ){

                User::topUpWalletBalance($user->wallet_balance + ($res['data']['amount'] / 100));

                $card = $res['data']['authorization'];

                $cardPayload = [

                    'user_id' => $user->id,
                    'reference' => $reference,
                    'authorization_code' => $card['authorization_code'],
                    'bank' => $card['bank'],
                    'card_type' => $card['card_type'],
                    'country' => $card['country_code'],
                    'last4' => $card['last4'],
                    'exp_year' => $card['exp_year'],
                    'exp_month' => $card['exp_month'],
                ];

                $card =  Card::create($cardPayload);

            }

            return response()->json([

                'card' => $card,
                'history' => $this->logWalletHistory($res['data'], $res['data']['amount'] / 100, $user->id)

            ]);
        }

        $res = $this->paystackService->recurrentCharge([

            'amount' => intval($request->amount) * 100,
            'email' => $user->email,
            'authorization_code' => $userCard->authorization_code
        ]);

        if($res['status'] == true && strtolower($res['data']['status']) == 'success'){

            User::topUpWalletBalance($user->wallet_balance + ($res['data']['amount'] / 100));

        }

        return response()->json([

            'history' => $this->logWalletHistory($res['data'], ($res['data']['amount'] / 100), $user->id)

        ]);

    }

    public function deleteCard(Request $request){

        return Card::deleteUserCard();

    }

    public function logWalletHistory(array $data, int $amount, int $userId){

        $payload = [

            'currency' => $data['currency'],
            'amount' => $amount,
            'status' => $data['status'],
            'user_id' => $userId,
            'reference' => $data['reference'],

        ];

        return WalletHistory::create($payload);

    }
}
