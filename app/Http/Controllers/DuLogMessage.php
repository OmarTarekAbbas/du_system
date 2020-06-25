<?php

namespace App\Http\Controllers;

use App\DuMo;
use App\LogMessage;
use Illuminate\Http\Request;

class DuLogMessage extends Controller
{
    public function index(Request $request)
    {
        $messages = DuMo::query();
        $without_paginate = 0;

        if ($request->has('msisdn') && $request->msisdn != '') {
            $messages = $messages->where('msisdn', $request->msisdn);
            $without_paginate = 1;
        }

        if ($request->has('message') && $request->message != '') {
            $messages = $messages->where('message', $request->message);
            $without_paginate = 1;
        }

        if ($request->has('created') && $request->created != '') {
            $messages = $messages->whereDate('created_at', $request->created);
            $without_paginate = 1;
        }

        if ($without_paginate) {
            $messages = $messages->get();
        } else {
            $messages = $messages->paginate(10);
        }
        return view('backend.moMessage.index', compact('messages', 'without_paginate'));
    }

    public function logMessage(Request $request)
    {
        $messages = LogMessage::query();
        $without_paginate = 0;

        if ($request->has('msisdn') && $request->msisdn != '') {
            $messages = $messages->where('msisdn', $request->msisdn);
            $without_paginate = 1;
        }

        if ($request->has('message') && $request->message != '') {
            $messages = $messages->where('message', 'LIKE', '%'.$request->message.'%');
            $without_paginate = 1;
        }

        if ($request->has('service_id') && $request->service_id != '') {
            $messages = $messages->where('service', $request->service_id);
            $without_paginate = 1;
        }

        if ($request->has('message_type') && $request->message_type != '') {
            $messages = $messages->where('message_type', $request->message_type);
            $without_paginate = 1;
        }

        if ($request->has('created') && $request->created != '') {
            $messages = $messages->whereDate('created_at', $request->created);
            $without_paginate = 1;
        }

        if ($without_paginate) {
            $messages = $messages->get();
        } else {
            $messages = $messages->paginate(10);
        }
        return view('backend.logMessage.index', compact('messages', 'without_paginate'));
    }
}
