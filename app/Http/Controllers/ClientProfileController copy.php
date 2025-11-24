<?php

namespace App\Http\Controllers;

use App\Helpers\FileUpload;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientProfileController extends Controller
{
    public function clientList()
    {
        $newLeads = Client::where('follow_up_status', 'pending')->count();
        $quotationsSent = Client::where('quotation_sent_status', true)->count();
        $closedDeals = Client::where('follow_up_status', 'approved')->count();
        $lostDeals = Client::where('follow_up_status', 'lost')->count();

        $clients = Client::all();

        return Inertia::render('Client/Index', compact('clients', 'newLeads', 'quotationsSent', 'closedDeals', 'lostDeals'));
    }

    public function clientsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|max:255',
            'email' => 'required|string|email|unique:clients|max:255',
            'whatsapp' => 'required|string|max:255',
            'source_of_lead' => 'required|string|max:255',
            'service_type' => 'required|string|max:255',
            'follow_up_status' => 'required|string|max:255',
            'project_cost' => 'required|max:255',
        ]);

        $filePath = FileUpload::storeFile($request->file('documents'), 'uploads/clients');

        $client = new Client();

        $client->name = $request->name;
        $client->phone = $request->phone;
        $client->email = $request->email;
        $client->whatsapp = $request->whatsapp;
        $client->source_of_lead = $request->source_of_lead;
        $client->service_type = $request->service_type;
        $client->follow_up_status = $request->follow_up_status;
        $client->project_cost = $request->project_cost;
        $client->client_interaction = $request->client_interaction;
        $client->follow_date = $request->follow_date;
        $client->documents = $filePath;

        $client->save();

        return redirect()->back()->with('success', 'Client created successfully.');
    }

    public function clientUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|max:255',
            'email' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:255',
            'source_of_lead' => 'required|string|max:255',
            'service_type' => 'required|string|max:255',
            'follow_up_status' => 'required|string|max:255',
            'client_interaction' => 'required|string|max:255',
            'project_cost' => 'required|max:255',
            'follow_date' => 'required|date|max:255',
            'documents' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $client = Client::find($id);

        if ($request->hasFile('documents')) {
            if ($client->documents && file_exists(public_path(ltrim($client->documents, '/')))) {
                unlink(public_path(ltrim($client->documents, '/')));
            }

            $imagePath = FileUpload::storeFile($request->file('documents'), 'uploads/clients');
            $client->documents = $imagePath;
        }

        $client->name = $request->name;
        $client->phone = $request->phone;
        $client->email = $request->email;
        $client->whatsapp = $request->whatsapp;
        $client->source_of_lead = $request->source_of_lead;
        $client->service_type = $request->service_type;
        $client->follow_up_status = $request->follow_up_status;
        $client->client_interaction = $request->client_interaction;
        $client->project_cost = $request->project_cost;
        $client->follow_date = $request->follow_date;

        $client->save();

        return redirect()->back()->with('success', 'Client updated successfully.');
    }

    public function clientDestroy($id)
    {
        $client = Client::find($id);
        if ($client->documents && file_exists(public_path(ltrim($client->documents, '/')))) {
            unlink(public_path(ltrim($client->documents, '/')));
        }
        $client->delete();

        return redirect()->back()->with('success', 'Client deleted successfully.');
    }
}
