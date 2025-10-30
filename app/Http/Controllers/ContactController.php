<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactCustomValue;
use App\Models\CustomField;
use App\Models\MergedContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{


    public function getAll()
    {
        $contacts = Contact::whereNotIn('status', ['merged', 'deleted'])->get();
        return response()->json($contacts);
    }
    /**
     * Display a listing of contacts with search & filter via AJAX.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->search($request);
        }

        $contacts = Contact::whereNotIn('status', ['merged', 'deleted'])
            ->with('customValues.customField')
              ->orderBy('id', 'asc')
            ->get();

        $customFields = CustomField::all();

        return view('contacts.index', compact('contacts', 'customFields'));
    }

    /**
     * AJAX Search & Filter
     */
    public function search(Request $request)
    {
        $query = Contact::query()->whereNotIn('status', ['merged', 'deleted']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $contacts = $query->with('customValues.customField')->orderBy('id', 'asc')->get();

        return view('contacts.partials.table', compact('contacts'))->render();
    }

    /**
     * Show the form for creating a new contact.
     */
    public function create()
    {
        $customFields = CustomField::all();
        $contact = new Contact(); // Empty contact for form binding
        return response()->json([
            'html' => view('contacts.partials.form-fields', compact('contact', 'customFields'))->render()
        ]);
    }

    /**
     * Store a newly created contact via AJAX.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email',
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:male,female,other',
            'profile_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'additional_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['name', 'email', 'phone', 'gender']);

            if ($request->hasFile('profile_image')) {
                $data['profile_image'] = $request->file('profile_image')->store('contacts/images', 'public');
            }

            if ($request->hasFile('additional_file')) {
                $data['additional_file'] = $request->file('additional_file')->store('contacts/documents', 'public');
            }

            $contact = Contact::create($data);

            $this->syncCustomFields($contact, $request->input('custom_fields', []));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contact created successfully.',
                'contact' => $contact->load('customValues.customField')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create contact: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified contact.
     */
    public function edit(Contact $contact)
    {
        $customFields = CustomField::all();
        $contact->load('customValues.customField');
        return view('contacts.partials.form-fields', compact('contact', 'customFields'));
    }

    /**
     * Update the specified contact via AJAX.
     */
    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email,' . $contact->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:male,female,other',
            'profile_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'additional_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['name', 'email', 'phone', 'gender']);

            if ($request->hasFile('profile_image')) {
                if ($contact->profile_image) {
                    Storage::disk('public')->delete($contact->profile_image);
                }
                $data['profile_image'] = $request->file('profile_image')->store('contacts/images', 'public');
            }

            if ($request->hasFile('additional_file')) {
                if ($contact->additional_file) {
                    Storage::disk('public')->delete($contact->additional_file);
                }
                $data['additional_file'] = $request->file('additional_file')->store('contacts/documents', 'public');
            }

            $contact->update($data);
            $this->syncCustomFields($contact, $request->input('custom_fields', []));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contact updated successfully.',
                'contact' => $contact->load('customValues.customField')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified contact via AJAX (soft mark as merged later).
     */
    public function destroy(Contact $contact)
    {
        try {
            $contact->update(['status' => 'deleted']);
            return response()->json(['success' => true, 'message' => 'Contact deleted.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Delete failed.'], 500);
        }
    }

    /**
     * Preview merge between two contacts
     */
    public function mergePreview(Request $request)
    {
        $request->validate([
            'master_id' => 'required|exists:contacts,id',
            'secondary_id' => 'required|exists:contacts,id|different:master_id'
        ]);

        $master = Contact::with('customValues.customField')->find($request->master_id);
        $secondary = Contact::with('customValues.customField')->find($request->secondary_id);

        $diff = $this->calculateMergeDiff($master, $secondary);

        return response()->json([
            'diff' => $diff,
            'master' => $master,
            'secondary' => $secondary
        ]);
    }

    /**
     * Perform the actual merge
     */
    public function merge(Request $request)
    {
        $request->validate([
            'master_id' => 'required|exists:contacts,id',
            'secondary_id' => 'required|exists:contacts,id|different:master_id'
        ]);

        DB::beginTransaction();
        try {
            $master = Contact::find($request->master_id);
            $secondary = Contact::find($request->secondary_id);

            $mergeLog = [
                'emails' => [],
                'phones' => [],
                'custom_fields' => []
            ];

            // Merge emails
            if ($secondary->email && strpos($master->email ?? '', $secondary->email) === false) {
                $master->email = $master->email ? $master->email . ', ' . $secondary->email : $secondary->email;
                $mergeLog['emails'][] = $secondary->email;
                $updated = true;
            }

            // Merge phones
            if ($secondary->phone && strpos($master->phone ?? '', $secondary->phone) === false) {
                $master->phone = $master->phone ? $master->phone . ', ' . $secondary->phone : $secondary->phone;
                $mergeLog['phones'][] = $secondary->phone;
                $updated = true;
            }

            // Merge custom fields
            foreach ($secondary->customValues as $cv) {
                $existing = $master->customValues->where('custom_field_id', $cv->custom_field_id)->first();

                if (!$existing) {
                    ContactCustomValue::create([
                        'contact_id' => $master->id,
                        'custom_field_id' => $cv->custom_field_id,
                        'value' => $cv->value
                    ]);
                    $mergeLog['custom_fields'][] = [
                        'field' => $cv->customField->name,
                        'value' => $cv->value,
                        'action' => 'added'
                    ];
                } elseif ($existing->value !== $cv->value) {
                    $mergeLog['custom_fields'][] = [
                        'field' => $cv->customField->name,
                        'master_value' => $existing->value,
                        'secondary_value' => $cv->value,
                        'action' => 'kept_master'
                    ];
                }
            }

            if ($updated) {
                $master->save();
            }

            // Log merge (keep existing code)
            MergedContact::create([
                'master_contact_id' => $master->id,
                'merged_contact_id' => $secondary->id,
                'merged_data' => json_encode($mergeLog, JSON_UNESCAPED_UNICODE)
            ]);

            // Mark secondary as merged
            $secondary->update(['status' => 'merged']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contacts merged successfully.',
                'master' => $master->load('customValues.customField')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Merge failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // ===================================================================
    // Helper Methods
    // ===================================================================

    private function syncCustomFields(Contact $contact, array $customFields)
    {
        $contact->customValues()->delete();

        foreach ($customFields as $fieldId => $value) {
            if ($value !== null && $value !== '') {
                ContactCustomValue::create([
                    'contact_id' => $contact->id,
                    'custom_field_id' => $fieldId,
                    'value' => is_array($value) ? json_encode($value) : $value
                ]);
            }
        }
    }

    private function calculateMergeDiff(Contact $master, Contact $secondary)
    {
        $diff = ['standard' => [], 'custom' => []];

        if ($secondary->email && strpos($master->email ?? '', $secondary->email) === false) {
            $diff['standard']['email'] = [
                'master' => $master->email,
                'secondary' => $secondary->email,
                'action' => 'append to master'
            ];
        }

        if ($secondary->phone && strpos($master->phone ?? '', $secondary->phone) === false) {
            $diff['standard']['phone'] = [
                'master' => $master->phone,
                'secondary' => $secondary->phone,
                'action' => 'append to master'
            ];
        }

        foreach ($secondary->customValues as $cv) {
            $masterValue = $master->customValues->where('custom_field_id', $cv->custom_field_id)->first();

            if (!$masterValue) {
                $diff['custom'][$cv->customField->name] = [
                    'master' => null,
                    'secondary' => $cv->value,
                    'action' => 'add'
                ];
            } elseif ($masterValue->value !== $cv->value) {
                $diff['custom'][$cv->customField->name] = [
                    'master' => $masterValue->value,
                    'secondary' => $cv->value,
                    'action' => 'keep_master'
                ];
            }
        }

        return $diff;
    }
}
