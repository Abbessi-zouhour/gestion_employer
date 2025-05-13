<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeAdminRequest;
use App\Http\Requests\updateAdminRequest;
use App\Models\ResetCodePassword;
use App\Models\User;
use App\Notifications\SendEmailToAdminAfterRegistrationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Exception;

class AdminController extends Controller
{
    public function index(){
        $admins = User::paginate(10);
        return view('admins/index', compact('admins'));
    }
    public function create(){
        return view('admins/create');
    }
    public function edit(User $user){
        return view('admins/edit', compact('user'));
    }

    // Enregistrer un Admin en DB et envoyer un mail
    public function store(storeAdminRequest $request){

        // dd($request);
        try{
            //Logique de création de compte

            $user = new User();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make('default');
            $user->save();

            //Envoyer un mail pour que l'utilisateur puisse confirmer son compte

            //Envoyer un code par email pour verification

            if($user){
                try{
                    ResetCodePassword::where('email', $user->email)->delete();
                $code = rand(1000, 4000);

                $data = [
                    'code'=>$code,
                    'email'=>$user->email
                ];

                ResetCodePassword::create($data);

                Notification::route('mail', $user->email)->notify(
                    new SendEmailToAdminAfterRegistrationNotification(
                        $code, $user->email
                    ));

                    // Redidriger l'utilisateur vers une URL
                    return redirect()->route('administrateurs')->with('success_message','Administrateur ajouté');

                }catch(Exception $e){
                    dd($e);
                    throw new Exception('une erreur est survenue lors de l\'envoie du mail ');
                }
            }





        }catch(Exception $e){
            //dd($e);
            throw new Exception('Une erreur est survenue lors de la création de cet asministrateur');
        }
    }

    public function update(updateAdminRequest $request, User $user){
        try{
            //Logique de mise à jour de compte
        }catch(Exception $e){
            // dd($e);
            throw new Exception('Une erreur est survenue lors de la mise à jour des informations de l\'utilisateur');
        }
    }

    public function destroy(User $user)
{
    try {

        //Logique de suppression

        $connectedAdminId = Auth::user()->id;

        if($connectedAdminId !== $user->id){
        $user->delete();
        return redirect()->back()->with('success_message', 'L\'administrateur a été retiré');
        }
        else{
    return redirect()->back()->with('error_message', 'vous ne pouvez pas supprimé votre compte');
}

    } catch (\Exception $e) {
        throw new \Exception("Une erreur est survenue lors de la suppression de l'administrateur.");
    }
}


    public function defineAccess($email){
        dd($email);

        $checkUserExist = User::where('email', $email)->first();

        dd($checkUserExist);
    }
}
