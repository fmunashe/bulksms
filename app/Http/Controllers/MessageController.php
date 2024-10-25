<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Models\Merchant;
use App\Models\Message;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMessageRequest $request): JsonResponse
    {
        $merchant = Merchant::query()->where('trade_name', 'like', '%' . $request->input('merchant') . '%')->first();
        if ($merchant != null) {
            Message::query()->create([
                'merchant_id' => $merchant->id,
                'text_message' => $request->input('message'),
                'recipient' => $request->input('recipient')
            ]);
            return response()->json("Message Successfully Sent");
        }
        return response()->json("Provided Merchant " . $request->input('merchant') . " does not exist");
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMessageRequest $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        //
    }
}
