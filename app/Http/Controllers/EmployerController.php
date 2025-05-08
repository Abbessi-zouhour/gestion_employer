<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployerRequest;
use App\Http\Requests\UpdateEmployerRequest;
use App\Models\Employer;
use App\Models\Departement;
use Exception;

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
        $departements = Departement::all();
        return view('employers.edit', compact('employer', 'departements'));
    }

    public function store(StoreEmployerRequest $request){
        try {
            $query = Employer::create($request->all());
            if($query){
                return redirect()->route('employer.index')->with('success_message', 'Employer ajouté');
            }
        } catch (Exception $e) {
            dd($request);
        }
    }

    public function update(UpdateEmployerRequest $request, Employer $employer){
        try {
            $employer->nom = $request->nom;
            $employer->prenom = $request->prenom;
            $employer->email = $request->email;
            $employer->contact = $request->contact;
            $employer->departement_id = $request->departement_id;
            $employer->montant_journalier = $request->montant_journalier;

            $employer->update();


            return redirect()->route('employer.index')->with('success_message', 'Les informations de l\'employer ont été mise à jour');
        } catch (Exception $e) {
            dd($request);
        }
    }
    public function delete(Employer $employer){
        try{
            $employer->delete();

            return redirect()->route('employer.index')->with('success_message', 'Employer retirer');
        }catch (Exception $e){
            dd($e);
        }
    }
}
