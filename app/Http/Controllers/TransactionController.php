<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{

    private $__userId;
    private $__amount;
    private $__description;
    private $__transactionType;
    private $__status;
    private $__createdAt;
    private $__image;
    private $__approvedBy;

    public function __construct() {
        $this->__createdAt = now();
        $this->__image = null;
        $this->__approvedBy = null;
    }

    public function index(Request $request)
    {        

        $query = Transaction::query();

        if ($request->has('id')) {
            $query->where('id', $request->id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->get();

        return response()->json($transactions);
    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|numeric',
                'description' => 'required',
                'transaction_type' => 'required|in:D,P', //Deposit, Purchase
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }
    
    
            //set transactions values
            $this->__userId = $request->user_id;
            $this->__amount = $request->amount;
            $this->__description = $request->description;
            $this->__transactionType = $request->transaction_type;
    
            //Get user by id
            $user = User::find($request->user_id);
    
    
            //check if is a (D)eposit or (P)urchase
            if($this->__transactionType === 'D') {

                //check if has image - put "!"
                if ($request->hasFile('image')) {
                    return response()->json(['error' => 'Image upload is mandatory for this type of transaction'], 400);
                    //return error
                }
    
                $this->__status = 'UR'; //first status ir Under Review
                $this->__saveImage($request->image); //save image in server
    
                //return insert
                return $this->__insertTransaction();
    
            } elseif($this->__transactionType === 'P') {

                //check if the user has balance to it
                if ($this->__amount > $user->balance) {
                    $this->__status = 'R';
                } else {
                    $user->balance -= $request->amount;
                    $user->save(); //save new user amount

                    $this->__status = 'A';
                }


                return $this->__insertTransaction();
            } else {
                return response()->json(['error' => 'Invalid transaction_type'], 400);
            }

        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            );
        }
    }

    private function __insertTransaction()
    {

        $transaction = new Transaction();
        $transaction->user_id = $this->__userId;
        $transaction->amount = $this->__amount;
        $transaction->description = $this->__description;
        $transaction->transaction_type = $this->__transactionType;
        $transaction->status = $this->__status;
        $transaction->created_at = $this->__createdAt;
        $transaction->image = $this->__image;
        $transaction->approved_by = $this->__approvedBy;
        $transaction->save();

        return response()->json($transaction, 201);
    }

    private function __saveImage($image)
    {
        try {
            //Remove header
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            //Put it in binary
            $dataImage = base64_decode($image);

            $ext = 'jpg';
            if (preg_match('/^data:image\/(\w+);base64,/', $image, $matches)) {
                $ext = $matches[1];
            }

            //File name
            $fileName = $this->__userId . now()->timestamp . "-deposit." . $ext;

            //Save image in the server
            $saved = file_put_contents('deposits/'.$fileName, $dataImage);

            if($saved) {
                $this->__image = 'deposits/' . $fileName;

                return true;
            }

            return false;

        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            );
        }
    }

    public function updateTransactionStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:transactions',
                'status' => 'required|in:A,R',
                'approved_by' => 'required|exists:users,id,user_type,A'
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
    
            $transaction = Transaction::findOrFail($request->id);

            //check if transction is under review
            if($transaction->status != 'UR') {
                return response()->json("Cannot update transaction that is not under review", 400);
            }
            $transaction->status = $request->status;
            $transaction->approved_by = $request->approved_by;
    
            //get user to update balance
            $user = User::find($transaction->user_id);
            if(!$user) {
                return response()->json("User not found", 400);
            }

            if ($request->status === 'A') {
                $user->balance += $transaction->amount;
                $user->save(); //save new user amount
            }
    
            $transaction->save();
    
            return response()->json($transaction, 200);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            );
        }
    }
}
