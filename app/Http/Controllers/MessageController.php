<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

Use App\Message;

class MessageController extends Controller
{
    public function index()
    {
        $message = Message::all();
        return response()->json($message, 200);
    }

    public function show($id)
    {
        $message = Message::find($id);
        return response()->json($message, 200);
    }

    public function store(Request $request)
    {
        $message = Message::create($request->all());
        return response()->json($message, 201);
    }

    public function update($id)
    {
        $message = Message::findOrFail($id);
        $message->IsysResponse = 'OK';
        $message->IsysURL = 'zain_kuwait_sms';
        $message->save();

        return response()->json(['message' => 'success']);
    }

    public function delete(Request $request, $id)
    {
        $message = Message::findOrFail($id);
        $message->delete();

        return response()->json(null, 204);
    }


}
