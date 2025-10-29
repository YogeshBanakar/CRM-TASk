<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    public function index()
    {
        $fields = CustomField::all();
        return view('custom-fields.index', compact('fields'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,date,dropdown,checkbox',
            'options' => 'nullable|array|required_if:type,dropdown'
        ]);

        CustomField::create([
            'name' => $request->name,
            'type' => $request->type,
            'options' => $request->type === 'dropdown' ? json_encode($request->options) : null
        ]);

        return response()->json(['success' => true, 'message' => 'Custom field added!']);
    }

    public function destroy(CustomField $customField)
    {
        $customField->delete();
        return response()->json(['success' => true, 'message' => 'Field deleted.']);
    }
}