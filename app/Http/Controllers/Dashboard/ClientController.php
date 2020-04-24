<?php

namespace App\Http\Controllers\Dashboard;

use App\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class ClientController extends Controller
{

    public function index(Request $request)
    {
        $clients = Client::search($request)->paginate();

        return view('dashboard.clients.index', compact('clients'));
    }


    public function create()
    {
        return view('dashboard.clients.create');
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone.0' => 'required',
            'phone' => 'required|array|min:1'
        ]);

        $data['phone'] = array_filter($data['phone']);
        Client::create($data);

        session()->flash('success', Lang::get('site.added_successfully'));

        return redirect()->route('dashboard.clients.index');
    }

    public function edit(Client $client)
    {
        return view('dashboard.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone.0' => 'required',
            'phone' => 'required|array|min:1'
        ]);

        $data['phone'] = array_filter($data['phone']);
        $client->update($data);

        session()->flash('success', Lang::get('site.updated_successfully'));

        return redirect()->route('dashboard.clients.index');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        session()->flash('success', Lang::get('site.deleted_successfully'));

        return redirect()->route('dashboard.clients.index');
    }
}
