<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployerRequest;
use Illuminate\Http\Request;
use App\Models\Employer;
use App\Models\Departement;


class EmployerController extends Controller
{

    public function index(){
        $employers = Employer::with('departement')->paginate(10);
        return view('employers.index', compact('employers'));
    }

    public function create(){
        $departements = Departement::all();
        return view('employers.create', compact('departements'));
    }

    public function edit(Employer $employer){
        return view('employers.edit', compact('employer'));
    }

    public function store(StoreEmployerRequest $request){

        $query = Employer::create($request->all());

        if($query){
            return redirect()->route('employer.index')->with('success_message', 'Employer ajouté');
        }
    }
}
